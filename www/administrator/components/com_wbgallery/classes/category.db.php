<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Category Data Class
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class wbGalleryDB_cat extends mosDBTable {

  var $id = null;
  var $parent_id = null;
  var $file = null;
  var $title = null;
  var $name = null;
  var $description = null;
  var $published = null;
  var $checked_out = null;
  var $checked_out_time = null;
  var $editor = null;
  var $ordering = null;
  var $access = null;
  var $params = null;

  // For Recursion
  var $_name      = null; // True Name
  var $_level     = null; // Level
  var $_images    = null; // # Images
  var $_children  = null; // # Categories

  // Initialize
  function wbGalleryDB_cat( &$db ) {
    $this->mosDBTable( '#__wbgallery_cat', 'id', $db );
  }

  // Check
  function check() {
    $ignoreList = Array('description');
    $this->filter($ignoreList);
    if (trim( $this->title ) == '') {
      $this->_error = "Your Category must contain a title.";
      return false;
    }
    if (trim( $this->name ) == '') {
      $this->_error = "Your Category must have a name.";
      return false;
    }
    // Check Duplicates Name / Parent
    $query = "SELECT id"
      . "\n FROM #__categories "
      . "\n WHERE name = " . $this->_db->Quote( $this->name )
      . "\n AND section = " . $this->_db->Quote( $this->section )
      . "\n AND parent_id = " . $this->_db->Quote( $this->parent_id )
      ;
    $this->_db->setQuery( $query );
    $xid = intval( $this->_db->loadResult() );
    if ($xid && $xid != intval( $this->id )) {
      $this->_error = "There is Already a Category with that Name";
      return false;
    }
    return true;
  }

  // ************************************************************************
  function getCategoryTree( $published=false, $img_count=true, $sub_count=false ){
    global $mainframe, $database;

    // Load Records
    // ( $img_count ? ", CONCAT(c.name,' ( ',COUNT(DISTINCT i.id),' )') AS name" : ', c.name' )
    $query = "
      SELECT c.id
        , c.parent_id
        , c.name AS name
        , c.name AS _name
        ".( $img_count ? ', COUNT(DISTINCT i.id) AS _images' : '' )."
        ".( $sub_count ? ', COUNT(DISTINCT sc.id) AS _children' : '' )."
      FROM #__wbgallery_cat AS c
      ".( $img_count ? 'LEFT JOIN #__wbgallery_img AS i ON (i.cat_id = c.id AND i.published = 1)' : '')."
      ".( $sub_count ? 'LEFT JOIN #__wbgallery_cat AS sc ON (sc.parent_id = c.id AND sc.published = 1)' : '')."
      ".( $published ? "WHERE c.published = 1" : '' )."
      GROUP BY c.id
      ORDER BY c.parent_id, c.ordering
      ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    echo $database->getErrorMsg();

    // Build Parent List
    $level=0;$parents=Array();
    for($i=0;$i<count($rows);$i++){
      $rows[$i]->name .= ' ('.$rows[$i]->_images.')';
      $parents[$rows[$i]->parent_id][] = $rows[$i];
    }

    // Build Final Tree
    $level = 0;
    $catTree=Array();
    if(count($parents) && count($parents[0]))
      foreach($parents[0] AS $row)
        $this->recurseCategory($parents, $catTree, $row, $level);

    return $catTree;
  }

  // ************************************************************************
  function recurseCategory($rows, &$tree, $row, $level){
    $name = '';
    for($i=1;$i<$level;$i++)
      $name .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    if($level != 0)
      $name .= '|__';
    $row->name    = $name.' '.$row->name;
    $row->_level  = $level;
    $tree[] = $row;
    if(count($rows[$row->id]))
      foreach( $rows[$row->id] AS $child )
        $this->recurseCategory($rows, $tree, $child, $level + 1);
  }

  // ************************************************************************
  function getSubCatImg($cat_id){
    global $database, $option, $my, $mainframe;

    $database->setQuery("
      SELECT i.id AS img_id
        , i.file AS img_file
        , i.name AS img_name
      FROM #__wbgallery_img AS i
      WHERE i.cat_id = ".(int)$cat_id."
        AND i.published = 1
      ORDER BY i.ordering ASC
      LIMIT 1
    ");
    $database->loadObject($row);
    echo $database->getErrorMsg();

    if( !$row->img_id ){
      $database->setQuery("
        SELECT c.id
        FROM #__wbgallery_cat AS c
        WHERE c.parent_id = ".(int)$cat_id."
          AND c.published = 1
          AND c.access <= ".(int)$my->gid."
        GROUP BY c.id
        ORDER BY c.ordering ASC
        ");
      $subcats = $database->loadObjectList();
      echo $database->getErrorMsg();
      foreach( $subcats AS $subcat ){
        if( $subcat->img_id )
          return $subcat;
        $row = $this->getSubCatImg( $subcat->id );
        if( $row->img_id )
          return $row;
      }
    }

    return $row;
  }

}
