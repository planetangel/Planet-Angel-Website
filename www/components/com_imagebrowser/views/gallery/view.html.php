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

jimport( 'joomla.application.component.view');

/**
 * Image Browser Component gallery view class
 *
 * @package		Joomla
 * @subpackage	com_imagebrowser
 */
class imagebrowserViewGallery extends JView {
	var $imagebrowserConfig=null; // Image browser global config
	var $option='com_imagebrowser'; // the component name
	var $task=null; // the task switch
	var $view='gallery'; // the view name
	var $type=null; // the view type (suffix used to load the relevant file in tmpl)
	var $folder_title=null;
	var $folder=null;
	var $images=null;
	var $subdirectories=null;
	
	function init() {
		// Load component config
		$modelConfig = new imagebrowserModelConfig();
		$this->imagebrowserConfig = $modelConfig->config;
		
		// Get request vars and assign them to the view
		$this->option = JRequest::getVar('option', 'com_imagebrowser');
		$this->view = JRequest::getVar('view', 'gallery');
		$this->type = JRequest::getVar('type', '');
		$this->task = JRequest::getVar('task', '');
		$this->folder = JRequest::getVar('folder', '');
	}
	
	function display($tpl = null) {
		// Initialise the view
		$this->init();
		
		$array = explode('/', $this->folder);
		// Take the last element from the array
		$folder_title = $array[(count($array)-1)];
		// Set page title (replacing "_" and "/")
		$this->folder_title = str_replace(array('_', '/'), array(' ', ''), $folder_title);
		
		// Push model into the view
		$model =& $this->getModel();
		$gallery = $model->getGallery($this->folder);
		
		$this->images = $gallery[0];
		$this->subdirectories = $gallery[1];
		
		// Set up document and update pathway before we display the view
		$this->setUpDocument($this->page_title);
		if (!empty($this->folder)) {
			$this->doBreadcrumbs();
		}

		parent::display($tpl);
	}
	
	function setUpDocument($title) {
		$document =& JFactory::getDocument();
		// Set page title
		$document->setTitle( JText::_( $title ) );
		
		$document->addStyleSheet("components/com_imagebrowser/lib/slimbox/css/slimbox.css");
		$document->addScript("media/system/js/mootools.js");
		$document->addScript("components/com_imagebrowser/lib/slimbox/js/slimbox.js");
	}
	
	function doBreadcrumbs() {
		global $mainframe;
		
		$breadcrumbs =& $mainframe->getPathWay();
		$array = explode('/', $this->folder);
		for ($i=0; $i<count($array); $i++) {
			$href = 'index.php?option='.$this->option.'&view='.$this->view.'&folder=';
			$href .= urlencode($str.$array[$i]);
			if (isset($_REQUEST['Itemid'])) {
				$href .= '&Itemid='.$_REQUEST['Itemid'];
			}
			$breadcrumbs->addItem($array[$i], $href);
		}
	}

}

?>