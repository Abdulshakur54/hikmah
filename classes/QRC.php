<?php 
class QRC{
    public static function get_code(string $name, array $stored_data){
        $db = DB::get_instance();
        $db->query('select count(qr_name) as counter from qrcode where qr_name = ?',[$name]);
        if($db->one_result()->counter === 0){
            $token = password_hash(Token::generate(8), PASSWORD_DEFAULT);
            $identifier = Token::generate(8);
            $db->insert('qrcode', ['qr_name' => $name, 'token' => $token, 'identifier' => $identifier,'others'=>json_encode($stored_data)]);
        }
        $db->query('select qr_name,token,identifier from qrcode where qr_name = ?', [$name]);
        return $db->one_result();
    }

    public static function get_other_data(string $identifier) :array{
        $db = DB::get_instance();
        return json_decode($db->get('qrcode','others',"identifier = '$identifier'")->others,true);
    }

    public static function verify($identifier,$token) :bool{
        $db = DB::get_instance();
        $db->query('select token from qrcode where identifier=?', [$identifier]);
        if ($db->row_count() > 0){
            $res = $db->one_result();
            return password_verify($token,$res->token);
        }
        return false;
    }
}