<?php
spl_autoload_register(
    function($class){
        require_once'../classes/'.$class.'.php';
    }
);
//Payment::accept(5000,'mabdulshakur54@gmail.com','Abdulshakur','08106413226');
//Payment::get_banks();
require_once '../libraries/vendor/autoload.php';
$flw = new \Flutterwave\Rave(getenv('FLWSECK_TEST-65de94da82ba58636eb09e4810a6cdf1-X')); // Set `PUBLIC_KEY` as an environment variable
$transferService = new \Flutterwave\Transfer();
$details = [
    "account_bank" => "044",
    "account_number" => "0690000040",
    "amount" => 200,
    "narration" => "Payment for things",
    "currency" => "NGN",
    "reference" => Token::create(6),
];
$response = $transferService->singleTransfer($details);