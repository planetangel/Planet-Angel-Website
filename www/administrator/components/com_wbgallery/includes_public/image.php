<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Image Viewing
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class wbGallery_img {

  function view( $id, $params ){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    if( !$id )
      $id = $params->get('id',0);

    // Load Image
    $row = new wbGalleryDB_img($database);
    $row->load($id);
    if( !$row->id ){
      echo "<script> alert('Image Not Found'); window.history.go(-1); </script>\n";
      exit();
    }

    // Track Hit
    $row->hit();

    // Load Category
    if( $row->cat_id != '0' ){
      $cat = new wbGalleryDB_cat($database);
      $cat->load($row->cat_id);
      if( !$cat->id ){
        echo "<script> alert('Category Not Found'); window.history.go(-1); </script>\n";
        exit();
      }
      if( $cat->access > $my->gid ){
        mosNotAuth(); exit();
      }
    }

    // Load Prev / Next
    $neighbors = $row->getNeighbors();

    // Load Related Images
    $database->setQuery("
      SELECT i.*
      FROM #__wbgallery_img AS i
      WHERE i.cat_id = ".(int)$row->cat_id."
        AND i.id != ".(int)$row->id."
        AND i.published = 1
      ORDER BY i.hits DESC
        , i.ordering ASC
      LIMIT 0, ".$params->get('related_list_limit',$mainframe->getCfg('list_limit',15))."
      ");
    $related = $database->loadObjectList();
    echo $database->getErrorMsg();

    wbGallery_img_html::view($row,$cat,$neighbors,$related,$params);
  }

}

class wbGallery_img_html {

  // ************************************************************************
  function view(&$row, &$cat, &$neighbors, &$related, &$params){
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
      if( $params->get('view_img_name',0) )
        $page_title = $row->name;
      elseif( $params->get('wbg_title_follow',0) )
        $page_title = $params->get('wbg_title',$row->name);
      if( $page_title )
        $title = sprintf($WBG_LANG->_('VIEW_IMAGE'), $page_title );
        else $title = $WBG_LANG->_('VIEW_IMAGE_GEN');
      $mainframe->setPageTitle( $title );
      if( is_object($cat) && strlen($cat->name) && $params->get('view_img_name_cat',1) )
        echo '<h1 class="componentheading"><a href="'.sefRelToAbs('index.php?option='.$option.'&cid='.$cat->id.'&Itemid='.$wbgItemid).'" title="'.$cat->name.'">'.$cat->name.'</a> :: '. $title .'</h1>';
      else
        echo '<h1 class="componentheading">'. $title .'</h1>';
    }

    // Neighbor Links
    if( $params->get('view_neighbors',1) && ($neighbors->prev_id || $neighbors->next_id) ){
      $link = 'index.php?option='.$option.'&Itemid='.$wbgItemid.'&id=';
      echo '<div class="wbgNeighbor">';
      if($neighbors->prev_id)
        echo '<a href="'.sefRelToAbs($link.$neighbors->prev_id).'" alt="'.$neighbors->prev_name.'" class="prev">'.$WBG_LANG->_('VIEW_PREV_IMAGE').'</a>';
      if($neighbors->next_id)
        echo '<a href="'.sefRelToAbs($link.$neighbors->next_id).'" alt="'.$neighbors->next_name.'" class="next">'.$WBG_LANG->_('VIEW_NEXT_IMAGE').'</a>';
      echo '</div>';
    }

    // Render Detail Image
    $imgPath = wbGalleryDB_img::getImagePath( $params->get('view_img_size','large') );
    $imgFile = $imgPath.$row->file;
    ?>
    <div class="wbgImgView">
      <span class="wbgImg">
        <div class="img"><img src="<?= $imgFile ?>" border="0" /></div>
      </span>
    </div>
    <?php

    // Sku / Price / Photographer
    if($params->get('view_img_cat',0))
      echo '<div class="category">'.sprintf($WBG_LANG->_('CAT_NAME'), $cat->name ).'</div>';
    if($params->get('view_img_sku',0))
      echo '<div class="sku"><span>'.$WBG_LANG->_('SKU_TITLE').'</span> '
        .(strlen($row->sku)?$row->sku:$WBG_LANG->_('SKU_NA')).'</div>';
    if($params->get('view_img_price',0))
      echo '<div class="price"><span>'.$WBG_LANG->_('PRICE_TITLE').'</span> '
        .$wbGallery_common->formatPrice($row->price).'</div>';
    if($params->get('view_img_photog',0))
      echo '<div class="photog"><span>'.$WBG_LANG->_('PHOTOGRAPHER_TITLE').'</span> '
        .(strlen($row->photographer)?$row->photographer:$WBG_LANG->_('PHOTOGRAPHER_NA')).'</div>';

    // Image Description
    if( $params->get('view_img_desc',1) && strlen(trim(strip_tags($row->description))) ){
      echo '<div class="componentdescription">'.$row->description.'</div>';
      echo '<div class="clr"></div>';
    }

    // Show Related Images
    if( $params->get('view_related',1) && count($related) ){

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
      $imgPath     = wbGalleryDB_img::getImagePath( $params->get('related_img_size','thumb') );
      $lboxImgPath = wbGalleryDB_img::getImagePath( $params->get('lightbox_img_size','large') );

      // Render Relative Header
      if( $params->get('view_related_title',1) ){
        if( is_object($cat) )
          $relLink = '<a href="'.sefRelToAbs('index.php?option='.$option.'&cid='.$cat->id.'&Itemid='.$wbgItemid).'" title="'.$cat->name.'">'.$cat->name.'</a>';
        else
          $relLink = '<a href="'.sefRelToAbs('index.php?option='.$option.'&Itemid='.$wbgItemid).'" title="'.$WBG_LANG->_('RELATED_LINK_NOCAT').'">'.$WBG_LANG->_('RELATED_LINK_NOCAT').'</a>';
        echo '<h2 class="contentheading">'.sprintf($WBG_LANG->_('RELATED_IMAGES'),$relLink).'</h2>';
      }

      // Render Images
      echo '<div class="wbgRelatedImages">';
        echo '<div class="wbgImgList">';
          $count = 1;
          $cols = $params->get('related_img_cols',3);
          echo '<div class="ncpr'.$cols.'">';
          foreach( $related AS $img ){
            $link     = sefRelToAbs('index.php?option='.$option.'&id='.$img->id.'&Itemid='.$wbgItemid);
            $imgFile  = $imgPath.$img->file;
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
                  if($params->get('img_desc',1))
                    echo '<div class="desc">'.$img->description.'</div>';
                  ?>
                </span>
              </span>
            </span>
            <?php
            if(!($count++ % $cols))
              echo '<div class="clr"></div>';
          }
          echo '</div>';
          if((--$count % $cols))
            echo '<div class="clr"></div>';
        echo '</div>'; // wbgImgList
      echo '</div>'; // wbgRelatedImages

    } // End Related Images

    if( $params->get('view_goback') || $params->get('view_continue') ){
      echo '<div class="wbgFootnav">';
      if( $params->get('view_goback') )
        echo '<div class="wbg_goback"><a href="javascript:history.back();" title="'.$WBG_LANG->_('GOBACK').'">'.$WBG_LANG->_('GOBACK').'</a></div>';
      if( $params->get('view_continue') ){
        if( is_object($cat) )
          $relLink = '<a href="'.sefRelToAbs('index.php?option='.$option.'&cid='.$cat->id.'&Itemid='.$wbgItemid).'" title="'.$cat->name.'">'.$cat->name.'</a>';
        else
          $relLink = '<a href="'.sefRelToAbs('index.php?option='.$option.'&Itemid='.$wbgItemid).'" title="'.$WBG_LANG->_('RELATED_LINK_NOCAT').'">'.$WBG_LANG->_('RELATED_LINK_NOCAT').'</a>';
        echo '<div class="wbg_continue">'.sprintf($WBG_LANG->_('CONTINUE'),$relLink).'</div>';
      }
      echo '</div>';
    }

  }

}
