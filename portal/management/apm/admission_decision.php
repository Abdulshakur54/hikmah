<?php
require_once './includes/apm.inc.php';

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

function outputScore($score)
{
    return (!empty($score)) ? $score : 'pending';
}

function getStatusText(int $status)
{
    switch ($status) {
        case 0:
            return '<span class="font-weight-bold">pending</span>';
        case 1:
            return '<span class="text-success font-weight-bold">offered</span>';
        case 2:
            return '<span class="text-danger font-weight-bold">rejected</span>';
    }
}

if (Input::submitted('get') && (!empty(Input::get('sch_abbr')) && !empty(Input::get('level')))) {
    $sch_abbr = Utility::escape(Input::get('sch_abbr'));
    $level = (int)Input::get('level');
    $applicants = $apm->selectAdmissionApplicants($sch_abbr, $level);
} else {
    $applicants = $apm->selectAdmissionApplicants();
}

?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Decision Page</h4>
            <div class="row d-flex justify-content-center">
                <div class="form-group mr-2">
                    <label for="sch_abbr" class="d-block text-center">School</label>
                    <div>
                        <select class="js-example-basic-single p-5" id="sch_abbr" title="School" name="sch_abbr" required onchange="populateLevel(this)">
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
            <div class="row">
                <div class="col">
                    <form class="forms-sample" id="form" onsubmit="return false">
                        <?php
                        if (!empty($applicants)) {
                        ?>
                            <div class="m-3 text-right">
                                <label for="selectAll" id="selectAll">Select all</label>
                                <input type="checkbox" name="selectAll" checked="checked" onclick="checkAll(this)" />
                            </div>
                            <table class="table table-hover display" id="admissionTable">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Adm ID</th>
                                        <th>Name</th>
                                        <th>Score</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = $applicants;
                                    foreach ($res as $val) {
                                        $idVal = Utility::escape(strtoupper($val->adm_id));
                                        $formatted_id = str_replace('/','_',$idVal);
                                        $status = getStatusText((int)$val->status);
                                        if ($val->status == 0) {
                                            $check_row = '<input type="checkbox" id="chk' . $formatted_id . '" checked /><input type="hidden" id="val' . $formatted_id . '" value="' . $idVal . '"/>';
                                            $reset_btn = '';
                                        } else {
                                            $check_row = '';
                                            $reset_btn = '<button class="btn btn-sm btn-secondary" onclick="resetDecision(\'' . $idVal . '\')" id="reset_btn_' . $formatted_id . '">reset decision</button><span id="ld_loader_' . $formatted_id . '"></span>';
                                        }
                    
                                        echo '<tr id="row' . $val->adm_id . '"><td></td><td>' . $idVal . '</td><td>' . Utility::escape(Utility::formatName($val->fname, $val->oname, $val->lname)) .
                                            '</td><td>' . outputScore($val->score) . '</td><td><a href="#" onclick="getPage(\'management/apm/application_preview.php?adm_id=' . $idVal . '\')">view application</a></td><td id="status_' . $formatted_id . '">' . $status . '</td><td id="check_' . $formatted_id.'">'. $check_row . '</td><td id="reset_' . $formatted_id . '">'.$reset_btn.'</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <input type="hidden" value="<?php echo ($count - 1); ?>" id="counter" />
                            <!--hidden counter -->

                        <?php
                        } else {
                            echo '<div class="message text-center py-5">No Admission Request</div>';
                        }

                        ?>
                        <div class="d-flex justify-content-center">
                            <button id="acceptAdm" class="btn btn-md bg-success mr-2">Accept</button><span id="ld_loader"></span><button id="declineAdm" class="btn btn-md bg-danger">Decline</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
    <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
</div>
<script src="scripts/management/apm/admission_decision.js"></script>