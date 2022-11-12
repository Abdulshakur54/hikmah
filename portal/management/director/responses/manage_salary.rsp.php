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
        $receiver = Utility::escape(Input::get('receiver'));
        $salary = Utility::escape(Input::get('salary'));
        $category = Utility::escape(Input::get('category'));
        $name = Utility::escape(Input::get('name'));
        $id = (int)Input::get('id');
        $account = new Account();
        $req = new Request();
        if($category === 'updatesalary'){
            //delete the request if it exists before
            $req->delRequest($receiver, 1);// 1 as a parameter here shows the category is salary
            $account->updateSalary($receiver,$salary); //update salary
            //send a confirmation request to the accountant
            $req->send($receiver, 3,'Please, confirm a request of &#8358;'.$salary.' as salary for '.$name, RequestCategory::SALARY_CONFIRMATION);
            echo json_encode(['success'=>true,'token'=>Token::generate(),'id'=>$id,'category'=>$category,'receiver'=>$receiver]);
         }else{
   
            //send a confirmation request to the accountant
            if($status = $req->send($receiver, 3,'Please, confirm a request of &#8358;'.$salary.' as salary for '.$name, RequestCategory::SALARY_CONFIRMATION)){
                if($status === 1){
                    echo json_encode(['success'=>true,'token'=>Token::generate(),'id'=>$id, 'category'=>$category, 'code'=>1]); //this means that a request has already been sent
                }else{
                    echo json_encode(['success'=>true,'token'=>Token::generate(),'id'=>$id, 'category'=>$category, 'code'=>0]); //this means that the request have not been sent before
                }
            }   
         }

     }