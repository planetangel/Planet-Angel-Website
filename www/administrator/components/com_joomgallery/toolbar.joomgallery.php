<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/toolbar.joomgallery.php $
// $Id: toolbar.joomgallery.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once( $mainframe->getPath( 'toolbar_html' ) );

if ($act) $task = $act;

switch ($task) {

	// Categories
  case "categories":
    menujoomgallery::CATEGORIES_MENU();
    break;
  case "newcatg":
    menujoomgallery::NEW_CATEGORY_MENU();
    break;
  case "editcatg":
    menujoomgallery::EDIT_CATEGORY_MENU();
    break;
//case "movecatg":
//  menujoomgallery::MOVE_CATEGORIES_MENU();
//  break;

	// Pictures
  case "pictures":
    menujoomgallery::PICTURES_MENU();
    break;
  case "newpic":
    menujoomgallery::NEW_PICTURE_MENU();
    break;  
  case "editpic":
    menujoomgallery::EDIT_PICTURE_MENU();
    break;
  case "movepic":
    menujoomgallery::MOVE_PICTURES_MENU();
    break;

	// Comments
  case "comments":
    menujoomgallery::COMMENTS_MENU();
    break;

	// Uploads
  case "upload":
    menujoomgallery::UPLOAD_MENU('upload');
    break;
  //TODO
  case "uploadhandler":
    menujoomgallery::UPLOAD_MENU('upload');
    break;
  case "batchupload":
    menujoomgallery::UPLOAD_MENU('batchupload');
    break;
  //TODO
  case "batchuploadhandler":
    menujoomgallery::UPLOAD_MENU('batchupload');
    break;
  case "ftpupload":
    menujoomgallery::UPLOAD_MENU('ftpupload');
    break;
  //TODO
  case "ftpuploadhandler":
    menujoomgallery::UPLOAD_MENU('ftpupload');
    break;
  case "jupload":
    menujoomgallery::UPLOAD_MENU('jupload');
    break;
	// Configuration
  case "configuration":
    menujoomgallery::CONFIG_MENU();
    break;

	// CSS Edit
  case "editcss":
    if (file_exists(JPATH_COMPONENT_SITE.DS.'assets'.DS.'css'.DS.'joom_local.css')){
      menujoomgallery::CSS_MENU_EDIT();
    }else{
      menujoomgallery::CSS_MENU_NEW();
    }
    break;

	// VOTING
  case "votes":
   menujoomgallery::VOTING_MENU();
   break;

	// Migration
  case "migrate":
   menujoomgallery::MIGRATE_MENU();
   break;

	// Help, Information
  case "help":
   menujoomgallery::HELP_MENU();
   break;

	default:
    menujoomgallery::DEFAULT_MENU();
    break;
}
?>
