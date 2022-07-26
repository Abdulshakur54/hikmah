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
         $id = Utility::escape(Input::get('id'));
         $db = DB::get_instance();
         if($db->query('delete from token where id=?',[$id])){
             echo json_encode(['success'=>true,'token'=>Token::generate(),'id'=>$id]);
         }else{
              echo json_encode(['success'=>false,'token'=>Token::generate(),'id'=>$id]);
         }
         
     }