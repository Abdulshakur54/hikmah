<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
$res_url = $url->to('students_result.php?session='.$currSession.'&term='.$currTerm.'&school='.$sch_abbr.'&token='.Token::generate(),0);
$profile_url = $url->to('profile.php',0);
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">My Students</h4>
            <?php
            $stds = $staff->getStudents($classId);
            if (!empty($stds)) {
                $numRows = count($stds);
                echo '<div class="message mb-2 text-right">' . $numRows . ' records found</div>';
                echo '<table class="table table-striped table-bordered nowrap responsive" id="studentsTable"><thead><th>S/N</th><th>Student ID</th><th>Fullname</th><th></th><th></th><th></th><th></th></thead><tbody>';
                foreach ($stds as $std) {
                    echo '<tr><td></td><td>' . Utility::escape($std->std_id) . '</td><td>' . Utility::formatName(Utility::escape($std->fname), Utility::escape($std->oname), Utility::escape($std->lname)) . '</td><td><a href="'.$profile_url.'?username='.urlencode($std->std_id).'">profile</a></td><td><a href="'.$res_url.'&student_ids='.urlencode(json_encode([$std->std_id])). '">result</a></td><td><a href="#" onclick="getPage(\'staff/student_psy.php?std_id='.$std->std_id. '\')">psychometry</a></td><td><a href="#" onclick="getPage(\'staff/comments.php?std_id=' . $std->std_id . '\')">commentary</a></td></tr>';
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
        $("#studentsTable").DataTable(dataTableOptions);
        if ($(".js-example-basic-single").length) {
            $(".js-example-basic-single").select2();
        }
    });
</script>