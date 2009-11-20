<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/html/admin.uploads.html.php $
// $Id: admin.uploads.html.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HTML_Joom_AdminUploads {


  /**
   * Single upload
   *
   */
  function Joom_ShowUpload_HTML() {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
?>
<form action="index.php?task=uploadhandler" method="post" name="adminForm" enctype="multipart/form-data" onsubmit="return joom_checkme();">
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminlist">
  <tr>
    <td colspan="2" align="center">
<?php
    if ($this->batchul) {
?>
      <span style="color:green"><b><?php echo JText::_('JGA_UPLOAD_COMPLETE_CHOOSE_NEXT'); ?></b></span>
<?php
    } else {
?>
      &nbsp;
<?php
    }
?>
    </td>
  </tr>
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
<?php
    for ($i=0; $i < 10; $i++) {
?>
  <tr>
    <td align="right" width="50%">
      <div align="right">
       <?php echo JText::_('JGA_PLEASE_SELECT_IMAGE'); ?>
      </div>
    </td>
    <td align="left" width="50%">
      <input type="file" name="arrscreenshot[<?php echo $i; ?>]" />
    </td>
  </tr>
<?php
    }
?>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_PICTURE_ASSIGN_TO_CATEGORY'); ?>
      </div>
    </td>
    <td align="left">
<?php
    $clist = Joom_ShowDropDownCategoryList(0,'catid',' class="inputbox" size="1" style="width:228;"');
    echo $clist;
?>
    </td>
  </tr>
<?php
    if (!$config->jg_useorigfilename) {
?>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_GENERIC_TITLE'); ?>
      </div>
    </td>
    <td align="left">
      <input type="text" name="gentitle" size="34" maxlength="256" value="" />
    </td>
  </tr>
<?php
    }
?>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_GENERIC_DESCRIPTION') . ' ' . JText::_('JGA_OPTION'); ?>
      </div>
    </td>
    <td align="left">
      <input type="text" name="gendesc" size="34" maxlength="1000" />
    </td>
  </tr>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_AUTHOR') . ' ' . JText::_('JGA_OPTION'); ?>
      </div>
    </td>
    <td align="left">
      <input type="text" name="photocred" size="34" maxlength="256" />
    </td>
  </tr>
<?php
    if ($config->jg_delete_original != 2) {
      $sup2 = "&sup1;";
      $sup3 = "&sup2";
    } else {
      $sup2 = "&sup2;";
      $sup3 = "&sup3;";
?>
  <tr>
    <td align="right">
        <?php echo JText::_('JGA_DELETE_ORIGINAL_AFTER_UPLOAD'); ?>&nbsp;&sup1;
    </td>
    <td align="left">
      <input type="checkbox" name="original_delete" value="1" />
    </td>
  </tr>
<?php
    }
?>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_CREATE_SPECIAL_GIF'); ?>&nbsp;<?php echo $sup2; ?>
      </div>
    </td>
    <td align="left">
      <input type="checkbox" name="create_special_gif" value="1" />
    </td>
  </tr>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_DEBUG_MODE'); ?>
      </div>
    </td>
    <td align="left">
      <input type="checkbox" name="debug" value="1" />
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <div align="center">
        <br /><input type="submit" value="<?php echo JText::_('JGA_UPLOAD'); ?>" />
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <div align="center" class="smallgrey">
<?php
    if ($config->jg_delete_original == 2) {
?>
        <br />&sup1;&nbsp;<?php echo JText::_('JGA_DELETE_ORIGINAL_AFTER_UPLOAD_ASTERISK'); ?>
<?php
    }
?>
        <br /><?php echo $sup2; ?>&nbsp;<?php echo JText::_('JGA_CREATE_SPECIAL_GIF_ASTERISK'); ?>
        <br /><b><?php echo JText::_('JGA_DEBUG_MODE_ASTERISK'); ?></b>
      </div>
    </td>
  </tr>
</table>
</form>
<?php
  }


  /**
   * Batch upload
   *
   */
  function Joom_ShowBatchUpload_HTML() {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
?>
<form action="index.php?task=batchuploadhandler" method="post" name="adminForm" enctype="multipart/form-data" onSubmit="return joom_checkme();">
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminlist">
  <tr align="center" valign="middle">
    <td colspan="2">
      <div align="center">
        <?php echo JText::_('JGA_BATCH_UPLOAD_NOTE'); ?>
        <br /><br />
      </div>
    </td>
  </tr>
  <input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
  <tr>
    <td align="right" width="50%">
      <?php echo JText::_('JGA_BATCH_ZIP_FILE'); ?>
    </td>
    <td align="left" width="50%">
      <input type="file" name="zippack" accept="application/zip, application/x-zip-compressed">
    </td>
  </tr>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_PICTURE_ASSIGN_TO_CATEGORY'); ?>
    </td>
    <td align="left">
<?php
    $clist = Joom_ShowDropDownCategoryList(0,'catid',' class="inputbox" size="1" style="width:228px;"');
    echo $clist;
?>
    </td>
  </tr>
<?php
    if (!$config->jg_useorigfilename && $config->jg_filenamenumber) {
      $sup1 = "&sup1;";
      $sup2 = "&sup2;";
      $sup3 = "&sup3;";
    } else {
      $sup2 = "&sup1;";
      $sup3 = "&sup2;";
    }
    if ($config->jg_delete_original == 2) {
      $sup3 = "&sup2;";
    }
    
    if (!$config->jg_useorigfilename && $config->jg_filenamenumber) {
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_COUNTER_NUMBER'); ?>&nbsp;<?php echo $sup1; ?>
    </td>
    <td align="left">
      <input type="text" name="filecounter" size="5" maxlength="5" />
    </td>
  </tr>
<?php
    }
    if (!$config->jg_useorigfilename) {
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_GENERIC_TITLE'); ?>
    </td>
    <td align="left">
      <input type="text" name="gentitle" size="34" maxlength="256" value="" />
    </td>
  </tr>
<?php
    }
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_GENERIC_DESCRIPTION') . ' ' . JText::_('JGA_OPTION'); ?>
    </td>
    <td align="left">
      <input type="text" name="gendesc" size="34" maxlength="1000" />
    </td>
  </tr>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_AUTHOR') . ' ' . JText::_('JGA_OPTION'); ?>
    </td>
    <td align="left">
      <input type="text" name="photocred" size="34" maxlength="256" />
    </td>
  </tr>
<?php
    if ($config->jg_delete_original == 2) {
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_DELETE_ORIGINAL_AFTER_UPLOAD'); ?>&nbsp;<?php echo $sup2; ?>
    </td>
    <td align="left">
      <input type="checkbox" name="original_delete" value="1" />
    </td>
  </tr>
<?php
    }
?>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_DEBUG_MODE'); ?>
      </div>
    </td>
    <td align="left">
      <input type="checkbox" name="debug" value="1" />
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <br /><input type="submit" value="<?php echo JText::_('JGA_START_BATCHUPLOAD'); ?>" />
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <div align="center" class="smallgrey">
<?php
    if (!$config->jg_useorigfilename && $config->jg_filenamenumber) {
?>
        <br /><?php echo $sup1; ?>&nbsp;<?php echo JText::_('JGA_COUNTER_NUMBER_ASTERISK'); ?>
<?php
    }
    if ($config->jg_delete_original == 2) {
?>
        <br /><?php echo $sup2; ?>&nbsp;<?php echo JText::_('JGA_DELETE_ORIGINAL_AFTER_UPLOAD_ASTERISK'); ?>
<?php
    }
?>
        <br /><b><?php echo JText::_('JGA_DEBUG_MODE_ASTERISK'); ?></b>

      </div>
    </td>
  </tr>
</table>
</form>
<?php
  }


  /**
   * FTP upload
   *
   */
  function Joom_ShowFTPUpload_HTML() {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
    $subdirectory = $this->subdirectory;
?>
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminlist">
  <tr align="center" valign="middle">
    <td colspan="2" align="center" valign="top">
<?php
    if ($this->batchul) {
?>
      <span style="color:green"><b><?php echo JText::_('JGA_UPLOAD_COMPLETE_CHOOSE_NEXT'); ?></b></span>
<?php
    }
?>
    </td>
  </tr>
  <p />
  <tr align="center" valign="middle">
    <td align="right" width="50%">
      <?php echo JText::_('JGA_PICTURES_PATH'); ?>:
    </td>
    <td align="left" width="50%">
      <?php echo JPath::clean(JPATH_ROOT.DS.$config->jg_pathftpupload.$subdirectory); ?>
    </td>
  </tr>
<?php
    $dirs = JFolder::folders(JPATH_ROOT.DS.$config->jg_pathftpupload,'',true,true);
    if(count($dirs)>0) {
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_SELECT_DIRECTORY'); ?>:
    </td>
    <td align="left">
  <form action="index.php?option=<?php echo _JOOM_OPTION; ?>&amp;act=ftpupload" method="post" name="dirForm">
  <select name="subdirectory"  size="1">
  <option><?php echo DS;?></option>
<?php
      foreach($dirs as $dir) {
       $dir = str_replace(JPath::clean(JPATH_ROOT.DS.$config->jg_pathftpupload),'',$dir);
       $selected = ($dir.DS == $subdirectory) ? " selected = \"selected\"" : "";
?>
    <option<?php echo $selected.">".$dir.DS; ?></option>
<?php
      }
?>
  </select>
  <input type="submit" value="<?php echo JText::_('JGA_CHANGE_FOLDER'); ?>" />
  </form>
    </td>
  </tr>
<?php
    }
?>
<form action="index.php?task=ftpuploadhandler" method="post" name="adminForm" enctype="multipart/form-data" onsubmit="return joom_checkme();">
  <input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
  <input type="hidden" name="approved" value="1" />
  <input type="hidden" name="owner" value="<?php echo $user->get('username'); ?>" />
  <input type="hidden" name="debug" value="<?php echo $this->debug; ?>" />
  <input type="hidden" name="subdirectory" value="<?php echo $subdirectory; ?>" />
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_PLEASE_SELECT_PICTURES'); ?>:
    </td>
    <td align="left">
<?php
     $imgFiles = JFolder::files(JPATH_ROOT.DS.$config->jg_pathftpupload.$subdirectory);  // portierung: JFolder::files anstatt mosReadDirectory
?>
      <select name="ftpfiles[]"  multiple size="20">
<?php
    foreach ($imgFiles as $file) {
      if (eregi("gif|jpe|jpeg|jpg|png", $file)) {
?>
        <option><?php echo $file; ?></option>
<?php
      }
    }
?>
      </select>
    </td>
  </tr>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_PICTURE_ASSIGN_TO_CATEGORY'); ?>
    </td>
    <td align="left">
<?php
    $clist = Joom_ShowDropDownCategoryList(0,'catid',' class="inputbox" size="1" style="width:194;"');
    echo $clist;
?>
    </td>
  </tr>
<?php
    if (!$config->jg_useorigfilename && $config->jg_filenamenumber) {
      $sup1 = "&sup1;";
      $sup2 = "&sup2;";
      if (!$config->jg_delete_original == 2) {
        $sup3 = "&sup2;";
      } else {
        $sup3 = "&sup3;";
      }
    } else {
      if (!$config->jg_delete_original == 2) {
        $sup3 = "&sup1;";
      } else {
        $sup2 = "&sup1;";
        $sup3 = "&sup2;";
      }
    }
    if (!$config->jg_useorigfilename && $config->jg_filenamenumber) {
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_COUNTER_NUMBER'); ?>&nbsp;<?php echo $sup1; ?>
    </td>
    <td align="left">
      <input type="text" name="filecounter" size="5" maxlength="5" />
    </td>
  </tr>
<?php
    }
    if (!$config->jg_useorigfilename) {
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_GENERIC_TITLE'); ?>
    </td>
    <td align="left">
      <input type="text" name="gentitle" size="34" maxlength="256" value="" />
    </td>
  </tr>
<?php
    }
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_GENERIC_DESCRIPTION') . ' ' . JText::_('JGA_OPTION'); ?>
    </td>
    <td align="left">
      <input type="text" name="gendesc" size="34" maxlength="1000" />
    </td>
  </tr>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_AUTHOR') . ' ' . JText::_('JGA_OPTION'); ?>
    </td>
    <td align="left">
      <input type="text" name="photocred" size="34" maxlength="256" />
    </td>
  </tr>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_DELETE_AFTER_UPLOAD'); ?>
    </td>
    <td align="left">
      <input type="checkbox" name="file_delete" value="1" checked="checked" />
    </td>
  </tr>
<?php
   if ($config->jg_delete_original == 2) {
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_DELETE_ORIGINAL_AFTER_UPLOAD'); ?>&nbsp;<?php echo $sup2; ?>
    </td>
    <td align="left">
      <input type="checkbox" name="original_delete" value="1" />
    </td>
  </tr>
<?php
   }
?>
  <tr>
    <td align="right">
      <?php echo JText::_('JGA_CREATE_SPECIAL_GIF'); ?>&nbsp;<?php echo $sup3; ?>
    </td>
    <td align="left">
      <input type="checkbox" name="create_special_gif" value="1" />
    </td>
  </tr>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_DEBUG_MODE'); ?>
      </div>
    </td>
    <td align="left">
      <input type="checkbox" name="debug" value="1" />
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <br /><input type="submit" value="<?php echo JText::_('JGA_UPLOAD'); ?>" />
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <div align="center" class="smallgrey">
<?php
    if (!$config->jg_useorigfilename && $config->jg_filenamenumber) {
?>
        <br /><?php echo $sup1; ?>&nbsp;<?php echo JText::_('JGA_COUNTER_NUMBER_ASTERISK'); ?>
<?php
    }
    if ($config->jg_delete_original == 2) {
?>
        <br /><?php echo $sup2; ?>&nbsp;<?php echo JText::_('JGA_DELETE_ORIGINAL_AFTER_UPLOAD_ASTERISK'); ?>
<?php
    }
?>
        <br /><?php echo $sup3; ?>&nbsp;<?php echo JText::_('JGA_CREATE_SPECIAL_GIF_ASTERISK'); ?>
        <br /><b><?php echo JText::_('JGA_DEBUG_MODE_ASTERISK'); ?></b>
      </div>
    </td>
  </tr>
</form>
</table>
<?php
  }


  /**
   * JAVA upload
   *
   */
  function Joom_ShowJUpload_HTML($cookieNavigator) {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();

    //cpanel
    echo "<script language = \"javascript\" type = \"text/javascript\">\n";
    echo "<!--\n";
    echo "function submitbutton(pressbutton) {\n";
    echo "  var form = document.adminForm;\n";
    echo "  if (pressbutton == 'cpanel') {\n";
    echo "    location.href = \"index.php?option="._JOOM_OPTION."\";\n";
    echo "  }\n";
    echo "}\n";
    echo "//-->\n";
    echo "</script>\n";
    echo "</form>";
?>
<!-- --------------------------------------------------------------------------------------------------- -->
<!-- --------     A DUMMY APPLET, THAT ALLOWS THE NAVIGATOR TO CHECK THAT JAVA IS INSTALLED   ---------- -->
<!-- --------               If no Java: Java installation is prompted to the user.            ---------- -->
<!-- --------------------------------------------------------------------------------------------------- -->
<!--"CONVERTED_APPLET"-->
<!-- HTML CONVERTER -->
<script language="JavaScript" type="text/javascript"><!--
    var _info = navigator.userAgent;
    var _ns = false;
    var _ns6 = false;
    var _ie = (_info.indexOf("MSIE") > 0 && _info.indexOf("Win") > 0 && _info.indexOf("Windows 3.1") < 0);
//--></script>
    <comment>
        <script language="JavaScript" type="text/javascript"><!--
        var _ns = (navigator.appName.indexOf("Netscape") >= 0 && ((_info.indexOf("Win") > 0 && _info.indexOf("Win16") < 0 && java.lang.System.getProperty("os.version").indexOf("3.5") < 0) || (_info.indexOf("Sun") > 0) || (_info.indexOf("Linux") > 0) || (_info.indexOf("AIX") > 0) || (_info.indexOf("OS/2") > 0) || (_info.indexOf("IRIX") > 0)));
        var _ns6 = ((_ns == true) && (_info.indexOf("Mozilla/5") >= 0));
//--></script>
    </comment>

<script language="JavaScript" type="text/javascript"><!--
    if (_ie == true) document.writeln('<object classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" WIDTH = "0" HEIGHT = "0" NAME = "JUploadApplet"  codebase="http://java.sun.com/update/1.5.0/jinstall-1_5-windows-i586.cab#Version=5,0,0,3"><noembed><xmp>');
    else if (_ns == true && _ns6 == false) document.writeln('<embed ' +
      'type="application/x-java-applet;version=1.5" \
            CODE = "wjhk.jupload2.EmptyApplet" \
            ARCHIVE = "<?php echo _JOOM_LIVE_SITE; ?>administrator/components/<?php echo _JOOM_OPTION; ?>/assets/java/wjhk.jupload.jar" \
            NAME = "JUploadApplet" \
            WIDTH = "0" \
            HEIGHT = "0" \
            type ="application/x-java-applet;version=1.6" \
            scriptable ="false" ' +
      'scriptable=false ' +
      'pluginspage="http://java.sun.com/products/plugin/index.html#download"><noembed><xmp>');
//--></script>
<applet  code = "wjhk.jupload2.EmptyApplet" ARCHIVE = "<?php echo _JOOM_LIVE_SITE; ?>administrator/components/<?php echo _JOOM_OPTION; ?>/assets/java/wjhk.jupload.jar" WIDTH = "0" HEIGHT = "0" NAME = "JUploadApplet"></xmp>
    <param name = CODE VALUE = "wjhk.jupload2.EmptyApplet" >
    <param name = ARCHIVE VALUE = "<?php echo _JOOM_LIVE_SITE; ?>administrator/components/<?php echo _JOOM_OPTION; ?>/assets/java/wjhk.jupload.jar" >
    <param name = NAME VALUE = "JUploadApplet" >
    <param name = "type" value="application/x-java-applet;version=1.5">
    <param name = "scriptable" value="false">
    <param name = "type" VALUE="application/x-java-applet;version=1.6">
    <param name = "scriptable" VALUE="false">
</xmp>
Java 1.5 or higher plugin required.
</applet>
</noembed>
</embed>
</object>
<form name="adminForm">
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
<input type="hidden" name="approved" value="1" />
<input type="hidden" name="owner" value="<?php echo $user->get('username'); ?>" />
<input type="hidden" name="debug" value="<?php echo $this->debug; ?>" />
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminlist">
  <tr>
    <td colspan="2">
      <div align="center">
        <?php echo JText::_('JGA_JUPLOAD_NOTE'); ?>
      </div>
    </td>
  </tr>
  <tr>
    <td align="right" width="50%">
      <div align="right">
        <?php echo JText::_('JGA_PICTURE_ASSIGN_TO_CATEGORY'); ?>
      </div>
    </td>
    <td align="left"  width="50%">
<?php
    $clist = Joom_ShowDropDownCategoryList(0,'catid',' class="inputbox" size="1" style="width:228;"');
    echo $clist;
?>
    </td>
  </tr>
<?php
    if($config->jg_delete_original == 2){
      $sup1 = '&sup1;';
      $sup2 = '&sup2;';
    } else {
      $sup2 = '&sup1;';
    }
      
    if (!$config->jg_useorigfilename) {
?>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_GENERIC_TITLE'); ?>
      </div>
    </td>
    <td align="left">
      <input type="text" name="gentitle" size="34" maxlength="256" value="" />
    </td>
  </tr>
<?php
    }
?>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_GENERIC_DESCRIPTION') . ' ' . JText::_('JGA_OPTION'); ?>
      </div>
    </td>
    <td align="left">
      <input type="text" name="gendesc" size="34" maxlength="1000" />
    </td>
  </tr>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_AUTHOR') . ' ' . JText::_('JGA_OPTION'); ?>
      </div>
    </td>
    <td align="left">
      <input type="text" name="photocred" size="34" maxlength="256" />
    </td>
  </tr>
<?php
    if ($config->jg_delete_original == 2) {
?>
  <tr>
    <td align="right">
        <?php echo JText::_('JGA_DELETE_ORIGINAL_AFTER_UPLOAD'); ?>&nbsp;&sup1;
    </td>
    <td align="left">
      <input type="checkbox" name="original_delete" value="1" />
    </td>
  </tr>
<?php
    }
?>
  <tr>
    <td align="right">
      <div align="right">
        <?php echo JText::_('JGA_CREATE_SPECIAL_GIF'); ?>&nbsp;<?php echo $sup2; ?>
      </div>
    </td>
    <td align="left">
      <input type="checkbox" name="create_special_gif" value="1" />
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <div align="center" class="smallgrey">
<?php
    if ($config->jg_delete_original == 2) {
?>
        <br /><?php echo $sup1; ?>&nbsp;<?php echo JText::_('JGA_DELETE_ORIGINAL_AFTER_UPLOAD_ASTERISK'); ?>
<?php
    }
?>
        <br /><?php echo $sup2; ?>&nbsp;<?php echo JText::_('JGA_CREATE_SPECIAL_GIF_ASTERISK'); ?>
      </div>
    </td>
  </tr>
  <tr>
  <?php
  
  //If 'originals deleted' setted in backend AND the picture has to be resized
  //this will be done local within in the applet, so only the detail picture
  //will be uploaded
  ?>
    <td colspan="2" align="center">
      <applet name="JUpload" code="wjhk.jupload2.JUploadApplet" archive="<?php echo _JOOM_LIVE_SITE; ?>administrator/components/<?php echo _JOOM_OPTION; ?>/assets/java/wjhk.jupload.jar" width="800" height="600" mayscript>
      <param name="postURL" value="<?php echo _JOOM_LIVE_SITE; ?>administrator/index.php?option=<?php echo _JOOM_OPTION; ?>&task=juploadhandler_receive">
      <param name="lookAndFeel" value="system">
      <param name="showLogWindow" value=false>
      <param name="showStatusBar" value="true">
      <param name="formdata" value="adminForm">
      <param name="debugLevel" value="0">
      <param name="afterUploadURL" value="javascript:alert('<?php echo JText::_('JGA_UPLOAD_COMPLETE',true); ?>');">
      <param name="nbFilesPerRequest" value="4">
      <param name="stringUploadSuccess" value="JOOMGALLERYUPLOADSUCCESS">
      <param name="stringUploadError" value="JOOMGALLERYUPLOADERROR (.*)">
      <param name="uploadPolicy" value="PictureUploadPolicy">
      <param name="allowedFileExtensions" value="jpg/jpeg/jpe/png/gif">
<?php
    if ($config->jg_delete_original && $config->jg_resizetomaxwidth) {
?>
      <param name="maxPicHeight" value="<?php echo $config->jg_maxwidth; ?>">
      <param name="maxPicWidth" value="<?php echo $config->jg_maxwidth; ?>">
      <param name="pictureCompressionQuality" value="<?php echo ($config->jg_picturequality/100); ?>">
<?php
    } else {
      //set picture quality to 1 to override the applet default of 0.8
?>
      <param name="pictureCompressionQuality" value="1">
<?php
    }
?>
      <param name="fileChooserImagePreview" value="false">
      <param name="fileChooserIconFromFileContent" value="-1">
<?php
    if (!$cookieNavigator){
?>
      <param name="readCookieFromNavigator" value="false">
      <param name="specificHeaders" value="Cookie: <?php echo $this->sessionname.'='.$this->sessiontoken;?>">
<?php
    }
?>
      Java 1.5 or higher plugin required.
      </applet>
    </td>
  </tr>
</table>
</form>
<?php
  }
}
?>
