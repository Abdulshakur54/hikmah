<?php
    class Utility{

        //this method sanitizes a string value and returns it
        public static function escape($input){
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
        


        //this method checks if a string is equal and returns a boolean,it is case insenitive
        public static function equals($str1,$str2){
            return (strtolower($str1) === strtolower($str2)) ? true:false;
        }
        
        //this is to be used by forms action attribute
        public static function myself(){
            return self::escape($_SERVER['PHP_SELF']);
        }

        //this is used to get the period of the day
        public static function getPeriod(){
            date_default_timezone_set('Africa/Lagos');
            $hour = date('G');
            if($hour < 12){
                return 'morning';
            }else if($hour < 16){
                return 'afternoon';
            }else if($hour < 21){
                return 'evening';
            }else{
                return 'day';
            }
        }

        //this method helps to prevent client side scripts from being injected
        public static function noScript($input) :bool{
            return (!preg_match('/<[ ]*script[ ]*>/',$input))?true:false;
        }


        //this function returns the time from a datetime value
        public static function getTime($datetime){
           return explode(' ',$datetime)[1];
        }

        //this function returns the date from a datetime value
        public static function getDate($datetime){
            return explode(' ',$datetime)[0];
         }

         //this method helps format name in the form 'Abdulshakur J. Muhammed'
         public static function formatName($fname,$oname,$lname){
            if(!empty($oname)){
                return ucwords($fname.' '.substr($oname,0,1).'. '.$lname);
            }
            return ucwords($fname.' '.$lname);
         }

         public static function numSuffix(string $char) :string{
            $lastLetter = substr($char,-1,1);
            switch($lastLetter){
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

         private static function formatDay($date) :string{
             $day = date('j',$date);
            return $day .= self::numSuffix($day);
         }

         public static function formatDate(string $date, int $formatType = 0) :string{
            $date = strtotime($date);
            $day = self::formatDay($date);
            switch($formatType){
                case 0:
                    return date('l, ',$date).$day.date(' F, Y',$date);
                break;
            }
         }

         //this method returns an array of randum numbers
         public static function randNums($start,$end,$count=null){
            $numArr = [];
            for($i=$start;$i<=$end;$i++){
                $numArr[] = $i;
            }
            shuffle($numArr);
            if(isset($count)){ //if count is provided the array is trimmed so that it contains only count number of elements
                return array_slice($numArr,0,$count);
            }
            return $numArr;
         }
         
        public static function getBanks() :array{
           $db = DB::get_instance();
           $banks = $db->select('banks','name');
           $banks = self::convertToArray($banks,'name');
           return $banks;
        }

        public static function getStates() :array{
           $db = DB::get_instance();
           $states = $db->select('states','*');
           $states = self::convertToArray($states,['id','name']);
           return $states;
        }
        public static function getLgas($stateId) :array{
           $db = DB::get_instance();
           $lgas = $db->select('local_governments','*','state_id='.$stateId);
           $lgas = self::convertToArray($lgas,['id','name']);
           return $lgas;
        }
        
        public static function altValue($val,$altVal){
            return (!empty($val))?$val:$altVal;
        }
        
        //this function helps to output an escaped value to the screen or html
        public static function output($name){
            return self::escape(Input::get($name));
        }
        
        public static function getFormatedSession($session){
            $arr = explode('/', $session);
            return $arr[0].'_'.$arr[1];
        }
        
        public static function formatTerm($term) :string{
            switch($term){
                case 'ft':
                    return 'First Term';
                case 'st':
                    return 'Second Term';
                case 'tt':
                    return 'Third Term';
                
            }
        }
        
        public static  function sum(array $num){
            $sum = 0;
            foreach($num as $x){
                $sum+=$x;
            }
            return $sum;
        }

        public static function convertToArray(array $result,$columns) :array{
            $res = [];
            if(is_string($columns)){
                foreach($result as $r){
                    $res[]=$r->$columns;
                }
            }else{
            foreach ($result as $r) {
                $vals = [];
                foreach($columns as $col){
                    $vals[$col] = $r->$col;
                }
                $res[]=$vals;
            }
            }
            return $res;
        }
       
    }
    
    
