<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
   require_once './director.inc.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Manage Management Member Salaries</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/manage_salary.css" />
</head>
<body>
    <main>
        <?php 
            require_once '../nav.inc.php';
            //echo welcome flash message
            if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$dir->getPosition($rank).'</div>';
                Session::delete('welcome');
                if(Session::exists('welcome back')){
                    Session::delete('welcome back');
                }
            }else{
                if(Session::exists('welcome back')){
                    echo '<div class="message">Welcome '.$dir->getPosition($rank).'</div>';
                    Session::delete('welcome back');
                }
            }
        ?>
        
        <?php
            if(Input::submitted() && Token::check(Input::get('token'))){ //if submitted via post and token is valid
                $ids = Input::get('hiddenIds');
                $names = Input::get('hiddenNames');
                if(Utility::noScript($ids)){
                    $validIds = json_decode($ids,true);
                    $validNames = json_decode($names,true);
                    $count = count($validIds);
                }else{
                    exit();
                }
                $account = new Account();
                $req = new Request(true);
                
                if(isset($_POST['updateAll'])){
                    //delete the request if it exists before
                    $req->delRequest($validIds, 1);// 1 as a parameter here shows the category is salary
                    $account->updateSalary($validIds); //update salary
                    
                     //send a confirmation request to the accountant for each id
                    foreach($validIds as $id=> $sal){
                         $req->send($id, 3,'Please, confirm a request of &#8358;'.$sal.' as salary for '.$validNames[$id], 1,true);
                    }
                   Session::set_flash('accountsUpdated', '<div class="success">'.$count.' account(s) have been successfully updated, Confirmation request has been sent to the accountant</div>');
                   
                }
                if(isset($_POST['requestAll'])){
                    //send a confirmation request to the accountant
                    foreach($validIds as $id=> $sal){
                        $req->send($id, 3,'Please, confirm a request of &#8358;'.$sal.' as salary for '.$validNames[$id], 1,true); 
                    }
                     Session::set_flash('accountsApproval', '<div class="success">Confirmation request has been sent for all the required accounts</div>');
                }
            }
            echo Session::get_flash('accountsUpdated');
            echo Session::get_flash('accountsApproval');
            $account = new Account();
            $accounts = $account->getSalariesDetails(1);
            if(!empty($accounts)){?>
            <form method="post"  action="<?php echo Utility::myself() ?>" onsubmit="return fillIds();">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Salary</th><th>Action</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($accounts as $val){
                            if($val->approved){
                                echo '<tr id="row'.$val->id.'"><td>'.$val->receiver.'</td><td id="name'.$val->id.'">'.Utility::escape(Utility::formatName($val->fname, $val->oname, $val->lname)).
                                 '</td><td><input type="text" id="salary'.$val->id.'" value="'.Utility::escape($val->salary).'" onfocus="setvalidUserIds(\''.$val->receiver.'\','.$val->id.',this)" onblur="validateSalary(\''.$val->receiver.'\','.$val->id.',this)" /></td><td><button class="actioncolumn" onclick="updateSalary(\''.$val->receiver.'\','.$val->id.')">update</button></td><td id="approval'.$val->id.'">approved</td><tr>';
                            }else{
                                echo '<tr id="row'.$val->id.'"><td>'.$val->receiver.'</td><td id="name'.$val->id.'">'.Utility::escape(Utility::formatName($val->fname, $val->oname, $val->lname)).
                                 '</td><td><input type="text" id="salary'.$val->id.'" value="'.Utility::escape($val->salary).'" onfocus="setvalidUserIds(\''.$val->receiver.'\','.$val->id.',this)" onblur="validateSalary(\''.$val->receiver.'\','.$val->id.',this)" /></td><td><button class="actioncolumn" onclick="updateSalary(\''.$val->receiver.'\','.$val->id.')">update</button></td><td id="approval'.$val->id.'"><button class="actioncolumn" onclick="approveSalary(\''.$val->receiver.'\','.$val->id.')">request approval</button></td><tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
                 <div>
                    <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
                    <input type = "hidden"  name="hiddenIds" id="hiddenIds"/> <!--stores the user_ids that needs to be updated or approved when the form is submitted via post -->
                     <input type = "hidden"  name="hiddenNames" id="hiddenNames"/> <!--similar to user_ids but this stores their respective names -->
                    <button id="updateAllBtn"  name="updateAll">Update All</button><button id="requestAllBtn"  name="requestAll">Request All</button>
                </div>
            </form>
            <?php
            }else{
                echo '<div class="message">No accounts available</div>';
            }
            
        ?>

        <script>
            window.addEventListener('load',function(){
                appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('scripts/portalscript.js',0))?>'); 
                appendScript('<?php echo Utility::escape($url->to('scripts/ajaxrequest.js',0))?>');  
                appendScript('<?php echo Utility::escape($url->to('scripts/validation.js',0))?>');
                appendScript('scripts/manage_salary.js');  
            });
            function appendScript(source){
                let script = document.createElement('script');
                script.src=source;
                document.body.appendChild(script);
            }
        </script>
    </main>
</body>
</html>
