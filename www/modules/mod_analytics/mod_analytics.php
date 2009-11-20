<?php
/** Estime Oy - Google Analytics tracking code 
PLEASE NOTE THAT THIS MODULE IS COPYRIGHTED. YOU CAN NOT USE THIS MODULE CODE UNLESS
YOU FIRST ASK PERMISSION FROM THE OWNER.
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// pick up parameters for configuring
$state = $params->get( 'state', 0 );
$udn = $params->get( 'udn', 0 );
$ucode = $params->get( 'ucode', 0 );
$https = $params->get( 'https', 0 );


// other needed parameters
$output = '';
$secureurl = 0;


if($ucode && $state==1){
	if($https){
		if ($_SERVER['HTTPS']) {
			$secureurl = 1;
		}else{
			$secureurl = 0;
		}
	}else{
		$secureurl = 0;
	}
	
	switch ($secureurl) {
		case 0:
			$output = "<script src=\"http://www.google-analytics.com/urchin.js\" type=\"text/javascript\">\n";
			break;
		case 1:
			$output = "<script src=\"https://ssl.google-analytics.com/urchin.js\" type=\"text/javascript\">\n";
			break;
	}
	
	$output .= "</script>\n";
	$output .= "<script type=\"text/javascript\">\n";
	$output .= "_uacct =\"" . $ucode . "\";\n";
	if(strlen($udn)>1){	
		$output .= "_udn=\"" . $udn . "\";\n";
	}
	
	$output .= "urchinTracker();\n";
	$output .= "</script>\n";
    echo $output;
}
?>