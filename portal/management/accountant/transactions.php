<?php
require_once './includes/accountant.inc.php';
$categories = TransactionCategory::cases();
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="p-3 d-flex justify-content-end">
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </div>

            <h4 class="card-title text-primary">Transactions</h4>
            <form class="forms-sample" id="transactionsForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="from">From</label>
                    <input type="date" class="form-control" id="from" title="From" name="from" required>
                </div>
                <div class="form-group">
                    <label for="to">To</label>
                    <input type="date" class="form-control" id="to" title="To" name="to" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select class="js-example-basic-single w-100 p-2" id="category" title="Category" name="category" required>
                        <option value="0">ALL</option>
                        <?php
                        foreach ($categories as $cat) {
                        ?>
                            <option value="<?php echo $cat->value ?>"><?php echo $cat->getName() ?></option>
                        <?php
                        } ?>
                    </select>
                </div>
                <div class="p-3">
                    <button class="btn btn-primary w-100" onclick="search()" id="searchBtn">Search</button>
                    <span id="ld_loader"></span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover display" id="transactions">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Transaction ID</th>
                                <th>Payer</th>
                                <th>Receiver</th>
                                <th>Amount (&#8358;)</th>
                                <th>Balance (&#8358;)</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Created</th>
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
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>

        </div>

    </div>
    <script>
        var table = $("#transactions").DataTable(dataTableOptions);
        var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        async function search() {
            if (validate('transactionsForm', {
                    validateOnSubmit: true
                })) {
                if (_('from').value > _('to').value) {
                    swalNotify('From date should not be later than To date')
                    return;
                }
                ld_startLoading("searchBtn");
                const rsp = await getPostPageWithUpload(
                    "transactionsForm",
                    "management/accountant/responses/responses.php", {
                        op: "transactions",
                    },
                    false, true
                );
                const successCode = [200, 201, 204];
                if (successCode.includes(rsp.status)) {
                    const data = await rsp.data;
                    table.clear();
                    let row = 0;
                    let amount;
                    for (let dt of data) {
                        table.row.add(['', `${dt.trans_id}`, `${dt.payer}`, `${dt.receiver}`, `${formatMoney(dt.amount)}`, `${formatBalance(dt.school_balance,dt.trans_cat)}`, `${dt.trans_cat}`, `${dt.trans_type}`, `${formatDescription(dt.description)}`, `${formatDate(dt.created)}`, `${formatDownloadReceipt(dt.trans_id)}`]);
                        row++;
                    }
                    table.draw();
                } else {
                    swalNotify(rsp.message, 'warning')
                }
                ld_stopLoading("searchBtn");
            }
        }

        function formatBalance(balance, category) {
            if (equal('TRANSFER', category)) {
                return '';
            }
            return formatMoney(balance);
        }

        function formatDownloadReceipt(trans_id) {
            return `<span class = "message" onclick = "downloadReceipt('${trans_id}')"style = "cursor:pointer"> download receipt </span>`;
        }

        function formatDescription(description) {
            if (description == null) {
                return '';
            }
            return description;
        }

        function formatDate(date) {
            const dateObj = new Date(date)
            const year = dateObj.getFullYear();
            const month = months[dateObj.getMonth()];
            const day = dateObj.getDate();
            const hour = dateObj.getHours();
            const minute = dateObj.getMinutes();
            return day + ' ' + month.substr(0, 3) + ', ' + year + ' : ' + hour + ':' + minute;

        }

        let startedDownloadingReceipt = false;
        async function downloadReceipt(trans_id) {
            if (!startedDownloadingReceipt) {
                startedDownloadingReceipt = true;
                await window.location.assign('receipts.php?trans_id=' + trans_id + '&token=' + _('token').value);
                startedDownloadingReceipt = false;
            }
        }
        validate('transactionsForm');
        $(".js-example-basic-single").select2();
    </script>