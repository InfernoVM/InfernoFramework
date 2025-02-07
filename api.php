<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/core/init.php');

$requestData = $Helper->sanitizeRequestData(json_decode(file_get_contents('php://input'), true) ?? []);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'login':
            $email = $requestData['email'] ?? '';
            $password = $requestData['password'] ?? '';
            $recaptchaResponse = $requestData['recaptcha'] ?? '';

            $response = $UserManager->login($email, $password, $recaptchaResponse);
        break;
        case 'register':
            $username = $requestData['username'] ?? '';
            $email = $requestData['email'] ?? '';
            $password = $requestData['password'] ?? '';
            $recaptchaResponse = $requestData['recaptcha'] ?? '';
            
            $response = $UserManager->register($username, $email, $password, $recaptchaResponse);
        break;
        case 'forgotPassword':
            $email = $requestData['email'] ?? '';
            $recaptchaResponse = $requestData['recaptcha'] ?? '';

            $response = $UserManager->forgotPassword($email, $recaptchaResponse);
        break;
        case 'updateEmail':
            $Helper->isUserLoggedIn();

            $userId = $requestData['userid'] ?? '';
            $oldEmail = $requestData['oldEmail'] ?? '';
            $newEmail = $requestData['newEmail'] ?? '';

            $response = $UserManager->updateEmail($userId, $oldEmail, $newEmail);
        break;
        case 'updatePassword':
            $Helper->isUserLoggedIn();

            $UserID = $_SESSION['user_id'];
            $oldPassword = $requestData['oldPassword'] ?? '';
            $newPassword = $requestData['newPassword'] ?? '';
            
            $response = $UserManager->updatePassword($userId, $oldPassword, $newPassword);
        break;
        case 'updateTheme':
            $Helper->isUserLoggedIn();

            $UserID = $_SESSION['user_id'];
            $Theme = $requestData['theme'] ?? '';

            $response = $UserManager->updateTheme($UserID, $Theme);
        break;
        default:
            $response = ['type' => 'error', 'message' => $_LANG["general"]["invact"]];
        break;
    }
} else {
    $response = ['type' => 'error', 'message' => $_LANG["general"]["misspar"]];
}

$Helper->sendResponse($response);
?>
