<?php
require_once './includes/accountant.inc.php';
$payment_month = (int) Utility::escape(Input::get('payment_month'));
$acct = new Account();
$salary_months = $acct->getSalaryMonths();

function selectedSalaryMonth($month_id)
{
    global $payment_month;
    if ($payment_month == $month_id) {
        return 'selected';
    }
}


?>

<div class="grid-margin stretch-card">
    <div class="card">
        <form id="salaryPayablesForm" onsubmit="return false">
            <div class="card-body">
                <h4 class="card-title text-primary">Salary Payables </h4>
                <div class="form-group">
                    <label for="salary_month">Salary Month</label>
                    <select class="js-example-basic-single w-100 p-2" id="salary_month" title="salary_month" name="salary_month" required onchange="getSalaryPayables()">
                        <option value="ALL">ALL</option>
                        <?php
                        foreach ($salary_months as $salary_month) {
                        ?>
                            <option value="<?php echo $salary_month->id ?>" <?php echo selectedSalaryMonth($salary_month->id) ?>><?php echo $salary_month->month . ', ' . $salary_month->year ?></option>
                        <?php
                        } ?>
                    </select>
                </div>
                <span id="ld_loader"></span>
                <div class="form-group">
                    <label for="category">School</label>
                    <select class="js-example-basic-single w-100 p-2" id="school" title="school" name="school" required onchange="getSalaryPayables()">
                        <option value="ALL">ALL</option>
                        <?php
                        foreach (School::getSchools(2) as $sch_abbr) {
                        ?>
                            <option value="<?php echo $sch_abbr ?>"><?php echo $sch_abbr ?></option>
                        <?php
                        } ?>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover display" id="salaryPayables">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>ID</th>
                                <th>Payable (&#8358;)</th>
                                <th>School</th>
                                <th>Payment Month</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="p-3 d-flex justify-content-center">
                    <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                </div>
            </div>
            <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
        </form>

    </div>
    <script>
        var table = $("#salaryPayables").DataTable(dataTableOptions);

        async function getSalaryPayables() {
            const salaryMonth = _('salary_month').value;
            const school = _('school').value;
            if (school.length > 0 && salaryMonth.length > 0) {
                ld_startLoading('salary_month');
                ld_startLoading('school');
                const rsp = await getPostPageWithUpload(
                    "salaryPayablesForm",
                    "management/accountant/responses/responses.php", {
                        op: "get_salary_payables",
                        school,
                        salary_month: salaryMonth
                    },
                    false, true
                );
                const successCode = [200, 201, 204];
                if (successCode.includes(rsp.status)) {
                    const data = rsp.data;
                    table.clear();
                    let row = 0;
                    let payable;
                    for (let dt of data) {
                        if (dt.status == 1) { //part paid
                            payable = dt.salary - dt.paid;
                        } else {
                            if (dt.status == 0) {
                                payable = dt.salary;
                            }
                        }
                        table.row.add(['', `${dt.title}. ${formatName(dt.fname,dt.oname,dt.lname)}`, `${dt.receiver}`, `${formatMoney(payable)}`, `${dt.sch_abbr}`, `${dt.month}, ${dt.year}`, `${formatStatus(dt.status,row,dt.cancelled)}`, `${formatButton(dt.cancelled,row,dt.id)}`]);
                        row++;
                    }
                    table.draw();
                } else {
                    swalNotify(rsp.message, 'warning')
                }
                ld_stopLoading('salary_month');
                ld_stopLoading('school');
            }



        }



        function formatStatus(status, row, cancelled) {
            if (cancelled == 1) {
                return `<p class="text-secondary" row="status_${row}">Cancelled</p>`;
            } else {

                switch (status) {
                    case 0:
                        return `<p class="text-danger" row="status_${row}">Not Payed</p>`;
                    case 1:
                        return `<p class="message" row="status_${row}">Part Payed</p>`;
                    default:
                        return '';
                }
            }
        }
        async function changeDebtStatus(salId, operation, row) {
            ld_startLoading('btn_' + row, 'ld_loader_' + row);
            const rsp = await getPostPageWithUpload(
                "salaryPayablesForm",
                "management/accountant/responses/responses.php", {
                    op: "change_salary_debt_status",
                    sal_id: salId,
                    operation
                },
                false, true
            );
            const successCode = [200, 201, 204];
            if (successCode.includes(rsp.status)) {
                let salStatus;
                const data = rsp.data;
                if (operation == 'cancel') {
                    salStatus = 4;
                    cancelled = 1;
                } else {
                    salStatus = data.status;
                    cancelled = 0;
                }
                table
                    .cell({
                        row: row,
                        column: 6
                    })
                    .data(formatStatus(salStatus, row, cancelled))
                table
                    .cell({
                        row: row,
                        column: 7
                    })

                    .data(formatButton(cancelled, row, salId))
                    .draw();
            } else {
                swalNotify(rsp.message, 'warning')
            }
            ld_stopLoading('btn_' + row, 'ld_loader_' + row);
        }

        function formatButton(cancelled, row, salId) {
            if (cancelled == 1) {
                return `<button class="btn btn-sm btn-success" id="btn_${row}" onclick="changeDebtStatus('${salId}','restate',${row})">restate</button><span id="ld_loader_${row}"></span>`;
            } else {
                return `<button class="btn btn-sm btn-danger" id="btn_${row}" onclick="changeDebtStatus('${salId}','cancel',${row})">cancel</button><span id="ld_loader_${row}"></span>`;
            }
        }

        $(".js-example-basic-single").select2();
        getSalaryPayables()
    </script>