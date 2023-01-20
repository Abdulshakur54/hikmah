<?php

//initializations
require_once './error_reporting.php';
spl_autoload_register(

  function ($class) {

    require_once '../classes/' . $class . '.php';
  }

);

session_start(Config::get('session/options'));
//end of initializatons
if (!empty(Input::get('page'))) {
  Session::setLastPage(Input::get('page'));
}


if (Session::exists('user')) {
  $username = Session::get('user');
  $menu = new Menu();
  $menus = $menu->get($username);
  $link = User::get_link($username);
  $last_page = (Session::lastPageExists()) ? Session::getLastPage() : $link . '/home.php';
  $profile_image_path = User::get_profile_image_path($username);
} else {

  Redirect::to('login.php');
}

?>

<!DOCTYPE html>

<html lang="en">



<head>

  <!-- Required meta tags -->

  <meta charset="utf-8" />

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <title>Hikmah Portal</title>

  <!-- plugins:css -->

  <link rel="stylesheet" href="vendors/feather/feather.css" />

  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css" />

  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css" />

  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">

  <!-- endinject -->

  <!-- Plugin css for this page -->

  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css" />

  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


  <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap4.min.css" rel="stylesheet" />

  <!-- End plugin css for this page -->

  <!-- inject:css -->

  <link rel="stylesheet" href="css/vertical-layout-light/style.css" />

  <!-- endinject -->

  <link rel="shortcut icon" href="images/favicon.png" />



  <!-- My CSS-->

  <link rel="stylesheet" href="styles/style.css" />

  <link rel="stylesheet" type="text/css" href="ld_loader/ld_loader.css" />

  <link rel="stylesheet" href="validation/validation.css" />

  <!-- My CSS-->
  <style>
    .count-indicator {
      position: relative;
      display: none;
    }

    .indicator {
      position: absolute;
      top: 4px;
      left: 23px;
    }

    @media screen and (max-width:768px) {
      .content-wrapper {
        background: rgb(255, 255, 255);
      }
    }
  </style>

</head>



<body>

  <?php

  ?>

  <div class="container-scroller">

    <!-- partial:partials/_navbar.html -->

    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">

        <a class="navbar-brand brand-logo mr-5" href="#" onclick="getPage('<?php echo $link . '/home.php' ?>')"><img src="images/logo.jpg" class="mr-2" alt="logo" /></a>

        <a class="navbar-brand brand-logo-mini" href="#" onclick="getPage('<?php echo $link . '/home.php' ?>')"><img src="images/logo.jpg" alt="logo" /></a>

      </div>

      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">

          <span class="icon-menu"></span>

        </button>



        <ul class="navbar-nav navbar-nav-right">

          <li class="nav-item dropdown  mr-3">

            <a class="nav-link count-indicator pr-5 text-primary" id="notificationLink" href="#" onclick="getPage('<?php echo $link . '/notifications.php' ?>')">

              <i class="mdi mdi-email-outline"></i>

              <span id="notificationCount" class="indicator"></span>

            </a>

            <a class="nav-link count-indicator mr-3 text-success" id="requestLink" href="#" onclick="getPage('<?php echo $link . '/requests.php' ?>')">

              <i class="mdi mdi-human-greeting"></i>

              <span id="requestCount" class="indicator"></span>

            </a>

          </li>

          <li class="nav-item nav-profile dropdown mr-3">

            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">

              <img src="<?php echo $profile_image_path; ?>" alt="profile" />

            </a>

            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">

              <a class="dropdown-item" href="logout.php">

                <i class="ti-power-off text-primary"></i>

                Logout

              </a>

            </div>

          </li>

        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">

          <span class="icon-menu"></span>

        </button>

      </div>

    </nav>

    <!-- partial -->

    <div class="container-fluid page-body-wrapper">

      <!-- partial:partials/_sidebar.html -->

      <nav class="sidebar sidebar-offcanvas" id="sidebar">

        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="#" onclick="getPage('<?php echo $link . '/home.php' ?>')">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>

          </li>

          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-roles" aria-expanded="false" aria-controls="ui-roles">
              <i class="icon-layout menu-icon"></i>
              <span class="menu-title">Roles Management</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-roles">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" onclick="getPage('superadmin/role_list.php')" href="#">Roles</a></li>
                <li class="nav-item"> <a class="nav-link" onclick="getPage('superadmin/menu_list.php')" href="#">Menus</a></li>
                <li class="nav-item"> <a class="nav-link" onclick="getPage('superadmin/assign_menus.php')" href="#">Assign Menus</a></li>
                <li class="nav-item"> <a class="nav-link" onclick="getPage('superadmin/unassign_menus.php')" href="#">Unassign Menus</a></li>
              </ul>
            </div>
          </li>

          <?php

          $counter  = 1;
          foreach ($menus as $menu) { ?>



            <li class="nav-item">

              <a class="nav-link" data-toggle="collapse" href="#menu<?php echo $counter ?>" aria-expanded="false" aria-controls="menu<?php echo $counter ?>">

                <i class="icon-layout menu-icon"></i>

                <span class="menu-title"><?php echo Utility::escape($menu->display_name) ?></span>

                <i class="menu-arrow"></i>

              </a>

              <div class="collapse" id="menu<?php echo $counter ?>">

                <ul class="nav flex-column sub-menu">

                  <?php

                  if (property_exists($menu, 'children')) {

                    foreach ($menu->children as $child) {

                  ?>

                      <li class="nav-item"> <a class="nav-link" onclick="<?php echo (!Utility::is_new_exam($child->url)) ? 'getPage(\'' . Utility::escape($child->url) . '\')' : 'javascript:void(0)' ?>" href="<?php echo (Utility::is_new_exam($child->url)) ? Utility::escape($child->url) : '#' ?>"><?php echo Utility::escape($child->display_name) ?></a></li>

                  <?php

                    }
                  } ?>

                </ul>

              </div>

            </li>

          <?php

            $counter++;
          }

          ?>

        </ul>

      </nav>

      <!-- partial -->

      <div class="main-panel">

        <div class="content-wrapper px-0 px-md-4">

          <div class="row">
            <div class="d-flex justify-content-end w-100 mr-md-5 px-0" id="welcomeMessage">
              <div class="font-weight-bold d-flex flex-column align-items-center pr-3 pr-md-5" style="color:blue;"><?php echo Session::get_flash(Config::get('hikmah/flash_welcome')) ?></div>
            </div>

            <div class="col-md-12 grid-margin mb-0 pb-0 mx-0" id="page">
              <!-- page token -->

              <input type="hidden" name="page_token" id="page_token" value="<?php echo Token::generate(32, 'page_token') ?>">

              <!-- end of page token -->

            </div>

          </div>

        </div>

        <input type="hidden" name="lastpage" id="lastpage" value="<?php echo $last_page ?>">

        <!-- content-wrapper ends -->

        <!-- partial:partials/_footer.html -->

        <footer class="footer mt-0 pt-0">

          <div class="d-sm-flex justify-content-center justify-content-sm-between">

            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © <?php echo date('Y') . ' @ Hikmah Group of Schools' ?></span>

          </div>

        </footer>

        <!-- partial -->

      </div>

      <!-- main-panel ends -->

    </div>

    <!-- page-body-wrapper ends -->

  </div>


  <!-- inject:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="js/off-canvas.js"></script>

  <script src="js/hoverable-collapse.js"></script>

  <script src="js/template.js"></script>

  <script src="js/settings.js"></script>

  <script src="js/todolist.js"></script>

  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="validation/validation.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
  <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

  <!-- endinject -->

  <!-- endinject -->

  <!-- Plugin js for this page -->

  <script src="vendors/chart.js/Chart.min.js"></script>

  <script src="vendors/datatables.net/jquery.dataTables.js"></script>

  <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>

  <script src="js/dataTables.select.min.js"></script>



  <!-- End plugin js for this page -->



  <!-- Custom js for this page-->

  <script src="js/dashboard.js"></script>

  <script src="js/Chart.roundedBarCharts.js"></script>

  <!-- End custom js for this page-->




  <!-- 

 -->
















  <!--Div to hold custom JS -->







  <!--My JS -->

  <script src="scripts/script.js"></script>

  <script src="scripts/portalscript.js"></script>

  <script src="scripts/ajaxrequest.js"></script>

  <script src="scripts/main.js"></script>

  <script src="ld_loader/ld_loader.js"></script>

  <script>
    var lastPage = document.getElementById('lastpage').value;
    if (lastPage.length > 0) {

      getPage(lastPage);

    }
  </script>

  <!--End of My JS -->

  <!-- End custom js for this page-->

</body>



</html>