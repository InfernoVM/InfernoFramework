<?php
/************************************************
 *  InfernoFramework  
 *  https://github.com/InfernoVM/InfernoFramework  
 *  
 *  Powered by InfernoVM  
 *  Fast, Secure, and Reliable PHP Framework  
 ************************************************/
require_once($_SERVER["DOCUMENT_ROOT"] . '/core/init.php');

define('PAGES_PATH', __DIR__ . '/core/pages/');

$page = isset($_GET['page']) && !empty(trim($_GET['page'])) ? trim($_GET['page']) : 'home';

$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

switch($page) {
    case "home":
        $headerFile = PAGES_PATH  . 'partials/header.php';
        $pageFile = PAGES_PATH.'home.php';
        $footerFile = PAGES_PATH . 'partials/footer.php';
    break;
}


if (file_exists($pageFile)) {
    include $headerFile;
    include $pageFile;
    include $footerFile;
} else {
    http_response_code(404);
    include PAGES_PATH . '404.php';
}
?>
