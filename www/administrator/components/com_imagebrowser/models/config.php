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
class imagebrowserModelConfig extends JModel {
	var $config=null;
	
	function __construct() {
		$comp_config =& JComponentHelper::getParams('com_imagebrowser');
		$config['root_folder'] = $comp_config->get('root_folder', 'images/stories/imagebrowser');
		$config['order_by'] = $comp_config->get('order_by', 'date_modified');
		$config['order_direction'] = $comp_config->get('order_direction', 'DESC');
		$config['show_comp_description'] = $comp_config->get('show_comp_description', 1);
		$config['comp_description'] = $comp_config->get('comp_description', '');
		$config['language'] = $comp_config->get('language', '');
		$config['max_width'] = $comp_config->get('max_width', 800);
		$config['max_height'] = $comp_config->get('max_height', 600);
		$config['thumb_width'] = $comp_config->get('thumb_width', 120);
		$config['thumb_height'] = $comp_config->get('thumb_height', 90);
		$config['imgcomp'] = $comp_config->get('imgcomp', 0);
		$config['max_upload_size'] = $comp_config->get('max_upload_size', 2);
		
		// The mode is read from menu item (for frontend only)
		$menu =& JMenu::getInstance('site');
		$item = $menu->getActive();
		
		if (is_object($item)) {
			$params =& $menu->getParams($item->id); 
			$config['mode'] = $params->get('mode', false);
			$config['page_title'] = $params->get('page_title', '');
			$config['show_page_title'] = $params->get('show_page_title', 0);
			$config['pageclass_sfx'] = $params->get('pageclass_sfx', '');
			
			// Override global component settings with menu item settings
			$config['root_folder'] = $params->get('root_folder', $config['root_folder']);
			$config['order_by'] = $params->get('order_by', $config['order_by']);
			$config['order_direction'] = $params->get('order_direction', $config['order_direction']);
			$config['show_comp_description'] = $params->get('show_comp_description', $config['show_comp_description']);
			$config['comp_description'] = $params->get('comp_description', $config['comp_description']);
			$config['language'] = $params->get('language', $config['language']);
			$config['max_width'] = $params->get('max_width', $config['max_width']);
			$config['max_height'] = $params->get('max_height', $config['max_height']);
			$config['thumb_width'] = $params->get('thumb_width', $config['thumb_width']);
			$config['thumb_height'] = $params->get('thumb_height', $config['thumb_height']);
			$config['imgcomp'] = $params->get('imgcomp', $config['imgcomp']);
			$config['max_upload_size'] = $params->get('max_upload_size', $config['max_upload_size']);
		}
		
		$this->config = $config;
	}
}
?>