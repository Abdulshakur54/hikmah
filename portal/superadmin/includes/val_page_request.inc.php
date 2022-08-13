<?php

if (!Input::submitted('get') || !Token::check(Input::get('page_token'), 'page_token')) {
    exit('Kindly Access this page properly');
}
$url = trim(Utility::escape($_SERVER['REQUEST_URI']));
$url = str_replace('&amp;', '&', $url);
$index = strpos($url, 'portal') + 7;
$tokenIndex = strpos($url, "page_token");
$len = $tokenIndex - $index;
$page_url = substr($url, $index, $len - 1);
Session::setLastPage($page_url);
?>
<input type="hidden" name="notCount" id="notCount" value="0">
<input type="hidden" name="reqCount" id="reqCount" value="0">