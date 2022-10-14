<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
function selectedParam($param, $selParam)
{
    if ($param == $selParam) {
        return 'selected';
    }
}


?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Schedules And Initializations</h4>
            <form class="forms-sample" id="scheduleForm" onsubmit="return false" novalidate enctype="multipart/form-data">
                <?php
                $schedule = $staff->getSchedule($classId);
                ?>
                <section class="card border border-secondary mt-3 mb-3">
                    <div class="card-header text-center">
                        <h4>Psychomotor Skills</h4>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label for="punc">Punctuality</label>
                            <select class="js-example-basic-single w-100 p-2" name="punc" id="punc" title="Punctuality">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy1) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy1) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy1) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy1) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy1) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy1) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hon">Honesty</label>
                            <select class="js-example-basic-single w-100 p-2" name="hon" id="hon" title="Honesty">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy2) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy2) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy2) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy2) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy2) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy2) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dhw">Does Homework</label>
                            <select class="js-example-basic-single w-100 p-2" name="dhw" id="dhw" title="Does Homework">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy3) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy3) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy3) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy3) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy3) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy3) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rap">Respect and Politeness</label>
                            <select class="js-example-basic-single w-100 p-2" name="rap" id="rap" title="Respect and Politeness">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy4) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy4) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy4) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy4) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy4) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy4) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sot">Spirit of Teamwork</label>
                            <select class="js-example-basic-single w-100 p-2" name="sot" id="sot" title="Spirit of Teamwork">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy5) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy5) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy5) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy5) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy5) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy5) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rwp">Relationship with Peers</label>
                            <select class="js-example-basic-single w-100 p-2" name="rwp" id="rwp" title=">Relationship with Peers">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy6) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy6) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy6) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy6) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy6) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy6) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ls">Leadership skills</label>
                            <select class="js-example-basic-single w-100 p-2" name="ls" id="ls" title="Leadership skills">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy7) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy7) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy7) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy7) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy7) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy7) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="atw">Attitude to work</label>
                            <select class="js-example-basic-single w-100 p-2" name="atw" id="atw" title="Attitude to work">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy8) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy8) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy8) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy8) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy8) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy8) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ho">Helping others</label>
                            <select class="js-example-basic-single w-100 p-2" name="ho" id="ho" title="Helping others">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy9) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy9) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy9) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy9) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy9) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy9) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="car">Carefulness</label>
                            <select class="js-example-basic-single w-100 p-2" name="car" id="car" title="Carefulness">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy10) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy10) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy10) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy10) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy10) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy10) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="con">Consideration</label>
                            <select class="js-example-basic-single w-100 p-2" name="con" id="con" title="Consideration">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy11) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy11) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy11) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy11) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy11) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy11) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="wi">Works Independently</label>
                            <select class="js-example-basic-single w-100 p-2" name="wi" id="wi" title="Works Independently">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy12) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy12) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy12) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy12) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy12) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy12) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ob">Obedience</label>
                            <select class="js-example-basic-single w-100 p-2" name="ob" id="ob" title="Obedience">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy13) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy13) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy13) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy13) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy13) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy13) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hea">Health</label>
                            <select class="js-example-basic-single w-100 p-2" name="hea" id="hea" title="Health">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy14) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy14) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy14) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy14) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy14) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy14) ?>>NULL</option>
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
                            <select class="js-example-basic-single w-100 p-2" name="vs" id="vs" title="Verbal Skills">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy15) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy15) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy15) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy15) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy15) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy1) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pig">Participation in games</label>
                            <select class="js-example-basic-single w-100 p-2" name="pig" id="pig" title="Participation in games">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy16) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy16) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy16) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy16) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy16) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy16) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pis">Participation in sports</label>
                            <select class="js-example-basic-single w-100 p-2" name="pis" id="pis" title="Participation in sports">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy17) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy17) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy17) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy17) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy17) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy17) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ac">Artistic Creativity</label>
                            <select class="js-example-basic-single w-100 p-2" name="ac" id="ac" title="Artistic Creativity">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy18) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy18) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy18) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy18) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy18) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy18) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pama">Physical and Mental Agility</label>
                            <select class="js-example-basic-single w-100 p-2" name="pama" id="pama" title="Physical and Mental Agility">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy19) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy19) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy19) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy19) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy19) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy19) ?>>NULL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ms">Manual Skill (Dexterity)</label>
                            <select class="js-example-basic-single w-100 p-2" name="ms" id="ms" title="Manual Skill(Dexterity)">
                                <option value="A" <?php echo selectedParam('A', $schedule->psy20) ?>>A</option>
                                <option value="B+" <?php echo selectedParam('B+', $schedule->psy20) ?>>B+</option>
                                <option value="B" <?php echo selectedParam('B', $schedule->psy20) ?>>B</option>
                                <option value="C" <?php echo selectedParam('C', $schedule->psy20) ?>>C</option>
                                <option value="D" <?php echo selectedParam('D', $schedule->psy20) ?>>D</option>
                                <option value="" <?php echo selectedParam("", $schedule->psy20) ?>>NULL</option>
                            </select>
                        </div>

                </section>

                <section class="card border border-secondary mt-3 mb-3">
                    <div class="card-header text-center">
                        <h4>Commentary Settings (For overall average)</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="a1" class="form-label">A1 (75-100)</label>
                            <input type="text" name="a1" value="<?php echo Utility::escape($schedule->a1) ?>" class="form-control" title="A1 Commentary" />
                        </div>
                        <div class="form-group">
                            <label for="b2" class="form-label">B2 (70-74)</label>
                            <input type="text" name="b2" value="<?php echo Utility::escape($schedule->b2) ?>" class="form-control" title="B2 Commentary" />
                        </div>
                        <div class="form-group">
                            <label for="b3" class="form-label">B3 (65-69)</label>
                            <input type="text" name="b3" value="<?php echo Utility::escape($schedule->b3) ?>" class="form-control" title="B3 Commentary" />
                        </div>
                        <div class="form-group">
                            <label for="c4" class="form-label">C4 (60-64)</label>
                            <input type="text" name="c4" value="<?php echo Utility::escape($schedule->c4) ?>" class="form-control" title="C4 Commentary" />
                        </div>
                        <div class="form-group">
                            <label for="c5" class="form-label">C5 (55-59)</label>
                            <input type="text" name="c5" value="<?php echo Utility::escape($schedule->c5) ?>" class="form-control" title="C5 Commentary" />
                        </div>
                        <div class="form-group">
                            <label for="c6" class="form-label">C6 (50-54)</label>
                            <input type="text" name="c6" value="<?php echo Utility::escape($schedule->c6) ?>" class="form-control" title="C6 Commentary" />
                        </div>
                        <div class="form-group">
                            <label for="d7" class="form-label">D7 (45-49)</label>
                            <input type="text" name="d7" value="<?php echo Utility::escape($schedule->d7) ?>" class="form-control" title="D7 Commentary" />
                        </div>
                        <div class="form-group">
                            <label for="e8" class="form-label">E8 (40-44)</label>
                            <input type="text" name="e8" value="<?php echo Utility::escape($schedule->e8) ?>" class="form-control" title="E8 Commentary" />
                        </div>
                        <div class="form-group">
                            <label for="f9" class="form-label">F9 (0-39)</label>
                            <input type="text" name="f9" value="<?php echo Utility::escape($schedule->f9) ?>" class="form-control" title="F9 Commentary" />
                        </div>
                    </div>

                </section>
                <section class="card border border-secondary mt-3 mb-3">
                    <div class="card-header text-center">
                        <h4>Height and Weight</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="height_beg" class="form-label">Height at term began (m)</label>
                            <input type="text" name="height_beg" value="<?php echo Utility::escape($schedule->height_beg) ?>" class="form-control" id="height_beg" title="Height at term began" />
                        </div>
                        <div class="form-group">
                            <label for="height_end" class="form-label">Height at term end (m)</label>
                            <input type="text" name="height_end" value="<?php echo Utility::escape($schedule->height_end) ?>" class="form-control" id="height_end" title="Height at term end" />
                        </div>
                        <div class="form-group">
                            <label for="weight_beg" class="form-label">Weight at term began (kg)</label>
                            <input type="text" name="weight_beg" value="<?php echo Utility::escape($schedule->weight_beg) ?>" class="form-control" id="weight_beg" title="Weight at term began" />
                        </div>
                        <div class="form-group">
                            <label for="weight_end" class="form-label">Weight at term end (kg)</label>
                            <input type="text" name="weight_end" value="<?php echo Utility::escape($schedule->weight_end) ?>" class="form-control" id="weight_end" title="Weight at term end" />
                        </div>
                    </div>

                </section>


                <section>
                    <h4>Signature</h4>
                    <div>
                        <label for="signature" id="uploadTrigger" style="cursor: pointer; color:blue;">Upload Signature</label>
                        <div>
                            <input type="file" name="signature" id="signature" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png" />
                            <img id="image" width="100" height="100" src="<?php echo $url->to('uploads/signatures/' . Utility::escape($schedule->signature), 2) ?>" />
                            <input type="hidden" name="hiddenPic" value="" id="hiddenPic" />
                            <div id="picMsg" class="errMsg"></div>
                        </div>
                    </div>
                </section>
                <div class="text-center p-3">
                    <button onclick="saveChanges()" class="btn btn-primary btn-md">Save changes</button>
                </div>
                <input type="hidden" value="<?php echo $currTerm; ?>" name="current_term" id="current_term" />
                <input type="hidden" value="<?php echo $sch_abbr ?>" name="school" id="school" />
                <input type="hidden" value="<?php echo $username ?>" name="username" id="username" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
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
<script src="scripts/staff/schedule.js"></script>
<script>
    validate('scheduleForm');
</script>