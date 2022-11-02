<?php

class SMS
{
    public static function send(string $message, string $recipients)
    {
        $email = "mabdulshakur54@gmail.com";
        $password = "190587Ab";
        $sender_name = Config::get('sms/sender_name');
        $forcednd = 1;
        $data = array("email" => $email, "password" => $password, "message" => $message, "sender_name" => $sender_name, "recipients" => $recipients, "forcednd" => $forcednd);
        $data_string = json_encode($data);
        $ch = curl_init('https://app.multitexter.com/v2/app/sms');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
        $result = curl_exec($ch);
        $res_array = json_decode($result);
       if($res_array->status === 1){
            return ['message' => $res_array->msg, 'status' => 200];
       }else{
            return ['message' => $res_array->msg, 'status' => 500];
       }
       
    }
}
