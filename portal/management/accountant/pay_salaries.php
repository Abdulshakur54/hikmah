<?php
require_once './includes/accountant.inc.php';
$acct = new Account();
$payment_months = $acct->getPaymentMonths();
$recipients = Utility::escape(Input::get('recipients'));
$payment_month = Utility::escape(Input::get('payment_month'));

function selectedPaymentMonth($pm)
{
    global $payment_month;
    if ($payment_month == $pm) {
        return 'selected';
    }
}
?>
<style>
    tr>td:nth-child(3)>input {
        width: 80px;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Pay <?php echo ucfirst($recipients) ?> Salaries</h4>
            <form class="forms-sample" id="paymentForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="month">Payment Month</label>
                    <select class="js-example-basic-single w-100 p-2" id="payment_month" title="Payment Month" name="payment_month" required onchange="reload()">
                        <?php
                        foreach ($payment_months as $pm) {
                        ?>
                            <option value="<?php echo $pm->id ?>" <?php echo selectedPaymentMonth($pm->id) ?>><?php echo $pm->month . ', ' . $pm->year ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
                if ($recipients == 'staff') {
                ?>
                    <div class="form-group">
                        <label for="school">School</label>
                        <select class="js-example-basic-single w-100 p-2" id="school" title="School" name="school" required onchange="reload()">
                            <option value="">:::Select School:::</option>
                            <?php
                            foreach (School::getSchools(2) as $school) {
                            ?>
                                <option value="<?php echo $school ?>"><?php echo $school ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                <?php
                }
                ?>

                <div class="form-group">
                    <label for="percentage">Percentage of remaining salary (%)</label>
                    <select class="js-example-basic-single w-100 p-2" id="percentage" title="Percentage" name="percentage" required onchange="reload()">
                        <option value="100">100</option>
                        <option value="75">75</option>
                        <option value="50">50</option>
                        <option value="25">25</option>
                        <option value="10">10</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="payment_type">Type</label>
                    <select class="js-example-basic-single w-100 p-2" id="payment_type" title="Type" name="payment_type" required>
                        <option value="online">Online</option>
                        <option value="manual">Manual</option>
                    </select>
                </div>
                <div class="m-3 text-right">
                    <label for="selectAll" id="selectAll">Select all</label>
                    <input type="checkbox" name="selectAll" onclick="checkAll(this)" checked />
                </div>
                <div class="table-responsive">
                    <table class="table table-hover display" id="paymentTable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Amount (&#8358;)</th>
                                <th>Received (&#8358;)</th>
                                <th>Salary (&#8358;)</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody">

                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary mr-2" onclick="paySalary()" id="payBtn">Pay</button><span id="ld_loader"></span>
                    <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                    <input type="hidden" value="<?php echo $recipients ?>" name="recipients" id="recipients" />
                    <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                    <input type="hidden" value="<?php echo $username ?>" name="username" id="username" />
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(".js-example-basic-single").select2();
    var table = $("#paymentTable").DataTable(dataTableOptions);
    var recipients = _('recipients').value;
    var rowCount = 0; //stores the no of table rows to help determines the no of recipients
    if (recipients == 'management') {
        reload(); //populate on table load if it is management
    }

    function reload() {
        const payment_month = _('payment_month').value;
        const percentage = _('percentage').value;;
        if (recipients == 'staff') {
            const school = _('school').value;
            if (payment_month.length > 0 && school.length > 0 && percentage.length > 0) {
                getRecipients();
            }
        } else {
            if (payment_month.length > 0 && percentage.length > 0) {
                getRecipients();
            }
        }

    }

    function getRecipients() {
        if (validate('paymentForm', {
                validateOnSubmit: true
            })) {

            const payment_month = _('payment_month').value;
            const percentage = parseInt(_('percentage').value);
            const token = _('token').value;
            const recipients = _('recipients').value;
            const op = 'get_recipients';
            if (recipients == 'staff') {
                const school = _('school').value;
                ajaxRequest('management/accountant/responses/responses.php', getRecipientsRsp, 'token=' + token + '&school=' + school + '&payment_month=' + payment_month + '&op=' + op + '&recipients=' + recipients);
            } else {
                ajaxRequest('management/accountant/responses/responses.php', getRecipientsRsp, 'token=' + token + '&payment_month=' + payment_month + '&op=' + op + '&recipients=' + recipients);
            }


            function getRecipientsRsp() {
                const rsp = JSON.parse(xmlhttp.responseText);
                _("token").value = rsp.token;
                if (rsp.status == 200) {
                    const data = rsp.data;
                    table.clear();
                    let row = 0;
                    let amount;
                    for (let dt of data) {
                        amount = getAmount(parseFloat(dt.salary), parseFloat(dt.paid), percentage);
                        table.row.add(['', `${dt.title}. ${formatName(dt.fname,dt.oname,dt.lname)}`, `<input type="text" class="p-2 rounded" pattern="^[0-9][0-9]*([.][0-9]+)?$" id="txt_${dt.receiver}" value="${amount}" onchange="update(this,${row},'${dt.receiver}')" />`, `${formatMoney(dt.paid)}`, `${formatMoney(dt.salary)}`, `${getStatus(dt.status,row)}`, `<input type="checkbox" value="${dt.receiver}" checked />`]);
                        row++;
                    }
                    table.draw();
                    rowCount = row++;
                } else {
                    swalNotify(rsp.message, 'error');
                }
            }

            function getAmount(payable, paid, percentage) {
                return round((payable - paid) * (percentage / 100), 2);
            }


        } else {
            swalNotify('All input fields should be filled with proper values', 'danger');
        }

    }

    function checkAll(event) {
        let checkElements = document.querySelectorAll("tbody input[type=checkbox]");
        if (event.checked) {
            for (let chk of checkElements) {
                chk.checked = true;
            }
        } else {
            for (let chk of checkElements) {
                chk.checked = false;
            }
        }
    }

    async function paySalary() {
        if (validate('paymentForm', {
                validateOnSubmit: true
            })) {
            const payment_month = _('payment_month').value;
            const percentage = parseInt(_('percentage').value);
            const token = _('token').value;
            const recipients = _('recipients').value;
            const op = 'pay_salary';
            const payment_type = _('payment_type').value;
            const username = _('username').value;
            if (await swalConfirm('Are you sure you want to proceed with <span style="font-weight: bold">' + payment_type + '</span> salary payment')) {
                if (rowCount > 0) {
                    const recipientsData = getRecipientsData();

                    if (recipients == 'staff') {
                        const school = _('school').value;
                        ajaxRequest('management/accountant/responses/responses.php', getPaymentRsp, 'token=' + token + '&school=' + school + '&payment_month=' + payment_month + '&op=' + op + '&recipients=' + recipients + '&recipientsData=' + JSON.stringify(recipientsData) + '&payment_type=' + payment_type + '&username=' + username + '&percentage=' + percentage);
                    } else {
                        ajaxRequest('management/accountant/responses/responses.php', getPaymentRsp, 'token=' + token + '&payment_month=' + payment_month + '&op=' + op + '&recipients=' + recipients + '&recipientsData=' + JSON.stringify(recipientsData) + '&payment_type=' + payment_type + '&username=' + username + '&percentage=' + percentage);
                    }
                }else{
                    swalNotify('No salary receiver is selected','warning');
                }

            }


        } else {
            swalNotify('Ensure form fields are properly filled', 'warning');
        }

        function getPaymentRsp() {
            const rsp = JSON.parse(xmlhttp.responseText);
            _("token").value = rsp.token;
            const validStatus = [200, 201, 204];
            if (validStatus.includes(rsp.status)) {
                swalNotify(rsp.message, 'success');
                table.draw();

            } else {
                swalNotify(rsp.message, 'error');
            }
        }
    }

    function getRecipientsData() {
        const tableBody = _('tbody');
        const checkboxes = tableBody.querySelectorAll('input[type = checkbox]');
        const recipientsData = [];
        let id;
        for (let chk of checkboxes) {
            if (chk.checked) {
                id = chk.value;
                recipientsData.push([id, _(`txt_${id}`).value]);
            }
        }
        return recipientsData;
    }

    function update(obj, row, receiver) {
        let newData = `<input type="text" class="p-2 rounded" pattern="^[1-9][0-9]*([.][0-9]+)?$" id="txt_${receiver}" value="${obj.value}" onchange="update(this,${row},'${receiver}')" />`;
        table
            .cell(row, 2)
            .data(newData)
            .draw();

    }

    function getStatus(status, row) {
        switch (status) {
            case 0:
                return `<p class="text-danger" row="status_${row}">Not Payed</p>`;
            case 1:
                return `<p class="message" row="status_${row}">Part Payed</p>`;
            case 2:
                return `<p class="text-success" row="status_${row}">Fully Payed</p>`;
            default:
                return '';
        }
    }
</script>