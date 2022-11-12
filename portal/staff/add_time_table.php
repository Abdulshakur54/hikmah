<?php
require_once './includes/staff.inc.php';
require_once './includes/class_teacher.inc.php';
$operation = Input::get('operation');
if ($operation === 'edit') {
    if (empty(Input::get('id'))) {
        exit();
    }
    $id = Utility::escape(Input::get('id'));
    $activity_details = TimeTable::get($id);
    $day = (int)$activity_details->day;
    $activity = $activity_details->activity;
    $starts = $activity_details->start_time;
    $ends =  $activity_details->end_time;
} else {
    $day = 1;
    $activity = '';
    $starts = '';
    $ends =  '';
    $id= '';
}

function selected($cat, $value): string
{
    global $day, $activity;
    switch ($cat) {
        case 'day':
            return ($day == $value) ? 'selected' : '';
        case 'activity':
            return ($activity == $value) ? 'selected' : '';
        default:
            return '';
    }
}
?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary"><?php echo ucfirst($operation) ?> Activity</h4>
            <form class="forms-sample" id="activityForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="day">Day</label>
                    <select class="js-example-basic-single w-100 p-2" id="day" title="Day" name="day" required>
                        <?php
                        $days = Utility::getDays();
                        $genHtml = '';
                        foreach ($days as $d => $pos) {
                            $genHtml .= '<option value="' . $pos . '" ' . selected('day', $pos) . '>' . $d . '</option>';
                        }
                        echo $genHtml;

                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="activity">Activity</label>
                    <select class="js-example-basic-single w-100 p-2" id="activity" title="activity" name="activity" required onchange="changeActivity()">
                        <?php
                        $activities = TimeTable::getActivities((int)$classId);
                        var_dump($activities);

                        $other_activities = ['Assembly', 'Long Break', 'Short Break', 'Sports', 'Others'];
                        $genHtml = '';
                        foreach ($activities as $act) {
                            if (empty($act->subject)) {
                                $genHtml .= '<option value="' . $act->activity . '" ' . selected('activity', $act->activity) . '>' . ucwords($act->activity) . '</option>';
                            } else {

                                $genHtml .= '<option value="' . $act->id . '" ' . selected('activity', $act->id) . '>' . ucwords($act->subject) . '</option>';
                            }
                        }
                        foreach ($other_activities as $other_activity) {
                            $genHtml .= '<option value="' . $other_activity . '" ' . selected('activity', $other_activity) . '>' . $other_activity . '</option>';
                        }
                        echo $genHtml;

                        ?>
                    </select>
                </div>
                <div class="form-group" style="display: none;" id="othersDiv">
                    <label for="other">Please Specify</label>
                    <input type="text" class="form-control" id="other" name="other" title="Others" required>
                </div>
                <div class="form-group">
                    <label for="startTime">Starts</label>
                    <input type="time" class="form-control" id="startTime" name="startTime" title="Starts" required value="<?php echo $starts ?>">
                </div>
                <div class="form-group">
                    <label for="endTime">Ends</label>
                    <input type="time" class="form-control" id="endTime" name="endTime" title="Ends" required value="<?php echo $ends ?>">
                </div>
                <button type="button" class="btn btn-primary mr-2" id="addBtn" onclick="saveActivity('<?php echo $operation ?>')">Save</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo $classId ?>" name="class_id" id="class_id" />
                <input type="hidden" value="<?php echo $id ?>" name="id" id="id" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script>
    validate('activityForm');
    $(".js-example-basic-single").select2();

    function changeActivity() {
        const activity = _('activity').value;
        const othersDiv = _('othersDiv');
        if (activity === 'Others') {
            othersDiv.style.display = 'block';
        } else {
            othersDiv.style.display = 'none';
        }
    }

    async function saveActivity(operation) {
        const hiddenFields = {
            op: 'save_activity',
            operation,
            id:_('id').value,
            class_id:_('class_id').value
        };
        const startTime = _('startTime').value;
        const endTime = _('endTime').value;
        if (endTime <= startTime) {
            swalNotify('End must be greater than start');
        } else {

            if (validate('activityForm'), {
                    validateOnSubmit: true
                }) {
                await getPostPageWithUpload('activityForm', 'staff/responses/responses.php', hiddenFields, false);
                if (activity === 'Others') {
                    othersDiv.style.display = 'none';
                }
                if (operation == 'add') {

                    emptyInputs(["startTime", "endTime"]);
                }
                resetInputStyling("activityForm", "inputsuccess", "inputfailure");
            }
        }
    }
</script>