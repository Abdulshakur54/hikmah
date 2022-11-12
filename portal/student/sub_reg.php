<?php
require_once './includes/std.inc.php';
$msg = '';
$regSubArr = $minNoSub = null;
$sub = new Subject();
if (Input::submitted() && Token::check(Input::get('token'))) {
    $submitType = Input::get('submittype');
    if ($submitType == 'register') {
        $subIds = explode(',', Utility::escape(Input::get('subjectids')));
        $sub->registerSubjects($username, $subIds, $classId, $sch_abbr);
        $minNoSub = $sub->getMinNoSub($classId);
        $regSubArr = $sub->getRegisteredSubjectsId(Utility::getFormattedSession($currSession) . '_score', $username);
        if (($minNoSub <= count($regSubArr)) && !$data->sub_reg_comp) {
            //update table indicating that the minimum no of subjects has been registered
            $sub->updateCompSubReg($username);
        }
        $msg = '<div class="success">Registration was successful</div>';
    }
}

?>
<style>
    .regsub{
        border: 1px solid #ddd;
        border-radius: 5%;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Subjects Registration</h4>
            <form class="forms-sample" id="subRegForm" onsubmit="return false" novalidate />
            <?php echo $msg; ?>
            <?php
            if (!empty($classId)) {
                if (empty($regSubArr)) {
                    $regSubArr = $sub->getRegisteredSubjectsId(Utility::getFormattedSession($currSession) . '_score', $username);
                }

                $regListArr = $sub->getRegistrationList($classId);
                if (empty($minNoSub)) {
                    $minNoSub = $sub->getMinNoSub($classId);
                }

                $noOfRegSub = count($regSubArr);
                $noOfSubToReg = $minNoSub - $noOfRegSub;
                $regSubsIdArray = Utility::convertToArray($regSubArr, 'id');
                if (!empty($regSubArr)) {
                    echo '<div class="card-body m-3 regsub"><h6>Registered Subject</h6><p><em>' . implode(', ', Utility::convertToArray($regSubArr, 'subject')) . '</em></p></div>';
                }
            ?>

                <?php
                if (count($regListArr) > 0) {
                    if ($noOfSubToReg > 0) {
                        echo '<div class="message">Register at least ' . $noOfSubToReg . ' subjects</div>';
                    } ?>

                    <div class="m-3 text-right">
                        <label for="selectAll" id="selectAll">Select all</label>
                        <input type="checkbox" name="selectAll" onclick="checkAll(this)" />
                    </div>
                    <table class="table table-hover display" id="subjectTable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 0;
                            foreach ($regListArr as $rgl) {
                                if (!in_array($rgl->id, $regSubsIdArray)) {
                                    $counter++;
                                    echo '<tr><td></td><td>' . $rgl->subject . '</td><td> <input type="checkbox" id="chk' . $counter . '" value="' . $rgl->id . '"/></td></tr>';
                                }
                            }
                          
                            ?>
                        </tbody>
                    </table>
                    <input type="hidden" value="<?php echo $counter?>" id="counter" />
                    <input type="hidden" name="subjectids" id="subjectIds" />
                    <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                    <input type="hidden" name="submittype" id="submittype" />
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary mr-2" name="register" onclick="confirmSubmission()" id="regBtn">Register</button>
                    </div>

            <?php
                } else {
                    echo '<div class="message">No record found</div>';
                }
            } else {
                echo '<div class="message">You would be able to register subjects after you have been assigned to a class</div>';
            }
            ?>
            </form>
        </div>
    </div>
</div>
<script src="scripts/student/sub_reg.js"></script>