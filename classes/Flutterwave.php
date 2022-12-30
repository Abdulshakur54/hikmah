<?php
class Flutterwave
{
    public static function accept($amount, $email, $name, $phone)
    {
        $tx_ref = Token::create(6);
        $url = new Url();
        $redirect_url = $url->to('test2_redirect.php', 0);
        $customer = ['name' => $name, 'email' => $email, 'phonenumber' => $phone];
        $data = ['tx_ref' => $tx_ref, 'amount' => $amount, 'currency' => 'NGN', 'redirect_url' => $redirect_url, 'customer' => $customer];
        $ch = curl_init('https://api.flutterwave.com/v3/payments');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
        $headers = [];
        $headers[] = 'Content-Type:application/json';
        $token = "FLWSECK_TEST-65de94da82ba58636eb09e4810a6cdf1-X";
        $headers[] = "Authorization: Bearer " . $token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $res_obj = json_decode($result);

        if ($res_obj->status === 'success') {
            $link = $res_obj->data->link;
            header('Location: ' . $link);
            exit();
        } else {
            echo 'Not successful';
        }
    }

    public static function get_banks()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/banks/NG",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer FLWSECK_TEST-65de94da82ba58636eb09e4810a6cdf1-X"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    // public static function send($title,$bulk_data){
    //     $details = [
    //         "title" =>  "Staff salary for April",
    //         "bulk_data" => [
    //             [
    //                 "bank_code" => "044",
    //                 "account_number" => "0690000032",
    //                 "amount" => 690000,
    //                 "currency" => "NGN",
    //                 "narration" => "Salary payment for April",
    //             ],
    //             [
    //                 "bank_code" => "044",
    //                 "account_number" => "0690000034",
    //                 "amount" => 420000,
    //                 "currency" => "NGN",
    //                 "narration" => "Salary payment for April",
    //             ]
    //         ]
    //     ];
    //     $response = $transferService->bulkTransfer($details);
    // }
}
