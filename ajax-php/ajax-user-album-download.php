<?php

ob_start();

// include common files
include '../common.inc.php';
include '../library/JSON.php';
include '../library/CreateZipFile.inc.php';
$createZipFile=new CreateZipFile;

//NEW FACEBOOK INSTANCE
$facebook = new Facebook(array('appId' => FB_APP_ID, 'secret' => FB_SECRET_KEY, 'cookie' => false));
  
//CREATING A NEW SESSION
$user = $facebook->getUser();

$data = array();
$html_content = '';
$ip=getenv('REMOTE_ADDR');
$today = time();

if($user){
	if(isset($_POST['album_id']) && trim($_POST['album_id']) != ''){
		$album_id = trim($_POST['album_id']);	
		
		try {
			$album_details = $facebook->api("/{$album_id}");
			if(is_array($album_details) && count($album_details) > 0){
				$user_id = $album_details['from']['id'];
				if (!is_dir(ABSOLUTE_DOWNLOAD_PATH.$user_id)){
					mkdir(ABSOLUTE_DOWNLOAD_PATH.$user_id, 0777, true);
				}else{
					$func->removeDirectory(ABSOLUTE_DOWNLOAD_PATH.$user_id); 
					if (!is_dir(ABSOLUTE_DOWNLOAD_PATH.$user_id)){
						mkdir(ABSOLUTE_DOWNLOAD_PATH.$user_id, 0777, true);
					}
				}
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
					
					$data['ErrorCode'] = 0;
					$data['Content'] = '<a href="'.ROOT_PATH.'album-download.php?id='.$album_id.'" target="_blank" style="color:#FFF;">Click to download zip file</a>';
				}else{
					$data['ErrorCode'] = 1;
					$data['ErrorMessage'] = "There is no photo in this album.";
				}
			}else{
				$data['ErrorCode'] = 1;
				$data['ErrorMessage'] = "There is no such album. Please try another.";
			}
		}catch (FacebookApiException $e) {
			$data['ErrorCode'] = 1;
			$data['ErrorMessage'] = $e;
		}		
	}else{
		$data['ErrorCode'] = 1;
		$data['ErrorMessage'] = "There are some internal problem. Please refresh the page and try again.";
	}
}else{
	$data['ErrorCode'] = 1;
	$data['ErrorMessage'] = "Please login with Facebook";
}


die(json_encode($data));

?>