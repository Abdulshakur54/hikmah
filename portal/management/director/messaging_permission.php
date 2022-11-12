<?php
require_once './includes/director.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            <h4 class="card-title text-primary">Messaging Permissions</h4>
            <div class="table-responsive">
                <table class="table table-hover display" id="permissionTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Management ID</th>
                            <th>Name</th>
                            <th>SMS</th>
                            <th>Email</th>
                            <th>Notification</th>
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
                            <td id="td_sms_' . $sb->id . '">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="sms_' . $sb->id . '" ' . ($sb->sms == 1 ? "checked" : "") . ' onchange="setChecked(this,' . $sb->id . ',\'sms\')" value="' . $sb->mgt_id . '"><span id="ld_loader_sms_' . $sb->id . '"></span>
                                    <label class="custom-control-label" for="sms_' . $sb->id . '"></label>
                                </div>
                            </td>
                            <td id="td_email_' . $sb->id . '">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_' . $sb->id . '" ' . ($sb->email == 1 ? "checked" : "") . ' onchange="setChecked(this,' . $sb->id . ',\'email\')" value="' . $sb->mgt_id . '"><span id="ld_loader_email_' . $sb->id . '"></span>
                                    <label class="custom-control-label" for="email_' . $sb->id . '"></label>
                                </div>
                            </td>
                            <td id="td_notification_' . $sb->id . '">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notification_' . $sb->id . '" ' . ($sb->notification == 1 ? "checked" : "") . ' onchange="setChecked(this,' . $sb->id . ',\'notification\')" value="' . $sb->mgt_id . '"><span id="ld_loader_notification_' . $sb->id . '"></span>
                                    <label class="custom-control-label" for="notification_' . $sb->id . '"></label>
                                </div>
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
    </div>
    <script>
        var table = $("#permissionTable").DataTable(dataTableOptions);

        function setChecked(event, rowId, type) {
            const checked = event.checked ? 1 : 0;
            const subordinate = _(`td_${type}_${rowId}`).querySelector('input[type=checkbox]').value;
            let newData;
            if (checked === 1) {
                newData = `<td id="td_${type}_${rowId}">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="${type}_${rowId}" checked onchange="setChecked(this,'${rowId}','${type}')" value="${subordinate}"><span id="ld_loader_${type}_${rowId}"></span>
                        <label class="custom-control-label" for="${type}_${rowId}"></label>
                    </div>
                </td>`;
            } else {
                newData = `<td id="td_${type}_${rowId}">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="${type}_${rowId}" onchange="setChecked(this,'${rowId}','${type}')" value="${subordinate}"><span id="ld_loader_${type}_${rowId}"></span>
                        <label class="custom-control-label" for="${type}_${rowId}"></label>
                    </div>
                </td>`;
            }
            table
                .cell(`#td_${type}_${rowId}`)
                .data(newData)
                .draw();

            ld_startLoading('td_' + type + "_" + rowId, "ld_loader_" + type + "_" + rowId);
            ajaxRequest(
                "management/director/responses/responses.php",
                handleSetChecked,
                `op=set_messaging_permission&menu_id=${rowId}&subordinate=${subordinate}&type=${type}&checked=${checked}&token=${token.value}`
            );

            function handleSetChecked() {
                ld_stopLoading('td_' + type + "_" + rowId, "ld_loader_" + type + "_" + rowId);
                const rsp = JSON.parse(xmlhttp.responseText);
                if (rsp.status != 204) {
                    swalNotifyDismiss(rsp.message, "error");
                }
                _("token").value = rsp.token;
            }
        }
    </script>