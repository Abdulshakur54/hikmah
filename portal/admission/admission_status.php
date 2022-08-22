<?php
require_once './includes/adm.inc.php';

if (Input::submitted() && Token::check(Input::get('token'))) {
    $decision = Utility::escape(Input::get('decision'));
    if ($decision === 'download') {
        $body = '
                <main>
                   <h2>OFFER OF ADMISSION</h2>
                   <p>Congratulations! ' . Utility::formatName($data->fname, $data->oname, $data->lname) . ' with Applicant ID: ' . $data->adm_id . '</p><p>You have been offered admission into ' . School::getLevelName($sch_abbr, $level) . ', ' . School::getFullName($sch_abbr) . '</p>
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
}

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Admission Status</h4>
            <?php
            $admStatus = $data->status;
            switch ($admStatus) {
                case 0:
                    if ($data->applied) {
                        echo '<div class="p-2 font-weight-bold"><p>A decision is yet to be made on your Application</p>';
                        echo '<p>Please, check back later</p></div>';
                    } else {
                        echo '<div class="p-2 font-weight-bold"><p>You are yet to apply for Admission</p>';
                        echo '<p><a onclick="getPage(\'admission/apply.php\')" href="">Complete your application to apply</a></p></div>';
                    }

                    break;
                case 1:
            ?>
                    <div class="card">
                        <div class="card-header text-center">
                            <h4>OFFER OF ADMISSION</h4>
                        </div>
                        <div class="card-body">
                            <?php
                            echo '<p>Congratulations! ' . Utility::formatName($data->fname, $data->oname, $data->lname) . ' with Applicant ID: ' . $data->adm_id . '</p><p>You have been offered admission into <span class="font-weight-bold">' . School::getLevelName($data->sch_abbr, $data->level) . ', ' . School::getFullName($data->sch_abbr) . '</span></p>';
                            echo '<input type="hidden" id="token" value="' . Token::generate() . '"  name="token"/>';
                            ?>
                        </div>
                        <div class="card-footer d-flex justify-content-center flex-wrap">
                            <?php
                            echo '<button id="acceptBtn" class="btn btn-md btn-success m-2" onclick ="acceptAdmission()" name="acceptBtn">Accept</button> <button id="declineBtn" class="btn btn-md btn-danger m-2" onclick ="declineAdmission()" name="declineBtn">Decline</button> <span id="ld_loader"></span> <button class="btn btn-md btn-secondary m-2" onclick ="downloadAdmission()" name="downloadBtn" id="downloadBtn">Download</button>';
                            ?>
                        </div>
                    </div>

            <?php
                    break;
                case 3:
                    echo '<div class="p-2 font-weight-bold"><p>You have decline our offer of admission</p>';
                    echo '<p>You can only try again next session</p></div>';
                    break;
                case 4:
                    echo '<div class="p-2 font-weight-bold"><p>We are sad to inform you that your application was not successful</p>';
                    echo '<p>Please, try again next session</p></div>';
                    break;
            }
            ?>
        </div>
        <input type="hidden" name="admId" id="admId" value="<?php echo $username ?>">
        <input type="hidden" name="id" id="id" value="<?php echo $id ?>">
    </div>
</div>
<script src="scripts/admission/admission_status.js"></script>