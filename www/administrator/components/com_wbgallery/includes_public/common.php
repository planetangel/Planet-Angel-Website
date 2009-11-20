<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Common Functions
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class wbGallery_common {

  // ************************************************************************
  // Pathway
  function pathway(&$row,$level=0){
    global $mainframe, $database, $option, $wbgItemid;
    global $wbGalleryDB_cat, $wbGallery_menuParamsCache, $WBG_LANG;
    if( !is_object($row) || !$row->id ){
      if( is_object($wbGallery_menuParamsCache) )
        $page_title = $wbGallery_menuParamsCache->get('wbg_title');
      if( !strlen($page_title) )
        $page_title = $WBG_LANG->_('VIEW_CATEGORY_GEN');
      $mainframe->appendPathWay($page_title,'index.php?option='.$option.'&Itemid='.$wbgItemid);
      return;
    }
    $p_row = new wbGalleryDB_cat($database);
    if( $row->cat_id )
      $p_row->load( $row->cat_id );
    elseif( $row->parent_id )
      $p_row->load( $row->parent_id );
    if( $p_row->id )
      $this->pathway($p_row,$level+1);
    if( defined('_JEXEC') ){
      if($row->cat_id) // is image
        $mainframe->appendPathWay($row->name,sefRelToAbs('index.php?option='.$option.'&id='.$row->id.'&Itemid='.$wbgItemid));
      else // is category
        $mainframe->appendPathWay($row->name,sefRelToAbs('index.php?option='.$option.'&cid='.$row->id.'&Itemid='.$wbgItemid));
    }elseif( $level )
      $mainframe->appendPathWay('<a href="'.sefRelToAbs('index.php?option='.$option.'&cid='
        .$row->id.'&Itemid='.$wbgItemid).'">'.$row->name.'</a>');
    else
      $mainframe->appendPathWay($row->name);
  }

  // ************************************************************************
  // Format the Price
  function formatPrice( $price, $sep=',', $dec='.' ){
    if( round($price,2) > 0 )
      return number_format($price, 2, $dec, $sep);
    else {
      global $WBG_LANG;
      return $WBG_LANG->_('PRICE_NA');
    }
  }

  // ************************************************************************
  // Find All the Files in a Folder Recursively
  function find_files($path,$regex='/.*/',$recurse=true){
    $path = preg_replace('/\/$/','',$path);
    $fileList = Array();
    if( file_exists($path) ){
      $dir = opendir($path);
      while (false !== ($file = readdir($dir))){
        $filePath = $path.'/'.$file;
        if(is_dir($filePath)){
          if( $recurse && !in_array($file,Array('.','..')) )
            $fileList = array_merge($fileList,$this->find_files($filePath,$regex));
        } elseif(preg_match($regex,$file)) {
          $fileList[] = Array(
            'name' => $file,
            'path' => $path,
            'size' => filesize($filePath),
            'type' => strtolower(preg_replace('/^.*\.(\w+)$/','$1',$file))
            );
        }
      }
      closedir($dir);
    }
    return $fileList;
  }

  // ************************************************************************
  // Recursively Remove a Directory and Contents
  function remove_dir($directory,$empty=false,$level=0){
    global $mainframe;
    if( !preg_match('/'.preg_replace('/\//','\/',($mainframe->getCfg('absolute_path'))).'/',$directory) ){
      echo "Cannot Delete from Below User Path - $directory<br/>";
      return false;
    }
    if( $level > 5 )die('Depth Redundancy Fail');
    if(substr($directory,-1) == '/')
      $directory = substr($directory,0,-1);
    if(!file_exists($directory) || !is_dir($directory)){
      return false;
    } elseif(is_readable($directory)) {
      $handle = opendir($directory);
      while(false !== ($item = readdir($handle))){
        if($item != '.' && $item != '..'){
          $path = $directory.'/'.$item;
          if(is_dir($path)){
            $this->remove_dir($path,false,$level+1);
          }else{
            unlink($path);
          }
        }
      }
      closedir($handle);
      if($empty == false){
        if(!rmdir($directory)){
          return false;
        }
      }
    }
    return true;
  }

  // ************************************************************************
  // Copyright Statement
  // This can be disabled in the Setup Area
  function showCopyright(){
    global $wbGallery_product, $wbGallery_version, $wbGallery_sourceurl;
    echo '<div class="copyright">Powered by '.$wbGallery_product.' v'.$wbGallery_version.', '.$wbGallery_sourceurl.'</div>';
  }

}