<?php
	/////////////////////////////////////////////////////////////////////////////////////////
	define("rootsec", "true");
	/////////////////////////////////////////////////////////////////////////////////////////
	if (session_status() == PHP_SESSION_NONE) {
    		session_start();
	}
	/////////////////////////////////////////////////////////////////////////////////////////
	require_once($_SERVER["DOCUMENT_ROOT"]. '/core/classes/lang.class.php');
	/////////////////////////////////////////////////////////////////////////////////////////
	require_once($_SERVER["DOCUMENT_ROOT"]. '/core/libs/autoload.php');
	/////////////////////////////////////////////////////////////////////////////////////////
	spl_autoload_register(function ($className) {
		$classPath = $_SERVER["DOCUMENT_ROOT"]. '/core/classes/' . $className . '.class.php';
		
		if (file_exists($classPath)) {
			require_once($classPath);
		}
	});
	/////////////////////////////////////////////////////////////////////////////////////////
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();
	/////////////////////////////////////////////////////////////////////////////////////////
    	if ($_SERVER['HTTP_HOST'] == 'localhost') {
		define("DB_HOST", $_ENV['DB_HOST_LOCAL']);
		define("DB_USER", $_ENV['DB_USER_LOCAL']);
		define("DB_PASS", $_ENV['DB_PASS_LOCAL']);
		define("DB_NAME", $_ENV['DB_NAME_LOCAL']);
	} else {
		define("DB_HOST", $_ENV['DB_HOST_REMOTE']);
		define("DB_USER", $_ENV['DB_USER_REMOTE']);
		define("DB_PASS", $_ENV['DB_PASS_REMOTE']);
		define("DB_NAME", $_ENV['DB_NAME_REMOTE']);
	}
	/////////////////////////////////////////////////////////////////////////////////////////
	$currentDomain = $Helper->currentDomain();

	$DataBase = new DataBase($_LANG, DB_HOST, DB_USER, DB_PASS, DB_NAME);

	$Helper = new Helper($DataBase);
	$SiteConfig = new SiteConfig($DataBase);
	$UserManager = new UserManager($DataBase, $_LANG, $SiteConfig->getGoogleRecaptchaSecretKey());

	//////////////////////////////////////////////////////////////////////////////////////////	
?>
