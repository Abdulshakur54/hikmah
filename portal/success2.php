<?php
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
//end of initializatons

if (Input::submitted('get')) {
    $user_type = Utility::escape(Input::get('user_type'));
}
?>
<!doctype html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
</head>
<style>
    body {
        text-align: center;
        padding: 40px 0;
        background: #EBF0F5;
    }

    h1 {
        color: #88B04B;
        font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
        font-weight: 900;
        font-size: 40px;
        margin-bottom: 10px;
    }

    p {
        color: #404F5E;
        font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
        font-size: 25px;
        margin: 0;
    }

    i {
        color: #9ABC66;
        font-size: 100px;
        line-height: 200px;
        margin-left: -15px;
    }

    .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 0 auto;
    }
</style>

<body>
    <div class="card">
        <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
            <i class="checkmark">✓</i>
        </div>
        <h1>Success</h1>
        <p>
            <?php
            if (Session::exists('new_user')) {
                echo '<div class="success">' . Session::get_flash('new_user') . '</div>';
                echo 'Login <a href="login.php">Here</a>';
            }
            if (Input::get('recoverPassword') == 'true') {
                echo '<div class="success">' . Session::get_flash('recoverPassword') . '</div>';
                exit(); //this page is exited here so as to stop script execution
            }

            if (Input::get('resetSuccess') == 'true') {
                echo '<div class="success">' . Session::get_flash('resetSuccess') . '</div>';
                echo '<div><a href="login.php">Click to Login</a></div>';
                exit(); //this page is exited here so as to stop script execution
            }
            ?>
        </p>
        <input type="hidden" name="user_type" id="userType" value="<?php echo $user_type ?>" />
    </div>
    <script>
        localStorage.setItem('user_type', document.getElementById('userType').value);
    </script>
</body>

</html>