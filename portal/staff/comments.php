<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
$token = Token::generate();
$res_url = $url->to('students_result.php?session=' . $currSession . '&term=' . $currTerm . '&school=' . $sch_abbr . '&token=' . $token, 0);
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary"><?php echo Utility::formatTerm($currTerm) . ' ' . School::getLevelName($sch_abbr, $level) . ' ' . $class . ' Commentary' ?></h4>
            <?php
            if (Input::submitted('get') && !empty(Input::get('std_id'))) {
                $std_id = Utility::escape(Input::get('std_id'));
                $students = Student::getStudentsWithComments($classId, $currTerm, $std_id);
            } else {

                $students = Student::getStudentsWithComments($classId, $currTerm);
            }
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
                            <input type="text" value="<?php echo $student->{$currTerm . '_com'} ?>" class="w-100 p-3 fs-5" id="<?php echo str_replace('/', '_', $student->std_id) ?>" onchange="storeId(this)" />
                        </div>
                    </div>
                <?php
                }

                ?>
                <div class="card-footer d-flex justify-content-center bg-light">
                    <input type="hidden" value="<?php echo $currTerm ?>" name="term" id="term" />
                    <input type="hidden" value="<?php echo $token ?>" name="token" id="token" />
                    <button type="button" class="btn btn-primary mr-2 btn-md mx-3" id="saveBtn" onclick="save()">Save</button><span id="ld_loader"></span>
                    <button type="button" class="btn btn-light btn-md mx-3" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>

                </div>

            <?php
            } else {
                echo '<div class="message text-center">No Students in this class</div>';
            }

            ?>

        </div>
    </div>
</div>

<script src="scripts/staff/comments.js"></script>