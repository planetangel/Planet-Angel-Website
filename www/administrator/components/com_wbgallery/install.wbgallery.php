<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Installation Script
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
function com_install(){
  global $mainframe, $database;

  // Create the First Menu Item
  $database->setQuery("SELECT ordering FROM #__components WHERE `option`='com_wbgallery' AND `parent`!='0' ORDER BY ordering ASC LIMIT 0, 1");
  $ordering = (int)$database->loadResult();

  // Update Component Icons
  if( defined('_JEXEC') ){
    // Joomla v1.5.x
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/component.png' WHERE `option`='com_wbgallery' AND `parent`='0'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/media.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/install.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/category.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/config.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/help.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
  } else {
    // Joomla v1.0.x
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/home.png' WHERE `option`='com_wbgallery' AND `parent`='0'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/media.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/globe2.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/categories.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/config.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
    $database->setQuery("UPDATE #__components SET `admin_menu_img`='js/ThemeOffice/help.png' WHERE `option`='com_wbgallery' AND `parent`!='0' AND `ordering`='".$ordering++."'");
    $database->query();
  }

  // Create the First Menu Item
  $database->setQuery("SELECT id FROM #__components WHERE `option`='com_wbgallery' AND `parent`='0'");
  $componentid = (int)$database->loadResult();
  if( $componentid ){
    $database->setQuery("SELECT ordering FROM #__menu WHERE `menutype`='mainmenu' ORDER BY `ordering` DESC LIMIT 0, 1");
    $ordering = (int)$database->loadResult();
    $ordering++;
    if( defined('_JEXEC') ){
      // Joomla v1.5.x
      $database->setQuery("
        INSERT INTO `#__menu` (
          `id`,`menutype`,`name`,`alias`,`link`,`type`,`published`,`parent`,`componentid`,`sublevel`,`ordering`,`checked_out`,
          `checked_out_time`,`pollid`,`browserNav`,`access`,`utaccess`,`params`,`lft`,`rgt`,`home`) VALUES (
          NULL,'mainmenu','wbGallery','wbgallery','index.php?option=com_wbgallery','component',0,0,$componentid,0,$ordering,0,'0000-00-00 00:00:00',0,0,0,0,
          'cid=\ntarget_itemid=\ncss=default.css\npage_title=\nshow_title=1\nlist_limit=9\nshow_pagenav=both\ncat_image=1\nuse_cat_img=1\ncat_size=thumb\ncat_cols=3\ncat_name=1\ncat_desc=0\nimg_image=1\nimg_size=thumb\nimg_cols=3\nimg_name=1\nimg_name_link=view\nimg_desc=1\nimg_sku=0\nimg_price=0\nimg_photog=0\nshow_lightbox=1\nlightbox_img_size=large\nview_img_size=large\nview_img_desc=1\nview_goback=1\nview_continue=1\nview_neighbors=1\nview_related=1\nrelated_img_size=thumb\nrelated_list_limit=15\nrelated_img_cols=3\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n',0,0,0
          );
        ");
    } else {
      // Joomla v1.0.x
      $database->setQuery("
        INSERT INTO `#__menu` (
          `id`,`menutype`,`name`,`link`,`type`,`published`,`parent`,`componentid`,`sublevel`,`ordering`,`checked_out`,`checked_out_time`,
          `pollid`,`browserNav`,`access`,`utaccess`,`params`) VALUES (
          NULL,'mainmenu','wbGallery','index.php?option=com_wbgallery','components',0,0,$componentid,0,$ordering,0,'0000-00-00 00:00:00',0,0,0,0,
          'cid=\ntarget_itemid=\ncss=default.css\npage_title=\nshow_title=1\nlist_limit=15\nshow_pagenav=both\ncat_image=1\nuse_cat_img=1\ncat_size=thumb\ncat_cols=3\ncat_name=1\ncat_desc=0\nimg_image=1\nimg_size=thumb\nimg_cols=3\nimg_name=1\nimg_name_link=view\nimg_desc=0\nimg_price=0\nimg_photog=0\nshow_lightbox=1\nlightbox_img_size=large\nview_img_size=large\nview_img_desc=1\nview_goback=1\nview_continue=1\nview_neighbors=1\nview_related=1\nrelated_img_size=thumb\nrelated_list_limit=15\nrelated_img_cols=3'
          );
        ");
    }
    $database->query();

    // Find New Menu Item
    $database->setQuery("SELECT id FROM #__menu WHERE `link` LIKE '%com_wbgallery%'");
    $menuid = (int)$database->loadResult();
  }

  // Load the Cofiguration
  if( file_exists($mainframe->getCfg('absolute_path').'/administrator/components/com_wbgallery/config.php') )
    include($mainframe->getCfg('absolute_path').'/administrator/components/com_wbgallery/config.php');
  else
    return false;

  // Create Folders
  $paths = Array(
    $WBG_CONFIG->path_large,
    $WBG_CONFIG->path_medium,
    $WBG_CONFIG->path_thumb,
    $WBG_CONFIG->path_tack,
    $WBG_CONFIG->path_original
  );

  $outHtml = '
    <h1>Welcome to the wbGallery Image Gallery by Webuddha.com</h1>
    <p>The wbGallery is a powerful, yet simple to manage Image Gallery that can be easily customized using CSS and the Component options provided with each Menu item created.</p>
    <p>The following folders should have write permissions - CHMOD 777 on most Linux based systems.</p>
    <table border="0" cellpadding="0" cellspacing="0">
    ';
  foreach( $paths AS $dir ){
    $outHtml .= '<tr><td><b>'.$dir.'</b></td><td><b>';
    $outHtml .= (!file_exists($mainframe->getCfg('absolute_path').$dir) && !create_dir($dir)
      ? '<font color="red">Failed to Create Folder</font>'
      : '<font color="green">Folder Created</font>').' - ';
    $outHtml .= (is_writable($mainframe->getCfg('absolute_path').$dir)
      ? '<font color="green">Is Writeable</font>'
      : '<font color="red">Is NOT Writeable</font>');
    $outHtml .= '</b></td></tr>';
  }
  $outHtml .= '
    </table>
    <p>Thank you for taking the time to install and review our application. If you like this component, take a few minutes and check out our collection of software available at <a href="http://software.webuddha.com/" target="_blank" alt="Load Webuddha in a New Window">Webuddha Software</a>.</p>
    <p><a href="index2.php?option=com_wbgallery&task=home">Click Here</a> to Get Started with wbGallery.</p>
    <p><a href="index2.php?option=com_wbgallery&task=image.upload&hidemainmenu=1">Click Here</a> to Upload your First Images.</p>
    <p><a href="index2.php?option=com_wbgallery&task=category&hidemainmenu=1">Click Here</a> to setup your first Gallery Category.</p>
    ';
  if( $menuid )
    $outHtml .= '
      <p><font color="red" size="+1">* IMPORTANT NOTICE *</font><br/>
        A menu item has been created in the MainMenu - it is currently NOT published. Make sure when you add new menu items, to first APPLY, then SAVE to ensure that the parameter list is stored.</p>
      <p><a href="index2.php?option=com_menus&menutype=mainmenu&task=edit&id='.$menuid.'&hidemainmenu=1">Click Here</a> to edit the new wbGallery Menu Item.</p>
      ';
  else
    $outHtml .= '
      <p><font color="red" size="+1">* IMPORTANT NOTICE *</font><br/>
        Make sure when you add new menu items, to first APPLY, then SAVE to ensure that the parameter list is stored.</p>
      ';

  if( defined('_JEXEC') ){
    echo $outHtml;
    return true;
  } else
   return $outHtml;
}

// ************************************************************************
// Recursively Create Directories and CHMOD for Use
function create_dir( $directory ){
  global $mainframe;
  $path = $mainframe->getCfg('absolute_path');
  $dirs = split('/',$directory);
  foreach($dirs AS $dir){
    $path .= '/'.$dir;
    if(!is_dir($path)){
      if(!mkdir($path, 0755))
        return false;
      if(!is_writable($path))
        mosChmod($path, 0755);
    }
  }
  return true;
}
