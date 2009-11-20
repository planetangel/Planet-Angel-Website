<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Public Kernel
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************

// Prepare
global $option, $mainframe, $my, $Itemid;
if( !$option || !$mainframe || !$my )
  die('Required Globals Not Available');

// Load Gallery Objects
include( $mainframe->getCfg('absolute_path').'/administrator/components/com_wbgallery/load.php');

// Load menu parameters
$menu = new mosMenu( $database );
$menu->load( $Itemid );
$params = new mosParameters( $menu->params );

// Store Menu Params
global $wbGallery_menuParamsCache;
$wbGallery_menuParamsCache =& $params;

// Prepare Runtime
global $task, $priTask, $subTask, $cid, $id;
$task = mosGetParam($_REQUEST, 'task', '');
$cid  = mosGetParam($_REQUEST, 'cid', 0);
$id   = mosGetParam($_REQUEST, 'id', 0);

// Prepare wbgItemid
global $wbgItemid;
$wbgItemid = (int)$params->get('target_itemid');
if( !$wbgItemid ) $wbgItemid = $Itemid;

// Prepare Category ID
if( !$task )
  if( $id )
    $task = 'image';
  elseif( $cid )
    $task = 'category';
  else
    $task = 'category';

// Prepare subTask
$priTask  = preg_replace('/^(\w+)\.(\w+)$/','$1',$task);
$subTask  = preg_replace('/^(\w+)\.(\w+)$/','$2',$task);
if( $priTask == $subTask )
  $subTask = null;

// Header
$no_html = mosGetParam($_REQUEST,'no_html',0);
if( !$no_html )echo '<div class="wbgWrap">';

switch($priTask){

  // ==================================
  // Images
  // ==================================
  case 'image':
    switch( $subTask ){
      default:
        $wbGallery_img->view( $id, $params );
        break;
    }
    break;

  // ==================================
  // Categories
  // ==================================
  case 'category':
    switch( $subTask ){
      default:
        $wbGallery_cat->view( $cid, $params );
        break;
    }
    break;

  // ==================================
  // Default
  // ==================================
  default:
    die('No Task!');
    break;

}

// Copyright Notice
// This can be disabled in the Setup Area
if( $WBG_CONFIG->show_copyright )
  $wbGallery_common->showCopyright();

// Footer
if( !$no_html )echo '</div>';
