<?php
require_once './includes/apm.inc.php';
$classes = School::get_classes($sch_abbr);
$day = Input::get('day');
$class_id = Input::get('class_id');
if (empty($class_id)) {
    $first_row = $classes[0];
    $class_id = $first_row->id;
}
$current_activity_id = null;
if (Utility::in_session($sch_abbr)) {
    $current_activity_obj = TimeTable::get_current_activity($class_id);
    $current_activity_id = null;

    if (!empty($current_activity_obj)) {
        $current_activity_id = $current_activity_obj->id;
?>
        <div class="card m-3 mb-3  text-center rounded  border border-info">
            <div class="card-body">
                <span class="text-info text-light bg-info p-1 rounded-circle" style="font-size: 0.8em;">Now</span>
                <p class="font-weight-bold mt-3"><?php echo (!empty($current_activity_obj->subject)) ? ucwords(strtolower($current_activity_obj->subject)) : ucwords(strtolower($current_activity_obj->activity)) ?></p>
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
function selectedClass($id)
{
    global $class_id;
    return ((int)$class_id === $id) ? 'selected' : '';
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
            <div class="d-flex flex-wrap d-md-flex justify-content-center g-1 mb-3">
                <div class="flex-grow-1">
                    <label for="day" class="d-block">Day</label>
                    <select class="js-example-basic-single w-100 p-2" id="day" title="Day" name="day" required>
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
                <div class="flex-grow-1">
                    <label for="class_id" class="d-block">Class</label>
                    <select class="js-example-basic-single w-100 p-2" id="class_id" title="Class" name="class_id" required>
                        <?php
                        foreach ($classes as $class) {
                        ?>
                            <option value="<?php echo $class->id ?>" <?php echo selectedClass($class->id) ?>><?php echo School::getLevelName($sch_abbr, $class->level) . ' ' . $class->class ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="flex-grow-1 align-self-end">
                    <button class="w-100 btn btn-info" onclick="viewTimeTable()">View</button>
                </div>
            </div>
            <div class="d-flex justify-content-between flex-wrap mb-3">
                    <h4 class="card-title text-primary align-self-end">Time Table</h4>
                    <button type="button" class="btn btn-light" onclick="getPage('management/apm/schools.php')" id="returnBtn">Return</button>
            </div>

            <?php
            $time_tables = TimeTable::select($class_id, (int)$day);
            ?>

            <div id="timeTables" class="time-tables">
                <?php
                foreach ($time_tables as $time_table) {
                ?>
                    <div class="card time-table <?php echo ($time_table->id == $current_activity_id) ? "border border-info" : "" ?>" id="<?php echo 'row_' . $time_table->id ?>">
                        <div class="card-body">
                            <p class="font-weight-bold text-center"><?php echo (!empty($time_table->subject)) ? ucwords(strtolower($time_table->subject)) : ucwords(strtolower($time_table->activity)) ?></p>
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
<input type="hidden" name="school" id="school" value="<?php echo $sch_abbr ?>">
<script>
    $(".js-example-basic-single").select2();

    function viewTimeTable() {
        getPage('management/apm/time_table.php?day=' + _('day').value + '&class_id=' + _('class_id').value + '&school=' + _('school').value);
    }
</script>