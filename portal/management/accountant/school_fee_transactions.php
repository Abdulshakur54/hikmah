<?php
require_once './includes/accountant.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            <form class="forms-sample" id="transactionsForm" onsubmit="return false" novalidate>
                <div class="p-3 d-flex justify-content-end">
                    <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                </div>
                <h4 class="card-title text-primary">School Fee Transactions</h4>
                <div class="form-group">
                    <label for="from">From</label>
                    <input type="date" class="form-control" id="from" title="From" name="from" required>
                </div>
                <div class="form-group">
                    <label for="to">To</label>
                    <input type="date" class="form-control" id="to" title="To" name="to" required>
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
                                <th>Type</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="p-3 d-flex justify-content-center">
                    <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                </div>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>

        </div>
    </div>

</div>
<script>
    var table = $("#transactions").DataTable(dataTableOptions);
    $(".js-example-basic-single").select2();
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
                    op: "school_fee_transactions",
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
                    table.row.add(['', `${dt.trans_id}`, `${dt.payer}`, `${dt.receiver}`, `${formatMoney(dt.amount)}`, `${formatMoney(dt.school_balance)}`, `${dt.trans_type}`, `${formatDate(dt.created)}`, `${formatDownloadReceipt(dt.trans_id)}`]);
                    row++;
                }
                table.draw();
            } else {
                swalNotify(rsp.message, 'warning')
            }
            ld_stopLoading("searchBtn");
        }
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

    function formatDownloadReceipt(trans_id) {
        return `<span class = "message" onclick = "downloadReceipt('${trans_id}')"style = "cursor:pointer"> download receipt </span>`;
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
</script>