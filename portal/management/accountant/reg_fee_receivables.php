<?php

require_once './includes/accountant.inc.php';
$school = Utility::escape(Input::get('school'));
$school = (!empty($school)) ? $school : '';
$session = Utility::escape(Input::get('session'));
$session = (!empty($session)) ? $session : '';
$level = Utility::escape(Input::get('level'));
$level = (!empty($level)) ? $level : '';
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
function selectedLevel($lv)
{
    global $level;
    if ($level == $lv) {
        return 'selected';
    }
}
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Registration Fees Receivables</h4>
            <form class="forms-sample" id="regFeeReceivablesForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="school">School</label>
                    <select class="js-example-basic-single w-100 p-2" id="school" title="School" name="school" required onchange="schoolChanged()">
                        <option value="">:::Select School:::</option>
                        <?php
                        foreach ($schools as $sch) {
                        ?>
                            <option value="<?php echo $sch ?>" <?php echo selectedSchool($sch) ?>><?php echo $sch ?></option>
                        <?php
                        }
                        ?>
                        <option value="ALL">ALL</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="session">Session</label>
                    <select class="js-example-basic-single w-100 p-2" id="session" title="Session" name="session" required onchange="proceed()">
                        <option value="">:::Select Session:::</option>
                        <?php
                        $sess = Ses::get();
                        foreach ($sess as $ses) {
                        ?>
                            <option value="<?php echo $ses->session ?>" <?php echo selectedSession($ses->session) ?>><?php echo $ses->session ?></option>
                        <?php
                        }
                        ?>
                        <option value="ALL">ALL</option>
                    </select>
                </div>
                <span id="ld_loader"></span>
                <div class="form-group">
                    <label for="level">Level</label>
                    <select class="js-example-basic-single w-100 p-2" id="level" title="Level" name="level" required onchange="proceed()">
                        <option value="">:::Select Level:::</option>
                        <?php
                        if ($school != 'ALL') {
                            $levels = School::getLevels($school);
                            foreach ($levels as $levName => $lev) {
                        ?>
                                <option value="<?php echo $lev ?>" <?php echo selectedLevel($lev) ?>><?php echo $levName ?></option>
                        <?php
                            }
                        }
                        ?>
                        <option value="ALL">ALL</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover display" id="regFeeReceivables">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>ID</th>
                                <th>Receivable (&#8358;)</th>
                                <th>School</th>
                                <th>Session</th>
                                <th>Level</th>
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
    validate('regFeeReceivablesForm')
    $(".js-example-basic-single").select2();
    var table = $("#regFeeReceivables").DataTable(dataTableOptions);
    let session
    async function schoolChanged() {
        const school = _('school').value;
        if (school.length > 0) {
            ld_startLoading('school')
            const rsp = await getPostPageWithUpload(
                "regFeeReceivablesForm",
                "management/accountant/responses/responses.php", {
                    op: "get_session_and_levels",
                    school
                },
                false, true
            );
            const successCode = [200, 201, 204];
            if (successCode.includes(rsp.status)) {
                const data = rsp.data;
                const sessions = data.sessions;
                const levels = data.levels;
                let levelsString = `<option value="" selected>:::Select Level:::</option><option value="ALL">ALL</option>`;
                let sesString = `<option value="" selected>:::Select Session:::</option><option value="ALL">ALL</option>`;
                for (let lev in levels) {
                    levelsString += `<option value="${levels[lev]}">${lev}</option>`;
                }
                for (let ses of sessions) {
                    sesString += `<option value="${ses.session}">${ses.session}</option>`;
                }
                _('level').innerHTML = levelsString
                _('session').innerHTML = sesString
            }

            ld_stopLoading('school')
        }
    }
    async function proceed() {
        if (validate('regFeeReceivablesForm', {
                validateOnSubmit: true
            })) {
            const token = _('token').value;
            const level = _('level').value;
            const session = _('session').value;
            const school = _('school').value;
            ld_startLoading('school')
            ld_startLoading('session')
            ld_startLoading('level');
            const rsp = await getPostPageWithUpload(
                "regFeeReceivablesForm",
                "management/accountant/responses/responses.php", {
                    op: "get_reg_fee_receivables",
                    school,
                    level,
                    session
                },
                false, true
            );
            const successCode = [200, 201, 204];
            if (successCode.includes(rsp.status)) {
                const data = rsp.data;
                table.clear();
                let row = 0;
                for (let dt of data) {
                    table.row.add(['', `${formatName(dt.fname,dt.oname,dt.lname)}`, `${dt.std_id}`, `${formatMoney(dt.reg_fee)}`, `${dt.sch_abbr}`, `${dt.session}`, `${dt.name}`, `${formatStatus(dt.status,row,dt.cancelled)}`, `${formatButton(dt.cancelled,row,dt.id)}`]);
                    row++;
                }
                table.draw();
            } else {
                swalNotify(rsp.message, 'warning')
            }
            ld_stopLoading('school')
            ld_stopLoading('session')
            ld_stopLoading('level');

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
            "regFeeReceivablesForm",
            "management/accountant/responses/responses.php", {
                op: "change_reg_fee_debt_status",
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