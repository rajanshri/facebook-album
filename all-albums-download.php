<?php
ob_start();

// include common files
include 'common.inc.php';
include 'library/CreateZipFile.inc.php';
$createZipFile=new CreateZipFile;

//NEW FACEBOOK INSTANCE
$facebook = new Facebook(array('appId' => FB_APP_ID, 'secret' => FB_SECRET_KEY, 'cookie' => false));

  
//CREATING A NEW SESSION
$user = $facebook->getUser();


$today = time();

if(isset($_REQUEST['btn_download_all_albums']) && trim($_REQUEST['btn_download_all_albums']) != ''){
	$all_album_list = $_POST['chk_list'];
	if(is_array($all_album_list) && count($all_album_list) > 0){
		if ($user) {
			try {
				$user_profile = $facebook->api('/me', 'GET');
				$zip_directory_name = str_replace(' ','-',$user_profile['name'].'-albums').".zip";	
				$user_id = $user_profile['id'];
				if (!is_dir(ABSOLUTE_DOWNLOAD_PATH.$user_id)){
					mkdir(ABSOLUTE_DOWNLOAD_PATH.$user_id, 0777, true);
				}else{
					$func->removeDirectory(ABSOLUTE_DOWNLOAD_PATH.$user_id); 
					if (!is_dir(ABSOLUTE_DOWNLOAD_PATH.$user_id)){
						mkdir(ABSOLUTE_DOWNLOAD_PATH.$user_id, 0777, true);
					}
				}
													
				foreach($all_album_list as $album_id){
					$album_details = $facebook->api("/{$album_id}");
					if(is_array($album_details) && count($album_details) > 0){
						
						$directory_name = $album_details['name'];
						
						$directory_name_with_path = ABSOLUTE_DOWNLOAD_PATH.$user_id.'/'.$directory_name;
						if (!is_dir($directory_name_with_path)){
							mkdir($directory_name_with_path, 0777, true);
						}else{
							$func->removeDirectory($directory_name_with_path); 
							if (!is_dir($directory_name_with_path)){
								mkdir($directory_name_with_path, 0777, true);
							}
						}
						$album_photos = $facebook->api("/{$album_id}/photos");
						if(isset($album_photos) && is_array($album_photos['data']) && count($album_photos['data']) > 0){
							
							foreach($album_photos['data'] as $photos){
								$img_extention = pathinfo($photos['source'], PATHINFO_EXTENSION);
								$new_image_name = $photos['id'].".".$img_extention;
								
								copy($photos['source'], $directory_name_with_path.'/'.$new_image_name);
							}						
							
							$matches = glob($directory_name_with_path."/*"); 
							if ( is_array ( $matches ) ) { 
							   foreach ( $matches as $filename) { 								  
								  $createZipFile->addFile(file_get_contents($filename),$directory_name."/".basename($filename));
							   } 
							}
							$fd = fopen($zip_directory_name, "wb");
							$out = fwrite($fd, $createZipFile->getZippedfile());
							fclose($fd);
						}else{
							header("Location: " . ROOT_PATH);
							exit();
						}
					}else{
						header("Location: " . ROOT_PATH);
						exit();
					}
				}			
				
				
				$createZipFile->forceDownload($zip_directory_name);
				@unlink($zip_directory_name);		
			} catch (FacebookApiException $e) {
				header("Location: " . ROOT_PATH);
				exit();
			}
		} else {    
			header("Location: " . ROOT_PATH);
			exit();
		}
	}else{
		header("Location: " . ROOT_PATH);
		exit();
	}
}else{
	header("Location: " . ROOT_PATH);
	exit(); 
}



?>