<?php
require_once './includes/hos.inc.php';
$schedule = $hos->getSchedule($sch_abbr);
$errCode = 0;
if (empty($schedule)) {
    exit(); // some data needs to be inserted for initials in table schedule2;
}

function get_file(){
    global $schedule;
    $url = new Url();
    if(!empty(Utility::escape($schedule->signature))){
        return $url->to('hos/uploads/signatures/' . Utility::escape($schedule->signature),1);
    }
    return;
}

?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Schedules And Initializations</h4>
            <form class="forms-sample" id="scheduleForm" onsubmit="return false" novalidate enctype="multipart/form-data">
                <section class="card border border-secondary mt-3 mb-3">
                    <div class="card-header text-center">
                        <h4>Scores Setting</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="fa" class="form-label">First Assignment</label>
                            <input type="number" name="fa" value="<?php echo Utility::escape($schedule->fa) ?>" id="fa" max="100" min="0" required class="form-control" title="First Assignment" />
                        </div>

                        <div class="form-group">
                            <label for="sa" class="form-label">Second Assignment</label>
                            <input type="number" name="sa" value="<?php echo Utility::escape($schedule->sa) ?>" id="sa" max="100" min="0" required class="form-control" title="Second Assignment"/>
                        </div>

                        <div class="form-group">
                            <label for="ft" class="form-label">First Test</label>
                            <input type="number" name="ft" value="<?php echo Utility::escape($schedule->ft) ?>" id="ft" max="100" min="0" required class="form-control" title="First Test"/>
                        </div class="form-group">

                        <div class="form-group">
                            <label for="st" class="form-label">Second Test</label>
                            <input type="number" name="st" value="<?php echo Utility::escape($schedule->st) ?>" id="st" max="100" min="0" required class="form-control" title="Second Test"/>
                        </div>

                        <div class="form-group">
                            <label for="pro" class="form-label">Project</label>
                            <input type="number" name="pro" value="<?php echo Utility::escape($schedule->pro) ?>" id="pro" max="100" min="0" required class="form-control" title="Project"/>
                        </div>

                        <div class="form-group">
                            <label for="exam" class="form-label">Exam</label>
                            <input type="number" name="exam" value="<?php echo Utility::escape($schedule->exam) ?>" id="exam" max="100" min="0" required class="form-control" title="Exam"/>
                        </div>
                    </div>

                    <div><?php if ($errCode == 1) {
                                echo $genMsg;
                            } ?></div>
                </section>

                <section class="card border border-secondary mt-3 mb-3">
                    <div class="card-header text-center">
                        <h4>Schedules</h4>
                    </div>
                    <div class="card-body">
                        <section class="card-body">
                            <h4>First Term Schedules</h4>

                            <div class="form-group">
                                <label for="ftto" class="form-label">Times Opened</label>
                                <input type="number" name="ftto" value="<?php echo Utility::escape($schedule->ft_times_opened) ?>" id="ftto" max="200" min="0" required class="form-control" title="Times Opened"/>
                            </div>

                            <div class="form-group">
                                <label for="ftrd" class="form-label">Resumption Date(Appears on result)</label>
                                <input type="date" name="ftrd" value="<?php echo Utility::escape($schedule->ft_res_date) ?>" id="ftrd" required class="form-control" title="Resumption Date" />
                            </div>

                            <div class="form-group">
                                <label for="ftcd" class="form-label">Closing Date(Appears on result)</label>
                                <input type="date" name="ftcd" value="<?php echo Utility::escape($schedule->ft_close_date) ?>" id="ftcd" required class="form-control" title="Closing Date"/>
                            </div>
                        </section>
                        <section class="card-body">
                            <h4>Second Term Schedules</h4>
                            <div class="form-group">
                                <label for="stto" class="form-label">Times Opened</label>
                                <input type="number" name="stto" value="<?php echo Utility::escape($schedule->st_times_opened) ?>" id="stto" max="200" min="0" required class="form-control" title="Times Opened" />
                            </div>

                            <div class="form-group">
                                <label for="strd" class="form-label">Resumption Date(To appear on Second Term result)</label>
                                <input type="date" name="strd" value="<?php echo Utility::escape($schedule->st_res_date) ?>" id="strd" required class="form-control" title="Resumption Date" />
                            </div>

                            <div class="form-group">
                                <label for="ftcd" class="form-label">Closing Date(Appears on result)</label>
                                <input type="date" name="stcd" value="<?php echo Utility::escape($schedule->st_close_date) ?>" id="stcd" required class="form-control" title="Closing Date"/>
                            </div>

                        </section>

                        <section class="card-body">
                            <h4>Third Term Schedules</h4>
                            <div class="form-group">
                                <label for="ttto" class="form-label">Times Opened</label>
                                <input type="number" name="ttto" value="<?php echo Utility::escape($schedule->tt_times_opened) ?>" id="ttto" max="200" min="0" required class="form-control" title="Times Opened"/>
                            </div>

                            <div class="form-group">
                                <label for="ttrd" class="form-label">Resumption Date(Appears on result)</label>
                                <input type="date" name="ttrd" value="<?php echo Utility::escape($schedule->tt_res_date) ?>" id="ttrd" required  class="form-control"  title="Resumption Date"/>
                            </div>

                            <div class="form-group">
                                <label for="ttcd" class="form-label">Closing Date(Appears on result)</label>
                                <input type="date" name="ttcd" value="<?php echo Utility::escape($schedule->tt_close_date) ?>" id="ttcd" required  class="form-control"title="Closing Date" />
                            </div>
                        </section>
                    </div>

                </section>


                <section class="card border border-secondary mt-3 mb-3">
                    <div class="card-header text-center">
                        <h4>Commentary Settings (For overall average)</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="a1" class="form-label">A1(75-100)</label>
                            <input type="text" name="a1" value="<?php echo Utility::escape($schedule->a1) ?>" id="a1" required class="form-control" title="A1 Commentary"/>
                        </div>

                        <div class="form-group">
                            <label for="b2" class="form-label">B2(70-74)</label>
                            <input type="text" name="b2" value="<?php echo Utility::escape($schedule->b2) ?>" id="b2" required class="form-control" title="B2 Commentary" />
                        </div>

                        <div class="form-group">
                            <label for="b3" class="form-label">B3(65-69)</label>
                            <input type="text" name="b3" value="<?php echo Utility::escape($schedule->b3) ?>" id="b3" required class="form-control" title="B3 Commentary" />
                        </div>

                        <div class="form-group">
                            <label for="c4" class="form-label">C4(60-64)</label>
                            <input type="text" name="c4" value="<?php echo Utility::escape($schedule->c4) ?>" id="c4" required class="form-control" title="C4 Commentary" />
                        </div>

                        <div class="form-group">
                            <label for="c5" class="form-label">C5(55-59)</label>
                            <input type="text" name="c5" value="<?php echo Utility::escape($schedule->c5) ?>" id="c5" required class="form-control" title="C5 Commentary" />
                        </div>

                        <div class="form-group">
                            <label for="c6" class="form-label">C6(50-54)</label>
                            <input type="text" name="c6" value="<?php echo Utility::escape($schedule->c6) ?>" id="c6" required class="form-control" title="C6 Commentary" />
                        </div>

                        <div class="form-group">
                            <label for="d7" class="form-label">D7(45-49)</label>
                            <input type="text" name="d7" value="<?php echo Utility::escape($schedule->d7) ?>" id="d7" required class="form-control" title="D7 Commentary" />
                        </div>

                        <div class="form-group">
                            <label for="e8" class="form-label">E8(40-44)</label>
                            <input type="text" name="e8" value="<?php echo Utility::escape($schedule->e8) ?>" id="e8" required class="form-control" title="E8 Commentary" />
                        </div>

                        <div class="form-group">
                            <label for="f9" class="form-label">F9(0-39)</label>
                            <input type="text" name="f9" value="<?php echo Utility::escape($schedule->f9) ?>" id="f9" required class="form-control" title="F9 Commentary" />
                        </div>
                    </div>

                </section>



                <section class="card border border-secondary mt-3 mb-3">
                    <div class="card-header text-center">
                        <h4>Signature</h4>
                    </div>
                    <div class="card-body">
                        <div>
                            <label for="signature" id="uploadTrigger" style="cursor: pointer; color:blue;">Upload Signature</label>
                            <div>
                                <?php $file = get_file();?>
                                <input type="file" name="signature" id="signature" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png" />
                                <img id="image" width="100" height="100" src="<?php echo !empty($file)?$file:''; ?>" />
                                <input type="hidden" name="hiddenPic" value="" id="hiddenPic" />
                                <div id="picMsg" class="errMsg"></div>
                            </div>
                        </div>
                    </div>
                </section>
                <div class="text-center p-3">
                    <button onclick="saveChanges()" class="btn btn-primary btn-md">Save changes</button>
                </div>
                <input type="hidden" value="<?php echo $currTerm; ?>" name="current_term" id="current_term" />
                <input type="hidden" value="<?php echo $sch_abbr ?>" name="school" id="school" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/hos/schedule.js"></script>
<script>
    validate('scheduleForm');;
</script>