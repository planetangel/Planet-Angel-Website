<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Setup Management
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class wbGallery_setup {

  // ************************************************************************
  function edit(){
    wbGallery_config_html::edit();
  }

  // ************************************************************************
  function save(){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat, $wbGallery_admin;

    // Check Writeable
    if(!is_writable($wbGallery_admin.'/config.php')){
      echo "<script> alert('Permission Denied for config.php'); window.history.go(-1); </script>\n";
      exit();
    }

    // Save Old Configuration
    $bakFile = $wbGallery_admin.'/config_bak.'.date('Y-m-d').'.php';
    if( !file_exists($bakFile) && !copy($wbGallery_admin.'/config.php',$bakFile) ){
      echo "<script> alert('Failed to Create Backup Copy of Configuration $bakFile'); window.history.go(-1); </script>\n";
      exit();
    }

    // Process Configuration Values
    $exLines = Array();
    $exLines[] = '<?php'."\n";
    $exLines[] = 'defined(\'_VALID_MOS\') or die(\'Restricted access\');'."\n";
    $exLines[] = '$WBG_CONFIG = new stdClass();';
    $wbgConfig = mosGetParam($_REQUEST, 'wbgconf');

    // Prepare Output & Count Active Types
    $img_types = 0;
    foreach( $wbgConfig AS $k => $v ){
      $exLines[] = '$WBG_CONFIG->'.$k." = '".addslashes($v)."';";
      if( preg_match('/^save_/',$k) && (int)$v )$img_types++;
    }

    // Write New Configuration
    $fp = fopen($wbGallery_admin.'/config.php','w');
    fwrite($fp, join("\n",$exLines)); fclose($fp);

    // If No Types Active, Flag Error and Force Return...
    if( !$img_types ){
      $subTask = 'error';
      $errorMsg = 'No Image Types where set to Active!\nAt Least (1) Type is Required, or else nothing will be stored when you try and add images!';
    }

    // Redirect
    switch($subTask){
      case 'save':
        mosRedirect('index2.php?option='.$option, 'Configuration Saved Successfully');
        break;
      case 'error':
        echo "<script> alert('Error: $errorMsg'); document.location='index2.php?option=$option&task=setup'; </script>\n";
        exit();
        break;
      default:
        mosRedirect('index2.php?option='.$option.'&task=setup', 'Configuration Saved Successfully');
        break;
    }
  }

}

class wbGallery_config_html {

  // ************************************************************************
  function edit(){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat, $wbGallery_admin;

    mosCommonHTML::loadOverlib();

    ?>
    <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
      <table class="adminheading">
        <tr>
          <th class="config">
            <font size="+1">wbGallery</font><br/>
            Manage Configuration Options<br/>
            <a href="<?= $mainframe->getCfg('live_site') ?>/administrator/components/com_wbgallery/CHANGELOG.txt"
              target="_blank">[ View Change Log ]</a> in New Window
          </th>
        </tr>
      </table>
      <table width="100%" cellspacing="2" cellpadding="0">
        <tr><td valign="top">
          <table class="adminlist" width="100%" cellspacing="2" cellpadding="0">
            <tr>
              <th class="title" colspan="2">Resized Images - Large Format</th>
            </tr>
            <tr>
              <td>Resize Large:</td>
              <td><?= mosHTML::yesnoRadioList('wbgconf[save_large]', '', $WBG_CONFIG->save_large) ?>
                <?= mosToolTip('Should we Save the a Large Resized Image?', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Large Image Path:</td>
              <td><input type="text" name="wbgconf[path_large]" class="text_area" size="30" value="<?= $WBG_CONFIG->path_large; ?>" />
                <?= mosToolTip('Path to Save a the Large Resized Images (with trailing slash)', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Large Width:</td>
              <td><input type="text" name="wbgconf[width_large]" class="text_area" size="30" value="<?= $WBG_CONFIG->width_large; ?>" />
                <?= mosToolTip('Maxiumum Width of the Large Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Large Height:</td>
              <td><input type="text" name="wbgconf[height_large]" class="text_area" size="30" value="<?= $WBG_CONFIG->height_large; ?>" />
                <?= mosToolTip('Maxiumum Height of the Large Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Large Image Quality:</td>
              <td><input type="text" name="wbgconf[quality_large]" class="text_area" size="30" value="<?= $WBG_CONFIG->quality_large; ?>" />
                <?= mosToolTip('This is the Image Quality to use for Large Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <th class="title" colspan="2">Resized Images - Medium Format</th>
            </tr>
            <tr>
              <td>Resize Medium:</td>
              <td><?= mosHTML::yesnoRadioList('wbgconf[save_medium]', '', $WBG_CONFIG->save_medium) ?>
                <?= mosToolTip('Should we Save the a Medium Resized Image?', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Medium Image Path:</td>
              <td><input type="text" name="wbgconf[path_medium]" class="text_area" size="30" value="<?= $WBG_CONFIG->path_medium; ?>" />
                <?= mosToolTip('Path to Save a the Medium Resized Images (with trailing slash)', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Medium Width:</td>
              <td><input type="text" name="wbgconf[width_medium]" class="text_area" size="30" value="<?= $WBG_CONFIG->width_medium; ?>" />
                <?= mosToolTip('Maxiumum Width of the Medium Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Medium Height:</td>
              <td><input type="text" name="wbgconf[height_medium]" class="text_area" size="30" value="<?= $WBG_CONFIG->height_medium; ?>" />
                <?= mosToolTip('Maxiumum Height of the Medium Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Medium Image Quality:</td>
              <td><input type="text" name="wbgconf[quality_medium]" class="text_area" size="30" value="<?= $WBG_CONFIG->quality_medium; ?>" />
                <?= mosToolTip('This is the Image Quality to use for Medium Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <th class="title" colspan="2">Resized Images - Thumbnail Format</th>
            </tr>
            <tr>
              <td>Resize Thumbnail:</td>
              <td><?= mosHTML::yesnoRadioList('wbgconf[save_thumb]', '', $WBG_CONFIG->save_thumb) ?>
                <?= mosToolTip('Should we Save the a Thumbnail Resized Image?', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Thumbnail Image Path:</td>
              <td><input type="text" name="wbgconf[path_thumb]" class="text_area" size="30" value="<?= $WBG_CONFIG->path_thumb; ?>" />
                <?= mosToolTip('Path to Save a the Thumbnail Resized Images (with trailing slash)', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Thumbnail Width:</td>
              <td><input type="text" name="wbgconf[width_thumb]" class="text_area" size="30" value="<?= $WBG_CONFIG->width_thumb; ?>" />
                <?= mosToolTip('Maxiumum Width of the Thumbnail Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Thumbnail Height:</td>
              <td><input type="text" name="wbgconf[height_thumb]" class="text_area" size="30" value="<?= $WBG_CONFIG->height_thumb; ?>" />
                <?= mosToolTip('Maxiumum Height of the Thumbnail Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Thumbnail Image Quality:</td>
              <td><input type="text" name="wbgconf[quality_thumb]" class="text_area" size="30" value="<?= $WBG_CONFIG->quality_thumb; ?>" />
                <?= mosToolTip('This is the Image Quality to use for Thumbnail Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <th class="title" colspan="2">Resized Images - ThumbTack Format</th>
            </tr>
            <tr>
              <td>Resize ThumbTack:</td>
              <td><?= mosHTML::yesnoRadioList('wbgconf[save_tack]', '', $WBG_CONFIG->save_tack) ?>
                <?= mosToolTip('Should we Save the a ThumbTack Resized Image?', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>ThumbTack Image Path:</td>
              <td><input type="text" name="wbgconf[path_tack]" class="text_area" size="30" value="<?= $WBG_CONFIG->path_tack; ?>" />
                <?= mosToolTip('Path to Save a the ThumbTack Resized Images (with trailing slash)', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>ThumbTack Width:</td>
              <td><input type="text" name="wbgconf[width_tack]" class="text_area" size="30" value="<?= $WBG_CONFIG->width_tack; ?>" />
                <?= mosToolTip('Maxiumum Width of the ThumbTack Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>ThumbTack Height:</td>
              <td><input type="text" name="wbgconf[height_tack]" class="text_area" size="30" value="<?= $WBG_CONFIG->height_tack; ?>" />
                <?= mosToolTip('Maxiumum Height of the ThumbTack Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>ThumbTack Image Quality:</td>
              <td><input type="text" name="wbgconf[quality_tack]" class="text_area" size="30" value="<?= $WBG_CONFIG->quality_tack; ?>" />
                <?= mosToolTip('This is the Image Quality to use for ThumbTack Resized Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <th class="title" colspan="2">Original Images</th>
            </tr>
            <tr>
              <td width="200">Save Originals:</td>
              <td><?= mosHTML::yesnoRadioList('wbgconf[save_original]', '', $WBG_CONFIG->save_original) ?>
                <?= mosToolTip('Should we Save the Original Image?', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Images Path:</td>
              <td><input type="text" name="wbgconf[path_original]" class="text_area" size="30" value="<?= $WBG_CONFIG->path_original; ?>" />
                <?= mosToolTip('Path to Save a Copy of the Original Images', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <th class="title" colspan="2">Image Processing Options</th>
            </tr>
            <tr>
              <td>Manage Memory:</td>
              <td><?= mosHTML::yesnoRadioList('wbgconf[use_memManager]', '', $WBG_CONFIG->use_memManager) ?>
                <?= mosToolTip('Should we Attempt to Manage the Server Memory when Processing Images? This feature attempts to allocate additional memory from the server when the image resize process is taking place.', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Use Image Magik:</td>
              <td><?= mosHTML::yesnoRadioList('wbgconf[use_ImageMagik]', '', $WBG_CONFIG->use_ImageMagik) ?>
                <?= mosToolTip('Should we Attempt to use the Image Magik Library for Image Processing? This is a SHELL command that will need to be installed if not already.', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Path to Image Magik:</td>
              <td><input type="text" name="wbgconf[path_ImageMagik]" class="text_area" size="30" value="<?= $WBG_CONFIG->path_ImageMagik; ?>" />
                <?= mosToolTip('This is the PATH (with trailing slash) to the Image Magik script.', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <td>Image Magik Command:</td>
              <td><input type="text" name="wbgconf[file_ImageMagik]" class="text_area" size="30" value="<?= $WBG_CONFIG->file_ImageMagik; ?>" />
                <?= mosToolTip('This is the SCRIPT FILENAME for the Image Magik script, located in the Image Magik Path.', 'Configuration Tip'); ?></td>
            </tr>
            <tr>
              <th class="title" colspan="2">General Operation</th>
            </tr>
            <tr>
              <td>Show Copyright Footer:</td>
              <td><?= mosHTML::yesnoRadioList('wbgconf[show_copyright]', '', $WBG_CONFIG->show_copyright) ?>
                <?= mosToolTip('Will you Spread the Word and Keep the Copyright Footer Active?', 'Configuration Tip'); ?></td>
            </tr>
          </table>
        </td><td valign="top">
          <table class="adminlist" width="100%" cellspacing="2" cellpadding="0">
            <tr>
              <th class="title" colspan="5">wbGallery Menu Items</th>
            </tr>
            <tr>
              <th>#</th>
              <th>Menu</th>
              <th>Type</th>
              <th>Title</th>
              <th>Itemid</th>
            </tr>
            <?php
              $database->setQuery("SELECT * FROM #__menu WHERE `link` LIKE '%$option%'");
              $mRows = $database->loadObjectList();
              $count = 1;
              foreach( $mRows AS $mRow ){
                $menuLink = 'index2.php?option=com_menus&menutype='.$mRow->menutype;
                $itemLink = 'index2.php?option=com_menus&menutype='.$mRow->menutype.'&task=edit&id='.$mRow->id.'&hidemainmenu=1';
                $prevLink = $mainframe->getCfg('live_site').'/'.$mRow->link;
                if( $mRow->type == 'components' ) $prevLink .= '&Itemid='.$mRow->id;
                ?>
                <tr>
                  <td><?= $count++ ?></td>
                  <td><a href="<?= $menuLink ?>" target="_blank" alt="Manage Menu"><?= $mRow->menutype ?></a></td>
                  <td><?= ($mRow->type == 'components' ? 'Control' : 'Reference') ?></td>
                  <td><a href="<?= $itemLink ?>" target="_blank" alt="Edit Menu Item"><?= $mRow->name ?></a></td>
                  <td><?= $mRow->id ?> <a href="<?= $prevLink ?>" target="_blank" alt="Preview">[ show ]</a></td>
                </tr>
                <?php
              }
            ?>
          </table>
        </td></tr>
      </table>
      <input type="hidden" name="option" value="<?= $option ?>" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="hidemainmenu" value="0" />
    </form>
    <?php
  }

}