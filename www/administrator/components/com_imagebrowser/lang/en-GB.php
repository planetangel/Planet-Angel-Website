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

// no direct access
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

define("_IMAGEBROWSER_LANG_BACK", "Back");

define("_IMAGEBROWSER_FILE_NAME", "File Name");
define("_IMAGEBROWSER_CAPTION", "Caption");
define("_IMAGEBROWSER_FILESIZE", "File Size");
define("_IMAGEBROWSER_MODIFIED", "Date Uploaded");
define("_IMAGEBROWSER_NEW_FOLDER", "Create new folder");
define("_IMAGEBROWSER_UPLOAD_IMAGE", "Upload Image or ZIP archive");
define("_IMAGEBROWSER_EDIT_CAPTION", "Edit Caption");
define("_IMAGEBROWSER_GENERATE_THUMB", "Generate Thumbnail");
define("_IMAGEBROWSER_GENERATE_FOLDER_THUMBS", "Generate thumbs for all images in current folder");
define("_IMAGEBROWSER_CREATE", "Create");
define("_IMAGEBROWSER_UPLOAD", "Upload");
define("_IMAGEBROWSER_SAVE", "Save");
define("_IMAGEBROWSER_DELETE", "Delete");
define("_IMAGEBROWSER_GO", "Go");
define("_IMAGEBROWSER_INVALID_FOLDER", "Invalid folder");
define("_IMAGEBROWSER_ERROR_EXTRACT_ARCHIVE", "Error extracting archive");

define("_IMAGEBROWSER_ROOT", "Root");

define("_IMAGEBROWSER_GENERATE_THUMB_OK", "Thumbnail generated successfully");
define("_IMAGEBROWSER_GENERATE_THUMB_ERROR", "ERROR: Thumbnail could not be generated");
define("_IMAGEBROWSER_GENERATE_THUMB_NO_FILE", "No file selected to generate thumbnail.");

define("_IMAGEBROWSER_RESIZE_OK", "Image resized successfully");
define("_IMAGEBROWSER_RESIZE_ERROR", "ERROR: Image could not be resized");

define("_IMAGEBROWSER_EDIT_CAPTION_OK", "Caption saved successfully");
define("_IMAGEBROWSER_EDIT_CAPTION_ERROR", "ERROR: Caption could not be saved");

define("_IMAGEBROWSER_FILE_DELETE_OK", "File deleted successfully");
define("_IMAGEBROWSER_FILE_DELETE_ERROR", "ERROR: File could not be deleted");
define("_IMAGEBROWSER_FILE_DELETE_NO_FILE", "ERROR: File could not be deleted because it doesn't exist");

define("_IMAGEBROWSER_FOLDER_NEW_OK", "Folder created successfully");
define("_IMAGEBROWSER_FOLDER_NEW_ERROR", "ERROR: Folder could not be created");
define("_IMAGEBROWSER_FOLDER_NEW_ALREADY_EXISTS", "ERROR: Folder could not be created because another folder with the same name already exists");

define("_IMAGEBROWSER_FOLDER_DELETE_OK", "Folder deleted successfully");
define("_IMAGEBROWSER_FOLDER_DELETE_ERROR", "ERROR: Folder could not be deleted. Please make sure that folder is empty before you try to delete it.");
define("_IMAGEBROWSER_FOLDER_DELETE_NO_FOLDER", "ERROR: Folder could not be deleted because it doesn't exist");
define("_IMAGEBROWSER_FOLDER_NAME_NOT_VALID", "Folder name not valid");

// UPLOAD
define("_IMAGEBROWSER_UPLOAD_ERROR_PHP_UP_MAX_FILESIZE", "ERROR: File exceeded PHP upload_max_filesize");
define("_IMAGEBROWSER_UPLOAD_ERROR_PHP_MAX_FILESIZE", "ERROR: File exceeded PHP max_file_size");
define("_IMAGEBROWSER_UPLOAD_ERROR_PARTIAL_UPLOAD", "ERROR: File only partially uploaded");
define("_IMAGEBROWSER_UPLOAD_ERROR_NO_FILE", "ERROR: No file uploaded");
define("_IMAGEBROWSER_UPLOAD_ERROR_MAX_FILESIZE", "ERROR: File exceeded upload_max_filesize set in preferences.");
define("_IMAGEBROWSER_UPLOAD_ERROR_FILETYPE", "ERROR: File type not valid.");
define("_IMAGEBROWSER_UPLOAD_ERROR_MOVE", "ERROR: Could not move file to destination directory");
define("_IMAGEBROWSER_UPLOAD_ERROR_ATTACK", "ERROR: Possible file upload attack. Filename: ");

// BATCH THUMBNAIL GENERATION
define("_IMAGEBROWSER_START_THUMB_GENERATION", "Start thumbnail generation");
define("_IMAGEBROWSER_OVERALL", "OVERALL");
define("_IMAGEBROWSER_ALL_REQUESTS_STARTED", "All Requests started");
define("_IMAGEBROWSER_ALL_COMPLETED", "All Completed");

// Added in 0.1.8
define("_IMAGEBROWSER_RENAME", "Rename");
define("_IMAGEBROWSER_FOLDER_RENAME_OK", "Folder renamed successfully");
define("_IMAGEBROWSER_FOLDER_RENAME_ERROR", "ERROR: Could not rename folder");
define("_IMAGEBROWSER_FOLDER_RENAME_NO_FOLDER", "ERROR: Folder to be renamed does not exist");
define("_IMAGEBROWSER_PROCESS_FORCE_MAX_DIMENSIONS", "Process images and apply maximum height and width as set up in Parameters window");
define("_IMAGEBROWSER_FOLDER_RESIZE_IMAGES", "Resize images in current folder");
define("_IMAGEBROWSER_START_IMAGE_RESIZING", "Start processing images");
define("_IMAGEBROWSER_RESIZE_IMAGES_OK", "Images resized successfully");
define("_IMAGEBROWSER_RESIZE_IMAGES_ERROR", "ERROR: Could not resize images in current folder");
define("_IMAGEBROWSER_RESIZE_IMAGES_NO_FILE", "ERROR: File to be resized didn't exist");
?>