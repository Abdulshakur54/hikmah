<?php
require_once './includes/hos.inc.php';

function selectedTeacher($teaId)
{
    global $teacherId;
    if (!empty($teacherId) && $teaId == $teacherId) {
        return 'selected';
    }
}

function selectedSubject($subId)
{
    global $subjectId;
    if (!empty($subjectId) && $subId == $subjectId) {
        return 'selected';
    }
}
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Assign Subject</h4>
            <form class="forms-sample" id="subjectForm" onsubmit="return false" novalidate>

                <?php
                if (Input::submitted() && Token::check(Input::get('token'))) {
                    $subjectId = Utility::escape(Input::get('subject'));
                    $teacherId = Utility::escape(Input::get('teacherid'));
                    $msg = '';
                    $val = new Validation();
                    $values = [
                        'subject' => [
                            'name' => 'Subject',
                            'required' => true,
                            'exists' => 'id/subject'
                        ],
                        'teacherid' => [
                            'name' => 'Teacher',
                            'required' => true,
                            'exists' => 'staff_id/staff'
                        ]
                    ];
                    if ($val->check($values)) {
                        $names = $hos->getStaffNames($teacherId);
                        $fullname = Utility::formatName($names->fname, $names->oname, $names->lname);
                        $submitType = Input::get('submitType');
                        $role_id = Config::get('hikmah/subject_teacher_role');
                        if ($submitType === 'assign') {

                            if (!$hos->isSubjectTeacher($teacherId, $subjectId)) {
                                if (!$hos->subjectHasTeacher($subjectId)) {
                                    //update subject table
                                    $assign_menus = (!$hos->isSubjectTeacher($teacherId)) ? true : false; //returns true when teacher is not a teacher of any subject so that he can be assign menus for the first time

                                    $hos->updateSubjectTeacher($subjectId, $teacherId);


                                    /*for hikmah only to help use the role and menu functionality*/
                                    if ($assign_menus) {

                                        Menu::add_available_menus($teacherId, $role_id); //add menus for subject teachers
                                    }
                                    /*for hikmah only to help use the role and menu functionality*/



                                    $msg = '<div class="success">' . $fullname . ' is now a Teacher for the selected subject</div>';
                                } else {
                                    $msg = '<div class="failure">Subject already has a teacher<br>You should unassign the current teacher to create way to assign another teacher</div>';
                                }
                            } else {
                                $msg = '<div class="failure">' . $fullname . ' is already the teacher for the selected subject</div>';
                            }
                        }

                        if ($submitType === 'unassign') {
                            if ($hos->isSubjectTeacher($teacherId, $subjectId)) {
                                //update subject and teachers table
                                $hos->updateSubjectTeacher($subjectId, $teacherId, true);
                                $unassign_menus = (!$hos->isSubjectTeacher($teacherId)) ? true : false; //returns true when teacher is no longer a teacher of any subject so that he can be unassigned subject teacher menus
                                /*for hikmah only to help use the role and menu functionality*/
                                if ($unassign_menus) {

                                    Menu::delete_available_menus($teacherId, $role_id); //add menus for subject teachers
                                }
                                /*for hikmah only to help use the role and menu functionality*/

                                $msg = '<div class="success">Unassignment was successful</div>';
                            } else {
                                $msg = '<div class="failure">' . $fullname . ' is not a teacher for the selected subject</div>';
                            }
                        }
                    } else {
                        $errors = $val->errors();
                        foreach ($errors as $error) {
                            $msg .= $error . '<br>';
                        }
                        $msg = '<div class="failure">' . $msg . '</div>';
                    }


                    Session::set_flash('message', $msg);
                }
                ?>

                <div class="form-group">
                    <label for="level">Teacher</label>
                    <select class="js-example-basic-single w-100 p-2" id="teacher" title="Teacher" name="teacherid" required>
                        <?php
                        $availableTeachers  = $hos->getTeachers($sch_abbr);

                        if (!empty($availableTeachers)) {
                            foreach ($availableTeachers as $availableTeacher) {
                                $name = Utility::formatName($availableTeacher->fname, $availableTeacher->oname, $availableTeacher->lname);
                                echo '<option value="' . $availableTeacher->staff_id . '" ' . selectedTeacher($availableTeacher->staff_id) . '>' . $name . ' &nbsp;(' . $availableTeacher->staff_id . ')</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <select class="js-example-basic-single w-100 p-2" id="subject" title="Subject" name="subject" required>
                        <?php
                        $subjects  = $hos->getDiscreteSubjects($sch_abbr);
                        if (!empty($subjects)) {
                            foreach ($subjects as $subject) {
                                echo '<option value="' . $subject->subid . '"' . selectedSubject($subject->subid) . '>' . $subject->subject . ' [' . School::getLevelName($sch_abbr, $subject->level) . $subject->class . ']' . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

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
                <input type="hidden" name="submitType" id="submitType" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <div>
                    <button name="assign" onclick="confirmSubmission('assign')" class="btn btn-md btn-primary">Assign</button>
                    <button name="unassign" onclick="confirmSubmission('unassign')" class="btn btn-md btn-primary">UnAssign</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/hos/classes.js"></script>
<script>
    validate('subjectForm');
    var submitType = document.getElementById('submitType');
    async function confirmSubmission(param) {
        if (param === 'assign') {
            if (await swalConfirm('This will assign the selected subject to the selected teacher', 'info')) {
                submitType.value = 'assign';
                getPostPage("subjectForm", "management/hos/assign_subject.php");
            }
        } else {
            if (await swalConfirm('This will proceed with Unassignment', 'info')) {
                submitType.value = 'unassign';
                getPostPage("subjectForm", "management/hos/assign_subject.php");
            }
        }
    }
</script>