<?php
require_once './includes/adm.inc.php';
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Admission Application</h4>
            <?php
            if ($adm->hasApplied($id)) {
                if(!empty(Input::get('complete')) && Input::get('complete') == 'yes'){
                    $message = 'Congratulations! You successfully applied for Admission';
                }else{
                    $message = "You have already applied for admission";
                }
                $msg = '<div class="success p-2 font-weight-bold">'.$message.' <br><br><a onclick="getPage(\'admission/application_preview.php?adm_id=' . $id . '\')" href="#">View My Application</a></div>';
                echo $msg;
            } else {
            ?>
                <form class="forms-sample" id="applyForm" onsubmit="return false" novalidate>
                    <div class="form-group">
                        <label for="fatherName">Father's Name</label>
                        <input type="text" class="form-control" id="fatherName" onfocus="clearHTML('messageContainer')" title="FatherName" required pattern="^[a-zA-Z` ]+$">
                    </div>
                    <div class="form-group">
                        <label for="motherName">Mother's Name</label>
                        <input type="text" class="form-control" id="motherName" onfocus="clearHTML('messageContainer')" title="MotherName" required pattern="^[a-zA-Z` ]+$">
                    </div>
                    <div id="messageContainer"></div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary mr-2" id="applyBtn" onclick="applyAdmission()">Apply</button><span id="ld_loader"></span>
                    </div>

                    <input type="hidden" value="<?php echo $username ?>" name="username" id="username" />
                    <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                </form>
                <script>
                    validate('applyForm');
                </script>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<script src="scripts/admission/apply.js"></script>