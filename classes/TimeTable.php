<?php


class TimeTable
{
    public static function select(int $class_id, int $day): array|object
    {
        $db = DB::get_instance();
        $db->query('select time_table.*, subject.subject from time_table left join subject2 on time_table.activity = subject2.id left join subject on subject2.subject_id = subject.id where time_table.class_id = ? and time_table.day = ? order by time_table.start_time asc', [$class_id, $day]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        } else {
            return [];
        }
    }
    public static function get(int $activity_id): array|object
    {
        $db = DB::get_instance();
        $db->query('select time_table.*, subject.subject from time_table left join subject2 on time_table.activity = subject2.id left join subject on subject2.subject_id = subject.id where time_table.id = ?', [$activity_id]);
        if ($db->row_count() > 0) {
            return $db->one_result();
        } else {
            return [];
        }
    }

    public static function getActivities(int $class_id)
    {
        $db = DB::get_instance();
        $db->query('select time_table.activity,subject.subject,subject.level,subject.sch_abbr,subject2.class_id,subject2.teacher_id,subject2.id from subject inner join subject2 on subject.id = subject2.subject_id left join time_table on subject2.id = time_table.activity where subject2.class_id = ?
        UNION
        select time_table.activity,subject.subject,subject.level,subject.sch_abbr,subject2.class_id,subject2.teacher_id,subject2.id from subject inner join subject2 on subject.id = subject2.subject_id right join time_table on subject2.id = time_table.activity where time_table.class_id = ?
        ', [$class_id, $class_id]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        }
        return [];
    }
    public static function get_current_activity(int $class_id): object|array
    {
        $current_time = date('H:i') . ':00';
        $current_day = date('l');
        $current_day_pos = Utility::getDayPos($current_day);
        $db = DB::get_instance();
        $db->query("select time_table.*, subject.subject from time_table left join subject2 on time_table.activity = subject2.id left join subject on subject2.subject_id = subject.id where time_table.class_id = ? and time_table.start_time <= '$current_time' and time_table.end_time >= '$current_time' and time_table.day = $current_day_pos", [$class_id]);
        if ($db->row_count() > 0) {
            return $db->one_result();
        }
        return [];
    }

    public static function get_period_activity(int $class_id, string $startTime, string $endTime,)
    {
        $db = DB::get_instance();
        $db->query("select time_table.*, subject.subject from time_table left join subject2 on time_table.activity = subject2.id left join subject on subject2.subject_id = subject.id where time_table.class_id = ? and start_time <= '$startTime' and start_time < '$endTime'", [$class_id]);
        if ($db->row_count() > 0) {
            return $db->one_result();
        } else {
            return [];
        }
    }
    public static function get_subject_teacher_activities(string $teacher_id, int $day): array|object
    {
        $db = DB::get_instance();
        $subjects_ids = $db->select('subject2','id',"teacher_id='$teacher_id'");
        $subjects_ids_array = Utility::convertToArray($subjects_ids,'id');
        $sub_ids = implode(',',$subjects_ids_array);
        $db->query("select time_table.*,class.class,class.level, subject.subject from time_table inner join subject2 on time_table.activity = subject2.id inner join subject on subject2.subject_id = subject.id inner join class on time_table.class_id = class.id where time_table.activity in ($sub_ids) and time_table.day = ? order by time_table.start_time asc", [$day]);
        if ($db->row_count() > 0) {
            return $db->get_result();
        } else {
            return [];
        }
    }

    public static function get_subject_teacher_current_activity(string $teacher_id): object|array
    {
        $current_time = date('H:i') . ':00';
        $current_day = date('l');
        $current_day_pos = Utility::getDayPos($current_day);
        $db = DB::get_instance();
        $subjects_ids = $db->select('subject2', 'id', "teacher_id='$teacher_id'");
        $subjects_ids_array = Utility::convertToArray($subjects_ids, 'id');
        $sub_ids = implode(',', $subjects_ids_array);
        $db->query("select time_table.*,class.class,class.level, subject.subject from time_table inner join subject2 on time_table.activity = subject2.id inner join subject on subject2.subject_id = subject.id inner join class on time_table.class_id = class.id where time_table.activity in ($sub_ids) and time_table.start_time <= '$current_time' and time_table.end_time >= '$current_time' and time_table.day = $current_day_pos");
        if ($db->row_count() > 0) {
            return $db->one_result();
        }
        return [];
    }
   
}
