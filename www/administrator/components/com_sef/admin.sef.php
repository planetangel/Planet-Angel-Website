<?php

include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sef'.DS.'ajax.php');

defined( '_JEXEC' ) or die( 'Restricted access' );

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sef'.DS.'tables');

$controllerName = JRequest::getCmd( 'c', 'cp' );

switch ($controllerName) {
	default:
		$controllerName = 'cp';
	case 'about':
	case 'alias':
		jimport('joomla.html.pagination');
		$init = new JPagination( 0, 0, 0 );
		$init = new JArrayHelper();
	case 'backup':
		jimport('joomla.filesystem.path');
		$init = JPath::check('');
	case 'config':
		jimport('joomla.cache.cache');
		$init = JCache::getInstance();
	case 'info':
	case 'logs':
		jimport('joomla.html.pagination');
		$init = new JPagination( 0, 0, 0 );
		$init = new JArrayHelper();
	case 'redirects':
		jimport('joomla.html.pagination');
		$init = new JPagination( 0, 0, 0 );
		$init = new JArrayHelper();
	case 'rss':
		jimport ('simplepie.simplepie');
		$init = new SimplePie();
	case 'cp':
		jimport( 'joomla.application.component.controller' );
		$init = new JController();
		$init = new JRequest();
		$init = new JError();
		$init = new JText();

		require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );
		$controllerName = 'SefController'.$controllerName;

		// Create the controller
		$controller = new $controllerName();

		// Perform the Request task
		$controller->execute( JRequest::getCmd('task') );

		// Redirect if set by the controller
		$controller->redirect();
		break;
}

?>