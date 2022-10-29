<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';

if (Input::submitted('get') && !empty(Input::get('term'))) {
    $term = Utility::escape(Input::get('term'));
} else {
    $term = '';
}

$qry_count = 0;
function get_formatted_grade_summary(string $stdId): string
{
    global $currSession, $term, $qry_count;
    if ($term === 'ses') {
        $ses_scores = ($qry_count > 0) ? Result::get_student_ses_totals($currSession, $stdId, true) : Result::get_student_ses_totals($currSession, $stdId);
        $scores = [];
        foreach ($ses_scores as $scr) {
            if (is_null($scr->ft_tot) && is_null($scr->st_tot) && is_null($scr->tt_tot)) {
                $scores[] = null;
            } else {
                $scores[] = $scr->ft_tot + $scr->st_tot + $scr->tt_tot;
            }
        }
    } else {
        $scores = ($qry_count > 0) ?  Result::get_student_term_totals($currSession, $stdId, $term, true) : Result::get_student_term_totals($currSession, $stdId, $term);
        $scores = Utility::convertToArray($scores, $term . '_tot');
    }
    $qry_count++;
    $grades = Result::get_grades_summary($scores);
    return Result::format_grade_summary($grades, true);
}

?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary "><?php echo School::getLevelName($sch_abbr, $level) . ' ' . $class . ' ' . Utility::formatTerm($term) . ' Performance Summary' ?></h4>
            <?php
            if (!empty($term)) {
            ?><div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered nowrap" style="width:100%" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Summary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $students = $staff->getStudents($classId);
                                foreach ($students as $std) {
                                    echo '
                                     <tr">
                                        <td></td>
                                        <td>' . strtoupper($std->std_id) . '</td>
                                        <td>' . Utility::formatName($std->fname, $std->oname, $std->lname, false) . '</td>
                                        <td>' . get_formatted_grade_summary($std->std_id) . '</td>
                                    </tr>
                                ';
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-2">
                        <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="p-5 text-center d-flex justify-content-center g-1 flex-wrap">
                    <button class="btn btn-primary" onclick="viewPerformance('ft')" id="ft_performance_btn">First Term</button>
                    <button class="btn btn-primary" onclick="viewPerformance('st')" id="st_performance_btn">Second Term</button>
                    <button class="btn btn-primary" onclick="viewPerformance('tt')" id="tt_performance_btn">Third Term</button>

                    <button class="btn btn-primary" onclick="viewPerformance('ses')" id="ses_performance_btn">Sessional</button>
                </div>

            <?php
            }

            ?>

        </div>
    </div>
</div>
</div> <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
<script>
    function viewPerformance(term) {
        getPage('staff/performance_summary.php?term=' + term +
            '&token=' +
            _('token').value);
    }
    $(document).ready(function() {
        $("#studentsTable").DataTable(dataTableOptions);
        $(".js-example-basic-single").select2();
    });
</script>