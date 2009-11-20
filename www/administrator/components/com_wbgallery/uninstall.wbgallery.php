<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Uninstall Script
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
function com_uninstall(){
  global $mainframe, $database;

  // Remove Menu Items
  $database->setQuery("DELETE FROM #__menu WHERE `link` LIKE '%com_wbgallery%'");
  $database->query();

  // Load the Configuration
  if( file_exists($mainframe->getCfg('absolute_path').'/administrator/components/com_wbgallery/config.php') )
    include($mainframe->getCfg('absolute_path').'/administrator/components/com_wbgallery/config.php');
  else
    return false;

  // Uninstall Message? ... j1.0.x? too bad...
  echo '
    <h1>Thank you for trying our Product!</h1>
    <p>We hope that you have enjoyed the software. If you have experienced problems or can offer suggestions for improvement, please visit
      our community forum. Your feedback is appreciated, and will help us to improve the products we offer to the community.</p>
    <ul>
      <li><a href="http://forum.webuddha.com/" target="_blank">Webuddha Developers Forum</a>
      <li><a href="http://software.webuddha.com/" target="_blank">Webuddha Software Repository</a>
    </ul>
  ';

  // Remove Image Folders
  $remDir = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_large;
  if( is_dir($remDir) && !remove_dir( $remDir ) ) return false;
  $remDir = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_medium;
  if( is_dir($remDir) && !remove_dir( $remDir ) ) return false;
  $remDir = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_thumb;
  if( is_dir($remDir) && !remove_dir( $remDir ) ) return false;
  $remDir = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_tack;
  if( is_dir($remDir) && !remove_dir( $remDir ) ) return false;
  $remDir = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_original;
  if( is_dir($remDir) && !remove_dir( $remDir ) ) return false;

  // Success
  return true;
}

// ************************************************************************
// Recursively Remove a Directory and Contents
function remove_dir($directory,$empty=false,$level=0){
  global $mainframe;
  $directory = preg_replace('/\\\/','/',$directory);
  $basePath  = preg_replace('/\\\/','/',$mainframe->getCfg('absolute_path'));
  echo "Remove: $directory ";
  if( !preg_match('/'.preg_replace('/\//','\/',$basePath).'/',$directory) ){
    echo " - Cannot Delete from Below User Path <br/>";
    return false;
  }
  if( $level > 5 )die('Depth Redundancy Fail');
  if(substr($directory,-1) == '/')
    $directory = substr($directory,0,-1);
  if(!file_exists($directory) || !is_dir($directory)){
    echo " - Folder Does Not Exist <br/>";
    return false;
  } elseif(is_readable($directory)) {
    $handle = opendir($directory);
    while(false !== ($item = readdir($handle))){
      if($item != '.' && $item != '..'){
        $path = $directory.'/'.$item;
        if(is_dir($path)){
          remove_dir($path,false,$level+1);
        }else{
          unlink($path);
        }
      }
    }
    closedir($handle);
    if($empty == false){
      if(!rmdir($directory)){
        echo " - Failed<br/>";
        return false;
      }
    }
  }
  echo " - Success<br/>";
  return true;
}
