<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.userpanel.php $
// $Id: joom.userpanel.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined ('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

class Joom_UserPanel
{

  var $userid;
  var $picid;
  var $adminlogged;

  var $showusercats_url;
  var $userpanel_url;

  function Joom_UserPanel(&$func,&$cid)
  {
    $user = & JFactory::getUser();

    include_once JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'joom.userpanel.html.php';
    $this->userid = JRequest::getInt('uid', 0);
    $this->picid  = JRequest::getInt('id', 0);

    $this->showusercats_url = 'index.php?option=com_joomgallery&func=showusercats&uid=';
    $this->userpanel_url    = 'index.php?option=com_joomgallery&func=userpanel';

    if($user->get('usertype') == 'Administrator' || $user->get('usertype') == 'Super Administrator')
    {
      $this->adminlogged = true;
    }
    else
    {
      $this->adminlogged = false;
    }

    switch($func)
    {
      //Overview Pictures and Buttons New Picture and categories
      case 'userpanel':
        $this->Joom_User_PanelShow();
        break;
      case 'showusercats':
        $this->Joom_User_CatsShow();
        break;
      case 'newusercat':
      case 'editusercat':
        $this->Joom_User_EditUserCat($cid);
        break;
      case 'saveusercat':
        $this->Joom_User_SaveUserCat($cid);
        break;
      case 'deleteusercat':
        $this->Joom_User_DeleteUserCat($cid);
        break;  
      case 'editpic':
        $this->Joom_User_EditPic();
        break;
      case 'savepic':
        $this->Joom_User_SavePic();
        break;
      case 'deletepic':
        $this->Joom_User_DeletePic();
        break;
      case 'showupload':
        $this->Joom_User_ShowUpload();
        break;
      default:
        die();
        break;
    }
  }//End function Joom_UserPanel


  /**
   * Uebersicht aller Bilder
   */
  function Joom_User_PanelShow()
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();


    //Pagination part
    $jconfig = JFactory::getConfig();
    // get the standard limit
    $list_limit = $jconfig->getValue('list_limit');
    // read the limit from user session, default = config limit, if 0 = all
    $list_limit = $mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $list_limit);
    // read the limitstart from $_REQUEST, default = 1
    $limitstart = JRequest::getInt('limitstart', 1);
    if ($limitstart == 0)
    {
      $limitstart = 1;
    }

    //Button 'Kategorien' wird nur angezeigt, wenn vom User angelegte Kategorien
    //vorhanden sind, die auch im Acces Level gueltig sind oder Backendkategorie
    //mit zutreffendem Acces Level zur Verfuegung steht
    //fuer Admin/SuperAdmin werden alle Backendkategorien angezeigt, Button
    //wird dort immer angezeigt
    if($this->adminlogged)
    {
      $showcats      = true;
      $showpicupload = true;
    }
    else
    {
      //Userkategorien lesen, die bereits angelegt sind und dem Acces Level 
      //des User entsprechen und zusaetzlich dem Acces Level entsprechende
      //Backendkategorien, wenn globale Freigabe im Backend eingestellt

      $query = "  SELECT 
                    cid 
                  FROM 
                    #__joomgallery_catg";
      if(!$config->jg_userowncatsupload)
      {
        $query .= " WHERE owner IS NOT NULL";
      }
      else
      {
        $query .= " WHERE owner=".$user->get('id');
      }

      if(!empty($config->jg_category))
      {
        $query .= " OR cid IN ($config->jg_category)";
      }

      if($config->jg_usercat && !empty($config->jg_usercategory))
      {
        $query .= " OR (cid IN ($config->jg_usercategory) AND access <= ".$user->get('aid').")"; 
      }
      $database->setQuery($query);
      $result = $database->loadResultArray();

      if (!empty($config->jg_category))
      {
        $jgcats = explode(',', $config->jg_category);
      }
      else
      {
        $jgcats = array();
      }
      if($config->jg_usercat && !empty($config->jg_usercategory))
      {
        $jgusercats = explode(',', $config->jg_usercategory);
      }
      else
      {
        $jgusercats = array();
      }

      //soll Button fuer Upload angezeigt werden
      //Pruefung, ob im Backend Kategorien fuer den Upload freigegeben sind
      //oder vom User angelegte Kategorien
      //Catids von jg_usercat aus $result entfernen, wenn vorhanden
      //nicht, wenn die Kategorie gleichzeitig fuer den Upload freigegeben ist
      $resultarr = $result;
      if($config->jg_usercat && !empty($config->jg_usercategory))
      {
        $resultarr = array_diff($resultarr, array_diff($jgusercats, $jgcats));
      }
      if(count($resultarr) == 0)
      {
        $showpicupload = false;
      }
      else
      {
        $showpicupload = true;
      }

      //soll Button Kategorien angezeigt werden
      //Catid von jg_category aus $result entfernen, wenn vorhanden
      //nicht, wenn die Kategorie gleichzeitig fuer Usercats freigegeben ist
      if($config->jg_usercat && count($jgusercats))
      {
        $resultarr = $result;
        if(!empty($config->jg_category))
        {
          $resultarr = array_diff($resultarr, array_diff($jgusercats, $jgcats));
        }
        if(count($resultarr) == 0)
        {
          $showcats = false;
        }
        else
        {
          $showcats = true;
        }
      }
      else
      {
        $showcats = false;
      }
    }

    //Sortierung
    $sordercat = JRequest::getInt('sordercat', null);
    if(is_null($sordercat))
    {
      $sordercat = $mainframe->getUserState('joom.userpanel.sordercat');
      if(is_null($sordercat))
      {
        $sordercat = 0;
      }
    }
    else
    {
      $mainframe->setUserState('joom.userpanel.sordercat', $sordercat);
    }
    $sortorder  = '';
    switch($sordercat)
    {
      case 0:
        $sortorder = 'imgdate ASC';
        break;
      case 1:
        $sortorder = 'imgdate DESC';
        break;
      case 2:
        $sortorder = 'imgtext ASC';
        break;
      case 3:
        $sortorder = 'imgtext DESC';
        break;
      case 4:
        $sortorder = 'imgcounter ASC';
        break;
      case 5:
        $sortorder = 'imgcounter DESC';
        break;      
      case 6:
        $sortorder = 'catid ASC,imgtext ASC';
        break;
      case 7:
        $sortorder = 'catid ASC,imgtext DESC';
        break;
    }

    //Filter by Type
    $sortcat = JRequest::getInt('sortcat', null);
    $sortcat_state = $mainframe->getUserState('joom.userpanel.sortcat');
    if(is_null($sortcat))
    {
      $sortcat = $sortcat_state;
      if(is_null($sortcat))
      {
        $sortcat = 0;
      }
    }
    else
    {
      $mainframe->setUserState('joom.userpanel.sortcat', $sortcat);
      if($sortcat != $sortcat_state)
      {
        $limitstart = 1;  //number of images changes now, so go to first page
      }
    }
    $where = '';
    switch($sortcat)
    {
      case 1: //approved
        $where = 'approved = 1';
        break;
      case 2: //not approved
        $where = 'approved = 0';
        break;
    }

    $query = '';
    //Dem Admin/SuperAdmin werden alle veroeffentlichten Bilder abgezeigt, 
    //wenn die Option im Backend aktiviert ist
    if(($this->adminlogged) && $config->jg_showallpicstoadmin == 1)
    {
      $query = "  SELECT 
                    *
                  FROM 
                    #__joomgallery
                  WHERE 
                    published = '1'
                ";
    }
    else
    {
      $query = "  SELECT 
                    *
                  FROM 
                    #__joomgallery
                  WHERE 
                            owner = ".$user->get('id')."
                    AND published = '1'
                ";
    }

    if(!empty($where))
    {
      $query .= ' AND '.$where;
    } 
    
    if(!empty($sortorder))
    {
      $query .= ' ORDER BY '.$sortorder;
    } 

    //execute the query with the $limits
    //if list_limit = 0, then all pictures
    if ($list_limit == 0)
    {
      $database->setQuery($query);
      $rows = $database->loadObjectList();
      $totalcount=count($rows);
    }
    else
    {
      //take the query and replace the 'select *' with 'select count(id)' -> $querycount
      //to count total rows for navigation
      $querycount = str_replace('SELECT *', 'SELECT COUNT(id)', $query);
      $database->setQuery($querycount);
      $totalcount=$database->loadResult();

      if($totalcount <= $list_limit)
      {
        $limitstart = 1;
      }
      if($limitstart ==1 )
      {
        $limitstart--;
      }

      $database->setQuery($query, $limitstart, $list_limit);
      $rows = $database->loadObjectList();
    }

    //create the navigation, only if pictures exist
    if($totalcount)
    {
      jimport('joomla.html.pagination'); 
      $pageNav = new JPagination($totalcount, $limitstart, $list_limit);
    }
    else
    {
      $pageNav = null;
    }

    //Sortierung der Bilder
    $o_options[] = JHTML::_('select.option', 0, JText::_('JGS_USER_ORDERBY_DATE_ASC'));
    $o_options[] = JHTML::_('select.option', 1, JText::_('JGS_USER_ORDERBY_DATE_DESC'));
    $o_options[] = JHTML::_('select.option', 2, JText::_('JGS_USER_ORDERBY_TITLE_ASC'));
    $o_options[] = JHTML::_('select.option', 3, JText::_('JGS_USER_ORDERBY_TITLE_DESC'));
    $o_options[] = JHTML::_('select.option', 4, JText::_('JGS_USER_ORDERBY_HITS_ASC'));
    $o_options[] = JHTML::_('select.option', 5, JText::_('JGS_USER_ORDERBY_HITS_DESC'));
    $o_options[] = JHTML::_('select.option', 6, JText::_('JGS_USER_ORDERBY_CATNAME_ASC') .' - '. JText::_('JGS_USER_ORDERBY_TITLE_ASC'));
    $o_options[] = JHTML::_('select.option', 7, JText::_('JGS_USER_ORDERBY_CATNAME_DESC') .' - '. JText::_('JGS_USER_ORDERBY_TITLE_DESC'));

    $olist = JHTML::_('select.genericlist',$o_options, 'sordercat',
            'class="inputbox" size="1" onchange="form.submit();"',
            'value', 'text', $sordercat);

    //Filter
    $s_options[] = JHTML::_('select.option', 0, JText::_('JGS_ALL')); 
    $s_options[] = JHTML::_('select.option', 1, JText::_('JGS_APPROVED_ONLY'));
    $s_options[] = JHTML::_('select.option', 2, JText::_('JGS_NOT_APPROVED_ONLY'));

    $slist = JHTML::_('select.genericlist',$s_options, 'sortcat',
            'class="inputbox" size="1" onchange="form.submit();"',
            'value', 'text', $sortcat);

    HTML_Joom_UserPanel::Joom_User_PanelShow_HTML($showcats, $showpicupload, $olist, 
                                                  $slist, $rows, $pageNav);
  }//End function Joom_User_PanelShow


  /**
   * Uebersicht aller Userkategorien
   */
  function Joom_User_CatsShow()
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();


    //wenn user = admin* und alle cat angezeigt werden sollen
    if($this->adminlogged && $config->jg_showallpicstoadmin == 1)
    {
      $database->setQuery(" SELECT 
                              cid,
                              name,
                              catpath,
                              catimage,
                              parent,
                              published
                            FROM 
                              #__joomgallery_catg
                          ");
    }
    else
    {
      $database->setQuery(" SELECT 
                              cid,
                              name,
                              catpath,
                              catimage,
                              parent,
                              published
                            FROM 
                              #__joomgallery_catg
                            WHERE 
                              owner=".$user->get('id')."
                          ");
    }
    $rows = $database->loadObjectList();

    foreach($rows as $key => $catobj)
    {
      //zusaetzlich pruefen, ob die Kategorie Parent ist
      $database->setQuery(" SELECT 
                              COUNT(cid)
                            FROM 
                              #__joomgallery_catg
                            WHERE 
                              parent = $catobj->cid
                          ");
      $resultparent = $database->loadResult();

      //Bilder in Kategorie
      $database->setQuery(" SELECT 
                              COUNT(id)
                            FROM 
                              #__joomgallery
                            WHERE 
                              catid = $catobj->cid
                          ");
      $resultpics = $database->loadResult();
      $rows[$key]->piccount = $resultpics;

      if($resultparent > 0 || $resultpics > 0)
      {
        $rows[$key]->allowdel = false;
      }
      else
      {
        $rows[$key]->allowdel = true;
      }
    }

    HTML_Joom_UserPanel::Joom_User_CatsShow_HTML($rows);
  }//End function Joom_User_CatsShow


  /**
   * Aendern einer bestehenden Kategorie oder Anlegen einer neuen Kategorie
   *
   * @param int $cid category, if null -> new category
   */
  function Joom_User_EditUserCat($cid)
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    // Erstellung der Auswahlliste fuer die Veroeffentlichung
    $yesno[] = JHTML::_('select.option', '1', JText::_('JGS_YES'));
    $yesno[] = JHTML::_('select.option', '0', JText::_('JGS_NO'));

    // Erstellung der Liste fuer die Anordnung der Kategorie
    $orders = JHTML::_('list.genericordering', "  SELECT 
                                                    ordering AS value, 
                                                    name AS text
                                                  FROM 
                                                    #__joomgallery_catg
                                                  WHERE 
                                                    owner = ".$user->get('id')."
                                                  ORDER BY 
                                                    ordering"
                      );

    if($cid != 0)
    {
      // existierende Kategorie
      $row = new mosCatgs($database);
      $row->load( $cid );

      //dem Admin/SuperAdmin werden alle Kategorien angezeigt
      if($this->adminlogged)
      {
        $Lists['catgs'] = Joom_ShowDropDownCategoryList($row->parent, 'parent', '', $cid);
      }
      else
      {
        $Lists['catgs'] = $this->Joom_User_ShowDropDownCategoryList($row->parent, $cid, 'parent');
      }

      // Auswahlliste fuer die Veroeffentlichung
      $publist = JHTML::_('select.genericlist', $yesno, 'published', 
                          'class="inputbox" size="1"', 'value', 'text', $row->published);

      //Anordnung der Kategorie
      $orderlist = JHTML::_('select.genericlist',$orders, 'ordering', 'class="inputbox" size="1"',
                            'value', 'text', intval($row->ordering));
      $description = $row->description;
      $name = $row->name;

      //wenn Acces Level im Frontend geaendert werden duerfen ,die Access Level
      //suchen,die zwischen der aid des user und dem im Backend eingestellten
      //Level der parent Kategorie liegen
      $glist = null;
      if($config->jg_usercatacc || $this->adminlogged)
      {
        $query = "  SELECT 
                      id AS value, 
                      name AS text 
                    FROM #__groups
                  ";

        //wenn Admin oder SuperAdmin werden alle Level angezeigt
        if(!$this->adminlogged)
        {
          //parent Kategorie lesen
          $row2 = new mosCatgs($database);
          $row2->load($row->parent);
          $query .= " WHERE id >= ".$row2->access." AND id <= ".$user->get('aid');
        }
        $query .= " ORDER BY id";
        $database->setQuery($query);
        $groups = $database->loadObjectList();

        //wenn nur ein Eintrag gefunden wurde, wird die Auswahl nicht angezeigt
        if (count($groups) > 1)
        {
          $glist = JHTML::_('select.genericlist', $groups, 'access', 
                            'class="inputbox" size="1"', 'value', 'text', intval($row->access));
        }
      }
    }
    else
    {
      //Neue Kategorie
      //dem Admin/SuperAdmin werden alle Kategorien angezeigt
      if($this->adminlogged)
      {
        $Lists['catgs'] = Joom_ShowDropDownCategoryList(0, 'parent', '');
      }
      else
      {
        $Lists['catgs'] = $this->Joom_User_ShowDropDownCategoryList(0, null, 'parent');
      }

      //Admin/SuperAdmin werden bei Neuanlage alle Access Level angezeigt
      //sonst keine -> dann wird der Level des Parent uebernommen
      $glist = null;
      if($this->adminlogged)
      {
        $query="  SELECT 
                    id AS value, 
                    name AS text 
                  FROM #__groups
                  ORDER BY 
                    id
                ";
        $database->setQuery($query);
        $groups = $database->loadObjectList();

        if (count($groups) > 1)
        {
          $glist = JHTML::_('select.genericlist', $groups, 'access', 
                            'class="inputbox" size="1"', 'value', 'text', 0);
        }
      } 

      // Auswahlliste fuer die Veroeffentlichung
      $publist = JHTML::_('select.genericlist',$yesno, 'published', 
                          'class="inputbox" size="1"', 'value', 'text',0);
      //Anordnung der Kategorie
      $orderlist = JHTML::_('select.genericlist',$orders, 'ordering', 
                            'class="inputbox" size="1"', 'value', 'text', 1);
      $description = '';
      $name = '';
    }

    // Erstellung der Liste der verfuegbaren und genehmigten
    // Kategorie-Thumbnails, nur bei existierenden Kategorien
    $thumblist = null;
    if($cid != 0)
    {
      $database->setQuery(" SELECT 
                              imgthumbname
                            FROM 
                              #__joomgallery
                            WHERE 
                                     catid = $cid
                              AND approved = '1'
                            ORDER BY 
                              imgthumbname
                          ");
      $thuFiles2 = $database->loadObjectList();
      $thumbs    = array(JHTML::_('select.option', '', JText::_('JGS_SELECT_THUMBNAIL')));
      foreach($thuFiles2 as $tfile2)
      {
        $thumbs[] = JHTML::_('select.option', $tfile2->imgthumbname);
      }
      $catpath = Joom_GetCatPath($cid);

      $thumblist = JHTML::_('select.genericlist',$thumbs, 'catimage', 'class=\"inputbox\" size=\"1\"'
      . " onchange=\"javascript:"
      . "if (document.usercatForm.catimage.options[selectedIndex].value!='') {"
      .   "document.imagelib.src='"._JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath."' "
      .   "+ document.usercatForm.catimage.options[selectedIndex].value"
      . "} else {"
      .   "document.imagelib.src='"._JOOM_LIVE_SITE."images/M_images/blank.png'}\"",
        'value', 'text', $row->catimage);
    }
    HTML_Joom_UserPanel::Joom_User_EditUserCat_HTML($cid, $publist, $glist,
                                                    $Lists, $orderlist, $thumblist,
                                                    $description, $name);
  }//End function Joom_User_EditUserCat


  /**
   * saves a changed or a new category
   *
   * @param integer $cid, if null -> new category
   */
  function Joom_User_SaveUserCat(&$cid)
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();
    jimport('joomla.filesystem.file');

    $row = new mosCatgs($database);

    if($cid == 0)
    {
      $database->setQuery(" SELECT 
                              COUNT(cid)
                            FROM 
                              #__joomgallery_catg
                            WHERE 
                              owner=".$user->get('id')." 
                          ");
      $countcat = $database->loadResult();
      if($countcat >= $config->jg_maxusercat)
      {
        $mainframe->redirect('index.php?option=com_joomgallery&func=showusercats'._JOOM_ITEMID, false);
      }
      $newcat = true;
    }
    else
    {
      $newcat = false;
      //load an existing category
      $row->load($cid);
      //get old parent category
      $parentold = $row->parent;
      //get old category name
      $catnameold = $row->name;
    }

    //get new values
    if(!$row->bind($_POST))
    {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }

    if(get_magic_quotes_gpc())
    {
      $row->name = stripslashes($row->name);
      $row->description = stripslashes($row->description);
    }

    if(!$newcat)
    {
      if($catnameold != $row->name)
      {
        // Macht den neuen Kategorienamen sicher, wenn geaendert
        JFilterOutput::objectHTMLSafe($row->name);
        // Joom_FixCatname; Umlaute werden umgewandelt und alle Sonderzeichen bis auf
        // den Unterstrich entfernt, gilt nur fuer den catpath
        $catname = Joom_FixCatname($row->name);
        $catnamemodif = true;
      }
      else
      {
        $catname = $catnameold;
        $catnamemodif = false;
      }

      //Kategorieordner verschieben, wenn die Parentzuordnung oder der Kategoriename
      //geaendert wurde
      if($parentold != $row->parent || $catnamemodif == true)
      {
        //alten Pfad sichern
        $catpathold = $row->catpath;

        //Kategoriepfad der Parent-Kategorie lesen, nur noetig wenn parent != 0
        if($row->parent != 0)
        {
          $row_parent = new mosCatgs($database);
          $row_parent->load($row->parent);
          $catpathnew = $row_parent->catpath . '/' .$catname . '_' . $row->cid;
        }
        else
        {
          $catpathnew = $catname . '_' . $row->cid;
        }

        //Kategoriepfad in DB aktualisieren
        $row->catpath = $catpathnew;

        $cat_originalpathold  = JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpathold);
        $cat_picturepathold   = JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpathold);
        $cat_thumbnailpathold = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpathold);

        $cat_originalpathnew  = JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpathnew);
        $cat_picturepathnew   = JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpathnew);
        $cat_thumbnailpathnew = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpathnew);

        //Ordner verschieben
        //TODO Fehlermeldungen
        JFolder::move($cat_originalpathold, $cat_originalpathnew);
        JFolder::move($cat_picturepathold, $cat_picturepathnew);
        JFolder::move($cat_thumbnailpathold, $cat_thumbnailpathnew);

        //wenn Parentkategorie geaendert wurde, den Catpath aller Unterkategorien
        //in DB anpassen
        $rowid = $row->cid;
        Joom_UpdateNewCatpath($rowid, $catpathold, $catpathnew);
      }
      if(!$row->store())
      {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
      }
    }
    else
    {
      //Neue Kategorie
      //Kategorienamen absichern
      JFilterOutput::objectHTMLSafe($row->name);
      // Joom_FixCatname; Umlaute werden umgewandelt und alle Sonderzeichen bis auf
      // den Unterstrich entfernt, gilt nur fuer den catpath
      $catname = Joom_FixCatname($row->name);
      $row->img_position = 0;
      $row->catimage = null;

      //access von der Elternkategorie erben, wenn nicht Admin/SuperAdmin
      if(!$this->adminlogged)
      {
        $row->owner = $user->get('id');
        $rowparent  = new mosCatgs($database);
        $rowparent->load( $row->parent );
        $row->access = $rowparent->access;
      }
      else
      {
        $row->owner = null;
      }

      //Anlegen des DB Eintrages
      if(!$row->store())
      {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
      }

      //catpath mit vergebener cid aufbauen
      $parentpathnew = Joom_GetCatPath($row->parent);
      $catpathnew = $parentpathnew . $catname . '_' . $row->cid;
      $row->catpath=$catpathnew;

      $row->store();

      //Kategorieverzeichnisse anlegen
      Joom_MakeDirectory(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpathnew);
      Joom_MakeDirectory(JPATH_ROOT.DS.$config->jg_pathimages.$catpathnew);
      Joom_MakeDirectory(JPATH_ROOT.DS.$config->jg_paththumbs.$catpathnew);
    }

    // Redirect zur Kategorieuebersicht
    $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&func=showusercats&uid='.$user->get('id')._JOOM_ITEMID,false));
  }//End function Joom_User_SaveUserCat



/**
* Loeschen einer Kategorie
*/
  function Joom_User_DeleteUserCat(&$cid)
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    //ueberpruefen, ob der User Owner der Kategorie ist
    $row = new moscatgs($database);
    $row->load($cid);

    if(!$this->adminlogged)
    {
      if($row->owner != $user->get('id'))
      {
        //sie sind nicht berechtigt...
        echo JText::_('ALERTNOTAUTH');
        if($user->get('id') < 1)
        {
          echo "<br />" . JText::_( 'You need to login.' );
        }
        #mosNotAuth();
        exit;
      }
    }

    //direkte URL-Aufrufe verhindern
    //zusaetzlich pruefen, ob die Kategorie Parent ist oder Bilder enthaelt
    $database->setQuery(" SELECT 
                            COUNT(cid)
                          FROM 
                            #__joomgallery_catg
                          WHERE 
                            parent = $cid
                        ");
    $resultparent=$database->loadResult();

     if($resultparent > 0)
     {
      //sie sind nicht berechtigt...
      echo JText::_('ALERTNOTAUTH');
      if($user->get('id') < 1)
      {
        echo "<br />" . JText::_('You need to login.');
      }
      #mosNotAuth();
      exit;
    }
    //Bilder in Kategorie
    $database->setQuery(" SELECT 
                            COUNT(id)
                          FROM 
                            #__joomgallery
                          WHERE 
                            catid = $cid
                        ");
    $resultpics=$database->loadResult();

    if($resultpics > 0)
    {
      //sie sind nicht berechtigt...
      echo JText::_('ALERTNOTAUTH');
      if($user->get('id') < 1)
      {
        echo "<br />" . JText::_( 'You need to login.' );
      }
      #mosNotAuth();
      exit;
    }

    $returnval   = array();
    $returnval[] = JFolder::delete(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$row->catpath);
    $returnval[] = JFolder::delete(JPATH_ROOT.DS.$config->jg_pathimages.$row->catpath);
    $returnval[] = JFolder::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$row->catpath);

    if(in_array(false, $returnval))
    {
      $mainframe->redirect(JRoute::_($this->showusercats_url.$user->get('id')._JOOM_ITEMID,false), 
                                     JText::_('JGS_ERROR_DELETING_CATEGORY_DIRECTORY'));
      return;
    }

    //DB-Eintrag loeschen
    if($row->delete() != true)
    {
      $mainframe->redirect(JRoute::_($this->showusercats_url.$user->get('id')._JOOM_ITEMID,false), 
                                     JText::_('JGS_ERROR_DELETING_CATEGORY_DATABASE_ENTRY'));
      return;
    }

    $mainframe->redirect(JRoute::_($this->showusercats_url.$user->get('id')._JOOM_ITEMID,false),
                                   JText::_('JGS_SUCCESS_DELETING_CATEGORY'));
  }//End function Joom_User_DeleteUserCat


/**
* Aendern einer bestehenden Kategorie
*/
  function Joom_User_EditPic()
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    $row = new mosjoomgallery($database);
    $row->load($this->picid);

    if($row->owner != $user->get('id') && !$this->adminlogged)
    {
      $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID,false),JText::_('JGS_ALERT_NOT_ALLOWED_TO_EDIT_PICTURE'));
    }
    if($this->adminlogged)
    {
      $clist = Joom_ShowDropDownCategoryList($row->catid, 'catid');
    }
    else
    {
      $clist = $this->Joom_User_ShowDropDownCategoryList($row->catid);
    }

    //wenn $clist = null, wurde das Bild in eine Backend-Kategorie geladen, 
    //die nicht mehr freigeschaltet ist. Oder es handelt sich um die einzige 
    //Kategorie. In diesem Fall nur den Text der Kategorie ausgeben
    if($clist == null)
    {
      $rowcat = new mosCatgs($database);
      $rowcat->load($row->catid);
      $clist = $rowcat->name;
    }
    HTML_Joom_UserPanel::Joom_User_EditPic_HTML($row, $clist);
  }//End function Joom_User_EditPic


/**
* Sicherung der Aenderungen an dem Bild
*/
  function Joom_User_SavePic()
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();
    jimport('joomla.filesystem.file');

    $row = new mosjoomgallery($database);

    //bestehenden DB Eintrag einlesen
    $row->load($this->picid);

    //alte Angaben sichern
    $catid_old   = $row->catid;
    $catpath_old = Joom_GetCatPath($row->catid);

    if(!$row->bind($_POST))
    {
      echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
      exit();
    }

    if(get_magic_quotes_gpc())
    {
      $row->imgtitle = stripslashes($row->imgtitle);
      $row->imgtext  = stripslashes($row->imgtext);
    }

    //wenn sich die Kategorie geaendert hat, die Bilddateien verschieben
    if($catid_old != $row->catid)
    {
      $catpathold = $catpath_old;
      $catpathnew = Joom_GetCatPath($row->catid);

      $cat_originalpathold  = JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath_old);
      $cat_picturepathold   = JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath_old);
      $cat_thumbnailpathold = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath_old);

      $cat_originalpathnew  = JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpathnew);
      $cat_picturepathnew   = JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpathnew);
      $cat_thumbnailpathnew = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpathnew);

      JFile::move($cat_originalpathold.$row->imgfilename, $cat_originalpathnew.$row->imgfilename);
      JFile::move($cat_picturepathold.$row->imgfilename, $cat_picturepathnew.$row->imgfilename);
      JFile::move($cat_thumbnailpathold.$row->imgfilename, $cat_thumbnailpathnew.$row->imgfilename);
    }

    if(!$row->store())
    {
      echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
      exit();
    }
    $mainframe->redirect(JRoute::_($this->userpanel_url._JOOM_ITEMID,false),
                                   JText::_('JGS_ALERT_PICTURE_SUCCESSFULLY_UPDATED'));
  }//End function Joom_User_SavePic


/**
* Loeschen eines Bildes
*/
  function Joom_User_DeletePic()
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();
    jimport('joomla.filesystem.file');


    if($this->userid != $user->get('id') && !$this->adminlogged)
    {
      $mainframe->redirect(JRoute::_($this->userpanel_url._JOOM_ITEMID,false), 
                                     JText::_('JGS_ALERT_NOT_ALLOWED_DELETE_PICTURE'));
    }
    if($this->picid)
    {
      $row = new mosjoomgallery($database);
      $row->load($this->picid);
      $catpath = Joom_GetCatPath($row->catid);
      //Detailbild loeschen
      if(JFile::delete(JPATH_ROOT.DS.$config->jg_pathimages.DS.$catpath.$row->imgfilename))
      {
        //Thumb loeschen
        if(JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.DS.$catpath.$row->imgthumbname))
        {
          // ggf. Originalbild loeschen
          if(file_exists(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$row->imgfilename))
          {
            JFile::delete(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$row->imgfilename);
          }
          //Kommentare loeschen
          $database->setQuery(" DELETE 
                                FROM 
                                  #__joomgallery_comments
                                WHERE 
                                  cmtpic=$this->picid
                              ");
          if(!$database->query())
          {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
          }

          //Namensschilder loeschen
          $database->setQuery(" DELETE 
                                FROM 
                                  #__joomgallery_nameshields
                                WHERE 
                                  nid = $this->picid
                              ");
          if(!$database->query())
          {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
          }

          //Bild loeschen
          $database->setQuery(" DELETE 
                                FROM 
                                  #__joomgallery
                                WHERE 
                                  id = $this->picid
                              ");
          if(!$database->query())
          {
            echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
          }
        }
        else
        {
          die(JText::_('JGS_COULD_NOT_DELETE_THUMBNAIL_FILE'));
        }
      }
      else
      {
        die(JText::_('JGS_COULD_NOT_DELETE_PICTURE_FILE'));
      }
      $mainframe->redirect(JRoute::_($this->userpanel_url._JOOM_ITEMID,false),
                                     JText::_('JGS_ALERT_PICTURE_AND_COMMENTS_DELETED'));
    }
  }//End function Joom_User_DeletePic


/**
* Upload eines oder mehrerer Bilder
*/
  function Joom_User_ShowUpload()
  {

    //fuer Admin/SuperAdmin werden alle Kategorien angezeigt
    if($this->adminlogged)
    {
      $clist = Joom_ShowDropDownCategoryList(0, 'catid', ' class="inputbox"');
    }
    else
    {
      //wenn $config->jg_userowncatsupload=true, duerfen user nur in eigens erstellte
      //User-Kategorien hochladen
      $clist = $this->Joom_User_ShowDropDownCategoryList(0, null, 'catid', true);
    }
    HTML_Joom_UserPanel::Joom_User_ShowUpload_HTML($clist);
  }//End function Joom_User_ShowUpload


/**
 * Aufbau HTML Auswahlliste der vom User angelegten Kategorien
 * und der fuer den Upload freigegebenen Katgeorien
 *
 * @param int catid akt cat oder parent
 * @param int $ignoreme cid, ignoriere die Untercats dieser cat
 * @param string Name fuer das HTML Element, catid oder parent
 * @param bool wenn true, 
 * @return string HTML
 */
  function Joom_User_ShowDropDownCategoryList($cid, $ignoreme=null, $cname='catid', $upload=false)
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    //im Backend fuer den Userupload freigegebene Kategorien
    if($upload)
    {
      if(!empty($config->jg_category))
      {
        $allowedcats = $config->jg_category;
      } 
    }
    else
    {
      //im Backend fuer die Anlage von Userkategorien freigegebene Kategorien    
      if(!empty($config->jg_usercategory))
      {
        $allowedcats = $config->jg_usercategory;
      }
      else
      {
        $allowedcats = '';
      }
    }

    $query = "  SELECT 
                  cid, 
                  parent, 
                  name ,
                  '0' AS ready
                FROM 
                  #__joomgallery_catg
              ";

    if ($upload && !$config->jg_userowncatsupload)
    {
      $query .= " WHERE owner IS NOT NULL";
    }
    else
    {
      $query .= " WHERE owner=".$user->get('id');
    }

    if(!empty($allowedcats))
    {
       $query .= " OR cid IN ($allowedcats)";
    }
  
    $database->setQuery($query);
    $rows = $database->loadObjectList("cid");

    $countrows = count($rows);

    if($countrows == 0 && $upload)
    {
      return null;
    }

    $output = "<select name=\"".$cname."\" class=\"inputbox\">\n";

    if($countrows == 0)
    {
      $output .= "</select>\n";
      return $output;
    }

    //wenn cname = parent und ignoreme != null, dann die Cats loeschen, die direkt
    //oder indirekt child der cat=ignoreme sind, 
    //$cid = Cat des Parent, $ignoreme=akt. Cat
    //nur bei Edit Cat
    if($cname == 'parent' && $ignoreme != null)
    {
      $ignorearr   = array();//zu ignorierende cats
      $ignorearr[] = $ignoreme;//akt. Cat aufnehmen
      $backendcats = explode(',', $allowedcats);
      foreach($rows as $key => $obj)
      {
        //wenn Backendcat -> ueberspringen
        //ebenso die aktuelle Cat
        if(in_array($key, $backendcats) || $key == $ignoreme)
        {
          continue;
        }
        $found  = false;
        $parent = $obj->parent;
        while(array_key_exists($parent, $rows) && !in_array($key, $ignorearr) && !$found)
        {
          $ignore[] = $key;
          if ($parent == $ignoreme)
          {
            $found =  true;
            break;
          }
          $parent = $rows[$parent]->parent;
        }
        if(!$found)
        {
          $ignore = array();
        }
        else
        {
          $ignorearr = array_merge($ignorearr, $ignore);
        }
      }

      //aus Array die in $ignore gesammelten nicht auszugebenden cats entfernen
      foreach($ignorearr as $catignore)
      {
        unset($rows[$catignore]);
      }
    }

    //Iteration through array and completion of the shown path in the input box
    foreach($rows as $key => $obj)
    {
      $parent = $obj->parent;

      //at first try to complete the name with a look in the array
      //to avoid unnecessary db queries
      while($parent != 0)
      {
        if(isset($rows[$parent]))
        {
          $rows[$key]->name = $rows[$parent]->name . ' &raquo; ' . $rows[$key]->name;
          //if found parent element includes completed pathname
          //leave the while to set the actual element to ready
          if($rows[$parent]->ready == true)
          {
            break;
          }
          else
          {
            $parent = $rows[$parent]->parent;
          }
        }
        else
        {
          $query = "  SELECT 
                        parent,
                        name 
                      FROM 
                        #__joomgallery_catg 
                      WHERE 
                        cid = ".$parent;
          $database->setQuery($query);
          $parentcat = $database->loadObject();
          $parent    = $parentcat->parent;
          $rows[$key]->name = $parentcat->name . ' &raquo; ' . $rows[$key]->name; 
        }
      }
      //mark cat element as ready when path of them completed
      $rows[$key]->ready = true;
    }

    //sort the array with key pathname if more than one element
    if(count($rows) > 1)
    {
      usort( $rows , "Joom_SortCatArray" );
    }
    
    //build the html
    foreach($rows as $key => $obj)
    {
      $output .= "<option value=\"".$obj->cid."\"";
      if($cid == $obj->cid)
      {
        $output .= " selected=\"selected\"";
      }
      $output .=">".$obj->name."</option>\n";
    }
    $output .= "</select>\n";

    return $output; 
  }//End function Joom_User_ShowDropDownCategoryList

}//End class Joom_UserPanel
?>
