<?php
if(!defined('rootsec')) {
   die('Direct access not permitted');
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

$lang_path = $_SERVER['DOCUMENT_ROOT'].'/core/lang/';

$supported_languages = ['en', 'me'];

if (in_array($_SESSION['lang'], $supported_languages)) {
    $lang_file = $lang_path . $_SESSION['lang'] . '.lang.php';
    include_once($lang_file);
} else {
    $_SESSION['lang'] = "en";
    include_once($lang_path . 'en.lang.php');
}

?>
