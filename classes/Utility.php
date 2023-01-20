<?php
class Utility
{

    //this method sanitizes a string value and returns it
    public static function escape($input): string
    {
        if (is_null($input)) {
            return '';
        } else {
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
    }

    public static function notEmpty($val): bool
    {

        return (!empty($val) || $val === (float) 0 || $val === (int) 0) ? true : false;
    }



    //this method checks if a string is equal and returns a boolean,it is case insenitive
    public static function equals($str1, $str2)
    {
        return (strtolower($str1) === strtolower($str2)) ? true : false;
    }

    //this is to be used by forms action attribute
    public static function myself()
    {
        return self::escape($_SERVER['PHP_SELF']);
    }

    //this is used to get the period of the day
    public static function getPeriod()
    {
        $hour = date('G');
        if ($hour < 12) {
            return 'Morning';
        } else if ($hour < 16) {
            return 'Afternoon';
        } else if ($hour < 21) {
            return 'Evening';
        } else {
            return 'Day';
        }
    }

    //this method helps to prevent client side scripts from being injected
    public static function noScript($input): bool
    {
        return (!preg_match('/<[ ]*script[ ]*>/', $input)) ? true : false;
    }


    //this function returns the time from a datetime value
    public static function getTime($datetime)
    {
        return explode(' ', $datetime)[1];
    }

    //this function returns the date from a datetime value
    public static function getDate($datetime)
    {
        return explode(' ', $datetime)[0];
    }

    //this method helps format name in the form 'Abdulshakur J. Muhammed'
    public static function formatName($fname, $oname, $lname, $shorten_other_name = true)
    {
        if (!empty($oname)) {
            if ($shorten_other_name) {
                return ucwords($fname . ' ' . substr($oname, 0, 1) . '. ' . $lname);
            }
            return ucwords($fname . ' ' . $oname . ' ' . $lname);
        }
        return ucwords($fname . ' ' . $lname);
    }

    public static function numSuffix(string $char): string
    {
        $num = (int)$char;
        if (floor($num / 10) == 1) {
            return 'th';
        }
        $lastLetter = substr($char, -1, 1);
        switch ($lastLetter) {
            case '0':
                return 'th';
            case '1':
                return 'st';
            case '2':
                return 'nd';
            case '3':
                return 'rd';
            case '4':
                return 'th';
            case '5':
                return 'th';
            case '6':
                return 'th';
            case '7':
                return 'th';
            case '8':
                return 'th';
            case '9':
                return 'th';
        }
    }

    public static function formatDay($date): string
    {
        $day = date('j', $date);
        return $day .= self::numSuffix($day);
    }

    public static function formatDate(string $date, int $formatType = 0): string
    {
        $date = strtotime($date);
        $day = self::formatDay($date);
        switch ($formatType) {
            case 0:
                return date('l, ', $date) . $day . date(' F, Y', $date);
            case 1:
                return $day . date(' F, Y', $date);
            default:
                return '';
        }
    }

   public static function formatFullDate(string $date, $fullMonth = false):string{
    if($fullMonth){
            return date('jS F, Y g:i A', strtotime($date));
    }else{
            return date('jS M, Y g:i A', strtotime($date));
    }
       
   } 

    //this method returns an array of randum numbers
    public static function randNums($start, $end, $count = null)
    {
        $numArr = [];
        for ($i = $start; $i <= $end; $i++) {
            $numArr[] = $i;
        }
        shuffle($numArr);
        if (isset($count)) { //if count is provided the array is trimmed so that it contains only count number of elements
            return array_slice($numArr, 0, $count);
        }
        return $numArr;
    }

    public static function getBanks(): array
    {
        $db = DB::get_instance();
        $banks = $db->select('banks', 'name');
        $banks = self::convertToArray($banks, 'name');
        return $banks;
    }

    public static function getStates(): array
    {
        $db = DB::get_instance();
        $states = $db->select('states', '*');
        $states = self::convertToArray($states, ['id', 'name']);
        return $states;
    }
    public static function getLgas($stateId): array
    {
        $db = DB::get_instance();
        $lgas = $db->select('local_governments', '*', 'state_id=' . $stateId);
        $lgas = self::convertToArray($lgas, ['id', 'name']);
        return $lgas;
    }

    public static function get_past_time(string $time)
    {
        $currently = date('Y-m-d H:i:s');
        $current_time = strtotime($currently);
        $past_time = strtotime($time);
        $difference = $current_time - $past_time;
        $difference_in_days = (int)floor($difference / (60 * 60 * 24));
        $difference_for_hours = $difference - $difference_in_days * 60 * 60 * 24;
        $hours_remaining = (int)floor(($difference_for_hours) / (60 * 60));
        $minutes_remaining = (int)floor(($difference_for_hours - $hours_remaining * 60 * 60) / 60);
        $count = 0;
        $output_string = '';
        if ($difference_in_days > 29) {
            if ($difference_in_days == 30) {
                $output_string = 'A month';
            } else {
                $month = (int) floor($difference_in_days / 30);
                $output_string = $month . ' month ';
                if ($month > 1) {
                    $output_string = $month . ' months ';
                }
                $rem_days = $difference_in_days - $month * 30;
                $output_string .= $rem_days . ' day';
                if ($rem_days > 1) {
                    $output_string .= 's';
                }
            }
            return $output_string . ' ago';
        } else if ($difference_in_days > 0) {
            $output_string .= $difference_in_days . ' day';
            if ($difference_in_days > 1) {
                $output_string .= 's';
            }
            $count++;
        }

        if ($hours_remaining > 0) {
            if ($count == 1) {
                $output_string .= ' ' . $hours_remaining . 'hr';
                if ($hours_remaining > 1) {
                    $output_string .= 's';
                }
                return $output_string . ' ago';
            } else {
                $output_string = $hours_remaining . 'hr';
                if ($hours_remaining > 1) {
                    $output_string .= 's';
                }
                $count++;
            }
        }

        if ($minutes_remaining > 0) {
            if ($count == 1) {
                $output_string .= ' ' . $minutes_remaining . 'min';
                if ($minutes_remaining > 1) {
                    $output_string .= 's';
                }
            } else {
                $output_string = $minutes_remaining . 'min';
                if ($minutes_remaining > 1) {
                    $output_string .= 's';
                }
            }
            return $output_string . ' ago';
        } else {
            return 'just now';
        }
    }

    public static function altValue($val, $altVal)
    {
        return (!empty($val)) ? $val : $altVal;
    }

    //this function helps to output an escaped value to the screen or html
    public static function output($name)
    {
        return self::escape(Input::get($name));
    }

    public static function getFormattedSession($session)
    {
        $arr = explode('/', $session);
        return $arr[0] . '_' . $arr[1];
    }

    public static function formatTerm($term): string
    {
        switch ($term) {
            case 'ft':
                return 'First Term';
            case 'st':
                return 'Second Term';
            case 'tt':
                return 'Third Term';
            case 'ses':
                return 'Sessional';
            default:
                return '';
        }
    }

    public static  function sum(array $num)
    {
        $sum = 0;
        foreach ($num as $x) {
            $sum += $x;
        }
        return $sum;
    }

    public static function getColumnDisplayName(string $col): string
    {
        switch ($col) {
            case 'fa':
                return 'First Assignment';
            case 'sa':
                return 'Second Assignment';
            case 'ft':
                return 'First Test';
            case 'st':
                return 'Second Test';
            case 'pro':
                return 'Project';
            case 'ex':
            case 'exam':
                return 'Exam';
        }
    }

    public static function convertToArray(array $result, string|array $columns): array
    {
        $res = [];
        if (is_string($columns)) {
            foreach ($result as $r) {
                $res[] = $r->$columns;
            }
        } else {
            foreach ($result as $r) {
                $vals = [];
                foreach ($columns as $col) {
                    $vals[$col] = $r->$col;
                }
                $res[] = $vals;
            }
        }
        return $res;
    }

    public static function getGenderFromTitle(string $title, GenderEnum $category): string
    {
        switch (strtolower($title)) {
            case 'mall':
            case 'mr':
                switch ($category->value) {
                    case 'subject':
                        return 'He';
                    case 'object':
                        return 'him';
                    case 'descriptive_subject':
                        return 'his';
                    default:
                        return '';
                }
            case 'mallama':
            case 'mrs':
            case 'miss':
                switch ($category->value) {
                    case 'subject':
                        return 'She';
                    case 'object':
                        return 'her';
                    case 'descriptive_subject':
                        return 'his';
                    default:
                        return '';
                }
        }
    }

    public static function format_student_id(string $student_id)
    {
        $arr = explode('/', $student_id);
        return implode('', $arr);
    }

    public static function get_bank_and_acctno(string $username)
    {
        $db = DB::get_instance();
        return $db->get('account', 'bank,no', "receiver='$username'");
    }

    public static function getDay(int $pos): string
    {
        switch ($pos) {
            case 1:
                return 'Monday';
            case 2:
                return 'Tuesday';
            case 3:
                return 'Wednesday';
            case 4:
                return 'Thursday';
            case 5:
                return 'Friday';
            case 6:
                return 'Saturday';
            case 7:
                return 'Sunday';
            default:
                return '';
        }
    }

    public static function getDayPos(string $day): int|null
    {
        switch (strtolower($day)) {
            case 'monday':
                return 1;
            case 'tuesday':
                return 2;
            case 'wednesday':
                return 3;
            case 'thursday':
                return 4;
            case 'friday':
                return 5;
            case 'saturday':
                return 6;
            case 'sunday':
                return 7;
            default:
                return null;
        }
    }

    public static function getDays(): array
    {
        return [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        ];
    }

    public static function formatTime(string $time): string
    {
        if (strlen($time) > 5) {
            //remove the seconds part of the time
            $time = substr($time, 0, strlen($time) - 3);
        }
        $hour = substr($time, 0, 2);
        if ($hour == '12') {
            return $time . ' PM';
        } else if ($hour > '12') {
            $cal_hour = (int)$hour - 12;
            $cal_hour = (strlen($cal_hour) < 2) ? '0' . $cal_hour : $cal_hour;
            return $cal_hour . ':' . substr($time, strlen($time) - 2) . ' PM';
        } else {
            return $time . ' AM';
        }
    }

    public static function in_session(string $school): bool
    {
        $now = date('Y-m-d');
        $db = DB::get_instance();
        $current_term = $db->get('school', 'current_term', "sch_abbr = '$school'")->current_term;
        $school_data = $db->get('school2', $current_term . '_res_date,' . $current_term . '_close_date', "sch_abbr = '$school'");
        return ($school_data->{$current_term . '_res_date'} < $now && $school_data->{$current_term . '_close_date'} > $now) ? true : false;
    }

    //this function is used to allow complete redirect into exam portal, this is done when the file url ends with new_exam.php
    public static function is_new_exam($url): bool

    {

        return (substr($url, -12) == 'new_exam.php') ? true : false;
    }

    public static function get_remaining_months(int $pos): array
    {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return array_slice($months, $pos - 1);
    }

    public static function get_months()
    {
        return ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
    }

    public static function get_month(string|int $month): string|int
    {
        if (is_numeric($month)) {
            switch ($month) {
                case 1:
                    return 'January';
                case 2:
                    return 'February';
                case 3:
                    return 'March';
                case 4:
                    return 'April';
                case 5:
                    return 'May';
                case 6:
                    return 'June';
                case 7:
                    return 'July';
                case 8:
                    return 'August';
                case 9:
                    return 'September';
                case 10:
                    return 'October';
                case 11:
                    return 'November';
                case 12:
                    return 'December';
                default:
                    return '';
            }
        } else {
            switch ($month) {
                case 'January':
                    return 1;
                case 'February':
                    return 2;
                case 'March':
                    return 3;
                case 'April':
                    return 4;
                case 'May:
                    return 5';
                case 'June':
                    return 6;
                case 'July':
                    return 7;
                case 'August':
                    return 8;
                case 'September':
                    return 9;
                case 'October':
                    return 10;
                case 'November':
                    return 11;
                case 'December':
                    return 12;
                default:
                    return '';
            }
        }
    }

    public static function get_years()
    {
        return [2050,2049,2048,2047,2046,2045,2044,2043,2042,2041,2040,2039,2038,2037,2036,2035,2034,2033,2032,2031,2030,2029,2028,2027,2026,2025,2024,2023,2022,2021,2020, 2019, 2018, 2017, 2016, 2015, 2014, 2013, 2012, 2011, 2010, 2009, 2008, 2007, 2006, 2005, 2004, 2003, 2002];
    }

    public static function response(int $status, $message = '', array $data = [])
    {
        return json_encode(['status' => $status, 'message' => $message, 'data' => $data, 'token' => Token::generate()]);
    }
    
}
