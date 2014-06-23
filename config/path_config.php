<?php
    
    define('ABSOLUTE_PATH', dirname(dirname(__FILE__))); //Set your server absolute path
    defined('ROOT_PATH') || define('ROOT_PATH', 'http://www.domain_name.com/'); //Enter the full domain path name
    defined('CSS_PATH') || define('CSS_PATH', ROOT_PATH . 'css/');
    defined('JS_PATH') || define('JS_PATH', ROOT_PATH . 'js/');
    defined('IMAGES_PATH') || define('IMAGES_PATH', ROOT_PATH . 'images/');
    defined('INCLUDE_PATH') || define('INCLUDE_PATH', ROOT_PATH . 'include/'); 
	
	define('SITE_NAME', 'Facebook Album');
	
	defined('FB_APP_ID') || define('FB_APP_ID', 'XXXXXXXXXXXXXXX'); //Enter your Facebook App ID
	defined('FB_SECRET_KEY') || define('FB_SECRET_KEY', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX'); //Enter your Facebook Secret Key
	
	defined('ABSOLUTE_DOWNLOAD_PATH') || define('ABSOLUTE_DOWNLOAD_PATH', ABSOLUTE_PATH . '/download/');

?>