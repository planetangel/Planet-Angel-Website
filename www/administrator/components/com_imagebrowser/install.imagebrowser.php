<?php
/**
* @package		Joomla
* @subpackage	com_imagebrowser
* @copyright	Copyright (C) 2008 E-NOISE.COM LIMITED. All rights reserved.
* @license		GNU/GPL.
* @author 		Luis Montero [e-noise.com]
* @version 		0.1.6
* Joomla! and com_imagebrowser are free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed they include or
* are derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

function com_install() {
	// Create root directory for gallery if it doesnt exist
	$imagebrowser_root = JPATH_SITE.'/images/stories/imagebrowser/';
	if (!file_exists($imagebrowser_root)) {
		mkdir($imagebrowser_root, 0755);
	}
}
?>