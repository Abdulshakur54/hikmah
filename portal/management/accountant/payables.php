<?php
require_once './includes/accountant.inc.php';
$account = Utility::escape(Input::get('account'));
$accounts = Account::getAccounts();
$acct = new Account();
$payables = $acct->getAccountPayablesDetails($account);
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
        <form id="payablesForm" onsubmit="return false">
            <div class="card-body">
                <h4 class="card-title text-primary">Payables </h4>
                <div class="form-group">
                    <label for="account">Account</label>
                    <select class="js-example-basic-single w-100 p-2" id="account" title="account" name="account" required onchange="getAccountPayables()">
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
                    <table class="table table-hover display" id="payables">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>receiver ID</th>
                                <th>Payable (&#8358;)</th>
                                <th>Account</th>
                                <th>Payment Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($payables as $payable) {
                            ?>
                                <tr>
                                    <td></td>
                                    <td><?php echo $payable->title . '. ' . Utility::formatName($payable->fname, $payable->oname, $payable->lname) ?></td>
                                    <td><?php echo $payable->user_id ?></td>
                                    <td><?php echo number_format($payable->payable,2) ?></td>
                                    <td><?php echo $payable->account ?></td>
                                    <td><?php echo $payable->month . ', ' . $payable->year ?></td>
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
        var table = $("#payables").DataTable(dataTableOptions);

        async function getAccountPayables() {
            const account = _('account').value;
            if (account.length > 0) {
                ld_startLoading('account');
                const rsp = await getPostPageWithUpload(
                    "payablesForm",
                    "management/accountant/responses/responses.php", {
                        op: "get_account_payables",
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
                        table.row.add(['', `${dt.title}. ${formatName(dt.fname,dt.oname,dt.lname)}`, `${dt.user_id}`, `${formatMoney(dt.payable)}`, `${dt.account}`, `${dt.month}, ${dt.year}`]);
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