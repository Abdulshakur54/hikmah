<?php
//initializations
require_once '../../error_reporting.php';
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
$acct = new Accountant();
if (!$acct->isRemembered()) { //runs for people that are not logged in and automatically log in those that have cookie
    Session::setLastPage($url->getCurrentPage());
    Redirect::home('login.php', 0);
}
$data = $acct->data();
$id_col = $acct->getIdColumn();
$user_col = $acct->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $acct->getRank();
if ($data->active != 1) {
    exit('Sorry! you have been made inactive on the portal');
}
if ($rank !== 3) {
    exit(); // exits the page if the user is not the Accountant
}

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