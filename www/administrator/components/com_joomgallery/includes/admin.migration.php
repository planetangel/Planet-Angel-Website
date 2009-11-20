<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/admin.migration.php $
// $Id: admin.migration.php 449 2009-06-14 11:57:04Z aha $
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

class Joom_AdminMigration{

  /**
   * Constructor of class Joom_AdminMigration
   *
   * @return Joom_AdminMigration
   */
  function Joom_AdminMigration(){
    jimport('joomla.filesystem.file');
    require_once(JPATH_COMPONENT.DS.'adminclasses'.DS.'admin.migration.class.php');

    $migration = Joom_mosGetParam('migration', '');
    $action    = Joom_mosGetParam('migration_action', 'check');

    if($migration != ''){
      if(JFile::exists(JPATH_COMPONENT.DS.'adminclasses'.DS.'admin.migrate'.$migration.'.class.php')){
        require_once(JPATH_COMPONENT.DS.'adminclasses'.DS.'admin.migrate'.$migration.'.class.php');
        $classname    = 'Joom_Migrate_'.$migration;
        $migrateclass = new $classname($action);
      }
    }

    //check if not in running migration
    if($action != 'start' && $action != 'continue'){
      //show migration manager
      require_once (JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'admin.migration.html.php');
      $files = JFolder::files(JPATH_COMPONENT.DS.'adminclasses'.DS, '.php$');
      $other = array('admin.migration.class.php', 'admin.upload.class.php', 'admin.tabs.class.php');
      foreach($files as $key => $file){
        if(in_array($file, $other)){
          unset($files[$key]);
        }
      }
      $htmladminmigration = new HTML_Joom_AdminMigration($files);
    }
  }
}
?>
