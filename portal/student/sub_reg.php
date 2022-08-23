<?php
require_once './includes/std.inc.php';
$msg = '';
$regSubArr = $minNoSub = null;
if (Input::submitted() && Token::check(Input::get('token'))) {
    $submitType = Input::get('submittype');
    if ($submitType == 'register') {
        $subIds = explode(',', Utility::escape(Input::get('subjectids')));
        $std->instantUtil();
        $std->registerSubjects($username, $subIds, $classId, $sch_abbr);
        $minNoSub = $std->getMinNoSub($classId);
        $regSubArr = $std->getRegisteredSubjectsId($username);
        if (($minNoSub <= count($regSubArr)) && !$data->sub_reg_comp) {
            //update table indicating that the minimum no of subjects has been registered
            $std->updateCompSubReg($username);
        }
        $msg = '<div class="success">Registration was successful</div>';
    }
}

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Subjects Registration</h4>
            <form class="forms-sample" id="subRegForm" onsubmit="return false" novalidate />
            <?php echo $msg; ?>
            <?php
            if (!empty($classId)) {
                if (empty($regSubArr)) {
                    $regSubArr = $std->getRegisteredSubjectsId($username); //to avoid requery of the database for a data that is already available, especially when the user submits
                }

                $regListArr = $std->getRegistrationList($classId);
                if (empty($minNoSub)) {
                    $minNoSub = $std->getMinNoSub($classId);  //to avoid requery of the database for a data that is already available, especially when the user submits
                }

                $noOfRegSub = count($regSubArr);
                $noOfSubToReg = $minNoSub - $noOfRegSub;
                $regSubsIdArray = array_keys($regSubArr);
                if (!empty($regSubArr)) {
                    echo '<div class="registeredSub"><h3>Already Registered Subject</h3><p><em>' . implode(', ', array_values($regSubArr)) . '</em></p></div>';
                }
            ?>

                <div class="formhead">Available Subjects For Registration</div>
                <?php
                if (count($regListArr) > 0) {
                    if ($noOfSubToReg > 0) {
                        echo '<div>Register at least ' . $noOfSubToReg . ' subjects</div>';
                    }

                    echo '<div><label for="checkall">Check All</label><input type="checkbox" id="checkall" onclick="checkAll(this)" /></div>';
                    echo '<table><thead><th>Subject Name</th><th>Check</th></thead><tbody>';
                    $counter = 0;
                    foreach ($regListArr as $subId => $subName) {
                        if (!in_array($subId, $regSubsIdArray)) {
                            $counter++;
                            echo '<tr><td>' . $subName . '</td><td><input type="checkbox" id="chk' . $counter . '" value="' . $subId . '"/></td></tr>';
                        }
                    }
                    echo '</tbody></table>';
                    echo '<input type="hidden" id="counter" value="' . $counter . '" />';
                ?>
                    <input type="hidden" name="subjectids" id="subjectIds" />
                    <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                    <input type="hidden" name="submittype" id="submittype" />
                    <div class="text-center mt-2">
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
            <script>
                validate('subRegForm');;
            </script>
        </div>
    </div>
</div>
<script src="scripts/student/sub_reg.js"></script>