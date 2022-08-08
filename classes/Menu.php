<?php
class Menu
{
    private $menu_table, $role_table, $user_menu_table, $output_menus;
    public function __construct()
    {
        $this->menu_table = Config::get('menu/menu_table');
        $this->role_table = Config::get('menu/role_table');
        $this->user_menu_table = Config::get('menu/user_menu_table');
        $this->output_menus = [];
    }

    public function get(string $user_id){
        $db = DB::get_instance();
        $users_menu = Config::get('users/menu_table');
        $username_column = Config::get('users/username_column');
        $menu_ids = $db->select($users_menu,'menu_id',"$username_column = '$user_id' and shown=1");
        $menu_ids_array = [];
        foreach($menu_ids as $menu_id){
            $menu_ids_array[] = $menu_id->menu_id;
        }
        $menu_ids_string = implode("','",$menu_ids_array);
        $first_level_parents = $db->select('menu', '*', 'shown = 1 and parent_id = 0 and id in(\'' . $menu_ids_string . '\')', 'parent_order');
        $output_menus = $first_level_parents;
        if(!empty($first_level_parents)){
            foreach ($output_menus as $menu) {
                $menu = $this->addChild($menu, true);
            }
            return $output_menus;
        }else{
            return [];
        }

    }

    function addChild($menu, bool $starting = false){
        $db = DB::get_instance();
        if($starting){
            $menu_id = $menu->id;
            $child_menus = $db->select('menu', '*', 'parent_id = ' . $menu_id . ' and shown =1');
            if (!empty($child_menus)) {
                $menu->children = $child_menus;
                $this->addChild($menu, false);
            }
            return $menu;
        }else{
            foreach($menu->children as $child){
                $child = $this->getAllChildren($child, $child);
            }
            return $menu;
           
        }
    }

    function getAllChildren($menu, $placeholder){
        $db =DB::get_instance();
        $menu_id = $placeholder->id;
        $child_menus = $db->select('menu', '*', 'parent_id = ' . $menu_id . ' and shown =1');
        if (!empty($child_menus)) {
            $placeholder->children = $child_menus;
            $this->getAllChildren($menu, $placeholder->children);
        }
        return $menu;

    }

    public static function get_roles()
    {
        $db = DB::get_instance();
        $db->query('select * from role order by role asc');
        return $db->get_result();
    }


    public static function get_menus()
    {
        $db = DB::get_instance();
        $db->query('select * from menu order by menu asc');
        return $db->get_result();
    }


    public static function get_role($role_id): string
    {
        $db = DB::get_instance();
        $db->query('select role from role where id = ?', [$role_id]);
        return $db->one_result()->role;
    }

    public static function get_menu($menu_id)
    {
        $db = DB::get_instance();
        $db->query('select * from menu where id = ?', [$menu_id]);
        return $db->one_result();
    }

    public static function get_available_menus($role_id)
    {
        $db = DB::get_instance();
        $db->query('select * from menu where id not in(select menu_id from roles_menu where role_id = ?)',[$role_id]);
        if($db->row_count()>0){
            return $db->get_result();
        }
        return [];
    }

    public static function get_role_menus($role_id)
    {
        $db = DB::get_instance();
        $db->query('select * from menu where id in(select menu_id from roles_menu where role_id = ?) order by menu asc', [$role_id]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }

    public static function edit_role(array $values, $role_id): bool
    {
        $db = DB::get_instance();
        return $db->update('role', $values, 'id=' . $role_id);
    }

    public static function edit_menu(array $values, $menu_id): bool
    {
        $db = DB::get_instance();
        return $db->update('menu', $values, 'id=' . $menu_id, true);
    }

    public static function add_role(array $values): bool
    {
        $db = DB::get_instance();
        return $db->insert('role', $values);
    }

    public static function add_menu(array $values): bool
    {
        $db = DB::get_instance();
        return $db->insert('menu', $values, true);
    }

    public static function delete_role($role_id): bool
    {
        $db = DB::get_instance();
        return $db->delete('role', 'id = ' . $role_id);
    }

    public static function delete_menu($menu_id): bool
    {
        $db = DB::get_instance();
        return $db->delete('menu', 'id = ' . $menu_id, true);
    }


    public static function add_menu_to_roles($role_id, array $menu__ids)
    {
        $db = DB::get_instance();

        foreach($menu__ids as $menu__id){
            $db->insert('roles_menu', ['role_id' => $role_id, 'menu_id' => $menu__id],true);
        }
    }

    public static function remove_menu_from_roles($role_id, array $menu__ids)
    {
        $db = DB::get_instance();
        $menu__ids_string = implode("','",$menu__ids);
        $db->delete('roles_menu','role_id='.$role_id.' and menu_id in(\''.$menu__ids_string.'\')',true);
    }

    public static function add_menu_to_users($role_id, array $menu__ids)
    {
        $db = DB::get_instance();
        $users_menu = Config::get('users/menu_table');
        $users_table = Config::get('users/table_name');
        $username_column = Config::get('users/username_column');
        $users = $db->select($users_table,$username_column,'role_id='.$role_id);
        $users_count = count($users);
        if($users_count > 0){
            foreach ($menu__ids as $menu__id) {
                $db->query('insert into ' . $users_menu . '(' . $username_column . ',menu_id) values(?,?)', [$users[0]->$username_column, $menu__id]);
                for ($i = 1; $i < $users_count; $i++) {
                    $db->requery([$users[$i]->$username_column, $menu__id]);
                }
            }
        }
    }

    public static function remove_menu_from_users($role_id, array $menu_ids)
    {
        $db = DB::get_instance();
        $users_menu = Config::get('users/menu_table');
        $users_table = Config::get('users/table_name');
        $username_column = Config::get('users/username_column');
        $users = $db->select($users_table, $username_column, 'role_id=' . $role_id);
        $users_array = [];
        foreach($users as $user){
            $users_array[] = $user->$username_column;
        }
        $users_count = count($users);
        if ($users_count > 0) {
            foreach ($menu_ids as $menu_id) {
                $users_string = implode("','", $users_array);
                $db->delete($users_menu,'menu_id = '.$menu_id.' and '.$username_column.' in(\''.$users_string.'\')');
            }
        }
    }

    public static function add_available_menus($user_id, $role_id)
    {
        $db = DB::get_instance();
        $menus = self::get_available_menus($role_id);
        $menusArray = Utility::convertToArray($menus, 'id');
        $users_menu = Config::get('users/menu_table');
        $len = count($menusArray);
        $db->query('insert into ' . $users_menu . ' (users_id,menu_id) values(?,?)', [$user_id, $menusArray[0]]);
        for ($i = 1; $i < $len; $i++) {
            $db->requery([$user_id, $menusArray[$i]]);
        }
    }

    public static function delete_available_menus($user_id, $role_id)
    {
        $db = DB::get_instance();
        $menus = self::get_available_menus($role_id);
        $menusArray = Utility::convertToArray($menus, 'id');
        $menu_string = implode("','", $menusArray);
        $users_menu = Config::get('users/menu_table');
        $users_id_column = Config::get('users/username_column');
        $db->delete($users_menu, "$users_id_column='$$user_id' and id in('$menu_string')");
    }

}
