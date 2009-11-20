<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/admin.uploads.php $
// $Id: admin.uploads.php 449 2009-06-14 11:57:04Z aha $
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

include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'admin.uploads.html.php');

class Joom_AdminUploads {
  var $debug;
  var $batchul;
  var $subdirectory;
  var $sessionname;
  var $sessiontoken;

  /**
   * Constructor of class Joom_AdminUploads
   * single upload
   * batch upload (zip)
   * FTP upload
   * JAVA upload
   *
   * @param string $task
   * @return Joom_AdminUploads
   */
  function Joom_AdminUploads($task) {

    $this->debug        = JRequest::getInt('debug', 0);
    $this->subdirectory = stripslashes(Joom_mosGetParam('subdirectory', DS, 'post'));

    switch($task) {
      case 'upload':
        $this->Joom_ShowUpload();
        break;
      case 'batchupload':
        $this->Joom_ShowBatchUpload();
        break;
      case 'ftpupload':
        $this->Joom_ShowFTPUpload();
        break;
      case 'jupload':
        $this->Joom_ShowJUpload();
        break;
   }
  }


  /**
   * Single Upload
   * Outputs the javascripts for checking the entries
   * and calls Joom_ShowUpload_HTML
   *
   */
  function Joom_ShowUpload() {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();

    echo "  <script language = \"javascript\" type = \"text/javascript\">\n";
    echo "  <!--\n";
    echo "function submitbutton(pressbutton) {\n";
    echo "  if (pressbutton == 'cpanel') {\n";
    echo "   location.href = \"index.php?option="._JOOM_OPTION."\";\n";
    echo "  }\n";
    echo "}\n";
    echo "  function joom_checkme() {\n";
    echo "    var form = document.adminForm;\n";
    echo "    var ffwrong = '".$config->jg_wrongvaluecolor."';\n";
    echo "    form.catid.style.backgroundColor = '';\n";
    echo "    var doublefiles = false;\n";
    // do field validation
    if(!$config->jg_useorigfilename) {
      echo "    form.gentitle.style.backgroundColor = '';\n";
      echo "    if (form.gentitle.value == '' || form.gentitle.value == null) {\n";
      echo "      alert('".JText::_('JGA_ALERT_PICTURE_MUST_HAVE_TITLE',true)."');\n";
      echo "      form.gentitle.style.backgroundColor = ffwrong;\n";
      echo "      form.gentitle.focus();\n";
      echo "      return false;\n";
      echo "    }\n";
    }
    echo "    if (form.catid.value == '0') {\n";
    echo "      alert('".JText::_('JGA_ALERT_YOU_MUST_SELECT_CATEGORY',true)."');\n";
    echo "      form.catid.style.backgroundColor = ffwrong;\n";
    echo "      form.catid.focus();\n";
    echo "      return false;\n";
    echo "    }\n";
    //checks if files already exist
    echo "    else {\n";
    echo "     var zaehl = 0;\n";
    echo "     var arenofiles = true;\n";
    echo "     var fullfields = new Array();\n";
    echo "     var screenshotfieldname = new Array();\n";
    echo "     var screenshotfieldvalue = new Array();\n";
    echo "     for(i=0;i<10;i++) {\n";
    echo "      screenshotfieldname[i] = 'arrscreenshot['+i+']';\n";
    echo "      screenshotfieldvalue[i] = document.getElementsByName(screenshotfieldname[i])[0].value;\n";
    echo "      document.getElementsByName(screenshotfieldname[i])[0].style.backgroundColor='';\n";
    echo "      if(screenshotfieldvalue[i] != '') {\n";
    echo "       arenofiles = false;\n";
    echo "       fullfields[zaehl] = i;\n";
    echo "       zaehl++;\n";
    echo "      }\n";
    echo "     }\n";
    echo "    }\n";
    echo "    if(arenofiles) {\n";
    echo "     alert('". JText::_('JGA_ALERT_YOU_MUST_SELECT_ONE_PICTURE',true)."');\n";
    echo "     document.getElementsByName(screenshotfieldname[0])[0].focus();\n";
    echo "     return false;\n";
    echo "    }\n";
    //check the file types .jpg,.gif or .png 
    echo "    else {\n";
    echo "     var extensionsnotok = false;\n";
    echo "     var searchextensiontest = new Array();\n";
    echo "     var searchextension = new Array();\n";
    //However you have to define this RegExp for each item.
    for ($i=0; $i < 10; $i++) {
      echo "      searchextension[$i] = new RegExp('\.jpg$|\.jpeg$|\.jpe$|\.gif$|\.png$','ig');\n";
    }
    echo "     for(i=0;i<fullfields.length;i++) {\n";
    echo "      searchextensiontest = searchextension[i].test(screenshotfieldvalue[fullfields[i]]);\n";
    echo "      if(searchextensiontest!=true) {\n";
    echo "       extensionsnotok = true;\n";
    echo "       document.getElementsByName(screenshotfieldname[fullfields[i]])[0].style.backgroundColor = ffwrong;\n";
    echo "      }\n";
    echo "     }\n";
    echo "    }\n";
    echo "    if(extensionsnotok) {\n";
    echo "     alert('".str_replace("\n",'\n',JText::_('JGA_ALERT_WRONG_EXTENSION',true))."');\n";
    echo "     document.getElementsByName(screenshotfieldname[0])[0].focus();\n";
    echo "     return false;\n";
    echo "    }\n";
    echo "    else {\n";
    echo "     var filenamesnotok = false;\n";
    if($config->jg_filenamewithjs!=0) {
      echo "     var searchwrongchars = /[^ a-zA-Z0-9_-]/;\n";
      echo "     var lastbackslash = new Array();\n";
      echo "     var endoffilename = new Array();\n";
      echo "     var filename = new Array();\n";
      echo "     for(i=0;i<fullfields.length;i++) {\n";
      echo "      lastbackslash[i] = screenshotfieldvalue[fullfields[i]].lastIndexOf('\\\\');\n";
      echo "      if(lastbackslash[i]<1) {\n";
      echo "       lastbackslash[i] = screenshotfieldvalue[fullfields[i]].lastIndexOf('/');\n";
      echo "      }\n";
      echo "      endoffilename[i] = screenshotfieldvalue[fullfields[i]].lastIndexOf('\\.')-screenshotfieldvalue[fullfields[i]].length;\n";
      echo "      filename[i] = screenshotfieldvalue[fullfields[i]].slice(lastbackslash[i]+1,endoffilename[i]);\n";
      echo "      if(searchwrongchars.test(filename[i])) {\n";
      echo "       filenamesnotok = true;\n";
      echo "       document.getElementsByName(screenshotfieldname[fullfields[i]])[0].style.backgroundColor = ffwrong;\n";
      echo "      }\n";
      echo "     }\n";
    }
    echo "    }\n";
    echo "    if(filenamesnotok) {\n";
    echo "     alert('".str_replace("\n",'\n',JText::_('JGA_ALERT_WRONG_FIILENAME',true))."');\n";
    echo "     document.getElementsByName(screenshotfieldname[0])[0].focus();\n";
    echo "     return false;\n";
    echo "    }\n";
    echo "    else if(fullfields.length>1) {\n";
    echo "     var feld1 = new Number();\n";
    echo "     var feld2 = new Number();\n";
    echo "     for(i=0;i<fullfields.length;i++) {\n";
    echo "      for(j=fullfields.length-1;j>i;j--) {\n";
    echo "       if(screenshotfieldvalue[fullfields[i]].indexOf(screenshotfieldvalue[fullfields[j]])==0) {\n";
    echo "        doublefiles = true;\n";
    echo "        document.getElementsByName(screenshotfieldname[fullfields[i]])[0].style.backgroundColor = ffwrong;\n";
    echo "        document.getElementsByName(screenshotfieldname[fullfields[j]])[0].style.backgroundColor = ffwrong;\n";
    echo "        feld1 = i+1;\n";
    echo "        feld2 = j+1\n";
    echo "        alert('".str_replace("\n",'\n',JText::_('JGA_ALERT_FILENAME_DOUBLE1',true))." '+feld1+' ".JText::_('JGA_ALERT_FILENAME_DOUBLE2',true)." '+feld2+'.');\n";
    echo "       }\n";
    echo "      }\n";
    echo "     }\n";
    echo "    }\n";
    echo "    if(doublefiles) {\n";
    echo "     document.getElementsByName(screenshotfieldname[0])[0].focus();\n";
    echo "     return false;\n";
    echo "    }\n";
    echo "    else {\n";
    echo "     form.submit();\n";
    echo "     return true;\n";
    echo "   }\n";
    echo "  }\n";
    echo "  //-->\n";
    echo "  </script>\n";
  
    HTML_Joom_AdminUploads::Joom_ShowUpload_HTML();
  }
  
  /**
   * Batch Upload
   * Outputs the javascripts for checking the entries
   * and calls Joom_ShowBatchUpload_HTML
   * 
  */
  function Joom_ShowBatchUpload() {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
  
    echo "<script language=\"Javascript\" type=\"text/javascript\">\n";
    echo "<!--\n";
    echo "function submitbutton(pressbutton) {\n";
    echo "  if (pressbutton == 'cpanel') {\n";
    echo "   location.href = \"index.php?option="._JOOM_OPTION."\";\n";
    echo "  }\n";
    echo "}\n";
    echo "function joom_checkme() {\n";
    echo "\n";
    echo "    var form = document.adminForm;\n";
    echo "    var ffwrong = '".$config->jg_wrongvaluecolor."';\n";
    echo "    form.zippack.style.backgroundColor = '';\n";
    echo "    form.catid.style.backgroundColor = '';\n";
    if(!$config->jg_useorigfilename) {
      echo "    form.gentitle.style.backgroundColor = '';\n";
    }
    echo "    if (form.zippack.value == '' || form.zippack.value == null) {\n";
    echo "     alert('".JText::_('JGA_ALERT_YOU_MUST_SELECT_ONE_FILE',true)."');\n";
    echo "     form.zippack.style.backgroundColor = ffwrong;\n";
    echo "     form.zippack.focus();\n";
    echo "     return false;\n";
    echo "    }\n";
    if(!$config->jg_useorigfilename) {
      echo "    else if (form.gentitle.value == '' || form.gentitle.value == null) {\n";
      echo "     alert('".JText::_('JGA_ALERT_PICTURE_MUST_HAVE_TITLE',true)."');\n";
      echo "     form.gentitle.style.backgroundColor = ffwrong;\n";
      echo "     form.gentitle.focus();\n";
      echo "     return false;\n";
      echo "    }\n";
    }
    echo "\n";
    echo "    var filecounterok = true;\n";
    if ( !$config->jg_useorigfilename && $config->jg_filenamenumber) {
      echo "    form.filecounter.style.backgroundColor = '';\n";
      echo "    if (form.filecounter.value != '') {\n";
      echo "     var searchwrongchars1 = /[^0-9]/;\n";
      echo "     if(searchwrongchars1.test(form.filecounter.value)) {\n";
      echo "      filecounterok = false;\n";
      echo "      alert('".JText::_('JGA_ALERT_WRONG_VALUE',true)."');\n";
      echo "      form.filecounter.style.backgroundColor = ffwrong;\n";
      echo "      form.filecounter.focus();\n";
      echo "      return false;\n";
      echo "     }\n";
      echo "    }\n";
      echo "\n";
    }
    echo "    if (form.catid.value == '0' && filecounterok) {\n";
    echo "     alert('".JText::_('JGA_ALERT_YOU_MUST_SELECT_CATEGORY')."');\n";
    echo "     form.catid.style.backgroundColor = ffwrong;\n";
    echo "     form.catid.focus();\n";
    echo "     return false;\n";
    echo "    }\n";
    echo "\n";
    echo "    else {\n";
    echo "     var filenamesnotok = false;\n";
    if($config->jg_filenamewithjs!=0  && $config->jg_useorigfilename==0) {
      echo "     var searchwrongchars = /[^ a-zA-Z0-9_-]/;\n";
      echo "     if(searchwrongchars.test(form.gentitle.value)) {\n";
      echo "      filenamesnotok = true;\n";
      echo "     }\n";
    }
    echo "    }\n";
    echo "    if(filenamesnotok) {\n";
    echo "     alert('".str_replace("\n",'\n',JText::_('JGA_ALERT_WRONG_FIILENAME',true))."');\n";
    echo "     form.gentitle.style.backgroundColor = ffwrong;\n";
    echo "     form.gentitle.focus();\n";
    echo "     return false;\n";
    echo "    }\n";
    echo "    else {\n";
    echo "     form.submit();\n";
    echo "    return true;\n";
    echo "     }\n";
    echo "\n";
    echo "}\n";
    echo "//-->\n";
    echo "</script>\n";
  
    HTML_Joom_AdminUploads::Joom_ShowBatchUpload_HTML();
  }
  
  
  /**
   * FTP Upload
   * Outputs the javascripts for checking the entries
   * and calls Joom_ShowFTPUpload_HTML
   * 
  */
  
  function Joom_ShowFTPUpload() {
    $config = Joom_getConfig();
    /** portierung **/
    $mainframe = & JFactory::getApplication('administrator');  // anstatt global $mainframe;
    $database = & JFactory::getDBO(); // anstatt global $database;
    $user = & JFactory::getUser();  // anstatt global $my;
    /* */
  
    echo "<script language=\"Javascript\" type=\"text/javascript\">\n";
    echo "<!--\n";
    echo "function submitbutton(pressbutton) {\n";
    echo "  if (pressbutton == 'cpanel') {\n";
    echo "   location.href = \"index.php?option="._JOOM_OPTION."\";\n";
    echo "  }\n";
    echo "}\n";
    echo "function joom_checkme() {\n";
    echo "\n";
    echo "    var form = document.adminForm;\n";
    echo "    var ffwrong = '".$config->jg_wrongvaluecolor."';\n";
    echo "    form.catid.style.backgroundColor = '';\n";
    if($config->jg_useorigfilename==0) {
      echo "    form.gentitle.style.backgroundColor = '';\n";
      echo "    if (form.gentitle.value == '' || form.gentitle.value == null) {\n";
      echo "     alert('".JText::_('JGA_ALERT_PICTURE_MUST_HAVE_TITLE',true)."');\n";
      echo "     form.gentitle.style.backgroundColor = ffwrong;\n";
      echo "     form.gentitle.focus();\n";
      echo "     return false;\n";
      echo "    }\n";
    }
    echo "\n";
    echo "    var filecounterok = true;\n";
    if ( !$config->jg_useorigfilename && $config->jg_filenamenumber) {
      echo "    form.filecounter.style.backgroundColor = '';\n";
      echo "    if (form.filecounter.value != '') {\n";
      echo "     var searchwrongchars1 = /[^0-9]/;\n";
      echo "     if(searchwrongchars1.test(form.filecounter.value)) {\n";
      echo "      filecounterok = false;\n";
      echo "      alert('".JText::_('JGA_ALERT_WRONG_VALUE',true)."');\n";
      echo "      form.filecounter.style.backgroundColor = ffwrong;\n";
      echo "      form.filecounter.focus();\n";
      echo "      return false;\n";
      echo "     }\n";
      echo "    }\n";
      echo "\n";
    }
    echo "    if (form.catid.value == '0' && filecounterok) {\n";
    echo "     alert('".JText::_('JGA_ALERT_YOU_MUST_SELECT_CATEGORY')."');\n";
    echo "     form.catid.style.backgroundColor = ffwrong;\n";
    echo "     form.catid.focus();\n";
    echo "     return false;\n";
    echo "    }\n";
    echo "\n";
    echo "    else {\n";
    echo "     var filenamesnotok = false;\n";
    if($config->jg_filenamewithjs!=0  && $config->jg_useorigfilename==0) {
      echo "     var searchwrongchars = /[^ a-zA-Z0-9_-]/;\n";
      echo "     if(searchwrongchars.test(form.gentitle.value)) {\n";
      echo "      filenamesnotok = true;\n";
      echo "     }\n";
    }
    echo "    }\n";
    echo "    if(filenamesnotok) {\n";
    echo "     alert('".str_replace("\n",'\n',JText::_('JGA_ALERT_WRONG_FIILENAME',true))."');\n";
    echo "     form.gentitle.style.backgroundColor = ffwrong;\n";
    echo "     form.gentitle.focus();\n";
    echo "     return false;\n";
    echo "    }\n";
    echo "    else {\n";
    echo "     form.submit();\n";
    echo "    return true;\n";
    echo "     }\n";
    echo "\n";
    echo "}\n";
    echo "//-->\n";
    echo "</script>\n";
  
    HTML_Joom_AdminUploads::Joom_ShowFTPUpload_HTML();
  }
  
  /**
   * JAVA Upload
   *
   */
  function Joom_ShowJUpload() {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
    $mainframe = & JFactory::getApplication('administrator');

    //check the php.ini setting 'session.cookie_httponly'
    //if set and = 1 then build the parameter 'readCookieFrom Navigator=false'
    //in Applet (new since V 4.2.1c)
    //and provide the cookie with sessionname=token in parameter 'specificHeaders' 
    $cookieNavigator=true;
    $sesscook=@ini_get('session.cookie_httponly');
    if (!empty($sesscook) && $sesscook==1){
      $cookieNavigator=false;
      //get the actual session
      $currentSession=JSession::getInstance('',array());
      $this->sessionname=$currentSession->getName();
      //function getToken() delivers wrong token, so get the right one
      //from $_COOKIE array (since PHP 4.1.0)
      $this->sessiontoken=$_COOKIE[$this->sessionname];
    }
    HTML_Joom_AdminUploads::Joom_ShowJUpload_HTML($cookieNavigator);
  }

}
?>
