<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Joomla Toolbar Triggers
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
// Load HTML
require_once( $mainframe->getPath( 'toolbar_html' ) );

// Prepare Runtime
global $task, $priTask, $subTask;

// ************************************************************************
if($priTask == 'image'){
  switch($subTask) {
    case 'upload':
      TOOLBAR_wbgallery::_UPLOAD();
      break;
    case 'edit':
      TOOLBAR_wbgallery::_EDIT();
      break;
    default:
      TOOLBAR_wbgallery::_LIST_IMAGE();
      break;
  }
  return;
}

// ************************************************************************
if($priTask == 'setup'){
  switch($subTask) {
    default:
      TOOLBAR_wbgallery::_EDIT();
      break;
  }
  return;
}

// ************************************************************************
if($priTask == 'home')
  return;

// ************************************************************************
if($priTask == 'support')
  return;

// ************************************************************************
switch($subTask) {
  case 'new':
  case 'edit':
    TOOLBAR_wbgallery::_EDIT( );
    break;
  default:
    TOOLBAR_wbgallery::_LIST();
    break;
}
