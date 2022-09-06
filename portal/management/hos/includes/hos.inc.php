<?php
require_once '../../error_reporting.php';
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');
//end of initializatons

$url = new Url();
$alert = new Alert();
$req = new Request();
$hos = new Hos();
if (!$hos->isRemembered()) { //runs for people that are not logged in and automatically log in those that have cookie
    Session::setLastPage($url->getCurrentPage());
    Session::set_flash('welcome back', '');
    Redirect::home('login.php', 1);
}
$data = $hos->data();
$id_col = $hos->getIdColumn();
$user_col = $hos->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $hos->getRank();
if ($rank !== 5 && $rank !== 17) {
    exit(); // exits the page if the user is not the HOS
}

$sch_abbr = Utility::escape($data->sch_abbr);
//checks if students needed to be assigned a class
$noStdNeedClass = $hos->getNoStudentsNeedsClass($sch_abbr);
if ($noStdNeedClass > 0 && basename(Utility::myself()) !== 'assign_student.php') { //checks if some students are not assigned classes and if it is not the assign class page
    echo '<p style="font-weight: bold" class="text-right">' . $noStdNeedClass . ' students needs to be assigned a class. <a onclick="getPage(\'management/hos/assign_student.php\')" href="#">Assign Here</a></p>';
}
if (!$hos->isStdsSubRegComplete($sch_abbr) && basename(Utility::myself()) !== 'need_to_reg_sub.php') {
    echo '<p style="font-weight: bold" class="text-right">Some students are yet to complete their Subject Registration. <a onclick="getPage(\'management/hos/need_to_reg_sub.php\')" href="#">View Here</a></p>';
}
$utils = new Utils();
$currTerm = $utils->getCurrentTerm($sch_abbr);
$currSession = $utils->getSession($sch_abbr);


if ((Input::submitted('post') || Input::submitted('get')) && Token::check(Input::get('page_token'), 'page_token')) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $url = trim(Utility::escape($_SERVER['REQUEST_URI']));
        $url = str_replace('&amp;', '&', $url);
        $index = strpos($url, 'portal') + 7;
        $tokenIndex = strpos($url, "page_token");
        $len = $tokenIndex - $index;
        $page_url = substr($url, $index, $len - 1);
        Session::setLastPage($page_url);
    }
} else {
    exit('Kindly Access this page properly');
}

$count_alert = 0;
$count_request = 0;
$url = new Url();
//display alert
if ($alert->hasAlerts($username) && basename(Utility::myself()) != 'notifications.php') {
    $count_alert = $alert->getUnseenCount($username);
}

//display request
if ($req->hasRequests($rank) && basename(Utility::myself()) != 'requests.php') {
    $count_request = $req->getCount($rank);
}
$db = DB::get_instance();
?>
<input type="hidden" name="page_token" id="page_token" value="<?php echo Token::generate(32, 'page_token') ?>">
<input type="hidden" name="notCount" id="notCount" value="<?php echo $count_alert ?>">
<input type="hidden" name="reqCount" id="reqCount" value="<?php echo $count_request ?>">