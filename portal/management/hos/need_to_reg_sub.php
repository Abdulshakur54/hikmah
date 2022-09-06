<?php
    require_once './includes/hos.inc.php';

    ?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Students that have not completed subject registration</h4>
            <?php
            $stds = $hos->getStdsNotCompSubReg($sch_abbr);
            if (!empty($stds)) {
                $numRows = count($stds);
                echo '<div class="message  mb-2 text-right">' . $numRows . ' records found</div>';
                echo '<table class="table table-striped table-bordered nowrap responsive" id="studentsTable"><thead><th>S/N</th><th>Student ID</th><th>Fullname</th><td>Class</td></thead><tbody>';
                foreach ($stds as $std) {
                    echo '<tr><td></td><td>' . Utility::escape($std->std_id) . '</td><td>' . Utility::formatName(Utility::escape($std->fname), Utility::escape($std->oname), Utility::escape($std->lname)) . '</td><td>' . School::getLevelName($sch_abbr, (int)$std->level) . ' ' . Utility::escape($std->class) . '</td></tr>';
                }
                echo '</tbody></table>';
                echo '<div class="text-center p-3"><button onclick="notify()" class="btn btn-md btn-primary">Notify Students</button></div>';
            } else {
                echo '<div class="message">No record found</div>';
            }
            ?>
        </div>
    </div>
</div>
<script>
    function notify() {
        swalNotify('Students have been persistently notified', 'info');
    }

    $(document).ready(function() {
        $("#studentsTable").DataTable(dataTableOptions);
        if ($(".js-example-basic-single").length) {
            $(".js-example-basic-single").select2();
        }
    });
</script>