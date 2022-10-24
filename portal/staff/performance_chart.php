<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
require_once('../jpgraph/jpgraph.php');
require_once('../jpgraph/jpgraph_pie.php');
require_once('../jpgraph/jpgraph_pie3d.php');
if (Input::submitted('get') && !empty(Input::get('term'))) {
    $term = Utility::escape(Input::get('term'));
    $scores = Result::get_class_term_totals($currSession,$classId,$term);
    $scores = Utility::convertToArray($scores,$term.'_tot');
    $grades = Result::get_grades_summary($scores);
    $grades = ['A'=>3,'B'=>2,'C'=>4,'D'=>1,'E'=>0,'F'=>2];
    $gradeCollections = [];
    foreach($grades as $grade){
        $gradeCollections[] = $grade;
    }
    $pie_image_path = '../charts/' . $sch_abbr . $term . $level . $class . '_pie.png';
    Chart::pie_chart($gradeCollections, 'Chart Title',900,600,['A','B','C','D','E','F'], true, $pie_image_path);
} else {
    $term = '';
}

?>
<style>
    .pie-chart{
        width: 70%;
        height: auto;
        object-fit: cover;
        min-width:280px;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary pb-0 mb-0"><?php echo School::getLevelName($sch_abbr, $level) . ' ' . $class . ' '.Utility::formatTerm($term).' Performance Chart' ?></h4>
            <?php
            if (!empty($term)) {
            ?><div class="text-center">

                <img src="<?php echo 'portal/' . $pie_image_path ?>" class="pie-chart"/>
                <div class="font-weight-bold mb-3"><?php echo Result::format_grade_summary($grades,true)?></div>
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