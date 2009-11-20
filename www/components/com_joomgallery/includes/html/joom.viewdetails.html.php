<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/html/joom.viewdetails.html.php $
// $Id: joom.viewdetails.html.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined ('_JEXEC') or die('Direct Access to this location is not allowed.');

class HTML_Joom_Detail
{

  function Joom_PagingCategory_HTML($pid, $nid, $fileinfo, $act_key)
  {
    $config      = Joom_getConfig();
    $number_pics = count($this->rows);

    if($config->jg_slideshow && $this->slideshow)
    {
      include(JPATH_COMPONENT.DS.'includes'.DS.'joom.slideshow.php');
    }
?>
  <div class="jg_detailnavi">
    <div class="jg_detailnaviprev">
<?php
    if($pid > 0 && !$this->slideshow)
    {
      //previous pic
      $backlink = JRoute::_($this->joom_componenturl.
                            '&func=detail&id='.$pid._JOOM_ITEMID).'#joomimg';
?>
      <form  name="form_jg_back_link" action="<?php echo $backlink;?>">
        <input type="hidden" name="jg_back_link" readonly="readonly" />
      </form>
      <a href="<?php echo $backlink; ?> ">
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/arrow_left.png' ;?>"
          alt="<?php echo JText::_('JGS_PREVIOUS_IMAGE'); ?>" class="pngfile jg_icon" /></a>
      <a href="<?php echo $backlink; ?> ">
        <?php echo JText::_('JGS_PREVIOUS_IMAGE'); ?></a>
<?php
      if($config->jg_showdetailnumberofpics)
      {
?>
      <?php echo '<br />('.JText::_('JGS_PICTURE').' '.$act_key.' '.JText::_('JGS_OF').
                         ' '.$number_pics.')'; ?>&nbsp;
<?php
      }
?>
<?php
    }
    else
    {
?>
      &nbsp;
<?php
    }
?>
    </div>
<?php
    $this->Joom_ShowIcons();
?>
    <div class="jg_detailnavinext">
<?php
    if($nid > 0 && !$this->slideshow)
    {
      //next pic
      $act_key     = ($act_key + 2);
      $forwardlink = JRoute::_($this->joom_componenturl.
                               '&func=detail&id='.$nid._JOOM_ITEMID).'#joomimg';
?>
      <form name="form_jg_forward_link" action="<?php echo $forwardlink;?>">
        <input type="hidden" name="jg_forward_link" readonly="readonly" />
      </form>
      <a href="<?php echo $forwardlink; ?>">
        <?php echo JText::_('JGS_NEXT_IMAGE'); ?></a>
      <a href="<?php echo $forwardlink; ?>">
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/arrow_right.png' ;?>"
          alt="<?php echo JText::_('JGS_NEXT_IMAGE'); ?>" class="pngfile jg_icon" /></a>
<?php
      if($config->jg_showdetailnumberofpics)
      {
?>
      <?php echo '<br />('.JText::_('JGS_PICTURE').' '.$act_key.' '.JText::_('JGS_OF').
                         ' '.$number_pics.')'; ?>&nbsp;
<?php
      }
    }
    else
    {
?>
      &nbsp;
<?php
    }
?>
    </div>
  </div>
<?php
  }//End function Joom_PagingCategory_HTML


  function Joom_ShowPicture_HTML()
  {
    $config     = Joom_getConfig();
    $database   = & JFactory::getDBO();
    $user       = & JFactory::getUser();
    jimport('joomla.filesystem.file');


    if($config->jg_resizetomaxwidth)
    {
      $ratio  = max($this->srcWidth,$this->srcHeight);
      $ratio  = ($ratio/$config->jg_maxwidth);
      $ratio  = max($ratio, 1.0);
      $width  = (int)($this->srcWidth / $ratio);
      $height = (int)($this->srcHeight / $ratio);
    }
    else
    {
      $width  = $this->srcWidth;
      $height = $this->srcHeight;
    }

    if($config->jg_nameshields && $user->get('username') && !$this->slideshow)
    {
      $document = &JFactory::getDocument();
      $document->addScript(_JOOM_LIVE_SITE.$this->joom_assetspath.
                           'js/wz_dragdrop/js/wz_dragdrop.js');
    }
?>
  <div style="text-align:center;">
<?php
    $linkOverImage = false;
    // Link zu Originalbild anzeigen:
    if( ( ($config->jg_bigpic == 1 && $user->get('aid') > 0) || ($config->jg_bigpic == 2) )
        && !$this->slideshow
        && JFile::exists(JPATH_ROOT.DS.$this->joom_originalsource)
        && ($this->srcWidth_ori > $this->srcWidth && $this->srcHeight_ori > $this->srcHeight)
      )
    {
      // Originalbild ueber Detailbild verlinkt, wenn keine Namensschilder verwendet werden:
      if(    !$config->jg_nameshields
         || (!$config->jg_show_nameshields_unreg && !$user->get('username'))
        )
      {
        $linkOverImage = true;
?>
    <a href="<?php echo $this->link;?>">
<?php
      }
    }

    //Namensschilder nur in der DB suchen, wenn Slideshow nicht aktiviert und
    //Berechtigung zum Ansehen vorliegt
    //ebenso den DIV fuer die Namensschilder nur in diesen Faellen ausgeben
    if(   !$this->slideshow && $config->jg_nameshields && ($user->get('username'))
       || ($config->jg_nameshields_unreg && !$user->get('username'))
      )
    {
      $database->setQuery(" SELECT
                              COUNT(nid)
                            FROM
                              #__joomgallery_nameshields
                            WHERE
                                  npicid  = ".$this->id."
                              AND nuserid = ".$user->get('id')."
                          ");
      $count = $database->loadResult();

      $database->setQuery(" SELECT
                              *
                            FROM
                              #__joomgallery_nameshields
                            WHERE
                              npicid = ".$this->id."
                          ");
      $rows = $database->loadObjectList();

      $database->setQuery(" SELECT
                              MAX(nzindex)
                            FROM
                              #__joomgallery_nameshields
                            WHERE
                              npicid = ".$this->id."
                          ");
      $zindex = $database->loadResult();

      if(!$zindex)
      {
        $zindex = 500;
      }
      $length = strlen($user->get('username')) * $config->jg_nameshields_width;
      if($rows != NULL)
      {
        $output = '';
        $i      = 1;
        foreach($rows as $row)
        {
          $length2 = Joom_GetDisplayNameLength($row->nuserid)*$config->jg_nameshields_width;
          $output .= '<div id="id'.$i.'" style="position:absolute; top:'.$row->nxvalue.'px; left:'.$row->nyvalue.'px; width:'.$length2.'px; z-index:'.$row->nzindex.'" class="nameshield">';
          $output .= "\n        ";
          $output .= Joom_GetDisplayName($row->nuserid, false);
          $output .= "\n      ";
          $output .= '</div>';
          $output .= "\n";
          $i++;
        }
      }
?>
    <div id="pic" style="position:relative; margin:10px auto; width:<?php echo ++$this->srcWidth; ?>px; height: <?php echo ++$this->srcHeight; ?>px; z-index:1;">
<?php
    }
//TODO cbe joomgallerytab

    if($config->jg_disable_rightclick_detail == 1)
    {
      $action = 'onmouseover="javascript:joom_hover();" onmouseout="javascript:joom_hover();"';
    }
    else
    {
      $action = '';
    }
?>
      <img src="<?php echo $this->picture_src ?>" class="jg_photo" id="jg_photo_big" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php echo $this->imgtitle;?>" <?php echo $action;?> />
<?php
    // eg. Namensschild platzieren:
    if($config->jg_nameshields && $user->get('username'))
    {
      if(!$this->slideshow && $count < 1 && $user->get('username'))
      {
?>
        <div id="u1" style="position:absolute; top:0px; left:0px; width:<?php echo $length; ?>px; z-index: 500;" class="nameshield">
          <?php echo $user->get('username'); ?>&nbsp;
        </div>
<?php
      }
    }
    if(   ($config->jg_nameshields_unreg && $config->jg_nameshields && !$user->get('username'))
       || ($config->jg_nameshields && $user->get('username'))
      )
    {
      if(isset($rows) && $rows != NULL)
      {
?>
      <?php echo $output; ?>
<?php
      }
    }
    if(    !$this->slideshow && $config->jg_nameshields && ($user->get('username'))
       || ($config->jg_nameshields_unreg && !$user->get('username'))
      )
    {
?>
    </div>
<?php
    }
    if($config->jg_nameshields && $user->get('username') && !$this->slideshow)
    {
?>
    <form name="nameshieldform" action="<?php echo JRoute::_($this->joom_componenturl.'&func=savenameshield'._JOOM_ITEMID); ?>" target="_top" method="post">
      <input type="hidden" name="id"     value="<?php echo $this->id; ?>" />
      <input type="hidden" name="uid"    value="<?php echo $user->get('id'); ?>" />
      <input type="hidden" name="xvalue" value="" />
      <input type="hidden" name="yvalue" value="" />
      <input type="hidden" name="zindex" value="<?php echo $zindex-1; ?>" />
      <input type="hidden" name="length" value="<?php echo $length; ?>" />
      <input type="hidden" name="height" value="<?php echo $config->jg_nameshields_height; ?>" />
    </form>
<?php
    }
    if($linkOverImage)
    {
?>
    </a>
<?php
    }
//    $this->Joom_ShowIcons($link);
    if(!$this->slideshow && $config->jg_nameshields && $user->get('username'))
    {
?>
    <script type="text/javascript">
    SET_DHTML("u1"+CURSOR_MOVE+MAXOFFLEFT+0+MAXOFFRIGHT+
              <?php echo $this->srcWidth-$length; ?>+MAXOFFTOP+0+MAXOFFBOTTOM+
              <?php echo $this->srcHeight; ?>);
    </script>
<?php
    }
?>
  </div>
<?php
  }//End function Joom_ShowPicture_HTML


  function Joom_ShowMinis_HTML($rows)
  {
    $config = Joom_getConfig();
?>
  <div class="jg_minis">
<?php
    if($config->jg_motionminis == 2)
    {
?>
    <div id="motioncontainer">
      <div id="motiongallery">
        <div style="white-space:nowrap;" id="trueContainer">
<?php
    }
    if(count($rows) > 0)
    {
      foreach($rows as $row1)
      {
?>
          <a href="<?php echo JRoute::_($this->joom_componenturl.'&func=detail&id='.$row1->id._JOOM_ITEMID).'#joomimg'; ?>">
<?php

        if($row1->id == $this->id)
        {
?>
            <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_thumbnailpath.$row1->imgthumbname; ?>" name="jg_mini_akt" class="jg_minipic" alt="<?php echo $row1->imgtitle; ?>" id="jg_mini_<?php echo $row1->id; ?>" /></a>
<?php
        }
        elseif($row1->id != $this->id)
        {
?>
            <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_thumbnailpath.$row1->imgthumbname; ?>" class="jg_minipic" alt="<?php echo $row1->imgtitle; ?>" id="jg_mini_<?php echo $row1->id; ?>" /></a>
<?php
        }
      }
    }
    if($config->jg_motionminis == 2)
    {
?>
        </div>
      </div>
    </div>
<?php
    }
?>
  </div>
<?php
  }//End function Joom_ShowMinis_HTML


  function Joom_ShowPictureTitle_HTML()
  {
?>
  <div>
    <h3 class="jg_imgtitle" id="jg_photo_title">
      <?php echo Joom_BBDecode($this->imgtitle); ?>&nbsp;
    </h3>
  </div>
<?php
  }//End function Joom_ShowPictureTitle_HTML


  function Joom_ShowPictureData_HTML()
  {
    $config = Joom_getConfig();
    $user   = & JFactory::getUser();

    $ii = 0;
    if($this->imgtitle == '')
    {
      $imgtitle = '&nbsp;';
    }
    else
    {
      $imgtitle = $this->imgtitle;
    }
?>
  <div class="jg_details">
    <div class="sectiontableheader">
      <h4 <?php echo $this->toggler; ?>>
        <?php echo JText::_('JGS_PICTUREDETAILS'); ?>&nbsp;
      </h4>
    </div>
    <div <?php echo $this->slider; ?>>
      <p>
<?php
    if($config->jg_showdetaildescription)
    {
      if($this->imgtext == '')
      {
        $imgtext = '&nbsp;<br />';
      }
      else
      {
        $imgtext = $this->imgtext;
      }
?>
      <div class="sectiontableentry1">
        <div class="jg_photo_left">
          <?php echo JText::_('JGS_DESCRIPTION'); ?>:
        </div>
        <div class="jg_photo_right" id="jg_photo_description">
          <?php echo Joom_BBDecode($imgtext); ?>&nbsp;
        </div>
      </div>
<?php
    }
    if($config->jg_showdetaildatum)
    {
      if($this->fimgdate == '')
      {
        $fimgdate = '&nbsp;';
      }
      else
      {
        $fimgdate = $this->fimgdate;
      }
      $ii++;
?>
      <div class="sectiontableentry<?php echo ($ii%2)+1; ?>">
        <div class="jg_photo_left">
          <?php echo JText::_('JGS_DATE'); ?>:
        </div>
        <div class="jg_photo_right" id="jg_photo_date">
          <?php echo $fimgdate; ?>&nbsp;
        </div>
      </div>
<?php
    }
    if($config->jg_showdetailhits)
    {
      if($this->imgcounter == '')
      {
        $imgcounter = '&nbsp;';
      }
      else
      {
        $imgcounter = $this->imgcounter;
      }
      $ii++;
?>
      <div class="sectiontableentry<?php echo ($ii%2)+1; ?>">
        <div class="jg_photo_left">
          <?php echo JText::_('JGS_HITS'); ?>:
        </div>
        <div class="jg_photo_right" id="jg_photo_hits">
          <?php echo $imgcounter; ?>&nbsp;
        </div>
      </div>
<?php
    }
    if($config->jg_showdetailrating)
    {
      $ii++;
?>
      <div class="sectiontableentry<?php echo ($ii%2)+1; ?>">
        <div class="jg_photo_left">
          <?php echo JText::_('JGS_RATING'); ?>:
        </div>
        <div class="jg_photo_right" id="jg_photo_rating">
<?php
    if($this->imgvotes > 0)
    {
      if($this->imgvotes == 1)
      {
        $ratings = $this->frating.' ('.$this->imgvotes.' '.  JText::_('JGS_ONE_VOTE') . ')';
      }
      else
      {
        $ratings = $this->frating.' ('.$this->imgvotes.' '.  JText::_('JGS_VOTES') . ')';
      }
    }
    else
    {
      $ratings = JText::_('JGS_NO_VOTES');
    }
?>
          <?php echo $ratings; ?>&nbsp;
        </div>
      </div>
<?php
    }
    if($config->jg_showdetailfilesize)
    {
      $ii++;
?>
      <div class="sectiontableentry<?php echo ($ii%2)+1; ?>">
        <div class="jg_photo_left">
          <?php echo JText::_('JGS_FILESIZE'); ?>:
        </div>
        <div class="jg_photo_right" id="jg_photo_filesize">
          <?php echo $this->fimgsize; ?>
          (<?php echo $this->srcWidth; ?> x <?php echo $this->srcHeight; ?> px)&nbsp;
        </div>
      </div>
<?php
    }
    if($config->jg_showdetailauthor)
    {
      $ii++;
?>
      <div class="sectiontableentry<?php echo ($ii%2)+1; ?>">
        <div class="jg_photo_left">
          <?php echo JText::_('JGS_AUTHOR'); ?>:
        </div>
        <div class="jg_photo_right" id="jg_photo_author">
<?php
      if($this->imgauthor != '')
      {
?>
          <?php echo $this->imgauthor; ?>&nbsp;
<?php
      }
      else
      {
?>
          <?php echo Joom_GetDisplayName($this->imgowner); ?>&nbsp;
<?php
      }
?>
        </div>
      </div>
<?php
    }
    if(   is_file(JPath::clean(JPATH_ROOT.DS.$this->joom_originalsource))
       && $config->jg_showoriginalfilesize == 1 && !$this->slideshow
      )
    {
      $ii++;
?>
      <div class="sectiontableentry<?php echo ($ii%2)+1; ?>">
        <div class="jg_photo_left">
          <?php echo JText::_('JGS_FILESIZE_ORIGINAL'); ?>:
        </div>
        <div class="jg_photo_right" id="jg_photo_filesize">
          <?php echo $this->foriginalimgsize; ?>
          (<?php echo $this->srcWidth_ori; ?> x <?php echo $this->srcHeight_ori; ?> px)&nbsp;
        </div>
      </div>
<?php
    }
?>
      &nbsp;
      </p>
    </div>
  </div>
<?php
  }//End function Joom_ShowPictureData_HTML


  function Joom_ShowIcons_HTML($showZoomIcon, $showDownloadIcon, $showTagIcon,
                               $showFavouritesIcon)
  {
    $user = & JFactory::getUser();
?>
    <div class="jg_iconbar">
<?php
    // Fullview Icon
    if($showZoomIcon == 1)
    {
?>
      <a href="<?php echo $this->link;?>" onMouseOver="return overlib('<?php echo JText::_('JGS_FULLSIZE_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_FULLSIZE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/zoom.png' ;?>" class="pngfile jg_icon" alt="<?php echo JText::_('JGS_FULLSIZE_TOOLTIP_CAPTION'); ?>" /></a>
<?php
    }
    elseif($showZoomIcon == -1)
    {
?>
      <span onMouseOver="return overlib('<?php echo JText::_('JGS_FULLSIZE_TOOLTIP_TEXT_LOGIN',true); ?>', CAPTION, '<?php echo JText::_('JGS_FULLSIZE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/zoom_gr.png' ;?>" class="pngfile jg_icon" alt="<?php echo JText::_('JGS_FULLSIZE_TOOLTIP_CAPTION'); ?>" />
      </span>
<?php
    }
    // Download Icon:
    if($showDownloadIcon == 1)
    {
?>
      <a href="<?php echo JRoute::_($this->joom_componenturl.'&func=download&catid='.$this->catid.'&id='.$this->id._JOOM_ITEMID); ?>"
        onMouseOver="return overlib('<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/download.png' ;?>" alt="<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" /></a>
<?php
    }
    elseif($showDownloadIcon == -1)
    {
?>
      <span onMouseOver="return overlib('<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_TEXT_LOGIN',true); ?>', CAPTION, '<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/download_gr.png' ;?>" alt="<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
      </span>
<?php
    }
    // name-tagging icons:
    if($showTagIcon == 'save')
    {
?>
      <a href="javascript:joom_getcoordinates();" onMouseOver="return overlib('<?php echo JText::_('JGS_NAMESHIELD_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_NAMESHIELD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/tag_add.png' ;?>" alt="<?php echo JText::_('JGS_NAMESHIELD_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" /></a>
<?php
    }
    elseif($showTagIcon == 'delete')
    {
?>
      <a href="javascript:if(confirm('<?php echo JText::_('JGS_ALERT_SURE_DELETE_NAMESHIELD_',true); ?>')){ location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=deletenameshield&npicid='.$this->id.'&nuserid='.$user->get('id')._JOOM_ITEMID,false);?>';}"
        onMouseOver="return overlib('<?php echo JText::_('JGS_NAMESHIELD_DELETE_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_NAMESHIELD_DELETE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/tag_delete.png' ;?>" alt="<?php echo JText::_('JGS_NAMESHIELD_DELETE_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" /></a>
<?
    }
    elseif($showTagIcon == 'login')
    {
?>
      <span onMouseOver="return overlib('<?php echo JText::_('JGS_NAMESHIELD_UNREGISTERED_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_NAMESHIELD_UNREGISTERED_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/tag_add_gr.png' ;?>"  alt="<?php echo JText::_('JGS_NAMESHIELD_UNREGISTERED_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" />
      </span>
<?php
    }
    // Favourites Icon:
    if($showFavouritesIcon == 1)
    {
?>
      <a href="<?php echo JRoute::_($this->joom_componenturl.'&func=addpicture&id='.$this->id._JOOM_ITEMID); ?>"
        onMouseOver="return overlib('<?php echo JText::_('JGS_FAV_ADD_PICTURE_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_FAV_ADD_PICTURE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/star.png' ;?>" alt="<?php echo JText::_('JGS_FAV_ADD_PICTURE_TOOLTIP_CAPTION'); ?>" class="pngfile jg_icon" />
      </a>
<?php
    }
    elseif($showFavouritesIcon == -1)
    {
?>
      <span onMouseOver="return overlib('<?php echo JText::_('JGS_FAV_ADD_PICTURE_NOT_ALLOWED_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/star_gr.png' ;?>" alt="<?php echo JText::_('JGS_DOWNLOAD_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
      </span>
<?php
    }
    elseif($showFavouritesIcon == 2)
    {
?>
      <a href="<?php echo JRoute::_($this->joom_componenturl.'&func=addpicture&id='.$this->id._JOOM_ITEMID); ?>"
        onMouseOver="return overlib('<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/basket_put.png' ;?>" alt="<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
      </a>
<?php
    }
    elseif($showFavouritesIcon == -2)
    {
?>
      <span onMouseOver="return overlib('<?php echo JText::_('JGS_ZIP_ADD_PICTURE_NOT_ALLOWED_TOOLTIP_TEXT',true); ?>', CAPTION, '<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_CAPTION',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
        <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/basket_put_gr.png' ;?>" alt="<?php echo JText::_('JGS_ZIP_ADD_PICTURE_TOOLTIP_CAPTION'); ?>"  class="pngfile jg_icon" />
      </span>
<?php
    }
?>
    </div>
<?php
  }//End function Joom_ShowIcons_HTML


  function Joom_ShowSlideshow_HTML()
  {
?>
  <form name="jg_slideshow_form" target="_top" method="post" action="">
  <input type="hidden" name="jg_number" value="<?php echo $this->id;?>" readonly="readonly" />
<?php
    if(!$this->slideshow)
    {
?>
  <input type="hidden" name="jg_slideshow" value="true" readonly="readonly" />
<?php
    }
?>
  </form>
<?php
    if($this->slideshow)
    {
?>
  <script language = "javascript" type = "text/javascript">
      if(document.getElementById("jg_photo_big").style.filter == '') {
        photo.filter = true;
      }
      photo.start();
  </script>
<?php
    }
    else
    {
?>
  <script language = "javascript" type = "text/javascript">
      function joom_startslideshow() {
        document.jg_slideshow_form.submit();
      }
  </script>
<?php
    }
  }//End function Joom_ShowSlideshow_HTML


  function Joom_ShowBBCodeLink_HTML($pic_src, $img=1, $url=1)
  {
    $config   = Joom_getConfig();
    $pic_addr = JRoute::_(_JOOM_LIVE_SITE.$this->joom_componenturl.'&func=detail&id='.$this->id._JOOM_ITEMID).'#joomimg';
    $line     = 0;
?>
  <div class="jg_bbcode">
    <div class="sectiontableheader">
      <h4 <?php echo $this->toggler; ?>>
        <?php echo JText::_('JGS_BBCODE_SHARE'); ?>&nbsp;
      </h4>
    </div>
    <div <?php echo $this->slider; ?>>
      <p>
<?php
    if($img)
    {
      $line++;
?>
        <div class="sectiontableentry<?php echo $line; ?>">
          <div class="jg_bbcode_left">
            <?php echo JText::_('JGS_BBCODE_IMG'); ?>:
          </div>
          <div class="jg_bbcode_right">
            <input class="inputbox jg_img_BB_box" size="50" value="[IMG]<?php echo $pic_src; ?>[/IMG]" readonly="readonly" onClick="select()" type="text">
          </div>
        </div>
<?php
    }

    if($url)
    {
      $line++;
?>
        <div class="sectiontableentry<?php echo $line; ?>">
          <div class="jg_bbcode_left">
            <?php echo  JText::_('JGS_BBCODE_LINK'); ?>:
          </div>
          <div class="jg_bbcode_right">
            <input class="inputbox jg_img_BB_box" size="50" value="[URL]<?php echo $pic_addr; ?>[/URL]" readonly="readonly" onClick="select()" type="text">
          </div>
        </div>
<?php
    }
?>
        &nbsp;
      </p>
    </div>
  </div>
<?php
  }//End function Joom_ShowBBCodeLink_HTML


/**
 * Displays modules in the pane area
 *
 * @param array array with the data of the modules
 */
  function Joom_ShowModules_HTML($modules)
  {
    $config   = Joom_getConfig();
    $document = &JFactory::getDocument();
    $renderer = $document->loadRenderer('module');
    $style    = -2;
    $params   = array('style'=>$style);

    foreach($modules as $module)
    {
?>
<div class="jg_panemodule">
  <div class="sectiontableheader">
    <h4 <?php echo $this->toggler; ?>>
      <?php echo $module->title; ?>&nbsp;
    </h4>
  </div>
  <div <?php echo $this->slider; ?>>
<?php
      echo $renderer->render($module, $params);
?>
  </div>
</div>
<?php
    }
  }//End function Joom_ShowModules_HTML


  function Joom_ShowVotingArea_HTML()
  {
    $id     = $this->id;
    $config = Joom_getConfig();
    $user   = & JFactory::getUser();

?>
  <div class="jg_voting">
    <div class="sectiontableheader">
      <h4 <?php echo $this->toggler; ?>>
        <?php echo JText::_('JGS_PICTURE_RATING'); ?>&nbsp;
      </h4>
    </div>
    <div <?php echo $this->slider; ?>>
      <div class="sectiontableentry1">
<?php
    if($config->jg_onlyreguservotes && $user->get('aid') == 0)
    {
?>
        <?php echo JText::_('JGS_LOGIN_FIRST'); ?>&nbsp;
<?php
    }
    elseif($config->jg_onlyreguservotes && $this->imgownerid == $user->get('id'))
    {
?>
        <?php echo JText::_('JGS_NO_RATING_ON_OWN_PICTURES'); ?>&nbsp;
<?php
    }
    else
    {
?>
        <form name="ratingform" action="<?php echo JRoute::_($this->joom_componenturl.'&func=votepic&id='.$this->id._JOOM_ITEMID); ?>" target="_top" method="post">
          <p>
            1 (<?php echo JText::_('JGS_BAD'); ?>)
<?php
      $selitem = floor($config->jg_maxvoting / 2) + 1;
      for($i = 1; $i <= $config->jg_maxvoting; $i++)
      {
        if($i == $selitem)
        {
          $checked = 'checked="checked"';
        }
        else
        {
          $checked = '';
        }
?>
            <input type="radio" value="<?php echo $i; ?>" name="imgvote" <?php echo $checked; ?> />
<?php
      }
      $i--;
?>
          <?php echo $i; ?> (<?php echo JText::_('JGS_GOOD'); ?>)&nbsp;
          </p>
          <p>
            <input class="button" type="submit" value="<?php echo JText::_('JGS_VOTE'); ?>" name="<?php echo JText::_('JGS_VOTE'); ?>" />
          </p>
        </form>
<?php
    }
?>
      </div>
    </div>
  </div>
<?php
  }//End function Joom_ShowVotingArea_HTML


  function Joom_ShowCommentsHead_HTML()
  {
    $config = Joom_getConfig();
?>
  <div class="jg_commentsarea">
    <div class="sectiontableheader">
      <h4 <?php echo $this->toggler; ?>>
        <?php echo JText::_('JGS_EXISTING_COMMENTS'); ?>&nbsp;
      </h4>
    </div>
    <div <?php echo $this->slider; ?>>
      <table width="100%" border="0" cellspacing="0px" cellpadding="0px">
<?php
  }//End function Joom_ShowCommentsHead_HTML


  function Joom_ShowCommentsEnd_HTML()
  {
?>
      </table>
    </div>
  </div>
<?php
  }//End function Joom_ShowCommentsEnd_HTML


  function Joom_ShowCommentsArea_HTML($allowcomment)
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    $linecolor = 0;
?>
      <a name="joomcomments"></a>
<?php
    if(     $user->get('username')
       || (!$user->get('username') && $config->jg_showcommentsunreg == 0)
      )
    {
      if($config->jg_showcommentsarea == 1)
      {
        $order = 'DESC';
      }
      else
      {
        $order = 'ASC';
      }
      $database->setQuery(" SELECT
                              cm.*
                            FROM
                              #__joomgallery_comments AS cm
                            WHERE
                                  cm.cmtpic    = ".$this->id."
                              AND cm.published = '1'
                              AND cm.approved  = '1'
                            ORDER BY
                              cmtid $order
                          ");
      $result1 = $database->LoadObjectList();
      $count   = count($result1);
      if($count > 0)
      {
?>
      <tr class="sectiontableheader">
        <td class="jg_cmtl">
          <?php echo JText::_('JGS_AUTHOR'); ?>&nbsp;
        </td>
        <td class="jg_cmtr">
          <?php echo JText::_('JGS_COMMENT'); ?>&nbsp;
        </td>
      </tr>
<?php
        foreach($result1 as $row1)
        {
          $linecolor = ($linecolor % 2) + 1;
?>
      <tr class="<?php echo "sectiontableentry".$linecolor; ?>">
        <td class="jg_cmtl">
<?php
        if($row1->userid > 0)
        {
?>
          <?php echo Joom_GetDisplayName($row1->userid, false); ?>&nbsp;
<?php
        }
        elseif($row1->cmtname == JText::_('JGS_GUEST'))
        {
?>
          <?php echo $row1->cmtname; ?>&nbsp;
<?php
        }
        else
        {
?>
      <?php echo $row1->cmtname.' ('.JText::_('JGS_GUEST').') '; ?>&nbsp;
<?php
        }
        // Editor logged?
        if(($user->get('gid') ==  20) || ($user->get('gid') ==  24) || ($user->get('gid') == 25))
        {
?>
          <div class="jg_cmticons">
            <a href="http://www.db.ripe.net/whois?form_type=simple&full_query_string=&searchtext=<?php echo $row1->cmtip;?>&do_search=Search" target="_blank">
              <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath; ?>images/ip.gif" alt="<?php echo $row1->cmtip; ?>" title="<?php echo $row1->cmtip; ?>" hspace="3" border="0" /></a>
            <a href="<?php echo JRoute::_($this->joom_componenturl.'&func=deletecomment&cmtid='.$row1->cmtid._JOOM_ITEMID); ?>">
              <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath; ?>images/del.gif" alt="<?php echo JText::_('JGS_DELETE_COMMENT'); ?>" hspace="3" border="0" /></a>
          </div>
<?php
        }
?>
        </td>
<?php
        $signtime = strftime($config->jg_dateformat, $row1->cmtdate);
        $origtext = $row1->cmttext;
        $origtext = Joom_ProcessText($origtext);
        if($config->jg_bbcodesupport) $origtext = Joom_BBDecode($origtext);
        if($config->jg_smiliesupport)
        {
          $smileys = Joom_GetSmileys();
          foreach($smileys as $i => $sm)
          {
            $origtext = str_replace ($i, '<img src="'.$sm.'" border="0" alt="'.$i.'" title="'.$i.'" />', $origtext);
          }
        }
?>
        <td class="jg_cmtr">
          <span class="small">
            <?php echo JText::_('JGS_COMMENT_ADDED'); ?>: <?php echo $signtime; ?>&nbsp;
            <hr />
          </span>
          <?php echo stripslashes($origtext); ?>&nbsp;
        </td>
      </tr>
<?php
        }
      }
      else
      {
?>
      <tr class="<?php echo "sectiontableentry".$linecolor; ?>">
        <td colspan="2" class="jg_cmtf">
          <p>
          <?php echo JText::_('JGS_NO_EXISTING_COMMENTS'); ?>&nbsp;
<?php
        if($allowcomment == 1)
        {
?>
          <?php echo ' ' . JText::_('JGS_WRITE_FIRST_COMMENT'); ?>&nbsp;
<?php
        }
?>
          </p>
        </td>
      </tr>
<?php
      }
    }
    else
    {
?>
      <tr class="<?php echo "sectiontableentry".$linecolor; ?>">
        <td colspan="2" class="jg_cmtf">
          <p>
          <?php echo JText::_('JGS_NO_COMMENTS_FOR_UNREG'); ?>&nbsp;
          </p>
        </td>
      </tr>
<?php
    }
  }//End function Joom_ShowCommentsArea_HTML


  function Joom_BuildCommentsForm_HTML($allowcomment)
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    $linecolor = 0;
    if(!$allowcomment)
    {
?>
        <tr class="sectiontableentry1">
          <td class="jg_cmtf" colspan="2">
            <?php echo JText::_('JGS_NO_COMMENTS_BY_GUEST'); ?>&nbsp;
          </td>
        </tr>
<?php
      return;
    }
    if($config->jg_secimages == 2 || ($config->jg_secimages == 1 && $user->get('aid') < 1))
    {
      if(file_exists(JPATH_ROOT.DS.'components'.DS.'com_easycaptcha'.DS.'class.easycaptcha.php'))
      {
        include_once(JPATH_ROOT.DS.'components'.DS.'com_easycaptcha'.DS.'class.easycaptcha.php');
        $captcha = new easyCaptcha();
      }
      else
      {
        $config->jg_secimages = 0;
      }
    }
?>
      <a name="joomcommentform"></a>
<?php
    $bbcodestatus = array(JText::_('JGS_BBCODE_OFF'), JText::_('JGS_BBCODE_ON'));
    if(isset($_COOKIE['sessioncookie']) && $_COOKIE['sessioncookie'] != '' )
    {
      $cryptSessionID = md5($_COOKIE['sessioncookie']);
      $database->setQuery(" SELECT
                              username
                            FROM
                              #__session
                            WHERE
                              session_ID = ".$cryptSessionID."
                          ");
      $cmtname = $database->LoadResult();
      //$cmtname = $result2->cmtname;
    }
?>
        <form name="commentform" action="<?php echo JRoute::_($this->joom_componenturl.'&func=commentpic&id='.$this->id._JOOM_ITEMID); ?>" target="_top" method="post">
<?php
    if($config->jg_secimages==2 || ($config->jg_secimages==1 && $user->get('aid') <1))
    {
?>
          <input type="hidden" name="jg_captcha_id" value="<?php echo $captcha->getCaptchaId();?>" />
<?php
    }
    if(!$user->get('username'))
    {
      $ip = $_SERVER['REMOTE_ADDR'];
?>
          <input type="hidden" name="cmtip" value="<?php echo $ip; ?>" />
<?php
    }
    $linecolor = ($linecolor % 2) + 1;
?>
        <tr class="sectiontableentry1">
          <td class="jg_cmtl">
            <?php echo $user->get('username'); ?>&nbsp;
<?php
    if($user->get('aid') < 1)
    {
      if($config->jg_namedanoncomment)
      {
?>
            <input type="text" class="inputbox" name="cmtname" value="<?php echo JText::_('JGS_GUEST'); ?>" />
<?php
      }
      else
      {
?>
            <input type="hidden" class="inputbox" name="cmtname" value="<?php echo JText::_('JGS_GUEST'); ?>" />
<?php
      }
    }
    else
    {
?>
            <input type="hidden" class="inputbox" name="cmtname" value="<?php echo $user->get('username'); ?>" />
<?php
    }
    if($config->jg_smiliesupport)
    {
?>
            <div style="padding:0.4em 0;">
<?php
      $count   = 1;
      $smileys = Joom_GetSmileys();
      foreach($smileys as $i=>$sm)
      {
?>
              <a href="javascript:joom_smilie('<?php echo $i; ?>')" title="<?php echo $i; ?>">
                <img src="<?php echo $sm; ?>" border="0" alt="<?php echo $sm; ?>" /></a><?php
        if($count%4 == 0)
        {
?>
              <br />
<?php
        }
        $count++;
      }
?>
            </div>
<?php
    }
?>
            <p class="small">
              <?php echo JText::_('JGS_BBCODE_IS'); ?>
              <b><?php echo $bbcodestatus[$config->jg_bbcodesupport]; ?></b>.
            </p>
          </td>
          <td class="jg_cmtr">
<?php
    if($config->jg_smiliesupport)
    {
      $rows = 8;
    }
    else
    {
      $rows = 4;
    }
?>
            <p>
              <textarea cols="40" rows="<?php echo $rows; ?>" name="cmttext" class="inputbox" onfocus="jg_comment_active=1" onchange="jg_comment_active=0" onblur="jg_comment_active=0"></textarea>
            </p>
          </td>
        </tr>
<?php
    if($config->jg_secimages == 2 || ($config->jg_secimages == 1 && $user->get('aid') < 1))
    {
?>
        <tr class="<?php echo "sectiontableentry".$linecolor; ?>">
          <td class="jg_cmtl">
            &nbsp;
          </td>
          <td class="jg_cmtr">
            <img src="<?php echo $captcha->getImageUrl(); ?>" alt="<?php echo $captcha->getAltText(); ?>" border="0" id="jg_captcha_image" />
          </td>
        </tr>
<?php
      $linecolor = ($linecolor % 2) + 1;
?>
        <tr class="sectiontableentry1">
          <td class="jg_cmtl">
            <?php echo JText::_('JGS_ENTER_CODE'); ?>&nbsp;
          </td>
          <td class="jg_cmtr">
            <input class="inputbox" type='text' value="" name='jg_code' />
            <?php echo $captcha->getReloadButton("jg_captcha_image");?>
            <?php echo $captcha->getReloadCode();?>
          </td>
        </tr>
<?php
    }
?>
        <tr class="sectiontableentry1">
          <td class="jg_cmtl">
          </td>
          <td class="jg_cmtr">
            <p>
              <input type="button" name="send" value="<?php echo JText::_('JGS_COMMENT_SEND'); ?>" class="button" onclick="joom_validatecomment()" />
              &nbsp;
              <input type="reset" value="<?php echo JText::_('JGS_DELETE'); ?>" name="reset" class="button" />
            </p>
          </td>
        </tr>
        </form>
<?php
  }//End function Joom_BuildCommentsForm_HTML


  function Joom_ShowSend2FriendArea_HTML()
  {
    $user   = & JFactory::getUser();
    $config = Joom_getConfig();
?>
  <div class="jg_send2friend">
    <div class="sectiontableheader">
      <h4 <?php echo $this->toggler; ?>>
        <?php echo JText::_('JGS_SEND_TO_FRIEND'); ?>&nbsp;
      </h4>
    </div>
    <div <?php echo $this->slider; ?>>
      <p>

<?php
    if ($user->get('id'))
    {
?>
      <p />
      <form name="send2friend" action="<?php echo JRoute::_($this->joom_componenturl.'&func=send2friend&id='.$this->id._JOOM_ITEMID); ?>" target=_top method="post">
      <input type="hidden" name="from2friendname" value="<?php echo $user->get('name'); ?>" />
      <input type="hidden" name="from2friendemail" value="<?php echo $user->get('email'); ?>" />
      <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
      <table width="100%" border="0" cellspacing="0px" cellpadding="0px">
        <tr class="sectiontableentry1">
          <td class="jg_s2fl">
            <?php echo JText::_('JGS_FRIENDS_NAME'); ?>:
          </td>
          <td class="jg_s2fr">
            <input type="text" name="send2friendname" size="15" class="inputbox" onfocus="jg_comment_active=1" onreset="jg_comment_active=0" onchange="jg_comment_active=0" onblur="jg_comment_active=0" />
          </td>
        </tr>
        <tr class="sectiontableentry2">
          <td class="jg_s2fl">
            <?php echo JText::_('JGS_FRIENDS_MAIL'); ?>:
          </td>
          <td class="jg_s2fr">
            <input type="text" name="send2friendemail" size="15" class="inputbox" onfocus="jg_comment_active=1" onreset="jg_comment_active=0" onchange="jg_comment_active=0" onblur="jg_comment_active=0" />
          </td>
        </tr>
        <tr class="sectiontableentry1">
          <td class="jg_s2fl">
            &nbsp;
          </td>
          <td class="jg_s2fr">
            <p>
            <input type="button" name="send" value="<?php echo JText::_('JGS_EMAILSEND'); ?>" class="button" onclick="joom_validatesend2friend()" />&nbsp;
            </p>
          </td>
        </tr>
      </table>
      </form>
<?php
    }
    else
    {
?>
      <div class="sectiontableentry1">
        <?php echo JText::_('JGS_LOGIN_FIRST'); ?>&nbsp;
      </div>
<?php
    }
?>
      </p>
    </div>
  </div>
<?php
  }//End function Joom_ShowSend2FriendArea_HTML

}//End class HTML_Joom_Detail
?>
