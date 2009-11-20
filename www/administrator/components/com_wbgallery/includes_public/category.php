<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Category Browsing
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class wbGallery_cat {

  function view( $id, $params ){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $limit      = (int)mosGetParam($_REQUEST,'limit',$params->get('list_limit',$mainframe->getCfg('list_limit',15)));
    $limitstart = (int)mosGetParam($_REQUEST,'limitstart',0);
    $page       = (int)mosGetParam($_REQUEST,'page',0);

    // No ID.. Do we have a Parameter?
    if( !$id )
      $id = $params->get('cid',0);

    // Ahh.. Page Defined... SEF is Talking
    if( $page )
      $limitstart = $limit * ($page-1);

    // Load Category
    $row = new wbGalleryDB_cat($database);
    $row->load($id);
    if( $row->id && ($row->access > $my->gid)){
      mosNotAuth(); return;
    }

    // Load Sub Categories
    $query = "
      SELECT c.*, COUNT(DISTINCT sc.id) AS total_subcats
      FROM #__wbgallery_cat AS c
      LEFT JOIN #__wbgallery_cat AS sc ON (sc.parent_id = c.id AND c.published = 1)
      WHERE c.parent_id = ".(int)$id."
        AND c.published = 1
        AND c.access <= ".(int)$my->gid."
      GROUP BY c.id
      ORDER BY c.ordering ASC
      ";
    $database->setQuery($query);
    $subcats = $database->loadObjectList();
    echo $database->getErrorMsg();

    // Load Sub Category Thumbnails ( if not already found )
    for($i=0;$i<count($subcats);$i++){
      if( !$sc->file ){
        $imgData = $wbGalleryDB_cat->getSubCatImg($subcats[$i]->id);
        $subcats[$i]->img_id   = $imgData->img_id;
        $subcats[$i]->img_file = $imgData->img_file;
        $subcats[$i]->img_name = $imgData->img_name;
      }
    }

    // Count Images
    $database->setQuery("
      SELECT COUNT(DISTINCT i.id)
      FROM #__wbgallery_img AS i
      WHERE i.cat_id = ".(int)$id."
        AND i.published = 1
      ");
    $total = $database->loadResult();

    // Load Images
    $database->setQuery("
      SELECT i.*
      FROM #__wbgallery_img AS i
      WHERE i.cat_id = ".(int)$id."
        AND i.published = 1
      ORDER BY i.ordering ASC
      LIMIT $limitstart, $limit
      ");
    $images = $database->loadObjectList();
    echo $database->getErrorMsg();

    // Page Navigation
    require_once($mainframe->getCfg('absolute_path') . '/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    wbGallery_cat_html::view($row,$subcats,$images,$pageNav,$params);
  }

}

class wbGallery_cat_html {

  // ************************************************************************
  function view(&$row, &$cats, &$imgs, &$pageNav, &$params){
    global $option, $mainframe, $wbgItemid, $my;
    global $WBG_CONFIG, $WBG_LANG, $wbGallery_common;

    // mosPathway must be in template
    $wbGallery_common->pathway($row);

    // Stylesheet
    if( $params->get( 'css' ) )
      $mainframe->addCustomHeadTag('<link href="'.$mainframe->getCfg('live_site').'/components/'.$option.'/css/'.$params->get( 'css' ).'" rel="stylesheet" type="text/css" />');
    else
      $mainframe->addCustomHeadTag('<link href="'.$mainframe->getCfg('live_site').'/components/'.$option.'/css/default.css" rel="stylesheet" type="text/css" />');

    // Page Title
    if( $params->get('wbg_title_show',0) ){
      $custom_title = ($params->get('wbg_title_follow',0) || ($row->id == $params->get('cid',0)));
      if( $custom_title && strlen($params->get('wbg_title',null)) )
        $title = $params->get('wbg_title');
      elseif( strlen($row->name) )
        $title = sprintf($WBG_LANG->_('VIEW_CATEGORY'), $row->name );
      else
        $title = $WBG_LANG->_('VIEW_CATEGORY_GEN');
      $mainframe->setPageTitle( $title );
      echo '<h1 class="componentheading">'. $title .'</h1>';
    }

    // Page Description
    if( $params->get('cat_desc',1) && strlen(trim(strip_tags($row->description))) ){
      echo '<div class="desc">'.$row->description.'</div>';
      echo '<div class="clr"></div>';
    }

    // Show Categories
    $countCat = 0;
    if( count($cats) ){

      // Get Image Size Path
      $imgPath = wbGalleryDB_img::getImagePath( $params->get('cat_size','thumb') );

      // Render Categories
      echo '<div class="wbgCatList">';
        $count = 1;
        $cols = $params->get('cat_cols',3);
        echo '<div class="ncpr'.$cols.'">';
        foreach( $cats AS $cat ){
          $imgFile = null;
          $link = sefRelToAbs('index.php?option='.$option.'&cid='.$cat->id.'&Itemid='.$wbgItemid);
          if( in_array($params->get('cat_image',1),array(2,3)) && $cat->file )
            $imgFile = $imgPath.$cat->file;
          elseif( in_array($params->get('cat_image',1),array(1,3)) && $cat->img_file )
            $imgFile = $imgPath.$cat->img_file;
          if( !$params->get('cat_image',1) || $imgFile ){
            ?>
            <span class="wbgCat">
              <span class="block">
                <span class="pad">
                  <?php
                    if( !is_null($imgFile) ){
                      if($params->get('cat_name',1))
                        echo '<div class="name">'.$cat->name.'</div>';
                      ?>
                      <a href="<?= $link ?>" title="'.$cat->name.'" class="img"><span style="background-image:url('<?= $imgFile ?>');">
                        <img src="<?= $imgFile ?>" alt="'.$cat->name.'" border="0" /></span></a>
                      <?php
                    } else
                      echo '<div class="name"><a href="'.$link.'" title="'.$cat->name.'">'
                        .$cat->name.'</a></div>';
                    if($params->get('cat_desc',1))
                      echo '<div class="desc">'.$cat->description.'</div>';
                  ?>
                </span>
              </span>
            </span>
            <?php
            if(!($count++ % $cols))
              echo '<div class="clr"></div>';
            $countCat++;
          }
        }
        echo '</div>';
        if((--$count % $cols))
          echo '<div class="clr"></div>';
      echo '</div>';
    }

    // Show Images
    $countImg = 0;
    if( count($imgs) ){

      // Page Navigation
      if( in_array($params->get('show_pagenav','both'), Array('top','both')) )
        echo '<div class="pagenav_top">'
          .$pageNav->writePagesLinks('index.php?option='.$option.'&cid='.$row->id.'&Itemid='.$wbgItemid)
          .'</div>';

      // Image Lightbox
      if( $params->get('img_link','lightbox') == 'lightbox'
          || $params->get('img_name_link','lightbox') == 'lightbox' ){
        $mainframe->addCustomHeadTag('<script type="text/javascript">var path="'.$mainframe->getCfg('live_site').'/components/'.$option.'/lightbox/";</script>');
        $mainframe->addCustomHeadTag('<script type="text/javascript" src="'. $mainframe->getCfg('live_site') . '/components/'.$option.'/lightbox/js/prototype.js"></script>');
        $mainframe->addCustomHeadTag('<script type="text/javascript" src="'. $mainframe->getCfg('live_site') . '/components/'.$option.'/lightbox/js/scriptaculous.js?load=effects"></script>');
        $mainframe->addCustomHeadTag('<script type="text/javascript" src="'. $mainframe->getCfg('live_site') . '/components/'.$option.'/lightbox/js/lightbox.js"></script>');
        $mainframe->addCustomHeadTag('<link rel="stylesheet" href="'. $mainframe->getCfg('live_site') . '/components/'.$option.'/lightbox/css/lightbox.css" type="text/css" media="screen" />');
      }

      // Get Image Size Path
      $imgPath     = wbGalleryDB_img::getImagePath( $params->get('img_size','thumb') );
      $lboxImgPath = wbGalleryDB_img::getImagePath( $params->get('lightbox_img_size','large') );

      // Render Images
      echo '<div class="wbgImgList">';
        $count = 1;
        $cols = $params->get('img_cols',3);
        echo '<div class="ncpr'.$cols.'">';
        foreach( $imgs AS $img ){
          $link    = sefRelToAbs('index.php?option='.$option.'&id='.$img->id.'&Itemid='.$wbgItemid);
          $imgFile = $imgPath.$img->file;
          $lboxFile = $lboxImgPath.$img->file;
          $lboxName = $img->name.($params->get('img_price',0)?' '.$wbGallery_common->formatPrice($img->price):'');
          ?>
          <span class="wbgImg">
            <span class="block">
              <span class="pad">
                <?php
                if($params->get('img_name',1))
                  if($params->get('img_name_link','view') == 'view')
                    echo '<div class="name"><a href="'.$link.'">'.$img->name.'</a></div>';
                  elseif($params->get('img_name_link') == 'lightbox')
                    echo '<div class="name"><a href="'.$lboxFile.'" rel="lightbox['.$row->name.']" title="'.$lboxName.'">'.$img->name.'</a></div>';
                  elseif($params->get('img_name_link') == 'load')
                    echo '<div class="name"><a href="'.$lboxFile.'" title="'.$lboxName.'">'.$img->name.'</a></div>';
                  elseif($params->get('img_name_link') == 'load_pop')
                    echo '<div class="name"><a href="'.$lboxFile.'" title="'.$lboxName.'" target="_blank">'.$img->name.'</a></div>';
                  else
                    echo '<div class="name">'.$img->name.'</div>';
                if($params->get('img_image',1)){
                  if( $params->get('img_link','lightbox') != 'lightbox' ){
                    $alt_link = null;
                    if( $params->get('lightbox_alternate','view') == 'view' )
                      $alt_link = $link;
                    elseif( in_array($params->get('lightbox_alternate'),array('load','load_pop')) )
                      $alt_link = $lboxFile;
                    ?>
                    <a href="<?= (is_null($alt_link)?'javascript:void(0);':$alt_link)
                      ?>" title="<?= $lboxName ?>" <?= ($params->get('lightbox_alternate')=='load_pop'?'target="_blank"':'')
                      ?> class="img"><span style="background-image:url('<?= $imgFile
                      ?>');"><img src="<?= $imgFile ?>" border="0" /></span></a>
                    <?php
                  } else {
                    ?>
                    <a href="<?= $lboxFile ?>" rel="lightbox[<?= $row->name ?>]" title="<?= $lboxName
                      ?>" class="img"><span style="background-image:url('<?= $imgFile
                      ?>');"><img src="<?= $imgFile ?>" border="0" /></span></a>
                    <?php
                  }
                }
                if($params->get('img_sku',0))
                  echo '<div class="sku"><span>'.$WBG_LANG->_('SKU_TITLE').'</span> '
                    .(strlen($img->sku)?$img->sku:$WBG_LANG->_('SKU_NA')).'</div>';
                if($params->get('img_price',0))
                  echo '<div class="price"><span>'.$WBG_LANG->_('PRICE_TITLE').'</span> '
                    .$wbGallery_common->formatPrice($img->price).'</div>';
                if($params->get('img_photog',0))
                  echo '<div class="photog"><span>'.$WBG_LANG->_('PHOTOGRAPHER_TITLE').'</span> '
                    .(strlen($img->photographer)?$img->photographer:$WBG_LANG->_('PHOTOGRAPHER_NA')).'</div>';
                if($params->get('img_desc',0))
                  echo '<div class="desc">'.$img->description.'</div>';
                ?>
              </span>
            </span>
          </span>
          <?php
          if(!($count++ % $cols))
            echo '<div class="clr"></div>';
          $countImg++;
        }
        echo '</div>';
        if((--$count % $cols))
          echo '<div class="clr"></div>';
      echo '</div>';

      // Page Navigation
      if( in_array($params->get('show_pagenav','both'), Array('bottom','both')) )
        echo '<div class="pagenav_bottom">'
          .$pageNav->writePagesLinks('index.php?option='.$option.'&cid='.$row->id.'&Itemid='.$wbgItemid)
          .'</div>';

    }

    // Nothing Displayed? Post Error...
    if( !$countCat && !$countImg )
      echo '<h2 class="alert_msg">'.$WBG_LANG->_('ALERT_NODISPLAY').'</h1>';

  }

}
