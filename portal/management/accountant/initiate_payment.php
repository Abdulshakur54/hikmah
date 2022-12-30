<?php
require_once './includes/accountant.inc.php';
$recipients = Utility::escape(Input::get('recipients'));

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Initiate Salary Month</h4>
            <form class="forms-sample" id="initiateForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="month">Year</label>
                    <select class="js-example-basic-single w-100 p-2" id="year" title="Year" name="year" required>
                        <?php
                        $current_year = (int)date('Y');
                        foreach (Utility::get_years() as $year) {
                        ?>
                            <option value="<?php echo $year ?>" <?php echo ($current_year == $year) ? 'selected' : '' ?>><?php echo $year ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="month">Month</label>
                    <select class="js-example-basic-single w-100 p-2" id="month" title="Month" name="month" required>
                        <?php
                        $current_month = date('F');
                        foreach (Utility::get_months() as $monthName) {
                        ?>
                            <option value="<?php echo $monthName ?>" <?php echo ($current_month == $monthName) ? 'selected' : '' ?>><?php echo $monthName ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <button type="button" class="btn btn-primary mr-2" onclick="initiate()" id="initiateBtn">Initiate</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script>
    async function initiate() {
        if (await swalConfirm('Confirm to proceed','question')) {
            if (validate('initiateForm', {
                    validateOnSubmit: true
                })) {
                ld_startLoading("initiateBtn");
                await getPostPageWithUpload(
                    "initiateForm",
                    "management/accountant/responses/responses.php", {
                        op: "initiate_payment"
                    },
                    false
                );
                ld_stopLoading("initiateBtn");
            }
        }

    }
    $(".js-example-basic-single").select2();
</script>