<?php
require_once './includes/director.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Manage Salary</h4>
            <div class="table-responsive">
                <table class="table table-hover display" id="manageSalaryTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Salary</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $account = new Account();
                        $accounts = $account->getSalariesDetails(1);
                        if (!empty($accounts)) {
                            foreach ($accounts as $val) {
                                if ($val->approved) {
                                    echo '<tr id="row' . $val->id . '">
                                <td></td>
                            <td>' . $val->receiver . '</td>
                            <td id="name' . $val->id . '">' . Utility::escape(Utility::formatName($val->fname, $val->oname, $val->lname)) .
                                        '</td>
                            <td><input type="text" class="form-control" id="salary' . $val->id . '" value="' . Utility::escape($val->salary) . '"/></td>
                            <td><button class="btn btn-primary btn-md p-2" onclick="updateSalary(\'' . $val->receiver . '\',' . $val->id . ')">update</button></td>
                            <td id="approval' . $val->id . '">approved</td>
                        </tr>';
                                } else {
                                    echo '
                        <tr id="row' . $val->id . '">
                        <td></td>
                            <td>' . $val->receiver . '</td>
                            <td id="name' . $val->id . '">' . Utility::escape(Utility::formatName($val->fname, $val->oname, $val->lname)) .
                                        '</td>
                            <td><input type="text"  class="form-control" id="salary' . $val->id . '" value="' . Utility::escape($val->salary) . '" /></td>
                            <td><button class="btn btn-primary btn-md p-2" onclick="updateSalary(\'' . $val->receiver . '\',' . $val->id . ')">update</button></td>
                            <td id="approval' . $val->id . '"><button class="btn btn-primary btn-md p-2" onclick="approveSalary(\'' . $val->receiver . '\',' . $val->id . ')">request approval</button></td>
                        </tr>';
                                }
                            }
                        } else {
                            echo '<tr colspan = "6"><td>No records found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
    </div>
    <script src="scripts/management/director/manage_salary.js"></script>