<?php
require_once './includes/accountant.inc.php';

$signature_link = 'management/accountant/signature/' . Account::getSignature();
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Upload Signature</h4>
            <form method="post" onsubmit="updateSignature(event)" id="updateSignatureForm" enctype="multipart/form-data">

                <div class="mb-4">
                    <label for="picture" id="uploadTrigger" style="cursor: pointer; color:green;">Change Signature</label>
                    <div>
                        <input type="file" name="picture" id="picture" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png" />
                        <img id="image" width="100" height="100" src="<?php echo $signature_link ?>" />
                        <input type="hidden" name="hiddenPic" value="" id="hiddenPic" />
                        <div id="picMsg" class="errMsg"></div>
                    </div>
                </div>


                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <div class="mt-3">
                    <button class="btn btn-primary" type="submit" id="saveBtn">Save</button><span id="ld_loader"></span>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="scripts/management/accountant/signature.js"></script>