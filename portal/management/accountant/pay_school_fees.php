<?php
require_once './includes/accountant.inc.php';
$sch_abbr = Utility::escape(Input::get('school'));
$term = Utility::escape(Input::get('term'));
$session = Utility::escape(Input::get('session'));
$students = Account::getSchoolFeeStudents($sch_abbr, $session, $term);

?>
<style>
    .label {
        width: 100px;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Pay School Fees</h4>
            <form class="forms-sample" id="paySchoolFeeForm" onsubmit="return false" novalidate>
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
                        <div class="label font-weight-bold">School Fee</div>
                        <div id="schoolfee"></div>
                    </div>
                    <div class="d-flex justify-content-start my-1">
                        <div class="label font-weight-bold">Paid</div>
                        <div id="paid"></div>
                    </div>
                    <div class="d-flex justify-content-start my-1">
                        <div class="label font-weight-bold">Remaining</div>
                        <div id="remaining"></div>
                    </div>
                    <div class="d-flex justify-content-start my-1">
                        <div class="label font-weight-bold">Status</div>
                        <div id="status"></div>
                    </div>
                    <div class="form-group mt-5" id="paymentDiv">
                        <label for="amount">Payment Amount(&#8358;)</label>
                        <input type="text" class="form-control" id="amount" title="Payment Amount" required pattern="^[0-9][0-9]*([.][0-9]+)?$" name="amount">
                    </div>
                </div>

                <button type="button" class="btn btn-primary mr-2" id="payBtn" onclick="paySchoolFee()">Pay</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo $term ?>" name="term" id="term" />
                <input type="hidden" value="<?php echo $session ?>" name="session" id="session" />
                <input type="hidden" value="<?php echo $sch_abbr ?>" name="school" id="school" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script>
    var displayBlock = _('displayBlock');
    var paymentDiv = _('paymentDiv');

    function display(show) {
        let displayVal
        if (show) {
            displayVal = 'block';
        } else {
            displayVal = 'none';
        }
        displayBlock.style.display = displayVal;
        paymentDiv.style.display = displayVal;
    }
    display(false);

    async function paySchoolFee() {
        if (validate('paySchoolFeeForm', {
                validateOnSubmit: true
            })) {
            if (await swalConfirm('Proceed to pay School Fee?', 'warning')) {
                const student = _('student');
                const schoolfee = _('schoolfee');
                const paid = _('paid');
                const remaining = _('remaining');
                const status = _('status');
                const amount = _('amount').value;
                const std_id = student.value;
                const term = _('term').value;
                const session = _('session').value;
                ld_startLoading("payBtn");
                const rsp = await getPostPageWithUpload(
                    "paySchoolFeeForm",
                    "management/accountant/responses/responses.php", {
                        op: "pay_school_fee",
                        std_id,
                        term,
                        session,
                        amount
                    },
                    false, true
                );
                const successCode = [200, 201, 204];
                if (successCode.includes(rsp.status)) {
                    _('amount').value = '';
                    let data = (rsp.data)[0];
                    schoolfee.innerHTML = '&#8358;' + formatMoney(data.amount);
                    paid.innerHTML = '&#8358;' + formatMoney(data.paid);
                    remaining.innerHTML = '&#8358;' + formatMoney(parseFloat(data.amount) - parseFloat(data.paid));
                    status.innerHTML = getStatus(parseInt(data.status));
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
        const schoolfee = _('schoolfee');
        const paid = _('paid');
        const remaining = _('remaining');
        const status = _('status');
        const term = _('term').value;
        const session = _('session').value;
        if (student.value.length > 0) {
            const std_id = student.value;
            ld_startLoading("student", "ld_loader_std");
            const rsp = await getPostPageWithUpload(
                "paySchoolFeeForm",
                "management/accountant/responses/responses.php", {
                    op: "school_fee_details",
                    std_id,
                    term,
                    session
                },
                false, true
            );
            const successCode = [200, 201, 204];
            if (successCode.includes(rsp.status)) {
                let data = (rsp.data)[0];
                let rem = parseFloat(data.amount) - parseFloat(data.paid);
                name.innerHTML = formatName(data.fname, data.oname, data.lname); //
                schoolfee.innerHTML = '&#8358;' + formatMoney(data.amount);
                paid.innerHTML = '&#8358;' + formatMoney(data.paid);
                remaining.innerHTML = '&#8358;' + formatMoney(rem);
                status.innerHTML = getStatus(parseInt(data.status));
                _('amount').value = rem;
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
            case 1:
                return '<div class="message">Half Paid</div>';
            case 2:
                return '<div class="success">Paid</div>';
            default:
        }
    }
    validate('paySchoolFeeForm')
    $(".js-example-basic-single").select2();
</script>