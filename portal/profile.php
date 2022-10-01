<?php
//initializations

spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');
if (Input::submitted('get') && !empty(Input::get('username'))) {
    $username = Input::get('username');
} else {
    Redirect::to(404);
}
$profile_data = User::get_profile($username);
if (empty($profile_data)) {
    Redirect::to(404);
}
$url = new Url();
$specifics = User::get_specifics($username, $profile_data->rank, (empty($profile_data->asst)) ? 0 : $profile_data->asst);
$hikmah_logo = $url->to('apm/uploads/logo/hikmah.jpg', 1);
$profile_photo = $specifics['profile_photo_path'] . $profile_data->picture;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        @media screen and (max-width: 992px) {
            .noleftpadding {
                padding-left: 0px;
            }
        }
    </style>
</head>

<body>
    <section style="background-color: #eee;">
        <div class="container-sm">
            <div class="row">
                <div class="col px-2">
                    <nav aria-label="breadcrumb" class="bg-light  p-3 pb-4 d-flex align-items-center bg-white">
                        <div class="me-auto" style="width:5rem;">
                            <img src="<?php echo $hikmah_logo ?>" class="d-block w-100" />
                        </div>
                        <div class="fs-1 text-dark pe-3">
                            Profile Page
                        </div>
                    </nav>
                </div>
            </div>

            <div class="row d-flex align-items-stretch flex-row px-2 mt-3">
                <div class="col-lg-4 d-flex align-items-center py-3" style="background-color: #7db9fb;">
                    <div class="w-100">
                        <div class="card-body text-center">
                            <img src="<?php echo $profile_photo ?>" alt="profile photo" class="rounded-circle img-fluid mt-3" style="width: 150px; height: 150px;">
                            <h5 class="mt-4"><?php echo Utility::formatName($profile_data->fname, $profile_data->oname, $profile_data->lname) ?></h5>
                            <h6 class="mb-1"><?php echo $specifics['role'] ?></h6>
                            <h6><?php echo ($profile_data->sch_abbr === 'All') ? 'Hikmah Schools, Jos' : School::getFullName($profile_data->sch_abbr) ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 pe-0 noleftpadding">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Full Name</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo Utility::formatName($profile_data->fname, $profile_data->oname, $profile_data->lname, false) ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">User ID</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo strtoupper($username) ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Role</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $specifics['role'] ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">School</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo ($profile_data->sch_abbr === 'All') ? 'Hikmah Schools, Jos' : School::getFullName($profile_data->sch_abbr) ?></p>
                                </div>
                            </div>
                            <?php
                            if ($specifics['show_parents']) {
                            ?>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0">Father</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0"><?php echo ucwords($profile_data->fathername) ?></p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0">Mother</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0"><?php echo ucwords($profile_data->mothername) ?></p>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>

                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">State of Origin</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $profile_data->state_of_origin ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">LGA of Origin</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $profile_data->lga_of_origin ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Email</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo strtolower($profile_data->email) ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Address</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $profile_data->address ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>