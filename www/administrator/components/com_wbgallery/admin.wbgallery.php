<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Admin Kernel
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************

// Prepare
global $option, $mainframe, $my;
if( !$option || !$mainframe || !$my )
  die('Required Globals Not Available');

// Load Gallery Objects
define('WBGALLERY_ADMIN',1);
include( $mainframe->getCfg('absolute_path').'/administrator/components/com_wbgallery/load.php');

// Prepare Runtime
global $task, $priTask, $subTask, $cid, $id;
$task = mosGetParam($_REQUEST, 'task', 'home');
$cid  = mosGetParam($_REQUEST, 'cid', null);
$id   = mosGetParam($_REQUEST, 'id', 0);

// Prepare subTask
$priTask  = preg_replace('/^(\w+)\.(\w+)$/','$1',$task);
$subTask  = preg_replace('/^(\w+)\.(\w+)$/','$2',$task);
if( $priTask == $subTask )
  $subTask = null;

// Prepare cid
if(!is_array($cid) && (int)$cid)
  $cid = array($cid);
elseif(!is_array($cid) && (int)$id)
  $cid = array($id);
elseif(!is_array($cid))
  $cid = array();

// Header
$no_html = mosGetParam($_REQUEST,'no_html',0);
if( !$no_html ){
  echo '<div class="wbgWrap">';
  echo '<link rel="stylesheet" href="components/'.$option.'/css/default.admin.css" type="text/css" />';
}

switch($priTask){

  // ==================================
  // Images
  // ==================================
  case 'image':
    switch( $subTask ){
      case 'upload':
        $wbGallery_img->upload();
        break;
      case 'upload_save':
        $wbGallery_img->upload_save();
        break;
      case 'edit':
        $wbGallery_img->edit($cid[0]);
        break;
      case 'save':
      case 'apply':
      case 'rename':
        $wbGallery_img->save();
        break;
      case 'remove':
        $wbGallery_img->remove($cid);
        break;
      case 'move':
        $wbGallery_img->move($cid);
        break;
      case 'orderup':
        $wbGallery_img->reorder($cid[0], -1);
        break;
      case 'orderdown':
        $wbGallery_img->reorder($cid[0], 1);
        break;
      case 'order':
        $wbGallery_img->order($cid);
        break;
      case 'publish':
        $wbGallery_img->publish($cid, 1);
        break;
      case 'unpublish':
        $wbGallery_img->publish($cid, 0);
        break;
      case 'feature':
        $wbGallery_img->featured($cid, 1);
        break;
      case 'unfeature':
        $wbGallery_img->featured($cid, 0);
        break;
      default:
      case 'cancel':
        $wbGallery_img->manage();
        break;
    }
    break;

  // ==================================
  // Categories
  // ==================================
  case 'category':
    switch( $subTask ){
      case 'new':
      case 'edit':
        $wbGallery_cat->edit($cid[0]);
        break;
      case 'save':
      case 'apply':
        $wbGallery_cat->save();
        break;
      case 'remove':
        $wbGallery_cat->remove($cid);
        break;
      case 'orderup':
        $wbGallery_cat->reorder($cid[0], -1);
        break;
      case 'orderdown':
        $wbGallery_cat->reorder($cid[0], 1);
        break;
      case 'order':
        $wbGallery_cat->order($cid);
        break;
      case 'publish':
        $wbGallery_cat->publish($cid, 1);
        break;
      case 'unpublish':
        $wbGallery_cat->publish($cid, 0);
        break;
      default:
      case 'cancel':
        $wbGallery_cat->manage();
        break;
    }
    break;

  // ==================================
  // Config
  // ==================================
  case 'setup':
    switch( $subTask ){
      case 'save':
      case 'apply':
        $wbGallery_setup->save();
        break;
      default:
      case 'cancel':
        $wbGallery_setup->edit();
        break;
    }
    break;

  // ==================================
  // Default
  // ==================================
  default:
  case 'support':
    $wbGallery_home->manage();
    // die('No Task!');
    break;

}

// Copyright Notice
// This can be disabled in the Setup Area
if( $WBG_CONFIG->show_copyright )
  $wbGallery_common->showCopyright();

// Header
if( !$no_html ) echo '</div>';
