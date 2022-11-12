<?php
require_once './includes/apm.inc.php';
function selectedTerm($val)
{
    global $schedules;
    $curr_term = Utility::escape($schedules->current_term);
    if (strtolower($curr_term) == $val) {
        return 'selected';
    }
}

function selectedSchool($abbr)
{

    if (Input::submitted()) {
        $sch_abbr = Input::get('school');
        if ($sch_abbr == $abbr) {
            return 'selected';
        }
    }
}
?>
<style>
    @media only screen and (max-width:465px) {
        #scheduleBtn, #saveBtn {
            width: 100% !important;
        }
    }
    @media only screen and (max-width:765px){
        #logoContainer{
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
                        $schools = School::getConvectionalSchools();
                        $genHtml = '<option value="">:::Select School:::</option>';
                        foreach ($schools as $sch => $sch_abbr) {
                            $genHtml .= '<option value="' . $sch_abbr . '" ' . selectedSchool($sch_abbr) . '>' . $sch . '</option>';
                        }
                        echo $genHtml;
                        ?>
                    </select>
                </div>
                <input type="hidden" name="submittype" id="submitType" /> <!-- To help detect the type of submission -->
                <?php
                if (Input::submitted() && Token::check(Input::get('token'))) {
                    $submitType = Input::get('submittype');
                    $sch_abbr = Input::get('school');
                    if ($submitType === 'browse') {
                        $schedules = $apm->getSchedules($sch_abbr);
                        $formFee = Utility::escape($schedules->form_fee);
                        $regFee = Utility::escape($schedules->reg_fee);
                        $ftsf = Utility::escape($schedules->ft_fee);
                        $stsf = Utility::escape($schedules->st_fee);
                        $ttsf = Utility::escape($schedules->tt_fee);
                        $logo = Utility::escape($schedules->logo);
                ?>
                        <div class="form-group">
                            <label for="term">Current Term</label>
                            <select class="js-example-basic-single w-100" id="term" title="term" name="term" required>
                                <option value="ft">First Term</option>
                                <option value="st">Second Term</option>
                                <option value="tt">Third Term</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="formfee">Form Fees(&#8358;)</label>
                            <input type="text" class="form-control" id="formfee" onfocus="clearHTML('messageContainer')" title="Form Fees" required pattern="^[0-9.]+$" name="formfee" value="<?php echo $formFee; ?>">
                        </div>
                        <div class="form-group">
                            <label for="regfee">Registration Fees(&#8358;)</label>
                            <input type="text" class="form-control" id="regfee" onfocus="clearHTML('messageContainer')" title="Registration Fees" required pattern="^[0-9.]+$" name="regfee" value="<?php echo $regFee; ?>">
                        </div>
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

                    <?php
                        //for school logo
                        echo '<div id="logoContainer">
                                    <label for = "logo" id="uploadTrigger" style="cursor: pointer; color:blue;">Change Logo</label>
                                    <div>
                                        <input type="file" name="logo" id="logo" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png"/>
                                        <img id="image" width="100" height="100" src="management/apm/uploads/logo/' . $logo . '" />
                                        <input type="hidden" name="hiddenPic" value="" id="hiddenPic"/>
                                        <div id="picMsg" class="errMsg"></div>
                                    </div>
                                </div>';
                    }
                    ?>
                    <div id="messageContainer"></div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary mr-2" id="saveBtn" onclick="saveChanges()">Save Changes</button><span id="ld_loader"></span>
                    </div>
                <?php
                }

                ?>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/apm/schedules.js"></script>
<script>
    validate('schedulesForm');
</script>