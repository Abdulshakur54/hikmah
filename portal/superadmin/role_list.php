<?php
require_once 'superadmin.inc1.php';
require_once '../includes/val_page_request.inc.php';
?>

<div class="col-lg-6 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Roles</h4>
            <script>
                function player() {
                    alert('worked');
                }
            </script>

            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2" onclick="getPage('superadmin/role_edit.php?op=add','scripts/superadmin/roles.js')">Add Role</button></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $roles = Menu::get_roles();
                        foreach ($roles as $role) {
                            echo '
                                     <tr id="row' . $role->id . '">
                                        <td>' . $role->role . '</td>
                                        <td class="text-info cursor-hand" onclick="getPage(\'superadmin/role_edit.php?op=edit&role_id=' . $role->id . '\',\'scripts/superadmin/roles.js\')">Edit</td>
                                        <td class="text-danger cursor-hand" onclick="deleteRole(' . $role->id . ')">Delete</td>
                                    </tr>
                                ';
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div><input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
</div><?php echo Token::generate(32, 'page_token') . 'status_code:200' ?>