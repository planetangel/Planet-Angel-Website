<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/uninstall.joomgallery.php $
// $Id: uninstall.joomgallery.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

function com_uninstall() {
  $db = & JFactory::getDBO();
  $db->setQuery("DELETE FROM #__modules WHERE position = 'joom_cpanel'");
  $db->query();
  echo 'JoomGallery was uninstalled successfully!';
}
?>
