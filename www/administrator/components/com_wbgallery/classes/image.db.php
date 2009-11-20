<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Image Data Class
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class wbGalleryDB_img extends mosDBTable {

  var $id = null;
  var $cat_id = null;
  var $file = null;
  var $name = null;
  var $sku = null;
  var $price = null;
  var $photographer = null;
  var $description = null;
  var $path = null;
  var $width = null;
  var $height = null;
  var $size = null;
  var $ordering = null;
  var $published = null;
  var $created = null;
  var $modified = null;
  var $hits = null;
  var $featured = null;

  // Initialize
  function wbGalleryDB_img(&$db){
    $this->mosDBTable('#__wbgallery_img', 'id', $db);
    $this->created    = date('Y-m-d H:i:s');
    $this->modified   = date('Y-m-d H:i:s');
    $this->published  = 1;
    $this->ordering   = 0;
  }

  // Check Valid Input
  function check(){
    // Trim the Values
    $this->name = trim($this->name);
    $this->sku = trim($this->sku);
    $this->price = trim($this->price);
    $this->photographer = trim($this->photographer);
    // Check Required
    if( !strlen($this->name) ){
      $this->_error = 'Invalid Name';
      return false;
    }
    // Good to Go
    return true;
  }

  // Track a Hit
  function hit(){
    if( $this->id ){
      $this->_db->setQuery("UPDATE ".$this->_tbl." SET `hits` = `hits` + 1 WHERE id = '".$this->id."'");
      $this->_db->query();
    }
  }

  // Get Previous / Next Items
  function getNeighbors(){
    $neighbors = new stdClass();
    if( !$this->id )
      return $neighbors;
    $this->_db->setQuery("
      SELECT prev.id AS prev_id, prev.name AS prev_name
        , next.id AS next_id, next.name AS next_name
      FROM #__wbgallery_img AS i
      LEFT JOIN #__wbgallery_img AS prev ON (prev.cat_id = i.cat_id AND prev.ordering < i.ordering)
      LEFT JOIN #__wbgallery_img AS next ON (next.cat_id = i.cat_id AND next.ordering > i.ordering)
      WHERE i.cat_id = ".(int)$this->cat_id."
        AND i.id = ".(int)$this->id."
        AND i.published = 1
      ORDER BY prev.ordering DESC, next.ordering ASC
      LIMIT 1
      ");
    // $this->_db->loadObject($neighbors);
    $neighbors = $this->_db->loadObjectList();
    echo $this->_db->getErrorMsg();
    return $neighbors[0];
  }

  // Get Image Size Path
  function getImagePath( $size ){
    global $mainframe, $WBG_CONFIG;
    $imgPath = null;
    switch( $size ){
      case 'original':  $imgPath = $mainframe->getCfg('live_site').$WBG_CONFIG->path_original; break;
      case 'large':     $imgPath = $mainframe->getCfg('live_site').$WBG_CONFIG->path_large; break;
      case 'medium':    $imgPath = $mainframe->getCfg('live_site').$WBG_CONFIG->path_medium; break;
      case 'tack':      $imgPath = $mainframe->getCfg('live_site').$WBG_CONFIG->path_tack; break;
      case 'thumb':     $imgPath = $mainframe->getCfg('live_site').$WBG_CONFIG->path_thumb; break;
    }
    return $imgPath;
  }

}

