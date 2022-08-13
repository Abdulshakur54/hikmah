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
  //initializations
  spl_autoload_register(
    function ($class) {
      require_once '../classes/' . $class . '.php';
    }
  );
  session_start(Config::get('session/options'));
  //end of initializatons
  $last_page = (Session::lastPageExists()) ? Session::getLastPage() : '';
  $username = Session::get('user');
  if(Session::exists('user')){
    $menu = new Menu();
    $menus = $menu->get($username);
  }else{
    Redirect::to('login.php');
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
              <a class="dropdown-item">
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
                      <li class="nav-item"> <a class="nav-link" onclick="getPage('<?php echo Utility::escape($child->url) ?>')" href="#"><?php echo Utility::escape($child->display_name) ?></a></li>
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

          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
              <i class="icon-layout menu-icon"></i>
              <span class="menu-title">UI Elements</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
                <li class="nav-item"> <a class="nav-link" href="pages/ui-features/dropdowns.html">Dropdowns</a></li>
                <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
              <i class="icon-columns menu-icon"></i>
              <span class="menu-title">Form elements</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="form-elements">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="pages/forms/basic_elements.html">Basic Elements</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#charts" aria-expanded="false" aria-controls="charts">
              <i class="icon-bar-graph menu-icon"></i>
              <span class="menu-title">Charts</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="charts">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="pages/charts/chartjs.html">ChartJs</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#tables" aria-expanded="false" aria-controls="tables">
              <i class="icon-grid-2 menu-icon"></i>
              <span class="menu-title">Tables</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="tables">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="pages/tables/basic-table.html">Basic table</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">
              <i class="icon-contract menu-icon"></i>
              <span class="menu-title">Icons</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="icons">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="pages/icons/mdi.html">Mdi icons</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">User Pages</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="auth">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="pages/samples/login.html"> Login </a></li>
                <li class="nav-item"> <a class="nav-link" href="pages/samples/register.html"> Register </a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#error" aria-expanded="false" aria-controls="error">
              <i class="icon-ban menu-icon"></i>
              <span class="menu-title">Error pages</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="error">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="pages/samples/error-404.html"> 404 </a></li>
                <li class="nav-item"> <a class="nav-link" href="pages/samples/error-500.html"> 500 </a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="pages/documentation/documentation.html">
              <i class="icon-paper menu-icon"></i>
              <span class="menu-title">Documentation</span>
            </a>
          </li>
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
  <!--Div to hold custom JS -->



  <!--My JS -->
  <script src="scripts/script.js"></script>
  <script src="scripts/portalscript.js"></script>
  <script src="scripts/ajaxrequest.js"></script>
  <script src="scripts/main.js"></script>
  <script src="ld_loader/ld_loader.js"></script>
  <script>
    const lastPage = document.getElementById('lastpage').value;
    if (lastPage.length > 0) {
      getPage(lastPage);
    }
  </script>
  <!--End of My JS -->
  <!-- End custom js for this page-->
</body>

</html>