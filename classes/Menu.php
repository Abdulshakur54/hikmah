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

    public static function get_role($role_id): string
    {
        $db = DB::get_instance();
        $db->query('select role from role where id = ?', [$role_id]);
        return $db->one_result()->role;
    }
    
    public static function edit_role($role, $role_id) :bool{
        $db = DB::get_instance();
        return $db->query('update role set role =? where id = ?', [$role, $role_id]);
    }

    public static function add_role($role) :bool{
        $db = DB::get_instance();
        return $db->query('insert into role(role) values (?)', [$role]);
    }

    public static function delete_role($role_id): bool
    {
        $db = DB::get_instance();
        return $db->query('delete from role where id = ?', [$role_id]);
    }

    
}
