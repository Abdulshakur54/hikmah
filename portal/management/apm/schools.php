<?php
require_once './includes/apm.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Schools</h4>
            <div class="table-responsive">
                <table class="table table-hover display" id="schoolsTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>School</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($rank === 2) {
                            $schools = School::getConvectionalSchools(2);
                        } else {
                            if ($rank !== 4) {
                                exit();
                            }
                            $schools = School::getIslamiyahSchools(2);
                        }
                        if (!empty($schools)) {
                            foreach ($schools as $school) {
                                echo '<tr>
                            <td></td>
                            <td>' . $school . '</td>
                            <td><a href="#" onclick="getPage(\'management/apm/students.php?school=' . $school . '\')">students</a></td>
                            <td><a href="#" onclick="getPage(\'management/apm/time_table.php?school=' . $school . '\')">time table</a></td>
                            <td><a href="#" onclick="getPage(\'management/apm/results.php?school=' . $school . '\')">results</a></td>
                            <td><a href="#" onclick="getPage(\'management/apm/ses_results.php?school=' . $school . '\')">sessional results</a></td>
                            <td><a href="#" onclick="getPage(\'management/apm/performance_chart.php?school=' . $school . '\')">performance chart</a></td>
                            <td><a href="#" onclick="getPage(\'management/apm/performance_summary.php?school=' . $school . '\')">performance summary</a></td>
                             <td><a href="#" onclick="getPage(\'management/apm/send_message.php?school=' . $school . '\')">send messages</a></td>
                            <td><a href="#" onclick="getPage(\'management/apm/manage_permission.php?school=' . $school . '\')">permissions</a></td>
                            <td><a href="#" onclick="getPage(\'management/apm/schedules.php?school=' . $school . '\')">schedules and fees</a></td>
                         </tr>';
                            }
                        } else {
                            echo '<tr><td colspan ="5">No records found</td></tr>';
                        }

                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
    </div>
    <script>
        $(document).ready(function() {
            $("#schoolsTable").DataTable(dataTableOptions);
        });
    </script>