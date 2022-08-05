<?php
class Menu
{
    private $menu_table, $role_table, $user_menu_table, $db;
    public function __construct()
    {
        $this->menu_table = Config::get('menu/menu_table');
        $this->role_table = Config::get('menu/role_table');
        $this->user_menu_table = Config::get('menu/user_menu_table');
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
        $db->query('select * from menu where id not in(select menu_id from roles_menu where role_id = ?) order by menu asc',[$role_id]);
        if($db->row_count()>0){
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

    public static function add_menu_to_users($role_id, array $menu__ids)
    {
        $db = DB::get_instance();
        $users_menu = Config::get('users/menu_table');
        $users_table = Config::get('users/table_name');
        $id_column = Config::get('users/username_column');
        $users = $db->select($users_table,$id_column,'role_id='.$role_id);
        $users_count = count($users);
        if($users_count > 0){
            foreach ($menu__ids as $menu__id) {
                $db->query('insert into ' . $users_menu . '(' . $id_column . ',menu_id) values(?,?)', [$users[0]->$id_column, $menu__id]);
                for ($i = 1; $i < $users_count; $i++) {
                    $db->requery([$users[$i]->$id_column, $menu__id]);
                }
            }
        }
    }
}
