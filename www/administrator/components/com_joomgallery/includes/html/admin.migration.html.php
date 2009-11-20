<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/html/admin.migration.html.php $
// $Id: admin.migration.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_AdminMigration {

  /**
   * Migration manager
   *
   * @return HTML_Joom_AdminMigration
   */
  function HTML_Joom_AdminMigration($files) {

    //cpanel
    echo "<script language = \"javascript\" type = \"text/javascript\">\n";
    echo "function submitbutton(pressbutton) {\n";
    echo "  var form = document.adminForm;\n";
    echo "  if (pressbutton == 'cpanel') {\n";
    echo "    location.href = \"index.php?option="._JOOM_OPTION."\";\n";
    echo "  }\n";
    echo "}\n";
    echo "</script>\n";
?>

        <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
<?php
    $show_jmtablerow = true;
    foreach($files as $file){
      require_once(JPATH_COMPONENT.DS.'adminclasses'.DS.$file);
    }
?>
        </table>
<?php
  }
}
?>
