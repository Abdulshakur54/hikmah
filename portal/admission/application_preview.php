<?php
require_once './includes/adm.inc.php';

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


if (Input::submitted('get') && Input::get('download') === 'true') {
    $school = Utility::escape($data->sch_abbr);
    //store the body content in a variable
    $body = '
           
                <main>
                    <div>
                        <div>Bio Details</div>
                        <div>
                            <img src="' . $url->to('uploads/passports/' . Utility::escape($data->picture), 5) . '" alt="picture" width="100" height="100"/>
                        </div>
                        <div>
                            <div class="label">First Name</div>
                            <div class="value">' . ucfirst(Utility::escape($data->fname)) . '</div>
                        </div>
                        <div>
                            <div class="label">Last Name</div>
                            <div class="value">' . ucfirst(Utility::escape($data->lname)) . '</div>
                        </div>
                        <div>
                            <div class="label">Other Name</div>
                            <div class="value">' . ucfirst(Utility::escape($data->oname)) . '</div>
                        </div>
                        <div>
                            <div class="label">Date Of Birth</div>
                            <div class="value">' . Utility::formatDate($data->dob) . '</div>
                        </div>
                    </div>
                    <div>
                        <div>Contacts</div>
                        <div>
                            <div class="label">Phone</div>
                            <div class="value">' . Utility::escape($data->phone) . '</div>
                        </div>
                        <div>
                            <div class="label">Email</div>
                            <div class="value">' . Utility::escape($data->email) . '</div>
                        </div>
                        <div>
                            <div class="label">Address</div>
                            <div class="value">' . Utility::escape($data->address) . '</div>
                        </div>
                    </div>
        <div>
            <div>Application Information</div>
            <div>
                <div class="label">Application ID</div>
                <div class="value">' . strtoupper($admId) . '</div>
            </div>
            <div>
                <div class="label">School</div>
                <div class="value">' . School::getFullName($school) . '</div>
            </div>
            <div>
                <div class="label">Level</div>
                <div class="value">' . School::getLevelName($school, $data->level) . '</div>
            </div>
            
            <div>
                <div class="label">Screening Score</div>
                <div class="value">' . $data->score . '</div>
            </div>
           
            <div>
                <div class="label">Admission Status</div>
                <div class="value">' . getStatus() . '</div>
            </div>
        </div>
        <div>
            <div>Guardians Information</div>
            <div>
                <div class="label">Father Name</div>
                <div class="value">' . Utility::escape($data->fathername) . '</div>
            </div>
            <div>
                <div class="label">Mother Name</div>
                <div class="value">' . Utility::escape($data->mothername) . '</div>
            </div>
        </div>
    </main>
               ';

    require_once '../../libraries/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($body);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->list_indent_first_level = 0;

    //call watermark content and image
    $mpdf->SetWatermarkText(School::getFullName($sch_abbr));
    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;
    //output in browser
    $mpdf->Output();
}
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
                <button onclick="getPag"><a href="<?php echo Utility::myself() . '?adm_id=' . $data->adm_id . '&download=true' ?>">Download</a></button>
            </div>
        </div>

    </div>
</div>
</div>
<!-- <script src="scripts/management/apm/add_token.js"></script> -->