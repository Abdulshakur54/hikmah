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
    Session::set_flash('welcome back', '');
    Redirect::home('login.php', 1);
}
$data = $staff->data();
$id_col = $staff->getIdColumn();
$user_col = $staff->getUsernameColumn();
$id = $data->$id_col;
$username = $data->$user_col;
$rank = $staff->getRank();
$allowedRank = [7, 8, 15, 16];
if (!in_array($rank, $allowedRank)) {
    exit(); // exits the page if the user is not a H.O.S
}
$sch_abbr = Utility::escape($data->sch_abbr);
$utils = new Utils();
$currTerm = $utils->getCurrentTerm($sch_abbr);
$currSession = $utils->getSession($sch_abbr);

$msg = '';
require_once './includes/sub_teacher.inc.php';

if (Input::submitted() && Token::check(Input::get('token'))) { //page has been submitted via post method
    $utils = new Utils();
    $table = $utils->getFormattedSession($sch_abbr) . '_score';
    $column = Utility::escape(Input::get('dtn'));
    $maxScore = Utility::escape(Input::get('maxscore'));
    switch ($currTerm) {
        case 'ft':
            $tableColumn = 'ft_' . $column;
            break;
        case 'st':
            $tableColumn = 'st_' . $column;
            break;
        case 'tt':
            $tableColumn = 'tt_' . $column;
            break;
    }
    $transfer = ['tableName' => $table, 'tableColumn' => $tableColumn, 'maxScore' => $maxScore, 'idColumn' => 'std_id', 'subid' => $subId];
    Redirect::to($url->to('new_exam.php?transfer=' . json_encode($transfer), 4));
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
if ($req2->hasRequests($username) && basename(Utility::myself()) != 'requests2.php') {
    $count_request = $req2->getCount($username);
}
$db = DB::get_instance();

?>
<input type="hidden" name="page_token" id="page_token" value="<?php echo Token::generate(32, 'page_token') ?>">
<input type="hidden" name="notCount" id="notCount" value="<?php echo $count_alert ?>">
<input type="hidden" name="reqCount" id="reqCount" value="<?php echo $count_request ?>">



<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Set E-Exam for Students</h4>
            <?php
            $scoreSettings = Subject::getScoreSettings($sch_abbr);

            ?>
            <form class="forms-sample" id="examForm" onsubmit="return false" novalidate method="post" action="<?php echo Utility::myself() ?>">
                <div class="form-group">
                    <label for="school">Category</label>
                    <select class="js-example-basic-single w-100 p-2" name="dtn" id="dtn" title="Category" required>
                        <?php
                        if ($scoreSettings['fa'] > 0) {
                            echo '<option value="fa">First Assignment (' . $scoreSettings['fa'] . ')</option>';
                        }
                        if ($scoreSettings['sa'] > 0) {
                            echo '<option value="sa">Second Assignment (' . $scoreSettings['sa'] . ')</option>';
                        }
                        if ($scoreSettings['ft'] > 0) {
                            echo '<option value="ft">First Test (' . $scoreSettings['ft'] . ')</option>';
                        }
                        if ($scoreSettings['st'] > 0) {
                            echo '<option value="st">Second Test (' . $scoreSettings['st'] . ')</option>';
                        }
                        if ($scoreSettings['pro'] > 0) {
                            echo '<option value="pro">Project (' . $scoreSettings['pro'] . ')</option>';
                        }
                        if ($scoreSettings['exam'] > 0) {
                            echo '<option value="exam">Exam (' . $scoreSettings['exam'] . ')</option>';
                        }

                        ?>
                    </select>

                </div>
                <div id="scores" class="none">
                    <?php echo json_encode($scoreSettings) ?>
                </div>
                <input type="hidden" name="token" id="token" value="<?php echo Token::generate() ?>" />
                <input type="hidden" name="maxscore" id="maxscore" />
                <input type="hidden" name="subid" id="subid" value="<?php echo $subId ?>" />
                <div id="msg"><?php echo $msg; ?></div>
                <button type="button" class="btn btn-primary mr-2" id="continue" onclick="submitForm()">Continue</button>
                <button type="button" class="btn btn-light" onclick="getAltPage('<?php echo Utility::escape(Session::getAltLastPage()) ?>')" id="returnBtn">Return</button>
            </form>
        </div>
    </div>
</div>
<script src="scripts/staff/set_exam.js"></script>
<script>
    validate('examForm');;
</script>