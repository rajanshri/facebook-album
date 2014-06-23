<?php
ob_start();

// include common files
include 'common.inc.php';

//NEW FACEBOOK INSTANCE
$facebook = new Facebook(array('appId' => FB_APP_ID, 'secret' => FB_SECRET_KEY, 'cookie' => false));
  
//CREATING A NEW SESSION
$user = $facebook->getUser();


$today = time();

if ($user) {
	try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me', 'GET'); //?fields=id,name,email,username,work,location,gender,birthday,link
		$facebook_logout_url = $facebook->getLogoutUrl(array(
		 	'next' => $site_url.'logout.php',  // Logout URL full path
		));
		
	   $access_token = $facebook->getAccessToken();
	  		
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}

    if (!empty($user_profile )) {
        # User info ok? Let's print it (Here we will be adding the login and registering routines)
		
		$user_id = $user_profile['id'];
		$user_fullname = $user_profile['name'];
		$username = $user_profile['username'];
		$user_email = $user_profile['email'];
		$user_location = $user_profile['location']['name'];		

		$user_album_details = array();
		$albums = $facebook->api('/me/albums');
		
		if(isset($albums) && is_array($albums['data']) && count($albums['data']) > 0){
			foreach($albums['data'] as $album){
				if(isset($album['count']) && $album['count'] >= 1){
					$album_id = $album['id'];
					$photo_id = $album['cover_photo'];
					$user_album['album_id'] = $album_id;
					$user_album['album_name'] = $album['name'];
					$user_album['album_link'] = $album['link'];
					$user_album['album_cover_photo_id'] = $album['cover_photo'];
					$user_album['album_count'] = $album['count'];
					
					$photos = $facebook->api("/{$photo_id}");
					if(isset($photos) && count($photos) > 0){
						//print_r($photos);
						$user_album['album_cover_photo'] = $photos['picture'];
					}
					
					$user_album_details[] = $user_album;
				}
			}
		}
        
    } else {
        # For testing purposes, if there was an error, let's kill the script
        die("There was an error.");
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
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>style.css" />
        <script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-1.8.1.min.js"></script>        
        
        <!--[if lt IE 9]>
        <script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <!--[if lt IE 10]>
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>styleIE.css" />
        <![endif]-->
        
        <script type="text/javascript" src="<?php echo JS_PATH; ?>all-common-functions.js"></script>
    </head>
    <body>
    	<div id="bodybg" style="background-color:#E9EAED;"></div>
        <div class="wrapper">
            <div style="top: 0px; left: 0px; width: 100%; height: 100%;">
                <div style="height:auto; width:900px; margin: 50px auto;">
                	<h1 style="color:#333333;">&#126; <?php echo SITE_NAME; ?> &#126;</h1>
        			
                    <div class="header-content">
                    	<div class="header-left-content">
                        &nbsp;
                        </div>
                        <div class="header-right-content-top">
                            <div class="header-right-content-top-left-panel">
                                <img src="<?php if(isset($user_id)){ echo 'https://graph.facebook.com/'.$user_id.'/picture'; } ?>" />
                            </div>
                            <div class="header-right-content-top-right-panel">
                                <div class="header-right-content-top-right-panel-top">
                                	<?php if(isset($user_fullname)){ echo $user_fullname; } ?><span style="float:right"><?php if(isset($user_location)){ echo ' | '.$user_location; } ?></span>
                                </div>
                                <div class="header-right-content-top-right-panel-bottom-left">
                                	@<?php if(isset($username)){ echo $username; } ?>
                                </div>
                                <div class="header-right-content-top-right-panel-bottom-right">
                                	<a href="<?php if(isset($facebook_logout_url)){ echo $facebook_logout_url; } ?>">Sign Out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="header-content">
                    	<div class="header-left-content">
                        Hi! <?php if(isset($user_fullname)){ echo $user_fullname; } ?>
                        </div>
                        <form method="post" action="<?php echo ROOT_PATH; ?>download.php" target="_blank" onSubmit="return tweetDownload();">
                        <input type="hidden" name="login_user_screen_name" value="<?php if(isset($login_user_screen_name)){ echo $login_user_screen_name; } ?>" />
                        <div class="header-right-content">
                        &nbsp;
                        </div>
                        </form>
                    </div>                               
                                        
                    <br clear="all" />
                    <?php
					if(isset($user_album_details) && count($user_album_details) >0){
					?>
                    <form action="<?php echo ROOT_PATH.'all-albums-download.php'; ?>" method="post" target="_blank" onSubmit="return downloadSelectedAlbums();">
                    <div class="header-content">
                    	<div class="header-left-content">                        
                        <input type="checkbox" name="select_all_checkbox" id="select_all_checkbox" value="1" onClick="check_all_check_box($(this));" /> 
                        <input type="submit" name="btn_download_all_albums" value="Download Albums Now" />                 
                        </div>
                    </div>
                    <br clear="all" />
                    <div>
                        <?php
						foreach($user_album_details as $user_album){//print_r($user_album);
						?>
                    	<div style="width:45%; display:inline; float:left; background-color:#4E5866; color:#000; padding:10px; height:100px; margin:10px; 5px;">
                        	<div style="width:100%; height:75px; margin:3px 0;">
                            	<div style="width:20px; float:left; display:inline; margin-right:2px;">
                                <input type="checkbox" name="chk_list[]" value="<?php echo $user_album['album_id']; ?>" onClick="click_check_box();" />
                                </div>
                                <div style="width:150px; float:left; display:inline; margin-right:2px;">
                                    <a href="<?php echo ROOT_PATH.'album-photos.php?id='.$user_album['album_id']; ?>"><img src="<?php echo $user_album['album_cover_photo']; ?>" style="width:130px; height:73px; border:1px solid #000;" /></a>
                                    
                                </div>
                                <div style="float:left; display:inline; color: #FFF;">
                                    <div style="width:100%; font-weight:bold; margin-left: 10px;" id="download_album_link_<?php echo $user_album['album_id']; ?>">
                                        <a href="javascript:void(0);" onClick="return getUserAlbumDownload('<?php echo $user_album['album_id']; ?>');" style="color:#FFF;">Download This Album</a>
                                    </div>
                                    <div style="width:100%; font-weight:bold; margin-left: 10px;" id="download_zip_link_<?php echo $user_album['album_id']; ?>" style="display:none;">
                                        
                                    </div>
                                </div>
                            </div>                            
                            <div style="width:97%; margin:3px 0 3px 20px; float:left;">
                            	<a href="<?php echo ROOT_PATH.'album-photos.php?id='.$user_album['album_id']; ?>" style="color:#FFF;"><?php echo $user_album['album_name']; ?> [<?php echo $user_album['album_count']; ?> <?php if($user_album['album_count'] > 1) { echo 'Photos'; }else{ echo 'Photo'; } ?>]</a>
                            </div>
                        </div>
                        <?php
						}
						?>                 
                    	                     
                        
                    </div>
                    <?php
					}
					?>
                    </form>
                </div>
            </div>
        </div>
    
    </body>
</html>
