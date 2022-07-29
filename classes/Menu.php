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
    
    
    public static function edit_role(array $values, $role_id) :bool{
        $db = DB::get_instance();
        return $db->update('role', $values, 'id=' . $role_id);
    }

    public static function edit_menu(array $values,$menu_id): bool
    {
        $db = DB::get_instance();
        return $db->update('menu',$values,'id='.$menu_id);
    }

    public static function add_role(array $values) :bool{
        $db = DB::get_instance();
        return $db->insert('role', $values);
    }

    public static function add_menu(array $values): bool
    {
        $db = DB::get_instance();
        return $db->insert('menu',$values);
    }

    public static function delete_role($role_id): bool
    {
        $db = DB::get_instance();
        return $db->delete('role','id = '.$role_id);
    }

    public static function delete_menu($menu_id): bool
    {
        $db = DB::get_instance();
        return $db->delete('menu', 'id = ' . $menu_id);
    }


    
}
