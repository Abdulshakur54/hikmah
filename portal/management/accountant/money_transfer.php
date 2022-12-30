<?php
require_once './includes/accountant.inc.php';
$acct = new Account();
$accounts = $acct->getSchoolAccounts();

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Money Transfer</h4>
            <form class="forms-sample" id="transferForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="source">Source</label>
                    <select class="js-example-basic-single w-100 p-2" id="source" title="Source" name="source" required onchange="selectSource()">
                        <option value="">:::Select Source Account:::</option>
                        <?php
                        foreach ($accounts as $ac) {
                        ?>
                            <option value="<?php echo $ac->account_name ?>"><?php echo $ac->account_name ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <span id="ld_loader_source"></span>
                </div>
                <div class="form-group">
                    <label for="destination">Destination</label>
                    <select class="js-example-basic-single w-100 p-2" id="destination" title="Destination" name="destination" required onchange="selectDestination()">
                        <option value="">:::Select Destination Account:::</option>
                        <?php
                        foreach ($accounts as $ac) {
                        ?>
                            <option value="<?php echo $ac->account_name ?>"><?php echo $ac->account_name ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <span id="ld_loader_destination"></span>
                </div>
                <div class="form-group">
                    <p><span class="font-weight-bold">Source Account Balance (&#8358;) :</span> <span id="srcAcct"></span></p>
                </div>
                <div class="form-group">
                    <p><span class="font-weight-bold">Destintation Account Balance (&#8358;) :</span> <span id="dtnAcct"></span></p>
                </div>
                <div class="form-group">
                    <label for="amount">Amount(&#8358;)</label>
                    <input type="text" class="form-control" id="amount" title="Amount" required pattern="^[0-9][0-9]*([.][0-9]+)?$" name="amount">
                </div>
                <button type="button" class="btn btn-primary mr-2" onclick="transfer()" id="transferBtn">Transfer</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script>
    async function selectSource() {
        const source = _('source').value;
        if (source.length > 0) {
            ld_startLoading('source', 'ld_loader_source');
            const rsp = await getPostPageWithUpload(
                "transferForm",
                "management/accountant/responses/responses.php", {
                    op: "get_school_account_balance",
                    account: source
                },
                false, true
            );
            const balance = rsp.data.balance;
            _('srcAcct').innerHTML = formatMoney(balance);
            ld_stopLoading('source', 'ld_loader_source');
        }
    }
    async function selectDestination() {
        const destination = _('destination').value;
        if (destination.length > 0) {
            ld_startLoading('source', 'ld_loader_destination');
            const rsp = await getPostPageWithUpload(
                "transferForm",
                "management/accountant/responses/responses.php", {
                    op: "get_school_account_balance",
                    account: destination
                },
                false, true
            );
            const balance = rsp.data.balance;
            _('dtnAcct').innerHTML = formatMoney(balance);
            ld_stopLoading('source', 'ld_loader_destination');
        }
    }
    async function transfer() {
        if (await swalConfirm('Confirm to proceed', 'question')) {
            if (validate('transferForm', {
                    validateOnSubmit: true
                })) {
                if (equal(_('source').value, _('destination').value)) {
                    swalNotify('Source Account should be different from Destination Account', 'warning');
                    return;
                }
                ld_startLoading("transferBtn");
                const rsp = await getPostPageWithUpload(
                    "transferForm",
                    "management/accountant/responses/responses.php", {
                        op: "transfer_money"
                    },
                    false, true
                );
                const successCode = [200, 201, 204];
                if (successCode.includes(rsp.status)) {
                    _('srcAcct').innerHTML = formatMoney(rsp.data.source);
                    _('dtnAcct').innerHTML = formatMoney(rsp.data.destination);
                    _('amount').value = '';
                    swalNotify(rsp.message, 'success')
                } else {
                    swalNotify(rsp.message, 'warning')
                }
                ld_stopLoading("transferBtn");
            }
        }

    }
    validate('transferForm')
            $(".js-example-basic-single").select2();
</script>