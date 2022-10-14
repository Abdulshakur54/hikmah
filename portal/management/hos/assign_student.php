<?php
require_once './includes/hos.inc.php';

$initClassId = null;
$db = DB::get_instance();
$msg = '';

function selectedClass($id)
{
    if (Input::submitted()) {
        global $classId;
        if ($classId == $id) {
            global $initClassId;
            $initClassId = $id; //this will help to dynamically load the Transfer to select box
            return 'selected';
        }
        return '';
    }
}

if (Input::submitted() && Token::check(Input::get('token'))) {
    $submitType = Input::get('submittype');
    $classId = (int) Input::get('classid');
    $msg = '';
    $val = new Validation();
    $values = [
        'classid' => [
            'name' => 'Class',
            'required' => true,
            'exists' => 'id/class'
        ]
    ];
    if (!$val->check($values)) {
        $errors = $val->errors();
        foreach ($errors as $error) {
            $msg .= $error . '<br>';
        }
        $msg = '<div class="failure">' . $msg . '</div>';
    } else {

        if ($submitType == 'assign') {
            $stdIdsString = Input::get('studentids');
            $sqlStdString = "'" . str_replace(",", "','", $stdIdsString) . "'";
            $db->query('update student3 inner join student on student3.std_id = student.std_id set student3.class_id = ?, student.class_id=? where student3.std_id in(' . $sqlStdString . ')', [$classId, $classId]);
            //populate student_psy table
            //notify the students
            $cD = $hos->getClassDetail($classId);
            $level = (int)$cD->level;
            $teaId = Utility::escape($cD->teacher_id);

            $notMsg = '<p>You have been Admitted into ' . School::getLevelName(Utility::escape($cD->sch_abbr), $level) . ' ' . Utility::escape($cD->class) . '.</p>';
            if (!empty($teaId)) { //this ensures that the class already has a Form Teacher so as to avoid error
                $teaDetail = $hos->getTeacherDetail($teaId);
                $teaName = Utility::escape($teaDetail->title) . '. ' . Utility::formatName(Utility::escape($teaDetail->fname), Utility::escape($teaDetail->oname), Utility::escape($teaDetail->lname));
                $notMsg .= '.</p><p>Your Form Teacher is ' . $teaName . '. <a href="' . $url->to('profile.php?id=' . $teaId, 0) . '">View Form Teacher\'s Profile</a></p>';
            }

            $alert = new Alert(true);
            if ($rank == 5) {
                $alert->sendToRank(9, "Class Assignment", $notMsg, "std_id in(" . $sqlStdString . ")", false);
            }

            if ($rank == 17) { //when mudir are the HOS
                $alert->sendToRank(10, "Class Assignment", $notMsg, "std_id in(" . $sqlStdString . ")", false);
            }
            $msg = '<div class="success">Assignment was successful</div>';
        }
    }
}
Session::set_flash('message', $msg);
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Assign student to classes</h4>
            <form class="forms-sample" id="studentForm" onsubmit="return false" novalidate>

                <div class="form-group">
                    <label for="class">Class</label>
                    <select class="js-example-basic-single w-100 p-2" name="classid" onchange="submitForm()" id="class" title="Class" required>
                        <?php
                        $availableClass  = $hos->getClasses($sch_abbr);
                        if (!empty($availableClass)) {
                            $initClassId = ($availableClass[0])->id;
                            foreach ($availableClass as $avaClass) {
                                $cId = (int)$avaClass->id;
                                echo '<option value="' . $cId . '" ' . selectedClass($cId) . '>' . School::getLevelName(Utility::escape($sch_abbr), Utility::escape($avaClass->level)) . ' ' . Utility::escape($avaClass->class) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                
                    <?php
                    //get the class level
                    $db->query('select level from class where id = ?', [$initClassId]);
                    if ($db->row_count() > 0) {
                        $level = $db->one_result()->level;
                        //select all students that have same level and do not have a class
                        $stdNeedClass = $hos->getStudentsNeedsClass($sch_abbr, $level);
                    }
                    if (!empty($stdNeedClass)) {
                        echo '<div class="font-weight-bold">Students qualified for the selected class</div>';
                        echo '<div class="text-right pr-1"><label for="checkall">Check All</label> <input type="checkbox" id="checkall" onclick="checkAll(this)" checked /></div>';
                        echo '<table class="table table-striped table-bordered nowrap responsive" id="studentsTable"><thead><th>S/N</th><th>Student ID</th><th>Name</th><th>Check</th></thead><tbody>';
                        $counter = 0;
                        foreach ($stdNeedClass as $std) {
                            $counter++;
                            echo '<tr><td></td><td>' . Utility::escape($std->std_id) . '</td><td>' . Utility::formatName(Utility::escape($std->fname), Utility::escape($std->oname), Utility::escape($std->lname)) . '</td><td><input type="checkbox" value="' . $std->std_id . '" id="chk' . $counter . '" checked /></td></tr>';
                        }
                        echo '</tbody></table>';
                        echo '<input type="hidden" id="counter" value="' . $counter . '" />';
                        echo '<div class="text-center"><button onclick="confirmSubmission()" name="assign" class="btn btn-md btn-primary">Assign</button></div>';
                    } else {
                        echo '<div class="message">No Student found for Assignment</div>';
                    }

                    ?>
              

                <div id="messageContainer">
                    <?php
                    $msg = Session::get_flash('message');
                    if (!empty($msg)) {
                    ?>
                        <script>
                            swalNotifyDismiss('<?php echo $msg ?>', 'info', 2500);
                        </script>
                    <?php
                    }
                    ?>

                </div>
                <input type="hidden" name="studentids" id="studentIds" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <input type="hidden" name="submittype" id="submittype" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/hos/assign_student.js"></script>
<script>
    validate('studentForm'); 
</script>