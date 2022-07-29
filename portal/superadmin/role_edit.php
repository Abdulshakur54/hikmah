<?php
require_once 'superadmin.inc1.php';
require_once '../includes/val_page_request.inc.php';
$op = Utility::escape(Input::get('op'));
if ($op === 'edit') {
    $role_id = Input::get('role_id');
    $role =  Menu::get_role($role_id);
} else {
    $role = '';
    $role_id = null;
}

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?php echo $op ?> Role</h4>
            <form class="forms-sample" id="roleForm">
                <div class="form-group">
                    <label for="role">Role</label>
                    <input type="text" class="form-control" id="role" placeholder="Name" value="<?php echo Utility::escape($role) ?>" onfocus="clearHTML('messageContainer')" title="Role" required>
                </div>
                <input type="hidden" name="role_id" id="role_id" value="<?php echo $role_id ?>">
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <div id="messageContainer"></div>
                <button type="button" class="btn btn-primary mr-2" id="saveBtn" onclick="saveRole('<?php echo $op . '_role' ?>')">Save</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </form>
        </div>
        <input type="hidden" name="page_token" id="page_token" value="<?php echo Token::generate(32, 'page_token') ?>">
    </div>
</div>
<script src="scripts/superadmin/roles.js"></script>
<script>
    validate('roleForm');;
</script>