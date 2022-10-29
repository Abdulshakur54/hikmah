<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
require_once('../jpgraph/jpgraph.php');
require_once('../jpgraph/jpgraph_pie.php');
require_once('../jpgraph/jpgraph_pie3d.php');
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
    $directory = '../charts/' . $sch_abbr . $term . $level . $class;
    if (!file_exists($directory)) {
        mkdir($directory);
    }
    File::delete_from_directory($directory, '*');
    $img_token = Token::create(3);
    $pie_image_path = $directory . '/pie_' . $img_token . '.png';
    $url_image_path = 'charts/' . $sch_abbr . $term . $level . $class . '/pie_' . $img_token . '.png';
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
            <h4 class="card-title text-primary pb-0 mb-0"><?php echo School::getLevelName($sch_abbr, $level) . ' ' . $class . ' ' . Utility::formatTerm($term) . ' Performance Chart' ?></h4>
            <?php
            if (!empty($term)) {
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
        getPage('staff/performance_chart.php?term=' + term +
            '&token=' +
            _('token').value);
    }
</script>