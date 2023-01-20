<?php

require_once './includes/accountant.inc.php';
$school = Utility::escape(Input::get('school'));
$school = (!empty($school)) ? $school : 'ALL';
$session = Utility::escape(Input::get('session'));
$session = (!empty($session)) ? $session : 'ALL';
$term = Utility::escape(Input::get('term'));
$term = (!empty($term)) ? $term : 'ALL';
$schools = School::getSchools(2);
function selectedSession($ses)
{
    global $session;
    if ($session == $ses) {
        return 'selected';
    }
}
function selectedSchool($sch)
{
    global $school;
    if ($school == $sch) {
        return 'selected';
    }
}
function selectedTerm($tm)
{
    global $term;
    if ($term == $tm) {
        return 'selected';
    }
}
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">School Fees Receivables</h4>
            <form class="forms-sample" id="schoolFeeReceivablesForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="school">School</label>
                    <select class="js-example-basic-single w-100 p-2" id="school" title="School" name="school" required onchange="proceed()">
                        <option value="ALL">ALL</option>
                        <?php
                        foreach ($schools as $sch) {
                        ?>
                            <option value="<?php echo $sch ?>" <?php echo selectedSchool($sch) ?>><?php echo $sch ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="session">Session</label>
                    <select class="js-example-basic-single w-100 p-2" id="session" title="Session" name="session" required onchange="proceed()">
                        <option value="ALL">ALL</option>
                        <?php
                        $sess = Ses::get();
                        foreach ($sess as $ses) {
                        ?>
                            <option value="<?php echo $ses->session ?>" <?php echo selectedSession($ses->session) ?>><?php echo $ses->session ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <span id="ld_loader"></span>
                <div class="form-group">
                    <label for="term">Term</label>
                    <select class="js-example-basic-single w-100 p-2" id="term" title="Term" name="term" required onchange="proceed()">
                        <option value="ALL">ALL</option>
                        <option value="ft" <?php echo selectedTerm('ft') ?>>FT</option>
                        <option value="st" <?php echo selectedTerm('st') ?>>ST</option>
                        <option value="tt" <?php echo selectedTerm('tt') ?>>TT</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover display" id="schoolFeeReceivables">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>ID</th>
                                <th>Receivable (&#8358;)</th>
                                <th>School</th>
                                <th>Session</th>
                                <th>Term</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script>
    validate('schoolFeeReceivablesForm')
    $(".js-example-basic-single").select2();
    var table = $("#schoolFeeReceivables").DataTable(dataTableOptions);
    proceed();
    async function proceed() {
        if (validate('schoolFeeReceivablesForm', {
                validateOnSubmit: true
            })) {
            const token = _('token').value;
            const term = _('term').value;
            const session = _('session').value;
            const school = _('school').value;
            ld_startLoading('school')
            ld_startLoading('session')
            ld_startLoading('term');
            const rsp = await getPostPageWithUpload(
                "schoolFeeReceivablesForm",
                "management/accountant/responses/responses.php", {
                    op: "get_school_fee_receivables",
                    school,
                    term,
                    session
                },
                false, true
            );
            const successCode = [200, 201, 204];
            if (successCode.includes(rsp.status)) {
                const data = rsp.data;
                table.clear();
                let row = 0;
                let receivable;
                for (let dt of data) {
                    if (dt.status == 1) { //part paid
                        receivable = dt.amount - dt.paid;
                    } else {
                        if (dt.status == 0) {
                            receivable = dt.amount;
                        }
                    }
                    table.row.add(['', `${formatName(dt.fname,dt.oname,dt.lname)}`, `${dt.std_id}`, `${formatMoney(receivable)}`, `${dt.sch_abbr}`, `${dt.session}`, `${dt.term}`, `${formatStatus(dt.status,row,dt.cancelled)}`, `${formatButton(dt.cancelled,row,dt.id)}`]);
                    row++;
                }
                table.draw();
            } else {
                swalNotify(rsp.message, 'warning')
            }
            ld_stopLoading('school')
            ld_stopLoading('session')
            ld_stopLoading('term');

        } else {
            swalNotify('All fields are required', 'warning')
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
    async function changeDebtStatus(feeId, operation, row) {
        ld_startLoading('btn_' + row, 'ld_loader_' + row);
        const rsp = await getPostPageWithUpload(
            "schoolFeeReceivablesForm",
            "management/accountant/responses/responses.php", {
                op: "change_school_fee_debt_status",
                fee_id: feeId,
                operation
            },
            false, true
        );
        const successCode = [200, 201, 204];
        if (successCode.includes(rsp.status)) {
            let feeStatus;
            const data = rsp.data;
            if (operation == 'cancel') {
                feeStatus = 4;
                cancelled = 1;
            } else {
                feeStatus = data.status;
                cancelled = 0;
            }
            table
                .cell({
                    row: row,
                    column: 7
                })
                .data(formatStatus(feeStatus, row, cancelled))
            table
                .cell({
                    row: row,
                    column: 8
                })

                .data(formatButton(cancelled, row, feeId))
                .draw();
        } else {
            swalNotify(rsp.message, 'warning')
        }
        ld_stopLoading('btn_' + row, 'ld_loader_' + row);
    }

    function formatButton(cancelled, row, feeId) {
        if (cancelled == 1) {
            return `<button class="btn btn-sm btn-success" id="btn_${row}" onclick="changeDebtStatus('${feeId}','restate',${row})">restate</button><span id="ld_loader_${row}"></span>`;
        } else {
            return `<button class="btn btn-sm btn-danger" id="btn_${row}" onclick="changeDebtStatus('${feeId}','cancel',${row})">cancel</button><span id="ld_loader_${row}"></span>`;
        }
    }
</script>