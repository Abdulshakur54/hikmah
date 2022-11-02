<?php

require_once './includes/hrm.inc.php';
$staff_id = Utility::escape(Input::get('staff_id'));
$menu = new Menu();
$menus = Permission::get_menus($staff_id);
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h4 class="card-title text-primary">Set Permissions</h4>
                <button type="button" class="btn btn-light btn-sm" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover display" id="permissionTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Feature</th>
                            <th>Functions</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($menus)) {
                            foreach ($menus as $mn) {
                                echo '<tr>
                            <td></td>
                            <td>' . $mn->display_name . '</td>
                            <td>' . $mn->description . '</td>
                           <td id="td_' . $mn->id . '">
                            <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="shown_' . $mn->id . '" ' . ($mn->shown == 1 ? "checked" : "") . ' onchange="setChecked(this,' . $mn->id . ')"><span id="ld_loader_shown_' . $mn->id . '"></span>
                                    <label class="custom-control-label" for="shown_' . $mn->id . '"></label>
                                </div>
                            </td>
                         </tr>';
                            }
                        } else {
                            echo '<tr><td colspan ="4">No staff found</td></tr>';
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

        function setChecked(event, menuId, type) {
            const checked = event.checked ? 1 : 0;
            let newData;
            if (checked === 1) {
                newData = `
                 <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="shown_${menuId}" checked onchange="setChecked(this,${menuId})"><span id="ld_loader_shown_${menuId}"></span>
                                    <label class="custom-control-label" for="shown_${menuId}"></label>
                </div>
            `;
            } else {
                newData = `
                 <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="shown_${menuId}" onchange="setChecked(this,${menuId})"><span id="ld_loader_shown_${menuId}"></span>
                                    <label class="custom-control-label" for="shown_${menuId}"></label>
                </div>
            `;
            }

            table
                .cell("#td_" + menuId)
                .data(newData)
                .draw();
            ld_startLoading("shown_" + menuId, "ld_loader_" + "shown_" + menuId);
            ajaxRequest(
                "responses/responses.php",
                handleSetChecked,
                `op=set_checked&menu_id=${menuId}&checked=${checked}&token=${token.value}`
            );

            function handleSetChecked() {
                ld_stopLoading("shown_" + menuId, "ld_loader_" + "shown_" + menuId);
                const rsp = JSON.parse(xmlhttp.responseText);
                if (rsp.status != 204) {
                    swalNotifyDismiss(rsp.message, "error");
                }
                _("token").value = rsp.token;
            }
        }
    </script>