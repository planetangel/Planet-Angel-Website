<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/admin.cssedit.php $
// $Id: admin.cssedit.php 449 2009-06-14 11:57:04Z aha $
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

class Joom_AdminCssEdit  {

  var $cssPath;
  var $localCssFile;

  /**
   * Constructor of class Joom_AdminCssEdit
   * editing indvidual CSS settings in backend and save them in joom.local.css
   *
   * @param string $task
   * @return Joom_AdminCssEdit
   */
  function Joom_AdminCssEdit($task){
    $this->cssPath = JPATH_COMPONENT_SITE.DS.'assets'.DS.'css'.DS;
    $this->localCssFile = $this->cssPath . 'joom_local.css';

    $filecontent = Joom_mosGetParam("csscontent", '');

    switch($task){
      case "cancelcss":
        $this->cancelCss();
        break;
      case "savecss":
        $this->saveCss($filecontent);
        break;
      case "deletecss":
        $this->deleteCss();
        break;
      default:
        $this->displayCssEdit();
    }
  }

  /**
   * Check permissions on CSS file
   * and display the content via HTML_Joom_AdminCssEdit()
   *
   */
  function displayCssEdit(){
    // error warning msg for CSS editor
    $msg = '';
    jimport('joomla.filesystem.file');

    $cssfile = $this->cssPath . 'joom_local.css.README';
    $editExistingFile = file_exists($this->localCssFile);
    if ($editExistingFile){
      $cssfile = $this->localCssFile;
      // test by trying to set permissions:
      Joom_Chmod($cssfile, 0766);
      if (!is_writable($cssfile)){
        $msg = JText::_('JGA_CSS_WARNING_PERMS');
      }
    } else{
      if (!is_writable($this->cssPath)){
        $msg = JText::_('JGA_CSS_WARNING_PERMS');
      }
    }

    if(!$content = JFile::read($cssfile)) {
      // output error, overwrite last error (this one is more important)
      $msg = JText::_('JGA_CSS_ERROR_READING') . $cssfile;
    } else {
      $content = htmlspecialchars($content,ENT_QUOTES,'UTF-8');
    }

    require_once (JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'admin.cssedit.html.php');
    $htmladmincss = new HTML_Joom_AdminCssEdit($content, $this->localCssFile, $editExistingFile, $msg);
  }

  /**
   * Write edited content to CSS file
   *
   * @param string $content
   */
  function saveCss($content){
    $mainframe = & JFactory::getApplication('administrator');
    jimport('joomla.filesystem.file');

    if (file_exists($this->localCssFile) && !is_writable($this->localCssFile)){
      $mainframe->redirect('index.php?option='._JOOM_OPTION, JText::_('JGA_CSS_ERROR_WRITING').$this->localCssFile, 'error');
    }
    $content = stripcslashes($content);
    if(JFile::write($this->localCssFile,$content)) {
      $mainframe->redirect('index.php?option='._JOOM_OPTION, JText::_('JGA_CSS_SAVED'));
    } else{
      $mainframe->redirect('index.php?option='._JOOM_OPTION, JText::_('JGA_CSS_ERROR_WRITING').$this->localCssFile, 'error');
    }
  }

  /**
   * Delete local.joom.css
   *
   */
  function deleteCss(){
    $mainframe = & JFactory::getApplication('administrator');
    jimport('joomla.filesystem.file');

    if (JFile::delete($this->localCssFile)){
      $mainframe->redirect('index.php?option='._JOOM_OPTION, JText::_('JGA_CSS_DELETED'));
    }else{
      $mainframe->redirect('index.php?option='._JOOM_OPTION, JText::_('JGA_CSS_ERROR_DELETING').$this->localCssFile, 'error');
    }
  }

  /**
   * Cancel ediiting css file
   *
   */
  function cancelCss(){
    $mainframe = & JFactory::getApplication('administrator');
    $mainframe->redirect('index.php?option='._JOOM_OPTION, JText::_('JGA_CSS_CANCELED'));
  }
}
?>
