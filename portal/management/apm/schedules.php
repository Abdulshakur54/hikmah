<?php
require_once './includes/apm.inc.php';
function selectedLevel($lev)
{
    global $level;
    if ($level == $lev) {
        return 'selected';
    }
}
function selectedTerm($term)
{
    global $currTerm;
    if ($currTerm == $term) {
        return 'selected';
    }
}

function selectedSchool($abbr)
{

    global $sch_abbr;
    if ($sch_abbr == $abbr) {
        return 'selected';
    }
}
$sch_abbr = Utility::escape(Input::get('school'));
$level = Utility::escape(Input::get('level'));
$currTerm = Utility::escape(Input::get('currTerm'));
if (empty($currTerm)) { //set default current term
    $currTerm = 'ft';
}
?>
<style>
    @media only screen and (max-width:465px) {

        #scheduleBtn,
        #saveBtn {
            width: 100% !important;
        }
    }

    @media only screen and (max-width:765px) {
        #logoContainer {
            text-align: center;
        }
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Schedules and Fees</h4>
            <form class="forms-sample" id="schedulesForm" onsubmit="return false" novalidate enctype="multipart/form-data">

                <div class="form-group">
                    <label for="school">School</label>
                    <select class="js-example-basic-single w-100" id="school" title="School" name="school" onchange="submitForm()" required>
                        <?php
                        if ($rank) {
                            $schools = School::getConvectionalSchools();
                        } else {
                            $schools = School::getIslamiyahSchools();
                        }
                        $genHtml = '';
                        foreach ($schools as $sch => $abbr) {
                            $genHtml .= '<option value="' . $abbr . '" ' . selectedSchool($abbr) . '>' . $sch . '</option>';
                        }
                        echo $genHtml;
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="level">Level</label>
                    <select class="js-example-basic-single w-100" id="level" title="Level" name="level" onchange="submitForm()" required>
                        <?php

                        $genHtml = '';
                        $levels = School::getLevels($sch_abbr);

                        foreach ($levels as $levName => $lev) {
                            $genHtml .= '<option value="' . $lev . '" ' . selectedLevel($lev) . '>' . $levName . '</option>';
                        }
                        echo $genHtml;
                        ?>
                    </select>
                </div>
                <?php if (empty($level)) { //select first level by default when no level is selected
                    foreach ($levels as $levName => $lev) {
                        $level = $lev;
                        break;
                    }
                } ?>
                <?php

                $schedules = $apm->getSchedules($sch_abbr, (int)$level);
                $formFee = Utility::escape($schedules->form_fee);
                $regFee = Utility::escape($schedules->reg_fee);
                $ftsf = Utility::escape($schedules->ft_fee);
                $stsf = Utility::escape($schedules->st_fee);
                $ttsf = Utility::escape($schedules->tt_fee);
                ?>
                <div class="form-group">
                    <label for="currTerm">Current Term</label>
                    <select class="js-example-basic-single w-100" id="currTerm" title="Current Term" name="currTerm" required onchange="submitForm()">
                        <option value="ft" <?php echo selectedTerm('ft') ?>>First Term</option>
                        <option value="st" <?php echo selectedTerm('st') ?>>Second Term</option>
                        <option value="tt" <?php echo selectedTerm('tt') ?>>Third Term</option>
                    </select>
                </div>
                <div class="card card-body border border-1 mb-3">
                    <h5 class="text-primary">Sessional</h5>
                    <div class="form-group">
                        <label for="formfee">Form Fees(&#8358;)</label>
                        <input type="text" class="form-control" id="formfee" onfocus="clearHTML('messageContainer')" title="Form Fees" required pattern="^[0-9.]+$" name="formfee" value="<?php echo $formFee; ?>">
                    </div>
                    <div class="form-group">
                        <label for="regfee">Registration Fees(&#8358;)</label>
                        <input type="text" class="form-control" id="regfee" onfocus="clearHTML('messageContainer')" title="Registration Fees" required pattern="^[0-9.]+$" name="regfee" value="<?php echo $regFee; ?>">
                    </div>
                </div>
                <div class="card card-body border border-1">
                    <h5 class="text-primary">Termly</h5>
                    <div class="form-group">
                        <label for="ftsf">First Term School Fees(&#8358;)</label>
                        <input type="text" class="form-control" id="ftsf" onfocus="clearHTML('messageContainer')" title="First Term School Fees" required pattern="^[0-9.]+$" name="ftsf" value="<?php echo $ftsf; ?>">
                    </div>
                    <div class="form-group">
                        <label for="stsf">Second Term School Fees(&#8358;)</label>
                        <input type="text" class="form-control" id="stsf" onfocus="clearHTML('messageContainer')" title="Second Term School Fees" required pattern="^[0-9.]+$" name="stsf" value="<?php echo $stsf; ?>">
                    </div>
                    <div class="form-group">
                        <label for="ttsf">Third Term School Fees(&#8358;)</label>
                        <input type="text" class="form-control" id="ttsf" onfocus="clearHTML('messageContainer')" title="Third Term School Fees" required pattern="^[0-9.]+$" name="ttsf" value="<?php echo $ttsf; ?>">
                    </div>
                </div>

                <div id="messageContainer"></div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary mr-2" id="saveBtn" onclick="saveChanges()">Save Changes</button><span id="ld_loader"></span>
                </div>

                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/apm/schedules.js"></script>
<script>
    validate('schedulesForm');
</script>