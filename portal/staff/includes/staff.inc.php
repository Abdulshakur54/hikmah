<?php

require_once '../error_reporting.php';
//initializations
spl_autoload_register(
    function ($class) {
        require_once '../../classes/' . $class . '.php';
    }
);
session_start(Config::get('session/options'));
date_default_timezone_set('Africa/Lagos');
//end of initializatons

$url = new Url();
$alert = new Alert();
$req2 = new Request2();
$staff = new Staff();
if (!$staff->isRemembered()) { //runs for people that are not logged in and automatically log in those that have cookie
    Session::setLastPage($url->getCurrentPage());
    Redirect::home('login.php', 0);
}
$data = $staff->data();
$id_col = $staff->getIdColumn();
$user_col = $staff->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $staff->getRank();
$allowedRank = [7, 8, 15, 16];
if ($data->active != 1) {
    exit('Sorry! you have been made inactive on the portal');
}
if (!in_array($rank, $allowedRank)) {
    exit(); // exits the page if the user is not a H.O.S
}
$sch_abbr = Utility::escape($data->sch_abbr);
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
if ($req2->hasRequests($username) && basename(Utility::myself()) != 'requests2.php') {
    $count_request = $req2->getCount($username);
}
$db = DB::get_instance();

?>
<input type="hidden" name="page_token" id="page_token" value="<?php echo Token::generate(32, 'page_token') ?>">
<input type="hidden" name="notCount" id="notCount" value="<?php echo $count_alert ?>">
<input type="hidden" name="reqCount" id="reqCount" value="<?php echo $count_request ?>">