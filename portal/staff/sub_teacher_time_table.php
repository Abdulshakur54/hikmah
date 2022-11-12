<?php
require_once './includes/staff.inc.php';
//require_once './includes/sub_teacher.inc.php';
$day = Input::get('day');
$current_activity_id = null;
if (Utility::in_session($sch_abbr)) {
    $current_activity_id = null;
    $current_activity_obj = TimeTable::get_subject_teacher_current_activity($username);

    if (!empty($current_activity_obj)) {
        $current_activity_id = $current_activity_obj->id;
?>
        <div class="card m-3 mb-3  text-center rounded  border border-info">
            <div class="card-body">
                <span class="text-info text-light bg-info p-1 rounded-circle" style="font-size: 0.8em;">Now</span>
                <p class="font-weight-bold mt-3"><?php echo ucwords(strtolower($current_activity_obj->subject)).' ('.School::getLevelName($sch_abbr,$current_activity_obj->level).' '.strtoupper($current_activity_obj->class.')') ?></p>
                <p>
                    <?php echo Utility::formatTime($current_activity_obj->start_time) . ' - ' . Utility::formatTime($current_activity_obj->end_time) ?>
                </p>
            </div>
        </div>

<?php
    }
}

function selected($d)
{
    global $day;
    return ((int)$day === $d) ? 'selected' : '';
}

?>
<style>
    .time-tables {
        display: flex;
        justify-content: start;
        flex-wrap: wrap;
        gap: 1em;
    }

    .time-table {
        width: 250px;
        border: 1px solid #ddd;
    }

    @media screen and (max-width:765px) {
        .time-table {
            width: 100%;
        }
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            <h4 class="card-title text-primary">Time Table</h4>
            <div class="form-group">
                <label for="day">Day</label>
                <select class="js-example-basic-single w-100 p-2" id="day" title="Day" name="day" required onchange="changeDay()">
                    <?php
                    $days = Utility::getDays();
                    $count = 0;
                    $genHtml = '';
                    foreach ($days as $d => $pos) {

                        if ($count === 0 && empty($day)) {
                            $genHtml .= '<option value="' . $pos . '" selected >' . $d . '</option>';
                            $day = $pos; //use the first day as the selected day
                        } else {
                            $genHtml .= '<option value="' . $pos . '" ' . selected($pos) . ' >' . $d . '</option>';
                        }
                        $count++;
                    }
                    echo $genHtml;

                    ?>
                </select>
            </div>
            <?php
            $time_tables = TimeTable::get_subject_teacher_activities($username,(int) $day);
    
            ?>

            <div id="timeTables" class="time-tables">
                <?php
                foreach ($time_tables as $time_table) {
                ?>
                    <div class="card time-table <?php echo ($time_table->id == $current_activity_id) ? "border border-info" : "" ?>" id="<?php echo 'row_' . $time_table->id ?>">
                        <div class="card-body">
                            <p class="font-weight-bold text-center"><?php echo ucwords(strtolower($time_table->subject)) . ' (' . School::getLevelName($sch_abbr, $time_table->level) . ' ' . strtoupper($time_table->class . ')') ?></p>
                            <p class="text-center">
                                <?php echo Utility::formatTime($time_table->start_time) . ' - ' . Utility::formatTime($time_table->end_time) ?>
                            </p>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php
            if (empty($time_tables)) {

                echo '<div class="message" id="noRec">No record found</div>';
            }
            ?>
        </div>
    </div>
</div>
<input type="hidden" name="token" id="token" value="<?php echo Token::generate() ?>">
<script>
    $(".js-example-basic-single").select2();

    function changeDay() {
        getPage('staff/sub_teacher_time_table.php?day=' + _('day').value);
    }
</script>