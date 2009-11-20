<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

function com_install() {

	$db =& JFactory::getDBO();

	// install the plugin
	jimport('joomla.installer.installer');

	/*
	// Get the path to the package to install
	$p_dir = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sef'.DS.'plugin';

	// Detect the package type
	$type = JInstallerHelper::detectType($p_dir);

	$package = array();
	$package['packagefile'] = null;
	$package['extractdir'] = null;
	$package['dir'] = $p_dir;
	$package['type'] = $type;
	*/
	// Get the path to the package to install
	$p_filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sef'.DS.'plugin'.DS.'sef_advance.tgz';
	// Unpack and verify
	$package = JInstallerHelper::unpack($p_filename);

	// Get an installer instance
	//$installer =& JInstaller::getInstance();
	$installer = new JInstaller();

	// Install the package
	if (!$installer->install($package['dir'])) {
		$msg = 'Error installing the plugin<br />Please install the SEF Advance plugin manually.';
	} else {
		$msg = 'SEF Advance system plugin installed';
		$query = "UPDATE #__plugins "
				."SET published=1, ordering=-200 "
				."WHERE element='sef_advance'";
		$db->setQuery($query);
		if ($db->query()) {
			$msg .= ' and published';
		}
	}
	
	// Cleanup
	JInstallerHelper::cleanupInstall($p_filename, $package['dir']);

	echo $msg;

	echo '<br />';

	$query = "UPDATE #__components "
			."SET name = '. SEF Advance .', admin_menu_alt = '. SEF Advance .' "
			."WHERE admin_menu_link = 'option=com_sef' AND name = 'SEF Advance'";
	$db->setQuery($query);
	$db->query();

	echo '<a href="index.php?option=com_sef">Proceed to SEF Advance control panel</a><br />';

	return true;

}

?>