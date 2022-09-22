<?php
require_once './includes/hos.inc.php';

function selectLevel($lev)
{
    global $level;
    if ($level !== null and $lev == $level) {
        return 'selected';
    }
}
$hos = new HOS();
$operation = Utility::escape(Input::get('operation'));

if (Input::submitted('get') && !empty($operation)) {
    if ($operation == 'edit') {
        $subject = Utility::escape(Input::get('subject_name'));
        $subject_id = Utility::escape(Input::get('subject_id'));
        $level = (int)Input::get('levelid');
    } else {
        $subject = '';
        $subject_id = '';
        $level = '';
    }
}

?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary"><?php echo ucfirst($operation) ?> Subject</h4>
            <form class="forms-sample" id="subjectForm" onsubmit="return false" novalidate>
                <?php
                $level = null; //this will only change when form is submitted via post
                if (Input::submitted() && Token::check(Input::get('token'))) {

                    $operation = Utility::escape(Input::get('operation'));
                    $subject_id = Utility::escape(Input::get('subject_id'));
                    $subject = Utility::escape(Input::get('subject'));
                    $subject = ucwords($subject);
                    $level = (int)Input::get('levelid');
                  
                    if ($operation == 'edit') {
                        if (preg_match('/^[a-zA-Z` ]{3,50}$/', $subject)) {
                            $hos->editSubject($subject_id, $subject);
                            $msg = '<div class="success">Successfully updated ' . $subject . ' for ' . School::getLevelName($sch_abbr, $level) . '</div>';
                        } else {
                            $msg = '<div class="failure">Invalid Subject Name</div>';
                        }
                    } else {
                        if (preg_match('/^[a-zA-Z` ]{3,50}$/', $subject)) {
                            if ($hos->subjectExists($sch_abbr, $level, $subject)) {
                                $msg = '<div class="failure">' . $subject . ' already exist for ' . School::getLevelName($sch_abbr, $level) . '</div>';
                            } else {

                                if ($hos->addSubject($sch_abbr, $subject, $level)) {

                                    $msg = '<div class="success">Successfully Added ' . $subject . ' for ' . School::getLevelName($sch_abbr, $level) . '</div>';
                                    $subject = '';
                                } else {
                                    $msg = '<div class="failure">No class has been added to ' . School::getLevelName($sch_abbr, $level) . ' yet</div>';
                                }
                            }
                        } else {
                            $msg = '<div class="failure">Invalid Subject Name</div>';
                        }
                    }
                }
                ?>
                <div class="form-group">
                    <label for="level">Level</label>
                    <select class="js-example-basic-single w-100 p-2" id="level" title="Level" name="levelid" required <?php echo ($operation == 'edit') ? 'disabled' : '' ?>>
                        <?php
                        $schLevels = School::getLevels($sch_abbr);
                        foreach ($schLevels as $levName => $lev) {
                            echo '<option value="' . $lev . '"' . selectLevel($lev) . '>' . $levName . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="subject" onfocus="clearHTML('messageContainer')" title="Subject" required pattern="^[a-zA-Z` ]{3,50}$" value="<?php echo $subject; ?>" name="subject">
                </div>
                <button type="button" class="btn btn-primary mr-2" id="addBtn" onclick="saveSubject()">Save</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
                <input type="hidden" value="<?php echo $operation ?>" name="operation" id="operation" />
                <input type="hidden" value="<?php echo $subject_id ?>" name="subject_id" id="subject_id" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/hos/subjects.js"></script>
<script>
    validate('subjectForm');;
</script>