<?php
require_once './includes/accountant.inc.php';
$account = Utility::escape(Input::get('account'));
$accounts = School::getSchools(2);
$acct = new Account();
$reg_fees = $acct->getAccountRegFeeReceivablesDetails($account);
function selectedAccount($acct)
{
    global $account;
    if ($account == $acct) {
        return 'selected';
    }
}


?>

<div class="grid-margin stretch-card">
    <div class="card">
        <form id="receivablesForm" onsubmit="return false">
            <div class="card-body">
                <h4 class="card-title text-primary">Registration Fee Receivables </h4>
                <div class="form-group">
                    <label for="account">Account</label>
                    <select class="js-example-basic-single w-100 p-2" id="account" title="account" name="account" required onchange="getRegFeeReceivables()">
                        <option value="ALL">ALL</option>
                        <?php
                        foreach ($accounts as $a) {
                        ?>
                            <option value="<?php echo $a ?>" <?php echo selectedAccount($a) ?>><?php echo $a ?></option>
                        <?php
                        } ?>
                    </select>
                </div>
                <span id="ld_loader"></span>
                <div class="table-responsive">
                    <table class="table table-hover display" id="regFee">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>Registration Fee (&#8358;)</th>
                                <th>Account</th>
                                <th>Session</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($reg_fees as $reg_fee) {
                            ?>
                                <tr>
                                    <td></td>
                                    <td><?php echo Utility::formatName($reg_fee->fname, $reg_fee->oname, $reg_fee->lname) ?></td>
                                    <td><?php echo $reg_fee->std_id ?></td>
                                    <td><?php echo number_format($reg_fee->reg_fee,2) ?></td>
                                    <td><?php echo $reg_fee->sch_abbr ?></td>
                                    <td><?php echo $reg_fee->session ?></td>                              
                                </tr>
                            <?php
                            }
                            ?>

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
        var table = $("#regFee").DataTable(dataTableOptions);

        async function getRegFeeReceivables() {
            const account = _('account').value;
            if (account.length > 0) {
                ld_startLoading('account');
                const rsp = await getPostPageWithUpload(
                    "receivablesForm",
                    "management/accountant/responses/responses.php", {
                        op: "get_reg_fees_receivable_balance",
                        account,
                    },
                    false, true
                );
                const successCode = [200, 201, 204];
                if (successCode.includes(rsp.status)) {
                    const data = rsp.data;
                    table.clear();
                    let row = 0;
                    for (let dt of data) {
                        table.row.add(['', `${formatName(dt.fname,dt.oname,dt.lname)}`, `${dt.std_id}`, `${formatMoney(dt.reg_fee)}`, `${dt.sch_abbr}`, `${dt.session}`]);
                        row++;
                    }
                    table.draw();
                } else {
                    swalNotify(rsp.message, 'warning')
                }
                ld_stopLoading('account');
            }


        }
        $(".js-example-basic-single").select2();
    </script>