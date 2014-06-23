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

if(isset($_REQUEST['id']) && trim($_REQUEST['id']) != ''){
	$album_id = trim($_REQUEST['id']);
}else{
	header("Location: " . ROOT_PATH);
	exit(); 
}

if ($user) {
	try {
		$album_details = $facebook->api("/{$album_id}");
		if(is_array($album_details) && count($album_details) > 0){
			$user_id = $album_details['from']['id'];
			$directory_name = $album_details['name'];
			$zip_directory_name = str_replace(' ','-',$album_details['name']).".zip";
			$directory_name_with_path = ABSOLUTE_DOWNLOAD_PATH.$user_id.'/'.$directory_name;
			
			if(is_dir($directory_name_with_path)){
				
				$matches = glob($directory_name_with_path."/*"); 
				if ( is_array ( $matches ) ) { 
				   foreach ( $matches as $filename) {
					  $createZipFile->addFile(file_get_contents($filename),$directory_name."/".basename($filename));
				   } 
				}
				
				$fd = fopen($zip_directory_name, "wb");
				$out = fwrite($fd, $createZipFile->getZippedfile());
				fclose($fd);
				$createZipFile->forceDownload($zip_directory_name);
				@unlink($zip_directory_name);
				
			}else{
				header("Location: " . ROOT_PATH);
				exit();
			}
		}else{
			header("Location: " . ROOT_PATH);
			exit();
		}		
	} catch (FacebookApiException $e) {
		header("Location: " . ROOT_PATH);
		exit();
	}
} else {    
    header("Location: " . ROOT_PATH);
	exit();
}

?>