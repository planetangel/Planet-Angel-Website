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

jimport('joomla.application.component.controller');

/**
 * Image Browser Component Controller
 *
 * @package		Joomla
 * @subpackage	com_imagebrowser
 * @since 0.1.8
 */
class imagebrowserController extends JController {
	var $imagebrowserConfig=null;
	
	function __construct($imagebrowserConfig) {
		parent::__construct();
		
		// Set component config in controller
		$this->imagebrowserConfig = $imagebrowserConfig;
	}
	
	/**
	 * Method to display a view
	 *
	 * @access	public
	 * @since	1.0.1
	 */
	function display() {
		// Display view
		parent::display();
	}

	function cancel() {
		$this->setRedirect( 'index.php' );
	}
}
?>