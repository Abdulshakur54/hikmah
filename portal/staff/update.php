<?php
     //initializations
    spl_autoload_register(
            function($class){
                    require_once'../../classes/'.$class.'.php';
            }
    );
    session_start(Config::get('session/options'));
    //end of initializatons
	require_once './nav1.inc.php';
    
	if(Input::submitted() && Token::check(Input::get('token'))){
		
		$val = new Validation(true);
		$values = array(
			'fname' => array('name'=>'First Name', 'required'=>true, 'min'=>3, 'max'=>'20', 'pattern' => '^[a-zA-Z`]+$'),
			'lname' => array('name'=>'Last Name', 'required'=>true, 'min'=>3, 'max'=>'20', 'pattern' => '^[a-zA-Z`]+$'),
			'oname' => array('name'=>'Other Name', 'min'=>3, 'max'=>'20', 'pattern' => '^[a-zA-Z`]+$')
		);

		if($val->check($values)){
			if(!$staff->update(['fname'=>Input::get('fname'), 'lname'=>Input::get('lname'), 'oname'=>Input::get('oname')])){
				echo 'update failed';
			}{
				Session::set_flash('update','<span class="success">Your Details have been Successfully Updated<br></span>');
				Redirect::home('index.php',2);
			}
		}else{
			foreach($val->errors() as $err){
				echo $err.'<br>';
			}
		}


	}
?>
<!DOCTYPE html>
<html lang = "en">
<head>
	<meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Update Your Details</title>
	<link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css',2))?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/update.css',2))?>" />
</head>
<body>
	<?php
		require_once './nav.inc.php';
	
		if(Session::exists('welcome')){
			echo 'Good '.ucfirst(Utility::getPeriod()).', '.$staff->getPosition($rank);
			Session::delete('welcome');
		}
	?>

	
	<form method = "POST" action = "<?php echo Utility::myself() ?>"  onsubmit="return submitForm();">
		<div>
			<label for = "name">First Name</label>
			<input type = "text" maxlength="20" name = "fname" value = "<?php echo Utility::escape($data->fname)?>" id = "fname">
			<div id="firstNameMsg" class="leftDiv"></div>
		</div>
		<div>
			<label for = "name">Last Name</label>
			<input type = "text" maxlength="20" name = "lname" value = "<?php echo Utility::escape($data->lname)?>" id = "lname">
			<div id="lastNameMsg" class="leftDiv"></div>
		</div>
		<div>
			<label for = "name">Other Name</label>
			<input type = "text" maxlength="20" name = "oname" value = "<?php echo Utility::escape($data->oname)?>" id = "oname">
			<div id="otherNameMsg" class="leftDiv"></div>
		</div>
		<input type = "hidden" value = "<?php echo Token::generate() ?>" name = "token" />
		<input type = "submit" value = "Update" id="updBtn" />
	</form>
	<script>
        window.onload = function(){
            appendScript("scripts/script.js");
			appendScript("scripts/validation.js");
			appendScript("scripts/update.js");
        }

        function appendScript(source){
            let script = document.createElement("script");
            script.src=source;
            document.body.appendChild(script);
        }
    </script>
</body>
</html>