<?php
require_once './includes/accountant.inc.php';
$students = Account::getRegFeeStudents();

?>
<style>
    .label {
        width: 100px;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Pay Registration Fees</h4>
            <form class="forms-sample" id="payRegFeeForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="student">Student</label>
                    <select class="js-example-basic-single w-100 p-2" id="student" title="Student" name="student" required onchange="showDetails()">
                        <option value="">::: Select Student :::</option>
                        <?php
                        foreach ($students as $std) {
                        ?>
                            <option value="<?php echo $std->std_id ?>"><?php echo Utility::formatName($std->fname, $std->oname, $std->lname) ?> (<?php echo $std->std_id ?>)</option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <span id="ld_loader_std"></span>
                <div class="form-group my-5" id="displayBlock">
                    <div class="d-flex justify-content-start mb-1">
                        <div class="label font-weight-bold">Name</div>
                        <div id="name"></div>
                    </div>
                    <div class="d-flex justify-content-start my-1">
                        <div class="label font-weight-bold">Registration Fee</div>
                        <div id="regfee"></div>
                    </div>
                    <div class="d-flex justify-content-start my-1">
                        <div class="label font-weight-bold">Status</div>
                        <div id="status"></div>
                    </div>

                </div>

                <button type="button" class="btn btn-primary mr-2" id="payBtn" onclick="payRegFee()">Pay</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script>
    var displayBlock = _('displayBlock');

    function display(show) {
        let displayVal
        if (show) {
            displayVal = 'block';
        } else {
            displayVal = 'none';
        }
        displayBlock.style.display = displayVal;
    }
    display(false);

    async function payRegFee() {
        if (validate('payRegFeeForm', {
                validateOnSubmit: true
            })) {
            if (await swalConfirm('Proceed to pay Registration Fee?', 'question')) {
                const student = _('student');
                const std_id = student.value;
                const status = _('status');
                ld_startLoading("payBtn");
                const rsp = await getPostPageWithUpload(
                    "payRegFeeForm",
                    "management/accountant/responses/responses.php", {
                        op: "pay_reg_fee",
                        std_id
                    },
                    false, true
                );
                const successCode = [200, 201, 204];
                if (successCode.includes(rsp.status)) {
                    status.innerHTML = getStatus(2);
                    swalNotify(rsp.message, 'success')
                } else {
                    swalNotify(rsp.message, 'warning')
                }
                ld_stopLoading("payBtn");
            }
        }

    }

    async function showDetails() {
        const student = _('student');
        const name = _('name');
        const regfee = _('regfee');
        const status = _('status');
        if (student.value.length > 0) {
            const std_id = student.value;
            ld_startLoading("student", "ld_loader_std");
            const rsp = await getPostPageWithUpload(
                "payRegFeeForm",
                "management/accountant/responses/responses.php", {
                    op: "reg_fee_details",
                    std_id,
                },
                false, true
            );
            const successCode = [200, 201, 204];
            if (successCode.includes(rsp.status)) {
                let data = (rsp.data)[0];
                name.innerHTML = formatName(data.fname, data.oname, data.lname); //
                regfee.innerHTML = '&#8358;' + formatMoney(data.reg_fee);
                status.innerHTML = getStatus(parseInt(data.status));
            }
            ld_stopLoading("student", "ld_loader_std");
            display(true);
        } else {
            display(false)
        }
    }


    function getStatus(sta) {
        switch (sta) {
            case 0:
                return '<div class="failure">Not Paid</div>';
            case 2:
                return '<div class="success">Paid</div>';
            default:
        }
    }
    validate('payRegFeeForm')
    $(".js-example-basic-single").select2();
</script>