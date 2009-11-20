<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

function com_uninstall() {
	
	$db =& JFactory::getDBO();

	$query = "SELECT id FROM #__plugins "
			."WHERE element='sef_advance'";
	$db->setQuery($query);
	if ($id = $db->loadResult()) {
		// uninstall the plugin
		jimport('joomla.installer.installer');
		$installer = new JInstaller();
		$result	= $installer->uninstall( 'plugin', $id, 0 );
		if ($result) {
			echo 'SEF Advance system plugin also removed<br />';
		} else {
			echo 'SEF Advance system plugin could not be removed<br />Please remove this plugin manually';
		}
	}

	return true;

}

?>