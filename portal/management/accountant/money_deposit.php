<?php
require_once './includes/accountant.inc.php';
$acct = new Account();
$accounts = $acct->getSchoolAccounts();

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Money Deposit</h4>
            <form class="forms-sample" id="depositForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="account">Account</label>
                    <select class="js-example-basic-single w-100 p-2" id="account" title="Account" name="account" required>
                        <option value="">:::Select Recipient Account:::</option>
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
                    <label for="depositor">Depositor Name</label>
                    <input type="text" class="form-control" id="depositor" title="Depositor" required name="depositor">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" title="Description" required name="description"></textarea>
                </div>

                <button type="button" class="btn btn-primary mr-2" onclick="deposit()" id="depositBtn">Deposit</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <input type="hidden" value="<?php echo $username ?>" name="username" id="username" />
            </form>
        </div>
    </div>
</div>
<script>
    async function deposit() {
        if (await swalConfirm('Confirm to proceed', 'question')) {
            if (validate('depositForm', {
                    validateOnSubmit: true
                })) {

                ld_startLoading("depositBtn");
                const rsp = await getPostPageWithUpload(
                    "depositForm",
                    "management/accountant/responses/responses.php", {
                        op: "deposit_money"
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
                emptyInputs(["amount", "description","depositor"]);
                resetInputStyling("depositForm", "inputsuccess", "inputfailure");
                ld_stopLoading("depositBtn");
            }
        }

    }
    validate('depositForm')
    $(".js-example-basic-single").select2();
</script>