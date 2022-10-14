<?php
require_once './includes/hos.inc.php';

if (!Input::submitted('get')) {
    Redirect::to(404);
}
$classes = $hos->getClasses($sch_abbr);
$classId = Input::get('class_id');
$selectedClassObj;;
function selectedClass($class)
{
    global $classId, $selectedClassObj;
    if ($class->id == $classId) {
        $selectedClassObj = $class;
        return 'selected';
    }
}
$token = Token::generate();
$res_url = $url->to('students_result.php?session=' . $currSession . '&term=' . $currTerm . '&school=' . $sch_abbr . '&token=' . $token, 0);
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            <div class="form-group">
                <label for="class">Class</label>
                <select class="js-example-basic-single w-100 p-2" id="class" name="class" onchange="changeClass(this)">
                    <option value="">:::Select Class:::</option>
                    <?php
                    foreach ($classes as $class) {
                    ?>
                        <option value="<?php echo $class->id ?>" <?php echo selectedClass($class) ?>><?php echo School::getLevelName($sch_abbr, $class->level) . ' ' . $class->class ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>

            <?php
            if (empty($classId)) {
                echo '<div class="message">Select a class first</div>';
            } else {
                $students = Student::getStudentsWithComments($classId, $currTerm);
            ?>
                <h4 class="card-title text-primary"><?php echo Utility::formatTerm($currTerm) . ' ' . School::getLevelName($sch_abbr, $selectedClassObj->level) . ' ' . $selectedClassObj->class . ' Commentary' ?></h4>
                <?php

                if (!empty($students)) {
                    foreach ($students as $student) {
                ?>

                        <div class="card border border-1 rounded mb-3">
                            <div class="card-header text-primary d-flex justify-content-between align-items-center">
                                <div class="font-weight-bold">
                                    <?php echo Utility::formatName($student->fname, $student->oname, $student->lname) . ' (' . $student->std_id . ')' ?> 
                                </div>
                                <a href="<?php echo $res_url . '&student_ids=' . urlencode(json_encode([$student->std_id])) ?>" style="color:#494949;">result</a>
                            </div>

                            <div class="card-body">
                                <input type="text" value="<?php echo $student->{$currTerm . '_p_com'} ?>" class="w-100 p-3 fs-5" id="<?php echo str_replace('/', '_', $student->std_id) ?>" onchange="storeId(this)" />
                            </div>
                        </div>
                    <?php
                    }

                    ?>
                    <div class="card-footer d-flex justify-content-center bg-light">
                        <input type="hidden" value="<?php echo $currTerm ?>" name="term" id="term" />

                        <button type="button" class="btn btn-primary mr-2 btn-md mx-3" id="saveBtn" onclick="save()">Save</button><span id="ld_loader"></span>
                        <button type="button" class="btn btn-light btn-md mx-3" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>

                    </div>

                <?php
                } else {
                    echo '<div class="message">No Students in this class</div>';
                }

                ?>

        </div>
    </div>
</div>
<?php
            }
?>
<input type="hidden" value="<?php echo $token ?>" name="token" id="token" />
<script src="scripts/management/hos/comments.js"></script>