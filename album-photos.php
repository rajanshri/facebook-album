<?php
ob_start();

// include common files
include 'common.inc.php';

//NEW FACEBOOK INSTANCE
$facebook = new Facebook(array('appId' => FB_APP_ID, 'secret' => FB_SECRET_KEY, 'cookie' => false));

  
//CREATING A NEW SESSION
$user = $facebook->getUser();


$today = time();

if(isset($_REQUEST['id']) && trim($_REQUEST['id']) != ''){
	$album_id = trim($_REQUEST['id']);
}else{
	header("Location: " . ROOT_PATH);
	exit(); 
}

if ($user) {
	try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me', 'GET'); //?fields=id,name,email,username,work,location,gender,birthday,link
		
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}

    if (!empty($user_profile )) {       
		$album_photos = $facebook->api("/{$album_id}/photos");
    } else {
        # For testing purposes, if there was an error, let's kill the script
        header("Location: " . ROOT_PATH);
		exit();
    }
} else {    
    header("Location: " . ROOT_PATH);
	exit();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo SITE_NAME; ?></title>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>superslides.css" />
        <script type="text/javascript" src="<?php echo JS_PATH; ?>/jquery-1.8.1.min.js"></script>
        
        
        <!--[if lt IE 9]>
        <script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <!--[if lt IE 10]>
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>styleIE.css" />
        <![endif]-->
        
        <script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.superslides.js"></script>
        <script type="text/javascript">
		var mainSlider;
		
		$(document).ready(function(){		  
			$('#slides').superslides({
				hashchange: false,
				play: 2000,
				//animation: 'fade',
				//animation_speed: 1000,
				//inherit_height_from: slider,
			});
			
			$('#slides').on('mouseenter', function() {
				$(this).superslides('stop');
				console.log('Stopped')
			});
			$('#slides').on('mouseleave', function() {
				$(this).superslides('start');
				console.log('Started')
			});
		});
		</script>
    </head>
    <body>
    	<div id="back_btn" style="position:absolute;right:50px;top:40px; z-index:9999;"><a href="<?php echo ROOT_PATH.'home.php'; ?>" title="Maximize"><img src="<?php echo IMAGES_PATH; ?>backbutton.png"></a></div>
    	<?php
		if(isset($album_photos) && is_array($album_photos['data']) && count($album_photos['data']) > 0){
		?>
    	<div id="slides">
            <div class="slides-container">
            	<?php
				foreach($album_photos['data'] as $photos){
					echo '<img src="'.$photos['images'][0]['source'].'" width="1024" height="682" alt="" />';
				}
				?>
            </div>
        
            <nav class="slides-navigation">
                <a href="" class="next">Next</a>
                <a href="" class="prev">Previous</a>
            </nav>
        </div>
        <?php
		}
		?>
    </body>
</html>
