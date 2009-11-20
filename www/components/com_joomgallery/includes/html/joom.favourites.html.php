<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/html/joom.favourites.html.php $
// $Id: joom.favourites.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_Favourites
{

  function Joom_ShowFavourites_HTML1( $rows, $showDownloadIcon )
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    $num_rows = ceil(count($rows ) / $config->jg_toplistcols);
    $index = 0;
    $line  = 1;

?>
  <div class="jg_favview">
    <div class="sectiontableheader">
      <?php echo $this->Output('HEADING'); ?> 
    </div>
    <div class="jg_fav_switchlayout">
      <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=switchlayout'._JOOM_ITEMID); ?>">
        <?php echo JText::_('JGS_FAV_SWITCH_LAYOUT'); ?> 
      </a>
    </div>
    <div class="jg_fav_clearlist">
      <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=removeall'._JOOM_ITEMID); ?>">
        <?php echo JText::_('JGS_FAV_REMOVE_ALL'); ?> 
      </a>
    </div>
<?php
  $count_rows = count($rows);
  if($count_rows)
  {
    for($row_count=0; $row_count < $num_rows; $row_count++)
    {
      $line++;
      $linecolor = ($line % 2) + 1;
?>
    <div class="jg_row <?php if ($linecolor == 1) echo "sectiontableentry1"; else echo "sectiontableentry2";?>">
<?php
      for($col_count = 0; ($col_count < $config->jg_toplistcols) && ($index < $count_rows); $col_count++)
      {
        $row = $rows[$index];
?>
      <div class="jg_favelement">
<?php
          $catpath = Joom_GetCatPath($row->catid);
          if(($config->jg_showdetailpage == 0 && $user->get('aid')!= 0) 
             || $config->jg_showdetailpage == 1)
          {
            $link = Joom_OpenImage($config->jg_detailpic_open, $row->id, $catpath, 
                                   $row->catid, $row->imgfilename, $row->imgtitle, $row->imgtext);
          }
          else
          {
            $link = "javascript:alert('".JText::_('JGS_ALERT_NO_DETAILVIEW_FOR_GUESTS',true)."')";
          }
?>
        <div class="jg_favelem_photo">
          <a href="<?php echo $link; ?>">
            <img src="<?php echo _JOOM_LIVE_SITE . $config->jg_paththumbs . $catpath . $row->imgthumbname; ?>" class="jg_photo" alt="<?php echo $row->imgtitle; ?>" />
          </a>
        </div>
        <div class="jg_favelem_txt">
          <ul>
            <li>
              <b><?php echo $row->imgtitle; ?></b>
            </li>
            <li>
              <?php echo JText::_('JGS_CATEGORY') . ":" ?> 
              <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$row->catid._JOOM_ITEMID); ?>">
                <?php echo $row->name; ?> 
              </a>
            </li>
<?php
          if($config->jg_showauthor)
          {
            if($row->imgauthor)
            {
              $authorowner = $row->imgauthor;
            }
            elseif($config->jg_showowner)
            {
              $authorowner = Joom_GetDisplayName($row->imgowner);
            }
            else
            {
              $authorowner = JText::_('JGS_NO_DATA');
            }
?>
            <li>
              <?php echo JText::_('JGS_AUTHOR') . ": ".$authorowner; ?> 
            </li>
<?php
          }
          if($config->jg_showhits )
          {
?>
            <li>
              <?php echo JText::_('JGS_HITS') . ": " . $row->imgcounter; ?> 
            </li>
<?php
          }
          if($config->jg_showcatrate)
          {
?>
            <li>
<?php
            if($row->imgvotes > 0)
            {
              $fimgvotesum = number_format( $row->imgvotesum / $row->imgvotes, 2, ',', '' );
              $frating = $fimgvotesum.' ('.$row->imgvotes.' '.JText::_('JGS_VOTES').')';
            }else
            {
              $frating = '(' . JText::_('JGS_NO_RATINGS') .')';
            }
?>
              <?php echo JText::_('JGS_RATING') . ": " . $frating; ?> 
            </li>
<?php
          }
          if($config->jg_showcatcom)
          {
            # Check how many comments exist
            $database->setQuery(" SELECT 
                                    COUNT(*)
                                  FROM 
                                    #__joomgallery_comments
                                  WHERE 
                                           cmtpic = '$row->id' 
                                    AND approved  = '1' 
                                    AND published = '1' 
                                ");
            $comments = $database->loadResult();
?>
            <li>
<?php
            switch($comments)
            {
              case 0:
?>
              <?php echo JText::_('JGS_NO_COMMENTS'); ?> 
<?php
                break;
              case 1:
?>
              <?php echo $comments.' '.JText::_('JGS_COMMENT'); ?> 
<?php
                break;
              default:
?>
              <?php echo $comments.' '.JText::_('JGS_COMMENTS'); ?> 
<?php
                break;
            }
?>
            </li>
<?php
          }
?>
            <li>
<?php
            // Download Icon
            if($showDownloadIcon == 1)
            {
?>
              <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=download&catid='.$row->catid.'&id='.$row->id._JOOM_ITEMID); ?>"
                  onMouseOver="return overlib('<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
                <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/download.png' ;?>" border="0" width="16" height="16" alt="<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" />
              </a>
<?php
            }
            elseif($showDownloadIcon == -1)
            {
?>
              <span onMouseOver="return overlib('<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_TEXT_LOGIN',true); ?>', CAPTION, '<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
                <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/download_gr.png' ;?>" alt="<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
              </span>
<?php
            }
?>
              <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=removepicture&id='.$row->id._JOOM_ITEMID); ?>"
                  onMouseOver="return overlib('<?php echo $this->Output('REMOVE_TOOLTIP_TEXT'); ?>', CAPTION, '<?php echo $this->Output('REMOVE_TOOLTIP_CAPTION'); ?>', BELOW, RIGHT);" onmouseout="return nd();">
                <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/basket_remove.png' ;?>" alt="<?php echo $this->Output('REMOVE_TOOLTIP_CAPTION');; ?>" class="pngfile jg_icon" />
              </a>
            </li>
          </ul>
        </div>
      </div>
<?php
      $index++;
      }
?>
      <div class="jg_clearboth"></div>
    </div>
<?php
      }
    }
    else
    {
?>
    <div class="jg_txtrow">
      <div class="sectiontableentry1">
        <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
      <?php echo $this->Output('NO_PICS'); ?>
      </div>
    </div>
<?php
    }
?>
    <div class="sectiontableheader">
      &nbsp;
    </div>
  </div>
<?php
  }//End function Joom_ShowFavourites_HTML1


  function Joom_ShowFavourites_HTML2($rows, $showDownloadIcon)
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

?>
  <div class="sectiontableheader">
    <?php echo $this->Output('HEADING'); ?> 
  </div>
  <div class="jg_fav_switchlayout">
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=switchlayout'._JOOM_ITEMID); ?>">
      <?php echo JText::_('JGS_FAV_SWITCH_LAYOUT'); ?> 
    </a>
  </div>
  <div class="jg_fav_clearlist">
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=removeall'._JOOM_ITEMID); ?>">
      <?php echo JText::_('JGS_FAV_REMOVE_ALL'); ?> 
    </a>
  </div>
  <div class="sectiontableheader">
    <div class="jg_up_entry">
      <div class="jg_up_ename">
        <?php echo JText::_('JGS_PICTURE_NAME'); ?> 
      </div>
      <div class="jg_up_ehits">
        <?php echo JText::_('JGS_HITS'); ?> 
      </div>
      <div class="jg_up_ecat">
        <?php echo JText::_('JGS_CATEGORY'); ?> 
      </div>
      <div class="jg_up_eact">
        <?php echo JText::_('JGS_ACTION'); ?> 
      </div>
    </div>
  </div>
  <?php
    $k = 0;
    if(count($rows))
    {
      foreach($rows as $row)
      {
        $k = 1 - $k;
        $p = $k+1;
        $catpath = Joom_GetCatPath($row->catid);
  ?>
  <div class="<?php echo "sectiontableentry".$p; ?>">
    <div class="jg_up_entry">
<?php
        if($row->approved)
        {
          $link = Joom_OpenImage($config->jg_detailpic_open,$row->id,$catpath,$row->catid,$row->imgfilename,$row->imgtitle,$row->imgtext);
        }
        if($config->jg_showminithumbs)
        {
?>
      <div class="jg_up_ename">
<?php
          if( $row->imgthumbname != '')
          {
            if($row->approved)
            {
?>
        <a href="<?php echo $link; ?>">
<?php
            }
?>
          <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->imgthumbname; ?>" border="0" height="30" alt="" />
<?php
            if($row->approved)
            {
?>
        </a>
<?php
            }
          }
        }
        else
        {
?>
        <div class="jg_floatleft">
          <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon"  alt="arrow" />
        </div>
<?php
        }
        if($row->approved)
        {
?>
        <a href="<?php echo $link; ?>"> 
<?php
        }
?>
        <?php echo $row->imgtitle; ?> 
<?php
        if($row->approved)
        {
?>
        </a>
<?php
        }
?>
      </div>
      <div class="jg_up_ehits">
        <?php echo $row->imgcounter; ?> 
      </div>
      <div class="jg_up_ecat">
        <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$row->catid._JOOM_ITEMID); ?>">
          <?php echo Joom_CategoryPathLink( $row->catid, false ); ?> 
        </a>
      </div>
<?php
        // Download Icon
        if($showDownloadIcon == 1)
        {
?>
      <div class="jg_up_esub1">
        <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=download&catid='.$row->catid.'&id='.$row->id._JOOM_ITEMID); ?>"
          onMouseOver="return overlib('<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
        <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/download.png' ;?>" border="0" width="16" height="16" alt="<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" /></a>
      </div>
<?php
        }
        elseif($showDownloadIcon == -1)
        {
?>
      <div class="jg_up_esub1" onMouseOver="return overlib('<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_TEXT_LOGIN',true); ?>', CAPTION, '<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
        <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/download_gr.png' ;?>" alt="<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
      </div>
<?php
        }
?>
      <div class="jg_up_esub2">
        <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=removepicture&id='.$row->id._JOOM_ITEMID); ?>"
          onMouseOver="return overlib('<?php echo $this->Output('REMOVE_TOOLTIP_TEXT');; ?>', CAPTION, '<?php echo $this->Output('REMOVE_TOOLTIP_CAPTION');; ?>', BELOW, RIGHT);" onmouseout="return nd();">
        <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/basket_remove.png' ;?>" alt="<?php echo $this->Output('REMOVE_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" /></a>
      </div>
<?php
        if($row->imgowner && $row->imgowner == $user->get('id'))
        {
?>
      <div class="jg_up_esub3">
        <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=editpic&uid='.$user->get('id').'&id='.$row->id._JOOM_ITEMID); ?>" title="<?php echo JText::_('JGS_EDIT'); ?>">
          <img src= "<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/edit.png" border="0" width="16" height="16" alt="<?php echo JText::_('JGS_EDIT'); ?>" class="pngfile jg_icon" />
        </a>
      </div>
      <div class="jg_up_esub4">
        <a href="javascript:if (confirm('<?php echo JText::_('JGS_ALERT_SURE_DELETE_SELECTED_ITEM',true); ?>')){ location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=deletepic&uid='.$user->get('id').'&id='.$row->id._JOOM_ITEMID,false);?>';}" title="<?php echo JText::_('JGS_DELETE'); ?>">
          <img src="<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/edit_trash.png" border="0" width="16" height="16" alt="<?php echo JText::_('JGS_DELETE'); ?>" class="pngfile jg_icon" />
        </a>
      </div>
<?php
        }
?>
    </div>
  </div>
<?php
      }
    }
    else
    {
    $p = $k+1;
?>
  <div class="jg_txtrow">
    <div class="<?php echo "sectiontableentry".$p; ?>">
      <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
    <?php echo $this->Output('NO_PICS'); ?> 
    </div>
  </div>
<?php
    }
?>
  <div class="sectiontableheader">
    &nbsp;
  </div>
<?php
  }//End function Joom_ShowFavourites_HTML2


  function Joom_Favourites_CreateZip_HTML($zipname,$zipsize)
  {
?>
  <div class="sectiontableheader">
    <?php echo $this->Output('DOWNLOAD'); ?> 
  </div>
  <div class="jg_createzip">
    <a href="<?php echo $zipname; ?>"><?php echo JText::_('JGS_ZIP_DOWNLOAD_READY'); ?></a>
    <br />
    <a href="<?php echo $zipname; ?>">
      <img src="<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/disk.png" border="0" align="middle" alt="<?php echo JText::_('JGS_ZIP_DOWNLOAD'); ?>" />
    </a>
    <br />
    <?php echo JText::_('JGS_ZIP_FILESIZE_PART_ONE').' '.$zipsize.' '.JText::_('JGS_ZIP_FILESIZE_PART_TWO'); ?> 
    <br /><br />
    <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=removeall'._JOOM_ITEMID); ?>">
      <?php echo $this->Output('CREATEZIP_REMOVE_ALL'); ?> 
    </a>
  </div>
  <div class="sectiontableheader">
    &nbsp;
  </div>
<?php
  }//End function Joom_Favourites_CreateZip_HTML



  function Joom_Favourites_CreateZip_Error_HTML($zipfile)
  {
?>
  <div class="sectiontableheader">
    <?php echo $this->Output('DOWNLOAD'); ?> 
  </div>
  <div class="jg_createzip">
    <i><?php echo JText::_('JGS_ZIP_DOWNLOAD_ERROR'); ?></i>
    <br />
    <p>
    <?php echo $zipfile->errorInfo(true); ?> 
    </p>
  </div>
  <div class="sectiontableheader">
    &nbsp;
  </div>
<?php
  }//End function Joom_Favourites_CreateZip_Error_HTML

}//End class HTML_Joom_Favourites
?>
