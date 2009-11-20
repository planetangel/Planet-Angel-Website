<?php
/**
* @package		Joomla
* @subpackage	com_imagebrowser
* @copyright	Copyright (C) 2008 E-NOISE.COM LIMITED. All rights reserved.
* @license		GNU/GPL.
* @author 		Luis Montero [e-noise.com]
* @version 		0.1.7b
* Joomla! and com_imagebrowser are free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed they include or
* are derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// define version as constant
define('_IMAGEBROWSER_VERSION', '0.1.8');

// function to check version with e-noise.com server and show link to download new version if available.
function version_check() {
	$version_check_url = 'http://www.e-noise.com/imagebrowser/api/version_check.php';
	$version_check_url .= '?version='.urlencode(_IMAGEBROWSER_VERSION);
	
	// If cURL functions are available we check version with remote server
	if (function_exists('curl_init')) {
		// create a new cURL resource
		$ch = curl_init();
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $version_check_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// grab URL and pass it to the browser
		$output = curl_exec($ch);	
		// close cURL resource, and free up system resources
		curl_close($ch);
	}
	// Otherwise we show link to image browser site and inform user that cURL is not available
	else {
		$output = '<div class="imagebrowser_warning">';
		$output .= 'Could not check if you are running the latest version';
		$output .= ' of Image Browser Component because <a href="http://uk3.php.net/curl" target="_blank">';
		$output .= 'cURL functions</a> are not enabled.<br />';
		$output .= '<a href="http://www.e-noise.com/imagebrowser" target="_blank">Click here to manually ';
		$output .= 'check for updates</a>';
		$output .= '</div>';
	}
		
	return $output;
}
?>

<div>
Image Browser Component v<?php echo _IMAGEBROWSER_VERSION; ?>
<br />
<?php echo version_check(); ?>
<br />
&copy; <a href="http://www.e-noise.com/" target="_blank">e-noise.com</a>
</div>