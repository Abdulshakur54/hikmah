<?php
require_once './includes/adm.inc.php';
?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Change Password</h4>
            <form class="forms-sample" id="changePasswordForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="presentPassword">Present Password</label>
                    <input class="form-control" id="presentPassword" title="Present Password" required type="password" pattern="^[A-Za-z0-9]+$" data-error-message="Present Password must contain only letters and numbers" minlength="6" maxlength="32">
                </div>
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input class="form-control" id="newPassword" title="New Password" required type="password" pattern="^[A-Za-z0-9]+$" data-error-message="New Password must contain only letters and numbers" minlength="6" maxlength="32">
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input class="form-control" id="confirmPassword" title="Confirm New Password" required type="text">
                </div>
                <div id="messageContainer"></div>
                <input type="hidden" value="<?php echo $username ?>" name="username" id="username" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <div class="text-center">
                    <button type="button" class="btn btn-primary mr-2" id="changePasswordBtn" onclick="changePassword()">Change Password</button><span id="ld_loader"></span>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    validate('changePasswordForm');

    function changePassword() {
        if (validate('changePasswordForm', {
                validateOnSubmit: true
            })) {
            const presentPassword = _("presentPassword");
            const newPassword = _("newPassword");
            const confirmPassword = _("confirmPassword");
            const username = _("username");
            const token = _("token");
            const op = 'change_password';
            if (newPassword.value === confirmPassword.value) {
                ld_startLoading("changePasswordBtn");
                ajaxRequest(
                    "admission/responses/responses.php",
                    changePasswordRsp,
                    "op=" + op +
                    "&username="+username.value+
                    "&password=" +
                    presentPassword.value +
                    "&new_password=" +
                    newPassword.value +
                    "&token=" +
                    token.value
                );
            } else {
                swalNotify('Confirm New Password must match New Password')
            }

        }

        function changePasswordRsp() {
            let rsp = JSON.parse(xmlhttp.responseText);
            ld_stopLoading("changePasswordBtn");
            _("token").value = rsp.token;
            const successCodes = [200, 201, 204];
            let msgStyleClass;
            const msgDiv = _("messageContainer");
            if (successCodes.includes(rsp.status)) {
                msgStyleClass = "success m-2";
            } else {
                msgStyleClass = "failure m-2";
            }
            msgDiv.innerHTML = "<div>" + rsp.message + "</div>"

            msgDiv.className = msgStyleClass;
            emptyInputs(["presentPassword", "newPassword", "confirmPassword"]);
            resetInputStyling("changePasswordForm", "inputsuccess", "inputfailure");
        }
    }
</script>