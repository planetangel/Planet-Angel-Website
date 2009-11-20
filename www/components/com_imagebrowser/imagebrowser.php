<?php
/**
* @package		Joomla
* @subpackage	com_imagebrowser
* @copyright	Copyright (C) 2008 E-NOISE.COM LIMITED. All rights reserved.
* @license		GNU/GPL.
* @author 		Luis Montero [e-noise.com]
* @version 		0.1.8
* Joomla! and com_imagebrowser are free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed they include or
* are derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// Include all components files 
require_once JPATH_COMPONENT_ADMINISTRATOR.DS."inc".DS."sorting.php";

// include config model
require_once JPATH_COMPONENT_ADMINISTRATOR.DS."models".DS."config.php";

// Include language helper file if 'JLanguageHelper' class doesn't exist
if (!class_exists('JLanguageHelper')) {
	jimport('joomla.language.helper');
}

// Load component config
$modelConfig = new imagebrowserModelConfig();
$imagebrowserConfig = $modelConfig->config;

// Include language files 
$langfile = JPATH_COMPONENT_ADMINISTRATOR.DS.'lang'.DS.$imagebrowserConfig['language'].'.php';
if (file_exists($langfile)) {
    require_once $langfile;
} else {
	$langfile = JPATH_COMPONENT_ADMINISTRATOR.DS.'lang'.DS.'en-GB.php';
	if (file_exists($langfile)) {
		require_once $langfile;
	} else {
		die( 'Files have not been located:<br />/lang/'.$imagebrowserConfig['language'].'.php<br />/lang/en-GB.php' );
	}
}

// Include overall stylesheet from current ioffice template
$document =& JFactory::getDocument();
$document->addStyleSheet('components/com_imagebrowser/styles.css');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Create the controller
$controller = new imagebrowserController($imagebrowserConfig);
// Execute task
$controller->execute(JRequest::getCmd('task'));	
// Redirect if set by the controller
$controller->redirect();
?>