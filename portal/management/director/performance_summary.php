<?php
require_once './includes/director.inc.php';

$selected_level =  null;

if (!Input::submitted('get')) {
    Redirect::to(404);
}


function selectedClass($classObj)
{
    global $classId, $selected_level, $class_name;
    if ($classObj->id == $classId) {
        $selected_level  = $classObj->level;
        $class_name = $classObj->class;
        return 'selected';
    }
}
$classes = School::get_classes($sch_abbr);
$classId = Input::get('class_id');
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
<style>
    .pie-chart {
        width: 70%;
        height: auto;
        object-fit: cover;
        min-width: 280px;
    }
</style>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-center g-2">
                <div class="form-group">
                    <select class="js-example-basic-single w-100 p-2" id="class" name="class" onchange="changeClass()">
                        <option value="">:::Select Class:::</option>
                        <?php
                        foreach ($classes as $class) {
                        ?>
                            <option value="<?php echo $class->id ?>" <?php echo selectedClass($class) ?>><?php echo School::getLevelName($sch_abbr, $class->level) . ' ' . $class->class ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <button class="btn btn-primary btn-md" onclick="submitClass()" id="resultBtn">Browse</button><span id="ld_loader"></span>
                </div>
            </div>

            <?php
            if (!empty($term)) {
            ?>

                <h4 class="card-title text-primary "><?php echo School::getLevelName($sch_abbr, $selected_level) . ' ' . $class_name . ' ' . Utility::formatTerm($term) . ' Performance Summary' ?></h4>
                <div>
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
                                $students = Student::getStudents($classId);
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
                </div>
            <?php
            } else {
            ?>
                <div class="p-5 text-center d-flex justify-content-center g-1 flex-wrap">
                    <?php
                    if (!empty($classId)) {
                    ?>
                        <button class="btn btn-primary" onclick="viewPerformance('ft')" id="ft_performance_btn">First Term</button>
                        <button class="btn btn-primary" onclick="viewPerformance('st')" id="st_performance_btn">Second Term</button>
                        <button class="btn btn-primary" onclick="viewPerformance('tt')" id="tt_performance_btn">Third Term</button>

                        <button class="btn btn-primary" onclick="viewPerformance('ses')" id="ses_performance_btn">Sessional</button>

                    <?php
                    } else {
                        echo '<div class="message text-center">Select a class first</div>';
                    }
                    ?>

                </div>
            <?php
            }

            ?>
            <div class="text-center mt-3">
                <button type="button" class="btn btn-light" onclick="getPage('management/director/schools.php')" id="returnBtn">Return</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo $term ?>" name="term" id="term" />
<input type="hidden" value="<?php echo $sch_abbr ?>" name="school" id="school" />
<input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
<script>
    function submitClass() {
        let cls = _('class').value;
        let school = _('school').value
        getPage('management/director/performance_summary.php?class_id=' + cls + '&school=' + school + '&token=' +
            _('token').value);
    }

    function changeClass() {
        let cls = _('class').value;
        let term = _('term').value;
        let school = _('school').value
        if (term.length > 0) {
            getPage('management/director/performance_summary.php?class_id=' + cls + '&term=' + term + '&school=' + school + '&token=' +
                _('token').value);
        }

    }

    function viewPerformance(term) {
        let cls = _('class').value;
        let school = _('school').value
        getPage('management/director/performance_summary.php?class_id=' + cls + '&term=' + term + '&school=' + school + '&token=' +
            _('token').value);
    }
    $(document).ready(function() {
        $("#studentsTable").DataTable(dataTableOptions);
        $(".js-example-basic-single").select2();
    });
</script>