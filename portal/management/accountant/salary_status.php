<?php
require_once './includes/accountant.inc.php';
$payment_month = (int) Utility::escape(Input::get('payment_month'));
$acct = new Account();
$salary_month = $acct->getSalaryMonth($payment_month);
$staff_report = $acct->getMonthlySalaryDetails($payment_month, 'staff');
$management_report = $acct->getMonthlySalaryDetails($payment_month, 'management');
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="p-3 d-flex justify-content-end">
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </div>

            <h4 class="card-title text-primary">Salary Status (<?php echo $salary_month ?>)</h4>
            <div class="table-responsive">
                <table class="table table-hover display" id="salaryStatus">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Salary (&#8358;)</th>
                            <th>Received (&#8358;)</th>
                            <th>Owed (&#8358;)</th>
                            <th>Category</th>
                            <th>School</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($staff_report as $sr) {
                        ?>

                            <tr>
                                <td></td>
                                <td><?php echo $sr->title . '. ' . Utility::formatName($sr->fname, $sr->oname, $sr->lname) ?></td>
                                <td><?php echo number_format($sr->salary, 2) ?></td>
                                <td><?php echo number_format($sr->paid, 2) ?></td>
                                <td><?php echo number_format(round(((float)$sr->salary - (float)$sr->paid), 2), 2) ?></td>
                                <td>Staff</td>
                                <td><?php echo $sr->sch_abbr ?></td>
                            </tr>
                        <?php
                        }

                        foreach ($management_report as $mr) {
                        ?>

                            <tr>
                                <td></td>
                                <td><?php echo Utility::formatName($mr->fname, $mr->oname, $mr->lname) ?></td>
                                <td><?php echo number_format($mr->salary, 2) ?></td>
                                <td><?php echo number_format($mr->paid, 2) ?></td>
                                <td><?php echo number_format(round(((float)$mr->salary - (float)$mr->paid), 2), 2) ?></td>
                                <td>Management</td>
                                <td><?php echo $mr->sch_abbr ?></td>
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
        var table = $("#salaryStatus").DataTable(dataTableOptions);
    </script>