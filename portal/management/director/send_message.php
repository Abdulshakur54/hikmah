<?php
require_once './includes/director.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <h4 class="card-title text-primary">Send Message</h4>
                <button type="button" class="btn btn-light btn-sm" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </div>
            <form class="forms-sample" id="messageForm" onsubmit="return false" novalidate>
                <div class="form-group">
                    <label for="msgType">Message Type</label>
                    <select class="js-example-basic-single w-100 p-2" id="msgType" title="Message Type" name="type" onchange="changeMessageType()" required>
                        <option value="sms">SMS</option>
                        <option value="email">Email</option>
                        <option value="notification">Notification</option>
                    </select>
                </div>
                <div class="form-group" id="titleDiv">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" title="Title" />
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control" id="message" title="Message" onkeyup="showDetails()" required></textarea>
                </div>
                <div id="messageFooter" class="d-flex justify-content-between" style="color:blue">
                    <div id="remCharDisplay"></div>
                    <div id="pageNo"></div>
                </div>
                <div class="text-left mt-5">
                    <label for="selectAll">Select All <input type="checkbox" checked id="selectAll" onchange="selectAll(this)" /></label>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover display" id="messagingTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Management ID</th>
                            <th>Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subordinates = MessagingPermission::get_subordinates();
                        if (!empty($subordinates)) {
                            foreach ($subordinates as $sb) {
                                echo '<tr>
                            <td></td>
                            <td>' . $sb->mgt_id . '</td>
                            <td>' . Utility::formatName($sb->fname, $sb->oname, $sb->lname) . '</td>
                            <td>
                                <input type="checkbox" class="checkbox" value="' . $sb->mgt_id . '" checked />
                            </td>
                         </tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
        <input type="hidden" value="management" name="userTable" id="userTable" />
        <input type="hidden" value="<?php echo $username ?>" name="sender" id="sender" />
        <div class="d-flex justify-content-center p-3">
            <button type="button" class="btn btn-primary mx-3" onclick="sendMessage()" id="sendBtn">Send</button>
            <span id="ld_loader"></span>
            <button type="button" class="btn btn-light mx-3" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
        </div>
    </div>
    <script>
        var table = $("#messagingTable").DataTable(dataTableOptions);
        $(".js-example-basic-single").select2();
        _('titleDiv').style.display = 'none';

        function selectAll(e) {
            const checkBoxes = document.querySelectorAll('input.checkbox');
            if (e.checked) {
                for (let chk of checkBoxes) {
                    chk.checked = true;
                }
            } else {
                for (let chk of checkBoxes) {
                    chk.checked = false;
                }
            }
        }

        function changeMessageType() {
            let msgType = _('msgType');
            msgType = msgType.value.toLowerCase();
            if (msgType === 'sms') {
                _('titleDiv').style.display = 'none';
            } else {
                _('titleDiv').style.display = 'block';
                _('remCharDisplay').innerHTML = '';
                _('pageNo').innerHTML = '';
            }
        }

        function sendMessage() {
            if (validate('messageForm', {
                    validateOnSubmit: true
                })) {

                const recipientIds = [];
                const checkBoxes = document.querySelectorAll('.checkbox');
                for (let chk of checkBoxes) {
                    if (chk.checked) {
                        recipientIds.push(chk.value);
                    }
                }
                if (recipientIds.length > 0) {
                    let msgType = _('msgType');
                    let title = '';
                    let message = _('message');
                    let userTable = _('userTable');
                    let sender = _('sender');
                    if (msgType.value.toLowerCase() !== 'sms') {
                        title = _('title').value;
                        if (msgType.value.toLowerCase() != 'sms' && title.trim().length < 1) {
                            swalNotify('Title is required', 'error');
                            return;
                        }
                    }
                    ld_startLoading('sendBtn');
                    let token = _('token');
                    ajaxRequest(
                        "responses/responses.php",
                        handleSendMessage,
                        `op=send_message&message_type=${msgType.value}&user_table=${userTable.value}&sender=${sender.value}&message=${message.value}&title=${title}&recipients=${JSON.stringify(recipientIds)}&token=${token.value}`
                    );

                    function handleSendMessage() {
                        ld_stopLoading('sendBtn');
                        const rsp = JSON.parse(xmlhttp.responseText);
                        _("token").value = rsp.token;
                        const validResponse = [200, 204];
                        if (validResponse.includes(rsp.status)) {
                            swalNotify(rsp.message, "success");
                        } else {
                            swalNotify(rsp.message, "error");
                        }
                    }
                } else {
                    swalNotify('No recipient selected', 'error');
                }
            }
        }

        function showDetails() {
            let msgType = _('msgType');
            let remCharDisplay = _('remCharDisplay');
            let messageFooter = _('messageFooter');
            let pageNo = _('pageNo');
            let message = _('message');
            let messageLength = message.value.length;
            if (msgType.value.toLowerCase() === 'sms' && messageLength > 0) {
                let pageIndex = Math.ceil((messageLength / 60));
                let characterRemaining = 60 * pageIndex - messageLength;
                if (characterRemaining > 0) {
                    remCharDisplay.innerHTML = characterRemaining + ' characters remaining';
                } else {
                    remCharDisplay.innerHTML = 'page ' + pageIndex + ' exhausted';
                }

                pageNo.innerHTML = 'page ' + pageIndex;
            } else {
                remCharDisplay.innerHTML = '';
                pageNo.innerHTML = '';
            }
        }

        validate('messageForm');
    </script>