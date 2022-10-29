<?php
require_once './includes/apm.inc.php';
require_once('../../jpgraph/jpgraph.php');
require_once('../../jpgraph/jpgraph_pie.php');
require_once('../../jpgraph/jpgraph_pie3d.php');
$selected_level = $class_name =  null;

if (!Input::submitted('get')) {
    Redirect::to(404);
}
$term = (!empty(Input::get('term'))) ? Utility::escape(Input::get('term')) : '';
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
    if ($term === 'ses') {
        $ses_scores = Result::get_class_ses_totals($currSession, $classId);
        $scores = [];
        foreach ($ses_scores as $scr) {
            if (is_null($scr->ft_tot) && is_null($scr->st_tot) && is_null($scr->tt_tot)) {
                $scores[] = null;
            } else {
                $scores[] = $scr->ft_tot + $scr->st_tot + $scr->tt_tot;
            }
        }
    } else {

        $scores = Result::get_class_term_totals($currSession, $classId, $term);
        $scores = Utility::convertToArray($scores, $term . '_tot');
    }
    $grades = Result::get_grades_summary($scores);
    $gradeCollections = [];
    foreach ($grades as $grade) {
        $gradeCollections[] = $grade;
    }
    $gradesNotEmpty = false;
    foreach ($gradeCollections as $key => $val) { //ensure gradecollections is not empy
        if (!empty($gradeCollections[$key])) {
            $gradesNotEmpty = true;
            break;
        }
    }
    $directory = '../../charts/' . $sch_abbr . $term . $selected_level . $classId;
    if (!file_exists($directory)) {
        mkdir($directory);
    }
    File::delete_from_directory($directory, '*');
    $img_token = Token::create(3);
    $pie_image_path = $directory . '/pie_' . $img_token . '.png';
    $url_image_path = 'charts/' . $sch_abbr . $term . $selected_level . $classId . '/pie_' . $img_token . '.png';
    if ($gradesNotEmpty) {
        Chart::pie_chart($gradeCollections, 'Chart Title', 900, 600, ['A', 'B', 'C', 'D', 'E', 'F'], true, $pie_image_path);
    }
} else {
    $term = '';
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
                <h4 class="card-title text-primary pb-0 mb-0"><?php echo School::getLevelName($sch_abbr, $selected_level) . ' ' . $class_name . ' ' . Utility::formatTerm($term) . ' Performance Chart' ?></h4>

                <?php
                ?><div class="text-center">
                    <?php
                    if ($gradesNotEmpty) {

                    ?>
                        <img src="<?php echo $url_image_path ?>" class="pie-chart" />
                        <div class="font-weight-bold mb-3"><?php echo Result::format_grade_summary($grades, true) ?></div>
                    <?php
                    } else {
                    ?>
                        <div class="message my-5">No suitable values to plot graph</div>
                    <?php
                    }
                    ?>
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
                <button type="button" class="btn btn-light" onclick="getPage('management/apm/schools.php')" id="returnBtn">Return</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
<input type="hidden" value="<?php echo $sch_abbr ?>" name="school" id="school" />
<input type="hidden" value="<?php echo $term ?>" name="term" id="term" />

<script>
    function submitClass() {
        let cls = _('class').value;
        let school = _('school').value
        getPage('management/apm/performance_chart.php?class_id=' + cls +
            '&school=' + school +
            '&token=' +
            _('token').value);
    }

    function changeClass() {
        let cls = _('class').value;
        let term = _('term').value;
        let school = _('school').value;
        if (term.length > 0) {
            getPage('management/apm/performance_chart.php?class_id=' + cls + '&term=' + term + '&school=' + school +
                '&token=' +
                _('token').value);
        }

    }

    function viewPerformance(term) {
        let cls = _('class').value;
        let school = _('school').value
        getPage('management/apm/performance_chart.php?class_id=' + cls + '&school=' + school + '&term=' + term +
            '&token=' +
            _('token').value);
    }
    $(".js-example-basic-single").select2();
</script>