<?php
require_once './includes/accountant.inc.php';
$acct = new Account();
$accounts = $acct->getSchoolAccounts();

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Money Withdrawal</h4>
            <form class="forms-sample" id="withdrawForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="account">Account</label>
                    <select class="js-example-basic-single w-100 p-2" id="account" title="Account" name="account" required>
                        <option value="">:::Select Source Account:::</option>
                        <?php
                        foreach ($accounts as $ac) {
                        ?>
                            <option value="<?php echo $ac->account_name ?>"><?php echo $ac->account_name ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount(&#8358;)</label>
                    <input type="text" class="form-control" id="amount" title="Amount" required pattern="^[0-9][0-9]*([.][0-9]+)?$" name="amount">
                </div>
                <div class="form-group">
                    <label for="recipient">Recipient Name</label>
                    <input type="text" class="form-control" id="recipient" title="Recipient" required name="recipient">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" title="Description" required name="description"></textarea>
                </div>

                <button type="button" class="btn btn-primary mr-2" onclick="withdraw()" id="withdrawBtn">Withdraw</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <input type="hidden" value="<?php echo $username ?>" name="username" id="username" />
            </form>
        </div>
    </div>
</div>
<script>
    async function withdraw() {
        if (await swalConfirm('Confirm to proceed', 'question')) {
            if (validate('withdrawForm', {
                    validateOnSubmit: true
                })) {

                ld_startLoading("withdrawBtn");
                const rsp = await getPostPageWithUpload(
                    "withdrawForm",
                    "management/accountant/responses/responses.php", {
                        op: "withdraw_money"
                    },
                    false, true
                );
                const successCode = [200, 201, 204];
                if (successCode.includes(rsp.status)) {
                    _('amount').value = '';
                    swalNotify(rsp.message, 'success')
                } else {
                    swalNotify(rsp.message, 'warning')
                }
                ld_stopLoading("withdrawBtn");
                emptyInputs(["amount","description","recipient"]);
                resetInputStyling("depositForm", "inputsuccess", "inputfailure");
            }
        }

    }
    validate('withdrawForm')
    $(".js-example-basic-single").select2();
</script>