<?php
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
//end of initializations
$adm = new Admission();
$url = new Url();
$msg = '';
$submittedViaPost = 'false'; //this will help in the javascript to know if the page has been submitted before
if (Input::submitted() && Token::check(Input::get('token'))) {
    $submittedViaPost = 'true';
    $val  = new Validation(true);
    $formvalues = array(
        'fname' => array('name' => 'First Name', 'required' => true, 'min' => 3, 'max' => '20', 'pattern' => '^[a-zA-Z`]+$'),
        'lname' => array('name' => 'Last Name', 'required' => true, 'min' => 3, 'max' => '20', 'pattern' => '^[a-zA-Z`]+$'),
        'oname' => array('name' => 'Other Name', 'min' => 3, 'max' => '20', 'pattern' => '^[a-zA-Z`]+$'),
        'password' => array('name' => 'password', 'required' => true, 'min' => 3, 'max' => '32', 'pattern' => '^[a-zA-Z0-9]+$'),
        'c_password' => array('name' => 'Confirm_Password', 'same' => 'password'),
        'phone' => array('name' => 'Phone', 'required' => true, 'pattern' => '^(080|070|090|081|091|071)[0-9]{8}$'),
        'pin' => array('name' => 'Pin', 'required' => true, 'pattern' => '^[0-9a-zA-Z]{14}$'),
        'email' => array('name' => 'Email', 'required' => true, 'max' => 70, 'min' => 10, 'pattern' => '^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$'),
        'dob' => array('name' => 'Date of Birth', 'required' => true, 'required' => true)
    );

    if ($val->check($formvalues)) {
        $agg = new Aggregate();
        $pin = Utility::escape(Input::get('pin'));
        if (Utility::equals($pin, $agg->lookUp('token', 'token', 'token,=,' . $pin))) { //confirm the token
            $adm_id = 'A' . Admission::genId(); //generates the management id
            $password = Utility::escape(Input::get('password'));
            $fname = Utility::escape(Input::get('fname'));
            $lname = Utility::escape(Input::get('lname'));
            $oname = Utility::escape(Input::get('oname'));
            $dob = Utility::escape(Input::get('dob'));
            $db = DB::get_instance();
            $db->query('select * from token where token=?', [$pin]); //get the token and the data associated with it
            $res = $db->one_result();
            $rank = $res->pro_rank;
            $sch_abbr = $res->sch_abbr;
            $level = (int) $res->level;
            $email = Utility::escape(Input::get('email'));
            $permission = Permission::getDefaultPermissions($rank);
            $sql1 = 'insert into ' . Config::get('users/table_name3') . '(' . Config::get('users/username_column3') . ', ' . Config::get('users/password_column') . ',fname,lname,oname,rank,sch_abbr,phone,email,permission,level,dob) values(?,?,?,?,?,?,?,?,?,?,?,?)';
            $vals1 = [$adm_id, password_hash($password, PASSWORD_DEFAULT), $fname, $lname, $oname, $rank, $sch_abbr, Utility::escape(Input::get('phone')), $email, $permission, $level, $dob]; //null values should be changed for permission and email


            if ($db->query($sql1, $vals1)) { //performs a query
                $agg->rowDelete('token', 'token,=,' . $pin); //delete the token from the token table
                $alert = new Alert(true);

                Session::set_flash('new_user', '<div>Thanks for Registering. You can now Login to your account</div><div>Your Username is <strong>' . $adm_id . '</strong><br><em>Copy and save it</em></div>');
                Redirect::to('success2.php');
            } else {
                $msg .= 'Registration Not Successful';
            }
        } else {
            $msg .= 'Pin did not match';
        }
    } else {
        foreach ($val->errors() as $val) {
            $msg .= $val . '<br>';
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="UTF-8">
    <title>Register Page</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/style.css', 0)) ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Utility::escape($url->to('styles/register.css', 1)) ?>" />
</head>

<body>
    <form method="POST" action="<?php echo Utility::myself(); ?>" autocomplete="off" onsubmit="return submitForm();">
        <?php
        if (!empty($msg)) {
            echo '<div class=failure>' . $msg . '</div>';
        }
        ?>
        <div class="formhead">Register With Us</div>
        <div id="indication">required<span class="required">*</span> optional<span class="optional">*</span></div>
        <div>
            <label for="fname">First Name</label>
            <div>
                <div><input type="text" maxlength="20" name="fname" value="<?php echo Utility::escape(Input::get('fname')) ?>" id="fname"><span class="required">*</span></div>
                <div id="fNameMsg" class="errMsg"></div>
            </div>
        </div>
        <div>
            <label for="lname">Last Name</label>
            <div>
                <div><input type="text" maxlength="20" name="lname" value="<?php echo Utility::escape(Input::get('lname')) ?>" id="lname"><span class="required">*</span></div>
                <div id="lNameMsg" class="errMsg"></div>
            </div>
        </div>
        <div>
            <label for="dob">Date Of Birth</label>
            <div>
                <div><input type="date" name="dob" id="dob" value="<?php echo Utility::escape(Input::get('dob')) ?>" /><span class="required">*</span></div>
            </div>
        </div>

        <div>
            <label for="oname">Other Name</label>
            <div>
                <div><input type="text" maxlength="20" name="oname" value="<?php echo Utility::escape(Input::get('oname')) ?>" id="oname"><span class="optional">*</span></div>
                <div id="oNameMsg" class="errMsg"></div>
            </div>
        </div>

        <div>
            <label for="password">Password</label>
            <div>
                <div><input type="password" maxlength="35" name="password" id="password"><span class="required">*</span></div>
                <div id="pwdMsg" class="errMsg"></div>
            </div>
        </div>

        <div>
            <label for="c_password">Confirm Password</label>
            <div>
                <div><input type="password" maxlength="35" name="c_password" id="c_password"><span class="required">*</span></div>
                <div id="conPwdMsg" class="errMsg"></div>
            </div>
        </div>

        <div>
            <label for="phone">Phone No(Parent)</label>
            <div>
                <div><input type="text" maxlength="11" name="phone" value="<?php echo Utility::escape(Input::get('phone')) ?>" id="phone"><span class="required">*</span></div>
                <div id="phoneMsg" class="errMsg"></div>
            </div>
        </div>

        <div>
            <label for="email">Email(Parent)</label>
            <div>
                <div><input type="email" maxlength="70" name="email" value="<?php echo Utility::escape(Input::get('email')) ?>" id="email"><span class="required">*</span></div>
                <div id="emailMsg" class="errMsg"></div>
            </div>
        </div>


        <div>
            <label>Pin</label>
            <div>
                <div><input type="text" name="pin" id="pin" value="<?php echo Utility::escape(Input::get('pin')) ?>" /><span class="required">*</span></div>
                <div id="pinMsg" class="errMsg"></div>
            </div>
        </div>
        <div id="genMsg" class="failure"></div>
        <div>
            <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
            <input type="hidden" value="<?php echo $submittedViaPost; ?>" id="scriptValid" />
            <input type="submit" value="Register" id="regBtn" />
        </div>
    </form>
    <script>
        window.onload = function() {
            appendScript('<?php echo Utility::escape($url->to('scripts/script.js', 0)) ?>');
            appendScript('<?php echo Utility::escape($url->to('scripts/validation.js', 0)) ?>');
            appendScript("scripts/register.js");
        }

        function appendScript(source) {
            let script = document.createElement("script");
            script.src = source;
            document.body.appendChild(script);
        }
    </script>
</body>

</html>