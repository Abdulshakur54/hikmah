<?php
require_once './includes/accountant.inc.php';
$acct = new Account();
$payment_months = $acct->getSalaryMonths();
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2" onclick="getPage('management/accountant/initiate_payment.php')">Initiate Salary Month</button></div>
            <h4 class="card-title text-primary">Salary Months</h4>
            <div class="table-responsive">
                <table class="table table-hover display" id="paymentMonth">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Months</th>
                            <th>Pay staff salaries</th>
                            <th>pay management salaries</th>
                            <th>salary status</th>
                            <th>transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($payment_months as $pm) {
                        ?>

                            <tr>
                                <td></td>
                                <td><?php echo $pm->month . ', ' . $pm->year ?></td>
                                <td><a href="#" onclick="getPage('management/accountant/pay_salaries.php?recipients=staff')">pay staff salaries</a></td>
                                <td><a href="#" onclick="getPage('management/accountant/pay_salaries.php?recipients=management')">pay management salaries</a></td>
                                <td><a href="#" onclick="getPage('management/accountant/salary_status.php?payment_month=<?php echo $pm->id ?>')">salaries status</a></td>
                                <td><a href="#" onclick="getPage('management/accountant/salary_transactions.php?payment_month=<?php echo $pm->id ?>')">transactions</a></td>
                            </tr>
                        <?php
                        }

                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
    </div>
    <script>
        var table = $("#paymentMonth").DataTable(dataTableOptions);
    </script>