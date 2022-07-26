<?php
    //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    require_once './nav1.inc.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Requests</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('ld_loader/ld_loader.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',0))?>" />
    <link rel="stylesheet" type="text/css" href="styles/requests.css" />
</head>
<body>
    <main>
        <?php 
            require_once '../nav.inc.php';
        ?>
        <?php
            $requests2 = $req2->getMyRequests($username);
            if(!empty($requests2)){
                foreach ($requests2 as $rqst){
                    echo'<div id="row'.$rqst->id.'">
                            <div class="title">'.$rqst->title.'</div>
                            <div class="message">'.$rqst->request.'</div>
                            <div class="decision"><button class="acceptBtn" onclick="accept('.$rqst->id.',\''.$rqst->requester_id.'\','.$rqst->category.')">Accept</button><span id="ld_loader_'.$id.'"></span><button class="declineBtn" onclick="decline('.$rqst->id.',\''.$rqst->requester_id.'\','.$rqst->category.')">Decline</button></div>
                            <div class="hidden" id="other">'.json_encode($rqst->other).'</div>
                         </div>';
                }
            }else{
                echo '<div class="message">No request available</div>';
            }
            
        ?>
        <input type = "hidden" value = "<?php echo Token::generate() ?>" name="token" id="token" />
        <div id="genMsg"></div>
        <script>
            window.addEventListener('load',function(){
                appendScript('<?php echo Utility::escape($url->to('ld_loader/ld_loader.js',0))?>');
                appendScript('<?php echo Utility::escape($url->to('scripts/script.js',0))?>'); 
                appendScript('<?php echo Utility::escape($url->to('scripts/ajaxrequest.js',0))?>');  
                appendScript('scripts/requests.js');  
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