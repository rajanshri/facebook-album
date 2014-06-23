<?php

session_start();

// include common files
include 'common.inc.php';

//NEW FACEBOOK INSTANCE
$facebook = new Facebook(array('appId' => FB_APP_ID, 'secret' => FB_SECRET_KEY, 'cookie' => false));

if(isset($_GET['action']) && $_GET['action'] === 'logout'){
	$facebook->destroySession(); 
}

session_destroy();

header('Location: '.ROOT_PATH);
exit;

?>