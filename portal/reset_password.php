 <?php
  //initializations
  spl_autoload_register(
    function ($class) {
      require_once '../classes/' . $class . '.php';
    }
  );
  session_start(Config::get('session/options'));
  $url = new Url();
  $msg = '';

  if (!Input::submitted('get') || empty(Input::get('selector')) || empty(Input::get('resetToken')) || empty(Input::get('cat'))) {
    Redirect::to(404);
  }

  $errors = [];
  $cat = Utility::escape(Input::get('cat'));
  if (!empty(Input::get('token')) && Token::check(Input::get('token'))) {
    if (empty(Input::get('selector')) || empty(Input::get('resetToken'))) {
      Redirect::to(404);
    }
    $values = [
      'password' => [
        'name' => 'Password',
        'required' => true,
        'min' => 6,
        'max' => 12,
        'pattern' => '^[a-zA-Z0-9]+$'
      ]
    ];
    if (in_array($cat, ['admission', 'student'])) {
      $values['id'] = [
        'name' => 'ID',
        'required' => true,
      ];
    }
    $val = new Validation();
    if ($val->check($values)) {
      $selector = Utility::escape(Input::get('selector'));
      $resetToken = Utility::escape(Input::get('resetToken'));
      $now = date('U');
      //check if selector token exists and has not expired
      $db = DB::get_instance();
      $db->query('select email from password_reset where selector=? and reset_token=?', [$selector, $resetToken]);
      if ($db->row_count() > 0) {
        $email = $db->one_result()->email; //store email
        //delete token
        $db->query('delete from password_reset where selector=? and reset_token=?', [$selector, $resetToken]);
        //change the password of the user
        $table = $cat;
        $pwd_col = 'password';
        $pwd = Utility::escape(Input::get('pwd'));
        $newPwd = password_hash($pwd, PASSWORD_DEFAULT);
        if (in_array($cat, ['admission', 'student'])) {
          $id = Utility::escape(Input::get('id'));
          $username_col = ($cat === 'admission') ? 'adm_id' : 'std_id';
          //check if the email is associated with the given ID
          $db->query('select ' . $username_col . ' from ' . $table . ' where ' . $username_col . '=?', [$id]);
          if ($db->row_count() > 0) {
            $db->update($table, ['password' => $newPwd], "$username_col='$id'"); //update password

          }
        } else {
          $db->update($table, ['password' => $newPwd], "choosen_email='$id'"); //update password
        }

        Session::set_flash('resetSuccess', 'You have successfully changed your password');
        Redirect::home('success.php?resetSuccess=true', 0);
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
               <form method="post" action="<?php echo Utility::myself(); ?>" onsubmit="submitForm(event)" novalidate id="resetPasswordForm">
                 <h6 class="font-weight-light font-weight-bold pb-3" id="headerInfo">Create new password</h6>
                 <div class="backendMsg">
                   <?php
                    foreach ($errors as $error) {
                      echo '<div class = "failure">' . $error . '</div>';
                    }
                    ?>
                 </div>
                 <?php
                  if (in_array($cat, ['admission', 'student'])) {
                  ?>
                   <div class="form-group">
                     <input type="text" class="form-control form-control-lg" id="username" placeholder="Username" title="Username" required name="username" />
                   </div>
                 <?php
                  }
                  ?>
                 <div class="form-group">
                   <input type="password" class="form-control form-control-lg" id="password" placeholder="Password" title="Password" required name="password" pattern="^[a-zA-Z0-9]+$" minlength="6" maxlength="32" />
                 </div>
                 <div class="form-group">
                   <input type="password" class="form-control form-control-lg" id="c_password" placeholder="Confirm Password" title="Confirm Password" required name="c_password" />
                 </div>
                 <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                 <input type="hidden" value="<?php echo Utility::escape(Input::get('resetToken')) ?>" name="resetToken" id="resetToken" />
                 <input type="hidden" value="<?php echo Utility::escape(Input::get('selector')) ?>" name="selector" id="selector" />
                 <input type="hidden" value="<?php echo Utility::escape(Input::get('cat')) ?>" name="cat" id="cat" />
                 <div class="mt-3">
                   <button class="btn btn-block btn-primary  font-weight-medium" type="submit">Create</button>
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
   <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
   <!--End of My JS -->
   <!-- End custom js for this page-->

   <script>
     validate('resetPasswordForm');

     function submitForm(event) {
       event.preventDefault();
       if (validate('resetPasswordForm', {
           validateOnSubmit: true
         })) {
         let cat = document.getElementById('cat').value;
         let token = document.getElementById('token');
         let password = document.getElementById('password').value;
         let c_password = document.getElementById('c_password').value;
         let resetToken = document.getElementById('resetToken').value;
         let selector = document.getElementById('selector').value;
         if (password !== c_password) {
           swalNotifiyDismiss('Confirm Password must match Password');
           return;
         }
         location.assign('recover_password.php?cat=' + cat + '&password=' + password + '&resetToken=' + resetToken + '&selector=' + selector + '&token=' + token.value);
       }
     }
   </script>
 </body>

 </html>