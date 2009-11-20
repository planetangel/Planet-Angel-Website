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
  
function custom_uploadFile($fieldName, $dir, $accept, $max_upload_size=0, $overwrite=0) {
	$file_tmp = $_FILES[$fieldName]['tmp_name']; // $file_tmp is where file went on webserver
	$file_name = $_FILES[$fieldName]['name']; // $file_tmp_name is original file name
	$file_size = $_FILES[$fieldName]['size']; // $file_size is size in bytes
	$file_type = $_FILES[$fieldName]['type']; // $file_type is mime type e.g. image/gif
	$file_error = $_FILES[$fieldName]['error']; // $file_error is any error encountered
	
	// check for generic errors first		  
	if ($file_error > 0) {
		echo "<script> alert('";
		switch ($file_error) {
		  case 1:  echo JText::_(_IMAGEBROWSER_UPLOAD_ERROR_PHP_UP_MAX_FILESIZE, true);  break;
		  case 2:  echo JText::_(_IMAGEBROWSER_UPLOAD_ERROR_PHP_MAX_FILESIZE, true);  break;
		  case 3:  echo JText::_(_IMAGEBROWSER_UPLOAD_ERROR_PARTIAL_UPLOAD, true);  break;
		  case 4:  echo JText::_(_IMAGEBROWSER_UPLOAD_ERROR_NO_FILE, true);  break;
		}
		echo "'); window.history.go(-1); </script>\n";
		exit;
	}
	
	// check custom max_upload_size passed into the function
	if ($max_upload_size < $file_size) {
		echo "<script> alert('";
		echo JText::_(_IMAGEBROWSER_UPLOAD_ERROR_MAX_FILESIZE, true);
		echo ' max_upload_size: '.$max_upload_size.' | file_size: '.$file_size;
		echo "'); window.history.go(-1); </script>\n";
		exit;
	}
	
	// Checkeamos el MIME type con la lista que formatos validos ($accept - valores separados por ',')
	$valid_file_types = explode(",", $accept);
	$type_ok = 0;
	
	foreach ($valid_file_types as $type) {
		if ($file_type == $type) {
			$type_ok = 1;
		}
	}
	
	if ($type_ok == 0) {
		echo "<script> alert('";
		echo JText::_(_IMAGEBROWSER_UPLOAD_ERROR_FILETYPE, true);
		echo "'); window.history.go(-1); </script>\n";
		exit;
	}
	
	// CHECK FOR SPECIAL CHARACTERS
	// [#10991] spezial chars in function custom_uploadFile - Submitted by Ra Jani
	$file_name = preg_replace('/[^a-z0-9\.\-\_]/i', '', $file_name);
	
	// BEFORE WE MOVE THE FILE TO IT'S TARGET DIRECTORY 
	// WE CHECK IF A FILE WITH THE SAME NAME EXISTS IN THE TARGET DIRECTORY
	if (empty($overwrite)) {
	  $check_if_file_exists = file_exists($dir.$file_name);
	  if ($check_if_file_exists === true) {
		// split file name into name and extension
		$split_point = strrpos($file_name, '.');
		$file_n = substr($file_name, 0, $split_point);
		$file_ext = substr($file_name, $split_point);
		$i=0;
		while (true === file_exists($dir.$file_n.$i.$file_ext)) {
			$i++;
		}
		$file_name = $file_n.$i.$file_ext;
	  }
	}
	
	// put the file where we'd like it
	$path = $dir.$file_name;
	// is_uploaded_file and move_uploaded_file added at version 4.0.3
	if (is_uploaded_file($file_tmp)) {
		if (!move_uploaded_file($file_tmp, $path)) {
			echo "<script> alert('";
			echo JText::_(_IMAGEBROWSER_UPLOAD_ERROR_MOVE, true);
			echo "'); window.history.go(-1); </script>\n";
			exit;
		}
	} 
	else {
		echo "<script> alert('";
		echo JText::_(_IMAGEBROWSER_UPLOAD_ERROR_ATTACK.' '.$file_name, true);
		echo "'); window.history.go(-1); </script>\n";
		exit;
	}
	$array = array($file_name, $file_size, $file_type);
	return $array;
}
?>