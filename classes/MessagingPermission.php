<?php
class MessagingPermission{
    public static function get_subordinates(){
        $db = DB::get_instance();
        $db->query('select management.fname, management.oname, management.lname, management.mgt_id, messaging_permission.id, messaging_permission.user_id, messaging_permission.email,messaging_permission.notification,messaging_permission.sms from management inner join messaging_permission on management.mgt_id = messaging_permission.user_id where management.rank != 1');
        if($db->row_count() > 0){
            return $db->get_result();
        }
        return [];
    }
}