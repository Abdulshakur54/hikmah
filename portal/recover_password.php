 <?php
  //initializations
  spl_autoload_register(
    function ($class) {
      require_once '../classes/' . $class . '.php';
    }
  );
  session_start(Config::get('session/options'));
  if (!Input::submitted('get')) {
    Redirect::to(404);
  }

  $errors = [];
  if (!empty(Input::get('token')) && Token::check(Input::get('token'))) {
    $val = new Validation();
    $form_values = array(
      'email' => array(
        'name' => 'Email',
        'required' => true,
        'pattern' => '^[a-zA-Z]+[a-zA-Z0-9]*@[a-zA-Z]+.[a-zA-Z]+$'
      ),
      'cat' => array(
        'name' => 'Category',
        'required' => true
      )
    );


    if ($val->check($form_values)) {
      $email = Utility::escape(Input::get('email'));
      $cat = Utility::escape(Input::get('cat'));
      $db = DB::get_instance();
      if (in_array($cat, ['student', 'admission', 'staff', 'management'])) {
        if (in_array($cat, ['student', 'admission'])) {
          $email_column = 'email';
        } else {
          $email_column = 'choosen_email';
        }
        if (!empty($db->get($cat, $email_column))) {
          //generate the tokens
          $selector = Token::create(8);
          $resetToken = Token::create();
          $expiry = date('U') + 1800; //token to expire after 30 minutes
          $db->query('delete from password_reset where email = ?', [$email]); //delete any previous token
          $link = $url->to('reset_password.php', 0) . '?cat=' . $cat . '&selector=' . $selector . '&resetToken=' . $resetToken;
          $message = '<p>We received a password reset request. The link to reset your password is shown below, '
            . 'you can either click it or copy and paste in a browser to reset your password. If you did'
            . ' not make this request, you can ignore this email</p>'
            . '<p>Reset Link: <a href="' . $link . '">' . $link . '</a></p>'; //email body
          $db->query('insert into password_reset(email,selector,reset_token,expiry) values(?,?,?,?)', [$email, $selector, $resetToken, $expiry]);
          $mail = new Email();
          if ($mail->send($email, 'Password Reset Link', $message)) {
            Session::set_flash('recoverPassword', '<div class="success">A reset link has been successfully sent to your email, you can use it to change your password</div>');
            Redirect::home('success2.php?recoverPassword=true', 0);
          } else {
            $errors[] = 'An error is preventing us from emailing you a reset link'; //output message
          }
        } else {
          $errors[] = 'Email not found';
        }
        require_once "../libraries/vendor/autoload.php";
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
               <form method="post" action="<?php echo Utility::myself(); ?>" onsubmit="submitForm(event)" novalidate id="recoverPasswordForm">
                 <h6 class="font-weight-light font-weight-bold pb-3" id="headerInfo">Get a password reset link.</h6>
                 <div class="backendMsg">
                   <?php
                    foreach ($errors as $error) {
                      echo '<div class = "failure">' . $error . '</div>';
                    }
                    ?>
                 </div>
                 <div class="form-group">
                   <select class="js-example-basic-single w-100 p-2" id="cat" title="Category" name=" cat" required>
                     <option value="">:::Select Category:::</option>
                     <option value="student">Student</option>
                     <option value="admission">Admission Student</option>
                     <option value="staff">Staff</option>
                     <option value="managemet">Management</option>
                   </select>
                 </div>
                 <div class="form-group">
                   <input type="email" class="form-control form-control-lg" id="email" placeholder="Email" title="Email" required name="email">
                 </div>
                 <input type="hidden" value="<?php echo Token::generate() ?>" name="token" id="token" />
                 <div class="mt-3">
                   <button class="btn btn-block btn-primary  font-weight-medium" type="submit">Email me the link</button>
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
   <script src="validation/validation.js"></script>
   <!--End of My JS -->
   <!-- End custom js for this page-->

   <script>
     $(".js-example-basic-single").select2();
     validate('recoverPasswordForm');

     function submitForm(event) {
       event.preventDefault();
       if (validate('recoverPasswordForm', {
           validateOnSubmit: true
         })) {
         let email = document.getElementById('email').value
         let cat = document.getElementById('cat').value;
         let token = document.getElementById('token');
         location.assign('recover_password.php?cat=' + cat + '&email=' + email + '&token=' + token.value);
       }
     }
   </script>
 </body>

 </html>