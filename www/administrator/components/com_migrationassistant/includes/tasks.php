<?php
/**
 * Document Description
 * 
 * Document Long Description 
 * 
 * PHP4/5
 *  
 * Created on Apr 7, 2008
 * 
 * @package package_name
 * @author Your Name <author@toowoombarc.qld.gov.au>
 * @author Toowoomba Regional Council Information Management Branch
 * @license GNU/GPL http://www.gnu.org/licenses/gpl.html
 * @copyright 2008 Toowoomba Regional Council/Sam Moffatt 
 * @version SVN: $Id:$
 * @see Project Documentation DM Number: #???????
 * @see Gaza Documentation: http://gaza.toowoomba.qld.gov.au
 * @see JoomlaCode Project: http://joomlacode.org/gf/project/
 */

defined('_JEXEC') or die('guten tag :)');
function migrateSettings(){ 
	$db =& JFactory::getDBO();
	$config =& JFactory::getConfig();
	$tables = $db->getTableList();
	if(in_array($config->getValue('config.dbprefix').'migration_configuration',$tables)) {
		$db->setQuery("SELECT `key`,`value` FROM #__migration_configuration");
		$results = $db->loadAssocList();
		if(!is_array($results)) { 
			echo $db->getErrorMsg();
			return;
		}
		$cfg = JFactory::getConfig();
		foreach($results as $result) {
			$cfg->setValue('config.'.$result['key'], $result['value']);
		}
		echo '<p>'. JText::_('Updating your configuration file') .'</p>';
		//echo '<pre>'.print_r(htmlspecialchars($cfg->toString('PHP', 'config', array('class' => 'JConfig'))),1).'</pre>';
		
		jimport('joomla.filesystem.file');
		$fname = JPATH_CONFIGURATION.DS.'configuration.php';
		if (JFile::write($fname, $cfg->toString('PHP', 'config', array('class' => 'JConfig')))) {
			$msg = JText::_('The Configuration Details have been updated');
		} else {
			$msg = JText::_('ERRORCONFIGFILE');
		}
	} else {
		$msg = JText::_('Error') .': '. JText::_('Migration Configuration table not found').'. '. JText::_('Was this site migrated with Migrator RC7 or greater').'?';
	}
	echo '<p>'. $msg .'</p>';
	//echo '<p><a href="index.php?option=com_migrationassistant">'. JText::_('Home') .'</a></p>';
}


function dumpLoad() {
	$model	= new JInstallationModel();
	$model->dumpLoad();
	//echo '<p>File loaded</p>';
}

function postmigrate() {
	$model = new JInstallationModel();
	if($model->postMigrate()) {
		// errors!
	}
	$db =& JFactory::getDBO();
	$db->setQuery("INSERT INTO #__components VALUES(0, 'Migration Assistant', 'option=com_migrationassistant', 0, 0, 'option=com_migrationassistant', 'Migration Assistant', 'com_migrationassistant', 0, 'js/ThemeOffice/component.png', 0, '', 1)");
	$db->Query();
	echo '<p>'.JText::_('MIGRATORRC7HIGHER').'</p>';
}

function fullMigrate() {
	$model = new JInstallationModel();
	$db =& JFactory::getDBO();
	$config =& JFactory::getConfig();
	$error = Array();
	$dbname = $config->getValue('config.db');
	$dbprefix = $config->getValue('config.dbprefix');
	$session = & JFactory :: getSession();
	$sessiontable = & JTable :: getInstance('session');
	$sessiontable->load($session->getId());
	JInstallationHelper::deleteDatabase($db, $dbname, $dbprefix, $error);
	// we need to reload base structure
	$model->makeDB();
	// create a new user
	$my =& JFactory::getUser();
	$user = new JUser();
	$user->id = 0;
	$user->username = 'migrationassistant';
	$user->password = md5($config->getValue('config.secret'));
	$user->gid = 25;
	$user->group = 'Super Administrator';
	$user->name = "Migration Assistant";
	$user->email = "migrationassistant@example.com";
	$user->save();
	// put a session back
	$sessiontable->insert($sessiontable->session_id, $sessiontable->client_id);
	// and build a session for them
	doUserLogIn('migrationassistant');
	// and now check what we've just gotten before moving on
	if(!$model->checkUpload()) {
		handleError($model);
	}
	include(MIGBASE.DS.'includes'.DS.'migpage.php');
}

function doUserLogIn($username) {
	$my = new JUser();
	jimport('joomla.user.helper');
	if ($id = intval(JUserHelper :: getUserId($username))) {
		$my->load($id);
	} else {
		return JError :: raiseWarning('SOME_ERROR_CODE', 'MigrationAssistant (doUserLogIn): Failed to load user');
	}

	// If the user is blocked, redirect with an error
	if ($my->get('block') == 1) {
		return JError :: raiseWarning('SOME_ERROR_CODE', JText :: _('E_NOLOGIN_BLOCKED'));
	}

	//Mark the user as logged in
	$my->set('guest', 0);

	// Discover the access group identifier
	// NOTE : this is a very basic for of permission handling, will be replaced by a full ACL in 1.6
	jimport('joomla.factory');
	$acl = & JFactory :: getACL();
	$grp = $acl->getAroGroup($my->get('id'));

	$my->set('aid', 1);
	if ($acl->is_group_child_of($grp->name, 'Registered', 'ARO') || $acl->is_group_child_of($grp->name, 'Public Backend', 'ARO')) {
		// fudge Authors, Editors, Publishers and Super Administrators into the special access group
		$my->set('aid', 2);
	}

	//Set the usertype based on the ACL group name
	$my->set('usertype', $grp->name);

	// Register the needed session variables
	$session = & JFactory :: getSession();
	$session->set('user', $my);

	// Get the session object
	$table = & JTable :: getInstance('session');
	$table->load($session->getId());

	$table->guest = $my->get('guest');
	$table->username = $my->get('username');
	$table->userid = intval($my->get('id'));
	$table->usertype = $my->get('usertype');
	$table->gid = intval($my->get('gid'));

	$table->update();

	// Hit the user last visit field
	$my->setLastVisit();

	// Set remember me option
	$lifetime = time() + 365 * 24 * 60 * 60;
	setcookie('usercookie[username]', $my->get('username'), $lifetime, '/');
	setcookie('usercookie[password]', $my->get('password'), $lifetime, '/');	
}

function handleError(&$incoming) {
	$msg = '';
	if(is_object($incoming)) $msg = $incoming->getError();
	else if(is_string($incoming)) $msg = $incoming;
	else $msg = print_r($incoming,1);
	echo '<p>'. JText::_('Error'). ': '. $msg .'</p>';
}