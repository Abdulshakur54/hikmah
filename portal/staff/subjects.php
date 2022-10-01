<?php
require_once './includes/staff.inc.php';
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">My Subjects</h4>
            <?php
            $subjects = $staff->getSubjectsWithIds($username);
            if (!empty($subjects)) {
                $numRows = count($subjects);
                echo '<div class="message mb-2 text-right">' . $numRows . ' records found</div>';
                echo '<table class="table table-striped table-bordered nowrap responsive" id="subjectsTable"><thead><th>S/N</th><th>Subject</th><th>Class</th><th></th><th></th><th></th></thead><tbody>';
                foreach ($subjects as $sub) {
                    echo '<tr><td></td><td>' . $sub->subject . '</td><td>' . School::getLevName($sch_abbr, $sub->level) . ' '.strtoupper($sub->class). '</td><td><a href="#" onclick="getPage(\'staff/set_exam.php?subid=' . $sub->id . '\')" />set e-exam</a></td><td><a href="#" onclick="getPage(\'staff/scores.php?subid=' . $sub->id . '\')" />scores</a></td><td><a href="#" onclick="getPage(\'staff/scheme_of_work.php?subid=' . $sub->id . '\')" />scheme of work</a></td></tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<div class="message">No record found</div>';
            }
            ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#subjectsTable").DataTable(dataTableOptions);
        if ($(".js-example-basic-single").length) {
            $(".js-example-basic-single").select2();
        }
    });
</script>