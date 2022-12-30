<?php
require_once './includes/director.inc.php';
$reqId = (int)Utility::escape(Input::get('reqId'));
$req = $db->get('request', 'requester_id,category,other', "id=$reqId");
$category = $req->category;
$requester_id = $req->requester_id;
$reqData = json_decode($req->other, true);
$payment_month = $reqData['payment_month'];
$salary_month_obj = $db->get('payment_months', 'month,year', "id=$payment_month");
$salary_month = $salary_month_obj->month . ', ' . $salary_month_obj->year;
$recipients_data = $reqData['recipients_data'];
$receivers = [];
foreach ($recipients_data as $rec) {
    $receivers[] = $rec[0]; //store all the recipients username;
}
$acct = new Account();
$receivers_data = $acct->getReceiversData($receivers, $reqData['recipients']);
?>
<style>
    .label {
        width: 150px;
        font-weight: bold;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card" id="pageContainer">
        <div class="card-body">
            <h4 class="card-title text-primary rounded">Salary Payment Invoice</h4>
            <div class="card border border-1 mb-3">
                <div class="card-header bg-white text-primary">Detail</div>
                <div class="card-body">
                    <p class="d-flex"><span class="label">Method: </span> <span><?php echo ucfirst($reqData['payment_type']) ?></span></p>
                    <p class="d-flex"><span class="label">Recipients: </span> <span><?php echo ucfirst($reqData['recipients']) ?></span></p>
                    <p class="d-flex"><span class="label">Month: </span> <span><?php echo $salary_month ?></span></p>
                    <p class="d-flex"><span class="label">Number of recipient: </span> <span><?php echo count($reqData['recipients_data']) ?></span></p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover display" id="tokensTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Amount (&#8358;)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 0;
                        foreach ($receivers_data as $rd) {
                        ?>
                            <tr>
                                <td></td>
                                <td><?php echo $rd->title . '. ' . Utility::formatName($rd->fname, $rd->oname, $rd->lname) ?></td>
                                <td>
                                    <?php echo number_format($recipients_data[$count][1],2);
                                    $count++; ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
        <input type="hidden" value="<?php echo $reqId ?>" name="req_id" id="reqId" />
        <input type="hidden" value="<?php echo $category ?>" name="category" id="category" />
        <input type="hidden" value="<?php echo $requester_id ?>" name="requester_id" id="requester_id" />
        <input type="hidden" value="<?php echo Utility::escape(Session::getAltLastPage()) ?>" name="lastpage" id="lastpage" />
        <div class="mb-3 d-flex justify-content-center gx-1">
            <button type="button" class="btn btn-primary" onclick="salaryPaymentDecision(true)" id="approveBtn">Approve</button><span id="ld_loader"></span>
            <span id="ld_loader"></span>
            <button type="button" class="btn btn-info" onclick="salaryPaymentDecision(false)" id="declineBtn">Decline</button>
        </div>
    </div>
</div>

<script>
    var table = $("#tokensTable").DataTable(dataTableOptions);
    async function salaryPaymentDecision(approve) {
        const confirmText = approve ? 'approve' : 'decline';
        if (await swalConfirm(`Proceed to <span style="font-weight: bold">${confirmText}</span> payment of salaries`)) {
            const reqId = _('reqId').value;
            const decision = approve ? 'true' : 'false';
            const token = _('token').value;
            const category = _('category').value;
            const requesterId = _('requester_id').value;
            ld_startLoading(`${confirmText}Btn`);
            ajaxRequest('management/director/responses/requests.rsp.php', salaryPaymentDecisionRsp, 'token=' + token + '&id=' + reqId + '&confirm=' + decision + '&requester_id=' + requesterId + '&category=' + category);
        }

        function salaryPaymentDecisionRsp() {
            const rsp = JSON.parse(xmlhttp.responseText);
            _("token").value = rsp.token;
            const validStatus = [200, 201, 204];
            const lastpage = _('lastpage').value;
            const content = `<div class="text-center py-5">
              <h4 class="text-success">Operation was successful</h4>
            <button class="btn btn-secondary" onclick="getPage('${lastpage}')">Return</button>
            </div>`;
            const pageContainer = _('pageContainer');
            ld_stopLoading(`${confirmText}Btn`);
            if (validStatus.includes(rsp.status)) {
                swalNotify(rsp.message, 'success');

            } else {
                swalNotify(rsp.message, 'error');
            }
            pageContainer.innerHTML = content;
        }
    }
</script>