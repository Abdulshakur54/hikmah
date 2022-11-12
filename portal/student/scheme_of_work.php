<?php
require_once './includes/std.inc.php';
$term = (!empty(Input::get('term'))) ? Utility::escape(Input::get('term')) : $currTerm;
$sub_id = (!empty(Input::get('subid'))) ? Utility::escape(Input::get('subid')) : '';
$subject = new Subject();
$formatted_session = Utility::getFormattedSession($currSession) . '_score';
$registered_subjects = $subject->getRegisteredSubjectsId($formatted_session, $username);
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
            <h4 class="card-title text-primary">View Scheme of Work</h4>
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
            <div class="text-center text-danger" id="msgDiv">

            </div>
            <?php
            if (!empty($sub_id)) {

                $schemes = Subject::get_schemes($sub_id, $term);

                if (!empty($schemes)) {
                    $counter = 1;
                    foreach ($schemes as $scheme) {
            ?>
                        <div class="card border border-1 rounded mb-3">
                           <div class="card-header text-primary d-flex justify-content-between flex-wrap">
                                <div class="font-weight-bold"><?php echo $scheme->title ?></div>
                                <div class="font-italic "><?php echo 'Week ' . $counter ?></div>
                            </div>
                            <div class="card-body"><?php echo $scheme->scheme; ?></div>
                        </div>
            <?php
                        $counter++;
                    }
                } else {
                    echo '<div class="message text-center">Schemes have not been entered</div>';
                }
            }
            ?>
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

            getPage('student/scheme_of_work.php?term=' + term + '&subid=' + subId);
        }
    }
</script>