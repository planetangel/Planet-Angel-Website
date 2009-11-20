<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/html/joom.viewcategory.html.php $
// $Id: joom.viewcategory.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_Category
{

  function Joom_ShowCategoryHead_HTML(&$catname, &$colum, &$count, &$catid, $order_by, &$order_dir)
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();

    if($count == 0)
    {
      echo '';
    }
    else
    {
?>
  <div class="jg_category">
<?php
      if($config->jg_showcathead)
      {
?>
    <div class="sectiontableheader">
      <?php echo $catname; ?> 
    </div>
<?php
      }
      if($config->jg_showcatdescriptionincat == 1)
      {
          $database->setQuery(" SELECT 
                                  cid,
                                  description
                                FROM 
                                  #__joomgallery_catg
                                WHERE 
                                  cid = '$catid'
                              ");
           $catdescobj = $database->loadObject();
           $catdescription = $catdescobj->description;
?>
    <div class="jg_catdescr">
      <?php echo $catdescription; ?> 
    </div>
<?php
      }
      if($config->jg_usercatorder)
      {
        $config->jg_usercatorderlist = explode (',', $config->jg_usercatorderlist);
        //if navigation active insert actual startpage and substartpage
        if(!empty($this->catstartpage))
        {
          if(!empty($this->substartpage))
          {
            $sortURL = JRoute::_($this->viewcategory_url.$catid."&startpage="
                                 .$this->catstartpage."&substartpage=".$this->substartpage)
                                 ."#category";
          }
          else
          {
            $sortURL = JRoute::_($this->viewcategory_url.$catid."&startpage="
                                 .$this->catstartpage)."#category";
          }
        }
        else
        {
          $sortURL = JRoute::_($this->viewcategory_url.$catid)."#category";
        }
?>
    <div style="white-space:nowrap;" align="right">
      <form action="<?php echo $sortURL;?>" method="post">
        <?php echo JText::_('JGS_USER_ORDERBY'); ?> 
        <select name="orderby" onchange='this.form.submit()' class="inputbox">
          <option value="default"><?php echo JText::_('JGS_USER_ORDERBY_DEFAULT'); ?></option>
<?php
         if(in_array('date', $config->jg_usercatorderlist))
         {
?>
          <option <?php if($order_by == 'date') echo 'selected="selected"'; ?> value="date"><?php echo JText::_('JGS_USER_ORDERBY_DATE'); ?></option>
<?php
         }
         if(in_array('user', $config->jg_usercatorderlist))
         {
?>
          <option <?php if($order_by == 'user') echo 'selected="selected"'; ?> value="user"><?php echo JText::_('JGS_USER_ORDERBY_AUTHOR'); ?></option>
<?php
         }
         if(in_array('title', $config->jg_usercatorderlist))
         {
?>
          <option <?php if($order_by == 'title') echo 'selected="selected"'; ?> value="title"><?php echo JText::_('JGS_USER_ORDERBY_TITLE'); ?></option>
<?php
         }
         if(in_array('hits', $config->jg_usercatorderlist))
         {
?>
          <option <?php if ($order_by == 'hits') echo 'selected="selected"'; ?> value="hits"><?php echo JText::_('JGS_USER_ORDERBY_HITS'); ?></option>
<?php
         }
         if(in_array('rating', $config->jg_usercatorderlist))
         {
?>
          <option <?php if($order_by == 'rating') echo 'selected="selected"'; ?> value="rating"><?php echo JText::_('JGS_USER_ORDERBY_RATING'); ?></option>
<?php
         }
?>
        </select>
<?php
          if($order_by != 'title' && $order_by != 'hits' && $order_by != 'date' && $order_by != 'user' && $order_by != 'rating' )
          {
?>
        <select<?php echo " disabled=\"disabled\""; ?> name="orderdir" onchange='this.form.submit()' class="inputbox">
<?php
          }
          else
          {
?>
        <select name="orderdir" onchange='this.form.submit()' class="inputbox">
<?php
          }
?>
          <option <?php if ($order_dir == 'asc') echo 'selected="selected"' ?> value="asc"><?php echo JText::_('JGS_USER_ORDERBY_ASC'); ?></option>
          <option <?php if ($order_dir == 'desc') echo 'selected="selected"' ?> value="desc"><?php echo JText::_('JGS_USER_ORDERBY_DESC'); ?></option>
        </select>
      </form>
    </div>
<?php
      }
?>
  </div>
<?php
    }
  }//End function Joom_ShowCategoryHead_HTML


  function Joom_ShowCategoryBody_HTML(&$rows, &$rowcounter, &$colum , $order_by, &$order_dir)
  {
    global $id;
    $config    = Joom_getConfig();
    $document  = & JFactory::getDocument();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    //wenn jg_cooliris = true, dann zusaetzlich XML im head aufbauen
    if($config->jg_cooliris && count($rows) > 0)
    {
      $href = _JOOM_LIVE_SITE.$this->viewcategory_url.$this->catid.'&startpage='.$this->catstartpage.'&cooliris=1'._JOOM_ITEMID;
      $attribs = array('id'=>'joomgallery','type'=>'application/rss+xml','title'=>'Cooliris');
      $document->addHeadLink($href,'alternate','rel',$attribs);

      if($config->jg_coolirislink )
      {
        $document->addScript('http://lite.piclens.com/current/piclens.js');
        echo '<a id="jg_cooliris" href="javascript:PicLensLite.start({feedUrl:\''._JOOM_LIVE_SITE.$this->viewcategory_url.$this->catid
          .'&startpage='.$this->catstartpage._JOOM_ITEMID.'&cooliris=1\'});">'.JText::_('JGS_COOLIRISLINK_TEXT').'</a>';
      }
    }

    if(!$config->jg_showtitle &&
      !$config->jg_showhits &&
      !$config->jg_showauthor &&
      !$config->jg_showowner &&
      !$config->jg_showcatrate &&
      !$config->jg_showcatcom &&
      !$config->jg_showcatdescription )
    {
      $show_text = false;
    }
    else
    {
      $show_text = true;
    }
    $num_rows = ceil(count($rows ) / $colum);
    $index = 0;
    $count_pics = count($rows);

?>
  <a name="category"></a>
<?php
    if($count_pics > 0)
    {
      for($row_count=0; $row_count < $num_rows; $row_count++)
      {
        $linecolor = (($row_count+1) % 2) + 1;
?>
  <div class="jg_row <?php if ($linecolor == 1) echo "sectiontableentry1"; else echo "sectiontableentry2";?>">
<?php
        for($col_count = 0; ($col_count < $colum) && ($index < $count_pics); $col_count++)
        {
          $ii   = 1;
          $row1 = $rows[$index];
          if($config->jg_showpicasnew)
          {
            $isnew = Joom_CheckNew($row1->imgdate, $config->jg_daysnew);
          }
          $catpath = Joom_GetCatPath($row1->cid);
          if(( $config->jg_showdetailpage==0 && $user->get('aid')!=0 ) 
             || $config->jg_showdetailpage==1
            )
          {
            $link = Joom_OpenImage($config->jg_detailpic_open, $row1->id, $catpath, 
                                   $row1->cid, $row1->imgfilename, $row1->imgtitle,
                                   $row1->imgtext);
          }
          else
          {
            $link = "javascript:alert('".JText::_('JGS_ALERT_NO_DETAILVIEW_FOR_GUESTS',true)."')";
          }
?>
    <div class="jg_element_cat">
      <a href="<?php echo $link; ?>" class="jg_catelem_photo">
        <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row1->imgthumbname; ?>" class="jg_photo" alt="<?php echo $row1->imgtitle; ?>" />
      </a>
<?php
         if($show_text)
         {
?>
      <div class="jg_catelem_txt">
        <ul>
<?php
              if($config->jg_showtitle || $config->jg_showpicasnew)
              {
?>
          <li>
<?php
                if($config->jg_showtitle)
                {
?>
            <b><?php echo $row1->imgtitle; ?></b>
<?php
                }
                if($config->jg_showpicasnew)
                {
?>
            <?php echo $isnew; ?>&nbsp;
<?php
                }
?>
          </li>
<?php
              }
              if($config->jg_showauthor)
              {
                if($row1->imgauthor)
                {
                  $authorowner = $row1->imgauthor;
                }
                elseif($config->jg_showowner)
                {
                  $authorowner = Joom_GetDisplayName($row1->owner);
                }
                else
                {
                  $authorowner = JText::_('JGS_NO_DATA');
                }
?>
          <li>
            <?php echo JText::_('JGS_AUTHOR') . ": ".$authorowner; ?>&nbsp;
          </li>
<?php
              }
              if($config->jg_showhits)
              {
?>
          <li>
            <?php echo JText::_('JGS_HITS') . ": " . $row1->imgcounter; ?>&nbsp;
          </li>
<?php
              }
              if($config->jg_showcatrate)
              {
                if($row1->imgvotes > 0)
                {
                  $fimgvotesum = number_format($row1->imgvotesum / $row1->imgvotes, 2, ',', '.');
                  if($row1->imgvotes == 1)
                  {
                    $frating = $fimgvotesum.' ('.$row1->imgvotes.' '.JText::_('JGS_ONE_VOTE').')';
                  }
                  else
                  {
                    $frating = $fimgvotesum.' ('.$row1->imgvotes.' '.JText::_('JGS_VOTES').')';
                  }
                }
                else
                {
                  $frating = JText::_('JGS_NO_VOTES');
                }
?>
          <li>
            <?php echo JText::_('JGS_RATING') . ": " . $frating; ?>&nbsp;
          </li>
<?php
              }
              if($config->jg_showcatcom)
              {
                # Check how many comments exist
                $database->setQuery(" SELECT 
                                        COUNT(cmtid)
                                      FROM 
                                        #__joomgallery_comments
                                      WHERE 
                                               cmtpic = '$row1->id' 
                                        AND published ='1' 
                                        AND approved = '1'
                                    ");
                $comments = $database->LoadResult();
?>
          <li>
            <?php echo JText::_('JGS_COMMENTS') . ": " . $comments; ?> 
          </li>
<?php
              }
              if ($config->jg_showcatdescription == 1  && $row1->imgtext)
              {
?>
          <li>
            <?php echo JText::_('JGS_DESCRIPTION') . ": " . $row1->imgtext; ?>&nbsp;
          </li>
<?php
              }
              $mainframe->triggerEvent('onAfterDisplayJoomThumb', array($row1->id));
              $li_tag_set = false;
              if( (is_file(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$row1->imgfilename)) 
                 || $config->jg_downloadfile!=1)
                )
              {
                if(   (($config->jg_showcategorydownload == 1) && ($user->get('aid') >= 1)) 
                   || (($config->jg_showcategorydownload == 2) && ($user->get('aid') == 2)) 
                   || (($config->jg_showcategorydownload == 3))
                  )
                {
?>
          <li>
            <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=download&catid='.$row1->catid.'&id='.$row1->id._JOOM_ITEMID); ?>"
                onMouseOver="return overlib('<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
              <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/download.png' ;?>" alt="<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" />
            </a>
<?php
                  $li_tag_set = true;
                }
                elseif(($config->jg_showcategorydownload == 1) && ($user->get('aid') < 1))
                {
?>
          <li>
            <span onMouseOver="return overlib('<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_TEXT_LOGIN',true); ?>', CAPTION, '<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
              <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/download_gr.png' ;?>" alt="<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
            </span>
<?php
                  $li_tag_set = true;
                }
              }
              if($config->jg_favourites == 1 && $config->jg_showcategoryfavourite)
              {
                if(   (($config->jg_showdetailfavourite == 0) && ($user->get('aid') >= 1))
                   || (($config->jg_showdetailfavourite == 1) && ($user->get('aid') == 2))
                   || (($config->jg_usefavouritesforpubliczip == 1) && ($user->get('aid') < 1))
                  )
                {
                  if($config->jg_usefavouritesforzip == 1
                     || (($config->jg_usefavouritesforpubliczip == 1) && ($user->get('aid') < 1))
                    )
                  {
                    if(!$li_tag_set)
                    {
                      $li_tag_set = true;
?>
          <li>
<?php
                    }
?>
            <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=addpicture&id='.$row1->id.'&catid='.$row1->catid._JOOM_ITEMID); ?>"
                onMouseOver="return overlib('<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
              <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/basket_put.png' ;?>" alt="<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
            </a>
<?php
                  }
                  else
                  {
                    if(!$li_tag_set)
                    {
                      $li_tag_set = true;
?>
          <li>
<?php
                    }
?>
            <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=addpicture&id='.$row1->id.'&catid='.$row1->catid._JOOM_ITEMID); ?>"
                onMouseOver="return overlib('<?php echo JText::_('JGS_FAV_ADD_PICTURE_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_FAV_ADD_PICTURE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
              <img src="<?php echo $this->assetsimages_url.'star.png' ;?>" alt="<?php echo JText::_('JGS_FAV_ADD_PICTURE_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" />
            </a>
<?php
                  }
                }
                elseif(($config->jg_favouritesshownotauth == 1))
                {
                  if($config->jg_usefavouritesforzip == 1)
                  {
                    if(!$li_tag_set)
                    {
                      $li_tag_set = true;
?>
          <li>
<?php
                    }
?>
            <span onMouseOver="return overlib('<?php echo JText::_('JGS_ZIP_ADD_PICTURE_NOT_ALLOWED_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
              <img src="<?php echo $this->assetsimages_url.'basket_put_gr.png' ;?>" alt="<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
            </span>
<?php
                  }
                  else
                  {
                    if(!$li_tag_set)
                    {
                      $li_tag_set = true;
?>
          <li>
<?php
                    }
?>
            <span onMouseOver="return overlib('<?php echo JText::_('JGS_FAV_ADD_PICTURE_NOT_ALLOWED_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_FAV_ADD_PICTURE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
              <img src="<?php echo $this->assetsimages_url.'star_gr.png' ;?>" alt="<?php echo JText::_('JGS_FAV_ADD_PICTURE_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
            </span>
<?php
                  }
                }
              }
              if($li_tag_set)
              {
?>
          </li>
<?php
              }
?>
        </ul>
      </div>
<?php
          }
?>
    </div>
<?php
          $index++;
        } // for loop over cols in row
?>
    <div class="jg_clearboth"></div>
  </div>
<?php
          $ii++;
      } // for loop over rows
      if($config->jg_showcathead)
      {
?>
    <div class="sectiontableheader">
      &nbsp; 
    </div>
<?php
      }
    } // if count($pics) > 0
?>
<?php
  }//End function Joom_ShowCategoryBody_HTML


  function Joom_ShowSubCategories_HTML(&$rows)
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    $pic_count = count($rows);
    $num_rows  = ceil($pic_count / $config->jg_colsubcat);
    $index     =0;
?>
  <div class="jg_subcat">
<?php
    if ($config->jg_showsubcathead)
    {
?>
    <div class="sectiontableheader">
      <?php echo JText::_('JGS_SUBCATEGORIES'); ?>&nbsp;
    </div>
<?php
    }
?>
  </div>
<?php
    //Ausrichtung entsprechend der globalen Vorgabe
    switch($config->jg_subcatthumbalign)
    {
      case 1:
        $img_position = 'left';
        break;
      case 2:
        $img_position = 'right';
        break;
      case 3:
        $img_position = 'middle';
        break;
    }
    for($row_count=0; $row_count < $num_rows; $row_count++)
    {
      $linecolor = (($row_count+1) % 2) + 1;
?>
  <div class="jg_row <?php if ($linecolor == 1) echo "sectiontableentry1"; else echo "sectiontableentry2"; ?>">
<?php
      for($col_count = 0; ($col_count < $config->jg_colsubcat) && ($index < $pic_count); $col_count++)
      {
        $cur_name = $rows[$index];

        if($config->jg_showcatasnew)
        {
          $isnew = Joom_CheckNewCatg( $cur_name->cid );
        }
        else
        {
          $isnew = '';
        }
        $catpath = $cur_name->catpath.'/';
?>
    <div class="jg_subcatelem_cat">
<?php
        if($cur_name != NULL)
        {
          if($config->jg_showsubthumbs != 0 )
          {
?>
      <div class="jg_subcatelem_photo">
<?php
          }
          if($config->jg_showsubthumbs == 1)
          {
            if($user->get('aid') >= $cur_name->access && $cur_name->catimage != '')
            {
?>
          <a href="<?php echo JRoute::_($this->viewcategory_url.$cur_name->cid._JOOM_ITEMID); ?>">
            <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$cur_name->catimage; ?>"  align="<?php echo $img_position; ?>" hspace="4" vspace="0" class="jg_photo" alt="<?php echo $cur_name->name; ?>" />
          </a>
<?php
            }
?>
      </div>
      <div class="jg_subcatelem_txt">
        <img src="<?php echo $this->assetsimages_url.'arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
<?php
            if($user->get('aid') >= $cur_name->access )
            {
?>
        <a href="<?php echo JRoute::_($this->viewcategory_url.$cur_name->cid._JOOM_ITEMID); ?>">
          <?php echo $cur_name->name; ?></a>
<?php
            }
            else
            {
?>
        <span class="jg_no_access" onMouseOver="return overlib('<?php echo JText::_('JGS_ALERT_YOU_NOT_ACCESS_THIS_DIRECTORY',true); ?>', CAPTION, '<?php echo addslashes($cur_name->name); ?>', BELOW, RIGHT);" onmouseout="return nd();">
          <?php echo $cur_name->name; ?>&nbsp;
        </span>
<?php
            }
?>
              (<?php echo Joom_GetNumberOfLinks($cur_name->cid); ?>)<?php echo $isnew; ?>&nbsp;
<?php
          }
          if($config->jg_showsubthumbs == 0)
          {
?>
        <div class="jg_subcatelem_txt">
          <ul>
            <li>
              <img src="<?php echo $this->assetsimages_url.'arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
<?php
            if($user->get('aid') >= $cur_name->access)
            {
?>
              <a href="<?php echo JRoute::_($this->viewcategory_url.$cur_name->cid._JOOM_ITEMID); ?>">
                <?php echo $cur_name->name; ?></a>
<?php
            }
            else
            {
?>
              <span class="jg_no_access" onMouseOver="return overlib('<?php echo JText::_('JGS_ALERT_YOU_NOT_ACCESS_THIS_DIRECTORY',true); ?>', CAPTION, '<?php echo addslashes($cur_name->name); ?>', BELOW, RIGHT);" onmouseout="return nd();">
                <?php echo $cur_name->name; ?>&nbsp;
              </span>
<?php
            }
?>
              (<?php echo Joom_GetNumberOfLinks($cur_name->cid); ?>) <?php echo $isnew; ?>&nbsp;
            </li>
<?php
          }
          if($config->jg_showsubthumbs == 2)
          {
            $allsubcats = Joom_GetAllSubCategories ($cur_name->cid, $config->jg_showrandomsubthumb);
            if($allsubcats)
            {
              mt_srand();
              $randomsubcat = $allsubcats[mt_rand(0, count($allsubcats)-1)];
            }
            else
            {
              $randomsubcat = '0';
            }
          }
          if($config->jg_showtotalsubcathits)
          {
            if($config->jg_showrandomsubthumb > 2 && $config->jg_showsubthumbs == 2)
            {
              $totalsubcats = $allsubcats;
            }
            else
            {
              $totalsubcats = Joom_GetAllSubCategories($cur_name->cid, 4);
            }
            $totalhits = Joom_GetTotalHits($totalsubcats);
          }
          if($config->jg_showsubthumbs == 2)
          {
            //random pic nur, wenn auch $randomsubcat(s) vorhanden
            if($config->jg_showrandomsubthumb == 1 
               || ($config->jg_showrandomsubthumb >= 2 && $randomsubcat != '0')
              )
            {
              $subcatid = $cur_name->cid;
              $query = "  SELECT 
                            *,
                            c.access 
                          FROM 
                            #__joomgallery AS p
                          LEFT JOIN 
                            #__joomgallery_catg AS c ON c.cid = p.catid
                          WHERE 
                      ";
              if($config->jg_showrandomsubthumb == 1)
              {
                $query.= "  p.catid = $cur_name->cid";
              }
              elseif($config->jg_showrandomsubthumb >= 2)
              {
                $query.= "  p.catid = $randomsubcat";
                $catpath = Joom_getCatPath($randomsubcat);
              }
              $query.= "  AND p.published = '1' 
                          AND p.approved  = '1' 
                          AND c.access   <= ".$user->get('aid')." 
                          AND c.published = '1'
                        ORDER BY 
                          rand() 
                        LIMIT 1
                      ";
              $database->setQuery( $query );
              $rows2 = $database->loadObjectList();
              $count = count($rows2);
            }
            else
            {
              $count = 0; 
            }
            if($count > 0 )
            {
              $row3 = $rows2[0];
              if($row3->imgfilename != '')
              {
?>
          <a href="<?php echo JRoute::_($this->viewcategory_url.$cur_name->cid._JOOM_ITEMID); ?>">
            <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row3->imgfilename; /*// vorher $row3->catpath.'/' anstatt $catpath */ ?>" align="<?php echo $img_position; ?>" hspace="4" vspace="0" class="jg_photo" alt="<?php echo $cur_name->name." :: ".$row3->imgtitle; ?>" />
          </a>
<?php
              }
            }
?>
      </div>
      <div class="jg_subcatelem_txt">
        <ul>
          <li>
            <img src="<?php echo $this->assetsimages_url.'arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
<?php
            if($user->get('aid') >= $cur_name->access)
            {
?>
            <a href="<?php echo JRoute::_($this->viewcategory_url.$cur_name->cid._JOOM_ITEMID); ?>">
              <?php echo $cur_name->name; ?></a>
<?php
            }
            else
            {
?>
            <span class="jg_no_access" onMouseOver="return overlib('<?php echo JText::_('JGS_ALERT_YOU_NOT_ACCESS_THIS_DIRECTORY',true); ?>', CAPTION, '<?php echo addslashes($cur_name->name); ?>', BELOW, RIGHT);" onmouseout="return nd();">
              <?php echo $cur_name->name; ?>&nbsp;
            </span>
<?php
            }
?>
            (<?php echo Joom_GetNumberOfLinks( $cur_name->cid ); ?>) <?php echo $isnew; ?>&nbsp;
          </li>
<?php
          }
          if($config->jg_rmsm > 0)
          {
            if($cur_name->access > 1)
            {
?>
          <li>
            <span class="jg_sm">
              <?php echo JText::_('JGS_SPECIAL_MEMBERS'); ?>&nbsp;
            </span>
          </li>
<?php
            }
            elseif($cur_name->access > 0)
            {
?>
          <li>
            <span class="jg_rm">
              <?php echo JText::_('JGS_REGISTERED_MEMBERS'); ?>&nbsp;
            </span>
          </li>
<?php
            }
          }
        }
        if($user->get('aid') >= $cur_name->access)
        {
          if($config->jg_showtotalsubcathits)
          {
?>
          <li>
            <?php echo JText::_('JGS_HITS'); ?>: <?php echo $totalhits; ?>&nbsp;
          </li>
<?php
          }
          if($cur_name->description)
          {
?>
          <li>
            <?php echo $cur_name->description; ?>&nbsp;
          </li>
<?php
          }
        }
        $mainframe->triggerEvent('onAfterDisplayJoomCatThumb', array($cur_name->cid));
?>
        </ul>
      </div>
    </div>
<?php
        $index++;
      } // for loop over cols in row
?>
    <div class="jg_clearboth"></div>
  </div>
<?php
    } // for loop over rows

  }//End function Joom_ShowSubCategories_HTML


  function Joom_ShowCategoryPageNav_HTML (&$count, &$start, &$startpage, &$gesamtseiten, &$catid)
  {
    $order_by  = $this->order_by;
    $order_dir = $this->order_dir;
    $config    = Joom_getConfig();

    if(!$config->jg_showpiccount && $gesamtseiten == 1 || $count == 0) return;
?>
  <div class="jg_pagination">
<?php
    if($config->jg_showpiccount)
    {
      if($count == 1)
      {
?>
    <?php echo JText::_('JGS_THERE_IS') .' '.$count.' '. JText::_('JGS_PICTURE_IN_CATEGORY'); ?> 
<?php
      }
      elseif($count > 1)
      {
?>
    <?php echo JText::_('JGS_THERE_ARE') .' '.$count.' '. JText::_('JGS_PICTURES_IN_CATEGORY'); ?> 
<?php
      }
    }
    if($gesamtseiten > 1)
    {
      if($config->jg_usercatorder)
      {
        $order_url = '';
        if($order_by != '')
        {
          $order_url.= "&amp;orderby=$order_by";
        }
        if($order_dir != '')
        {
          $order_url .= "&amp;orderdir=$order_dir";
        }
      }
      else
      {
        $order_url = '';
      }
      //Ausgeben '<< Anfang'
      if($startpage != 1)
      {
?>
    <br />
    <a href="<?php echo JRoute::_($this->viewcategory_url.$catid.'&startpage=1'.$order_url._JOOM_ITEMID).'#category'; ?>" class="jg_pagenav">
      &laquo;&laquo;&nbsp; <?php echo JText::_('JGS_PAGENAVIGATION_BEGIN'); ?></a>
    &nbsp;&nbsp;
<?php
      }
      else
      {
?>
    <br />
    <span class="jg_pagenav">
      &laquo;&laquo;&nbsp; <?php echo JText::_('JGS_PAGENAVIGATION_BEGIN'); ?> 
    </span>
    &nbsp;&nbsp;
<?php
      }
      // Ausgeben der Seite zurueck Funktion
      $seiterueck = $startpage - 1;
      if($seiterueck > 0)
      {
?>
    <a href="<?php echo JRoute::_($this->viewcategory_url.$catid.'&startpage='.$seiterueck.$order_url._JOOM_ITEMID)."#category"; ?>" class="jg_pagenav">
      &laquo;&nbsp; <?php echo JText::_('JGS_PAGENAVIGATION_PREVIOUS'); ?></a>
    &nbsp;
<?php
      }
      else
      {
?>
    <span class="jg_pagenav">
      &laquo;&nbsp;
      <?php echo JText::_('JGS_PAGENAVIGATION_PREVIOUS'); ?>&nbsp;
    </span>
    &nbsp;
<?php
      }
      // Ausgeben der einzelnen Seiten
?>
      <?php echo Joom_GenPagination($this->viewcategory_url.$catid.'&startpage=%u'.$order_url._JOOM_ITEMID,$gesamtseiten,$startpage,"#category");?>
<?php
      // Ausgeben der Seite vorwaerts Funktion
      $seitevor = $startpage + 1;
      if($seitevor <= $gesamtseiten)
      {
?>
    &nbsp;&nbsp;
    <a href="<?php echo JRoute::_($this->viewcategory_url.$catid.'&startpage='.$seitevor.$order_url._JOOM_ITEMID).'#category'; ?>" class="jg_pagenav">
      <?php echo JText::_('JGS_PAGENAVIGATION_NEXT'); ?> &nbsp;&raquo;</a>
    &nbsp;
<?php
      }
      else
      {
?>
    &nbsp;&nbsp;
    <span class="jg_pagenav">
      <?php echo JText::_('JGS_PAGENAVIGATION_NEXT'); ?> &nbsp;&raquo;&nbsp;
    </span>
<?php
      }
      //Ausgeben 'Ende >>'
      if($startpage != $gesamtseiten)
      {
?>
    &nbsp;
    <a href="<?php echo JRoute::_($this->viewcategory_url.$catid.'&startpage='.$gesamtseiten.$order_url._JOOM_ITEMID)."#category"; ?>" class="jg_pagenav">
      <?php echo JText::_('JGS_PAGENAVIGATION_END'); ?> &nbsp;&raquo;&raquo;</a>
<?php
      }
      else
      {
?>
    &nbsp;
    <span class="jg_pagenav">
      <?php echo JText::_('JGS_PAGENAVIGATION_END'); ?> &nbsp;&raquo;&raquo;
    </span>
<?php
      }
    }
?>
  </div>
<?php
  }//End function Joom_ShowCategoryPageNav_HTML


  function Joom_ShowSubCategoryPageNav_HTML(&$count3, &$substart, &$substartpage, &$subgesamtseiten, &$catid)
  {
    $config    = Joom_getConfig();
    $startpage = $this->catstartpage;

    if(!$config->jg_showsubcatcount && $subgesamtseiten == 1 || $count3 == 0) return;
?>
  <div class="jg_pagination">
<?php
    if($startpage == 0) $startpage = 1;
?>
    <a name="subcategory"></a>
<?php
    if($config->jg_showsubcatcount)
    {
      if($count3 == 1)
      {
?>
    <?php echo JText::_('JGS_THERE_IS') .' '.$count3.' '. JText::_('JGS_SUBCATEGORY_IN_CATEGORY'); ?>&nbsp;
<?php
      }
      elseif($count3 > 1)
      {
?>
    <?php echo JText::_('JGS_THERE_ARE') .' '.$count3.' '. JText::_('JGS_SUBCATEGORIES_IN_CATEGORY'); ?> 
<?php
      }
    }

    if($subgesamtseiten > 1)
    {
      //Ausgeben '<< Anfang'
      if($substartpage != 1)
      {
?>
    <br />
    <a href="<?php echo JRoute::_($this->viewcategory_url.$catid.'&startpage='.$startpage.'&substartpage=1'._JOOM_ITEMID)."#subcategory"; ?>">
      &laquo;&laquo;&nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_BEGIN'); ?>&nbsp;
    </a>
<?php
      }
      else
      {
?>
    <br />
    &laquo;&laquo;&nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_BEGIN'); ?>&nbsp;
<?php
      }
      // Ausgeben der Seite zurueck Funktion
      $subseiterueck = $substartpage - 1;
      if($subseiterueck > 0)
      {
?>
    <a href="<?php echo JRoute::_($this->viewcategory_url.$catid.'&startpage='.$startpage.'&substartpage='.$subseiterueck._JOOM_ITEMID)."#subcategory"; ?>">
      &laquo;&nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_PREVIOUS'); ?>&nbsp;
    </a>
<?php
      }
      else
      {
?>
    &laquo;&nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_PREVIOUS'); ?>&nbsp;
<?php
      }
      // Ausgeben der einzelnen Seiten
?>
      <?php echo Joom_GenPagination($this->viewcategory_url.$catid.'&startpage='.$startpage.'&substartpage=%u'._JOOM_ITEMID,$subgesamtseiten,$substartpage,"#subcategory"); ?>
<?php
      // Ausgeben der Seite vorwaerts Funktion
      $subseitevor = $substartpage + 1;
      if($subseitevor <= $subgesamtseiten)
      {
?>
    <a href="<?php echo JRoute::_($this->viewcategory_url.$catid.'&startpage='.$startpage.'&substartpage='.$subseitevor._JOOM_ITEMID)."#subcategory"; ?>">
      &nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_NEXT'); ?>&nbsp;&raquo;
    </a>
<?php
      }
      else
      {
  ?>
    &nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_NEXT'); ?>&nbsp;&raquo;
<?php
      }
      //Ausgeben 'Ende >>'
      if($substartpage != $subgesamtseiten)
      {
?>
    <a href="<?php echo JRoute::_($this->viewcategory_url.$catid.'&startpage='.$startpage.'&substartpage='.$subgesamtseiten._JOOM_ITEMID)."#subcategory"; ?>">
      &nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_END'); ?>&nbsp;&raquo;&raquo;
    </a>
<?php
      }
      else
      {
?>
    &nbsp;<?php echo JText::_('JGS_PAGENAVIGATION_END'); ?>&nbsp;&raquo;&raquo;
<?php
      }
    }
?>
  </div>
<?php
  }//End function Joom_ShowSubCategoryPageNav_HTML

}//End class HTML_Joom_Category
?>
