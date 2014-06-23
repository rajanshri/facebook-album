<?php
ob_start();

// include common files
include 'common.inc.php';



//NEW FACEBOOK INSTANCE
$facebook = new Facebook(array('appId' => FB_APP_ID, 'secret' => FB_SECRET_KEY, 'cookie' => false));

//CREATING A NEW SESSION
$user = $facebook->getUser();
//print_r($user);
if (isset($_REQUEST['state']) && isset($_REQUEST['code'])) {
	echo "<script>            
			window.close();
			window.opener.location.replace('".$site_url."home.php');						
        </script>";	
}

if ($user) {
	try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me', 'GET'); //?fields=id,name,email,username,work,location,gender,birthday,link
		print_r($user_profile);die;
	} catch (FacebookApiException $e) {
		//error_log($e);
		echo $error_msg = $e;die;
		$user = null;
	}

    if (!empty($user_profile )) {
        # User info ok? Let's print it (Here we will be adding the login and registering routines)
		$username = $user_profile['name'];
		$uid = $user_profile['id'];
		$email = $user_profile['email'];
		header("Location: home.php");
       
    } else {
        # For testing purposes, if there was an error, let's kill the script
        die("There was an error.");
    }
} else {
    # There's no active session, let's generate one
	$facebook_login_url = $facebook->getLoginUrl(array('scope' => 'email, user_about_me, user_birthday, publish_stream, user_photos', 'redirect_uri' => $site_url.'index.php', 'display'=>'popup'));
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
	<title><?php echo SITE_NAME; ?></title>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>style.css" />
	<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-supersized-3.2.7.js"></script>
	<!--[if lt IE 9]>
	<script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
    <!--[if lt IE 10]>
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>styleIE.css" />
	<![endif]-->
</head>
<body >
	<div id="bodybg"></div>
    <div class="wrapper">
        <div class="container">
            <div class="sp-container">
            	<h1>&#126; <?php echo SITE_NAME; ?> &#126;</h1>
            
                <div class="sp-content">
                    <!-- LEFT TEXT -->
                    <div class="sp-wrap sp-left">
                        <h2>
                            <span class="sp-top">We're nearly there</span> 
                            <span class="sp-mid">facebook</span> 
                            <span class="sp-bottom">get your ablums</span>
                        </h2>
                    </div>
                    <!-- RIGHT TEXT -->
                    <div class="sp-wrap sp-right">
                        <h2>
                            <span class="sp-top">Not long now</span> 
                            <span class="sp-mid">album! <i>...</i><i>...</i></span> 
                            <span class="sp-bottom">get blbum pictures</span>
                        </h2>
                    </div>
                </div>
            	<!-- BIG TEXT AND LINK BUTTON -->
                <div class="sp-full">
                    <h2>Like to know when we're ready?</h2>                    
                    <a href="javascript:void(0);" onclick='window.open("<?php echo $facebook_login_url; ?>","_blank","toolbar=no, scrollbars=no, resizable=yes, top=150, left=390, width=560, height=300");'>Sign in with Facebook!</a>
                </div>
            </div>
        </div>	
    
    </div>
	
	<!-- YOUR BACKGROUND IMAGE SETTINGS -->
	<script type="text/javascript">
		jQuery(function($){
			$.supersized({
				transition : 0,
				slides : [
					{image : '<?php echo IMAGES_PATH; ?>4.jpg'}
				]
			});
		});
    </script>
</body>
</html>