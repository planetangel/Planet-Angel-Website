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

class imageBrowser {
	var $config=null; // Object Containing configuration vars
	var $current_folder=null; // Current folder (with trailing slash)
	var $breadcrumbs=null; // String with html code for breadcrumbs
	var $folder_title=null; // Page title (based on current folder name)
	var $subdirectories=null; // Array containing the list of subfolders in current folder
	var $images=null; // Array containing image objects
	var $option=null;
	
	function imageBrowser($option, $folder) {
		// Check valid folder
		if ($this->checkFolder($folder)) {
			// Set current folder (preceding slash and no trailing slash)
			$this->current_folder = urldecode($folder);
			// Load component config
			$modelConfig = new imagebrowserModelConfig();
			$this->config = $modelConfig->config;
			// Set language according to settings (in case its empty or autodetect)
			$this->setLanguage();
			// Set option attribute (the component name)
			$this->option = $option;
			// Get contents from folder (this sets the subdirectories and images arrays)
			$this->getContents();
			// Order images according to preferences
			$this->orderImages();	
		}
		else {
			return false;
		}
	}
	
	/**
	 * This functions has been added as a response to security vulnerability reported by Paulino Michelazzo on 29 Sept 2008
	 * Check if folder contains '../'
	 * Luis Montero [e-noise.com]
	 *
	 */
	function checkFolder($folder) {
		if (strpos($folder, '..') !== false || strpos($folder, '../') !== false) {
			JError::raiseError(500, _IMAGEBROWSER_INVALID_FOLDER);
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * This function displays the image browser backend
	 *
	 */
	function imageBrowserBackend() {
		// Do breadcrumbs
		$this->doBackendBreadcrumbs($this->option);	
		// Instantiate HTML class for output
		// This class is in the backend folder
		HTML_imagebrowser::displayImagebrowser($this);
	}
	
	/**
	 * Get caption text for a given image
	 *
	 * @param string $img_file	String containing the file name of the image for which to get the caption.
	 * @return string	The caption text
	 */
	function getImageCaption($img_file) {		
		$split_filename = explode('.', $img_file);
		$filename = $split_filename[0];
		$extension = $split_filename[1];
		
		$info_file = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$filename.'.txt';

		if (file_exists($info_file)) {
			ob_start();
			require($info_file);
			$caption = ob_get_clean();
			return str_replace("'", '"', stripslashes($caption));
		}
		else {
			return false;
		}
	}
	
	function doBackendBreadcrumbs($option) {
		$breadcrumbs = ''; // Declare breadcrubs var (string)
		$str = ''; // Declare string variable used to accumulate folder path in for loop
		
		$array = explode('/', $this->current_folder);
		for ($i=0; $i<count($array); $i++) {
			$breadcrumbs .= ' > ';
			$href = 'index.php?option='.$option.'&folder=';
			$href .= urlencode($str.$array[$i]);
			if (isset($_REQUEST['Itemid'])) {
				$href .= '&Itemid='.$_REQUEST['Itemid'];
			}
			$breadcrumbs .= '<a href="'.JRoute::_($href).'">';
			$breadcrumbs .= $array[$i];
			$breadcrumbs .= '</a>';
			$str .= $array[$i].'/'; // we accumulate the folder path to build link in next iteration
		}
		$root = 'index.php?option='.$option;
		if (isset($_REQUEST['Itemid'])) {
			$root .= '&Itemid='.$_REQUEST['Itemid'];
		}
		$this->breadcrumbs =  '<a href="'.JRoute::_($root).'">'._IMAGEBROWSER_ROOT.'</a> '.$breadcrumbs;
	}
	
	function getContents() {
		// Set directory to open (abolute path)
		$dir = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS;

		// Open directory, and proceed to read its contents
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				$j=0; // counter for directories
				$k=0; // counter for images
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..' && $file != 'thumb') {
						$file_type = filetype($dir.$file);
						$mime_type = $this->getMimeType($dir.$file);
						if ($file_type == 'dir') {
							$this->subdirectories[$j] = $file;
							$j++;
						}
						elseif ($mime_type == 'jpg' || $mime_type == 'jpeg' || $mime_type == 'png' || $mime_type == 'gif') {
							$this->images[$k]['file'] = $file;
							$this->images[$k]['img_size'] = getimagesize($dir.$file);
							$this->images[$k]['caption'] = $this->getImageCaption($file);
							$this->images[$k]['file_size'] = number_format((filesize($dir.$file)/1024), 2).' Kb';
							$this->images[$k]['date_modified'] = date("d-m-y", filemtime($dir.$file));
							$k++;
						}
					}
				}
				closedir($dh);
			}
		}
	}
	
	function orderImages() {
        if (is_array($this->images)) {
			$sorter = new t_array_sorter;
			$sorter->sort($this->images, $this->config['order_by']);
			$this->images = array_reverse($this->images); // hacemos doble reverse, por alg�n motivo no funciona sin reverse
			if ($this->config['order_direction'] == 'ASC') {
			  $this->images = array_reverse($this->images);
			}
		}
	}
	
	function generateThumb($file) {
		if (!empty($file)) {
			// set parameters needed for creating the thumb
			$sourcefile = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$file;
			$destfile = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.'thumb'.DS.$file;
			$forcedwidth = $this->config['thumb_width'];
			$forcedheight = $this->config['thumb_height'];
			$imgcomp = $this->config['imgcomp']; // 0 best quality | 100 worst quality
			
			// check if thumb folder exists, if it doesnt we create it
			if (!file_exists(JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.'thumb'.DS)) {
				mkdir(JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.'thumb'.DS, 0755);
			}
			
			// Instantiate image class used for creating thumb - inc/image.class.php
			$thumb = new imageBrowserImage();
			// Create thumbnail
			$create_thumb = $thumb->resize_image($sourcefile, $destfile, $forcedwidth, $forcedheight, $imgcomp);
			
			if ($create_thumb === true) {
				JError::raiseNotice(500, _IMAGEBROWSER_GENERATE_THUMB_OK);
			}
			else {
				JError::raise(2, 500, _IMAGEBROWSER_GENERATE_THUMB_ERROR, '', false);
			}
		}
		else {
			JError::raiseWarning(500, _IMAGEBROWSER_GENERATE_THUMB_NO_FILE);
		}
	}
	
	function generateFolderThumbs() {
		HTML_imagebrowser::generateFolderThumbs($this);
	}
	
	function resizeImages($file) {
		if (!empty($file)) {
			// set parameters needed for creating the thumb
			$sourcefile = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$file;
			$destfile = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$file;
			$forcedwidth = $this->config['max_width'];
			$forcedheight = $this->config['max_height'];
			$imgcomp = $this->config['imgcomp']; // 0 best quality | 100 worst quality
			
			// check if thumb folder exists, if it doesnt we create it
			if (!file_exists(JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS)) {
				mkdir(JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS, 0755);
			}
			
			// Instantiate image class used for creating thumb - inc/image.class.php
			$thumb = new imageBrowserImage();
			// Create thumbnail
			$create_thumb = $thumb->resize_image($sourcefile, $destfile, $forcedwidth, $forcedheight, $imgcomp);
			
			if ($create_thumb === true) {
				JError::raiseNotice(500, _IMAGEBROWSER_RESIZE_IMAGES_OK);
			}
			else {
				JError::raise(2, 500, _IMAGEBROWSER_RESIZE_IMAGES_ERROR, '', false);
			}
		}
		else {
			JError::raiseWarning(500, _IMAGEBROWSER_RESIZE_IMAGES_NO_FILE);
		}
	}
		
	function resizeFolderImages() {
		HTML_imagebrowser::resizeFolderImages($this);
	}
	
	function editCaption($file) {
		$caption = $this->getImageCaption($file);
		HTML_imagebrowser::editCaption($this, $file, $caption);
	}
	
	function saveCaption($file) {
		global $mainframe;
		
		$split_filename = explode('.', $file);
		$filename = $split_filename[0];
		
		$destfile = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$filename.'.txt';
		
		if (file_put_contents($destfile, $_REQUEST['caption']) !== false) {
			JError::raiseNotice(500, _IMAGEBROWSER_EDIT_CAPTION_OK);
		}
		else {
			JError::raise(2, 500, _IMAGEBROWSER_EDIT_CAPTION_ERROR, '', false);
		}
		
		$mainframe->redirect( 'index.php?option='.$this->option.'&folder='.$this->current_folder );
	}
	
	function newFolder($new_folder) {
		global $mainframe;
		
		// Folder to be created
		$new_folder = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$new_folder.DS;
		
		// check if folder exists, if it doesnt we create it
		if (!file_exists(JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$new_folder.DS)) {
			// Try to create directory and catch error
			if (mkdir($new_folder, 0755)) {
				JError::raiseNotice(500, _IMAGEBROWSER_FOLDER_NEW_OK);
			}
			else {
				JError::raise(2, 500, _IMAGEBROWSER_FOLDER_NEW_ERROR, '', false);
			}
		}
		else {
			JError::raiseWarning(500, _IMAGEBROWSER_FOLDER_NEW_ALREADY);
		}
		
		$mainframe->redirect( 'index.php?option='.$this->option.'&folder='.$this->current_folder );
	}
	
	function renameFolderForm($oldname) {
		$dir_tree = JFolder::folders(JPATH_SITE.DS.$this->config['root_folder'], '.', true, true);
		// remove thumbnail folders and full path
		$clean_dir_tree = array();
		foreach ($dir_tree as $dir) {
			if (substr($dir, (strrpos($dir, DS)+1)) != "thumb") {
				$clean_dir_tree[] = str_replace(JPATH_SITE.DS, '', str_replace($this->config['root_folder'].DS, '', $dir));
			}
		}
		
		HTML_imagebrowser::renameFolderForm($clean_dir_tree, $this->current_folder, $oldname);
	}
	
	function renameFolder($new_parent_folder, $oldname, $newname) {
		global $mainframe;
		
		// Folder to be renamed
		$oldname = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$oldname;
		$newname = JPATH_SITE.DS.$this->config['root_folder'].DS.$new_parent_folder.DS.$newname;

		// Check if exists
		if (file_exists($oldname)) {
			// Try to rename and catch error
			if (rename($oldname, $newname)) {
				JError::raiseNotice(500, _IMAGEBROWSER_FOLDER_RENAME_OK);
			}
			else {
				JError::raise(2, 500, _IMAGEBROWSER_FOLDER_RENAME_ERROR, '', false);
			}
		}
		else {
			JError::raiseWarning(500, _IMAGEBROWSER_FOLDER_RENAME_NO_FOLDER);
		}
		
		$mainframe->redirect( 'index.php?option='.$this->option.'&folder='.$this->current_folder );
	}
	
	
	function deleteFolder($delete_folder) {
		global $mainframe;
		
		// Folders to be deleted
		$delete_folder = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$delete_folder.DS;
		$delete_folder_thumb = $delete_folder.'thumb'.DS;
		
		// Check if exists
		if (file_exists($delete_folder)) {
			// Try to delete and catch error
			rmdir($delete_folder_thumb); // Delete thumb folder
			if (rmdir($delete_folder)) {
				JError::raiseNotice(500, _IMAGEBROWSER_FOLDER_DELETE_OK);
			}
			else {
				JError::raise(2, 500, _IMAGEBROWSER_FOLDER_DELETE_ERROR, '', false);
			}
		}
		else {
			JError::raiseWarning(500, _IMAGEBROWSER_FOLDER_DELETE_NO_FOLDER);
		}
		
		$mainframe->redirect( 'index.php?option='.$this->option.'&folder='.$this->current_folder );
	}
	
	function uploadFile() {
		global $mainframe;
		
		$fieldName = 'upload_file';
		$dir =  JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS;
		$accept = 'image/jpeg,image/png,image/gif,application/zip';
		$max_upload_size = (1024*1024)*$this->config['max_upload_size']; // Mb
		$array = custom_uploadFile($fieldName, $dir, $accept, $max_upload_size);
		
		if ($array[2] == 'application/zip') { 
			// Process zip archive
			jimport('joomla.filesystem.archive');
			if (!JArchive::extract($dir.$array[0], $dir)) {
				JError::raiseError(500, _IMAGEBROWSER_ERROR_EXTRACT_ARCHIVE);
			}
		    // Delete zip file after its been extracted
		    unlink($dir.$array[0]);
		}
		else {
			// Process image
			$img_size = getimagesize($dir.$array[0]);
			if ($img_size[0] > $this->config['max_width'] || $img_size[1] > $this->config['max_height']) {
				// Instantiate image class used for resizing thumb - inc/image.class.php
				$image = new imageBrowserImage();
				// Create thumbnail
				$resize_image = $image->resize_image($dir.$array[0], $dir.$array[0], $this->config['max_width'], $this->config['max_height'], $this->config['imgcomp']);
				
				if ($resize_image === true) {
					JError::raiseNotice(500, _IMAGEBROWSER_RESIZE_OK);
				}
				else {
					JError::raise(2, 500, _IMAGEBROWSER_RESIZE_ERROR, '', false);
				}
			}
			// Generate thumbnail in thumb folder
			$this->generateThumb($array[0]);
		}
		
		$mainframe->redirect( 'index.php?option='.$this->option.'&folder='.$this->current_folder );	
	}

	
	/**
	 * Delete a file and its associated thumbnail and caption files if any.
	 *
	 * @param	string	$file	The file to be deleted within the current_folder.
	 */
	function deleteFile($file) {
		global $mainframe;
		
		// Files to be deleted
		$delete_file = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.$file;
		$thumb_file = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.'thumb'.DS.$file;
		$caption_file = JPATH_SITE.DS.$this->config['root_folder'].DS.$this->current_folder.DS.substr($file, 0, strrpos($file, '.')).'.txt';
		
		// Check if exists
		if (file_exists($delete_file)) {
			// Try to delete and catch error
			if (unlink($delete_file)) {
				unlink($thumb_file); // Delete thumbnail
				unlink($caption_file); // Delete caption
				JError::raiseNotice(500, _IMAGEBROWSER_FILE_DELETE_OK);
			}
			else {
				JError::raise(2, 500, _IMAGEBROWSER_FILE_DELETE_ERROR, '', false);
			}
		}
		else {
			JError::raiseWarning(500, _IMAGEBROWSER_FILE_DELETE_NO_FILE);
		}
		
		$mainframe->redirect( 'index.php?option='.$this->option.'&folder='.$this->current_folder );
	}
	
	/**
	 * Set the language to current language in Joomla session.
	 *
	 */
	function setLanguage() {
		// Check languade config and set current
		if (empty($this->config['language'])) {
			$this->config['language'] = JLanguageHelper::detectLanguage();
		}
	}
	
	/**
	 * This functions gets a file's mime type trying different methods.
	 * Updated in v0.1.7 to use file extension instead of relying on servers capability to identify mime types
	 * This was giving a lot of problems on Windows and Unix systems
	 * Bugs [#8137], [#8247] [#8795], [#8291]
	 *
	 * @param 	string 	$file	the path to the file to be tested
	 * @return 	string			A string with the mime type (ie: image/png)
	 */
	function getMimeType($file) {
		if (is_dir($file)) {
			return 'dir';
		}
		else {
			// return file extension (last 3 or 4 chars after the last dot
			preg_match('/\.(.{3,4})$/i', $file, $match);
			return strtolower($match[1]);
		}
	}
	
	
	/**
	 * This function shows the screen to add thumbs in content items.
	 * This function is used by the image browser plugin.
	 *
	 */
	function plugin($e_name) {
		HTML_imagebrowser::plugin($this, $e_name);
	}

}
?>