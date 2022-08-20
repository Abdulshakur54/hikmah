<?php
class Menu
{

    public function get(string $user_id){
        $db = DB::get_instance();
        $users_menu = Config::get('users/menu_table');
        $menu_table = Config::get('menu/menu_table');
        $username_column = Config::get('users/username_column');
        $db->query("select $menu_table.* from $menu_table inner join $users_menu  on $menu_table.id = $users_menu.menu_id where $users_menu.shown = 1  and $users_menu.$username_column = '$user_id' and $menu_table.parent_id = 0 order by $menu_table.parent_order asc", []);
        if($db->row_count() > 0){
            $first_level_parents = $db->get_result();
        }else{
            $first_level_parents = [];
        }
      
        $output_menus = $first_level_parents;
        foreach($output_menus as $menu){
            $db->query("select $menu_table.* from $menu_table inner join $users_menu  on $menu_table.id = $users_menu.menu_id where $users_menu.shown = 1  and $users_menu.$username_column = '$user_id' and $menu_table.parent_id = $menu->id order by $menu_table.menu_order asc",[]);
            if($db->row_count() > 0){
                $childrenMenu = $db->get_result();
                $menu->children = $childrenMenu;
            }
           
        }

        return $output_menus;

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
        $menus = self::get_role_menus($role_id);
        $menusArray = Utility::convertToArray($menus, 'id');
        $users_menu = Config::get('users/menu_table');
        $users_column = Config::get('users/username_column');
        $len = count($menusArray);
        if($len){
            $db->query('insert into ' . $users_menu . ' (' . $users_column . ',menu_id) values(?,?)', [$user_id, $menusArray[0]]);
            for ($i = 1; $i < $len; $i++) {
                $db->requery([$user_id, $menusArray[$i]]);
            }
        }
    }

    public static function delete_available_menus($user_id, $role_id)
    {
        $db = DB::get_instance();
        $menus = self::get_role_menus($role_id);
        $menusArray = Utility::convertToArray($menus, 'id');
        $menu_string = implode("','", $menusArray);
        $users_menu = Config::get('users/menu_table');
        $users_id_column = Config::get('users/username_column');
        $db->delete($users_menu, "$users_id_column='$$user_id' and id in('$menu_string')");
    }

}
