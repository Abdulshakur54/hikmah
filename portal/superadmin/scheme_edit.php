<?php
require_once './includes/staff.inc.php';
require_once './includes/sub_teacher.inc.php';
$op = Utility::escape(Input::get('op'));
if ($op === 'edit') {
    $scheme_id = Utility::escape(Input::get('scheme_id'));
    $scheme_row =  Subject::get_scheme($scheme_id, $term);
    $scheme = Utility::escape($scheme_row->scheme);
    $title - Utility::escape($scheme_row->title);
    $term - Utility::escape($scheme_row->term);
    $scheme_order - (int)Utility::escape($scheme_row->scheme_order);
} else {
    $scheme_id = '';
    $sub_id = $subId;
    $term = Utility::escape(Input::get('term'));
    $scheme = '';
    $title = '';
    $scheme_order = '';
}

?>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?php echo $op ?> Scheme</h4>
            <form class="forms-sample" id="schemeForm" onsubmit="return false">
                <div class="form-group">
                    <label for="term">Term</label>
                    <input type="text" class="form-control" id="term" value="<?php echo $term ?>"  required readonly>
                </div>
                <div class="form-group">
                    <label for="menu">Title</label>
                    <input type="text" class="form-control" id="title" placeholder="Title" value="<?php echo $title ?>" onfocus="clearHTML('messageContainer')" title="Title" required>
                </div>
                <div class="form-group">
                    <label for="display_name">Scheme</label>
                    <textarea name="scheme" id="scheme" class="form-control" required title="Scheme" onfocus="clearHTML('messageContainer')">
                    </textarea>
                </div>
                <div class="form-group">
                    <label for="url">Url</label>
                    <input type="text" class="form-control" id="url" placeholder="Url" value="<?php echo $url ?>" onfocus="clearHTML('messageContainer')" title="Url" required>
                </div>
                <div class="form-group">
                    <label for="order">Order</label>
                    <input type="number" class="form-control" id="scheme_order" placeholder="Order" value="<?php echo $scheme_order ?>" onfocus="clearHTML('messageContainer')" title="Order" required min="0">
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