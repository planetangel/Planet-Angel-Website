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
 
echo '<h1>'. JText::_('Migrating Site').'</h1>';
?>

<iframe src="about:blank" name="migrationtarget" style="padding: 0px;  width: 100%;  height: 500px;" class="license" frameborder="0" marginwidth="25px" scrolling="none"></iframe>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate" target="migrationtarget">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_migrationassistant" />
	<input type="hidden" name="tmpl" value="component" />
  	<input type="hidden" name="start" value="1" />
	<input type="hidden" name="foffset" value="0" />
	<input type="hidden" name="totalqueries" value="0" />
	
  	<!--  <jtmpl:tmpl name="migration">	<input type="hidden" name="migration" value="{MIGRATION}" /></jtmpl:tmpl>
  	<input type="hidden" name="loadchecked" value="{VAR_LOADCHECKED}" />
  	<input type="hidden" name="dataLoaded" value="{VAR_DATALOADED}" />
  	<input type="hidden" name="migration" value="{VAR_MIGRATION}" />
  	<input type="hidden" name="DBtype" value="{VAR_DBTYPE}" />
  	<input type="hidden" name="DBhostname" value="{VAR_DBHOSTNAME}" />
  	<input type="hidden" name="DBuserName" value="{VAR_DBUSERNAME}" />
  	<input type="hidden" name="DBpassword" value="{VAR_DBPASSWORD}" />
  	<input type="hidden" name="DBname" value="{VAR_DBNAME}" />
  	<input type="hidden" name="DBPrefix" value="{VAR_DBPREFIX}" />
  	<input type="hidden" name="ftpRoot" value="{VAR_FTPROOT}" />
  	<input type="hidden" name="ftpEnable" value="{VAR_FTPENABLE}" />
  	<input type="hidden" name="ftpHost" value="{VAR_FTPHOST}" />
  	<input type="hidden" name="ftpPort" value="{VAR_FTPPORT}" />
  	<input type="hidden" name="ftpUser" value="{VAR_FTPUSER}" />
  	<input type="hidden" name="ftpPassword" value="{VAR_FTPPASSWORD}" />
  	<input type="hidden" name="lang" value="{VAR_LANG}" />
  	-->
</form>
<script language="JavaScript" type="text/javascript">window.setTimeout('submitform("dumpLoad")',500);</script>