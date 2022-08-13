<?php
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
//end of initializations
$mgt = new Management();
$url = new Url();
$msg = '';

if (Input::submitted() && Token::check(Input::get('token'))) {
    $submittedViaPost = 'true';
    $val  = new Validation(true);
    $formvalues = array(
        'fname' => array('name' => 'First Name', 'required' => true, 'min' => 3, 'max' => '20', 'pattern' => '^[a-zA-Z`]+$'),
        'lname' => array('name' => 'Last Name', 'required' => true, 'min' => 3, 'max' => '20', 'pattern' => '^[a-zA-Z`]+$'),
        'oname' => array('name' => 'Other Name', 'min' => 3, 'max' => '20', 'pattern' => '^[a-zA-Z`]+$'),
        'password' => array('name' => 'password', 'min' => 3, 'max' => '32', 'pattern' => '^[a-zA-Z0-9]+$'),
        'c_password' => array('name' => 'Confirm_Password', 'same' => 'password'),
        'phone' => array('name' => 'Phone', 'pattern' => '^(080|070|090|081|091|071)[0-9]{8}$'),
        'email' => array('name' => 'Email', 'required' => false, 'max' => 70, 'min' => 10, 'pattern' => '^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$'),
        'pin' => array('name' => 'Pin', 'pattern' => '^[0-9a-zA-Z]{14}$'),
        'account' => array('name' => 'Account No', 'required' => true, 'max' => 15, 'min' => 10, 'pattern' => '^[0-9]+$'),
        'bank' => array('name' => 'Bank', 'in' => Utility::getBanks())
    );

    $fileValues = [
        'picture' => [
            'name' => 'Picture',
            'required' => true,
            'maxSize' => 100,
            'extension' => ['jpg', 'jpeg', 'png']
        ]
    ];

    $file = new File('picture');
    if (!$file->isUploaded()) {
        $msg .= 'Picture not uploaded';
    }

    if ($val->check($formvalues) && $val->checkFile($fileValues) && Utility::noScript(Input::get('address'))) {
        $agg = new Aggregate();
        $pin = Input::get('pin');
        if (Utility::equals($pin, $agg->lookUp('token', 'token', 'token,=,' . $pin))) { //confirm the token
            $mgt_id = 'M' . Management::genId(); //generates the management id
            $password = Utility::escape(Input::get('password'));
            $fname = Utility::escape(Input::get('fname'));
            $lname = Utility::escape(Input::get('lname'));
            $oname = Utility::escape(Input::get('oname'));
            $db = DB::get_instance();
            $db->query('select salary,sch_abbr,pro_rank,asst from token where token=?', [$pin]); //get the token and the data associated with it
            $res = $db->one_result();
            $rank = $res->pro_rank;
            $salary = $res->salary;
            $sch_abbr = $res->sch_abbr;
            $asst = $res->asst;
            $email = Utility::escape(Input::get('email'));
            $address = Input::get('address');
            $ext = $file->extension();
            $pictureName = $mgt_id . '.' . $ext;
            $permission = Permission::getDefaultPermissions($rank);
            $sql1 = 'insert into ' . Config::get('users/table_name0') . '(' . Config::get('users/username_column0') . ', ' . Config::get('users/password_column') . ',fname,lname,oname,rank,sch_abbr,asst,phone,email,permission,address,picture) values(?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $vals1 = [$mgt_id, password_hash($password, PASSWORD_DEFAULT), $fname, $lname, $oname, $rank, $sch_abbr, $asst, Utility::escape(Input::get('phone')), $email, $permission, $address, $pictureName]; //null values should be changed for permission and email
            $sql2 = 'insert into account(receiver,no,bank,salary) values(?,?,?,?)';
            $vals2 = [$mgt_id, Utility::escape(Input::get('account')), Utility::escape(Input::get('bank')), $salary];

            if ($db->trans_query([[$sql1, $vals1], [$sql2, $vals2]])) { //performs a transaction which consist of 2 queries
                $agg->rowDelete('token', 'token,=,' . $pin); //delete the token from the token table
                $file->move('uploads/passports/' . $pictureName); //move picture to the destination folder
                $alert = new Alert(true);
                $req = new Request();
                //send a confirmation request to the accountant
                $req->send($mgt_id, 3, 'Please, confirm a request of &#8358;' . $salary . ' as salary for ' . Utility::formatName($fname, $oname, $lname), 1);
                //notify the director(s)
                $alert->sendToRank(1, 'Registration Completion', 'This is to inform you that ' . Utility::formatName($fname, $oname, $lname) . ' has successfully registered as ' . formatPositionMsg($rank) . '.<br>A request has been sent to the accountant to confirm his salary of &#8358;' . $salary);
                Session::set_flash('new_user', '<div>Thanks for Registering. You can now Login to your account</div><div>Your Username is <strong>' . $mgt_id . '</strong><br><em>Copy and save it</em></div>');
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

//function to be used
function formatPositionMsg($rank)
{
    global $mgt;
    switch ($rank) {
        case 2:
            return 'an Academic Planning Manager';
        case 3:
            return 'an Accountant';
        default:
            return 'a ' . $mgt->getFullPosition($rank);
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
    <form method="POST" action="<?php echo Utility::myself(); ?>" autocomplete="off" onsubmit="return submitForm();" enctype="multipart/form-data">
        <?php
        if (!empty($msg)) {
            echo '<div class=failure>' . $msg . '</div>';
        }
        ?>
        <div class="formhead">Register with us</div>
        <div id="indication">required<span class="required">*</span></div>
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
            <label for="oname">Other Name</label>
            <div>
                <div><input type="text" maxlength="20" name="oname" value="<?php echo Utility::escape(Input::get('oname')) ?>" id="oname"></div>
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
            <label for="phone">Phone No</label>
            <div>
                <div><input type="text" maxlength="11" name="phone" value="<?php echo Utility::escape(Input::get('phone')) ?>" id="phone"><span class="required">*</span></div>
                <div id="phoneMsg" class="errMsg"></div>
            </div>
        </div>
        <div>
            <label for="email">Email</label>
            <div>
                <div><input type="email" maxlength="70" name="email" value="<?php echo Utility::escape(Input::get('email')) ?>" id="email"></div>
                <div id="emailMsg" class="errMsg"></div>
            </div>
        </div>

        <div>
            <label for="account">Account No</label>
            <div>
                <div><input type="text" maxlength="15" name="account" value="<?php echo Utility::escape(Input::get('account')) ?>" id="account"><span class="required">*</span></div>
                <div id="accountMsg" class="errMsg"></div>
            </div>
        </div>

        <div>
            <label for="bank">Bank</label>
            <div>
                <select id="bank" name="bank">
                    <?php
                    $banks = Utility::getBanks();
                    foreach ($banks as $bank) {
                        echo '<option value="' . $bank . '">' . $bank . '</option>';
                    }
                    ?>
                </select>
                <span class="required">*</span>
            </div>
        </div>

        <div>
            <label for="address">Residential Address</label>
            <div>
                <textarea id="address" name="address"><?php echo (Utility::noScript(Input::get('address'))) ? Input::get('address') : ''; ?></textarea>
            </div>
        </div>
        <div>
            <label for="picture" id="uploadTrigger" style="cursor: pointer; color:green;">Upload Picture</label>
            <div>
                <input type="file" name="picture" id="picture" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png" />
                <img id="image" width="100" height="100" />
                <input type="hidden" name="hiddenPic" value="" id="hiddenPic" />
                <div id="picMsg" class="errMsg"></div>
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
            <button id="regBtn">Register</button>
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