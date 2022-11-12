<?php
require_once './includes/director.inc.php';
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $schools = School::getSchools(2);
                        if (!empty($schools)) {
                            foreach ($schools as $school) {
                                echo '<tr>
                            <td></td>
                            <td>' . $school . '</td>
                            <td><a href="#" onclick="getPage(\'management/director/results.php?school=' . $school . '\')">results</a></td>
                            <td><a href="#" onclick="getPage(\'management/director/ses_results.php?school=' . $school . '\')">sessional results</a></td>
                            <td><a href="#" onclick="getPage(\'management/director/performance_chart.php?school=' . $school . '\')">performance chart</a></td>
                            <td><a href="#" onclick="getPage(\'management/director/performance_summary.php?school=' . $school . '\')">performance summary</a></td>
                         </tr>';
                            }
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