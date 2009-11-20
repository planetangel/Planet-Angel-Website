<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/admin.categories.php $
// $Id: admin.categories.php 449 2009-06-14 11:57:04Z aha $
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

include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'admin.categories.html.php');

class Joom_AdminCategories {

/**
 * Constructor of Joom_AdminCategories
 *
 * @param string $task
 * @param int $cid
 * @return Joom_AdminCategories
 */
  function Joom_AdminCategories($task, $cid) {
    switch($task) {
      case 'categories':
        $this->Joom_ShowCategories();
        break;
      case 'orderupcatg':
        $this->Joom_OrderCategory($cid[0], -1);
        break;
      case 'orderdowncatg':
        $this->Joom_OrderCategory($cid[0], 1);
        break;
      case 'publishcatg':
        $this->Joom_PublishCategories($cid, 1);
        break;
      case 'unpublishcatg':
        $this->Joom_PublishCategories($cid, 0);
        break;
      case 'newcatg':
        $this->Joom_ShowNewCategory($cid/*, $ordering*/);
      break;
      case 'savenewcatg':
        $this->Joom_SaveNewCategory();
        break;
      case 'editcatg':
        $this->Joom_ShowEditCategory($cid[0]);
        break;
      case 'saveeditcatg':
        $this->Joom_SaveEditCategory($cid);
        break;
      case 'removecatg':
        $this->Joom_RemoveCategories($cid);
        break;
      case 'cancelcatg':
        $this->Joom_CancelCategory();
        break;
    }
  }

  /**
   * Determine all necessary data to display all categories in category manager
   * invoke Joom_ShowCategories_HTML()
   */
  function Joom_ShowCategories() {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $config_list_limit = $mainframe->getCfg('list_limit');

    $limit      = $mainframe->getUserStateFromRequest('joom.categories.limit','limit',$config_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest('joom.categories.limitstart','limitstart',0);
    $search     = $mainframe->getUserStateFromRequest('joom.categories.search','search','');
    $search     = $database->getEscaped(trim(strtolower($search)));
    $sortcat    = $mainframe->getUserStateFromRequest('joom.categories.sort','sortcat',0);
    $sordercat  = $mainframe->getUserStateFromRequest('joom.categories.sorder','sordercat',0);

    //Sortierung
    $sortorder  = '';
    switch($sordercat) {
      case 0:
        $sortorder = 'a.ordering ASC';
        break;
      case 1:
        $sortorder = 'a.ordering DESC';
        break;
      case 2:
        $sortorder = 'a.catpath ASC';
        break;
      case 3:
        $sortorder = 'a.catpath DESC';
        break;
      case 4:
        $sortorder = 'a.cid ASC';
        break;
      case 5:
        $sortorder = 'a.cid DESC';
        break;
      case 6:
        $sortorder = 'a.name ASC';
        break;
      case 7:
        $sortorder = 'a.name DESC';
        break;
      case 8:
        $sortorder = 'a.owner ASC';
        break;
      case 9:
        $sortorder = 'a.owner DESC';
        break;
    }
    if ($sortorder != ''){
      $sortorder = 'ORDER BY '.$sortorder;
    }

    $where = array();
    //Filter by type
    switch($sortcat) {
      //published
      case 1:
        $where[] = 'published = 1';
        break;
      //not published
      case 2:
        $where[] = 'published = 0';
        break;
      //user categories
      case 3:
        $where[] = 'owner IS NOT NULL';
        break;
      //admin categories
      case 4:
        $where[] = 'owner IS NULL';
        break;
    }

    if ($search) {
      $where[] = " a.name LIKE '%$search%' OR a.description LIKE '%$search%'";
    }

    $whereclause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    //set navigation
    $database->setQuery("SELECT COUNT(*)
        FROM #__joomgallery_catg AS a
        LEFT JOIN #__groups AS g ON g.id = a.access
        $whereclause");
    $total = $database->loadResult();

    jimport('joomla.html.pagination');
    $pageNav = new JPagination( $total, $limitstart, $limit );

    $database->setQuery( "SELECT a.*, g.name AS groupname
        FROM #__joomgallery_catg AS a
        LEFT JOIN #__groups AS g ON g.id = a.access
        $whereclause
        $sortorder",$pageNav->limitstart, $pageNav->limit);

    $rows = $database->loadObjectList();

    $o_options[] = JHTML::_('select.option',JText::_('JGA_ORDERBY_ORDERING_ASC'), 0);
    $o_options[] = JHTML::_('select.option',"1", JText::_('JGA_ORDERBY_ORDERING_DESC'));
    $o_options[] = JHTML::_('select.option',"2", JText::_('JGA_ORDERBY_CATPATH_ASC'));
    $o_options[] = JHTML::_('select.option',"3", JText::_('JGA_ORDERBY_CATPATH_DESC'));
    $o_options[] = JHTML::_('select.option',"4", JText::_('JGA_ORDERBY_DBID_ASC'));
    $o_options[] = JHTML::_('select.option',"5", JText::_('JGA_ORDERBY_DBID_DESC'));
    $o_options[] = JHTML::_('select.option',"6", JText::_('JGA_ORDERBY_CATNAME_ASC'));
    $o_options[] = JHTML::_('select.option',"7", JText::_('JGA_ORDERBY_CATNAME_DESC'));
    $o_options[] = JHTML::_('select.option',"6", JText::_('JGA_ORDERBY_DBOWNERID_ASC'));
    $o_options[] = JHTML::_('select.option',"7", JText::_('JGA_ORDERBY_DBOWNERID_DESC'));

    $olist = JHTML::_('select.genericlist',$o_options, 'sordercat',
      'class="inputbox" size="1" onchange="document.adminForm.submit();"',
      'value', 'text', $sordercat);

    $s_options[] = JHTML::_('select.option','', 0);
    $s_options[] = JHTML::_('select.option',"1", JText::_('JGA_PUBLISHED'));
    $s_options[] = JHTML::_('select.option',"2", JText::_('JGA_NOT_PUBLISHED'));
    $s_options[] = JHTML::_('select.option',"3", JText::_('JGA_USERCATEGORIES_ONLY'));
    $s_options[] = JHTML::_('select.option',"4", JText::_('JGA_BACKENDCATEGORIES_ONLY'));

    $slist = JHTML::_('select.genericlist',$s_options, 'sortcat',
      'class="inputbox" size="1" onchange="document.adminForm.submit();"',
      'value', 'text', $sortcat);

    HTML_Joom_AdminCategories::Joom_ShowCategories_HTML($rows, $search, $slist,
                                                        $olist, $pageNav);
  }

  /**
   * provides the functionality for the ordering of category entries
   * with the arrow symbols
   *
   * @param unknown_type $uid
   * @param unknown_type $inc
   */
  function Joom_OrderCategory($uid, $inc) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    //new object in mosCatgs
    $fp = new mosCatgs($database);
    //load the database row
    $fp->load($uid);
    //move the row above or below
    $fp->move($inc);
    // creates the corrected ordering for all categories and saves them
    $fp->reorder();
    // redirect to category manager
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=categories');
  }

  /**
  * Un/-publish one ore more chosen categories
  *
  * @param    integer    $cid: id of category e.g. "10"
  * @param    integer    $publish: published="1" unpublished="0"
  */
  function Joom_PublishCategories($cid=null, $publish=1) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    if (!is_array($cid) || count($cid) < 1) {
      $action = $publish ? 'publish' : 'unpublish';
      echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
      exit;
    }

    $cids = implode(',', $cid);
    $database->setQuery("UPDATE #__joomgallery_catg
        SET published='$publish'
        WHERE cid IN ($cids)");

    if (!$database->query()) {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
      exit();
    }

    if (count($cid) == 1) {
      $row = new mosCatgs($database);
      $row->checkin($cid[0]);
    }
    //redirect to category manager
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=categories');
  }

  /**
   * Collects all necessary data for creation of a new category
   * (NEW in category manager)
   * and calls Joom_ShowNewCategory_HTML()
   *
   * @param unknown_type $cid
   */
  function Joom_ShowNewCategory($cid) {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();

    //dropdown list of all categories  for superior category
    $Lists['catgs'] = Joom_ShowDropDownCategoryList(0,'parent');

    //list of alle acl groups (public,registered,special)
    $database->setQuery("SELECT id AS value, name AS text
        FROM #__groups
        ORDER BY id");
    $groups = $database->loadObjectList();
    //list for access
    $glist = JHTML::_('select.genericlist',$groups, 'access', 'class="inputbox" size="1"',
      'value', 'text');

    //choice list for publishing
    $yesno[] = JHTML::_('select.option','1', JText::_('YES'));
    $yesno[] = JHTML::_('select.option','0', JText::_('NO'));

    $publist = JHTML::_('select.genericlist',$yesno, 'published', 'class="inputbox" size="1"',
      'value', 'text');

    //list for category ordering
    $orders = JHTML::_('list.genericordering',"SELECT ordering AS value, name AS text
        FROM #__joomgallery_catg
        ORDER BY ordering");
    $orderlist = JHTML::_('select.genericlist',$orders, 'ordering', 'class="inputbox" size="1"',
      'value', 'text');

    HTML_Joom_AdminCategories::Joom_ShowNewCategory_HTML($publist, $glist,
                                                         $Lists, $orderlist,
                                                         $config->jg_wrongvaluecolor);
  }

  /**
   * Saves a new category
   * at first create a database entry, then check if the folder are created
   * successfully. Then complete the DB entry with the categeory path
   */
  function Joom_SaveNewCategory() {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
    jimport('joomla.filesystem.file');

    $row = new mosCatgs($database);
    //new DB entry in mosCatgs
    //if not successful exit to previous site
    //TODO aha: redirect
    if (!$row->bind($_POST)) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    if(get_magic_quotes_gpc()) {
      $row->name = stripslashes($row->name);
      $row->description = stripslashes($row->description);
    }
    //make the variable safe
    JFilterOutput::objectHTMLSafe($row->name);

    //check if the conversion of category id to integer is possible
    //if not successful exit to previous site
    //TODO aha redirect
    if (!$row->check()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }

    //if creating of database entry not successful
    //exit to previous site
    //TODO aha redirect
    if (!$row->store()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    //checkin of new object
    $row->checkin();
    //new ordering
    $row->reorder();
    //if the new category should be assigned as subcategory...
    if ($row->parent) {
      //save the category path of parent category in a variable
      $parentpath = Joom_GetCatPath($row->parent);
    // otherwise let it empty
    } else {
      $parentpath = '';
    }
    //creation of category path
    //cleaning of category title with function Joom_FixFilename
    //so special chars are converted and underscore removed
    //affects only the category path
    $newcatname = Joom_FixFilename($row->name);
    //add a undersore and the category id
    //affects only the category path
    $newcatname = $newcatname . '_' . $row->cid;
    //prepend - if exists - the parent category path
    $newcatname = $parentpath . $newcatname;
    //create the paths of category fpr originals, pictures, thumbnails
    $cat_originalpath  = JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$newcatname);
    $cat_picturepath   = JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$newcatname);
    $cat_thumbnailpath = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$newcatname);
    //TODO: error message

    //create the folder of category in originals
    $resorig = Joom_MakeDirectory($cat_originalpath);

    //if not succesful, delete the database entry
    //show the according error message and abort
    if ($resorig != 0) {
      $row->delete();
      Joom_AlertErrorMessages(0, 0, 0, 0);
    } else {
      //copy the assets/index.html in new folder
      JFile::copy(JPATH_COMPONENT_SITE.DS.'assets'.DS.'index.html',$cat_originalpath.DS.'index.html');
    }

    //create the folder of category in pictures
    $respic = Joom_MakeDirectory($cat_picturepath);

    //if not succesful, delete the database entry
    //show the according error message and abort
    if ($respic != 0) {
      $row->delete();
      if ($resorig == 0) {
        JFolder::delete($cat_originalpath);
      }
      Joom_AlertErrorMessages(0, 0, 0, 0);
    } else {
      //copy the assets/index.html in new folder
      JFile::copy(JPATH_COMPONENT_SITE.DS.'assets'.DS.'index.html',$cat_picturepath.DS.'index.html');
    }

    //create the folder of category in thumbnails
    $resthumb = Joom_MakeDirectory($cat_thumbnailpath);

    //if not succesful, delete the database entry
    //show the according error message and abort
    if ($resthumb != 0) {
      $row->delete();
      if ($resorig == 0) {
        JFolder::delete($cat_originalpath);
      }
      if ($respic == 0) {
        JFolder::delete($cat_picturepath);
      }
      Joom_AlertErrorMessages(0, 0, 0, 0);
    }  else {
      //copy the assets/index.html in new folder
      JFile::copy(JPATH_COMPONENT_SITE.DS.'assets'.DS.'index.html',$cat_thumbnailpath.DS.'index.html');
    }

    //update of database entry with new catpath
    $database->setQuery("UPDATE #__joomgallery_catg
        SET catpath='$newcatname'
        WHERE cid=$row->cid");
    $database->query();

    //redirect to category manager
    //TODO aha: message
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=categories');
  }


  /**
   * Gets the data for editing a category via category manager
   * then calls Joom_ShowEditCategory_HTML()
   * @param    integer   $uid: id of category e.g. 10
  */
  function Joom_ShowEditCategory($uid) {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();

    //new object from mosCatgs
    $row = new mosCatgs($database);
    //lod row
    $row->load($uid);
    $parent = $row->parent;

    // DropDown category list for die parent category
    $Lists['catgs'] = Joom_ShowDropDownCategoryList($parent, 'parent', '', $uid);

    // liste of ACL groups (public, registered, special)
    $database->setQuery("SELECT id AS value, name AS text
        FROM #__groups
        ORDER BY id");
    $groups = $database->loadObjectList();

    //list of access
    $glist = JHTML::_('select.genericlist', $groups, 'access', 'class="inputbox" size="1"',
      'value', 'text', intval($row->access));

    //list for ordering the category
    $orders = JHTML::_('list.genericordering',"SELECT ordering AS value, name AS text
        FROM #__joomgallery_catg
        ORDER BY ordering");

    $orderlist = JHTML::_('select.genericlist',$orders, 'ordering', 'class="inputbox" size="1"',
      'value', 'text', intval($row->ordering));

    //list of thumbnail position
    $align[]    = JHTML::_('select.option','0' , JText::_('JGA_LEFT'));
    $align[]    = JHTML::_('select.option','1' , JText::_('JGA_RIGHT'));
    $align[]    = JHTML::_('select.option','2' , JText::_('JGA_CENTER'));
    $align_list = JHTML::_('select.genericlist',$align, 'img_position', 'class="inputbox" size="1"',
      'value', 'text', $row->img_position);

    //list of available thumbails of category
    $database->setQuery("SELECT imgthumbname
        FROM #__joomgallery
        WHERE catid=$uid
        ORDER BY imgthumbname");
    $thuFiles2 = $database->loadObjectList();
    $thumbs2   = array(JHTML::_('select.option','', JText::_('JGA_PLEASE_SELECT_THUMBNAIL')));
    foreach ($thuFiles2 as $tfile2) {
      if (eregi("bmp|gif|jpg|jpeg|jpe|png", $tfile2->imgthumbname)) {
        $thumbs2[] = JHTML::_('select.option', $tfile2->imgthumbname);
      }
    }
    //catpath of category
    $catpath    = Joom_GetCatPath($row->cid);

    //list for choosing thumbnail
    $thumblist2 = JHTML::_('select.genericlist',$thumbs2, 'catimage', 'class=\"inputbox\" size=\"1\"'
      . " onchange=\"javascript:"
      . "if (document.forms[0].catimage.options[selectedIndex].value!='') {"
      .   "document.imagelib.src='../".$config->jg_paththumbs.$catpath."' "
      .   "+ document.forms[0].catimage.options[selectedIndex].value"
      . "} else {"
      .   "document.imagelib.src='../images/M_images/blank.png'}\"",
        'value', 'text', $row->catimage);

    // Possible owners list
    $ownerlist = JHTML::_('list.users', 'owner', $row->owner, 1, NULL, 'name', 0 );
      
      
    HTML_Joom_AdminCategories::Joom_ShowEditCategory_HTML($row, $publist,
                                                          $glist, $Lists, $orderlist,
                                                          $thumblist2, $align_list,
                                                          $config->jg_paththumbs, $catpath,
                                                          $config->jg_wrongvaluecolor, $ownerlist);
  }

  /**
   * save a edited category
   *
   * @param int $cid id of category
   */
  function Joom_SaveEditCategory(&$cid) {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    jimport('joomla.filesystem.folder');

    $row = new mosCatgs($database);
    //read category from DB
    $row->load( $cid );

    //read old parent assignment
    $parentold = $row->parent;
    //read old title
    $catnameold = $row->name;

    //get new values
    if (!$row->bind($_POST)) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    if(get_magic_quotes_gpc()) {
      $row->name = stripslashes($row->name);
      $row->description = stripslashes($row->description);
    }

    if (intval($row->owner) == 0 ) {
     $row->owner = null;
    }
    
    //make the new category title safe
    if ($catnameold != $row->name){
      JFilterOutput::objectHTMLSafe($row->name);
      $catname = $row->name;
      $catnamemodif = true;
    } else {
      $catname = $catnameold;
      $catnamemodif = false;
    }

    //move the category folder, if parent assignment or category name changed
    if ($parentold != $row->parent || $catnamemodif == true){
      //save old path
      $catpathold = $row->catpath;
      $parentpathnew = Joom_GetCatPath($row->parent);

      //Joom_FixFilename() convert/remove special chars except the underscore
      //affects only catpath
      $catname = Joom_FixFilename($catname);
      $catpathnew = $parentpathnew . $catname . '_' . $row->cid;

      $cat_originalpathold  = JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpathold);
      $cat_picturepathold   = JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpathold);
      $cat_thumbnailpathold = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpathold);

      $cat_originalpathnew  = JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpathnew);
      $cat_picturepathnew   = JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpathnew);
      $cat_thumbnailpathnew = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpathnew);

      //move folders
      //actualize catpath in DB
      $row->catpath = $catpathnew;

      //TODO error messages
      JFolder::move($cat_originalpathold,$cat_originalpathnew);
      JFolder::move($cat_picturepathold,$cat_picturepathnew);
      JFolder::move($cat_thumbnailpathold,$cat_thumbnailpathnew);

      //if parent category changes, modify catpath of all subcategories in DB
      $rowid = $row->cid;
      Joom_UpdateNewCatpath($rowid,$catpathold,$catpathnew);
    }

    if (!$row->store(true)) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    // redirect to category manager
    //TODO aha: message
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=categories');
  }


  /**
   * Remove categories
   * checks if there are still any pictures or subcategories assigned
   * if yes abort
   * if no send an array with all id of categories to function
   * Joom_DeleteCategory
   *
   * @param    Array     $cid: id's of categories to delete
  */
  function Joom_RemoveCategories($cid) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    //TODO: error-messages
    //checks if categories selected
    if (count($cid)) {
      //create 2 new arrays
      //id's of categories with still assigned pictures
      $notemptycids = array();

      //id's of categories with no assigned pictures
      $emptycids    = array();

      //loop through select categories
      foreach ($cid as $cc) {
        //database query to check assigned pictures to category
        $database->setQuery("SELECT COUNT(id)
            FROM #__joomgallery
            WHERE catid = '$cc'");
        $is_notempty = $database->loadResult();

        //according to the result add the category id to the right array
        if (!$is_notempty) {
          array_push($emptycids , $cc);
        } else {
          array_push($notemptycids, $cc);
        }
      }
      //check if there are any empty categories
      //and concurrent not empty categories
      if (count($emptycids) && !count($notemptycids)) {
        //loop to check the empty categories
        foreach ($emptycids as $ecids ) {
          //database query: are there any subcategory assigned?
          $database->setQuery("SELECT COUNT(cid)
              FROM #__joomgallery_catg
              WHERE parent = '$ecids'");
          $is_nosubcat = $database->loadResult();
          //two new arrays
          //array of category id's with no assigned subcategories
          $nosubcats = array();

          //array of category id's with assigned subcategories
          $subcats   = array();

          //according to the result add the category id to the right array
          if (!$is_nosubcat) {
            array_push($nosubcats , $ecids);
          } else {
            array_push($subcats, $ecids);
          }
          //check if there are any categories with no assigned subcategories
          //and in concurrence not categories with still assigned subcategories
          if (count($nosubcats) && !count($subcats)) {
            //loop to delete the categories with no subcategories
            foreach ($nosubcats as $nosubs) {
              //call the function Joom_DeleteCategory()
              $this->Joom_DeleteCategory($nosubs);
            }
          //if there are no categories with no assigned subcategories
          //or only categories with assigned subcategories
          //then output an error message and abort
          } else {
            Joom_AlertErrorMessages(0, $subcats, 0, 0);
          }
        }
      //if there are no categories without pictures or only categories with pictures
      //then output an error message and abort
      } else {
        Joom_AlertErrorMessages(1001, $notemptycids, 0, 0);
      }
    }
    //redirect to category manager
    //TODO aha: message
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=categories');
  }


  /**
   * Deletes the folders and the database entry of category
   *
   * @param    integer   $catid: id of category, e.g. 10
  */
  function Joom_DeleteCategory($catid) {
    $database = & JFactory::getDBO();
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');

    //path of category
    $catpath = Joom_GetCatPath($catid);

    //compose the paths for originals, pictures, thumbs
    $catorigdir  = JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath);
    $catpicdir   = JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath);
    $catthumbdir = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath);

    //check with function Joom_CheckEmptyDirectory()
    //if folders are empty and writeable (permissions set for deletion)
    Joom_CheckEmptyDirectory($catorigdir, $catid);
    Joom_CheckEmptyDirectory($catpicdir, $catid);
    Joom_CheckEmptyDirectory($catthumbdir, $catid);

    //delete the folder in originals
    $resorig = JFolder::delete($catorigdir);
    //if not succesful, output an error message and abort
    if (!$resorig){
      Joom_AlertErrorMessages(0, $catid, $catorigdir, 0);
    }

    //delete the folder in pictures
    $respic = JFolder::delete($catpicdir);

    //if not succesful....
    if (!$respic) {
       //try to recreate the folder in originals
      $resdiro = Joom_MakeDirectory($catorigdir);

      //if not succesful, output an error message and abort
      if ($resdiro != 0) {
        if ($resdiro == -1){
          Joom_AlertErrorMessages(0, $catid, $catorigdir, 0);
        }
        if ($resdiro == -2){
          Joom_AlertErrorMessages(0, $catid, $catorigdir, 0);
        }
      } else {
        //if not succesful, output an error message and abort
        Joom_AlertErrorMessages(0, $catid, $catpicdir, 0);
      }
    }
    //delete the thumbnail folder
    $resthumb = JFolder::delete($catthumbdir);
    //if not succesful....
    if (!$resthumb) {
       //try to recreate the folder in originals
      $resdiro = Joom_MakeDirectory($catorigdir);
      //if not succesful, output an error message and abort
      if ($resdiro != 0) {
        if ($resdiro == -1){
          Joom_AlertErrorMessages(0, $catid, $catorigdir, 0);
        }
        if ($resdiro == -2){
          Joom_AlertErrorMessages(0, $catid, $catorigdir, 0);
        }
      } else {
        //if not succesful, output an error message about thumbnail folder and abort
        Joom_AlertErrorMessages(0, $catid, $catthumbdir, 0);
      }
      //and try to recreate the folder in pictures
      $resdirp = Joom_MakeDirectory($catpicdir);
      //if not succesful in recreation,  output an error message and abort
      if ($resdirp != 0) {
        if ($resdirp == -1){
          Joom_AlertErrorMessages(0, $catid, $catpicdir, 0);
        }
        if ($resdirp == -2){
          Joom_AlertErrorMessages(0, $catid, $catpicdir, 0);
        }
      } else {
        //if not succesful, output an error message about picture folder and abort
        Joom_AlertErrorMessages(0, $catid, $catpicdir, 0);
      }
    }
    //delete database entry if all folders succesfully deleted
    $database->setQuery("DELETE
        FROM #__joomgallery_catg
        WHERE cid = $catid");
    $database->query();
    echo $database->getErrorMsg();
    //update of ordering
    $fp = new mosCatgs($database);
    $fp->reorder();
    
    //delete the userstate variable 'catid' if exists
    $mainframe->setUserState('joom.pictures.catid','0');
  }

  /**
   * cancel an action in category manager (Cancel).
   *
  */
  function Joom_CancelCategory() {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    $row = new mosCatgs($database);
    $row->bind($_POST);
    $row->checkin();
    // redirect to category manager
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=categories');
  }
}
?>
