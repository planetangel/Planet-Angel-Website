<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/install.joomgallery.php $
// $Id: install.joomgallery.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

  function com_install() {
    $mainframe = & JFactory::getApplication('administrator');
    $db =& JFactory::getDBO();
    //jimport('joomla.filesystem.folder');

    //$src  = 'components' .DS. 'com_joomgallery' .DS. 'mod_joomadminmodule';
    //$dest = 'modules' .DS. 'mod_joomadminmodule';

    /*if(!JFolder::move($src, $dest, JPATH_ADMINISTRATOR)){
      $mainframe->enqueueMessage( JText::_('Unable to install JoomAdminModule!') );
    }*/

    $row = & JTable::getInstance('module');
    $row->title = 'JoomGallery News';
    $row->ordering = 1;
    $row->position = 'joom_cpanel';
    $row->published = 1;
    $row->showtitle = 1;
    $row->iscore = 0;
    $row->access = 0;
    $row->client_id = 1;
    $row->module = 'mod_feed';
    $row->params = 'cache=1
    cache_time=15
    moduleclass_sfx=
    rssurl=http://en.joomgallery.net/?format=feed&type=rss
    rssrtl=0
    rsstitle=1
    rssdesc=0
    rssimage=1
    rssitems=3
    rssitemdesc=1
    word_count=30';
    if (!$row->store()) {
      $mainframe->enqueueMessage( JText::_('Unable to insert feed Module data!') );
    }

    echo '<p><b>JoomGallery was installed successfully.</b></p>';
  }
?>