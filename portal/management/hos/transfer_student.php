<?php
require_once './includes/hos.inc.php';

$initClassId = null;
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

function selectedClassTo($id)
{
    if (Input::submitted()) {
        global $classIdTo;
        if ($classIdTo == $id) {
            return 'selected';
        }
        return '';
    }
}

if (Input::submitted() && Token::check(Input::get('token'))) {
    $classId = (int) Input::get('classid');
    $classIdTo = (int) Input::get('classidto');
    $submitType = Input::get('submittype');
    $msg = '';
    $db = DB::get_instance();
    if ($submitType == 'transfer') {
        $stdIdsString = Input::get('studentids');
        $sqlStdString = "'" . str_replace(",", "','", $stdIdsString) . "'";
        $classIdTo = (int) Input::get('classidto');
        $cD = $hos->getClassDetail($classId);
        $cDTo = $hos->getClassDetail($classIdTo);

        //update class_id for student table
        $db->query('update student set class_id = ? where std_id in(' . $sqlStdString . ')', [$classIdTo]);
        //update score table
        $utils = new Utils();
        $formSession = $utils->getFormattedSession($sch_abbr);
        $db->query('update ' . $formSession . '_score set class_id =? where std_id in(' . $sqlStdString . ')', [$classIdTo]);
        //notify the students
        $level = (int)$cDTo->level;
        $teaId = Utility::escape($cDTo->teacher_id);
        if (!empty($teaId)) { //this ensures that the class already has a Form Teacher so as to avoid error
            $teaDetail = $hos->getTeacherDetail($teaId);
            $teaName = Utility::escape($teaDetail->title) . '. ' . Utility::formatName(Utility::escape($teaDetail->fname), Utility::escape($teaDetail->oname), Utility::escape($teaDetail->lname));
            $notMsg = '<p>You have been transfered to ' . School::getLevelName(Utility::escape($cD->sch_abbr), $level) . ' ' . Utility::escape($cDTo->class) .
                '.</p><p>Your Form Teacher is now ' . $teaName . '. <a href="' . $url->to('profile.php?staffId=' . $teaId, 2) . '">View Form Teacher\'s Profile</a></p>';
        } else {
            $notMsg = '<p>You have been transferred to ' . School::getLevelName(Utility::escape($cDTo->sch_abbr), $level) . ' ' . Utility::escape($cDTo->class) . '.</p>';
        }

        //ensures the student have registered the necessary subjects for their class
        $minNoSub = (int)$cDTo->nos;
        $stdIds = explode(',', $stdIdsString);
        $db2 = DB::get_instance2();
        $res = $db->get_result();
        $stdIdsWithMinNoSub = [];
        $start = false;

        foreach ($stdIds as $stdId) {
            if (!$start) {
                $db->query('select count(id) as counter from ' . $formSession . '_score where std_id=?', [$stdId]);
                $start = true;
            } else {
                $db->requery([$stdId]);
            }
            $no_of_sub_registered = $db->one_result()->counter;
            if ($no_of_sub_registered < $minNoSub) {
                $stdIdsWithMinNoSub[] = $stdId;
            }
        }
        //update student table for those that need to complete subject registration
        $stdIdsWithMinNoSubString = "'" . implode("','", $stdIdsWithMinNoSub) . "'"; //formatting so it can be used in query
        $db->query('update student set sub_reg_comp = false where id in(' . $stdIdsWithMinNoSubString . ')');
        //notify all students that have been transferred
        $alert = new Alert(true);
        if ($rank == 5) {
            $alert->sendToRank(9, "Class Transfer", $notMsg, "std_id in(" . $sqlStdString . ")", false);
        }

        if ($rank == 17) { //when mudir are the HOS
            $alert->sendToRank(10, "Class Transfer", $notMsg, "std_id in(" . $sqlStdString . ")", false);
        }
        $msg = '<div class="success">Transfer was successful</div>';
        Session::set_flash('message', $msg);
    }

    Session::set_flash('message', $msg);
}
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Transfer student to another class</h4>
            <form class="forms-sample" id="studentForm" onsubmit="return false" novalidate>

                <div class="form-group">
                    <label for="class">From</label>
                    <select class="js-example-basic-single w-100 p-2" name="classid" onchange="submitForm()" id="class" title="From" required>
                        <?php

                        $availableClass  = $hos->getClasses($sch_abbr);
                        if (!empty($availableClass)) {
                            $initClassId = (int) ($availableClass[0])->id;
                            foreach ($availableClass as $avaClass) {
                                $cId = (int)$avaClass->id;
                                echo '<option value="' . $cId . '" ' . selectedClass($cId) . '>' . School::getLevelName(Utility::escape($sch_abbr), Utility::escape($avaClass->level)) . ' ' . Utility::escape($avaClass->class) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="classto">To</label>
                    <select class="js-example-basic-single w-100 p-2" name="classidto" onchange="submitForm()" id="classto" title="To" required>
                        <?php

                        $classSameLevel = $hos->getClassSameLevel($initClassId,$sch_abbr);
                        if (!empty($classSameLevel)) {
                            foreach ($classSameLevel as $cls) {
                                $cId = (int)$cls->id;
                                echo '<option value="' . $cId . '" ' . selectedClassTo($cId) . '>' . School::getLevelName(Utility::escape($sch_abbr), Utility::escape($cls->level)) . ' ' . Utility::escape($cls->class) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <?php
                $hos2 = new Hos2();
                $transStudent = $hos2->getStudents($initClassId);
                if (!empty($transStudent)) {
                    echo '<div class="text-right pr-1"><label for="checkall">Check All</label><input type="checkbox" id="checkall" onclick="checkAll(this)" checked /></div>';
                    echo '<table class="table table-striped table-bordered nowrap responsive" id="studentsTable"><thead><th>S/N</th><th>Student Id</th>S/N<th>Fullname</th><th>Check</th></thead><tbody>';
                    $counter = 0;
                    foreach ($transStudent as $transStd) {
                        $counter++;
                        $stdId = Utility::escape($transStd->std_id);
                        echo '<tr><td></td><td>' . $stdId . '</td><td>' . Utility::formatName(Utility::escape($transStd->fname), Utility::escape($transStd->oname), Utility::escape($transStd->lname)) . '</td><td><input type="checkbox" id="chk' . $counter . '" value="' . $stdId . '" checked /></td></tr>';
                    }
                    echo '</tbody></table>';
                    echo '<input type="hidden" id="counter" value="' . $counter . '" />';
                } else {
                    echo '<div class="message">No record found</div>';
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
                <?php
                if (!empty($classSameLevel)) { //only show the transer button when there are students to transfer
                    echo '<div class="text-center p-3"><button name="transfer" onclick="confirmSubmission()" class="btn btn-md btn-primary">Transfer</button></div>';
                }
                ?>
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/hos/transfer_student.js"></script>
<script>
    validate('studentForm');
</script>