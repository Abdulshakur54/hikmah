<?php
require_once './includes/hos.inc.php';

if (Input::submitted() && Token::check(Input::get('token'))) {

    $classId = Utility::escape(Input::get('classid'));
    $teacherId = Utility::escape(Input::get('teacherid'));
    $msg = '';
    $val = new Validation();
    $values = [
        'classid' => [
            'name' => 'Class',
            'required' => true,
            'exists' => 'id/class'
        ],
        'teacherid' => [
            'name' => 'Teacher',
            'required' => true,
            'exists' => 'staff_id/staff'
        ]
    ];
    if ($val->check($values)) {
        $names = $hos->getStaffNames($teacherId);
        $fullname = $names->title . '. ' . Utility::formatName($names->fname, $names->oname, $names->lname);
        $detail = $hos->getClassAndLevel($classId);
        $level = $detail->level;
        $class = $detail->class;
        $submitType = Input::get('submitType');
        $alert = new Alert(true);
        $allClassStudents = $hos->getAllClassStudents($classId);
        $role_id = Config::get('hikmah/class_teacher_role');
        if ($submitType === 'assign') {
            if ($hos->isClassTeacher($classId, $teacherId)) {
                $msg = '<div class="failure">' . $fullname . ' is already the class teacher of ' . School::getLevelName($sch_abbr, $level) . ' ' . strtoupper($class) . '</div>';
            } else {
                if (!$hos->isAClassTeacher($teacherId)) {
                    //check if class already has a teacher
                    if (!$hos->classHasATeacher($classId)) {
                        $hos->assignClass($classId, $teacherId);

                        /*for hikmah only to help use the role and menu functionality*/
                        Menu::add_available_menus($teacherId, $role_id); //add menus for real class teacher
                        /*for hikmah only to help use the role and menu functionality*/

                        //notify all the students in that class of the development
                        $notMsg = '<p>This is to notify that you now have a new Form Teacher ' . $fullname . '. <a href="' . $url->to('profile.php?id=' . $teacherId, 0) . '">View Form Teacher\'s Profile</a></p>';
                        $msg = '<div class="success">' . $fullname . ' is now the class teacher of ' . School::getLevelName($sch_abbr, $level) . ' ' . strtoupper($class) . '</div>';
                        if (!empty($allClassStudents)) {
                            foreach ($allClassStudents as $std) {
                                $alert->send($std->std_id, 'Form Teacher Introduction', $notMsg, true);
                            }
                        }
                    } else {
                        $msg = '<div class="failure">' . School::getLevelName($sch_abbr, $level) . ' ' . strtoupper($class) . ' already has a teacher <br>Unassign the teacher to proceed</div>';
                    }
                } else {
                    $msg = '<div class="failure">' . $fullname . ' is already a class teacher<br>Unassign him from the class to proceed' . '</div>';
                }
            }
        }
        if ($submitType === 'unassign') {
            if ($hos->isAClassTeacher($teacherId)) {
                
                $hos->unAssignClass($teacherId);
                /*for hikmah only to help use the role and menu functionality*/
                Menu::delete_available_menus($teacherId, $role_id); //delete menus of class teacher
                /*for hikmah only to help use the role and menu functionality*/

                //notify all the students in that class of the development
                $notMsg = '<p>This is to notify that ' . $fullname . ' is no longer your Form Teacher</p>';
                if (!empty($allClassStudents)) {
                    foreach ($allClassStudents as $std) {
                        $alert->send($std->std_id, 'Form Teacher Status', $notMsg, true);
                    }
                }
                $msg = '<div class="success">Unassignment was successful</div>';
            } else {
                $msg = '<div class="failure">No class is assigned to ' . $fullname . '</div>';
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
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Assign Class</h4>
            <form class="forms-sample" id="classForm" onsubmit="return false" novalidate>

                <div class="form-group">
                    <label for="level">Teacher</label>
                    <select class="js-example-basic-single w-100 p-2" id="teacher" title="Teacher" name="teacherid" required>
                        <?php
                        $availableTeachers  = $hos->getTeachers($sch_abbr);

                        if (!empty($availableTeachers)) {
                            foreach ($availableTeachers as $availableTeacher) {
                                $name = $availableTeacher->title . '. ' . Utility::formatName($availableTeacher->fname, $availableTeacher->oname, $availableTeacher->lname);
                                echo '<option value="' . $availableTeacher->staff_id . '">' . $name . ' &nbsp;(' . $availableTeacher->staff_id . ')</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="class">Class</label>
                    <select class="js-example-basic-single w-100 p-2" id="class" title="Class" name="classid" required>
                        <?php
                        $availableClass  = $hos->getClasses($sch_abbr);
                        if (!empty($availableTeachers) && !empty($availableClass)) {
                            foreach ($availableClass as $avaClass) {
                                $cId = (int)$avaClass->id;
                                echo '<option value="' . $cId . '">' . School::getLevelName(Utility::escape($sch_abbr), Utility::escape($avaClass->level)) . ' ' . Utility::escape($avaClass->class) . '</option>';
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
    validate('classForm');;
</script>