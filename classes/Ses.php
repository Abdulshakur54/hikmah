<?php
class Ses
{
    public static function get(string $sch_abbr = ''): array
    {
        $db = DB::get_instance();
        if (!empty($sch_abbr)) {

            return $db->select('session', 'session', "sch_abbr='$sch_abbr'");
        } else {
            $db->query('select distinct session from session');
            if ($db->row_count() > 0) {
                return $db->get_result();
            }
            return [];
        }
    }

    public static function getStudentsCount(string $sch_abbr, string $session) :int{
        $db = DB::get_instance();
        return $db->get('session', 'std_count', "sch_abbr='$sch_abbr' and session = '$session'")->std_count;
    }
    public static function updateStudentCount(string $sch_abbr, string $session){
        $db = DB::get_instance();
        $db->update('session', ['std_count' => Ses::getStudentsCount($sch_abbr,$session) + 1], "sch_abbr='$sch_abbr' and session = '$session'");
    }

}
