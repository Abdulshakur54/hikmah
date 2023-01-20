<?php

require_once './includes/accountant.inc.php';
$schools = School::getSchools(2);
$utils = new Utils();
function selectedSession($ses)
{
    global $currSession;
    if ($currSession == $ses) {
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
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">School Fees</h4>
            <form class="forms-sample" id="proceedForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="school">School</label>
                    <select class="js-example-basic-single w-100 p-2" id="school" title="School" name="school" required>
                        <?php
                        foreach ($schools as $sch) {
                        ?>
                            <option value="<?php echo $sch ?>"><?php echo $sch ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="session">Session</label>
                    <select class="js-example-basic-single w-100 p-2" id="session" title="Session" name="session" required>
                        <option value="">:::Select Session:::</option>
                        <?php
                        $sch_abbr = $schools[0];
                        $sess = Ses::get();
                        $currTerm = $utils->getCurrentTerm($sch_abbr);
                        $currSession = $utils->getSession($sch_abbr);
                        foreach ($sess as $ses) {
                        ?>
                            <option value="<?php echo $ses->session ?>" <?php echo selectedSession($ses->session) ?>><?php echo $ses->session ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="term">Term</label>
                    <select class="js-example-basic-single w-100 p-2" id="term" title="Term" name="term" required>
                        <option value="ft" <?php echo selectedTerm('ft') ?>>FT</option>
                        <option value="st" <?php echo selectedTerm('st') ?>>ST</option>
                        <option value="tt" <?php echo selectedTerm('tt') ?>>TT</option>
                    </select>
                </div>
                <div class="d-flex justify-content-center p-3">
                    <button type="button" class="btn btn-primary mr-2" onclick="proceed()" id="proceedBtn">Proceed</button>
                </div>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script>
    function proceed() {
        if (validate('proceedForm', {
                validateOnSubmit: true
            })) {
            const token = _('token').value;
            const term = _('term').value;
            const session = _('session').value;
            const school = _('school').value;
            getPage('management/accountant/pay_school_fees.php?school=' + school + '&session=' + session + '&term=' + term + '&token=' + token);
        }

    }


    validate('proceedForm')
    $(".js-example-basic-single").select2();
</script>