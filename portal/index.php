<?php 
	//initializations
	spl_autoload_register(
		function($class){
			require_once'../classes/'.$class.'.php';
		}
	);
	session_start(Config::get('session/options'));
	//end of initializations
	$url = new Url();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Portal</title>
	<meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="stylesheet" type="text/css" href="index.css" />

</head>
<body>
	<main id="wrapper">
		<h2>Portal Login</h2>
		<a href="<?php echo $url->to('student/login.php',0) ?>">Student Login</a>
		<a href="<?php echo $url->to('staff/login.php',0) ?>">Staff Login</a>
		<a href="<?php echo $url->to('management/login.php',0) ?>">Management Login</a>
		<a href="<?php echo $url->to('admission/login.php',0) ?>">Admission Login</a>
	</main>
</body>
</html>