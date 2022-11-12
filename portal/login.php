 <?php
  //initializations
  spl_autoload_register(
    function ($class) {
      require_once '../classes/' . $class . '.php';
    }
  );
  session_start(Config::get('session/options'));

  //redirect user
  if (Session::exists('user') || User::hikmahDashboardRememberMe()) {
    Redirect::to('dashboard.php');
  }
  //end of redirect user

  $errors = [];
  if (Input::submitted() && Token::check(Input::get('token'))) {
    $val = new Validation();
    $form_values = array(
      'username' => array(
        'name' => 'Username',
        'required' => true,
        'pattern' => '^[a-zA-Z1-9`][a-zA-Z0-9`\/]+$'
      ),
      'password' => array(
        'name' => 'Password',
        'required' => true
      )
    );

    if ($val->check($form_values)) {
      $remember = (Input::get('remember') === 'on') ? true : false;
      $user_type = Utility::escape(Input::get('user_type'));
      switch ($user_type) {
        case 'student':
          $user = new Student();
          break;
        case 'staff':
          $user = new Staff();
          break;
        case 'management':
          $user = new Management();
          break;
        case 'admission':
          $user = new Admission();
          break;
      }

      if ($user->pass_match(Input::get('username'), Input::get('password'))) {
        $username = Utility::escape(Input::get('username'));
        if ($user->login($remember)) {
          session_regenerate_id();
          $data = $user->data();
          Session::set('user_type', $user_type);
          Session::set('user', $username);
          $greeting = User::get_user_greeting($username);
          Session::set_flash(Config::get('hikmah/flash_welcome'), $greeting);
          if ($remember) {
            Cookie::set('user', $username, time() + Config::get('cookie/expiry'));
          }
          if(User::is_active($username)){

            Redirect::to('dashboard.php');
          }else{
            $errors[] = 'You are no longer allowed access in to this portal';
          }
        }
      } else {
        $errors[] = 'Wrong username and password combination';
      }
    } else {
      $errors = $val->errors();
    }
  }
  //end of initializatons
  //$last_page = (Session::lastPageExists()) ? Session::getLastPage() : '';
  ?>
 <!DOCTYPE html>
 <html lang="en">

 <head>
   <!-- Required meta tags -->
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <title>Skydash Admin</title>
   <!-- plugins:css -->
   <link rel="stylesheet" href="vendors/feather/feather.css">
   <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
   <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
   <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
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
                 <button id="admission" onclick="changeContent('admission')"> <i class="mdi mdi-account-multiple-plus"></i> Admission</button>
                 <button id="student" onclick="changeContent('student')"> <i class="mdi mdi-human-male-female"></i> Student</button>
                 <button id="staff" onclick="changeContent('staff')"> <i class="mdi mdi-account-multiple"></i> Staff</button>
                 <button id="management" onclick="changeContent('management')"> <i class="mdi mdi-account-multiple-plus"></i> Management</button>
               </div>
               <form class="pt-5" method="post" action="<?php echo Utility::myself(); ?>" onsubmit="login(event)" novalidate id="loginForm">
                 <h6 class="font-weight-light font-weight-bold pb-1" id="headerInfo">Sign in to continue.</h6>
                 <div class="backendMsg">
                   <?php
                    foreach ($errors as $error) {
                      echo '<div class = "failure">' . $error . '</div>';
                    }
                    ?>
                 </div>
                 <div class="form-group">
                   <input type="text" class="form-control form-control-lg" id="username" placeholder="Username" title="Username" required name="username">
                 </div>
                 <div class="form-group">
                   <input type="password" class="form-control form-control-lg" id="password" placeholder="Password" title="Password" required name="password">
                 </div>
                 <input type="hidden" name="user_type" id="userType" />
                 <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                 <div class="mt-3">
                   <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" type="submit" id="signInBtn">SIGN IN</button>
                 </div>
                 <div class="my-2 d-flex justify-content-between align-items-center">
                   <div class="form-check">
                     <label class="form-check-label text-muted">
                       <input type="checkbox" class="form-check-input" name="remember" id="remember">
                       Keep me signed in
                     </label>
                   </div>
                   <a href="recover_password.php" class="auth-link text-black">Recover Password</a>
                 </div>
                 <div class="text-center mt-4 font-weight-light">
                   <a href="register.php" class="text-primary">Create Account</a>
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
   <script src="validation/validation.js"></script>
   <script src="login.js"></script>
   <!--End of My JS -->
   <!-- End custom js for this page-->
 </body>

 </html>