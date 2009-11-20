<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.favourites.php $
// $Id: joom.favourites.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined ('_JEXEC') or die('Direct Access to this location is not allowed.');

class Joom_Favourites
{

  var $piclist;
  var $using_database;
  var $user_exists;
  var $layout;
  var $output;

  var $showfavourites_url;
  var $viewcategory_url;
  var $details_url;

  function Joom_Favourites($func, $id, $catid)
  {
    $mainframe = & JFactory::getApplication('site');
    $database  = JFactory::getDBO();
    $user      = & JFactory::getUser();
    $config    = Joom_getConfig();

    $this->showfavourites_url = 'index.php?option=com_joomgallery&func=showfavourites';
    $this->viewcategory_url   = 'index.php?option=com_joomgallery&func=viewcategory&catid=';
    $this->details_url        = 'index.php?option=com_joomgallery&func=detail&id=';


    $access = true;
    if($func == 'addpicture')
    {
      $database->setQuery(" SELECT 
                              id
                            FROM 
                              #__joomgallery AS a
                            LEFT JOIN 
                              #__joomgallery_catg AS c ON a.catid = c.cid
                            WHERE 
                                  a.id= '".$id."' 
                              AND a.approved  = '1' 
                              AND a.published = '1'
                              AND c.access   <= '".$user->get('aid')."' 
                              AND c.published = '1'
                          ");
      if(!$database->loadResult())
      {
        $access = false;
      }
    }

    // Berechtigung ueberpruefen
    if(  (  (   ($config->jg_showdetailfavourite == 0 && $user->get('aid') < 1)
             || ($config->jg_showdetailfavourite == 1 && $user->get('aid') < 2)
            )
          || ($config->jg_usefavouritesforpubliczip == 1 && $user->get('id') < 1)
         )
       || $config->jg_favourites == 0 || $access == false
      )
    {
      $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID,false), 
                                     JText::_('JGS_ALERT_NOT_ALLOWED_VIEW_PICTURE'));
    }

    // Ueberpruefung, ob mit der Datenbank oder mit der Session gearbeitet wird
    if($user->get('id') && $config->jg_usefavouritesforzip != 1)
    {
      $this->using_database = true;
      $this->output         = 'JGS_FAV_';

      $database->setQuery(" SELECT 
                              uuserid 
                            FROM 
                              #__joomgallery_users 
                            WHERE 
                              uuserid = '".$user->get('id')."'
                          ");
      if($database->loadResult())
      {
        $this->user_exists = true;
        $database->setQuery(" SELECT 
                                piclist,
                                layout 
                              FROM 
                                #__joomgallery_users 
                              WHERE 
                                uuserid = '".$user->get('id')."'
                            ");
        $row = $database->loadObject();
        $this->piclist = $row->piclist;
        $this->layout  = $row->layout;
      }
      else
      {
        $this->user_exists = false;
        $this->piclist = NULL;
        $this->layout  = 0;
      }
    }
    else
    {
      $this->using_database = false;
      $this->output         = 'JGS_ZIP_';

      $this->piclist = $mainframe->getUserState('joom.favourites.pictures');
      $this->layout  = $mainframe->getUserState('joom.favourites.layout');
    }

    switch($func)
    {
      case 'addpicture':
        $this->Joom_Favourites_AddPicture($id, $catid);
        break;
      case 'removepicture':
        $this->Joom_Favourites_RemovePicture($id);
        break;
      case 'removeall':
        $this->Joom_Favourites_RemoveAll();
        break;
      case 'switchlayout':
        $this->Joom_Favourites_SwitchLayout();
        break;
      case 'createzip':
        Joom_GalleryHeader();
        $this->Joom_Favourites_CreateZip();
        break;
      case 'showfavourites':
        Joom_GalleryHeader();
        $this->Joom_ShowFavourites();
        break;
    }
  }//End function Joom_Favourites


  function Joom_Favourites_AddPicture($id, $catid)
  {
    $mainframe = & JFactory::getApplication('site');
    $database  = JFactory::getDBO();
    $user      = JFactory::getUser();
    $config    = Joom_getConfig();

    if(is_null($this->piclist))
    {
      if($this->using_database)
      {
        if($this->user_exists)
        {
          $database->setQuery(" UPDATE 
                                  #__joomgallery_users
                                SET 
                                  piclist = '".$id."'
                                WHERE
                                  uuserid = '".$user->get('id')."'
                              ");
        }
        else
        {
          $database->setQuery(" INSERT INTO 
                                  #__joomgallery_users 
                                    (uuserid, piclist)
                                VALUES
                                  ('".$user->get('id')."', '".$id."')
                              ");
        }
        $database->query();
      }
      else
      {
        $mainframe->setUserState('joom.favourites.pictures', $id);
      }
    }
    else
    {
      $piclist_array = explode(',', $this->piclist);

      if(in_array($id,$piclist_array))
      {  // Bild bereits vorhanden
        if($catid != 0)
        {
          $mainframe->redirect(JRoute::_($this->viewcategory_url.$catid._JOOM_ITEMID,false),
                               $this->Output('ALREADY_IN'));
        }
        else
        {
          $mainframe->redirect(JRoute::_($this->details_url.$id._JOOM_ITEMID,false),
                                         $this->Output('ALREADY_IN'));
        }
      }
      if(count($piclist_array) == $config->jg_maxfavourites)// bereits maximala Anzahl an Bildern erreicht
      {
        if($catid != 0)
        {
          $mainframe->redirect(JRoute::_($this->viewcategory_url.$catid._JOOM_ITEMID,false),
                                         $this->Output('ALREADY_MAX'));
        }
        else
        {
          $mainframe->redirect(JRoute::_($this->details_url.$id._JOOM_ITEMID,false),
                                         $this->Output('ALREADY_MAX'));
        }
      }

      if($this->using_database)
      {
        $database->setQuery(" UPDATE 
                                #__joomgallery_users
                              SET 
                                piclist = '".$this->piclist.', '.intval($id)."'
                              WHERE 
                                uuserid = '".$user->get('id')."'
                            ");
        $database->query();
      }
      else
      {
        $mainframe->setUserState('joom.favourites.pictures',$this->piclist.','.intval($id));
      }
    }
    if($catid != 0)
    {
      $mainframe->redirect(JRoute::_($this->viewcategory_url.$catid._JOOM_ITEMID,false),
                                     $this->Output('SUCCESSFULLY_ADDED'));
    }
    else
    {
      $mainframe->redirect(JRoute::_($this->details_url.$id._JOOM_ITEMID,false),
                                     $this->Output('SUCCESSFULLY_ADDED'));
    }
  }//End function Joom_Favourites_AddPicture


  function Joom_Favourites_RemovePicture($id)
  {
    $mainframe = & JFactory::getApplication('site');
    $user      = JFactory::getUser();
    $config    = Joom_getConfig();

    $piclist = explode(',', $this->piclist);
    if(!in_array($id, $piclist))
    {
      $mainframe->redirect(JRoute::_($this->showfavourites_url._JOOM_ITEMID,false),
                                     $this->Output('NOT_IN'));
    }
    $new_piclist = array();
    foreach($piclist as $picid)
    {
      if($picid != $id)
      {
        array_push($new_piclist, $picid);
      }
    }
    if(count($new_piclist) == 0)
    {
      $new_piclist = NULL;
      $set_piclist = " SET piclist = NULL ";
    }
    else
    {
      $new_piclist = implode(',', $new_piclist);
      $set_piclist = " SET piclist = '".$new_piclist."' ";
    }
    if($this->using_database){
      $database = JFactory::getDBO();
      $database->setQuery(" UPDATE 
                              #__joomgallery_users 
                            $set_piclist
                            WHERE 
                              uuserid = '".$user->get('id')."'
                          ");
      $database->query();
    }
    else
    {
      $mainframe->setUserState('joom.favourites.pictures',$new_piclist);
    }
    $mainframe->redirect(JRoute::_($this->showfavourites_url._JOOM_ITEMID,false),
                                   $this->Output('SUCCESSFULLY_REMOVED'));
  }//End function Joom_Favourites_RemovePicture


  function Joom_Favourites_RemoveAll()
  {
    $mainframe = & JFactory::getApplication('site');

    if($this->using_database)
    {
      $database = & JFactory::getDBO();
      $user     = & JFactory::getUser();
      $database->setQuery(" UPDATE 
                              #__joomgallery_users
                            SET 
                              piclist = NULL
                            WHERE 
                              uuserid = '".$user->get('id')."'
                          ");
      $database->query();
    }
    else
    {
      $mainframe->setUserState('joom.favourites.pictures',NULL);
    }

    $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID,false),
                                   $this->Output('ALL_REMOVED'));
  }//End function Joom_Favourites_RemoveAll


  function Joom_Favourites_SwitchLayout()
  {
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    if($this->layout)
    {
      if($this->using_database)
      {
        $database->setQuery(" UPDATE 
                                #__joomgallery_users
                              SET 
                                layout = '0'
                              WHERE 
                                uuserid = '".$user->get('id')."'
                            ");
        $database->query();
      }
      else
      {
        $mainframe->setUserState('joom.favourites.layout',0);
      }
    }
    else
    {
      if($this->using_database)
      {
        $database->setQuery(" UPDATE 
                                #__joomgallery_users
                              SET 
                                layout = '1'
                              WHERE 
                                uuserid = '".$user->get('id')."'
                            ");
        $database->query();
      }
      else
      {
        $mainframe->setUserState('joom.favourites.layout',1);
      }
    }
    $mainframe->redirect(JRoute::_($this->showfavourites_url._JOOM_ITEMID,false));
  }//End function Joom_Favourites_SwitchLayout


  function Joom_Favourites_CreateZip()
  {
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();
    $config    = Joom_getConfig();

    // Kontrollabfrage, ob der Zip-Download erlaubt ist
    if($config->jg_zipdownload != 1 && ($user->get('id') 
        || $config->jg_usefavouritesforpubliczip != 1)
      )
    {
      $mainframe->redirect(JRoute::_($this->showfavourites_url._JOOM_ITEMID,false),
                           JText::_('JGS_FAV_NOT_ALLOWED'));
    }

    // Einbinden der PclZip-Library
    if(file_exists(JPATH_ADMINISTRATOR.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php'))
    {
      require_once(JPATH_ADMINISTRATOR.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php');
    }
    else
    {
      $mainframe->redirect(JRoute::_($this->showfavourites_url._JOOM_ITEMID,false),
                           JText::_('JGS_FAV_ZIPLIBRARY_NOT_FOUND'));
    }

    // Name des Zip-Archivs
    $zipname = 'components/com_joomgallery/joomgallery_'.date('d_m_Y').'__';
    if($user->get('id'))
    {
      $zipname .= $user->get('id').'_';
    }
    $zipname .= mt_rand(10000,99999).'.zip';

    // Erstellen des Zip-Archivs
    $zipfile = new PclZip($zipname);
    if(!is_null($this->piclist))
    {
      $picids = explode(',', $this->piclist);
      $files  = array();
      foreach( $picids as $picid)
      {
        $database->setQuery(" SELECT 
                                catid,imgfilename 
                              FROM 
                                #__joomgallery
                              WHERE 
                                id = '".$picid."'
                            ");
        $row = $database->loadObject();
        $catpath = Joom_getCatPath($row->catid);
        if(file_exists(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$row->imgfilename)))
        {
          array_push($files,$config->jg_pathoriginalimages.$catpath.$row->imgfilename);
        } 
        elseif(file_exists(JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$row->imgfilename)))
        {
          array_push($files,$config->jg_pathimages.$catpath.$row->imgfilename);
        }
      }
      $createzip = $zipfile->create($files, PCLZIP_OPT_REMOVE_ALL_PATH);
      if($createzip == 0)
      {
        // workaround for servers with wwwwrun problem
        Joom_Chmod(JPATH_ROOT.DS.'components'.DS.'com_joomgallery', 0777);
        $createzip = $zipfile->create($files,PCLZIP_OPT_REMOVE_ALL_PATH);
        Joom_Chmod(JPATH_ROOT.DS.'components'.DS.'com_joomgallery', 0755);
      }
      if($user->get('id'))
      {
        if($this->user_exists)
        {
          $database->setQuery(" SELECT 
                                  zipname 
                                FROM 
                                  #__joomgallery_users
                                WHERE 
                                  uuserid = '".$user->get('id')."'
                              ");
          if($old_zip = $database->loadResult())
          {
            if(file_exists($old_zip))
            {
              jimport('joomla.filesystem.file');
              JFile::delete($old_zip);
            }
          }
          $database->setQuery(" UPDATE 
                                  #__joomgallery_users
                                SET 
                                  time = NOW(),zipname = '".$zipname."'
                                WHERE 
                                  uuserid = '".$user->get('id')."'
                              ");
        }
        else
        {
          $database->setQuery(" INSERT INTO 
                                  #__joomgallery_users 
                                    (uuserid,time,zipname)
                                VALUES
                                  ('".$user->get('id')."', NOW(), '".$zipname."')
                              ");
        }
      }
      else
      {
        $database->setQuery(" INSERT INTO 
                                #__joomgallery_users
                                  (time,zipname)
                              VALUES
                                (NOW(),'".$zipname."')
                            ");
      }
      $database->query();

      include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'joom.favourites.html.php');
      if($createzip != 0 )
      {
        $zipsize = filesize($zipname);
        if($zipsize < 1000000)
        {
          $zipsize        = round($zipsize,-3)/1000;
          $zipsize_string = $zipsize.' KB';
        }
        else
        {
          $zipsize        = round($zipsize,-6)/1000000;
          $zipsize_string = $zipsize.' MB';
        }
        HTML_Joom_Favourites::Joom_Favourites_CreateZip_HTML($zipname, $zipsize_string);
      }
      else
      {
        HTML_Joom_Favourites::Joom_Favourites_CreateZip_Error_HTML($zipfile);
      }
    }
    else
    {
      $mainframe->redirect(JRoute::_($this->showfavourites_url._JOOM_ITEMID,false),
                                     $this->Output('NO_PICTURES'));
    }
  }//End function Joom_Favourites_CreateZip


  function Joom_ShowFavourites()
  {
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();
    $config    = Joom_getConfig();

    include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'joom.favourites.html.php');

    $query = "SELECT 
                *,
                a.owner AS imgowner
              FROM 
                #__joomgallery AS a, 
                #__joomgallery_catg AS ca
              WHERE 
                a.catid=ca.cid";
    if(is_null($this->piclist))
    {
      $query .= " LIMIT 0";
    }
    else
    {
      $query .= " AND a.id IN (".$database->getEscaped($this->piclist).")";
    }
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    // Download Icon # hier wird im Moment noch die Einstellung fuer die Detail-Ansicht uebernommen
    $showDownloadIcon = 0;
    /*if((is_file(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$imgfilename)) 
         || $config->jg_downloadfile!=1)) {*/
      if(   (($config->jg_showdetaildownload == 1) && ($user->get('aid') >= 1)) 
         || (($config->jg_showdetaildownload == 2) && ($user->get('aid') == 2)) 
         ||  ($config->jg_showdetaildownload == 3)
        )
      {
        $showDownloadIcon = 1;
      }
      elseif(($config->jg_showdetaildownload == 1) && ($user->get('aid') < 1))
      {
        $showDownloadIcon = -1;
      }
    #}

    if($this->layout)
    {
      HTML_Joom_Favourites::Joom_ShowFavourites_HTML1($rows, $showDownloadIcon);
    }
    else
    {
      HTML_Joom_Favourites::Joom_ShowFavourites_HTML2($rows, $showDownloadIcon);
    }
  }//End function Joom_ShowFavourites


  function Output($msg)
  {
    return JText::_($this->output.$msg);
  }//End function Output

}//End class Joom_Favourites
?>
