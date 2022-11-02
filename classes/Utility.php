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
                break;
            case 1:
                return $day . date(' F, Y', $date);
                break;
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

    public static function getFormatedSession($session)
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
}
