<?php
require_once 'superadmin.inc1.php';
require_once './includes/val_page_request.inc.php';
?>

<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="text-right"> <button type="submit" class="btn btn-info btn-sm mr-2" onclick="getPage('superadmin/menu_edit.php?op=add')">Add Menu</button></div>
            <h4 class="card-title text-primary">Menus</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered nowrap" style="width:100%" id="menusTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Menu</th>
                            <th>ID</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Url</th>
                            <th>Order</th>
                            <th>Parent ID</th>
                            <th>Icon</th>
                            <th>Parent Order</th>
                            <th>Shown</th>
                            <th>Active</th>
                            <th></th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $menus = Menu::get_menus();
                        foreach ($menus as $menu) {
                            echo '
                                     <tr id="row' . $menu->id . '">
                                        <td></td>
                                        <td>' . $menu->menu . '</td>
                                        <td>' . $menu->id . '</td>
                                        <td>' . $menu->display_name . '</td>
                                        <td>' . $menu->description . '</td>
                                        <td>' . $menu->url . '</td>
                                        <td>' . $menu->menu_order . '</td>
                                        <td>' . $menu->parent_id . '</td>
                                        <td>' . $menu->icon . '</td>
                                        <td>' . $menu->parent_order . '</td>
                                        <td id="td_shown_'.$menu->id.'">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="shown_' . $menu->id . '" ' . ($menu->shown == 1 ? "checked" : "") . ' onchange="setChecked(this,' . $menu->id . ',\'shown\')"><span id="ld_loader_shown_' . $menu->id . '"></span>
                                                <label class="custom-control-label" for="shown_' . $menu->id . '"></label>
                                            </div>
                                        </td>
                                         <td id="td_active_' . $menu->id . '">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="active_' . $menu->id . '" ' . ($menu->active == 1 ? "checked" : "") . ' onchange="setChecked(this,' . $menu->id . ',\'active\')"><span id="ld_loader_active_' . $menu->id . '"></span>
                                                <label class="custom-control-label" for="active_' . $menu->id . '"></label>
                                            </div>
                                        </td>
                                        <td><button class = "btn btn-success btn-sm" onclick="getPage(\'superadmin/menu_edit.php?op=edit&menu_id=' . $menu->id . '\')">Edit</button></td>
                                        <td><button class="btn btn-danger btn-sm" onclick="deleteMenu(' . $menu->id . ')" id="delete_' . $menu->id . '">Delete</button><span id="ld_loader_delete_' . $menu->id . '"></span></td>
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
<script src="scripts/superadmin/menus.js">