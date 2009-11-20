<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/admin.pictures.php $
// $Id: admin.pictures.php 449 2009-06-14 11:57:04Z aha $
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

include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'admin.pictures.html.php');

class Joom_AdminPictures {

  var $copy_original;
  var $newcatid;
  var $pcatid;
  var $tcatid;
  var $imgfilename;
  var $imgthumbname;
  var $imgtitle;
  var $imgauthor;
  var $owner;
  var $imgtext;

  /**
   * Constructor of class Joom_AdminPictures
   * picture manager in backend
   * @param string $task
   * @param int $cid  id of category
   * @param int/array $id id(s) of picture(s)
   * @return Joom_AdminPictures
   */
  function Joom_AdminPictures($task, $cid, $id) {

    $this->copy_original         = Joom_mosGetParam('copy_original', '');
    $this->newcatid              = Joom_mosGetParam('catid', '');
    $this->pcatid                = Joom_mosGetParam('pcatid', '');
    $this->tcatid                = Joom_mosGetParam('tcatid', '');
    $this->imgfilename           = Joom_mosGetParam('imgfilename', '');
    $this->imgthumbname          = Joom_mosGetParam('imgthumbname', '');
    $clearPicVotes               = Joom_mosGetParam('clearpicvotes', 0);
    $sorder                      = Joom_mosGetParam('sorder', 0);

    switch($task) {
      case 'pictures':
        $this->Joom_ShowPictures();
        break;
      case 'newpic':
        $this->Joom_ShowNewPicture();
        break;
      case 'savenewpic':
        $this->Joom_SaveNewPicture();
        break;
      case 'editpic':
        $this->Joom_ShowEditPicture($id[0]);
        break;
      case 'saveeditpic':
        $this->Joom_SaveEditPicture($clearPicVotes);
        break;
      case 'canceleditpic':
        $this->Joom_CancelEditPicture ();
        break;
      case 'movepic':
        $this->Joom_ShowMovePictures($id);
        break;
      case 'savemovepic':
        $this->Joom_SaveMovePicture($id);
        break;
      case 'publishpic':
        $this->Joom_PublishPictures($id, 1);
        break;
      case 'unpublishpic':
        $this->Joom_PublishPictures($id, 0);
        break;
      case 'approvepic':
        $this->Joom_ApprovePictures($id, 1);
        break;
      case 'rejectpic':
        $this->Joom_ApprovePictures($id, 0);
        break;
      case 'orderup':
        if ($sorder == 0) {
          $this->Joom_OrderPictures($id[0], 1);
        } else {
          $this->Joom_OrderPictures($id[0], -1);
        }
        break;
      case 'orderdown':
        if ($sorder == 0) {
          $this->Joom_OrderPictures($id[0], -1);
        } else {
          $this->Joom_OrderPictures($id[0], 1 );
        }
        break;
      case 'removepic':
        $this->Joom_RemovePictures($id);
        break;
    }
  }

  /**
   * Get all necessary data for showing all pictures in picture manager
   * via function Joom_ShowPictures_HTML()
   *
   */
  function Joom_ShowPictures() {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $config_list_limit = $mainframe->getCfg('list_limit');

    $group='';

    //Prepare pagelimit choices
    $limit      = $mainframe->getUserStateFromRequest('joom.pictures.limit', 'limit', $config_list_limit);
    $limitstart = $mainframe->getUserStateFromRequest('joom.pictures.limitstart', 'limitstart', 0);

    //Prepare category and search choices
    $catid      = $mainframe->getUserStateFromRequest('joom.pictures.catid', 'catid', 0);
    $search     = $mainframe->getUserStateFromRequest('joom.pictures.search', 'search', '');
    $search     = $database->getEscaped(trim(strtolower($search)));
    $sort       = $mainframe->getUserStateFromRequest('joom.pictures.sort','sort', 0);
    $sorder     = $mainframe->getUserStateFromRequest('joom.pictures.sorder','sorder', 0);

    $where      = array();
    $sortorder  = '';
    if ($catid > 0) {
      $where[]   = "catid='$catid'";
    }
    switch ($sort){
      case 1:
        $where[]   = 'a.approved = 0';
        break;
      case 2:
        $where[]   = 'a.approved = 1';
        break;
      case 3:
        $where[]   = 'useruploaded = 1';
        break;
      case 4:
        $where[]   = 'useruploaded = 0';
        break;
    }

    if ($sorder == 0) {
      $sortorder = 'a.catid ASC, a.ordering DESC, imgdate DESC, imgtitle DESC';
    }else if ($sorder == 1) {
      $sortorder = 'a.catid ASC, a.ordering ASC, imgdate DESC, imgtitle DESC';
    }

    if ($search) {
      $where[]   = "LOWER(imgtitle) LIKE '%$search%' OR LOWER(imgtext) LIKE '%$search%' ";
      $group     = "GROUP BY id";
    }

    //Get total number of records
    $database->setQuery("SELECT COUNT(*)
        FROM #__joomgallery AS a "
        . (count($where) ? 'WHERE '
        . implode(' AND ', $where) : ''));
    $total = $database->loadResult();
    if ($database->getErrorNum()) {
      echo $database->stderr();
      return false;
    }
    if ($limit > $total) {
      $limitstart = 0;
    }

    //PageNavigation
    jimport('joomla.html.pagination');
    $pageNav = new JPagination( $total, $limitstart, $limit );

    //Do the main database query
    $where[]     = 'a.catid = cc.cid';
    $whereclause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    $database->setQuery("SELECT a.*, cc.name AS category
        FROM #__joomgallery AS a, #__joomgallery_catg AS cc
        $whereclause
        $group
        ORDER BY $sortorder",$pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();
    if ($database->getErrorNum()) {
      echo $database->stderr();
      return false;
    }
    $clist = Joom_ShowDropDownCategoryList($catid, 'catid', 'class="inputbox" size="1"
                                           onchange="document.adminForm.submit();"');

    $s_options[] = JHTML::_('select.option', "0", JText::_('JGA_ALL_PICTURES'));
    $s_options[] = JHTML::_('select.option', "1", JText::_('JGA_NOT_APPROVED_ONLY'));
    $s_options[] = JHTML::_('select.option', "2", JText::_('JGA_APPROVED_ONLY'));
    $s_options[] = JHTML::_('select.option', "3", JText::_('JGA_USER_UPLOADED_ONLY'));
    $s_options[] = JHTML::_('select.option', "4", JText::_('JGA_ADMIN_UPLOADED_ONLY'));
    $slist       = JHTML::_('select.genericlist', $s_options, 'sort', 'class="inputbox" size="1"
                             onchange="document.adminForm.submit();"',
                             'value', 'text', $sort);

    $o_options[] = JHTML::_('select.option',"0",JText::_('JGA_ORDERBY_ORDERING_DESC'));
    $o_options[] = JHTML::_('select.option', "1", JText::_('JGA_ORDERBY_ORDERING_ASC'));
    $olist       = JHTML::_('select.genericlist', $o_options, 'sorder', 'class="inputbox"
                             size="1" onchange="document.adminForm.submit();"',
                             'value', 'text', $sorder);

    HTML_Joom_AdminPictures::Joom_ShowPictures_HTML($rows, $clist, $slist,
                                                    $search, $pageNav, $olist);
  }

  /**
   * provide functionality to set ordering with arrow symbols
   *
   * @param int $uid: id from picture e.g. 10
   * @param int $inc: control varaible up oder down, 1 or -1
  */
  function Joom_OrderPictures($uid, $inc) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    //get catid
    $database->setQuery( "SELECT catid
        FROM #__joomgallery
        WHERE id=$uid");
    $piccatid = $database->loadResult();
    
    $fp = new mosjoomgallery($database);
    $fp->load($uid);
    $fp->move($inc,'catid='.$piccatid);
    $fp->reorder('catid='.$piccatid);
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=pictures');
  }

  /**
   * publish/unpublish picture(s)
   *
   * @param int/array $cid id(s) of pictures
   * @param int $publish 1=publish 0=unpublish
   */
  function Joom_PublishPictures($cid=null, $publish=1) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    if (!is_array($cid) || count($cid) < 1) {
      $action = $publish ? 'publish' : 'unpublish';
      echo "<script> alert('" . JText::_('JGA_ALERT_SELECT_AN_ITEM',true) . " $action'); window.history.go(-1);</script>\n";
      exit;
    }

    $cids = implode(',', $cid);

    $database->setQuery("UPDATE #__joomgallery
        SET published='$publish'
        WHERE id IN ($cids)" );
    if (!$database->query()){
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
      exit();
    }
    //TODO aha: message
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=pictures');
  }

  /**
   * approve/unapprove picture(s)
   *
   * @param int/array $cid id(s) of pictures
   * @param int $approve 1=approve 0=unapprove
   */
  function Joom_ApprovePictures($cid=null, $approve=1) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    //any items to work on?
    if (!is_array($cid) || count($cid) < 1) {
      $action = $approve ? 'approve' : 'reject';
      echo "<script> alert('" . JText::_('JGA_ALERT_SELECT_AN_ITEM',true) . " $action'); window.history.go(-1);</script>\n";
      exit;
    }

    $cids = implode(',', $cid);

    $database->setQuery("UPDATE #__joomgallery
        SET approved='$approve'
        WHERE id IN ($cids)");
    if (!$database->query()) {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
      exit();
    }
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=pictures');
  }

  /**
   * get all necessary data for creation of a new picture (NEW in picture manager)
   * and calls Joom_ShowNewPicture_HTML()
   */
  function Joom_ShowNewPicture() {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
    jimport('joomla.filesystem.file');

    //If the category of picture or thumb had been changed the site will be
    //refreshed. Then get the new values from $_POST or $mainframe->getUserStateFromRequest

    //Title
    $this->imgtitle = Joom_mosGetParam('imgtitle', '', 'post');

    //Destination category
    $this->newcatid = $mainframe->getUserStateFromRequest("newcatid{"._JOOM_OPTION."}",'catid',0);

    //Description
    $this->imgtext =  Joom_mosGetParam('imgtext', '', 'post');

    //Author
    $this->imgauthor =  Joom_mosGetParam('imgauthor', '', 'post');

    // Owner
    $this->owner =  Joom_mosGetParam('owner', '', 'post');

    //Source category for original and detail picture
    $this->pcatid = $mainframe->getUserStateFromRequest("pcatid{"._JOOM_OPTION."}", 'pcatid', 0);

    //Name of original and/or detail picture
    $this->imgfilename =  Joom_mosGetParam('imgfilename', '', 'post');

    //Source category for thumbnail
    $this->tcatid = $mainframe->getUserStateFromRequest("tcatid{"._JOOM_OPTION."}", 'tcatid', 0);

    //Name of thumbnail
    $this->imgthumbname =  Joom_mosGetParam('imgthumbname', '', 'post');

    //creation of dropdown list for new category
    $Lists['clist'] = Joom_ShowDropDownCategoryList($this->newcatid , 'catid', 'size="1"');

    //creation of dropdown list for original and detail picture
    $Lists['cplist'] = Joom_ShowDropDownCategoryList($this->pcatid, 'pcatid',
                                                   "class=\"inputbox\" size=\"1\" "
                                                   . "onchange=\"document.adminForm.submit();\"");

    //get path for original and detail picture
    $ppath = Joom_GetCatPath($this->pcatid);

    // Zusammensetzen des absoluten Kategorie-Pfades fuer Original- und Detailbild
    $pcatpath = JPATH_ROOT.DS.$config->jg_pathimages.$ppath;

    //read the folder for original and detail pictures
    $imgFiles = JFolder::files($pcatpath);

    //array of pictures
    $images = array( JHTML::_('select.option', '', JText::_('JGA_PLEASE_SELECT_IMAGE')) );

    foreach ($imgFiles as $file) {
      // if prefix one of bmp, gif, jpg, png, jpeg oder jpe hat...
      if (eregi("bmp|gif|jpg|png|jpeg|jpe", $file)) {
        //add them to array
        $images[] = JHTML::_('select.option', $file );
      }
    }
    $Lists['imagelist'] = JHTML::_('select.genericlist',$images, 'imgfilename',
                                 "class=\"inputbox\" size=\"1\" "
                                 . "onchange=\"javascript:"
                                 . "if (document.forms[0].imgfilename.options[selectedIndex].value!='') {"
                                 .   "document.imagelib2.src='../".$config->jg_pathimages.$ppath."' "
                                 .   "+ document.forms[0].imgfilename.options[selectedIndex].value;"
                                 .   "document.adminForm.submit();"
                                 . "} else {"
                                 .   "document.imagelib2.src='../images/M_images/blank.png'"
                                 . "}\"",'value', 'text', $this->imgfilename);


    //if original exists
    if (JFile::exists(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$ppath.$this->imgfilename)) {
      //show them as existent
      $original_message = 1;
    } else if (!JFile::exists(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$ppath.$this->imgfilename)){
      //or not existent
      $original_message = 0;
    }

    //drop down list for choosing original
    $yesno[] = JHTML::_('select.option','1', JText::_('YES'));
    $yesno[] = JHTML::_('select.option','0', JText::_('NO'));
    $Lists['copy_original'] = JHTML::_('select.genericlist',$yesno, 'copy_original',
                                       'class="inputbox" size="1"',
                                       'value', 'text', $this->copy_original );

    //categorie drop down for thumbnail
    $Lists['ctlist'] = Joom_ShowDropDownCategoryList($this->tcatid, 'tcatid',
                                                     'class="inputbox" size="1" "
                                                     ."onchange="document.adminForm.submit();"');

    //catpath for thumbnail
    $tpath = Joom_GetCatPath($this->tcatid);

    //compose absolute catpath to thumbnail
    $tcatpath = JPATH_ROOT.DS.$config->jg_paththumbs.$tpath;

    //read folder of thumbails
    $thuFiles = JFolder::files($tcatpath);
    $thumbs = array(JHTML::_('select.option','', JText::_('JGA_PLEASE_SELECT_THUMBNAIL')) );
    foreach ($thuFiles as $tfile) {
      //if extension one of bmp, gif, jpg, png, jpeg oder jpe ...
      if (eregi( "bmp|gif|jpg|png|jpeg|jpe", $tfile)) {
        //add them to array
        $thumbs[] = JHTML::_('select.option',$tfile);
      }
    }
    $Lists['thumblist'] = JHTML::_('select.genericlist',$thumbs, 'imgthumbname',
                                   "class=\"inputbox\" size=\"1\""
                                   . " onchange=\"javascript:"
                                   . "if (document.forms[0].imgthumbname.options[selectedIndex].value!='') {"
                                   .   "document.imagelib.src='../".$config->jg_paththumbs.$tpath."' "
                                   .   "+ document.forms[0].imgthumbname.options[selectedIndex].value"
                                   . "} else {"
                                   .   "document.imagelib.src='../images/M_images/blank.png'"
                                   . "}\"",
                                   'value', 'text', $this->imgthumbname);

    // Build User select list
    $selname = ($config->jg_realname)?"name":"username";
    $sql	= "SELECT id as value, $selname as text"
	  . "\n FROM #__users"
	  . "\n ORDER BY $selname";

    $database->setQuery($sql);
    if (!$database->query()) {
	    echo $database->stderr();
	    return;
    }

    // set owner to current admin user, if none set:
    $owner = ($this->owner) ? $this->owner : $user->get('id');
    $Lists['users'] = JHTML::_('select.genericlist', $database->loadObjectList(), 'owner', 'class="inputbox" size="1"','value', 'text', $owner);

    HTML_Joom_AdminPictures::Joom_ShowNewPicture_HTML($Lists, $original_message);
  }

  /**
   * saves a picture created by NEW in picture manager
   * move originals/details/thumbs and save the new database entry
  */
  function Joom_SaveNewPicture() {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    jimport('joomla.filesystem.file');

    //category path for destination category
    $catpath = Joom_GetCatPath($this->newcatid);
    // source path original and detail
    $pcatpath = Joom_GetCatPath($this->pcatid);
    // source path thumbnail
    $tcatpath = Joom_GetCatPath($this->tcatid);

    //TODO: error messages
    //check if thumbnail already exists, if so don't move through the
    //following actions
    if (!JFile::exists(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname)) {
      //if the destination thumbnail directory not exists
      if (!is_dir(JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath))) {
        // error message and abort
        Joom_AlertErrorMessages(0,$this->newcatid,JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath),
                                $this->imgthumbname);
      // ...if exists
      } else {
        //open it
        $dir = opendir(JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath));
        //not succesful....
        if (!$dir) {
          //error message and abort
          Joom_AlertErrorMessages(0,$this->newcatid,JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath),
                                  $this->imgthumbname);
        //successful
        } else {
          //try to cop the thumbnail from source to destination
          $resthu = JFile::copy($config->jg_paththumbs.$tcatpath.$this->imgthumbname,
                                $config->jg_paththumbs.$catpath.$this->imgthumbname,
                                JPATH_ROOT);

          //not succesful
          if (!$resthu) {
            //close the directory
            closedir($dir);
            //error message and abort
            Joom_AlertErrorMessages(0,$this->newcatid,JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath),
                                    $this->imgthumbname);
          }
        }
      }
    //if thumbnail already exists
    } else {
      //set a control variable to avoid deleting it in case of aborting
      //the function
      $thumb_exist = 1;
    }

    //same procedure like thumbnail for copying the detail
    //in case of error delete the copied thumbnail from destination
    if (!JFile::exists(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$this->imgfilename)) {
      if (!is_dir(JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath))) {
        if (!$thumb_exist) JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname);
          Joom_AlertErrorMessages(0,$this->newcatid,JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath),
                                  $this->imgfilename);
      } else {
        $dir = opendir(JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath));
        if (!$dir) {
          if (!$thumb_exist) JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname);
            Joom_AlertErrorMessages(0,$this->newcatid,JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath),
                                    $this->imgfilename);
        } else {
          $respic = JFile::copy($config->jg_pathimages.$pcatpath.$this->imgfilename,
                                $config->jg_pathimages.$catpath.$this->imgfilename,
                                JPATH_ROOT);
          if (!$respic) {
            closedir($dir);
            if (!$thumb_exist) JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname);
              Joom_AlertErrorMessages(0, $this->newcatid,JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath),
                                      $this->imgfilename);
          }
        }
      }
    } else {
      $picture_exist = 1;
    }

    //if setted to create a original do the following action otherwise don not
    //copy the picture
    if ($this->copy_original == 1) {
      //already exists in destination directory
      if (!JFile::exists(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$this->imgfilename)) {
        if (JFile::exists(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$pcatpath.$this->imgfilename)) {
          //use from now on the path to originals
          $picturepath = JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$pcatpath);
        //picture does not exists
        } else {
          //use from now on the path to details and use detail picture as
          //original
          $picturepath = JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$pcatpath);
        }
        //directory does not exists
        if (!is_dir(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath))) {
          //delete the thumbnail and detail already created
          if (!$thumb_exist) JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname);
          if (!$picture_exist) JFile::delete(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$this->imgfilename);
            //error message and abort
            Joom_AlertErrorMessages(0, $this->newcatid,JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath),
                                    $this->imgfilename);
        //directory exists
        } else {
          $dir = opendir($picturepath);
          if (!$dir) {
            //delete the thumbnail and detail already created
            if (!$thumb_exist) JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname);
            if (!$picture_exist) JFile::delete(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$this->imgfilename);
              //error message and abort
              Joom_AlertErrorMessages(0,$this->newcatid,JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath),
                                      $this->imgfilename);
          } else {
            //destination directory exists
            //try to copy the picture from source to destination
            $resori = JFile::copy($picturepath.$this->imgfilename,
                                  JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$this->imgfilename));
            //not succesful
            if (!$resori) {
              closedir($dir);
              //delete thumbnail and detail if already exists
              if (!$thumb_exist) JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname);
              if (!$picture_exist) JFile::delete(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$this->imgfilename);
                Joom_AlertErrorMessages(0, $this->newcatid, $picturepath, $this->imgfilename);
            }
          }
        }
      }
    }

    //create new row in database for picture
    $row = new mosjoomgallery($database);
    //TODO error messages and abort if not succesful
    if (!$row->bind($_POST)) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    if(get_magic_quotes_gpc()) {
      $row->imgtitle = stripslashes($row->imgtitle);
      $row->imgtext = stripslashes($row->imgtext);
      $row->imgauthor = stripslashes($row->imgauthor);
    }
    //save the row
    //if not succesful error messages and abort
    //and redirect to picture manager
    if (!$row->store()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    //redirect
    //TODO message
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=pictures');
  }

  /**
   * Show the form to edit a picture
   *
   * @param int $uid id of picture
   */
  function Joom_ShowEditPicture($uid) {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();

    $row = new mosjoomgallery($database);
    $row->load($uid);
    $database->setQuery("SELECT name
        FROM #__joomgallery_catg
        WHERE cid = $row->catid" );
    $catname = $database->loadResult();

    // Build User select list
    $selname = ($config->jg_realname)?"name":"username";
    $sql	= "SELECT id as value, $selname as text"
	  . "\n FROM #__users"
  	. "\n ORDER BY $selname";

    $database->setQuery($sql);
    if (!$database->query()) {
	    echo $database->stderr();
	    return;
    }

    // set owner to current admin user, if none set:
    $owner = ($row->owner) ? $row->owner : $user->get('id');
    $Lists['users'] = JHTML::_('select.genericlist', $database->loadObjectList(), 'owner', 'class="inputbox" size="1"','value', 'text', $owner);

    HTML_Joom_AdminPictures::Joom_ShowEditPicture_HTML($row, $catname, $Lists);
  }

  /**
   * Saves a via EDIT in picture manager modified picture
   * @param bool $clearPicVotes true=clear votes
   */
  function Joom_SaveEditPicture($clearPicVotes = false) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    $row = new mosjoomgallery($database);
    if (!$row->bind($_POST)) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    if(get_magic_quotes_gpc()) {
      $row->imgtitle = stripslashes($row->imgtitle);
      $row->imgtext = stripslashes($row->imgtext);
      $row->imgauthor = stripslashes($row->imgauthor);
    }

    //clear votes if "clear" checked
    if ($clearPicVotes){
      $row->imgvotes = 0;
      $row->imgvotesum = 0;
      // delete votes for picture
      $query = "DELETE FROM #__joomgallery_votes WHERE picid = $row->id";
      $database->setQuery($query);
      if (!$database->query()){
        echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        exit();
      }
    }

    if (!$row->store()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }

    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=pictures');
  }

  /**
   * Shows the form to move picture(s)
   *
   * @param int/array $id id(s) of picture
   */
  function Joom_ShowMovePictures($id) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    $catid = $mainframe->getUserStateFromRequest("catid", 'catid', 0);
    //query to list items from pictures
    $ids = implode(',', $id);
    $query = "SELECT *
        FROM #__joomgallery
        WHERE id IN ( " . $ids . " )
        ORDER BY id, imgtitle";
    $database->setQuery($query);
    $items = $database->loadObjectList();
    //category select list
    $options = array(JHTML::_('select.option','1', JText::_('JGA_SELECT_CATEGORY')));
    //TODO: need of $options?
    $Lists['catgs'] = Joom_ShowDropDownCategoryList($catid, 'catid', 'class="inputbox" size="1" ');

    HTML_Joom_AdminPictures::Joom_ShowMovePictures_HTML($id, $Lists, $items);
  }

  /**
   * Move pictures
   * @param array $id: id's of pictures to be moved
  */
  function Joom_SaveMovePicture($id) {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
    jimport('joomla.filesystem.file');

    //TODO: error messages
    //id of destination category
    $pictureMove = JRequest::getInt('catid', '', 'post');
    //when no category chosen before
    if (!$pictureMove || $pictureMove == 0) {
        //error message and abort
        echo "<script> alert('" . JText::_('JGA_ALERT_PLEASE_SELECT_CATEGORY',true)  . "');
              window.history.go(-1);</script>\n";
        exit;
    //category chosen
    } else {
      //and the array contains picture
      if (count($id)) {
        //create a new array which contains the succesful moved pictures
        $successids = array();
        //loop through array
        for ($i = 0; $i < count($id); $i++) {
          //database query for picture title and the catid from source category
          $database->setQuery("SELECT id, catid, imgfilename, imgthumbname
              FROM #__joomgallery
              WHERE id = $id[$i]" );
          //if succesful
          if ($database->query()) {
            //write in array
            $rows = $database->loadObjectList();
            $row = $rows[0];
            //write to class members
            $this->imgfilename = $row->imgfilename;
            $this->imgthumbname = $row->imgthumbname;
            $old_catid = $row->catid;
            //catpath for new category
            $catpath = Joom_GetCatPath($pictureMove);
            //catpath for old category
            $catpath_ori = Joom_GetCatPath($row->catid);
            //query if there are entries in which the picture file to move
            //is assigned and count them
            $database->setQuery("SELECT COUNT(id)
                FROM #__joomgallery
                WHERE imgthumbname = '".$this->imgthumbname."'
                AND id != '".$id[$i]."'
                AND catid = '".$old_catid."'" );
            $count2 = $database->loadResult();
            //check if thumbnail already exists in source directory and not
            //exists already in destination
            //otherwise the file will not be copied
            if ((JFile::exists(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath_ori.$this->imgthumbname)) &&
               (!JFile::exists(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname))) {
              //if there is no picture remaining in source directory
              //which uses the picture
              if ($count2 < 1) {
                //move the thumbnail
                $result = JFile::move($config->jg_paththumbs.$catpath_ori.$this->imgthumbname,
                                      $config->jg_paththumbs.$catpath.$this->imgthumbname,
                                      JPATH_ROOT);
              //otherwise...
              } else {
                //copy the thumbnail and let it remain in source
                $result = JFile::copy($config->jg_paththumbs.$catpath_ori.$this->imgthumbname,
                                      $config->jg_paththumbs.$catpath.$this->imgthumbname,
                                      JPATH_ROOT);
              }
              //if not succesful error message and abort
              if (!$result) {
                Joom_AlertErrorMessages(0, 0, 0, $successids);
              }
              //set control variable according to succesful move/copy
              $thumb = 1;
            //not succesful
            } else {
              $thumb = 0;
            }
            //same procedure with detail
            //in case of error call previous copy/move of
            $database->setQuery("SELECT COUNT(id)
                FROM #__joomgallery
                WHERE imgfilename = '".$this->imgfilename."'
                AND id != '".$id[$i]."'
                AND catid = '".$old_catid."'");
            $count = $database->loadResult();
            $imgsource=JPATH_ROOT.DS.$config->jg_pathimages.$catpath_ori.$this->imgfilename;
            $imgdest=JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$this->imgfilename;
            if ((JFile::exists($imgsource)) && (!JFile::exists($imgdest))) {
              if ($count < 1) {
                $result2 = JFile::move($catpath_ori.$this->imgfilename,$catpath.$this->imgfilename,
                                       JPATH_ROOT.DS.$config->jg_pathimages); 
              } else {
                $result2 = JFile::copy($catpath_ori.$this->imgfilename,$catpath.$this->imgfilename,
                                       JPATH_ROOT.DS.$config->jg_pathimages);
              }
              if (!$result2) {
                if ($thumb == 1) {
                  if ($count2 < 1) {
                    JFile::move($config->jg_paththumbs.$catpath.$this->imgthumbname,
                                $config->jg_paththumbs.$catpath_ori.$this->imgthumbname,
                                JPATH_ROOT);
                  } else {
                    JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname);
                  }
                }
                Joom_AlertErrorMessages(0, 0, 0, $successids);
              }
              $picture = 1;
            } else {
              $picture = 0;
            }
            if ((JFile::exists(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath_ori.$this->imgfilename)) &&
               (!JFile::exists(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$this->imgfilename))) {
              if ($count < 1) {
                $result3 = JFile::move($catpath_ori.$this->imgfilename,$catpath.$this->imgfilename,
                                       JPATH_ROOT.DS.$config->jg_pathoriginalimages);
              } else {
                $result3 = JFile::copy($catpath_ori.$this->imgfilename,$catpath.$this->imgfilename,
                                       JPATH_ROOT.DS.$config->jg_pathoriginalimages);
              }
              if (!$result3) {
                if ($thumb == 1) {
                  if ($count2 < 1) {
                    JFile::move($config->jg_paththumbs.$catpath.$this->imgthumbname,
                                $config->jg_paththumbs.$catpath_ori.$this->imgthumbname,
                                JPATH_ROOT);
                  } else {
                    JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$this->imgthumbname);
                  }
                }
                if ($picture == 1) {
                  if ($count < 1) {
                    JFile::move($config->jg_pathimages.$catpath.$this->imgfilename,
                                $config->jg_pathimages.$catpath_ori.$this->imgfilename,
                                JPATH_ROOT);
                  } else {
                    JFile::delete(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$this->imgfilename);
                  }
                }
                Joom_AlertErrorMessages(0, 0, 0, $successids);
              }
            }
          //if database query not succesful error message and abort
          } else {
            echo "<script> alert('".$database->getErrorMsg()."');
                  window.history.go(-1); </script>\n";
          }
        //add the succesful processed picture (id) to array
        array_push($successids,$id[$i]);
        //if all folder operations for the picture succesful
        //modify the database entry
        $pic = new mosjoomgallery($database);
        //call Joom_MovePictures
        $pic->Joom_MovePictures($id[$i], $pictureMove);
        //new object from mosCatgs
        $cat = new mosCatgs($database);
        $cat->load($pictureMove);
        }
      }
      //construct the success message
      //count of moved pictures
      $total = count($successids);
      //TODO: MSG 'x Bilder verschoben zu y' anpassen
      $msg = $total . " Pictures moved to " . $cat->name;
      //redirect to picture manager with success message
      $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=pictures',$msg);
    }
  }

  /**
   * Delete picture(s) from directory and database
   *
   * @param    Array  $cid: id's of pictures to be deleted
  */
  function Joom_RemovePictures($ids) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $config = Joom_getConfig();
    jimport('joomla.filesystem.file');

    //one or more pictures
    if (!is_array($ids) || count($ids) < 1) {
      //no picture(s) -> error message and abort
      $msg = JText::_('JGA_ALERT_SELECT_AN_ITEM_TO_DELETE');
			$mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=pictures', $msg, 'error');
    }
    //two arrays, one for the succesful deleted, one for the pictures with
    //error in actions
    //TODO: error messages
    $deleted_items    = array();
    $notdeleted_items = array();

    // loop through array
    foreach($ids as $id) {
      //database query to get the category, name of picture and thumb
      $database->setQuery("SELECT id, catid, imgfilename, imgthumbname
          FROM #__joomgallery
          WHERE id = $id" );
      if($database->query()){
        $row = $database->loadObject();
        //catpath for category
        $catpath = Joom_GetCatPath($row->catid);
        //database query to check if there are other pictures with this thumbnail
        //assigned and how many
        $database->setQuery("SELECT COUNT(id)
            FROM #__joomgallery
            WHERE imgthumbname = '".$row->imgthumbname."'
            AND id != '".$row->id."'
            AND catid = '".$row->catid."'" );
        $count = $database->loadResult();
        //database query to check if there are other pictures with this detail
        //or original assigned ad how many
        $database->setQuery("SELECT COUNT(id)
            FROM #__joomgallery
            WHERE imgfilename = '".$row->imgfilename."'
            AND id != '".$row->id."'
            AND catid = '".$row->catid."'" );
        $count2 = $database->loadResult();
        //delete the thumbnail if there are no other pictures
        //in same category assigned to it
        if ($count < 1) {
          if(!JFile::delete(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->imgthumbname)){
            //if thumbnail is not deleteable error message and abort
            Joom_AlertErrorMessages(0, $row->catid,
                                    JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath),
                                    $row->imgthumbname);
          }
        }
        //delete the detail if there are no other detail and
        //originals from same category assigned to it
        if ($count2 < 1) {
          if(!JFile::delete(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$row->imgfilename)){
            //if detail is not deleteable error message and abort
            Joom_AlertErrorMessages(0, $row->catid,
                                    JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath),
                                    $row->imgfilename);
          }
          //original exists?
          if (JFile::exists(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$row->imgfilename)) {
            //delete it
            if (!JFile::delete(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$row->imgfilename)){
              //if original is not deleteable error message and abort
              Joom_AlertErrorMessages(0, $row->catid,
                                      JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath),
                                      $row->imgfilename);
            }
          }
        }
      //not succesful database query
      } else {
        echo "<script> alert('".$database->getErrorMsg()."');
              window.history.go(-1); </script>\n";
      }
      //TODO aha: better wrap the following DB actions in 'begin...commit/rollback'
      //to get consistence in case of error
        
      //delete the database entry of picture
      $database->setQuery("DELETE
          FROM #__joomgallery
          WHERE id = $id");
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."');
              window.history.go(-1); </script>\n";
      }
      //delete the corresponding database entries in comments
      $database->setQuery("DELETE
          FROM #__joomgallery_comments
          WHERE cmtpic = $id" );
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."');
              window.history.go(-1); </script>\n";
      }
      //delete the corresponding database entries in nameshields
      $database->setQuery("DELETE
          FROM #__joomgallery_nameshields
          WHERE npicid = $id" );
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."');
              window.history.go(-1); </script>\n";
      }
      //add the id of succesful deleted picture to array
      array_push($deleted_items, $id);
    }
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=pictures');
  }

  /**
   * Cancel editing picture
   */
  function Joom_CancelEditPicture () {
    $mainframe = & JFactory::getApplication('administrator');
    $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=pictures');
  }

}
?>
