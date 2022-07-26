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
         $name = Utility::escape(Input::get('name'));
         $name = strtolower($name);
         $preRank = (int)Input::get('rank');
         if($preRank > 20){
             $rank = Management::getBossRank($preRank); //gets the bosss rank
         }else{
             $rank = $preRank;
         }
         $level = Utility::escape(Input::get('level'));
         $sch_abbr = Utility::escape(Input::get('sch_abbr'));
         $db = DB::get_instance();
         $run = true;
         
         $db->query('select token from token where owner =? and pro_rank = ?',[$name,$rank]);
         if($db->row_count() > 0){
             echo json_encode(['success'=>false,'token'=>Token::generate(),'message'=>'Pin already generated']);
         }else{
            while($run){
                $createdToken = strtoupper(Token::create(7));
                $db->query('select token from token where token=?',[$createdToken]);
                if($db->row_count() === 0){
                    //add token to table
                    $db->query('insert into token (token,owner,sch_abbr,level,pro_rank,added_by) values(?,?,?,?,?,?)',[$createdToken,$name,$sch_abbr,$level,$rank,2]); //the 2 at the end shows it is added by the APM
                    $run = false;
                }
            }
            //a confirmation request should be sent to the accountant on salary
            echo json_encode(['success'=>true,'token'=>Token::generate(),'createdToken'=>$createdToken]);
         }
         
        
     }