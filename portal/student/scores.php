<?php
require_once './includes/std.inc.php';
$term = (!empty(Input::get('term'))) ? Utility::escape(Input::get('term')) : $currTerm;
$sub_id = (!empty(Input::get('subid'))) ? Utility::escape(Input::get('subid')) : '';
$subject = new Subject();
$formatted_session = Utility::getFormatedSession($currSession) . '_score';
$registered_subjects = $subject->getRegisteredSubjectsId($formatted_session, $username);
$columns = $subject->getNeededColumns($sch_abbr); //this returns an array  of the needed columns
/*
     * replace exam index with ex and store in another variable, this is done for compatibility for the column names in the different tables
     */
$pos = array_search('exam', $columns);
$col_names = $columns;
if ($pos !== false) { //false is used for comparison because 0 is a valid value in this scenario
    $col_names[$pos] = 'ex';
}
function selectedTerm($tm)
{
    global $term;
    return ($term === $tm) ? 'selected' : '';
}
function selected_sub_id($sid)
{
    global $sub_id;
    return ($sub_id == $sid) ? 'selected' : '';
}
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">View your scores</h4>
            <div class="row d-flex justify-content-center m-2 flex-wrap" style="gap: 1rem">
                <div class="form-group d-flex align-items-center">
                    <label for="term" class="mr-2">Term</label>
                    <select class="js-example-basic-single w-100 p-2 flex-grow-1" id="term" title="Term" name="term" required>
                        <option value="ft" <?php echo selectedTerm('ft') ?>>First Term</option>
                        <option value="st" <?php echo selectedTerm('st') ?>>Second Term</option>
                        <option value="tt" <?php echo selectedTerm('tt') ?>>Third Term</option>
                    </select>
                </div>
                <div class="form-group d-flex align-items-center">
                    <label for="subid" class="mr-2">Subject</label>

                    <select class="js-example-basic-single w-100 p-2 flex-grow-1" id="subid" title="Subject" name="subid" required>
                        <option value="">:::Select Subject:::</option>
                        <?php
                        foreach ($registered_subjects as $reg_sub) { ?>
                            <option value="<?php echo $reg_sub->id ?>" <?php echo selected_sub_id($reg_sub->id) ?>><?php echo ucwords($reg_sub->subject) ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary" onclick="viewScheme()">View</button>
                </div>

            </div>
            <div class="text-center text-danger" id="msgDiv"></div>
            <div class="row d-flex justify-content-center">
                <div class="col-sm-6">
                    <?php
                    if (!empty($sub_id)) {

                        $score = Subject::getStudentScore($formatted_session, $currTerm, $username, $sub_id);
                        foreach ($col_names as $col_name) {

                    ?>
                            <div class="d-flex p-3">
                                <div class="font-weight-bold mr-3 w-sm-50"><?php echo Utility::getColumnDisplayName($col_name) ?></div>
                                <div><?php echo $score->{$term . '_' . $col_name} ?></div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        if ($(".js-example-basic-single").length) {
            $(".js-example-basic-single").select2();
        }
    });

    function viewScheme() {
        let term = document.getElementById('term').value;
        let subId = document.getElementById('subid').value;
        if (term.length < 1 || subId.length < 1) {
            document.getElementById('msgDiv').innerHTML = 'Select Term and Subject to proceed';
        } else {

            getPage('student/scores.php?term=' + term + '&subid=' + subId);
        }
    }
</script>