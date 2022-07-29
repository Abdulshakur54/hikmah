<?php
//initializations

use Mpdf\Tag\Dl;

spl_autoload_register(
    function ($class) {
        require_once '../../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
//end of initializatons
header("Content-Type: application/json; charset=UTF-8");
$message = '';
$status = 400;
$data = [];
$db = DB::get_instance();
if (Input::submitted() && Token::check(Input::get('token'))) {
    $op = Input::get('op');
    switch($op){
        case 'edit_role':
            $role = Utility::escape(Input::get('role'));
            $role_id = Utility::escape(Input::get('role_id'));
            $rules = [
                'role'=>['name'=>'Role','required'=>true, 'unique' => 'role/role'],
                'role_id' => ['name' => 'Role Id', 'required' => true]
            ];
            $val = new Validation();
            if(!$val->check($rules)){
                echo response(406, implode('<br />',$val->errors()));
                exit();
            }
            
            if(Menu::edit_role(['role'=>$role], $role_id)){
                echo response(204, 'Changes were saved successfully');
            }else{
                echo response(500, 'Something went wrong');
            }
        break;
        case 'edit_menu':
            $menu = Utility::escape(Input::get('menu'));
            $menu_id = Utility::escape(Input::get('menu_id'));
            $url = Utility::escape(Input::get('url'));
            $menu_order = Utility::escape(Input::get('menu_order'));
            $parent_id = Utility::escape(Input::get('parent_id'));
            $icon = Utility::escape(Input::get('icon'));
            $parent_order = Utility::escape(Input::get('parent_order'));
            $shown = (int)Utility::escape(Input::get('shown'));
            $active = (int)Utility::escape(Input::get('active'));
            $rules = [
                'menu' => ['name' => 'Menu', 'required' => true],
                'menu_id' => ['name' => 'Menu Id', 'required' => true],
                'url' => ['name' => 'Url', 'required' => true],
                'menu_order' => ['name' => 'Order', 'required' => true],
                'parent_id' => ['name' => 'Parent Id', 'required' => true],
                'parent_order' => ['name' => 'Parent Order', 'required' => true],
                'shown' => ['name' => 'Shown', 'required' => true],
                'active' => ['name' => 'Active', 'required' => true]
            ];
            $val = new Validation();
            if (!$val->check($rules)) {
                echo response(406, implode('<br />', $val->errors()));
                exit();
            }

            if (Menu::edit_menu(['menu'=>$menu,'url'=>$url,'menu_order'=>$menu_order,'parent_id'=>$parent_id,'icon'=>$icon,'parent_order'=>$parent_order,'shown'=>$shown,'active'=>$active], $menu_id)) {
                echo response(204, 'Changes were saved successfully');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
        case 'add_role':
            $role = Utility::escape(Input::get('role'));
            $rules = [
                'role' => ['name' => 'Role', 'required' => true, 'unique' => 'role/role']
            ];
            $val = new Validation();
            if (!$val->check($rules)) {
                echo response(406, implode('<br />', $val->errors()));
                exit();
            }
            
            if (Menu::add_role(['role'=>$role])) {
                echo response(204, 'Role was successfully inserted');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
        case 'add_menu':
            $menu = Utility::escape(Input::get('menu'));
            $url = Utility::escape(Input::get('url'));
            $menu_order = Utility::escape(Input::get('menu_order'));
            $parent_id = Utility::escape(Input::get('parent_id'));
            $icon = Utility::escape(Input::get('icon'));
            $parent_order = Utility::escape(Input::get('parent_order'));
            $shown = (int)Utility::escape(Input::get('shown'));
            $active = (int)Utility::escape(Input::get('active'));
            $rules = [
                'menu' => ['name' => 'Menu', 'required' => true],
                'url' => ['name' => 'Url', 'required' => true],
                'menu_order' => ['name' => 'Order', 'required' => true],
                'parent_id' => ['name' => 'Parent Id', 'required' => true],
                'parent_order' => ['name' => 'Parent Order', 'required' => true],
                'shown' => ['name' => 'Shown', 'required' => true],
                'active' => ['name' => 'Active', 'required' => true]
            ];
            $val = new Validation();
            if (!$val->check($rules)) {
                echo response(406, implode('<br />', $val->errors()));
                exit();
            }

            if (Menu::add_menu(['menu' => $menu, 'url' => $url, 'menu_order' => $menu_order, 'parent_id' => $parent_id, 'icon' => $icon, 'parent_order' => $parent_order, 'shown' => $shown, 'active' => $active])) {
                echo response(204, 'Changes were saved successfully');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
        case 'delete_role':
            $role_id = Utility::escape(Input::get('role_id'));

            if (Menu::delete_role($role_id)) {
                echo response(204, 'role was successfully deleted');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
        case 'delete_menu':
            $menu_id = Utility::escape(Input::get('menu_id'));

            if (Menu::delete_menu($menu_id)) {
                echo response(204, 'menu was successfully deleted');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
        case 'set_checked':
            $menu_id = Input::get('menu_id');
            $type = Input::get('type');
            $checked = (int)Input::get('checked');
            $db = DB::get_instance();
            if($db->update('menu', [$type => $checked], "id=" . $menu_id)) {
                echo response(204, '');
            } else {
                echo response(500, 'Something went wrong');
            }
            break;
    }
    
}


function response(int $status, $message = '', $data = [])
{
    return json_encode(['status' => $status, 'message' => $message, 'data' => $data, 'token' => Token::generate()]);
}
