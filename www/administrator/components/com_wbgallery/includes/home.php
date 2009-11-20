<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Category Management
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class wbGallery_home {

  // ************************************************************************
  function manage(){
    wbGallery_home_html::manage();
  }

}

class wbGallery_home_html {

  // ************************************************************************
  function manage(){
    global $mainframe, $option, $WBG_CONFIG, $task;

    if( $task == 'support' ){
      ?><script>
        if( confirm('We are about to launch a new window connecting you to the Webuddha Developers Forum') )
          window.open('http://forum.webuddha.com/','wbForum');
      </script><?php
    }

    ?>
      <style>
        div.wbHome {
          text-align:left;
        }
        div.wbHome p {
          font-size:10pt;
        }
        div.wbHome ul {
          margin:5px 0;
          padding:0 0 0 20px;
        }
        div.wbHome ul ul {
          padding:0 0 0 10px;
        }
        div.wbToolBar div.icon {
          float:left;
          margin:5px;
          width:250px;
        }
        div.wbToolBar div.icon a {
          padding:10px;
          display:block;
          background:#efefef;
          border:1px solid #999;
        }
        div.wbToolBar div.icon img {
          float:left;
          margin:0 10px 0 0;
          border:0px;
        }
        div.wbToolBar div.icon h2 {
          font-size:18px;
          margin:0;padding:0;
          line-height:48px;
          text-decoration:none;
        }
        div.wbHome div.features {
          float:right;
          width:320px;
          padding:5px 10px;
          border:1px dashed #ccc;
          background:#f6f6f6;
          margin:0 0 0 15px;
        }
        div.wbHome div.features ul {
          padding:0 0 0 20px;
          margin:0;
        }
        div.wbHome a {
          text-decoration:none;
        }
      </style>
      <div class="wbHome">
        <div class="features">
          <h3>Resources:</h3>
          <ul>
            <li><a href="http://forum.webuddha.com/" target="_blank">Webuddha Developers Forum</a>
            <li><a href="http://software.webuddha.com/" target="_blank">Webuddha Software Repository</a>
          </ul>
          <h3>Folder Permission:</h3>
          <?php
            $paths = Array(
              $WBG_CONFIG->path_large,
              $WBG_CONFIG->path_medium,
              $WBG_CONFIG->path_thumb,
              $WBG_CONFIG->path_tack,
              $WBG_CONFIG->path_original
            );
            echo '<table border="0" cellpadding="2" cellspacing="0">';
            foreach( $paths AS $dir ){
              echo '<tr><td><b>'.$dir.'</b></td><td><b>';
              echo (is_writable($mainframe->getCfg('absolute_path').$dir)
                ? '<font color="green">Is Writeable</font>'
                : '<font color="red">Is NOT Writeable</font>');
              echo '</b></td></tr>';
            }
            echo '</table><br/>';
          ?>
          <h3>wbGallery System Highlights:</h3>
          <ul>
            <li>Image Lightbox
            <li>Image Details Page
            <li>Related Images on Detail
            <li>Next & Back Browsing
            <li>5 Image Sizes - Original, Large, Medium, Thumbnail, Thumbtack (all optional and customizable)
            <li>Multi-Level Category Strucure
            <li>Clean Image Folder Structure, Dated, and Obfuscated Naming Convention (ie: n89rhj9348jd8i9hjed.jpg)
            <li>Category Children Counters simplifying Management
            <li>Drag & Drop, Click to Rename, and Move to Category Features
            <li>Optional SKU, PRICE, PHOTOGRAPHER, and DESCRIPTION fields for each image
            <li>Two Image Administration Formats for Listing Preference
            <li>Upload Single Images, .ZIP or .GZ Image Collections, or Recursive Folder Scanning from a Local Path
            <li>Image Tag Embedding with Span Backgrounds to Minimize Right-Clicking to Save and allow for Forced CSS Image Cropping
            <li>Unique Category Image Specification
            <li>Automatic Category Image Discovery into Children Categories
            <li>Very Clean & Fast MySQL Queries - We are Critical!
            <li>Well Organized Code Base, Documented, and Object Class Oriented
            <li>Loading Kernel with Global Class References so you can Easily include wbGallery Features in your Apps!
            <li>Standard PHP Resizing or Integration with ImageMagick ( recommended for processing speed )
            <li>Referencing of wgGallery Menus within the Configuration Area
            <li>Tested in IE7, Firefox, and Safari - Firefox is Best
          </ul>
          <p>For updates, please visit the home of the Webuddha wbGallery at <a href="http://wbgallery.webuddha.com/" target="_blank">http://wbgallery.webuddha.com/</a>
        </div>
        <h1>Welcome to the wbGallery Image Collection Gallery by Webuddha.com</h1>
        <div class="wbToolBar">
          <div class="icon">
            <a href="index2.php?option=<?= $option ?>&task=image&hidemainmenu=1">
            <img src="<?= $mainframe->getCfg('live_site') ?>/administrator/components/<?= $option ?>/images/icon_media.png" />
            <h2>Manage Images</h2>
            <div style="clear:both;"></div>
            </a>
          </div>
          <div class="icon">
            <a href="index2.php?option=<?= $option ?>&task=image.upload">
            <img src="<?= $mainframe->getCfg('live_site') ?>/administrator/components/<?= $option ?>/images/icon_upload.png" />
            <h2>Upload Images</h2>
            <div style="clear:both;"></div></a>
          </div>
          <div class="icon">
            <a href="index2.php?option=<?= $option ?>&task=category">
            <img src="<?= $mainframe->getCfg('live_site') ?>/administrator/components/<?= $option ?>/images/icon_category.png" />
            <h2>Manage Categories</h2>
            <div style="clear:both;"></div></a>
          </div>
          <div class="icon">
            <a href="index2.php?option=<?= $option ?>&task=setup">
            <img src="<?= $mainframe->getCfg('live_site') ?>/administrator/components/<?= $option ?>/images/icon_config.png" />
            <h2>Edit Configuration</h2>
            <div style="clear:both;"></div></a>
          </div>
        </div>
        <h1 style="clear:left;padding:10px 0 0 0;">About the wbGallery Component</h1>
        <p>The wbGallery by Webuddha.com is a clean and efficient Image Gallery that provides a unique system for managing your images
        using Drag-and-Drop and On-the-Fly Naming Scripts built on the reliable Prototype, Mootools, and Scriptaculous frameworks.</p>
        <p>The wbGallery was built originally for Joomla! v1.0.x to give our clients a simple way to manage images.
        We found that the galleries provided were either too basic, too complicated, or some arrangement of code and licensing issues.</p>
        <p>Other issues with many components we find for Joomla is the output code format. While we would have liked to include a
        full "template" system (and may in the future), we went to great lengths to employ a clean layer formatted output that is
        extremely customizable using simple CSS logic. If you use FireBug, you can even be a novice.</p>
        <p>
          This Image Collection Gallery can be used as an Image Shopping Cart, for Company Portfolios,
          standard Photo Galleries, Artwork Display, or any number of instances where you want a Clean,
          Efficient gallery to display images.
        </p>
        <p>
          The wbGallery is provided to the community FREE through the GNU/GPL license.
          We have benefitted greatly from the Joomla community, and will be releasing a wealth of codes that have been developed for use with our clients over the years.
          This product will be one of many to come for media, product, collaboration, and catalog management. All will be integrated, all will be GPL compliant and ready for your use!
        </p>
        <p>
          Your comments and suggestions are what help us improve, so please take the time to visit our site and contact us with questions.
          If not already, we will be releasing our public forum for your collabration shortly.
        </p>
        <p>Go Joomla! Open-Source, and Collaboration! <grin></p>
        <p><a href="http://developer.webuddha.com/" target="_blank" title="Visit Webuddha Developers in a New Window">
          <img src="<?= $mainframe->getCfg('live_site') ?>/administrator/components/<?= $option ?>/images/webuddha_logo.jpg" border="0" /><br/>
          Connect to the Webuddha Developers Home Page
        </a></p>
      </div>
    <?php
  }

}
