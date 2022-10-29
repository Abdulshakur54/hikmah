<?php
require_once 'superadmin.inc1.php';
require_once './includes/val_page_request.inc.php';
$op = Utility::escape(Input::get('op'));
if ($op === 'edit') {
    $menu_id = Utility::escape(Input::get('menu_id'));
    $menu_row =  Menu::get_menu($menu_id);
    $display_name = Utility::escape($menu_row->display_name);
    $description = Utility::escape($menu_row->description);
    $menu = Utility::escape($menu_row->menu);
    $url = Utility::escape($menu_row->url);
    $menu_order = Utility::escape($menu_row->menu_order);
    $parent_id = Utility::escape($menu_row->parent_id);
    $icon = Utility::escape($menu_row->icon);
    $parent_order = Utility::escape($menu_row->parent_order);
    $shown = Utility::escape($menu_row->shown);
    $active = Utility::escape($menu_row->active);
} else {
    $menu_id = "";
    $menu = "";
    $display_name = '';
    $description = '';
    $url = "";
    $menu_order = "";
    $parent_id = "";
    $icon = "";
    $parent_order = "";
    $shown = "";
    $active = "";
}

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?php echo $op ?> Menu</h4>
            <form class="forms-sample" id="menuForm" onsubmit="return false">
                <div class="form-group">
                    <label for="menu">Menu</label>
                    <input type="text" class="form-control" id="menu" placeholder="Menu" value="<?php echo $menu ?>" onfocus="clearHTML('messageContainer')" title="Menu" required>
                </div>
                <div class="form-group">
                    <label for="display_name">Display Name</label>
                    <input type="text" class="form-control" id="display_name" placeholder="Display Name" value="<?php echo $display_name ?>" onfocus="clearHTML('messageContainer')" title="Display Name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" placeholder="Description" value="<?php echo $description ?>" onfocus="clearHTML('messageContainer')" title="Description" required>
                </div>
                <div class="form-group">
                    <label for="url">Url</label>
                    <input type="text" class="form-control" id="url" placeholder="Url" value="<?php echo $url ?>" onfocus="clearHTML('messageContainer')" title="Url" required>
                </div>
                <div class="form-group">
                    <label for="order">Order</label>
                    <input type="number" class="form-control" id="menu_order" placeholder="Order" value="<?php echo $menu_order ?>" onfocus="clearHTML('messageContainer')" title="Order" required min="0">
                </div>

                <div class="form-group">
                    <label for="parent_id">Parent ID</label>
                    <input type="number" class="form-control" id="parent_id" placeholder="Parent ID" value="<?php echo $parent_id ?>" onfocus="clearHTML('messageContainer')" title="Parent ID" required min="0">
                </div>
                <div class="form-group">
                    <label for="icon">Icon</label>
                    <input type="text" class="form-control" id="icon" placeholder="Icon" value="<?php echo $icon ?>" onfocus="clearHTML('messageContainer')" title="Icon">
                </div>

                <div class="form-group">
                    <label for="parent_order">Parent Order</label>
                    <input type="number" class="form-control" id="parent_order" placeholder="Parent Order" value="<?php echo $parent_order ?>" onfocus="clearHTML('messageContainer')" title="Parent Order" required min="0">
                </div>
                <div class="row container mb-4">
                    <div class="custom-control custom-switch col-6">
                        <input type="checkbox" class="custom-control-input" id="shown" <?php echo ($shown == 1 ? "checked" : "") ?>>
                        <label class="custom-control-label" for="shown">Shown</label>
                    </div>
                    <div class="custom-control custom-switch col-6">
                        <input type="checkbox" class="custom-control-input" id="active" <?php echo ($active == 1 ? "checked" : "") ?>>
                        <label class="custom-control-label" for="active">Active</label>
                    </div>
                </div>

                <input type="hidden" name="menu_id" id="menu_id" value="<?php echo $menu_id ?>">
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <div id="messageContainer"></div>
                <button type="button" class="btn btn-primary mr-2" id="saveBtn" onclick="saveMenu('<?php echo $op . '_menu' ?>')">Save</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </form>
        </div>
        <input type="hidden" name="page_token" id="page_token" value="<?php echo Token::generate(32, 'page_token') ?>">
    </div>
</div>
<script src="scripts/superadmin/menus.js"></script>
<script>
    validate('menuForm');;
</script>