<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
function selectedParam($param, $selParam)
{
    if ($param == $selParam) {
        return 'selected';
    }
}

$std_id = (Input::get('std_id')) ? Utility::escape(Input::get('std_id')) : '';
$psy_data = (!empty($std_id)) ? $staff->getStdPsyData($std_id, $currTerm) : '';
function selectedStudent($student_id)
{
    global $std_id;
    if ($std_id == $student_id) {
        return 'selected';
    }
}
$students = $staff->getStudents($classId);

?>

<style>
    .gray{
        background-color:#4f4f4f;
    }
    .position-fixed-container {
        position: relative;
    }

    .position-fixed {
        position: fixed;
        z-index: 1000;
        left: 60%;
        transform: translateX(-50%);
        color: #4B49AC !important;
        border: 2px solid #4B49AC;
        background-color: #b3b1de;
        width: 50%;

    }

    @media screen and (max-width:1100px) {
        .position-fixed {
            left: 80%;
            transform: translate(-83%);
            width: 50%;
        }
    }

        @media screen and (max-width:992px) {
            .position-fixed {
                left: 50%;
                transform: translateX(-45%);
                width: 70%;
            }

        }
</style>


<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body position-fixed-container">
            <div class="row d-flex flex-column align-content-center position-fixed p-3">
                <div class="row d-flex justify-content-center">
                    <div class=" form-group d-flex align-items-center font-weight-bold flex-wrap">
                        <select class="js-example-basic-single w-100 p-2 flex-grow-1" id="student" title="term" name="student" required onchange="changeStudent()" title="Student">
                            <option value="">:::Select Student:::</option>
                            <?php
                            foreach ($students as $student) {
                            ?>
                                <option value="<?php echo $student->std_id ?>" <?php echo selectedStudent($student->std_id) ?>>
                                    <?php
                                    echo Utility::formatName($student->fname, $student->oname, $student->lname)
                                    ?>
                                </option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-around">
                    <button onclick="saveChanges()" class="btn btn-primary mr-2 btn-sm">Save</button>
                    <button onclick="getPage('staff/students.php')" class="btn gray text-white mr-2 btn-sm">Return</button>
                </div>
            </div>
            <?php

            if (!empty($std_id)) {
            ?>


                <form class="forms-sample" id="scheduleForm" onsubmit="return false" novalidate enctype="multipart/form-data" style="margin-top:130px;">
                    <section class="card border  border-secondary  mb-3 ">
                        <div class="card-header text-center">
                            <h4>Psychomotor Skills</h4>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label for="punc">Punctuality</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy1" id="psy1" title="Punctuality" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy1'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy1'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy1'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy1'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy1'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy1'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="hon">Honesty</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy2" id="psy2" title="Honesty" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy2'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy2'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy2'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy2'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy2'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy2'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dhw">Does Homework</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy3" id="psy3" title="Does Homework" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy3'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy3'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy3'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy3'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy3'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy3'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="rap">Respect and Politeness</label>
                                <select class="js-example-basic-single w-100 p-2" name="rapsy4" id="rapsy4" title="Respect and Politeness" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy4'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy4'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy4'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy4'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy4'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy4'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sot">Spirit of Teamwork</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy5" id="psy5" title="Spirit of Teamwork" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy5'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy5'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy5'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy5'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy5'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy5'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="rwp">Relationship with Peers</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy6" id="psy6" title=">Relationship with Peers" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy6'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy6'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy6'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy6'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy6'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy6'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ls">Leadership skills</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy7" id="psy7" title="Leadership skills" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy7'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy7'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy7'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy7'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy7'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy7'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="atw">Attitude to work</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy8" id="psy8" title="Attitude to work" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy8'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy8'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy8'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy8'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy8'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy8'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ho">Helping others</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy9" id="psy9" title="Helping others" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy9'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy9'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy9'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy9'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy9'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy9'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="car">Carefulness</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy10" id="psy10" title="Carefulness" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy10'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy10'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy10'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy10'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy10'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy10'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="con">Consideration</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy11" id="psy11" title="Consideration" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy11'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy11'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy11'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy11'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy11'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy11'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="wi">Works Independently</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy12" id="psy12" title="Works Independently" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy12'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy12'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy12'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy12'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy12'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy12'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ob">Obedience</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy13" id="psy13" title="Obedience" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy13'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy13'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy13'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy13'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy13'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy13'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="hea">Health</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy14" id="psy14" title="Health" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy14'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy14'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy14'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy14'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy14'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy14'}) ?>>NULL</option>
                                </select>
                            </div>
                    </section>

                    <section class="card border border-secondary mt-3 mb-3">
                        <div class="card-header text-center">
                            <h4>Assessment of Behaviour</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="vs">Verbal Skills</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy15" id="psy15" title="Verbal Skills" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy15'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy15'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy15'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy15'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy15'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy15'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pig">Participation in games</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy16" id="psy16" title="Participation in games" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy16'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy16'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy16'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy16'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy16'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy16'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pis">Participation in sports</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy17" id="psy17" title="Participation in sports" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy17'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy17'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy17'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy17'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy17'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy17'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ac">Artistic Creativity</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy18" id="psy18" title="Artistic Creativity" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy18'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy18'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy18'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy18'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy18'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy18'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pama">Physical and Mental Agility</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy19" id="psy19" title="Physical and Mental Agility" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy19'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy19'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy19'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy19'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy19'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy19'}) ?>>NULL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ms">Manual Skill (Dexterity)</label>
                                <select class="js-example-basic-single w-100 p-2" name="psy20" id="psy20" title="Manual Skill(Dexterity)" required>
                                    <option value="A" <?php echo selectedParam('A', $psy_data->{$currTerm . '_psy20'}) ?>>A</option>
                                    <option value="B+" <?php echo selectedParam('B+', $psy_data->{$currTerm . '_psy20'}) ?>>B+</option>
                                    <option value="B" <?php echo selectedParam('B', $psy_data->{$currTerm . '_psy20'}) ?>>B</option>
                                    <option value="C" <?php echo selectedParam('C', $psy_data->{$currTerm . '_psy20'}) ?>>C</option>
                                    <option value="D" <?php echo selectedParam('D', $psy_data->{$currTerm . '_psy20'}) ?>>D</option>
                                    <option value="" <?php echo selectedParam("", $psy_data->{$currTerm . '_psy20'}) ?>>NULL</option>
                                </select>
                            </div>

                    </section>

                    <section class="card border border-secondary mt-3 mb-3">
                        <div class="card-header text-center">
                            <h4>Height and Weight</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="height_beg" class="form-label">Height at term began (m)</label>
                                <input type="text" name="height_beg" value="<?php echo $psy_data->{$currTerm . '_height_beg'} ?>" class="form-control" id="height_beg" title="Height at term began" />
                            </div>
                            <div class="form-group">
                                <label for="height_end" class="form-label">Height at term end (m)</label>
                                <input type="text" name="height_end" value="<?php echo $psy_data->{$currTerm . '_height_end'} ?>" class="form-control" id="height_end" title="Height at term end" />
                            </div>
                            <div class="form-group">
                                <label for="weight_beg" class="form-label">Weight at term began (kg)</label>
                                <input type="text" name="weight_beg" value="<?php echo $psy_data->{$currTerm . '_weight_beg'} ?>" class="form-control" id="weight_beg" title="Weight at term began" />
                            </div>
                            <div class="form-group">
                                <label for="weight_end" class="form-label">Weight at term end (kg)</label>
                                <input type="text" name="weight_end" value="<?php echo $psy_data->{$currTerm . '_weight_end'} ?>" class="form-control" id="weight_end" title="Weight at term end" />
                            </div>
                        </div>

                    </section>

                    <section class="card border border-secondary mt-3 mb-3">
                        <div class="card-header text-center">
                            <h4>Commentary</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <input type="text" name="comment" value="<?php echo $psy_data->{$currTerm . '_com'} ?>" class="form-control" id="comment" title="Comment" />
                            </div>
                        </div>

                    </section>
                    <input type="hidden" value="<?php echo $currTerm; ?>" name="current_term" id="current_term" />
                    <input type="hidden" value="<?php echo $std_id ?>" name="std_id" id="student" />
                    <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />

                </form>

            <?php
            }

            ?>

        </div>
    </div>
</div>
<?php
$msg = Session::get_flash('post_method_success_message');
if (!empty($msg)) {
?>
    <script>
        swalNotifyDismiss('<?php echo $msg ?>', 'success', 2000);
    </script>
<?php
}

?>
<script src="scripts/staff/student_psy.js"></script>
<script>
    validate('scheduleForm');
</script>