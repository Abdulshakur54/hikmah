<?php 

class SMS{
    public static function send($message,$recipients)
    {
        $email = Config::get('sms/email');
        $password = Config::get('sms/password');
        $sender_name = Config::get('sms/sender_name');
       // $recipients = "mobile numbers seperated by comma e.9 2348028828288,234900002000,234808887800";
        $data = array("email" => $email, "password" => $password, "message" => $message, "sender_name" => $sender_name, "recipients" => $recipients);
        $data_string = json_encode($data);
        $ch = curl_init('https://api.80kobosms.com/v2/app/sms');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
        $result = curl_exec($ch);
        $res_array = json_decode($result);
        print_r($res_array);
    }
}