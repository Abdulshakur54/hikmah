<?php
    //initializations
      spl_autoload_register(
              function($class){
                      require_once'../../../../classes/'.$class.'.php';
              }
      );
      session_start(Config::get('session/options'));
      //end of initializatons
     header("Content-Type: application/json; charset=UTF-8");
     if(Input::submitted() && Token::check(Input::get('token'))){
         $id = Input::get('id');
         $requester_id = Input::get('requester_id');
         $category = (int)Input::get('category');
         $confirm = Input::get('confirm');
         $req = new Request();
         if($confirm === 'true'){ //request accepted
             $req->requestConfirm($id, $requester_id, $category);
             echo json_encode(['success'=>true,'token'=>Token::generate(),'confirm'=>true]);
         }else{
             $req->requestDecline($id, $requester_id, $category);
             echo json_encode(['success'=>true,'token'=>Token::generate(),'confirm'=>false]);
         }
        
     }

