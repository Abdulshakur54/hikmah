<?php
require_once './includes/accountant.inc.php';
$account = new Account();
$account_det = $account->getAllAccountsStatus();
$total_payables = $total_school_fees = $total_reg_fees = $total_balance = 0;
foreach ($account_det as $det) {
    $total_balance += $det['balance'];
    $total_payables += $det['payables'];
    $total_school_fees += $det['school_fees'];
    $total_reg_fees += $det['reg_fees'];
}
?>
<style>
    .c-border {
        border: 1px solid #248AFD;
    }

    .c2-border {
        border-right: 0;
    }

    @media screen and (max-width: 765px) {

        .c2-border {
            border-bottom: 0;
            border-right: 1px solid #248AFD;
        }
    }
</style>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="p-3 d-flex justify-content-end">
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </div>

            <h4 class="card-title text-primary">Account Balance</h4>
            <form class="forms-sample" id="payRegFeeForm" onsubmit="return false" novalidate>
                <div class="form-group my-5 d-flex flex-column g-1 px-1">
                    <div class="row">
                        <div class="font-weight-bold col-md-6 text-center text-md-left text-info p-1 c-border c2-border p-2">Total Payables</div>
                        <div class="col-md-6 label-value p-2 c-border"><span class="font-weight-bold">(&#8358;)<?php echo $total_payables ?></span> <i class="mdi mdi-eye text-info px-2" style="cursor: pointer;" onclick="getPage('management/accountant/payables.php?account=ALL')"></i> </div>
                    </div>
                    <div class="row">
                        <div class="font-weight-bold col-md-6 text-center text-md-left text-info p-1 c-border c2-border p-2">Total School Fees</div>
                        <div class="col-md-6 label-value p-2 c-border"><span class="font-weight-bold">(&#8358;)<?php echo $total_school_fees ?></span> <i class="mdi mdi-eye text-info px-2" style="cursor: pointer;" onclick="getPage('management/accountant/school_fees_receivable_balance.php?account=ALL')"></i> </div>
                    </div>
                    <div class="row">
                        <div class="font-weight-bold col-md-6 text-center text-md-left text-info p-1 c-border c2-border p-2">Total Registration Fees</div>
                        <div class="col-md-6 label-value p-2 c-border"><span class="font-weight-bold">(&#8358;)<?php echo $total_reg_fees ?></span><i class="mdi mdi-eye text-info px-2" style="cursor: pointer;" onclick="getPage('management/accountant/reg_fees_receivable_balance.php?account=ALL')"></i></div>
                    </div>
                    <div class="row">
                        <div class="font-weight-bold col-md-6 text-center text-md-left text-info p-1 c-border c2-border p-2">Total Balance</div>
                        <div class="col-md-6 label-value p-2 c-border"><span class="font-weight-bold">(&#8358;)<?php echo $total_balance ?></span></div>
                    </div>

                </div>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <div class="table-responsive">
                    <table class="table table-hover display" id="monthlyTransaction">
                        <thead>
                            <tr>
                                <th rowspan="2">S/N</th>
                                <th rowspan="2">Account Name</th>
                                <th rowspan="2">Balance</th>
                                <th rowspan="2">Payables (&#8358;)</th>
                                <th class="text-center" colspan="2">Receivables (&#8358;)</th>
                            </tr>
                            <tr>
                                <th>School Fees</th>
                                <th>Registration Fees</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($account_det as $name => $det) {
                            ?>

                                <tr>
                                    <td></td>
                                    <td><?php echo $name ?></td>
                                    <td><?php echo number_format($det['balance'], 2) ?></td>
                                    <td><?php echo number_format($det['payables'], 2) ?> <i class="mdi mdi-eye text-info px-1" style="cursor: pointer;" onclick="getPage('management/accountant/payables.php?account=<?php echo $name ?>')"></i></td>
                                    <td><?php echo number_format($det['school_fees'], 2) ?> <i class="mdi mdi-eye text-info px-1" style="cursor: pointer;" onclick="getPage('management/accountant/school_fees_receivable_balance.php?account=<?php echo $name ?>')"></i></td>
                                    <td><?php echo number_format($det['reg_fees'], 2) ?> <i class="mdi mdi-eye text-info px-1" style="cursor: pointer;" onclick="getPage('management/accountant/reg_fees_receivable_balance.php?account=<?php echo $name ?>')"></i></td>
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
            </form>

        </div>

    </div>
    <script>
        var table = $("#monthlyTransaction").DataTable(dataTableOptions);
    </script>