<?php
require_once './includes/apm.inc.php';

?>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Manual Student Admission</h4>
            <form class="forms-sample" id="manualAdmissionForm" onsubmit="return false" novalidate>
                <div class="message font-weight-bold">Fill the form below to manually admit a student</div>
                <div class="form-group">
                    <label for="fname">First Name</label>
                    <input type="text" class="form-control" id="fname" name="fname" title="First Name" required pattern="^[a-zA-Z`]+$" />
                </div>
                <div class="form-group">
                    <label for="lname">Last Name</label>
                    <input type="text" class="form-control" id="lname" name="lname" title="Last Name" required pattern="^[a-zA-Z`]+$" />
                </div>
                <div class="form-group">
                    <label for="oname">Other Name</label>
                    <input type="text" class="form-control" id="oname" name="oname" title="Other Name" pattern="^[a-zA-Z`]+$" />
                </div>
                <div class="form-group">
                    <label for="stdid">Student ID</label>
                    <input type="text" class="form-control" id="stdid" name="std_id" title="Student ID" required pattern="/^(HCK|HCB|HA|HIS|HM|HCI|H E-M|HCM|hck|hcb|ha|his|hm|hci|h e-m|hcm)\/[1-9][0-9]\/[1-9]\/[0-9]{3,5}$/" data-error-message="A valid Student ID will take the format: HCK/20/1/001" />
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" title="Date of Birth" required />
                </div>
                <div class="form-group">
                    <label for="state">State Of Origin</label>
                    <select class="js-example-basic-single w-100 p-2" id="state" title="State Of Origin" onchange="populateLGA(this)" name="state" required>
                        <?php
                        $states = Utility::getStates();
                        echo '<option value="">:::Select State:::</option>';
                        foreach ($states as $state) {
                            echo '<option value="' . $state['id'] . '">' . $state['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lga">LGA Of Origin</label>
                    <select class="js-example-basic-single w-100 p-2" id="lga" title="LGA Of Origin" name="lga" required>
                        <option value="">:::Select State First:::</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fatherName">Father Name</label>
                    <input type="text" class="form-control" id="fatherName" name="fathername" title="Father Name" required pattern="^[a-zA-Z` ]+$" />
                </div>
                <div class="form-group">
                    <label for="motherName">Mother Name</label>
                    <input type="text" class="form-control" id="motherName" name="mothername" title="Mother Name" required pattern="^[a-zA-Z` ]+$" />
                </div>
                <div class="form-group">
                    <label for="email">Email (Parent)</label>
                    <input type="email" class="form-control form-control-lg" id="email" title="Email" required name="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="text" class="form-control form-control-lg" id="password" title="Password" required name="password" pattern="^[a-zA-Z0-9]+$" minlength="6" maxlength="20">
                </div>
                <div class="form-group">
                    <label for="doa">Date of Admission</label>
                    <input type="date" class="form-control" id="doa" name="doa" title="Date of Admission" required />
                </div>
                <input type="hidden" value="<?php echo $data->rank ?>" name="rank" id="rank" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <div class="text-center p-3">
                    <button onclick="admitStudent()" class="btn btn-primary btn-md" id="admitBtn">Admit</button><span id="ld_loader"></span>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/apm/manual_admission.js"></script>