<?php
/**
 * Document Description
 * 
 * Document Long Description 
 * 
 * PHP4/5
 *  
 * Created on Feb 11, 2008
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
 
// check we're in the right place...
defined('_JEXEC') or die('bad karma dude!');
JToolBarHelper::title( JText::_( 'Migration Assistant' ), 'config.png' );
JRequest::setVar('migration',1);
JRequest::setVar('oldPrefix','jos_');
define('MIGBASE',dirname(__FILE__));
include(MIGBASE.'/includes/tasks.php');
include(MIGBASE.'/includes/model.php');
include(MIGBASE.'/includes/mighelper.php');

$task = JRequest::getWord('task','');
switch($task) {
	case 'migratesettings':
		migrateSettings();
		break;
	case 'fullmigrate':
		fullMigrate();
		break;
	case 'dumpLoad':
		dumpLoad();
		break;
	case 'postmigrate':
		postMigrate();
		break;
	default:
		echo '<h1>'. JText::_('Settings Migration') .'</h1>';
		echo '<p>'. JText::_('Use this for sites that have already been migrated with the Migrator RC7 or higher').'.</p>';
		// are you sure, no really
		echo '<p>'. JText::_('Are you sure you want to migrate settings?') . ' <a href="index.php?option=com_migrationassistant&task=migratesettings">'. JText::_('Migrate Settings Now').'.</a></p>';
		echo '<br /><h1>'. JText::_('Full Migration') .'</h1>';
		echo '<p>'. JText::_('Use this if you wish to migrate your entire site') .'.</p>';
		echo '<dl id="system-message"><dt class="notice">WARNING</dt><dd class="notice message-fade"><ul>';
		echo '<li>'. JText::_('Warning: This will delete all existing data in your site and any tables from installed extensions').'</li>';
		echo '<li>'. JText::_('Any installed extensions will be removed however their files will have to be manually deleted').'</li>';
		echo '</ul></dt></dl>';
		echo '<p>'. JText::_('Migration Script').'</p>';
		$encodings = array( array('key'=>'iso-8859-1'),array('key'=>'iso-8859-2'),array('key'=>'iso-8859-3'),array('key'=>'iso-8859-4'),array('key'=>'iso-8859-5'),array('key'=>'iso-8859-6'),array('key'=>'iso-8859-7'),array('key'=>'iso-8859-8'),array('key'=>'iso-8859-9'),array('key'=>'iso-8859-10'),array('key'=>'iso-8859-13'),array('key'=>'iso-8859-14'),array('key'=>'iso-8859-15'),array('key'=>'cp874'),array('key'=>'windows-1250'),array('key'=>'windows-1251'),array('key'=>'windows-1252'),array('key'=>'windows-1253'),array('key'=>'windows-1254'),array('key'=>'windows-1255'),array('key'=>'windows-1256'),array('key'=>'windows-1257'),array('key'=>'windows-1258'),array('key'=>'utf-8'),array('key'=>'big5'),array('key'=>'euc-jp'),array('key'=>'euc-kr'),array('key'=>'euc-tw'),array('key'=>'iso-2022-cn'),array('key'=>'iso-2022-jp-2'),array('key'=>'iso-2022-jp'),array('key'=>'iso-2022-kr'),array('key'=>'iso-10646-ucs-2'),array('key'=>'iso-10646-ucs-4'),array('key'=>'koi8-r'),array('key'=>'koi8-ru'),array('key'=>'ucs2-internal'),array('key'=>'ucs4-internal'),array('key'=>'unicode-1-1-utf-7'),array('key'=>'us-ascii'),array('key'=>'utf-16') );
		$encodingsbox = JHTML::_('select.genericlist',$encodings,'srcEncoding', 'size="1"','key','key' );
		?>
		<form method="post" action="index.php" enctype="multipart/form-data">
		<input type="hidden" name="task" value="fullmigrate">
		<input type="hidden" name="option" value="com_migrationassistant" />
		<input class="input_box" id="migration_script" name="sqlFile" type="file" size="20"  />
		<br/>
		<input class="input_box" id="sqlUploaded" name="sqlUploaded" type="checkbox" /><?php echo JText::_('I have already uploaded a SQL file') ?>
		<br/>
		<?php echo JText::_('Old site encoding').': ' ?><?php echo $encodingsbox ?>
		<br/>
		<input class="input_box" type="submit" name="fullmigrate" value="<?php echo JText::_('Migrate') ?>" />
		<br /><br />	
		<?php
		//print_r($_REQUEST);
		break;
}
?>