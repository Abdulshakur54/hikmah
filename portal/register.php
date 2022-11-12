<?php
require_once './error_reporting.php';
//initializations
spl_autoload_register(
  function ($class) {
    require_once '../classes/' . $class . '.php';
  }
);
session_start(Config::get('session/options'));
$util = new Utils();

// //redirect user
// if (Session::exists('user')) {
//   Redirect::to('dashboard.php');
// }
// //end of redirect user
if (Input::submitted('get')) {
  if (empty(Utility::escape(Input::get('user_type')))) {
    if (!Session::exists('user_type')) {
      Session::set('user_type', 'admission');
    }
    $user_type = Session::get('user_type');
  } else {
    $user_type = Utility::escape(Input::get('user_type'));
  }
}
$errors = [];
if (Input::submitted() && Token::check(Input::get('token'))) {
  $val = new Validation();
  $formvalues = array(
    'fname' => array('name' => 'Firstname', 'required' => true, 'min' => 3, 'max' => '20', 'pattern' => '^[a-zA-Z`]+$'),
    'lname' => array('name' => 'Lastname', 'required' => true, 'min' => 3, 'max' => '20', 'pattern' => '^[a-zA-Z`]+$'),
    'oname' => array('name' => 'Othername', 'min' => 3, 'max' => '20', 'pattern' => '^[a-zA-Z`]+$'),
    'password' => array('name' => 'password', 'required' => true, 'min' => 6, 'max' => '32', 'pattern' => '^[a-zA-Z0-9`]+$'),
    'c_password' => array('name' => 'Confirm_Password', 'same' => 'password'),
    'phone' => array('name' => 'Phone', 'required' => true, 'pattern' => '^(080|070|090|081|091|071)[0-9]{8}$'),
    'pin' => array('name' => 'Pin', 'required' => true, 'pattern' => '^[0-9a-zA-Z]{14}$'),
    'email' => array('name' => 'Email', 'required' => true, 'max' => 70, 'min' => 10, 'pattern' => '^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$'),
    'dob' => array('name' => 'Date of Birth', 'required' => true),
    'state' => array('name' => 'State Of Origin', 'required' => true),
    'lga' => array('name' => 'LGA Of Origin', 'required' => true)
  );
  $user_type = Utility::escape(Input::get('user_type'));
  if ($user_type === 'staff' || $user_type === 'management') {
    $formvalues['account'] = array('name' => 'Account No', 'required' => true, 'max' => 15, 'min' => 10, 'pattern' => '^[0-9]+$');
    $formvalues['bank'] = array('name' => 'Bank', 'in' => Utility::getBanks());
  }

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
    $errors[] = 'Picture not uploaded';
  }
  $pin = Utility::escape(Input::get('pin'));
  $agg = new Aggregate();
  if ($val->check($formvalues) && $val->checkFile($fileValues) && Utility::noScript(Input::get('address'))) {
    $pin_from_table = $agg->lookUp('token', 'token', 'token,=,' . $pin);
    if (!empty($pin_from_table) && Utility::equals($pin, $pin_from_table)) { //confirm the token
      $class_name = ucfirst($user_type);
      $user = new $class_name();
      $password = Utility::escape(Input::get('password'));
      $fname = Utility::escape(Input::get('fname'));
      $lname = Utility::escape(Input::get('lname'));
      $oname = Utility::escape(Input::get('oname'));
      $dob = Utility::escape(Input::get('dob'));
      $db = DB::get_instance();
      $url = new Url();
      $db->query('select * from token where token=?', [$pin]); //get the token and the data associated with it
      $res = $db->one_result();
      $sch_abbr = $res->sch_abbr;
      $rank = $res->pro_rank;
      $valid_rank = false; // instantiate valid pin

      switch ($user_type) {
        case 'admission':
          $table = Config::get('users/table_name3');
          $username_column = Config::get('users/username_column3');
          $level = (int) $res->level;
          $valid_ranks = [11, 12];
          $valid_rank = (in_array($rank, $valid_ranks));
          if($valid_rank){
            $user_id = $util->getSession($sch_abbr) . '/' . 'A' . Admission::genId(); //generates the admission id
          }
          break;
        case 'staff':
          $user_id = 'S' . Staff::genId(); //generates the Staff id
          $table = Config::get('users/table_name1');
          $username_column = Config::get('users/username_column1');
          $salary = $res->salary;
          $asst = $res->asst;
          $account = Utility::escape(Input::get('account'));
          $bank = Utility::escape(Input::get('bank'));
          $title = Utility::escape(Input::get('title'));
          $valid_ranks = [7, 8, 15, 16];
          $valid_rank = (in_array($rank, $valid_ranks));
          break;
        case 'management':
          $user_id = 'M' . Management::genId(); //generates the Management i
          $table = Config::get('users/table_name0');
          $username_column = Config::get('users/username_column0');
          $salary = $res->salary;
          $asst = $res->asst;
          $account = Utility::escape(Input::get('account'));
          $bank = Utility::escape(Input::get('bank'));
          $title = Utility::escape(Input::get('title'));
          $valid_ranks = [1, 2, 3, 4, 5, 6, 17];
          $valid_rank = (in_array($rank, $valid_ranks));
          break;
      }
      if ($valid_rank) {
        require_once "../libraries/vendor/autoload.php";
        $mail = new Email();
        $email = Utility::escape(Input::get('email'));
        $state = Utility::escape(Input::get('state'));
        $lga = Utility::escape(Input::get('lga'));
        $address = Input::get('address');
        $ext = $file->extension();
        $pictureName = $user_id . '.' . $ext;
        $file_path = "$user_type/uploads/passports/";

        switch ($user_type) {
          case 'admission':
            $pictureName = Utility::format_student_id($user_id). '.' . $ext;
            $sql1 = 'insert into ' . $table . '(' . $username_column . ', password,fname,lname,oname,rank,sch_abbr,phone,email,level,dob,state,lga,picture) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $vals1 = [$user_id, password_hash($password, PASSWORD_DEFAULT), $fname, $lname, $oname, $rank, $sch_abbr, Utility::escape(Input::get('phone')), $email, $level, $dob, $state, $lga, $pictureName];
            if ($db->query($sql1, $vals1)) { //performs a query

              /*for hikmah only to help use the role and menu functionality*/
              $role_id = $user->get_role_id($rank, 0);
              $db->insert(Config::get('users/table_name'), ['user_id' => $user_id, 'role_id' => $role_id]);
              Menu::add_available_menus($user_id, $role_id);
              /*for hikmah only to help use the role and menu functionality*/

              $file->move($file_path . $pictureName); //move picture to the destination folder
              $agg->rowDelete('token', 'token,=,' . $pin); //delete the token from the token table
              $message = '<div style="padding:11px">Dear <strong>' . Utility::formatName($fname, $lname, $oname) . '</strong>, Your registeration have been approved,  you can login to your portal with the username: <strong>' . $user_id . '</strong> <a href="' . $url->to('login.php', 0) . '">Login Here</a></div>';
              $mail->send($email, 'Registration Completion', $message);
              Session::set_flash('new_user', '<div>Thanks for Registering. You can now Login to your account</div><div>Your Username is <strong>' . $user_id . '</strong><br><em>use it to login along with the password you created during registration. Your username has also be sent to your email for backup</em></div>');
              Redirect::to('success2.php?user_type=admission');
            } else {
              $errors[] = 'Registration Not Successful';
            }
            break;
          case 'staff':
          case 'management':
            $sql1 = 'insert into ' . $table . '(' . $username_column . ', password,fname,lname,oname,rank,sch_abbr,phone,email,dob,state,lga,title,picture) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $vals1 = [$user_id, password_hash($password, PASSWORD_DEFAULT), $fname, $lname, $oname, $rank, $sch_abbr, Utility::escape(Input::get('phone')), $email, $dob, $state, $lga, $title, $pictureName];
            $sql2 = 'insert into account(receiver,no,bank,salary) values(?,?,?,?)';
            $vals2 = [$user_id, $account, $bank, $salary];
            $sql3 = 'insert into messaging_permission(user_id,sms,email,notification) values(?,?,?,?)';
            if($rank == 1 || $rank == 6){ //director or HRM
              $vals3 = [$user_id, 1, 1, 1];
            }else{
              $vals3 = [$user_id, 0, 1, 1];
            }

            if ($db->trans_query([[$sql1, $vals1], [$sql2, $vals2], [$sql3, $vals3]])) { //performs a transaction which consist of 2 queries

              /*for hikmah only to help use the role and menu functionality*/
              $role_id = $user->get_role_id($rank, $asst);
              $db->insert(Config::get('users/table_name'), ['user_id' => $user_id, 'role_id' => $role_id]);
              Menu::add_available_menus($user_id, $role_id);
              /*for hikmah only to help use the role and menu functionality*/

              $agg->rowDelete('token', 'token,=,' . $pin); //delete the token from the token table
              $file->move($file_path . $pictureName); //move picture to the destination folder
              $alert = new Alert(true);
              $req = new Request();
              //send a confirmation request to the accountant
              $req->send($user_id, 3, 'Please, confirm a request of &#8358;' . $salary . ' as salary for ' . $title . ' ' . Utility::formatName($fname, $oname, $lname), RequestCategory::SALARY_CONFIRMATION);

              if ($user_type === 'management') {
                //notify the director(s)
                $alert->sendToRank(1, 'Registration Completion', 'This is to inform you that ' . Utility::formatName($fname, $oname, $lname) . ' has successfully registered as ' . formatManagementMsg($rank, $user) . '.<br>A request has been sent to the accountant to confirm his salary of &#8358;' . $salary);

                $message = '<div style="padding:11px">Dear <strong>' . Utility::formatName($fname, $lname, $oname) . '</strong>, Your registeration have been approved,  you can login to your portal with the username: <strong>' . $user_id . '</strong>  <a href="' . $url->to('login.php', 0) . '">Login Here</a></div>';
                $mail->send($email, 'Registration Completion', $message);

                Session::set_flash('new_user', '<div>Thanks for Registering. You can now Login to your account</div><div>Your Username is <strong>' . $user_id . '</strong><br><em>use it to login along with the password you created during registration. Your username has also be sent to your email for backup</em></div>');
                Redirect::to('success2.php?user_type=management');
              } else {
                if ($user_type === 'staff') {
                  //notify the APM(s)
                  $notMessage = 'This is to inform you that ' . $title . ' ' . Utility::formatName($fname, $oname, $lname) . ' has successfully registered as ' . formatStaffMsg($rank, $user);
                  $alert->sendToRank(2, 'Registration Completion', $notMessage);
                  $profile_url =$url->to('profile.php?username='.$user_id,0);
                  //customize a message to notify the HOS(s)
                  $notMessage .= '.<br><br>
                  <a href="'.$profile_url.'">View Profile</a><br><a href="#" onclick="getPage(\'management/hos/assign_class.php\')">Assign Class (If staff is a Class Teacher)</a><br>
                                            <a href="#" onclick="getPage(\'management/hos/add_subject.php\')">Assign Subject (If staff is a Subject Teacher)</a>
                                            <a href="#" onclick="' . Utility::escape(Session::getAltLastPage()) . '">Ignore (if staff is a Non Teaching Staff)</a>
                                           ';
                  //send to HOS(s) of the selected school
                  $alert->reset(); //this will help reset the object for another prepared query
                  $alert->sendToRank(5, 'Registration Completion', $notMessage, 'sch_abbr,=,' . $sch_abbr);

                  $message = '<div style="padding:11px">Dear <strong>' . Utility::formatName($fname, $lname, $oname) . '</strong>, Your registeration have been approved,  you can login to your portal with the username: <strong>' . $user_id . '</strong>  <a href="' . $url->to('login.php', 0) . '">Login Here</a> </div>';
                  $mail->send($email, 'Registration Completion', $message);

                  Session::set_flash('new_user', '<div>Thanks for Registering. You can now Login to your account</div><div>Your Username is <strong>' . $user_id . '</strong><br><em>use it to login along with the password you created during registration. Your username has also be sent to your email for backup</em></div>');
                  Redirect::to('success2.php?user_type=staff');
                }
              }
            } else {
              $errors[] = 'Registration Not Successful';
            }
            break;
        }
      } else {
        $errors[] = 'Use the proper tab to register';
      }
    } else {
      $errors[] = 'Pin did not match';
    }
  } else {
    $errors = $val->errors();
  }
}
//end of initializatons
//$last_page = (Session::lastPageExists()) ? Session::getLastPage() : '';

//helper functions 
function formatStaffMsg($rank, $user)
{
  return 'a ' . User::getFullPosition($rank, $user);
}

function formatManagementMsg($rank, $user)
{
  switch ($rank) {
    case 2:
      return 'an Academic Planning Manager';
    case 3:
      return 'an Accountant';
    default:
      return 'a ' . User::getFullPosition($rank, $user);
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Registration Page</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />
  <link rel="stylesheet" href="validation/validation.css" />
  <link rel="stylesheet" href="styles/style.css" />
  <link rel="stylesheet" href="login.css" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5 ">
              <div class="tab">
                <button id="admission" onclick="changeContent('admission')"> <i class="mdi mdi-human-male-female"></i> Admission</button>
                <button id="staff" onclick="changeContent('staff')"> <i class="mdi mdi-account-multiple"></i> Staff</button>
                <button id="management" onclick="changeContent('management')"> <i class="mdi mdi-account-multiple-plus"></i> Management</button>
              </div>
              <form class="pt-5" method="post" action="<?php echo Utility::myself(); ?>" onsubmit="register(event)" novalidate id="regForm" enctype="multipart/form-data">
                <h6 class="font-weight-light font-weight-bold pb-1" id="headerInfo"><?php echo Utility::escape(ucfirst($user_type) . ' Registration') ?>.</h6>
                <div class="backendMsg">
                  <?php
                  foreach ($errors as $error) {
                    echo '<div class = "failure">' . $error . '</div>';
                  }
                  ?>
                </div>
                <?php
                if ($user_type === 'staff' || $user_type === 'management') {
                ?>
                  <div class="form-group">
                    <label for="title">Title</label>
                    <select class="js-example-basic-single w-100 p-2" id="title" Title="title" name="title" required>
                      <option>Mall</option>
                      <option>Mallama</option>
                      <option>Mr</option>
                      <option>Mrs</option>
                      <option>Miss</option>
                    </select>
                  </div>
                <?php
                }
                ?>
                <div class="form-group">
                  <label for="fname">Firstname</label>
                  <input type="text" class="form-control form-control-lg" id="fname" title="Firstname" required name="fname" value="<?php echo Utility::escape(Input::get('fname')) ?>" pattern="^[a-zA-Z`]+$">
                </div>
                <div class="form-group">
                  <label for="lname">Lastname</label>
                  <input type="text" class="form-control form-control-lg" id="lname" title="Lastname" required name="lname" value="<?php echo Utility::escape(Input::get('lname')) ?>" pattern="^[a-zA-Z`]+$">
                </div>
                <div class="form-group">
                  <label for="oname">Othername</label>
                  <input type="text" class="form-control form-control-lg" id="oname" title="Othername" name="oname" value="<?php echo Utility::escape(Input::get('oname')) ?>" pattern="^[a-zA-Z`]+$">
                </div>
                <div class="form-group">
                  <label for="dob">Date of birth</label>
                  <input type="date" class="form-control form-control-lg" id="dob" title="Date of birth" required name="dob" value="<?php echo Utility::escape(Input::get('dob')) ?>">
                </div>

                <div class="form-group">
                  <label for="state">State Of Origin</label>
                  <select class="js-example-basic-single w-100 p-2" id="state" title="State Of Origin" onchange="populateLGA(this)" name="state" required>
                    <?php
                    $states = Utility::getStates();
                    echo '<option value="">:::Select State:::</option>';
                    foreach ($states as $state) {
                      echo '<option value="' . $state['id'] . '">' . $state['name'] . '</option>';
                    }
                    ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="lga">LGA Of Origin</label>
                  <select class="js-example-basic-single w-100 p-2" id="lga" title="LGA Of Origin" name="lga" required>
                    <option value="">:::Select State First:::</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="phone">Phone No
                    <?php if ($user_type == 'admission') {
                      echo '(Parent)';
                    } ?>
                  </label>
                  <input type="text" maxlength="11" class="form-control form-control-lg" id="phone" title="Phone No" required name="phone" pattern="^[0-9]{11}$" value="<?php echo Utility::escape(Input::get('phone')) ?>">
                </div>
                <div class="form-group">
                  <label for="email">Email
                    <?php if ($user_type == 'admission') {
                      echo '(Parent)';
                    } ?>
                  </label>
                  <input type="email" class="form-control form-control-lg" id="email" title="Email" required name="email" value="<?php echo Utility::escape(Input::get('email')) ?>">
                </div>

                <?php
                if ($user_type == 'management' || $user_type == 'staff') {
                ?>
                  <div class="form-group">
                    <label for="account">Account No</label>
                    <input type="text" class="form-control form-control-lg" id="account" title="Account No" required name="account" pattern="^[0-9]{10}$" value="<?php echo Utility::escape(Input::get('account')) ?>">
                  </div>
                  <div class="form-group">
                    <label for="bank">Bank</label>
                    <select class="js-example-basic-single w-100 p-2" id="bank" title="Bank" name="bank" required>
                      <?php
                      $banks = Utility::getBanks();
                      echo '<option value="">:::Select Bank:::</option>';
                      foreach ($banks as $bank) {
                        echo '<option>' . $bank . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                <?php
                }
                ?>


                <div class="form-group">
                  <label for="address">Residential Address</label>
                  <input type="text" id="address" name="address" value="<?php echo Input::get('address') ?>" class="form-control" />
                </div>


                <div class="mb-4">
                  <label for="picture" id="uploadTrigger" style="cursor: pointer; color:green;">Upload Picture</label>
                  <div>
                    <input type="file" name="picture" id="picture" style="display: none" onchange="showImage(this)" accept="image/jpg, image/jpeg, image/png" />
                    <img id="image" width="100" height="100" />
                    <input type="hidden" name="hiddenPic" value="" id="hiddenPic" />
                    <div id="picMsg" class="errMsg"></div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="pin">Pin</label>
                  <input type="text" class="form-control form-control-lg" id="pin" title="Pin" required name="pin" value="<?php echo Utility::escape(Input::get('pin')) ?>">
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control form-control-lg" id="password" title="Password" required name="password" pattern="^[a-zA-Z0-9`]+$">
                </div>
                <div class="form-group">
                  <label for="c_password">Confirm Password</label>
                  <input type="password" class="form-control form-control-lg" id="c_password" title="Confirm Password" required name="c_password">
                </div>
                <input type="hidden" name="user_type" id="userType" value="<?php echo $user_type ?>" />
                <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                <div class="mt-3">
                  <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" type="submit" id="signUpBtn">SIGN UP</button>
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Already have an account? <a href="login.php" class="text-primary">Login</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <!-- endinject -->
  <!--My JS -->
  <script src="scripts/script.js"></script>
  <script src="scripts/ajaxrequest.js"></script>
  <script src="validation/validation.js"></script>
  <script src="register.js"></script>
  <!--End of My JS -->
  <!-- End custom js for this page-->
</body>

</html>