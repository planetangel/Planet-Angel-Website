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

jimport('joomla.application.component.model');

/**
 * Image Browser Component Gallery Model
 *
 * @since 0.1.8
 */
class imagebrowserModelGallery extends JModel {
	var $current_folder=null; // Current folder (with trailing slash)
	var $subdirectories=null; // Array containing the list of subfolders in current folder
	var $images=null; // Array containing image objects
	var $option='com_imagebrowser';
	var $imagebrowserConfig=null;
	
	function getGallery($folder) {
		// Check valid folder
		if ($this->checkFolder($folder)) {
			// Set current folder (preceding slash and no trailing slash)
			$this->current_folder = urldecode($folder);
			// Load component config
			$modelConfig = new imagebrowserModelConfig();
			$this->imagebrowserConfig = $modelConfig->config;
			// Get contents from folder (this sets the subdirectories and images arrays)
			$this->getContents();
			// Order images according to preferences
			$this->orderImages();
			
			return array($this->images, $this->subdirectories);
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
	
	function getContents() {
		// Set directory to open (abolute path)
		$dir = JPATH_SITE.DS.$this->imagebrowserConfig['root_folder'].DS.$this->current_folder.DS;

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
		
		$info_file = JPATH_SITE.DS.$this->imagebrowserConfig['root_folder'].DS.$this->current_folder.DS.$filename.'.txt';

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
	
	function orderImages() {
        if (is_array($this->images)) {
			$sorter = new t_array_sorter;
			$sorter->sort($this->images, $this->imagebrowserConfig['order_by']);
			$this->images = array_reverse($this->images); // hacemos doble reverse, por algun motivo no funciona sin reverse
			if ($this->imagebrowserConfig['order_direction'] == 'ASC') {
			  $this->images = array_reverse($this->images);
			}
		}
		
		if (is_array($this->subdirectories)) {
			$sorter = new t_array_sorter;
			$sorter->sort($this->subdirectories, $this->imagebrowserConfig['order_by']);
			$this->subdirectories = array_reverse($this->subdirectories); // hacemos doble reverse, por algun motivo no funciona sin reverse
			if ($this->imagebrowserConfig['order_direction'] == 'ASC') {
			  $this->subdirectories = array_reverse($this->subdirectories);
			}
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

}
?>