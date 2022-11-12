<?php
class Permission
{
    public static function get_subordinates(string $position, string $school = ''): array
    {
        $db = DB::get_instance();
        $position = strtolower($position);
        switch ($position) {
            case 'director':
                return $db->select('management', 'mgt_id,fname,oname,lname,rank,asst,sch_abbr', 'rank != 1 and active=1');
            case 'hrm':
                return $db->select('staff', 'staff_id,fname,oname,lname,rank,sch_abbr', "sch_abbr='$school' and active=1");
            case 'hos':
            case 'apm':
                $db->query('select student.std_id,student.fname,student.oname,student.lname,student.rank,student.sch_abbr,student.level,student.class_id,class.class from student inner join class on student.class_id = class.id where student.sch_abbr = ? and active = 1',[$school]);
                if($db->row_count() >0){
                    return $db->get_result();
                }
                return [];
            default:
                return [];
        }
    }

    public static function get_menus(string $user){
        $db = DB::get_instance();
        $db->query('select menu.description,menu.display_name,users_menu.id, users_menu.shown  from menu inner join users_menu on menu.id = users_menu.menu_id where users_menu.user_id=? and menu.parent_id !=0 order by menu.display_name asc',[$user]);
        if($db->row_count() > 0){
            return $db->get_result();
        }
        return [];
    }
}
