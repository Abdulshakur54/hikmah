<?php
require_once 'superadmin.inc1.php';
require_once '../includes/val_page_request.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2" onclick="getPage('superadmin/role_edit.php?op=add')">Add Role</button></div>
            <h4 class="card-title text-primary">Roles</h4>
            <div class="table-responsive">
                <table class="table table-hover display" id="rolesTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Role</th>
                            <th></th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $roles = Menu::get_roles();
                        foreach ($roles as $role) {
                            echo '
                                     <tr id="row' . $role->id . '">
                                        <td></td>
                                        <td>' . $role->role . '</td>
                                        <td><button class = "btn btn-success btn-sm" onclick="getPage(\'superadmin/role_edit.php?op=edit&role_id=' . $role->id . '\')">Edit</button></td>
                                        <td><button class="btn btn-danger btn-sm" onclick="deleteRole(' . $role->id . ')">Delete</button></td>
                                    </tr>
                                ';
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div><input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
    <input type="hidden" name="page_token" id="page_token" value="<?php echo Token::generate(32, 'page_token') ?>">
</div>
<script src="scripts/superadmin/roles.js">