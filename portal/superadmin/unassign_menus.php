<?php
require_once 'superadmin.inc1.php';
require_once '../includes/val_page_request.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Unassign Menus</h4>
            <div class="form-group">
                <label>Select Role</label>
                <select class="js-example-basic-single w-100 p-2" id="role" onfocus="clearHTML('messageContainer')" title="Role" required onchange="populateMenuTable(this)">
                </select> <span id="ld_loader_browse"></span>
            </div>
            <div id="messageContainer"></div>
            <div class="mb-1 text-right">
                <label class="form-check-label">
                    <input type="checkbox" onclick="checkAll(this)">
                    Select All
                </label>
            </div>
            <div class="table-responsive">
                <table class="table table-hover display" id="menusTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Menu</th>
                            <th>Url</th>
                            <th>Check</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-primary mr-2" id="removeBtn" onclick="removeMenuToRole()">Remove Selected Menus</button><span id="ld_loader_remove"></span>
                </div>
            </div>
        </div>
    </div><input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
    <input type="hidden" name="page_token" id="page_token" value="<?php echo Token::generate(32, 'page_token') ?>">
</div>
<script src="scripts/superadmin/unassign_menus.js">