<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
    require_once './hrm.inc.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Add Token</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('ld_loader/ld_loader.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/add_token.css" />
</head>
<body>
    <main>
        <?php 
            require_once '../nav.inc.php';
            //echo welcome flash message
            if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$hrm->getPosition($rank).'</div>';
                Session::delete('welcome');
                if(Session::exists('welcome back')){
                    Session::delete('welcome back');
                }
            }else{
                if(Session::exists('welcome back')){
                    echo '<div class="message">Welcome '.$hrm->getPosition($rank).'</div>';
                    Session::delete('welcome back');
                }
            }
        ?>
        <form method="post" action = "<?php echo Utility::myself() ?>" onsubmit="return addToken();">
            <div class="formhead">Generate Staffs Tokens</div>
            <div>
                <label for="name">Name</label>
                <div>
                    <input type="text" name="name" id="name" />
                    <div id="nameMsg" class="failure"></div>
                </div>
            </div>
            <div>
                <label for="position">Position</label>
                <div>
                    <select name="position" id="position">
                        <option value="7">Teacher</option>
                        <option value="8">Non Teaching Staff</option>
                    </select>
                </div>
            </div>
            <div>
                 <label for="school">School</label>
                 <div>
                    <select name="school" id="school">
                        <?php 
                        $schools = School::getConvectionalSchools();
                        $genHtml = '';
                        foreach($schools as $sch=>$sch_abbr){
                            $genHtml.='<option value="'.$sch_abbr.'">'.$sch.'</option>';
                        }
                        echo $genHtml;
                        ?>
                    </select>
                 </div>
            </div>
            <div>
                <label for="salary">Salary(&#8358;)</label>
                <div>
                    <input type="text" name="salary" id="salary" />
                    <div id="salaryMsg"  class="failure"></div>
                </div>
            </div>
            <div>
                <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
                <div id="genMsg"></div>
                <input type="submit" value="Generate Pin" id="generatePin"/><span id="ld_loader"></span>
            </div>
        </form>
        <script>
            window.addEventListener('load',function(){
                appendScript('<?php echo Utility::escape($url->to('ld_loader/ld_loader.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('scripts/portalscript.js',0))?>'); 
                appendScript('<?php echo Utility::escape($url->to('scripts/ajaxrequest.js',0))?>');  
                appendScript('<?php echo Utility::escape($url->to('scripts/validation.js',0))?>');
                appendScript('scripts/add_token.js');  
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