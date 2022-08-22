<?php
require_once './includes/apm.inc.php';
//this custom function helps to determine which option in the select tag for sch_abbr is selected
function getSelectedSchool($val)
{
    global $sch_abbr;
    return ($val === $sch_abbr) ? 'selected' : '';
}

function getSelectedLevel($lev)
{
    global $level;
    return ($lev == $level) ? 'selected' : '';
}

function getLevelName($sch_abbr, $level)
{
    $level = School::getLevelName($sch_abbr, $level);
    if (empty($level)) {
        return 'ALL';
    }
    return $level;
}
if (Utility::escape(Input::get('filter') === 'true') && Token::check(Input::get('token'))) { //if filter
    $sch_abbr = Utility::escape(Input::get('school'));
    $level = (int)Utility::escape(Input::get('level'));
}else{
    $sch_abbr = 'ALL';
    $level = 0;
}
$attachment = $apm->selectAdmissionAttatchments($sch_abbr, $level);

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Admission Attachments</h4>
            <form class="forms-sample" id="attachmentForm" onsubmit="return false" enctype="multipart/form-data">
                <div class="row d-flex justify-content-center">
                    <div class="form-group mr-2">
                        <label for="school" class="d-block text-center">School</label>
                        <div>
                            <select class="js-example-basic-single p-5" id="school" title="School" name="school" required onchange="populateLevel(this)" id="school">
                                <option value="ALL" selected>ALL</option>'
                                <?php
                                $sch_abbrs = School::getConvectionalSchools(2); //this returns the abbreviation for each of the convectional schools
                                foreach ($sch_abbrs as $sch_ab) {
                                    echo '<option value="' . $sch_ab . '" ' . getSelectedSchool($sch_ab) . '>' . $sch_ab . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="level" class="d-block text-center">Level</label>
                        <div>
                            <select class="js-example-basic-single p-5" id="level" title="Level" name="level" required>
                                <?php
                                echo '<option value="ALL" ' . getSelectedLevel("ALL") . '>ALL</option>';
                                $levels = School::getLevels($sch_abbr);
                                if (!empty($levels)) {
                                    foreach ($levels as $levName => $lev) {
                                        echo '<option value="' . $lev . '" ' . getSelectedLevel($lev) . '>' . $levName . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="col-sm-12 col-md-4 m-auto">
                        <button onclick="reSubmit()" class="btn btn-md bg-secondary w-100">Filter</button>
                    </div>
                </div>

                <div class="form-group m-2">
                    <label  class="form-label" for="selectedFile" id="uploadTrigger" style="cursor: pointer; color:green;">Browse File</label>

                    <input type="file" name="selectedFile" id="selectedFile" style="display: none" onchange="displayName(this,30)" /> <!-- 30 means 30Mb -->
                    <button onclick="uploadFile()" class="btn-success btn-sm">Attach</button>
                    <span id="nameIndicator"></span>
                    <input type="hidden" name="hiddenFileName" value="" id="hiddenFileName" />
                    <div id="errMsg"></div>

                </div>

                <?php
                if (!empty($attachment)) { ?>

                    <table class="table table-hover display" id="attachmentTable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>File Name</th>
                                <th>School</th>
                                <th>Level</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = $attachment;
                            foreach ($res as $val) {
                                $sch_abbr = Utility::escape($val->sch_abbr);
                                $level = Utility::escape($val->level);
                                echo '<tr id="row' . $val->id . '"><td></td><td>' . Utility::escape($val->name) . '</td><td>' . $sch_abbr . '</td><td>' . getLevelName($sch_abbr, $level) . '</td><td><button onclick="deleteAttachment(' . $val->id . ')" class="btn btn-md btn-danger" id="btn'. $val->id.'">Delete</button><span id="ld_loader_'.$val->id.'"></span></td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <!--hidden counter -->
                <?php

                } else {
                    echo '<div class="message">No Attachments Available</div>';
                }
                ?>
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/apm/admission_attachment.js"></script>