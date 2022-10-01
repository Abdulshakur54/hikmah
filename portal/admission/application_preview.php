<?php
require_once './includes/adm.inc.php';
$download_link = $url->to('students_admission.php?adm_id=' . $data->adm_id . '&school=' . $data->sch_abbr . '&token=' . Token::generate(), 0);
function getStatus()
{
    global $data;
    switch ((int) $data->status) {
        case 0:
            return 'Awaiting Admission';
        case 1:
            return 'Admission Offered';
        case 2:
            return 'Admission Accepted';
        case 3:
            return 'Admission Declined'; //by Student
        case 4:
            return 'Admission Declined'; //by APM
    }
}

$msg = '';
$admId = $username;
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body font-weight-bold">
            <h4 class="card-title text-primary">Application Preview</h4>
            <?php
            if (!$adm->hasApplied($id)) {
                echo '<div>Incomplete Application. <a href="#" onclick="getPage(\'admission/apply.php\')">Proceed to complete</a></div>';
                exit();
            }
            $data = $adm->data();
            ?>
            <div class="text-center">
                <img src="<?php echo $url->to('uploads/passports/' . Utility::escape($data->picture), 5) ?>" alt="picture" width="100" height="100" />
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Bio Details</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">First Name: </div>
                        <div class="text-primary p-1"><?php echo ucfirst(Utility::escape($data->fname)); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Last Name</div>
                        <div class="text-primary p-1"><?php echo ucfirst(Utility::escape($data->lname)); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Other Name</div>
                        <div class="text-primary p-1"><?php echo ucfirst(Utility::escape($data->oname)); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Date Of Birth</div>
                        <div class="text-primary p-1"><?php echo Utility::formatDate($data->dob); ?></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Contacts</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Phone</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->phone); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Email</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->email); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Address</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->address); ?></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Application Information</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Application ID</div>
                        <div class="text-primary p-1"><?php echo strtoupper($admId); ?></div>
                    </div>
                    <?php $school = Utility::escape($data->sch_abbr); ?>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">School</div>
                        <div class="text-primary p-1"><?php echo School::getFullName($school); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Level</div>
                        <div class="text-primary p-1"><?php echo School::getLevelName($school, $data->level); ?></div>
                    </div>

                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Screening Score</div>
                        <div class="text-primary p-1"><?php echo $data->score ?></div>
                    </div class="d-flex justify-content-start">

                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Admission Status</div>
                        <div class="text-primary p-1"><?php echo getStatus(); ?></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Guardians Information</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Father Name</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->fathername); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1">Mother Name</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->mothername); ?></div>
                    </div>

                </div>
            </div>
            <div>
                <a href="<?php echo $download_link ?>" class="d-block text-center"><button type="button" class="btn btn-primary">Download</button></a>
            </div>
        </div>

    </div>
</div>
</div>
<!-- <script src="scripts/management/apm/add_token.js"></script> -->