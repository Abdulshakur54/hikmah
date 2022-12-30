<?php
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
include_once('../../../../libraries/vendor/autoload.php');
//end of initializatons
header("Content-Type: application/json; charset=UTF-8");

if (Input::submitted() && Token::check(Input::get('token'))) {
    $id = Input::get('id');
    $requester_id = Input::get('requester_id');
    $category = (int)Input::get('category');
    $confirm = Input::get('confirm');
    $req = new Request();
    $confirm = ($confirm == 'true')?true:false;
    if ($confirm) { //request accepted
        $rsp = $req->requestConfirm($id, $requester_id, $category);
    } else {
        $rsp = $req->requestDecline($id, $requester_id, $category);
    }
    if (!empty($rsp)) { //there is an internal response
        echo $rsp;
    } else {
        echo json_encode(['success' => true, 'token' => Token::generate(), 'confirm' => $confirm]);
    }
} 
