<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// System Loader (admin / public)
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************

// Register Primary Globals
// ************************************************************************

  global $wbGallery_admin, $wbGallery_public, $wbGallery_product, $wbGallery_version, $wbGallery_sourceurl;
  $wbGallery_admin      = $mainframe->getCfg('absolute_path').'/administrator/components/com_wbgallery/';
  $wbGallery_public     = $mainframe->getCfg('absolute_path').'/components/com_wbgallery/';
  $wbGallery_product    = 'wbGallery Image Collection Gallery';
  $wbGallery_version    = '1.0.3';
  $wbGallery_sourceurl  = '<a href="http://software.webuddha.com/" alt="wbGallery by Webuddha.com" target="_blank">Webuddha.com</a>';

// Load Config
// ************************************************************************

  global $WBG_CONFIG;
  require_once($wbGallery_admin.'config.php');

// Load Language Classes
// ************************************************************************

  global $WBG_LANG;
  require_once($wbGallery_admin.'includes_public/language.php');
  $WBG_LANG = new wbGallery_lang( $mainframe->getCfg('lang') );

// Load Data Classes
// ************************************************************************

  global $wbGalleryDB_cat;
  require_once($wbGallery_admin.'classes/category.db.php');
  $wbGalleryDB_cat = new wbGalleryDB_cat( $database );

  global $wbGalleryDB_img;
  require_once($wbGallery_admin.'classes/image.db.php');
  $wbGalleryDB_img = new wbGalleryDB_img( $database );

// Load Operation Classes
// ************************************************************************

  global $wbGallery_common;
  require_once($wbGallery_admin.'includes_public/common.php');
  $wbGallery_common = new wbGallery_common();

  // ADMINISTRATOR TRIGGER
  if( WBGALLERY_ADMIN == 1 ){

    global $wbGallery_home, $wbGallery_cat, $wbGallery_img
      , $wbGallery_eng, $wbGallery_setup;

    require_once($wbGallery_admin.'includes/home.php');
    require_once($wbGallery_admin.'includes/category.php');
    require_once($wbGallery_admin.'includes/image.php');
    require_once($wbGallery_admin.'includes/setup.php');
    require_once($wbGallery_admin.'includes/image_eng.php');

    $wbGallery_home   = new wbGallery_home();
    $wbGallery_cat    = new wbGallery_cat();
    $wbGallery_img    = new wbGallery_img();
    $wbGallery_eng    = new wbGallery_img_eng();
    $wbGallery_setup  = new wbGallery_setup();

  }

  // PUBLIC DEFAULT
  else {

    global $wbGallery_cat, $wbGallery_img;

    require_once($wbGallery_admin.'includes_public/category.php');
    require_once($wbGallery_admin.'includes_public/image.php');

    $wbGallery_cat    = new wbGallery_cat();
    $wbGallery_img    = new wbGallery_img();

  }
