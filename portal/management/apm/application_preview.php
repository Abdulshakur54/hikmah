<?php
require_once './includes/apm.inc.php';
$admId = Utility::escape(Input::get('adm_id'));
$adm = new Admission();
$data = $adm->getData($admId);

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

?>
<style>
    .subhead {
        border: 0;
        border-bottom: 1px solid #4B49AC;
    }

    .label {
        width: 130px;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="text-right"><button class="btn btn-md btn-primary" onclick="getPage('management/apm/admission_decision.php')">Back</button></div>
            <h4 class="card-title text-primary">Application Preview</h4>
            <div class="text-center">
                <img src="<?php echo $url->to('uploads/passports/' . Utility::escape($data->picture), 5) ?>" alt="picture" width="100" height="100" />
            </div>
            <div class="card">
                <h5 class="subhead ml-3 p-1">Bio Details</h5>
                <div class="card-body">
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">First Name: </div>
                        <div class="text-primary p-1"><?php echo ucfirst(Utility::escape($data->fname)); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Last Name</div>
                        <div class="text-primary p-1"><?php echo ucfirst(Utility::escape($data->lname)); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Other Name</div>
                        <div class="text-primary p-1"><?php echo ucfirst(Utility::escape($data->oname)); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Date Of Birth</div>
                        <div class="text-primary p-1"><?php echo Utility::formatDate($data->dob); ?></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <h5 class="subhead ml-3 p-1">Contacts</h5>
                <div class="card-body">
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Phone</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->phone); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Email</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->email); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Address</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->address); ?></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <h5 class="subhead ml-3 p-1">Application Information</h5>
                <div class="card-body">
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Application ID</div>
                        <div class="text-primary p-1"><?php echo strtoupper($admId); ?></div>
                    </div>
                    <?php $school = Utility::escape($data->sch_abbr); ?>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">School</div>
                        <div class="text-primary p-1"><?php echo School::getFullName($school); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Level</div>
                        <div class="text-primary p-1"><?php echo School::getLevelName($school, $data->level); ?></div>
                    </div>

                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Screening Score</div>
                        <div class="text-primary p-1"><?php echo $data->score ?></div>
                    </div class="d-flex justify-content-start">

                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Admission Status</div>
                        <div class="text-primary p-1"><?php echo getStatus(); ?></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h5 class="subhead ml-3 p-1">Guardians Information</h5>
                <div class="card-body">
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Father Name</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->fathername); ?></div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="mr-1 p-1 label">Mother Name</div>
                        <div class="text-primary p-1"><?php echo Utility::escape($data->mothername); ?></div>
                    </div>

                </div>
            </div>
            <div class="text-center"><button class="btn btn-md btn-primary" onclick="getPage('management/apm/admission_decision.php')">Decision Page</button></div>
        </div>
    </div>
</div>