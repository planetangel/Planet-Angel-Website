<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/joomgallery.class.php $
// $Id: joomgallery.class.php 449 2009-06-14 11:57:04Z aha $
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

class mosjoomgallery extends JTable { // portierung: JTable anstatt mosDBTable

  /** @var int Primary key */
  var $id=null;
  /** @var int */
  var $catid=null;
  /** @var int */
  var $imgtitle=null;
  /** @var string */
  var $imgauthor=null;
  /** @var string */
  var $imgtext=null;
  /** @var string */
  var $imgdate=null;
  /** @var int */
  var $imgcounter=null;
  /** @var int */
  var $imgvotes=null;
  /** @var int */
  var $imgvotesum=null;
  /** @var int */
  var $published=null;
  /** @var string */
  var $imgfilename=null;
  /** @var string */
  var $imgthumbname=null;
  /** @var string */
  var $checked_out=null;
  /** @var string */
  var $owner=null;
  /** @var int */
  var $approved=null;
  /** @var int */
  var $useruploaded=null;
  /** @var int */
  var $ordering=null;  
  
  function mosjoomgallery( &$db ) {
    #$this->mosDBTable( '#__joomgallery', 'id', $db );  // portierung: original
    parent::__construct( '#__joomgallery', 'id', $db ); // portierung: neu
  }

  function Joom_MovePictures( $id, $catid ) {
    /** portierung **/
    $database = & JFactory::getDBO(); // anstatt global $database;
    $user = & JFactory::getUser();  // anstatt global $my;
    /* */

    if ((count($id) < 1)) { 
      echo "<script> alert('". JText::_('JGA_SELECT_AN_ITEM_TO_MOVE',true) ." ); window.history.go(-1);</script>\n"; 
      return false; 
    }
    if (is_array($id)) {
      $ids = implode(',', $id);
      $database->setQuery( "UPDATE #__joomgallery 
           SET catid='$catid'"
           ." \nWHERE id IN ($ids) ");
    } else {
      $database->setQuery( "UPDATE #__joomgallery 
          SET catid='$catid'"
          ." \nWHERE id = '".$id."' ");
    }
    if (!$database->query()) {
      echo "<script> alert('".$database->getErrorMsg() ."'); window.history.go(-1); </script>\n";
      return false;
    }
  return true;
  }

}


class mosCatgs extends JTable { // portierung: JTable anstatt mosDBTable

  /** @var int Primary key */
  var $cid=null;
  /** @var int */
  var $owner=null;
  /** @var string */
  var $name=null;
  /** @var int */
  var $parent=null;
  /** @var string */
  var $description=null;
  /** @var int */
  var $ordering=null;
  /** @var string */
  var $access=null;
  /** @var int */
  var $published=null;
  /** @var string */
  var $catimage=null;
  /** @var int */
  var $img_position=null;
  /** @var string */
  var $catpath=null;
  /**
  /* @param database A database connector object
  */

  function mosCatgs( &$db ) {
    #$this->mosDBTable( '#__joomgallery_catg', 'cid', $db );  // portierung: original
    parent::__construct( '#__joomgallery_catg', 'cid', $db ); // portierung: neu
  }

  function check() {
    $this->cid = intval( $this->cid );
    return true;
  }
}

class mosComments extends JTable { // portierung: JTable anstatt mosDBTable

  /** @var int Primary key */
  var $cmtid=null;
  /** @var int */
  var $cmtpic=null;
  /** @var string */
  var $cmtip=null;
  /** @var string */
  var $userid=null;
  /** @var string */
  var $cmtname=null;
  /** @var string */
  var $cmttext=null;
  /** @var string */
  var $cmtdate=null;
  /** @var int */
  var $published=null;
  /** @var int */
  var $approved=null;
  
  function mosComments( &$db ) {
    $this->mosDBTable( '#__joomgallery_comments', 'cmtid', $db );
  }
}

class mosNameshields extends JTable { // portierung: JTable anstatt mosDBTable

  /** @var int Primary key */
  var $nid=null;
  /** @var int */
  var $npicid=null;
  /** @var int */
  var $nuserid=null;
  /** @var int */
  var $nxvalue=null;
  /** @var int */
  var $nyvalue=null;
  /** @var string */
  var $nuserip=null;
  /** @var int */
  var $ndate=null;
  /** @var int */
  var $nzindex=null;

  function mosNameshields( &$db ) {
    #$this->mosDBTable( '#__joomgallery_nameshields', 'nid', $db );   // portierung: original
    parent::__construct( '#__joomgallery_nameshields', 'nid', $db );  // portierung: neu
  }
}


class mosGroups extends JTable { // portierung: JTable anstatt mosDBTable

  /** @var int Primary key */
  var $id=null;
  /** @var string */
  var $name=null;
  /** @var string */
  var $description=null;
  /** @var int */
  var $published=null;
  /** @var string */
  var $group=null;
  /** @var string */
  var $user=null;
  /** @var int */
  var $core=null;
  
  function mosGroups( &$db ) {
    #$this->mosDBTable( '#__joomgallery_groups', 'id', $db ); // portierung: original
    parent::__construct('#__joomgallery_groups', 'id', $db ); // portierung: neu
  }
}
?>
