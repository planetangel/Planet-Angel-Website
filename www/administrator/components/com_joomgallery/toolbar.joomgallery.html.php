<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/toolbar.joomgallery.html.php $
// $Id: toolbar.joomgallery.html.php 449 2009-06-14 11:57:04Z aha $
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

/** portierung **/
# JToolbarHelper statt mosMenuBar
# startTable() und endTable() entfernt
# FRAGE: Jedes Mal 'JoomGallery :: ' davor setzen?
# 'if (defined('_JEXEC')){' und '}else{...}' entfernt
# JToolbarHelper::title(JText::_(... eingefuegt
# JToolbarHelper::title(JText::_(...
/* */

global $submenu_anders;
$submenu_anders = 0;
class menujoomgallery {

// Categories
  function CATEGORIES_MENU() {
    global $submenu_anders;
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_CATEGORY_MANAGER'), 'categories');
    JToolbarHelper::publishList('publishcatg','JGA_TOOLBAR_PUBLISH');
    JToolbarHelper::unpublishList('unpublishcatg','JGA_TOOLBAR_UNPUBLISH');
    JToolbarHelper::spacer();
    JToolbarHelper::divider();
    JToolbarHelper::spacer();
    JToolbarHelper::addNew('newcatg','JGA_TOOLBAR_NEW');
    JToolbarHelper::editList('editcatg','JGA_TOOLBAR_EDIT');
    JToolbarHelper::deleteList('','removecatg','JGA_TOOLBAR_REMOVE');
    JToolbarHelper::spacer();
    JToolbarHelper::divider();
    JToolbarHelper::spacer();
    JToolbarHelper::custom('cpanel','config.png','config.png','CPANEL',false);
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }

  function NEW_CATEGORY_MENU() {
    global $submenu_anders;
    JRequest::setVar('hidemainmenu', 1);
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_CATEGORY_MANAGER').' :: '.JText::_('JGA_ADD_CATEGORY') .' '. JText::_('JGA_CATEGORY'));
    JToolbarHelper::save('savenewcatg','JGA_TOOLBAR_SAVE');
    JToolbarHelper::cancel('cancelcatg','JGA_TOOLBAR_CANCEL');
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }

  function EDIT_CATEGORY_MENU() {
    global $submenu_anders;
    JRequest::setVar('hidemainmenu', 1);
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_CATEGORY_MANAGER').' :: '.JText::_('JGA_EDIT_CATEGORY') .' ' .JText::_('JGA_CATEGORY'));
    JToolbarHelper::save('saveeditcatg','JGA_TOOLBAR_SAVE');
    JToolbarHelper::cancel('cancelcatg','JGA_TOOLBAR_CANCEL');
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }

// Pictures
  function PICTURES_MENU() {
    global $submenu_anders;
    $document = &JFactory::getDocument();

    $document->addStyleDeclaration('    .icon-32-refresh {
      background-image:url(templates/khepri/images/toolbar/icon-32-refresh.png);
    }');
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_PICTURE_MANAGER'), 'mediamanager');
    JToolbarHelper::publishList('publishpic','JGA_TOOLBAR_PUBLISH');
    JToolbarHelper::unpublishList('unpublishpic','JGA_TOOLBAR_UNPUBLISH');
    JToolbarHelper::custom('approvepic','upload.png','upload_f2.png','JGA_TOOLBAR_APPROVE');
    JToolbarHelper::divider();
    JToolbarHelper::addNew('newpic','JGA_TOOLBAR_NEW');
    JToolbarHelper::editList('editpic','JGA_TOOLBAR_EDIT');
    JToolbarHelper::custom('movepic','move.png','move.png','JGA_TOOLBAR_MOVE');
    JToolbarHelper::custom('recreate', 'refresh.png', 'refresh.png', 'JGA_TOOLBAR_RECREATE', false);
    JToolbarHelper::deleteList('','removepic','JGA_TOOLBAR_REMOVE');
    JToolbarHelper::divider();
    JToolbarHelper::custom('cpanel','config.png','config.png','JGA_TOOLBAR_CPANEL',false);
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }

  function NEW_PICTURE_MENU() {
    global $submenu_anders;
    JRequest::setVar('hidemainmenu', 1);
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_PICTURE_MANAGER').' :: '.JText::_('JGA_PICTURE_ADD'));
    JToolbarHelper::save('savenewpic','JGA_TOOLBAR_SAVE');
    JToolbarHelper::cancel('canceleditpic','JGA_TOOLBAR_CANCEL');
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }

  function EDIT_PICTURE_MENU() {
    global $submenu_anders;
    JRequest::setVar('hidemainmenu', 1);
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_PICTURE_MANAGER').' :: '.JText::_('JGA_PICTURE_EDIT'));
    JToolbarHelper::save('saveeditpic','JGA_TOOLBAR_SAVE');
    JToolbarHelper::cancel('canceleditpic','JGA_TOOLBAR_CANCEL');
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }

  function MOVE_PICTURES_MENU() {
    global $submenu_anders;
    JRequest::setVar('hidemainmenu', 1);
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_PICTURE_MANAGER').' :: '.JText::_('JGA_MOVE_PICTURE'));
    JToolbarHelper::save('savemovepic','JGA_TOOLBAR_SAVE');
    JToolbarHelper::cancel('canceleditpic','JGA_TOOLBAR_CANCEL');
    //JToolbarHelper::spacer();
    //JToolbarHelper::custom('cpanel','config.png','config.png','JGA_TOOLBAR_CPANEL',false);
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }


	// Comments
  function COMMENTS_MENU() {
    global $submenu_anders;
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_COMMENTS_MANAGER'));
    JToolbarHelper::publishList('publishcmt','JGA_TOOLBAR_PUBLISH_COMMENT');
    JToolbarHelper::unpublishList('unpublishcmt','JGA_TOOLBAR_UNPUBLISH_COMMENT');
    JToolbarHelper::custom('approvecmt','upload.png','upload_f2.png','JGA_TOOLBAR_APPROVE_COMMENT');
    JToolbarHelper::spacer();
    JToolbarHelper::divider();
    JToolbarHelper::spacer();
    JToolbarHelper::deleteList(' ','removecmt','JGA_TOOLBAR_REMOVE_COMMENT');
    JToolbarHelper::spacer();
    JToolbarHelper::divider();
    JToolbarHelper::spacer();
    JToolbarHelper::custom('cpanel','config.png','config.png','JGA_TOOLBAR_CPANEL',false);
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }


	// Uploads
  function UPLOAD_MENU($act) {
    global $submenu_anders;
    if($submenu_anders == 1) {
      JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
      JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
      JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    }
    //ToolbarHelper::startTable();
    switch($act){
      case 'upload':JToolBarHelper::title(JText::_('JGA_PICTURE_UPLOAD_MANAGER'));
	      if($submenu_anders == 1) {
	        JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
	        JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
	        JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
	      }
	      break;
      case 'batchupload':JToolBarHelper::title(JText::_('JGA_BATCH_UPLOAD_MANAGER'));
				if($submenu_anders == 1) {
					JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
					JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
					JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
				}
				break;
      case 'ftpupload':JToolBarHelper::title(JText::_('JGA_FTP_UPLOAD_MANAGER'));
				if($submenu_anders == 1) {
					JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
					JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
					JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
				}
				break;
			case 'jupload':JToolBarHelper::title(JText::_('JGA_JAVA_UPLOAD_MANAGER'));
				if($submenu_anders == 1) {
					JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
					JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
					JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
				}
				break;
    }
    JToolbarHelper::back();
    JToolbarHelper::spacer();
    JToolbarHelper::divider();
    JToolbarHelper::spacer();
    JToolbarHelper::custom('cpanel','config.png','config.png','JGA_TOOLBAR_CPANEL',false);
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }


	// Configuration
  function CONFIG_MENU() {
    global $submenu_anders;
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_CONFIGURATION_MANAGER'),'config');
    JToolbarHelper::save('saveconfiguration','JGA_TOOLBAR_SAVE');
    JToolbarHelper::spacer();
    JToolbarHelper::divider();
    JToolbarHelper::spacer();
    JToolbarHelper::custom('cpanel','config.png','config.png','JGA_TOOLBAR_CPANEL',false);
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
  }

	// CSS Edit (new file)
  function CSS_MENU_NEW() {
    //JToolBarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_CSS_MANAGER') . " (".JText::_('JGA_TOOLBAR_NEW').")",'config');
    JToolBarHelper::save('savecss','JGA_TOOLBAR_SAVE');
    JToolBarHelper::cancel('cancelcss','JGA_TOOLBAR_CANCEL');
    JToolBarHelper::spacer();
    //JToolBarHelper::endTable();
  }
  
	// CSS Edit (edit file)
  function CSS_MENU_EDIT() {
    //JToolBarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_CSS_MANAGER') . " (".JText::_('JGA_TOOLBAR_EDIT').")",'config');
    JToolBarHelper::trash('deletecss', 'JGA_TOOLBAR_DEL_CSS');
    JToolBarHelper::save('savecss','JGA_TOOLBAR_SAVE');
    JToolBarHelper::cancel('cancelcss','JGA_TOOLBAR_CANCEL');
    JToolBarHelper::spacer();
    //JToolBarHelper::endTable();
  }

  // Voting
  function VOTING_MENU() {
    global $submenu_anders;
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_VOTES_MANAGER'));
    JToolbarHelper::custom('cpanel','config.png','config.png','JGA_TOOLBAR_CPANEL',false);
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }

  // Migration
  function MIGRATE_MENU() {
    global $submenu_anders;
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_MIGRATION_MANAGER'));
    JToolbarHelper::custom('cpanel','config.png','config.png','JGA_TOOLBAR_CPANEL',false);
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }


  function HELP_MENU() {
    global $submenu_anders;
    //JToolbarHelper::startTable();
    JToolBarHelper::title(JText::_('JGA_HELP_MANAGER') , 'systeminfo');
    JToolbarHelper::custom('cpanel','config.png','config.png','JGA_TOOLBAR_CPANEL',false);
    JToolbarHelper::spacer();
    //JToolbarHelper::endTable();
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }

	// Default
  function DEFAULT_MENU() {
    global $submenu_anders;
    $document = &JFactory::getDocument();

    JToolBarHelper::title( JText::_('JGA_ADMINMENU') , 'joom');
    $document->addStyleDeclaration(".icon-48-joom{
    background:transparent url(components/com_joomgallery/assets/images/joom_logo.png) no-repeat scroll left center;\n}\n");
    //JSubMenuHelper::addEntry(false);
    if($submenu_anders != 1)return;
    JSubMenuHelper::addEntry(JText::_('JGA_CATEGORY_MANAGER'),'index.php?option=com_joomgallery&amp;act=categories');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_MANAGER'),'index.php?option=com_joomgallery&amp;act=pictures');
    JSubMenuHelper::addEntry(JText::_('JGA_COMMENTS_MANAGER'),'index.php?option=com_joomgallery&amp;act=comments');
    JSubMenuHelper::addEntry(JText::_('JGA_PICTURE_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=upload');
    JSubMenuHelper::addEntry(JText::_('JGA_BATCH_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=batchupload');
    JSubMenuHelper::addEntry(JText::_('JGA_FTP_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=ftpupload');
    JSubMenuHelper::addEntry(JText::_('JGA_JAVA_UPLOAD_MANAGER'),'index.php?option=com_joomgallery&amp;act=jupload');
    JSubMenuHelper::addEntry(JText::_('JGA_CONFIGURATION_MANAGER'),'index.php?option=com_joomgallery&amp;act=configuration');
  }

}
?>
