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


    public function requestConfirm($id, $requester_id, $category)
    {
        $other_data = json_decode($this->_db->get('request', 'other', "id=$id")->other, true);
        switch ($category) {
            case 1: //confirmation of salary
                $this->_db->query('update account set approved = ? where receiver = ?', [true, $requester_id]);

                break;
            case 2:
                $mail = new Email();

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

                $user_id = $other_data['user_id'];
                $this->_db->query('select student2.email,student.fname,student.lname,student.oname,student.rank, student.sch_abbr,student.level,class.class from student inner join student2 on student.std_id = student2.std_id inner join class on class.id=student.class_id where student.std_id = ?', [$user_id]);
                $student_detail = $this->_db->one_result();
                $this->_db->update('student', ['active' => 0], "std_id = '$user_id'");
                $mail->send($student_detail->email, 'Management Decision', 'Dear ' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname) . ', The school writes to inform you that you have been expelled from ' . School::getFullName($student_detail->sch_abbr) . '. We wish you better days ahead');
                $alert = new Alert(true);
                $alert->sendToRank(2, 'Management Decision', 'This is to notify you that ' . Utility::formatName($student_detail->fname, $student_detail->oname, $student_detail->lname) . ' of ' . School::getFullName($student_detail->sch_abbr) . ' (' . School::getLevelName($student_detail->sch_abbr, (int)$student_detail->level) . ' ' . $student_detail->class . ' has been expelled');
                break;
            case 5:

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
                break;
            case 6: //confirmation of salary
                $this->_db->query('update account set approved = ? where receiver = ?', [true, $requester_id]);
                break;
            case 7: //payment of salary
                $acct = new Account();
                $other_data = json_decode($this->_db->get('request', 'other', "id=$id")->other);
                $payment_type = $other_data->payment_type;
                $recipients = $other_data->recipients;
                $recipients_data = $other_data->recipients_data;
                $percentage = $other_data->percentage;
                $receivers = [];
                foreach ($recipients_data as $rd) {
                    $receivers[] = $rd[0];
                }
                $payment_month = (int) $other_data->payment_month;
                $payment_details = $acct->getPaymentDetails($payment_month, $other_data->recipients, $other_data->school, $receivers);

                if ($payment_type == 'online') {
                    //make online payment,  exit code and echo a response if it was not successful

                }

                if ($this->populateSalaryTable($payment_details, $other_data)) {
                    $other_data = [];
                    $other_data['salary'] = $recipients_data;
                    $other_data['salary_month'] = Account::getSalaryMonth($payment_month);;
                    $other_data['others'] = $payment_details;
                    $other_data['recipients'] = $recipients;
                    $other_data['request_id'] = $id;
                    $other_data['percentage'] = $percentage;
                    if ($recipients == 'management') {
                        $cat = 'M';
                    } else {
                        $cat = 'S';
                    }
                    $this->deleteRequest($id);
                   
                    $this->_db->delete('payment_exist', "payment_month=$payment_month and category='$cat'");
                    $this->requestResponseNotification($requester_id, $category, true, $other_data);
                    return Utility::response(204, 'Payment was successfully executed');
                }
                return Utility::response(500, 'An error occurred when trying to process payment');
            case 8:
                $amount = (float)$other_data['amount'];
                $account = $other_data['account'];
                $description =  $other_data['description'];
                $depositor =  $other_data['depositor'];

                $acct = new Account();
                if ($acct->deposit($account, $amount, $description, $depositor)) {
                    $other_data = [
                        'amount' => $amount,
                        'account' => $account,
                        'depositor' => $depositor
                    ];
                    $this->requestResponseNotification($requester_id, $category, true, $other_data);
                    return Utility::response(201, "Deposit was successful");
                } else {
                    return Utility::response(201, "An error occurred while trying to deposit cash");
                }
            case 9:
                $amount = (float)$other_data['amount'];
                $account = $other_data['account'];
                $description =  $other_data['description'];
                $recipient =  $other_data['recipient'];

                $acct = new Account();
                if ($acct->withdraw($account, $amount, $description, $recipient)) {
                    $other_data = [
                        'amount' => $amount,
                        'account' => $account,
                        'recipient' => $recipient
                    ];
                    $this->requestResponseNotification($requester_id, $category, true, $other_data);
                    $this->deleteRequest($id);
                    return Utility::response(201, "Withdrawal was successful");
                } else {
                    return Utility::response(201, "An error occurred while trying to deposit cash");
                }
        }
        //delete the request from the request table
        $this->deleteRequest($id);
        //send notification to the requester
        $this->requestResponseNotification($requester_id, $category, true);
    }

    //this method is used to send a notification to a requester after his request have been accepted or rejected
    private function requestResponseNotification($requester_id, $category, $accepted, mixed $other_data = '')
    {
        switch ($category) {
            case 1:
            case 6:
                $acct = new Account();
                $not = new Alert(true);
                $details = $acct->getAccountDetails($requester_id);
                $salary = $details->salary;
                $name = Utility::formatName($details->fname, $details->oname, $details->lname);
                $firstLetter = strtoupper(substr($requester_id, 0, 1));
                if ($category == 1) {
                    $title = 'Salary Approval';
                    $message1 = 'A sum of  &#8358;' . $salary . ' has been approved for you as salary';
                    $message2 = 'A salary of &#8358;' . $salary . ' has been approved for ' . $name;
                } else {
                    //request is to accountant
                    $title = 'Salary Update Declination';
                    $message1 = 'A new salary of  &#8358;' . $salary . ' has been approved for you as salary';
                    $message2 = 'A new salary of &#8358;' . $salary . ' has been approved for ' . $name;
                }
                if ($accepted) {
                    $not->send($requester_id, $title, $message1);
                    //check if the requester is a member of management then send a notification to the director, else send a notification to the hrm
                    if ($firstLetter === $this->_mgtIdFirstLetter) { //this is a management member
                        $not->sendToRank($this->_directorRank, $title, $message2);
                    } else { //the requester is assumed to be a staff
                        $not->sendToRank($this->_hRMRank, $title, $message2);
                    }
                } else {
                    $not->send($requester_id, $title, 'Your request of  &#8358;' . $salary . ' as salary was rejected');
                    //check if the requester is a member of management then send a notification to the director, else send a notification to the hrm
                    if ($firstLetter === $this->_mgtIdFirstLetter) { //this is a management member
                        $not->sendToRank($this->_directorRank, $title, 'The salary of  &#8358;' . $salary . ' is rejected for ' . $name);
                    } else { //the requester is assumed to be a staff
                        $not->sendToRank($this->_hRMRank, $title, 'The salary of  &#8358;' . $salary . ' is rejected for ' . $name);
                    }
                }
                break;
            case 7:
                $not = new Alert(true);
                if ($accepted) {

                    $others = $other_data['others'];
                    $salary_data = $other_data['salary'];
                    $salary_month = $other_data['salary_month'];
                    $recipients = $other_data['recipients'];
                    $request_id = $other_data['request_id'];
                    $percentage = $other_data['percentage'];
                    $count = 0;
                    foreach ($others as $pd) {
                        $fname = ucfirst($pd->fname);
                        $salary = $salary_data[$count][1];
                        $msg = "<p>Hi $pd->title. $fname. </p><p>This is to notify that you have been paid <span style='font-style: bold'>(&#8358;)$salary</span> as salary for the salary month ($salary_month)</p> <p>$percentage% of remaining salary was paid</p>";
                        $not->send($pd->receiver, 'Payment of Salary', $msg, true);
                        $count++;
                    }
                    $not->reset();
                    //notify accountant office
                    $msg = "<p>This is to notify that $recipients salary have been paid for the salary month ($salary_month) after approval from the director</p>
                    <p>$percentage% of remaining salary was paid</p>";
                    $not->sendToRank(3, 'Payment of Salary', $msg);
                    $not->reset();
                    //notify director office
                    $msg = "<p>This is to notify that $recipients salary have been paid for the salary month ($salary_month) after approval from the Office of the Director</p> <p>$percentage% of remaining salary was paid</p>";
                    $not->sendToRank(1, 'Payment of Salary', $msg);
                    //audit payment request;
                    $record = $this->_db->get('request', '*', "id=$request_id");
                    $this->_db->insert('audit', ['operation' => 'delete', 'table_name' => 'request', 'record' => json_encode($record)]);
                } else {

                    $recipients = $other_data['recipients'];
                    $percentage = $other_data['percentage'];
                    $payment_month = $other_data['payment_month'];
                    if ($recipients == 'management') {
                        $cat = 'M';
                    } else {
                        $cat = 'S';
                    }
                    //delete record from payment_exist table
                    $this->_db->delete('payment_exist', "payment_month=$payment_month and category='$cat'");
                    $salary_month = Account::getSalaryMonth((int) $payment_month);
                    $msg = "<p>This is to notify that your request to the Director to approve payment for the salary month ($salary_month) was rejected</p><p>$percentage% of remaining salary was proposed</p>";
                    $not->sendToRank(3, 'Payment of Salary', $msg);
                    $not->reset();
                    $msg = "<p>This is to notify that the Accountant request to approve payment for the salary month ($salary_month) was rejected</p> <p>$percentage% of remaining salary was proposed</p>";
                    $not->sendToRank(1, 'Payment of Salary', $msg);
                }
                break;
            case 8:
                $amount = $other_data['amount'];
                $account = $other_data['account'];
                $depositor = $other_data['depositor'];
                $not = new Alert(true);
                $title = (($accepted) ? 'Approval' : 'Declination') . ' of Deposit Request';
                if ($accepted) {
                    $title = 'Approval of Deposit Request';
                    $msg = "Hello, This is to notify you that the <span class='font-weight-bold'>director</span> have approved your request for the deposit of <span class='font-weight-bold'>(&#8358;)" . number_format($amount, 2) . "</span> into <span class='font-weight-bold'>$account</span> from <span class='font-weight-bold'>$depositor</span>";
                } else {
                    $title = 'Declination of Deposit Request';
                    $msg = "Hello, This is to notify you that the <span class='font-weight-bold'>director</span> declined your request for the deposit of <span class='font-weight-bold'>(&#8358;)" . number_format($amount, 2) . "</span> into <span class='font-weight-bold'>$account</span> from <span class='font-weight-bold'>$depositor</span>";
                }
                $not->sendToRank(3, $title, $msg);
                break;
            case 9:
                $amount = $other_data['amount'];
                $account = $other_data['account'];
                $recipient = $other_data['recipient'];
                $not = new Alert(true);
                if ($accepted) {
                    $title = 'Approval of Withdrawal Request';
                    $msg = "Hello, This is to notify you that the <span class='font-weight-bold'>director</span> have approved your request for the withdrawal of <span class='font-weight-bold'>(&#8358;)" . number_format($amount, 2) . "</span> from <span class='font-weight-bold'>$account</span> for <span class='font-weight-bold'>$recipient</span>";
                } else {
                    $title = 'Declination of Withdrawal Request';
                    $msg = "Hello, This is to notify you that the <span class='font-weight-bold'>director</span> declined your request for the withdrawal of <span class='font-weight-bold'>(&#8358;)" . number_format($amount, 2) . "</span> from <span class='font-weight-bold'>$account</span> for <span class='font-weight-bold'>$recipient</span>";
                }
                $not->sendToRank(3, $title, $msg);
                break;
        }
    }

    public function requestDecline($id, $requester_id, $category)
    {
        $other_data = json_decode($this->_db->get('request', 'other', "id=$id")->other, true);
        $rsp = $this->deleteRequest($id, $category);
        //send notification to the requester
        $this->requestResponseNotification($requester_id, $category, false, $other_data);
        if (!empty($rsp)) {
            return $rsp;
        }
    }


    //this method sends request to the request table, it sends multiple request using prepared statements when requery is true
    public function send(string $requester_id, int $confirmer_rank, string $request, RequestCategory $category, array $other = [], bool $requery = false, $type = 0)
    {

        //title the request relative to the category
        $category = $category->value;
        switch ($category) {
            case 1:
                $title = 'Salary Confirmation';
                break;
            case 2:
                $title = 'Sacking of a staff';
                break;
            case 3:
                $title = 'Deletion of a staff';
                break;
            case 4:
                $title = 'Expulsion of a student';
                break;
            case 5:
                $title = 'Deletion of a student';
                break;
            case 6:
                $title = 'Update of salary';
                break;
            case 7:
                $title = 'Payment of Salary';
                break;
            default:
                $title = '';
        }

        if ($requery) {
            $other = (empty($other)) ? null : json_encode($other);
            if ($this->_sendQryPrepared) {
                $this->_db->requery([$requester_id, $category]); //check if request exists
                if (!($this->_db->row_count() > 0)) { //ensures that request are only sent when it does not exist
                    $this->_db2->requery([$requester_id, $confirmer_rank, $title, $request, $category, $other, $type]);
                }
            } else {
                //instansiate second connection and prepare query
                $this->_db2 = DB::get_instance2();
                $this->_db->query('select id from request where requester_id=? and category=?', [$requester_id, $category]); //check if request exists
                if (!($this->_db->row_count() > 0)) { //ensures that request are only sent when it does not exist
                    $this->_db2->query('insert into request(requester_id,confirmer_rank,title,request,category,other,type) values(?,?,?,?,?,?,?)', [$requester_id, $confirmer_rank, $title, $request, $category, $other, $type]);
                    $this->_sendQryPrepared = true;
                }
            }
        } else {
            if ($this->requestExists($requester_id, $category, $other)) {
                return 1;
            } else {
                $other = (empty($other)) ? null : json_encode($other);
                return $this->_db->query('insert into request(requester_id,confirmer_rank,title,request,category,other,type) values(?,?,?,?,?,?,?)', [$requester_id, $confirmer_rank, $title, $request, $category, $other, $type]);
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
    public function deleteRequest($id,  int $category = null): bool|string
    {
        if ($category) {
            $this->_db->query('delete from request where id =?', [$id]);
            switch ($category) {
                case 7:
                    return Utility::response(200, 'Payment request have been declined');
                default:
                    return $this->_db->query('delete from request where id =?', [$id]);
            }
        } else {
            return $this->_db->query('delete from request where id =?', [$id]);
        }
    }

    //this is another version of deleteRequest with different parameters
    public function delRequest($requester_id, RequestCategory $category)
    {
        $category = $category->value;
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


    public function requestExists($requester_id, $category, $other): bool
    {
        $this->_db->query('select id from request where requester_id=? and category=? and other=?', [$requester_id, $category, json_encode($other)]);
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

    private function populateSalaryTable(array $payment_details, object $other_data): bool
    {
        $count = 0;
        $db = DB::get_instance();
        $recipient_data = $other_data->recipients_data;
        $payment_month = (int)$other_data->payment_month;
        $recipients = $other_data->recipients;
        if ($recipients == 'management') {
            $payer = Config::get('hikmah/management_account');
            $category = TransactionCategory::MANAGEMENT_SALARY;
        } else {
            $payer = $other_data->school;
            $category = TransactionCategory::STAFF_SALARY;
        }
        $payer_balance = Account::getAccountBalance($payer);
        if (Utility::equals($other_data->payment_type, 'online')) {
            $transaction_type = TransactionType::ONLINE;
        } else {
            $transaction_type = TransactionType::MANUAL;
        }
        $payment = new Payment();
        $start = false;
        try {
            $db->beginTransaction();
            $payment->beginTransaction();
            $payment->setPaySalariesInitial($payer_balance, $payer, $payment_month, $transaction_type, $category);
            foreach ($payment_details as $pd) {
                $amount = (float) $recipient_data[$count][1];
                $newPaid = round((float) $pd->paid + $amount, 2);
                if (round($newPaid, 1) < round((float)$pd->salary, 1)) {
                    $newStatus = 1;
                } else {
                    $newStatus = 2;
                }
                if ($start) {
                    $db->requery([$newPaid, $newStatus, $pd->receiver]);
                } else {
                    $db->query("update salary set paid=?,status=? where payment_month=$payment_month and receiver=?", [$newPaid, $newStatus, $pd->receiver]);
                    $start = true;
                }
                $payment->paySalary($pd->receiver, $amount);
                $count++;
            }
            //update payer(school) balance
            $newBalance = $payment->getSchoolBalance();
            $db->update('accounts', ['balance' => $newBalance], "account_name='$payer'");
            $payment->commit();
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $payment->rollBack();
            $db->rollBack();
            return false;
        }
    }
}
