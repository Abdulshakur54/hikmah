<?php
        //initializations
	spl_autoload_register(
		function($class){
			require_once'../../../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
	//end of initializatons
    require_once './apm.inc.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>View Generated Pins</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/view_token.css" />
</head>
<body>
    <main>
        <?php 
            require_once '../nav.inc.php';
            //echo welcome flash message
             if(Session::exists('welcome')){
                echo '<div class="message">Good '.ucfirst(Utility::getPeriod()).', '.$apm->getPosition($rank).'</div>';
                Session::delete('welcome');
                if(Session::exists('welcome back')){
                    Session::delete('welcome back');
                }
            }else{
                if(Session::exists('welcome back')){
                    echo '<div class="message">Welcome '.$apm->getPosition($rank).'</div>';
                    Session::delete('welcome back');
                }
            }
        ?>
        <?php
            $db = DB::get_instance();
            $db->query('select id,owner,token,level,sch_abbr from token where added_by = ? order by owner asc',[$rank]);
            if($db->row_count() > 0){?>
            <table>
                <thead>
                    <tr><th>Name</th><th>Pin</th><th>School</th><th>level</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $res=$db->get_result();
                    foreach($res as $val){
                        echo '<tr id="row'.$val->id.'"><td>'.Utility::escape(ucwords($val->owner)).'</td><td>'.Utility::escape($val->token).
                             '</td><td>'.$val->sch_abbr.'</td><td>'.$val->level.'</td><td class="deleteData" onclick="deleteToken('.$val->id.')">Delete</td><tr>';
                    }
                    ?>
                </tbody>
            </table> <?php
            }else{
                echo '<div class="message">No pin available</div>';
            }
        ?>
        <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
        <script>
            window.addEventListener('load',function(){
                appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('scripts/ajaxrequest.js',0))?>');  
                appendScript('scripts/view_token.js');  
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