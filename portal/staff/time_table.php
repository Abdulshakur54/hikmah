<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
$day = Input::get('day');
$current_activity_id = null;
if (Utility::in_session($sch_abbr)) {
    $current_activity_obj = TimeTable::get_current_activity($classId);
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
            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2" onclick="getPage('staff/add_time_table.php?operation=add')">Add Activity</button></div>
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
            $time_tables = TimeTable::select($classId, (int)$day);
            ?>

            <div id="timeTables" class="time-tables">
                <?php
                foreach ($time_tables as $time_table) {
                ?>
                    <div class="card time-table <?php echo ($time_table->id == $current_activity_id)?"border border-info":""?>" id="<?php echo 'row_' . $time_table->id ?>">
                        <div class="card-body">
                            <p class="font-weight-bold text-center"><?php echo (!empty($time_table->subject)) ? ucwords(strtolower($time_table->subject)) : ucwords(strtolower($time_table->activity)) ?></p>
                            <p class="text-center">
                                <?php echo Utility::formatTime($time_table->start_time) . ' - ' . Utility::formatTime($time_table->end_time) ?>
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-end g-1">
                            <button class="btn btn-sm btn-success" onclick="getPage('staff/add_time_table.php?operation=edit&id=<?php echo $time_table->id ?>')">edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteActivity(<?php echo $time_table->id ?>)">delete</button>
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

    async function deleteActivity(activityId) {
        if (await swalConfirm('This will delete the selected activity')) {
            const token = _('token');
            ajaxRequest('staff/responses/responses.php', deleteActivityRsp, 'op=delete_activity&id=' + activityId + '&token=' + token.value);
        }

        function deleteActivityRsp() {
            let rsp = JSON.parse(xmlhttp.responseText);
            token.value = rsp.token;
            const successCodes = [200, 204, 201];
            if (successCodes.includes(rsp.status)) {
                _('timeTables').removeChild(_('row_' + activityId));
                swalNotify(rsp.message, 'success');
            } else {
                swalNotify(rsp.message, 'danger');
            }
        }
    }

    function changeDay() {
        getPage('staff/time_table.php?day=' + _('day').value);
    }
</script>