<?php
require_once './includes/director.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Management Members</h4>
            <div class="table-responsive">
                <table class="table table-hover display" id="memberTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Management ID</th>
                            <th>Name</th>
                            <th>Portfolio</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subordinates = Permission::get_subordinates('director');
                        if (!empty($subordinates)) {
                            foreach ($subordinates as $sb) {
                                echo '<tr id="row_' . $sb->mgt_id . '">
                            <td></td>
                            <td>' . $sb->mgt_id . '</td>
                            <td>' . Utility::formatName($sb->fname, $sb->oname, $sb->lname) . '</td>
                            <td>' . User::getFullPosition($sb->rank) . '</td>
                            <td><button class="btn btn-sm btn-warning" id="btn_' . $sb->mgt_id . '" onclick="remove(\'' . $sb->mgt_id . '\',\'sack\')">Sack</button><span id="ld_loader_sack_' . $sb->mgt_id . '"></span></td>
                            <td><button class="btn btn-sm btn-danger" id="btn_' . $sb->mgt_id . '" onclick="remove(\'' . $sb->mgt_id . '\',\'delete\')">Delete</button><span id="ld_loader_delete_' . $sb->mgt_id . '"></span></td>
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
        var table = $("#memberTable").DataTable(dataTableOptions);
        async function remove(id, type) {
            let confirmMessage, op;
            if (type == 'sack') {
                confirmMessage = '<p>This will sack member and relief him/her of his/her duties</p><p>His/Her data would be retained (not deleted)</p>';
                op = 'sack_management_member';
            } else {
                confirmMessage = '<p>This will delete member completely from the system</p><p>His/Her data would also be deleted</p>';
                op = 'delete_management_member';
            }
            if (await swalConfirm(confirmMessage, 'question')) {
                let userTable = _('userTable');
                let token = _('token');
                ld_startLoading('btn_' + id, `ld_loader_${type}_${id}`);
                let payload = 'id=' + id + '&op=' + op + '&token=' + token.value;
                ajaxRequest('management/director/responses/responses.php', handleSack, payload);
            }

            async function handleSack() {
                ld_stopLoading('btn_' + id, `ld_loader_${type}_${id}`);
                const rsp = JSON.parse(xmlhttp.responseText);
                _("token").value = rsp.token;
                if (rsp.status === 204) {
                    table
                        .row("#row_" + id)
                        .remove()
                        .draw();
                    swalNotify(rsp.message, "success");
                } else {
                    swalNotify(rsp.message, "error");
                }
            }
        }
    </script>