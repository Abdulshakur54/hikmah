<?php
require_once './includes/staff.inc.php';
require_once './includes/sub_teacher.inc.php';

require_once '../../libraries/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory as IOObj;

$msg = '';
$utils = new Utils();
$table = $utils->getFormatedSession($sch_abbr) . '_score';
$scoreSettings = $staff->getScoreSettings($sch_abbr);
$subject = new Subject($subId, $table, $scoreSettings);
$scores = $subject->getScores($table, $currTerm);
//initialize some settings to false to help determine if it would be displayed on the screen
$faSet = $saSet = $ftSet = $stSet = $proSet = $examSet = false;
if ($scoreSettings['fa'] > 0) {
    $faSet = true;
}
if ($scoreSettings['sa'] > 0) {
    $saSet = true;
}
if ($scoreSettings['ft'] > 0) {
    $ftSet = true;
}
if ($scoreSettings['st'] > 0) {
    $stSet = true;
}
if ($scoreSettings['pro'] > 0) {
    $proSet = true;
}
if ($scoreSettings['exam'] > 0) {
    $examSet = true;
}

/*getting the required column*/
$columns = $subject->getNeededColumns($sch_abbr); //this returns an array  of the needed columns
/*
     * replace exam index with ex and store in another variable, this is done for compatibility for the column names in the different tables
     */
$pos = array_search('exam', $columns);
$scoreColumns = $columns;
if ($pos !== false) { //false is used for comparison because 0 is a valid value in this scenario
    $scoreColumns[$pos] = 'ex';
}


$title = $subName . ' ' . School::getLevName($sch_abbr, $subLevel) . $subClass . ' Scoresheet'; //this refers to something like 'mathematics 2a scoresheet

?>

<style>
    input:focus {
        border: 1px solid #ddd;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Students that have not completed subject registration</h4>
            <div class="d-flex justify-content-center" style="gap:20px;">
                <div>
                    <label for="import" id="importTrigger" style="cursor: pointer; color:blue;"><span class="mdi mdi-folder"></span> Import</label>
                    <div>
                        <input type="file" name="import" id="import" style="display: none" onchange="importFile(this)" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" /><span id="import_loader"></span>
                        <div id="importMsg" class="text-center"></div>
                    </div>
                </div>
                <div>
                    <span style="cursor: pointer; color:blue;" onclick="downloadScores()"><span class="mdi mdi-arrow-down-bold-circle"></span> Download</span>
                </div>
            </div>
            <form class="forms-sample" id="scoresForm" onsubmit="return false" novalidate>
                <?php echo $msg; ?>
                <?php
                if (!empty($scores)) {
                    $header = '<div class="table-responsive"><table class="table table-striped table-bordered nowrap responsive" id="scoresTable"><thead><th>SN</th><th>ID</th><th>Name</th>';
                    if ($faSet) {
                        $header .= '<th>FA</th>';
                    }
                    if ($saSet) {
                        $header .= '<th>SA</th>';
                    }
                    if ($ftSet) {
                        $header .= '<th>FT</th>';
                    }
                    if ($stSet) {
                        $header .= '<th>ST</th>';
                    }
                    if ($proSet) {
                        $header .= '<th>PRO</th>';
                    }
                    if ($examSet) {
                        $header .= '<th>EXAM</th>';
                    }
                    echo $header .= '</thead><tbody>';

                    foreach ($scores as $score) {
                        $tr = '<tr>';
                        $tr .= '<td></td><td>' . $score->std_id . '</td><td>' . Utility::formatName($score->fname, $score->oname, $score->lname) . '</td>';
                        if ($faSet) {
                            $col = $currTerm . '_fa';
                            $scr = $score->$col;
                            $tr .= '<td class="p-1" id="td_fa_' . $score->id . '"><input class="p-2 pl-3 pr-3 rounded" type="number" min="0" max="' . $scoreSettings['fa'] . '" value="' . $scr . '" onchange="update(' . $score->id . ',this)" id="fa_' . $score->id . '"/></td>';
                        }
                        if ($saSet) {
                            $col = $currTerm . '_sa';
                            $scr = $score->$col;
                            $tr .= '<td class="p-1" id="td_sa_' . $score->id . '"><input class="p-2 pl-3 pr-3 rounded" type="number" min="0" max="' . $scoreSettings['sa'] . '" value="' . $scr . '" onchange="update(' . $score->id . ',this)" id="sa_' . $score->id . '" /></td>';
                        }
                        if ($ftSet) {
                            $col = $currTerm . '_ft';
                            $scr = $score->$col;
                            $tr .= '<td class="p-1" id="td_ft_' . $score->id . '"><input class="p-2 pl-3 pr-3 rounded" type="number" min="0" max="' . $scoreSettings['ft'] . '" value="' . $scr . '" onchange="update(' . $score->id . ',this)" id="ft_' . $score->id . '" /></td>';
                        }
                        if ($stSet) {
                            $col = $currTerm . '_st';
                            $scr = $score->$col;
                            $tr .= '<td class="p-1" id="td_st_' . $score->id . '"><input class="p-2 pl-3 pr-3 rounded" type="number" min="0" max="' . $scoreSettings['st'] . '" value="' . $scr . '" onchange="update(' . $score->id . ',this)" id="st_' . $score->id . '" /></td>';
                        }
                        if ($proSet) {
                           
                            $col = $currTerm . '_pro';
                            $scr = $score->$col;
                            $tr .= '<td class="p-1" id="td_pro_' . $score->id . '"><input class="p-2 pl-3 pr-3 rounded" type="number" min="0" max="' . $scoreSettings['pro'] . '" value="' . $scr . '" onchange="update(' . $score->id . ',this)" id="pro_' . $score->id . '" /></td>';
                        }
                        if ($examSet) {
                            $col = $currTerm . '_ex';
                            $scr = $score->$col;
                            $tr .= '<td class="p-1" id="td_ex_' . $score->id . '"><input class="p-2 pl-3 pr-3 rounded" type="number" min="0" max="' . $scoreSettings['exam'] . '" value="' . $scr . '" onchange="update(' . $score->id . ',this)" id="ex_' . $score->id . '" /></td>';
                        }
                        $tr .= '</tr>';
                        echo $tr;
                    }
                    echo '</tbody></table></div>';
                } else {
                    echo '<div class="message">No record found</div>';
                }

                ?>
                <div id="genMsg"></div>
                <input type="hidden" name="updateddata" id="updateddata" />
                <!--to hold data that needs to be updated -->
                <input type="hidden" id="hasProject" value="<?php echo ($proSet) ? 'true' : 'false'; ?>" />
                <!--to determine it there is project or not -->

                <div style="visibility: hidden" id="scorecolumns"> <?php echo json_encode($scoreColumns) ?></div><!-- score columns -->
                <input type="hidden" name="token" id="token" value="<?php echo Token::generate() ?>" />
                <input type="hidden" id="subid" name="subid" value="<?php echo $subId ?>" />
                <button type="button" class="btn btn-primary mr-2" id="save" onclick="saveData()">Save</button><span id="save_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </form>
        </div>
    </div>
</div>
<?php
$msg = Session::get_flash('import');
if (!empty($msg)) {
?>
    <script>
        swalNotifyDismiss('<?php echo $msg ?>', 'success', 2000);
    </script>
<?php
}
?>
<script src="scripts/staff/scores.js"></script>