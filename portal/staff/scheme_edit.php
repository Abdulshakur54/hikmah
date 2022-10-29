<?php
require_once './includes/staff.inc.php';
require_once './includes/sub_teacher.inc.php';
$op = Utility::escape(Input::get('op'));
if ($op === 'edit') {
    $scheme_id = Utility::escape(Input::get('scheme_id'));
    $scheme_row =  Subject::get_scheme($scheme_id);
    $scheme = Utility::escape($scheme_row->scheme);
    $title = Utility::escape($scheme_row->title);
    $term = Utility::escape($scheme_row->term);
    $scheme_order = (int)Utility::escape($scheme_row->scheme_order);
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
                    <input type="text" class="form-control" id="term" value="<?php echo $term ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="menu">Title</label>
                    <input type="text" class="form-control" id="title" placeholder="Title" value="<?php echo $title ?>" onfocus="clearHTML('messageContainer')" title="Title" required>
                </div>
                <div class="form-group">
                    <label for="scheme">Scheme</label>
                    <textarea name="scheme" id="scheme" class="form-control" required title="Scheme" onfocus="clearHTML('messageContainer')"><?php echo $scheme ?></textarea>
                </div>
                <div class="form-group">
                    <label for="order">Order</label>
                    <input type="number" class="form-control" id="order" placeholder="Order" value="<?php echo $scheme_order ?>" onfocus="clearHTML('messageContainer')" title="Order" required min="1" name="order">
                </div>
                <input type="hidden" name="scheme_id" id="scheme_id" value="<?php echo $scheme_id ?>">
                <input type="hidden" name="subid" id="subid" value="<?php echo $subId ?>">
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <div id="messageContainer"></div>
                <button type="button" class="btn btn-primary mr-2" id="saveBtn" onclick="saveScheme('<?php echo $op . '_scheme' ?>')">Save</button><span id="ld_loader"></span>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </form>
        </div>
    </div>
</div>
<script src="scripts/staff/scheme.js"></script>
<script>
    validate('schemeForm');;
</script>