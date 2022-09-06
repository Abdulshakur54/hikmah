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
$last_page = (Session::lastPageExists()) ? Session::getLastPage() : '';

if (Session::exists('user')) {
  $username = Session::get('user');
  $menu = new Menu();
  $menus = $menu->get($username);
 
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

</head>



<body>

  <?php
  //this function is used to allow complete redirect into exam portal, this is done when the file url ends with new_exam.php

  function is_new_exam($url): bool

  {

    return (substr($url, -12) == 'new_exam.php') ? true : false;
  }



  ?>

  <div class="container-scroller">

    <!-- partial:partials/_navbar.html -->

    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">

        <a class="navbar-brand brand-logo mr-5" href="dashboard.php"><img src="images/logo.jpg" class="mr-2" alt="logo" /></a>

        <a class="navbar-brand brand-logo-mini" href="dashboard.php"><img src="images/logo.jpg" alt="logo" /></a>

      </div>

      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">

          <span class="icon-menu"></span>

        </button>

        <ul class="navbar-nav mr-lg-2">

          <li class="nav-item nav-search d-none d-lg-block">

            <div class="input-group">

              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">

                <span class="input-group-text" id="search">

                  <i class="icon-search"></i>

                </span>

              </div>

              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">

            </div>

          </li>

        </ul>

        <ul class="navbar-nav navbar-nav-right">

          <li class="nav-item dropdown">

            <a class="nav-link count-indicator pr-5" id="notificationIcon" href="#">

              <i class="icon-bell"></i>

              <span class="notificationCount">5</span>

            </a>

            <a class="nav-link count-indicator" id="RequestIcon" href="#">

              <i class="icon-bell"></i>

              <span class="requestCount">7</span>

            </a>

          </li>

          <li class="nav-item nav-profile dropdown">

            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">

              <img src="images/faces/face28.jpg" alt="profile" />

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

          <li class="nav-item pt-2 pb-2 bg-primary font-weight-bold text-center rounded-circle" id="welcomeMessage" style="color:#fff">Good Afternoon, Director</li>

          <li class="nav-item">

            <a class="nav-link" href="index.html">

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

                      <li class="nav-item"> <a class="nav-link" onclick="<?php echo (!is_new_exam($child->url)) ? 'getPage(\'' . Utility::escape($child->url) . '\')' : '#' ?>" href="<?php echo (is_new_exam($child->url)) ? Utility::escape($child->url) : '#' ?>"><?php echo Utility::escape($child->display_name) ?></a></li>

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

        <div class="content-wrapper">

          <div class="row  mb-0 pb-0">

            <div class="col-md-12 grid-margin mb-0 pb-0" id="page">

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

  <script src="vendors/js/vendor.bundle.base.js"></script>

  <!-- endinject -->

  <!-- Plugin js for this page -->

  <script src="vendors/chart.js/Chart.min.js"></script>

  <script src="vendors/datatables.net/jquery.dataTables.js"></script>

  <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>

  <script src="js/dataTables.select.min.js"></script>



  <!-- End plugin js for this page -->

  <!-- inject:js -->

  <script src="js/off-canvas.js"></script>

  <script src="js/hoverable-collapse.js"></script>

  <script src="js/template.js"></script>

  <script src="js/settings.js"></script>

  <script src="js/todolist.js"></script>

  <!-- endinject -->

  <!-- Custom js for this page-->

  <script src="js/dashboard.js"></script>

  <script src="js/Chart.roundedBarCharts.js"></script>

  <!-- End custom js for this page-->



  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="validation/validation.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap4.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
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