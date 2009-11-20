<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/joomgallery.html.php $
// $Id: joomgallery.html.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

function Joom_GalleryHeader()
{
  global $func, $catid, $id;
  $config   = Joom_getConfig();
  $database = & JFactory::getDBO();
  $user     = & JFactory::getUser();

?>
<div class="gallery">
<?php
  if($config->jg_showgalleryhead)
  {
?>
  <div class="componentheading">
    <?php echo JText::_('JGS_GALLERY') ;?> 
  </div>
<?php
  }

  //load modules at position 'top'
  $modules = Joom_getModules('top');
  if(count($modules))
  {
    $document = &JFactory::getDocument();
    $renderer = $document->loadRenderer('module');
    $style    = -2;
    $params   = array('style'=>$style);

    foreach($modules as $module)
    {
?>
  <div class="jg_topmodule">
<?php
      echo $renderer->render($module, $params);
?>
  </div>
<?php
    }
  }

  if($config->jg_showpathway == 1 || $config->jg_showpathway == 3) Joom_ShowGalleryPathway();
  if($config->jg_search == 1 || $config->jg_search == 3) Joom_ShowGallerySearch();
  if($config->jg_showbacklink == 1 || $config->jg_showbacklink == 3) Joom_ShowGalleryBackLink_HTML();
  if($config->jg_userspace == 1)
  {
    if(   (($config->jg_showuserpanel == 1) && ($user->get('aid') > 0))
       || (($config->jg_showuserpanel > 0 ) && ($user->get('aid') == 2))
       || ($config->jg_showuserpanel == 3)
      )
    {
      if($user->get('aid') != 0)
      {
?>
  <div class="jg_mygal">
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID) ;?>">
      <?php echo JText::_('JGS_USER_PANEL') ;?> 
    </a>
  </div>
<?php
      }
      else
      {
?>
  <div class="jg_mygal">
    <span class="jg_no_access" onMouseOver="return overlib('<?php echo JText::_('JGS_YOU_ARE_NOT_LOGGED',true); ?>', CAPTION, '<?php echo JText::_('JGS_USER_PANEL',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
      <?php echo JText::_('JGS_USER_PANEL'); ?>
    </span>
  </div>
<?php
      }
    }
  }
  if($config->jg_favourites) Joom_ShowFavouritesLink();
  if($config->jg_showallpics == 1 || $config->jg_showallpics == 3) Joom_ShowGalleryAllPics ();
  if(    $config->jg_whereshowtoplist == 0 
     || ($config->jg_whereshowtoplist  > 0 && $func == '') 
     || ($config->jg_whereshowtoplist == 2 && $func == 'viewcategory') 
    )
  {
    if($config->jg_showtoplist > 0 && $config->jg_showtoplist < 3) Joom_ShowGalleryTopList_HTML();
  }
  return;
}//End function Joom_GalleryHeader


function Joom_GalleryDefault($start)
{
  $config   = Joom_getConfig();
  $database = & JFactory::getDBO();
  $user     = & JFactory::getUser();

  $query1 = " SELECT 
                *
              FROM 
                #__joomgallery_catg
              WHERE 
                 published = '1' 
                AND parent = 0
            ";
  if($config->jg_showrmsmcats == 0)
  {
   $query1 .= "  AND access<= '".$user->get('aid')."'";
  }
  if($config->jg_ordercatbyalpha)
  {
    $query1.= " ORDER BY 
                  name 
                LIMIT $start,".$config->jg_catperpage."
             ";
  } else {
    $query1.= " ORDER BY 
                  ordering 
                LIMIT $start,".$config->jg_catperpage."
            ";
  }
  $database->setQuery($query1);
  $rows = $database->loadObjectList();

  if(!$rows == NULL)
  {
    $num_rows   = ceil(count($rows ) / $config->jg_colcat);
    $count_pics = count($rows);
    $index      = 0;

    if($config->jg_showallcathead)
    {
?>
  <div class="sectiontableheader">
    <?php echo JText::_('JGS_CATEGORIES'); ?>&nbsp;
  </div>
<?php
    }
    for($row_count=0; $row_count < $num_rows; $row_count++)
    {
      $linecolor = ($row_count % 2) + 1;
      //Ausrichtung der Thumbs nur wirksam, wenn Random-Anzeige aktiviert
      if($config->jg_showcatthumb == 1) //random
      {
        if($config->jg_ctalign == 0)
        {
          $ctalign = ($row_count % 2) + 1;
          if($ctalign == 1)
          {
            $ctalign = 'left';
          }
          else
          {
            $ctalign = 'right';
          }
        }
        if($config->jg_ctalign == 1)
        {
          $ctalign = 'left';
        }
        elseif($config->jg_ctalign == 2)
        {
          $ctalign = 'right';
        }
        elseif($config->jg_ctalign == 3)
        {
          $ctalign = 'center';
        }
      }
      else
      {
        $ctalign = 'left';
      }
?>
  <div class="jg_row <?php if ($linecolor == 1) echo "sectiontableentry1"; else echo "sectiontableentry2";?>">
<?php
      for($col_count = 0; (($col_count < $config->jg_colcat) && ($index < $count_pics)); $col_count++)
      {
        if(($config->jg_ctalign == 0 && $linecolor == 1) || $config->jg_ctalign > 0)
        {
?>
    <div class="jg_element_gal">
<?php     
        }
        else
        {
?>
    <div class="jg_element_gal_r">
<?php
        }
        $row1 = $rows[$index];
        if($config->jg_showcatasnew)
        {
          $isnew = Joom_CheckNewCatg( $row1->cid );
        }
        else
        {
          $isnew ='';
        }
        $pictures         = Joom_GetNumberOfLinks($row1->cid);
        $numberofpictures = number_format($pictures, 0, ',', '.');
        if($pictures == 1)
        {
          $picorpics = JText::_('JGS_PICTURE');
        }
        else
        {
          $picorpics = JText::_('JGS_PICTURES');
        }
        if($row1->img_position == 0 || $row1->img_position == NULL)
        {
          $img_position = 'left';
        }
        elseif($row1->img_position == 1)
        {
          $img_position = 'right';
        }
        elseif($row1->img_position == 2)
        {
          $img_position = 'middle';
        }
        if($config->jg_showcatthumb == 1)
        {
          $allsubcats = Joom_GetAllSubCategories($row1->cid, $config->jg_showrandomcatthumb);
          if($allsubcats)
          {
            $randomcat = $allsubcats[mt_rand(0, count($allsubcats)-1)];
          }
          else
          {
            //keine Kategorie mit Bildern gefunden
            $randomcat = '0';
          }
        }
        if($config->jg_showtotalcathits)
        {
          if($config->jg_showrandomcatthumb > 2 && $config->jg_showcatthumb == 1)
          {
            //wenn Zufallsbild aus Cat oder Cat und Subcats und Anzeige des 
            //Cat Bildes, die schon vorher festgestellten Cats uebernehmen
            $totalsubcats = $allsubcats;
          }
          else
          {
            $totalsubcats = Joom_GetAllSubCategories($row1->cid, 4);
          }
          $totalhits = Joom_GetTotalHits($totalsubcats);
        }
        if($config->jg_showcatthumb > 0)
        {
          if($user->get('aid') >= $row1->access)
          {
            if($config->jg_showcatthumb == 1)
            {
              //random pic, nur wenn $randomcat(s) vorhanden
              if(    $config->jg_showrandomcatthumb == 1 
                 || ($config->jg_showrandomcatthumb >= 2 && $randomcat != '0')
                )
              {
                $catid = $row1->cid;
                $query = "  SELECT 
                              *,
                              c.access 
                            FROM 
                              #__joomgallery AS p
                            LEFT JOIN 
                              #__joomgallery_catg AS c ON c.cid = p.catid
                          ";
                if($config->jg_showrandomcatthumb == 1)
                {
                  $query.= "  WHERE 
                                p.catid = $catid
                           ";
                }
                elseif($config->jg_showrandomcatthumb >= 2)
                {
                  $query.= "  WHERE 
                                p.catid = $randomcat
                           ";
                }
                $query.= "      AND p.published = '1' 
                                AND p.approved='1' 
                                AND c.access <= ".$user->get('aid')." 
                                AND c.published = '1'
                              ORDER BY 
                                rand() 
                              LIMIT 1
                         ";
                $database->setQuery($query);
                $rows1 = $database->LoadObjectList();
                $count = count($rows1);
                if(isset( $rows1[0])) $row = $rows1[0];
              }
              else
              {
                $count = 0;
              }

              if($count > 0)
              {
                if(($config->jg_ctalign == 0 && $linecolor == 1) || $config->jg_ctalign > 0)
                {
?>
      <div class="jg_photo_container">
<?php
                }
                else
                {
?>
      <div class="jg_photo_container_r">
<?php
                }
?>
        <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$row1->cid._JOOM_ITEMID); ?>">
          <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$row->catpath.'/'.$row->imgthumbname; ?>" align="<?php if ($ctalign=='center')echo 'middle'; else echo $ctalign;?>" class="jg_photo" alt="<?php echo $row->imgtitle; ?>" />
        </a>
      </div>
<?php
              }
            }
            elseif($config->jg_showcatthumb == 2 && $row1->catimage != '')
            {
?>
      <div class="jg_photo_container">
        <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$row1->cid._JOOM_ITEMID); ?>">
          <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$row1->catpath.'/'.$row1->catimage; ?>" align="<?php echo $img_position;?>" class="jg_photo" alt="<?php echo $row1->name; ?>" />
        </a>
      </div>
<?php
            }
          }
          if($config->jg_showcatthumb == 2)
          {
            $ctalign = $img_position;
            if($ctalign == 'middle') $ctalign = 'center';
          }
          if(($config->jg_ctalign == 0 && $linecolor == 1) || $config->jg_ctalign > 0)
          {
?>
      <div class="jg_element_txt">
<?php     
          }
          else
          {
?>
      <div class="jg_element_txt_r">
<?php  
          } 
?>
        <ul>
          <li>
<?php
          if($user->get('aid') >= $row1->access)
          {
?>
            <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$row1->cid._JOOM_ITEMID); ?>">
              <b><?php echo $row1->name; ?></b>
            </a>
<?php
          }
          else
          {
?>
            <span class="jg_no_access" onMouseOver="return overlib('<?php echo JText::_('JGS_ALERT_YOU_NOT_ACCESS_THIS_DIRECTORY',true); ?>', CAPTION, '<?php echo addslashes($row1->name); ?>', BELOW, RIGHT);" onmouseout="return nd();">
              <b><?php echo $row1->name; ?></b>
            </span>
<?php
          }
        }
        else
        {
          if(($config->jg_ctalign == 0 && $linecolor == 1) || $config->jg_ctalign == 1 || $config->jg_ctalign == 3)
          {
            // set $ctalign for using later in Joom_ShowCategoryTree()
            if($config->jg_ctalign == 3)
            {
              $ctalign = 'center';
            }
            else
            {
              $ctalign = 'left';
            }
?>
      <div class="jg_element_txt">
<?php     
          }
          else
          {
            // set $ctalign for using in Joom_ShowCategoryTree
            $ctalign = 'right';
?>
      <div class="jg_element_txt_r">
<?php  
          } 
?>
        <ul>
          <li>
<?php
          if($user->get('aid') >= $row1->access)
          {
?>
            <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$row1->cid._JOOM_ITEMID); ?>">
              <b><?php echo $row1->name; ?></b>
            </a>
<?php
          }
          else
          {
?>
            <span class="jg_no_access" onMouseOver="return overlib('<?php echo JText::_('JGS_ALERT_YOU_NOT_ACCESS_THIS_DIRECTORY',true); ?>', CAPTION, '<?php echo addslashes($row1->name); ?>', BELOW, RIGHT);" onmouseout="return nd();">
              <b><?php echo $row1->name; ?></b>
            </span>
<?php
          }
        }
        if($config->jg_rmsm > 0)
        {
          if($row1->access > 1)
          {
?>
            <span class="jg_sm">
              <?php echo JText::_('JGS_SPECIAL_MEMBERS'); ?>&nbsp;
            </span>
<?php
          }
          elseif($row1->access > 0)
          {
            if( ($user->get('aid') >= $row1->access && !$config->jg_showrmsmcats ) 
               || $config->jg_showrmsmcats
              )
            {
?>
            <span class="jg_rm">
              <?php echo JText::_('JGS_REGISTERED_MEMBERS'); ?>&nbsp;
            </span>
<?php
            }
          }
        }
?>
          </li>
<?php
        if($user->get('aid') >= $row1->access)
        {
?>
          <li>
            (<?php echo $numberofpictures; ?> <?php echo $picorpics; ?>)<?php echo $isnew; ?>&nbsp;
          </li>
<?php
        }
        if($config->jg_showtotalcathits)
        {
?>
          <li>
            <?php echo JText::_('JGS_HITS'); ?>: <?php echo $totalhits; ?>&nbsp;
          </li>
<?php
        }
        if($row1->description)
        {
?>
          <li>
            <?php echo $row1->description; ?>&nbsp;
          </li>
<?php
        }?>
        </ul>
      </div>
<?php
        // use treeview to display subcategories
        if($config->jg_showsubsingalleryview)
        {
          Joom_ShowCategoryTree($row1->cid, $ctalign);
        }
?>
    </div>
<?php
      $index++;
      }
?>
    <div class="jg_clearboth"></div>
  </div>
<?php
    }
    if($config->jg_showallcathead)
    {
?>
  <div class="sectiontableheader">
    &nbsp;
  </div>
<?php
    }
  }
}//End function Joom_GalleryDefault


function Joom_GalleryFooter() 
{
  global $func;
  $config = Joom_getConfig();

  if($func == 'detail')
  {
?>
  <div class="sectiontableheader">
    &nbsp; 
  </div>
<?php
  }
  if(    $config->jg_whereshowtoplist == 0 
     || ($config->jg_whereshowtoplist  > 0 && !$func)
     || ($config->jg_whereshowtoplist == 2 && $func == 'viewcategory'))
  {
    if($config->jg_showtoplist > 1)
    {
      Joom_ShowGalleryTopList_HTML();
    }
  }
  if($config->jg_rmsm == 1 && (!$func || $func == 'viewcategory'))
  {
?>
  <div class="jg_rm">
    <?php echo JText::_('JGS_REGISTERED_MEMBERS'); ?>: <?php echo  JText::_('JGS_REGISTERED_MEMBERS_LONG'); ?>&nbsp;
  </div>
  <div class="jg_sm">
    <?php echo JText::_('JGS_SPECIAL_MEMBERS'); ?>: <?php echo  JText::_('JGS_SPECIAL_MEMBERS_LONG'); ?>&nbsp;
  </div>
<?php
  }
  if($config->jg_showallpics >= 2) Joom_ShowGalleryAllPics();
  if($config->jg_showbacklink >= 2) Joom_ShowGalleryBackLink_HTML();
  if($config->jg_search >= 2) Joom_ShowGallerySearch();
  if($config->jg_showpathway >= 2) Joom_ShowGalleryPathway();

  //load modules at position 'btm'
  $modules = Joom_getModules('btm');
  if(count($modules))
  {
    $document = &JFactory::getDocument();
    $renderer = $document->loadRenderer('module');
    $style    = -2;
    $params   = array('style'=>$style);

    foreach($modules as $module)
    {
?>
  <div class="jg_btmmodule">
<?php
      echo $renderer->render($module, $params);
?>
  </div>
<?php
    }
  }

  if($config->jg_suppresscredits)
  {
?>
  <div class="jg_clearboth"></div>
  <div align="center" class="jg_poweredbydiv">
    <a href="http://www.joomgallery.net" target="_blank">
      <img src="<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/powered_by.gif" class="jg_poweredby" alt="Powered by JoomGallery" />
    </a>
  </div>
<?php
  }
?>
</div>
<?php
}//End function Joom_GalleryFooter


function Joom_ShowFavouritesLink()
{
  global $func;
  $config = Joom_getConfig();
  $user = & JFactory::getUser();

  if($func != 'showfavourites')
  {
    if(   (($config->jg_showdetailfavourite == 0) && ($user->get('aid') >= 1)) 
       || (($config->jg_showdetailfavourite == 1) && ($user->get('aid') == 2)) 
       || (($config->jg_usefavouritesforpubliczip == 1) && ($user->get('aid') < 1))
      )
    {
      if( ($config->jg_usefavouritesforzip == 1)
         || (($config->jg_usefavouritesforpubliczip == 1) && ($user->get('aid') < 1))
        )
      {
?>
  <div class="jg_my_favourites">
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=showfavourites'._JOOM_ITEMID); ?>"
        onMouseOver="return overlib('<?php echo JText::_('JGS_ZIP_DOWNLOAD_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_ZIP_MY',true); ?>', BELOW, RIGHT);" onmouseout="return nd();"><?php echo JText::_('JGS_ZIP_MY'); ?>
      <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/basket.png' ;?>" alt="<?php echo JText::_('JGS_ZIP_MY'); ?>" class="pngfile jg_icon" />
    </a>
  </div>
<?php
      }
      else
      {
        $tooltip_text = JText::_('JGS_FAV_DOWNLOAD_TOOLTIP_TEXT',true);
        if($config->jg_zipdownload && $func != 'createzip')
        {
          $tooltip_text .= ' '.JText::_('JGS_ZIP_DOWNLOAD_ALLOWED_TOOLTIP_TEXT',true);
        }
?>
  <div class="jg_my_favourites">
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=showfavourites'._JOOM_ITEMID); ?>"
        onMouseOver="return overlib('<?php echo $tooltip_text; ?>', CAPTION, '<?php echo JText::_('JGS_FAV_MY',true); ?>', BELOW, RIGHT);" onmouseout="return nd();"><?php echo JText::_('JGS_FAV_MY',true); ?>
      <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/star.png' ;?>" alt="<?php echo JText::_('JGS_FAV_MY'); ?>" class="pngfile jg_icon" />
    </a>
  </div>
<?php
      }
    }
    elseif(($config->jg_favouritesshownotauth == 1/*) && ($user->get('aid') < 1*/))
    {
      if($config->jg_usefavouritesforzip == 1)
      {
?>
  <div class="jg_my_favourites">
    <span class="jg_no_access" onMouseOver="return overlib('<?php echo JText::_('JGS_ZIP_DOWNLOAD_NOT_ALLOWED_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_ZIP_MY',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" ><?php echo JText::_('JGS_ZIP_MY'); ?>
      <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/basket_gr.png' ;?>" alt="<?php echo JText::_('JGS_ZIP_MY'); ?>"  class="pngfile jg_icon" />
    </span>
  </div>
<?php
      }
      else
      {
?>
  <div class="jg_my_favourites">
    <span class="jg_no_access" onMouseOver="return overlib('<?php echo JText::_('JGS_FAV_DOWNLOAD_NOT_ALLOWED_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_FAV_MY',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" ><?php echo JText::_('JGS_FAV_MY'); ?>
      <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/star_gr.png' ;?>" alt="<?php echo JText::_('JGS_FAV_MY'); ?>"  class="pngfile jg_icon" />
    </span>
  </div>
<?php
      }
    }
  }
  elseif($config->jg_zipdownload == 1 || ($user->get('id') < 1 && $config->jg_usefavouritesforpubliczip))
  {
?>
  <div class="jg_my_favourites">
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=createzip'._JOOM_ITEMID); ?>"
        onMouseOver="return overlib('<?php echo JText::_('JGS_ZIP_CREATE_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_ZIP_DOWNLOAD',true); ?>', BELOW, RIGHT);" onmouseout="return nd();"><?php echo JText::_('JGS_ZIP_DOWNLOAD'); ?>
      <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/package_go.png' ;?>" alt="<?php echo JText::_('JGS_ZIP_DOWNLOAD'); ?>" class="pngfile jg_icon" />
    </a>
  </div>
<?php
  }
}//End function Joom_ShowFavouritesLink


function Joom_ShowGallerySearch()
{
?>
  <div class = "jg_search">
    <form action="<?php echo JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID); ?>" target="_top" method="post">
      <input type="hidden" name="func" value="special" />
      <input type="hidden" name="sorting" value="find" />
      <input type="text" name="sstring" class="inputbox" onblur="if(this.value=='') this.value='<?php echo JText::_('JGS_SEARCH',true) ;?>';" onfocus="if(this.value=='<?php echo  JText::_('JGS_SEARCH',true) ;?>') this.value='';" value="<?php echo  JText::_('JGS_SEARCH') ;?>" />
    </form>
  </div>
<?php
}//End function Joom_ShowGallerySearch


function Joom_ShowGalleryPathway()
{
  global $catid, $id, $func;
  $config   = Joom_getConfig();
  $database = & JFactory::getDBO();
  $user     = & JFactory::getUser();

  $path1 = _JOOM_LIVE_SITE.'index.php?option=com_joomgallery'._JOOM_ITEMID;
  $path2 = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
  $path3 = _JOOM_LIVE_SITE.'index.php?option=com_joomgallery&amp;Itemid=99999999';

  if( !(($config->jg_showgallerysubhead == 0) && (($path1 == $path2) 
     || ($path3 == $path2) )) 
    )
  {
?>
  <div class="jg_pathway" >
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID) ;?>">
    <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/home.png' ;?>" class="pngfile jg_icon" hspace="6" border="0" align="middle" alt="Home" /></a>
<?php
    if($catid != '' && $func != 'special')
    {
      echo Joom_CategoryPathLink($catid);
    }elseif($id)
    {
      $database->setQuery(" SELECT 
                              a.*, 
                              cc.name AS category
                            FROM 
                              #__joomgallery AS a, 
                              #__joomgallery_catg AS cc
                            WHERE 
                                     a.catid = cc.cid 
                              AND a.id       = '$id' 
                              AND cc.access <= '".$user->get('aid')."'
                          ");
      $rows = $database->loadObjectList();
      $row  = &$rows[0];
      echo Joom_CategoryPathLink($row->catid);
    }
?>
  </div>
<?php
  }
}//End function Joom_ShowGalleryPathway


function Joom_CompleteBreadcrumbs($catid, $id, $func = '')
{
  $config    = Joom_getConfig();
  $mainframe = & JFactory::getApplication('site');
  $database  = & Jfactory::getDBO();
  $user      = & JFactory::getUser();
  $pathway   = & $mainframe->getPathway();

  // Sonderfaelle zuerst
  switch($func)
  {
    case 'userpanel':
      $pathway->addItem(JText::_('JGS_USER_PANEL'));
      break;
    case 'uploadhandler':
    case 'showupload':
      $pathway->addItem(JText::_('JGS_USER_PANEL'),'index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID);
      $pathway->addItem(JText::_('JGS_NEW_PICTURE'));
      break;
    case 'showusercats':
      $pathway->addItem(JText::_('JGS_USER_PANEL'),'index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID);
      $pathway->addItem(JText::_('JGS_CATEGORIES'));
      break;
    case 'newusercat':
      $pathway->addItem(JText::_('JGS_USER_PANEL'),'index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID);
      $pathway->addItem(JText::_('JGS_NEW_CATEGORY'));
      break;
    case 'showfavourites':
      if($user->get('id') && $config->jg_usefavouritesforzip != 1) {
        $pathway->addItem(JText::_('JGS_FAV_MY'));
      } else {
        $pathway->addItem(JText::_('JGS_ZIP_MY'));
      }
      break;
    case 'createzip':
      $pathway->addItem(JText::_('JGS_ZIP_DOWNLOAD'));
      break;
  }
  if($func != '' && $func != 'viewcategory' && $func != 'detail')
  {
    return;
  }

  // falls keine catid vorhanden
  if($catid == 0 || $func == 'detail')
  {
    if($id != 0)
    {
      $database->setQuery(" SELECT 
                              a.id,
                              a.imgtitle,
                              a.catid
                            FROM 
                              #__joomgallery AS a, 
                              #__joomgallery_catg AS cc
                            WHERE 
                                     a.catid = cc.cid 
                              AND a.id       = '$id' 
                              AND cc.access <= '".$user->get('aid')."'
                          ");
      if(!$row = $database->loadObject())
      {
        return false;
      }
      $catid = $row->catid;
    }
    else
    {
      return false;
    }
  }
  // catid ist hier auf jeden Fall gesetzt

  // id's und Namen aller uebergeordneten Kategorien aus der Datenbank holen
  $cat_ids   = array($catid);
  $cat_names = array();
  while($catid != 0)
  {
    $database->setQuery(" SELECT 
                            name,
                            parent,
                            cid 
                          FROM 
                            #__joomgallery_catg
                          WHERE 
                                      cid = '$catid' 
                            AND published = '1' 
                            AND access    <= '".$user->get('aid')."'
                      ");
    if(!$cat_row = $database->loadObject())
    {
      $catid = 0;
    }
    else
    {
      $catid = $cat_row->parent;
    }
    if($catid != 0)
    {
      array_unshift($cat_ids, $catid);
    }
    array_unshift($cat_names, $cat_row->name);
  }

  // Breadcrumbs mit Kategorien vervollstaendigen
  for($i = 0; $i<count($cat_names); $i++)
  {
    $pathway->addItem($cat_names[$i], 'index.php?option=com_joomgallery&func=viewcategory&catid='.$cat_ids[$i]._JOOM_ITEMID);
  }
  
  // eventuell Bildnamen hinzufuegen
  if(isset($row->id))
  {
    $pathway->addItem($row->imgtitle, 'index.php?option=com_joomgallery&func=detail&id='.$row->id._JOOM_ITEMID);
  }
}//End Joom_CompleteBreadcrumbs


function Joom_ShowGalleryAllPics()
{
  $config   = Joom_getConfig();
  $database = & JFactory::getDBO();
  $user     = & JFactory::getUser();

  if($config->jg_showallhits)
  {
    $query = "  SELECT 
                  COUNT(id), 
                  SUM(imgcounter)
             ";
  }
  else
  {
    $query = "  SELECT 
                  COUNT(id)
             ";
  } 
  $query  .= "  FROM 
                  #__joomgallery AS a
                LEFT JOIN 
                  #__joomgallery_catg AS c ON c.cid = a.catid
                WHERE 
                    a.published = '1' 
                AND a.approved  = '1' 
                AND c.published = '1' 
                AND c.access   <= ".$user->get('aid')."
              ";

  $database->setQuery($query); 
  $numberarr    = $database->loadRow();  
  $numberofpics = number_format($numberarr[0], 0, ',', '.');
?>
  <div class="jg_gallerystats">
    <?php echo JText::_('JGS_NUMB_PICTURES_ALL') . ' ' . $numberofpics; ?>&nbsp;
<?php
    if($config->jg_showallhits)
    {
      Joom_ShowGalleryAllHits($numberarr[1]);
    }
?>
  </div>
<?php
}//End function Joom_ShowGalleryAllPics


/**
* Counts the hits of all published and approved pics in the gallery
* if the cats are published
*/
function Joom_ShowGalleryAllHits(&$numberofhits)
{
  if($numberofhits == NULL)
  {
    $numberofhits = 0;
  }
?>
    <br />
    <?php echo JText::_('JGS_NUMB_HITS_ALL_PICTURES') . ' ' . $numberofhits; ?>&nbsp;
<?php
}//End function Joom_ShowGalleryAllHits


function Joom_ShowGalleryTopList_HTML()
{
  $config = Joom_getConfig();
  $separator = "    -\n";
?>
  <div class="jg_toplist">
    <?php echo JText::_('JGS_TOP').' '.$config->jg_toplist; ?>:
<?php
  if($config->jg_showrate)
  {
?>
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=special&sorting=rating'._JOOM_ITEMID); ?>">
      <?php echo JText::_('JGS_TOP_RATED'); ?></a>
<?php
    if($config->jg_showlatest || $config->jg_showcom || $config->jg_showmostviewed)
    {
      echo $separator;
    }
  }
  if($config->jg_showlatest)
  {
?>
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=special&sorting=lastadd'._JOOM_ITEMID); ?>">
      <?php echo JText::_('JGS_LAST_ADDED'); ?></a>
<?php
    if($config->jg_showcom || $config->jg_showmostviewed)
    {
      echo $separator;
    }
  }
  if($config->jg_showcom)
  {
?>
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=special&sorting=lastcomment'._JOOM_ITEMID); ?>">
      <?php echo JText::_('JGS_LAST_COMMENTED'); ?></a>
<?php
    if($config->jg_showmostviewed)
    {
      echo $separator;
    }
  }
  if($config->jg_showmostviewed)
  {
?>
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=special'._JOOM_ITEMID); ?>">
      <?php echo JText::_('JGS_MOST_VIEWED'); ?></a>
<?php
  }
?>
  </div>
<?php
}//End function Joom_ShowGalleryTopList_HTML


function Joom_ShowGalleryPageNav_HTML($count2, $start, $startpage, $gesamtseiten)
{
  $config = Joom_getConfig();

  if(!$config->jg_showcatcount && $gesamtseiten == 1 || $count2 == 0) return;
?>
  <div class="jg_pagination">
<?php
  if($config->jg_showcatcount)
  {
    if($count2 == 1)
    {
?>
    <?php echo JText::_('JGS_THERE_IS') ." ".$count2." ". JText::_('JGS_CATEGORY_IN_GALLERY'); ?>&nbsp;
<?php
    }
    elseif($count2 > 1)
    {
?>
    <?php echo JText::_('JGS_THERE_ARE') ." ".$count2." ". JText::_('JGS_CATEGORIES_IN_GALLERY'); ?>&nbsp;
<?php
    }
  }
?>
    <br />
<?php
  //Wenn nur eine Seite, keine Ausgabe der Navigation
  if($gesamtseiten > 1)
  {
    //Ausgeben '<< Anfang'
    if($startpage != 1)
    {
?>
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&startpage=1'._JOOM_ITEMID); ?>" class="jg_pagenav">
      &laquo;&laquo;&nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_BEGIN'); ?></a>&nbsp;
<?php
    }
    else
    {
?>
    <span class="jg_pagenav">
      &laquo;&laquo;&nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_BEGIN'); ?>&nbsp;
    </span>
<?php
    }
    // Ausgeben der Seite zurueck Funktion
    $seiterueck = $startpage - 1;
    if($seiterueck > 0)
    {
?>
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&startpage='.$seiterueck._JOOM_ITEMID); ?>" class="jg_pagenav">
      &laquo;&nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_PREVIOUS'); ?></a>
<?php
    }
    else
    {
?>
    <span class="jg_pagenav">
      &laquo;&nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_PREVIOUS'); ?>&nbsp;
    </span>
<?php
    }
?>
    <?php echo Joom_GenPagination('index.php?option=com_joomgallery&startpage=%u'._JOOM_ITEMID,$gesamtseiten,$startpage,""); ?>

<?php
    // Ausgeben der Seite vorwaerts Funktion
    $seitevor = $startpage + 1;
    if($seitevor <= $gesamtseiten)
    {
?>
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&startpage='.$seitevor._JOOM_ITEMID); ?>" class="jg_pagenav">
      &nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_NEXT'); ?>&nbsp;&raquo;</a>
<?php
    }
    else
    {
?>
    <span class="jg_pagenav">
      &nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_NEXT'); ?>&nbsp;&raquo;
    </span>
<?php
    }
    //Ausgeben 'Ende >>'
    if($startpage != $gesamtseiten)
    {
?>
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&startpage='.$gesamtseiten._JOOM_ITEMID); ?>" class="jg_pagenav">
      &nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_END'); ?>&nbsp;&raquo;&raquo;</a>
<?php
    }
    else
    {
?>
    <span class="jg_pagenav">
      &nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_END'); ?>&nbsp;&raquo;&raquo;
    </span>
<?php
    }
  }
?>
  </div>
<?php
}//End function Joom_ShowGalleryPageNav_HTML


function Joom_ShowGalleryBackLink_HTML()
{
  global $func, $id, $catid;
  $database = & JFactory::getDBO();

  if(!empty($func))
  {
    $backtarget = '';
    $backtext   = '';

    //Unter/Kategorieansicht
    if($func == 'viewcategory')
    {
      $query = "  SELECT 
                    parent
                  FROM 
                    #__joomgallery_catg
                  WHERE 
                    cid = '$catid'
                ";
      $database->setQuery($query);
      $newcatid = $database->loadResult();
      if($newcatid != 0)
      {
        //Unterkategorieansicht -> Parentkategorie
        $backtarget = JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$newcatid._JOOM_ITEMID);
        $backtext   = JText::_('JGS_BACK_TO_CATEGORY');
      }
      else
      {
        //Kategorieansicht -> Galerieansicht
        $backtarget = JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID);
        $backtext   = JText::_('JGS_BACK_TO_GALLERY');
      }
    }
    elseif($func == 'detail')
    {
      //Detailansicht ->Kategorieansicht
      $query = "  SELECT 
                    catid
                  FROM 
                    #__joomgallery
                  WHERE 
                    id = '$id'
                ";
      $database->setQuery($query);
      $newcatid = $database->loadResult();

      $backtarget = JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$newcatid._JOOM_ITEMID).'#category';
      $backtext  = JText::_('JGS_BACK_TO_CATEGORY');
    }
    else
    {
      $backtarget = "javascript:history.back();";
      $backtext   = JText::_('JGS_BACK');
    }
?>
  <div class="jg_back">
    <a href="<?php echo $backtarget; ?>">
      <?php echo $backtext; ?></a>
  </div>
<?php
  }
}//End function Joom_ShowGalleryBackLink_HTML


function Joom_ShowCategoryTree($rootcatid, $ctalign)
{
  $config   = Joom_getConfig();
  $database = & JFactory::getDBO();
  $user     = & JFactory::getUser();

  // get all categories
  $query = "  SELECT 
                cid, 
                name, 
                parent, 
                access
              FROM 
                #__joomgallery_catg
              WHERE 
                published = '1'
              ORDER BY 
                parent ASC, 
                name ASC
            ";
  $database->setQuery($query);
  $categories = $database->LoadObjectList();

  // check access rights settings
  $filter_cats    = false;
  $show_rmsm      = false;
  $show_rmsm_cats = false;
  if(!$config->jg_rmsm && !$config->jg_showrmsmcats)
  {
    $filter_cats = true;
  }
  else
  {
    if($config->jg_rmsm)
    {
      $show_rmsm = true;
    }
    if($config->jg_showrmsmcats)
    {
      $show_rmsm_cats = true;
    }
  }

  // Array to hold the relevant subcategory objects
  $subcategories = Array();
  // array to hold the valid parent categories
  $validParentCats   = Array();
  $validParentCats[] = $rootcatid;
  // get all relevant the subcategories
  foreach($categories AS $category)
  {
    if(   ($category->parent == $rootcatid  || in_array($category->parent, $validParentCats))
       && ($filter_cats == false || $user->get('aid') >= $category->access)
      )
    {
      $subcategories[]   = $category;
      $validParentCats[] = $category->cid;
    }
  }

  // show the treeview
  $count = count($subcategories);
  if($count > 0)
  {
    if($ctalign == 'left')
    {
?>
        <div class="jg_treeview_l">
<?php
    }
    elseif($ctalign == 'right')
    {
?>
        <div class="jg_treeview_r">
<?php
    }
    else
    {
?>
        <div class="jg_treeview_c">
<?php
    }
          // Debug
          // echo "ctalign=".$ctalign;
?>
          <table>
            <tr>
              <td>
                <script type="text/javascript" language="javascript">
                <!--
                // create new dTree object
                var jg_TreeView<?php echo $rootcatid;?> = new jg_dTree( <?php echo "'"."jg_TreeView".$rootcatid."'"; ?>,
                                                                        <?php echo "'"._JOOM_LIVE_SITE."components/com_joomgallery/assets/js/dTree/img/"."'";?> );
                // dTree configuration
                jg_TreeView<?php echo $rootcatid;?>.config.useCookies = true;
                jg_TreeView<?php echo $rootcatid;?>.config.inOrder = true;
                jg_TreeView<?php echo $rootcatid;?>.config.useSelection = false;
                // add root node
                jg_TreeView<?php echo $rootcatid;?>.add( 0, -1, ' ', <?php echo "'".JRoute::_( 'index.php?option=com_joomgallery'.$rootcatid._JOOM_ITEMID )."'"; ?>, false );
                // add node to hold all subcategories
                jg_TreeView<?php echo $rootcatid;?>.add( <?php echo $rootcatid;?>, 0, <?php echo "'".JText::_('JGS_SUBCATEGORIES')."(".$count.")"."'";?>,
                                                         <?php echo "'".JRoute::_( 'index.php?option=com_joomgallery&func=viewcategory&catid='.$rootcatid._JOOM_ITEMID )."'"; ?>, false );
<?php
    foreach($subcategories AS $category)
    {
      // create subcategory name and subcategory link
      $rm_or_sm = "";
      if($filter_cats == false || $user->get('aid') >= $category->access)
      {
        if($user->get('aid') >= $category->access)
        {
          $cat_name = addslashes(trim( $category->name ));
          $cat_link = JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$category->cid._JOOM_ITEMID, false);
        }
        else
        {
          $cat_name = ($show_rmsm_cats == true ? addslashes(trim($category->name)) : JText::_('JGS_NO_ACCESS'));
          $cat_link = '';
        }
      }
      if($show_rmsm == true)
      {
        if(intval($category->access) == 1)
        {
          $rm_or_sm = '&nbsp'.'<span class="jg_rm">'.JText::_('JGS_REGISTERED_MEMBERS').'</span>';
        }
        elseif(intval($category->access) == 2)
        {
          $rm_or_sm = '&nbsp'.'<span class="jg_sm">'.JText::_('JGS_SPECIAL_MEMBERS').'</span>';
        }
        $cat_name .= $rm_or_sm;
      }
      if($config->jg_showcatasnew)
      { 
        $isnew = Joom_CheckNewCatg($category->cid);
      }
      else
      {
        $isnew = '';
      }
      $cat_name .= '&nbsp'.$isnew;

      // add node
      if($category->parent == $rootcatid)
      {
?>
                jg_TreeView<?php echo $rootcatid;?>.add(<?php echo $category->cid;?>, 
                                                        <?php echo $rootcatid;?>, 
                                                        <?php echo "'".$cat_name."'";?>,
                                                        <?php echo "'".$cat_link."'"; ?>, 
                                                        <?php echo $user->get('aid') >= $category->access ? 'false' :'true'; ?>
                                                        );
<?php
      }
      else
      {
?>
                jg_TreeView<?php echo $rootcatid;?>.add(<?php echo  $category->cid;?>, 
                                                        <?php echo $category->parent;?>,
                                                        <?php echo "'".$cat_name."'";?>, 
                                                        <?php echo "'".$cat_link."'"; ?>,
                                                        <?php echo $user->get('aid') >= $category->access ? 'false' :'true'; ?> 
                                                        );
<?php
      }
    }
?>
                document.write(jg_TreeView<?php echo $rootcatid;?>);
                -->
                </script>
              </td>
            </tr>
          </table>
        </div>
<?php
  }
}

?>
