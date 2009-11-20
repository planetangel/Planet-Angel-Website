<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/admin.help.php $
// $Id: admin.help.php 449 2009-06-14 11:57:04Z aha $
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

class Joom_AdminHelp {

  /**
   * Constructor of class Joom_AdminHelp
   *
   * @return Joom_AdminHelp
   */
  function Joom_AdminHelp(){
    require_once (JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'admin.help.html.php');
    $htmladminhelp = new HTML_Joom_AdminHelp();
  }
}
?>
