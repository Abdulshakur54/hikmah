<?php
require_once './includes/accountant.inc.php';
$payment_month = (int) Utility::escape(Input::get('payment_month'));
$acct = new Account();
$salary_month = $acct->getSalaryMonth($payment_month);
$staff_report = $acct->getMonthlySalaryTransactionDetails($payment_month, 'staff');
$management_report = $acct->getMonthlySalaryTransactionDetails($payment_month, 'management');
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="p-3 d-flex justify-content-end">
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </div>

            <h4 class="card-title text-primary">Salary Transactions (<?php echo $salary_month ?>)</h4>
            <div class="table-responsive">
                <table class="table table-hover display" id="monthlyTransaction">
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($staff_report as $tr) {
                        ?>

                            <tr>
                                <td></td>
                                <td><?php echo $tr->trans_id ?></td>
                                <td><?php echo $tr->payer ?></td>
                                <td><?php echo $tr->title . '. ' . Utility::formatName($tr->fname, $tr->oname, $tr->lname) ?></td>
                                <td><?php echo number_format($tr->amount, 2) ?></td>
                                <td><?php echo number_format($tr->school_balance, 2) ?></td>
                                <td><?php echo ($tr->type == 1) ? "online" : "manual" ?></td>
                                <td><?php echo Utility::formatFullDate($tr->created) ?></td>

                            </tr>
                        <?php
                        }

                        foreach ($management_report as $tr) {
                        ?>

                            <tr>
                                <td></td>
                                <td><?php echo $tr->trans_id ?></td>
                                <td><?php echo $tr->payer ?></td>
                                <td><?php echo Utility::formatName($tr->fname, $tr->oname, $tr->lname) ?></td>
                                <td><?php echo number_format($tr->amount, 2) ?></td>
                                <td><?php echo number_format($tr->school_balance, 2) ?></td>
                                <td><?php echo ($tr->type == 1) ? "online" : "manual" ?></td>
                                <td><?php echo date('jS M, Y g:i A', strtotime($tr->created)) ?></td>

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
    </div>
    <script>
        var table = $("#monthlyTransaction").DataTable(dataTableOptions);
    </script>