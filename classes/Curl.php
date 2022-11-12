<?php
class Curl
{
    public static function get(string $url): string
    {
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handler);
        curl_close($handler);
        return $response;
    }
    public static function post(string $url, array $fields): string
    {
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler,CURLOPT_POST,true);
        curl_setopt($handler,CURLOPT_POSTFIELDS,$fields);
        $response = curl_exec($handler);
        curl_close($handler);
        return $response;
    }
}
