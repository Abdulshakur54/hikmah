<?php

/* You are expected to have required PHPMailer in your php script before using this class*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Email{
    
    private $_mail, $_url,$_name, $_error=[];
    
    public function __construct() {
        $this->_mail = new PHPMailer(true);
        $this->_name = Config::get('webmail/name');
        $this->_url = new Url();
        $this->afterConstruct();
    }
    
    //this method helps complete the initializations when a new Email Object is istantiated
    private function afterConstruct(){
        
        /* Tells PHPMailer to use SMTP. */
           $this->_mail->isSMTP();
          //$this->_mail->SMTPDebug = 2;
        /* SMTP server address. */
        $this->_mail->Host = 'localhost';

        /* Use SMTP authentication. */
        $this->_mail->SMTPAuth = TRUE;

        /* Set the encryption system. */
        //$this->_mail->SMTPSecure = 'tls';

        /* SMTP authentication username. */
        $this->_mail->Username = Config::get('webmail/username');

        /* SMTP authentication password. */
        $this->_mail->Password = Config::get('webmail/password');

        /* Set the SMTP port. */
        $this->_mail->Port = 25;
        
        /*make it a html message*/
        $this->_mail->isHTML(TRUE);
        
        $this->_mail->setFrom(Config::get('webmail/username'),$this->_name);
       
    }
    
    
    //this method sends the emails using the given parameter to individuals if $to is a string, to group of individuals if $to is an array
    public function send($to,$subject,$body,$options=[]) :bool{
        if(count($options)){
            $this->setOptions($options);
        }
        if(is_array($to)){
            foreach ($to as $recipient){
                $this->_mail->addAddress($recipient, $this->_name);
            }
        }else{
            $this->_mail->addAddress($to, $this->_name);
        }
        $this->_mail->Subject = $subject;
        $this->_mail->Body = $this->emailMessageFormat($subject,$body);
        return $this->sendEmail();
    }
    
    private function emailMessageFormat($title,$body) :string{
        return '
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <style>
                        *{
                            box-sizing: border-box;
                            margin: 0px;
                            padding: 0px;
                            border: 0px;
                        }
                        #school_logo{
                            width:100%;
                            height: auto;
                        }
                        #logoContainer{
                            width: 40%;
                            margin: 0px auto;
                            min-width: 300px;
                            max-width: 500px;
                        }

                        body {
                            font-size: 100%;
                        }
                        header,main{
                            width: 90%;
                            margin: 0px auto;
                        }
                        main{
                            padding: 5px;
                        }
                        h2{
                            text-align: center;
                            margin-top: 5px;
                        }

                        h1 {
                            font-size: 2.5em;
                        }

                        h2 {
                            font-size: 1.75em;
                        }

                        p {
                            font-size: 0.875em;
                        }
                    </style>
                </head>
                <body>
                    <header>
                       <div id="logoContainer">
                            <img src="'.Utility::escape($this->_url->to('images/hkm_logo.jpg')).'" id="school_logo"/>
                       </div>  
                    </header>
                    <main>
                        <h2>'.$title.'</h2>
                        <div id="content">'.$body.'</div>
                    </main>
                    <footer>

                    </footer>
                </body>
            </html>

            ';
    }
    
    
    //this is used for a generic sms where each recipient have a customized message, all the parameters are expected to have and array value
    public function sendGeneric($to,$subject,$body,$options=[]) :bool{
        if(count($options)){
            $this->setOptions($options);
        }
        if(is_array($to)){
            foreach ($to as $recipient){
                $this->_mail->addAddress($recipient, $this->_name);
            }
        }else{
            $this->_mail->addAddress($to, $this->_name);
        }
        $this->_mail->Subject = $subject;
        $this->_mail->Body = $body;
        return $this->sendEmail();
    }
    
  
    
    //this method helps set the options for any email
    private function setOptions($options){
        foreach ($options as $option=>$optionVal){
                switch ($option){
                    case 'fromName':
                        $this->_mail->setFrom(Config::get('webmail/username'),$optionVal);
                        break;
                    case 'attachment': //to use this, the $optionVal should be a string of the file path and intended name(name of the file at the receivers end) delimited by a comma
                        if(is_array($optionVal)){ //this will return true if a generic sms with a generic attachment is to be sent, $optionVal is expected to be an araay of strings, each string is delimeted by a comma to seperate the file path from the name
                            foreach ($optionVal as $val){
                               addAttachment($val); 
                            }
                        }else{
                            addAttachment($optionVal);
                        }
                        
                        break;
                    case 'altBody':
                        $this->_mail->AltBody = $optionVal;
                }
            }
    }
    
    
    
    //this method helps add attachment to the emails
    private function addAttachment(string $pathAndName){
        $data = explode(',', $pathAndName);
        $counter = count($data);
        if($counter === 1){
            $this->_mail->addAttachment($data[0]);
        }else{
            if($counter === 2){
                $this->_mail->addAttachment($data[0],$data[1]);
            }
        }
    }
    
    
    private function sendEmail() :bool{
        try{
            $this->_mail->send();
            return true;
        } catch (Exception $e){
            $this->_error[] = $e->errorMessage();
        } catch (\Exception $e){
            $this->_error[] = $e->getMessage();
        }
        return false;
    }
    
    public function getErrors(){
        return $this->_error;
    }

}