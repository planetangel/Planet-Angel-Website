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

// Include all components files 
require_once dirname(__FILE__) . '/admin.imagebrowser.html.php';
require_once dirname(__FILE__) . '/imagebrowser.class.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS."models".DS."config.php";

// Include other files 
require_once dirname(__FILE__) . '/inc/image.class.php';
require_once dirname(__FILE__) . '/inc/sorting.php';
require_once dirname(__FILE__) . '/inc/upload.php';

// Include language helper file if 'JLanguageHelper' class doesn't exist
if (!class_exists('JLanguageHelper')) {
	jimport('joomla.language.helper');
}

// Init Request Variables
$task 			= JRequest::getString('task', '');
$option 		= JRequest::getVar('option', 'com_imagebrowser');
$folder			= JRequest::getVar('folder', '');
$file			= JRequest::getVar('file', '');
$new_folder 	= JRequest::getVar('new_folder', '');
$delete_folder 	= JRequest::getVar('delete_folder', '');
$oldname 		= JRequest::getVar('oldname', '');
$newname 		= JRequest::getVar('newname', '');
$new_parent_folder = JRequest::getVar('new_parent_folder', '');
$e_name 		= JRequest::getVar('e_name', '');

// Instantiate imageBrowser class
$imageBrowser = new imageBrowser($option, $folder); 

// Include language files
$langfile = dirname(__FILE__) . '/lang/'.$imageBrowser->config['language'].'.php';
if (file_exists($langfile)) {
    require_once $langfile;
} else {
	$langfile = dirname(__FILE__) . '/lang/en-GB.php';
	if (file_exists($langfile)) {
		require_once $langfile;
	} else {
		die( 'Files have not been located:<br />/lang/'.$imageBrowser->config['language'].'.php<br />/lang/en-GB.php' );
	}
}

// Main task switch
switch($task){
	case 'generate_thumb' :
		$imageBrowser->generateThumb($file);
		break;
		
	case 'generate_folder_thumbs' :
		$imageBrowser->generateFolderThumbs();
		break;
		
	case 'resize_folder_images' :
		$imageBrowser->resizeFolderImages();
		break;
		
	case 'resize_images' :
		$imageBrowser->resizeImages($file);
		break;
		
	case 'edit_caption' :
		$imageBrowser->editCaption($file);
		break;
		
	case 'save_caption' :
		$imageBrowser->saveCaption($file);
		break;
		
	case 'new_folder' : 
		$imageBrowser->newFolder($new_folder);
		break;
		
	case 'delete_folder' : 
		$imageBrowser->deleteFolder($delete_folder);
		break;
	
	case 'rename_folder_form' :
		$imageBrowser->renameFolderForm($oldname);
		break;
		
	case 'rename_folder' : 
		$imageBrowser->renameFolder($new_parent_folder, $oldname, $newname);
		break;
		
	case 'upload_file' : 
		$imageBrowser->uploadFile();
		break;
		
	case 'delete_file' : 
		$imageBrowser->deleteFile($file);
		break;
		
	case 'plugin' : 
		$imageBrowser->plugin($e_name);
		break;
		
	default :
		$imageBrowser->imageBrowserBackend();
		break;
}

require_once dirname(__FILE__) . '/inc/version.php';
?>