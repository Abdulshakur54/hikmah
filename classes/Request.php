<?php

/*
Request Categories
 case 1: Salary



  */

//this class send request to ranks
class Request
{


    private $_db, $_db2;
    private $_mgtIdFirstLetter = 'M';
    private $_directorRank = 1;
    private $_hRMRank = 6;
    private $_sendQryPrepared = false;


    public function __construct()
    {
        $this->_db = DB::get_instance();
    }


    public function requstConfirm($id, $requester_id, $category)
    {
        switch ($category) {
            case 1: //aproval of salary
                $this->_db->query('update account set approved = ? where receiver = ?', [true, $requester_id]);
                break;
            case 2:
                $mail = new Email();
                $other_data = json_decode($this->_db->get('request', 'other', "id=$id")->other, true);
                $user_id = $other_data['user_id'];
                $member_detail = $this->_db->get('staff', 'email,fname,lname,oname,rank,title', "staff_id = '$user_id'");
                $this->_db->update('staff', ['active' => 0], "staff_id = '$user_id'");
                $mail->send($member_detail->email, 'Management Decision', 'Dear Sir/Ma, The management writes to inform you that you have been relived of your duties. In other words, you have been sacked. We wish you better days ahead');
                $alert = new Alert(true);
                $alert->sendToRank(6, 'Management Decision', 'This is to notify you that ' . $member_detail->title . '. ' . Utility::formatName($member_detail->fname, $member_detail->oname, $member_detail->lname) . ' (' . User::getFullPosition($member_detail->rank) . ') have been relived of his duties');
                $alert->reset();
                $alert->sendToRank(2, 'Management Decision', 'This is to notify you that ' . $member_detail->title . '. ' . Utility::formatName($member_detail->fname, $member_detail->oname, $member_detail->lname) . ' (' . User::getFullPosition($member_detail->rank) . ') have been relived of his duties');
                break;
            case 3:
                $other_data = json_decode($this->_db->get('request', 'other', "id=$id")->other, true);
                $user_id = $other_data['user_id'];
                $picture = $this->_db->get('staff', 'picture', "staff_id='$user_id'")->picture;
                $sql1 = 'delete from staff where staff_id =?';
                $sql2 = 'delete from account where receiver = ?';
                $sql3 = 'delete from alert where receiver_id =?';
                $sql4 = 'delete from staff_cookie where staff_id =?';
                $sql6 = 'delete from request where requester_id =?';
                $sql7 = 'delete from request2 where requester_id =?';
                $sql8 = 'delete from users where user_id =?';
                $sql9 = 'delete from users_menu where user_id =?';
                if ($this->_db->trans_query([[$sql1, [$user_id]], [$sql2, [$user_id]], [$sql3, [$user_id]], [$sql4, [$user_id]], [$sql6, [$user_id]], [$sql7, [$user_id]], [$sql8, [$user_id]], [$sql9, [$user_id]]])) {
                    unlink('../../../staff/uploads/passports/' . $picture);
                }
                break;
            case 4:
                $mail = new Email();
                $other_data = json_decode($this->_db->get('request', 'other', "id=$id")->other, true);
                $user_id = $other_data['user_id'];
                $this->_db->query('select student2.email,student.fname,student.lname,student.oname,student.rank, student.sch_abbr,student.level,class.class from student inner join student2 on student.std_id = student2.std_id inner join class on class.id=student.class_id where student.std_id = ?', [$user_id]);
                $student_detail = $this->_db->one_result();
                $this->_db->update('student', ['active' => 0], "std_id = '$user_id'");
                $mail->send($student_detail->email, 'Management Decision', 'Dear ' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname) . ', The school writes to inform you that you have been expelled from ' . School::getFullName($student_detail->sch_abbr) . '. We wish you better days ahead');
                $alert = new Alert(true);
                $alert->sendToRank(2, 'Management Decision', 'This is to notify you that ' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname) . ' of ' . School::getFullName($student_detail->sch_abbr) . ' (' . School::getLevelName($student_detail->sch_abbr, (int)$student_detail->level) . ' ' . $student_detail->class . ' has been expelled');
                break;
            case 5:
                $other_data = json_decode($this->_db->get('request', 'other', "id=$id")->other, true);
                $user_id = $other_data['user_id'];
                $add_data = $this->_db->get('student', 'picture,sch_abbr', "std_id='$user_id'");
                $picture = $add_data->picture;
                $sch_abbr = $add_data->sch_abbr;
                $util = new Utils();
                $formatted_session = $util->getFormattedSession($sch_abbr) . '_score';
                $sql1 = 'delete from student where std_id =?';
                $sql2 = 'delete from student2 where std_id = ?';
                $sql3 = 'delete from alert where receiver_id =?';
                $sql4 = 'delete from std_cookie where std_id =?';
                $sql5 = 'delete from student_psy where std_id =?';
                $sql6 = 'delete from request where requester_id =?';
                $sql7 = 'delete from request2 where requester_id =?';
                $sql8 = 'delete from users where user_id =?';
                $sql9 = 'delete from users_menu where user_id =?';
                $sql10 = 'delete from  ' . $formatted_session . ' where std_id =?';
                if ($this->_db->trans_query([[$sql1, [$user_id]], [$sql2, [$user_id]], [$sql3, [$user_id]], [$sql4, [$user_id]], [$sql5, [$user_id]], [$sql6, [$user_id]], [$sql7, [$user_id]], [$sql8, [$user_id]], [$sql9, [$user_id]], [$sql10, [$user_id]]])) {
                    unlink('../../../student/uploads/passports/' . $picture);
                }
        }
        //delete the request from the request table
        $this->deleteRequset($id);
        //send notification to the requester
        $this->requestResponseNotification($requester_id, $category, true);
    }

    //this method is used to send a notification to a requester after his request have been accepted or rejected
    private function requestResponseNotification($requester_id, $category, $accepted)
    {
        switch ($category) {
            case 1:
                $acct = new Account();
                $not = new Alert(true);
                $details = $acct->getAccountDetails($requester_id);
                $salary = $details->salary;
                $name = Utility::formatName($details->fname, $details->oname, $details->lname);
                $firstLetter = strtoupper(substr($requester_id, 0, 1));
                if ($accepted) {
                    $not->send($requester_id, 'Salary Approval', 'Your request of  &#8358;' . $salary . ' as salary has been approved');
                    //check if the requester is a member of management then send a notification to the director, else send a notification to the hrm
                    if ($firstLetter === $this->_mgtIdFirstLetter) { //this is a management member
                        $not->sendToRank($this->_directorRank, 'Salary Approval', 'A salary of &#8358;' . $salary . ' has been approved for ' . $name);
                    } else { //the requester is assumed to be a staff
                        $not->sendToRank($this->_hRMRank, 'Salary Approval', 'A salary of &#8358;' . $salary . ' has been approved for ' . $name);
                    }
                } else {
                    $not->send($requester_id, 'Salary Declination', 'Your request of  &#8358;' . $salary . ' as salary was rejected');
                    //check if the requester is a member of management then send a notification to the director, else send a notification to the hrm
                    if ($firstLetter === $this->_mgtIdFirstLetter) { //this is a management member
                        $not->sendToRank($this->_directorRank, 'Salary Declination', 'The salary of  &#8358;' . $salary . ' is rejected for ' . $name);
                    } else { //the requester is assumed to be a staff
                        $not->sendToRank($this->_hRMRank, 'Salary Declination', 'The salary of  &#8358;' . $salary . ' is rejected for ' . $name);
                    }
                }
                break;
        }
    }

    public function requstDecline($id, $requester_id, $category)
    {
        $this->deleteRequset($id);
        //send notification to the requester
        $this->requestResponseNotification($requester_id, $category, false);
    }


    //this method sends request to the request table, it sends multiple request using prepared statements when requery is true
    public function send(string $requester_id, int $confirmer_rank, string $request, RequestCategory $category, array $other = [], bool $requery = false)
    {

        //title the request relative to the category
        $category = $category->value;
        switch ($category) {
            case 1:
                $title = 'Salary Confirmation';
                break;
            case 2:
                $title = 'Notification of sacking of a staff';
                break;
            case 3:
                $title = 'Notification of deletion of a staff';
                break;
            case 4:
                $title = 'Notification of expulsion of a student';
                break;
            case 5:
                $title = 'Notification of deletion of a student';
                break;
            default:
                $title = '';
        }

        if ($requery) {
            if ($this->_sendQryPrepared) {
                $this->_db->requery([$requester_id, $category]); //check if request exists
                if (!($this->_db->row_count() > 0)) { //ensures that request are only sent when it does not exist
                    $this->_db2->requery([$requester_id, $confirmer_rank, $title, $request, $category]);
                }
            } else {
                //instansiate second connection and prepare query
                $this->_db2 = DB::get_instance2();
                $this->_db->query('select id from request where requester_id=? and category=?', [$requester_id, $category]); //check if request exists
                if (!($this->_db->row_count() > 0)) { //ensures that request are only sent when it does not exist
                    $this->_db2->query('insert into request(requester_id,confirmer_rank,title,request,category) values(?,?,?,?,?)', [$requester_id, $confirmer_rank, $title, $request, $category]);
                    $this->_sendQryPrepared = true;
                }
            }
        } else {
            if ($this->requestExists($requester_id, $category,$other)) {
                return 1;
            } else {
                $other = (empty($other)) ? null : json_encode($other);
                return $this->_db->query('insert into request(requester_id,confirmer_rank,title,request,category,other) values(?,?,?,?,?,?)', [$requester_id, $confirmer_rank, $title, $request, $category, $other]);
            }
        }
    }

    public function getMyRequests($confirmerRank)
    {
        $this->_db->query('select * from request where confirmer_rank =? order by id desc', [$confirmerRank]);
        return $this->_db->get_result();
    }

    public function getCount($confirmerRank): int
    {
        $arrVal = (array)$this->getMyRequests($confirmerRank);
        return count($arrVal);
    }

    //this delete a request from the request table
    public function deleteRequset($id): bool
    {
        return $this->_db->query('delete from request where id =?', [$id]);
    }

    //this is another version of deleteRequest with different parameters
    public function delRequest($requester_id, $category)
    {

        if (!is_array($requester_id)) {
            $this->_db->query('delete from request where requester_id = ? and category =?', [$requester_id, $category]);
        } else {
            $x = 1;
            foreach ($requester_id as $req => $cat) {
                if ($x === 1) {
                    $this->_db->query('delete from request where requester_id = ? and category =?', [$req, $category]);
                    $x++;
                } else {
                    //using requery
                    $this->_db->requery([$req, $category]);
                }
            }
        }
    }


    public function requestExists($requester_id, $category,$other): bool
    {
        $this->_db->query('select id from request where requester_id=? and category=? and other=?', [$requester_id, $category,json_encode($other)]);
        if ($this->_db->row_count() > 0) {
            return true;
        }
        return false;
    }

    //this method checks if a receiver has request(s)
    public function hasRequests($confirmerRank)
    {
        $request = $this->getMyRequests($confirmerRank);
        return (!empty($request)) ? $request : false;
    }

    function reset()
    {
        $this->_sendQryPrepared = false;
    }

    public function __destruct()
    {
        $this->_db2 = null;
    }
}
