<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.viewdetails.php $
// $Id: joom.viewdetails.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC') or die ('Direct Access to this location is not allowed.');

class Joom_DetailView
{

  var $c_access;
  var $cattitle;
  var $catpath;
  var $slideshow;
  var $id;
  var $catid;
  var $imgtitle;
  var $imgauthor;
  var $imgtext;
  var $imgdate;
  var $imgcounter;
  var $imgvotes;
  var $imgvotesum;
  var $published;
  var $imgfilename;
  var $imgthumbname;
  var $checked_out;
  var $imgowner;
  var $approved;
  var $useruploaded;
  var $ordering;
  var $imgownerid;
  var $srcWidth_ori;
  var $srcHeight_ori;
  var $srcWidth;
  var $srcHeight;
  var $imgsize;
  var $fimgsize;
  var $originalimgsize;
  var $foriginalimgsize;
  var $fimgdate;
  var $rating;
  var $frating;
  var $rows;
  var $picture_src;
  var $toggler;
  var $slider;
  var $link;
  var $_mainframe;

  //JoomGallery paths
  var $joom_thumbnailpath;
  var $joom_picturepath;
  var $joom_originalpath;
  var $joom_thumbnailsource;
  var $joom_picturesource;
  var $joom_originalsource;
  var $joom_componenturl;
  var $joom_assetspath;


  /**
  * Class constructor
  *
  */
  function Joom_DetailView()
  {
    include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'joom.viewdetails.html.php');
    jimport('joomla.filesystem.file');
    $this->_mainframe = & JFactory::getApplication('site');
    $database         = & JFactory::getDBO();
    $user             = & JFactory::getUser();
    $config           = Joom_getConfig();

    $this->id        = JRequest::getInt('id', 0);
    $this->slideshow = trim(Joom_mosGetParam('jg_slideshow', '', 'post'));

    if($config->jg_showdetailaccordion)
    {
      $this->toggler = 'class="joomgallery-toggler"';
      $this->slider  = 'class="joomgallery-slider"';
    }
    else
    {
      $this->toggler = 'class="joomgallery-notoggler"';
      $this->slider  = '';
    }

    $database->setQuery(" SELECT
                            c.access AS access,
                            c.name AS name,
                            c.cid AS cid
                          FROM
                            #__joomgallery_catg AS c
                          LEFT JOIN
                            #__joomgallery AS a ON a.catid = c.cid
                          WHERE
                            a.id = $this->id
                        ");
    if(!$catinfo = $database->loadObject())
    {
      $this->_mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&view=gallery'._JOOM_ITEMID, false),
                                  JText::_('JGS_ALERT_NOT_ALLOWED_VIEW_PICTURE'));
    }

    $this->c_access = $catinfo->access;
    $this->cattitle = $catinfo->name;

    if($user->get('aid') < $this->c_access)
    {
      $this->_mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&view=gallery'._JOOM_ITEMID, false),
                                  JText::_('JGS_ALERT_NOT_ALLOWED_VIEW_PICTURE'));
    }

    $database->setQuery(" SELECT
                            a.id,
                            a.catid,
                            a.imgtitle,
                            a.imgauthor,
                            a.imgtext,
                            a.imgdate,
                            a.imgcounter,
                            a.imgvotes,
                            a.imgvotesum,
                            a.published,
                            a.imgfilename,
                            a.imgthumbname,
                            a.checked_out,
                            a.owner,
                            a.approved,
                            a.useruploaded,
                            a.ordering,
                            u.username,
                            ROUND(imgvotesum/imgvotes, 2) AS rating
                          FROM
                            #__joomgallery AS a
                          LEFT JOIN
                            #__users AS u ON u.id = a.owner
                          WHERE
                                a.id        = ".$this->id."
                            AND a.approved  = '1'
                            AND a.published = '1'
                        ");
    $result1 = $database->loadObject();

    $this->id           =$result1->id;
    $this->catid        =$result1->catid;
    $this->imgtitle     =$result1->imgtitle;
    $this->imgauthor    =$result1->imgauthor;
    $this->imgtext      =$result1->imgtext;
    $this->imgdate      =$result1->imgdate;
    $this->imgcounter   =$result1->imgcounter;
    $this->imgvotes     =$result1->imgvotes;
    $this->imgvotesum   =$result1->imgvotesum;
    $this->published    =$result1->published;
    $this->imgfilename  =$result1->imgfilename;
    $this->imgthumbname =$result1->imgthumbname;
    $this->checked_out  =$result1->checked_out;
    $this->imgowner     =$result1->owner;
    $this->approved     =$result1->approved;
    $this->useruploaded =$result1->useruploaded;
    $this->ordering     =$result1->ordering;
    $this->imgownerid   =$result1->username;
    $this->rating       =$result1->rating;

    if($this->published != 1 && $this->approved != 1)
    {
      $this->_mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&view=gallery'._JOOM_ITEMID, false),
                                  JText::_('JGS_ALERT_NOPICTURE_OR_NOTAPPROVED'));
    }

    $this->catpath = Joom_GetCatPath($this->catid);

    $this->joom_thumbnailpath   = $config->jg_paththumbs.$this->catpath;
    $this->joom_picturepath     = $config->jg_pathimages.$this->catpath;
    $this->joom_originalpath    = $config->jg_pathoriginalimages.$this->catpath;
    $this->joom_thumbnailsource = $config->jg_paththumbs.$this->catpath.$this->imgfilename;
    $this->joom_picturesource   = $config->jg_pathimages.$this->catpath.$this->imgfilename;
    $this->joom_originalsource  = $config->jg_pathoriginalimages.$this->catpath.$this->imgfilename;
    $this->joom_componenturl    = 'index.php?option=com_joomgallery';
    $this->joom_assetspath      = 'components/com_joomgallery/assets/';

    $this->picture_src = '';
    if($config->jg_watermark == 1)
    {
      $this->picture_src = _JOOM_LIVE_SITE.$this->joom_componenturl.
                           '&amp;func=watermark&amp;catid='.$this->catid.
                           '&amp;id='.$this->id.str_replace('&','&amp;',_JOOM_ITEMID);
    }
    else
    {
      $this->picture_src = _JOOM_LIVE_SITE.$this->joom_picturesource;
    }
    if(JFile::exists(JPATH_ROOT.DS.$this->joom_originalsource))
    {
      $imginfo_ori = getimagesize(JPath::clean(JPATH_ROOT.DS.$this->joom_originalsource));
      $this->originalimgsize  = filesize(JPath::clean(JPATH_ROOT.DS.$this->joom_originalsource));
      $this->foriginalimgsize = number_format($this->originalimgsize / 1024, 2, ",", ".")." KB";
    }
    else
    {
      $imginfo_ori[0]         = 0;
      $imginfo_ori[1]         = 0;
      $this->foriginalimgsize = JText::_('JGS_NO_ORIGINAL_FILE');
    }
    $imginfo        = getimagesize(JPath::clean(JPATH_ROOT.DS.$this->joom_picturesource));
    $imgsize        = filesize(JPath::clean(JPATH_ROOT.DS.$this->joom_picturesource));
    $this->fimgsize = number_format($imgsize / 1024, 2, ',', '.').' KB';

    $this->srcWidth_ori  = $imginfo_ori[0];
    $this->srcHeight_ori = $imginfo_ori[1];
    $this->srcWidth      = $imginfo[0];
    $this->srcHeight     = $imginfo[1];

    $this->fimgdate = strftime($config->jg_dateformat, $this->imgdate);
    $this->frating  = number_format($this->rating, 2, ',', '.');

    if($config->jg_secondorder != '' && $config->jg_thirdorder == '')
    {
      $orderclause = $config->jg_firstorder.', '.$config->jg_secondorder;
    }
    elseif($config->jg_secondorder != '' && $config->jg_thirdorder != '')
    {
      $orderclause = $config->jg_firstorder.', '.$config->jg_secondorder.', '.$config->jg_thirdorder;
    }
    else
    {
      $orderclause = $config->jg_firstorder;
    }

    $database->setQuery(" SELECT
                            *
                          FROM
                            #__joomgallery
                          WHERE
                                    catid = ".$this->catid."
                            AND approved  = '1'
                            AND published = '1'
                          ORDER BY
                            $orderclause
                        ");
    $this->rows = $database->loadObjectList();
?>
  <a name="joomimg"></a>
<?php


    if($config->jg_showdetailtitle == 1)
    {
      HTML_Joom_Detail::Joom_ShowPictureTitle_HTML();
    }
    if(  (($config->jg_bigpic == 1 && $user->get('aid') > 0) || ($config->jg_bigpic == 2))
        && !$this->slideshow
        && JFile::exists(JPATH_ROOT.DS.$this->joom_originalsource)
        && ($this->srcWidth_ori > $this->srcWidth && $this->srcHeight_ori > $this->srcHeight)
       )
    {
      $this->link = Joom_OpenImage($config->jg_bigpic_open, $this->id, $this->catpath, $this->catid,
                             $this->imgfilename, $this->imgtitle, $this->imgtext);
    }
    else
    {
      $this->link ='';
    }

    if($this->slideshow == false)
    {
      Joom_LightboxImages(0,1,0, $this->catid, $this->id);
    }

    $this->Joom_ShowPicture();

    if($config->jg_slideshow) $this->Joom_ShowSlideshow();

    $this->Joom_PagingCategory();

    if(!$this->slideshow)
    {
      Joom_LightboxImages(0,2,0, $this->catid, $this->id);
    }

    if($config->jg_minis)
    {
      $this->Joom_ShowMinis();
    }

    if($config->jg_showdetailtitle == 2)
    {
      HTML_Joom_Detail::Joom_ShowPictureTitle_HTML();
    }

    $modules = Joom_getModules('detailbtm');
    if(count($modules))
    {
      $document = &JFactory::getDocument();
      $renderer = $document->loadRenderer('module');
      $style    = -2;
      $params   = array('style'=>$style);
      foreach($modules as $module)
      {
?>
  <div class="jg_module">
<?php   if($module->showtitle)
        {
?>
    <div class="sectiontableheader">
      <h4>
        <?php echo $module->title; ?>&nbsp;
      </h4>
    </div>
<?php
        }
        echo $renderer->render($module, $params);
?>
  </div>
<?php
      }
    }

    if($config->jg_showdetail)
    {
      $this->Joom_ShowPictureData();
    }

    # Update View counter
    $this->imgcounter++;
    if($config->jg_watermark == 0)
    {
      $database->setQuery(" UPDATE
                              #__joomgallery
                            SET
                              imgcounter = ".$this->imgcounter."
                            WHERE
                              id = ".$this->id."
                          ");
      $database->query();
    }

    if($config->jg_slideshow)
    {
     HTML_Joom_Detail::Joom_ShowSlideshow_HTML();
    }

//******************************************************************************
//wenn die Slideshow aktiviert ist, sind die folgenden Abfragen hinfaellig
    if($this->slideshow) return;
//******************************************************************************

    $modules = Joom_GetModules('detailpane');
    if(count($modules))
    {
      HTML_Joom_Detail::Joom_ShowModules_HTML($modules);
    }

    if($config->jg_showexifdata && extension_loaded('exif') && function_exists('exif_read_data'))
    {
      include_once(JPATH_COMPONENT.DS.'includes'.DS.'exif'.DS.'joom.exifdata.php');
    }

    if($config->jg_showiptcdata )
    {
      include_once(JPATH_COMPONENT.DS.'includes'.DS.'iptc'.DS.'joom.iptcdata.php');
    }

    if($config->jg_showrating)
    {
      HTML_Joom_Detail::Joom_ShowVotingArea_HTML();
    }

    if($config->jg_bbcodelink)
    {
      $show_img = false;
      $show_url = false;

      if($config->jg_bbcodelink == 1 || $config->jg_bbcodelink == 3)
      {
        $show_img = true;
      }

      if($config->jg_bbcodelink == 2 || $config->jg_bbcodelink == 3)
      {
        $show_url = true;
      }

      HTML_Joom_Detail::Joom_ShowBBCodeLink_HTML($this->picture_src, $show_img, $show_url);
    }

    if($config->jg_showcomment)
    {
      //darf der Besucher Kommentare eingeben
      if($config->jg_anoncomment || (!$config->jg_anoncomment && $user->get('id')))
      {
        $allowcomment = 1;
      }
      else
      {
        $allowcomment = 0;
      }

      HTML_Joom_Detail::Joom_ShowCommentsHead_HTML();

      if($config->jg_showcommentsarea == 2)
      {
        HTML_Joom_Detail::Joom_ShowCommentsArea_HTML($allowcomment);
        HTML_Joom_Detail::Joom_BuildCommentsForm_HTML($allowcomment);
      }
      else
      {
        HTML_Joom_Detail::Joom_BuildCommentsForm_HTML($allowcomment);
        HTML_Joom_Detail::Joom_ShowCommentsArea_HTML($allowcomment);
      }

      HTML_Joom_Detail::Joom_ShowCommentsEnd_HTML();
    }

    if($config->jg_send2friend)
    {
      HTML_Joom_Detail::Joom_ShowSend2FriendArea_HTML();
    }
  }//End function Joom_DetailView


/**
* Paging of detailpics in detailview
*/
  function Joom_PagingCategory()
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();

    $id_cache          = array();
    $source_cache      = array();
    $title_cache       = array();
    $description_cache = array();
    $date_cache        = array();
    $hits_cache        = array();
    $rating1_cache     = array();
    $rating2_cache     = array();
    $author_cache      = array();
    $filesize_cache    = array();

    foreach($this->rows as $row1)
    {
      $id_cache[]            =                                       $row1->id;
      if($this->slideshow)
      {
        $title_cache[]       =                                       $row1->imgtitle;
        $description_cache[] = ($config->jg_showdetaildescription) ? $row1->imgtext : '';
        $date_cache[]        = ($config->jg_showdetaildatum)       ? $row1->imgdate : '';
        $hits_cache[]        = ($config->jg_showdetailhits)        ? $row1->imgcounter : '';
        $rating1_cache[]     = ($config->jg_showdetailrating)      ? $row1->imgvotes : '';
        $rating2_cache[]     = ($config->jg_showdetailrating)      ? $row1->imgvotesum : '';
        $filesize            = @filesize( JPath::clean(JPATH_ROOT.DS.$this->joom_picturepath.$row1->imgfilename));
        $filesize_cache[]    = ($config->jg_showdetailfilesize)    ? $filesize : '';
        if($config->jg_showdetailauthor)
        {
          if($row1->imgauthor != '')
          {
            $author_cache[]  = $row1->imgauthor;
          }
          else
          {
            $user_obj        = & JFactory::getUser($row1->owner);
            $author_cache[]  = $user_obj->get('username');
          }
        }
        else
        {
          $author_cache[]    = '';
        }
      }
      if($config->jg_watermark == 1)
      {
        $source_cache[] = _JOOM_LIVE_SITE.$this->joom_componenturl.'&func=watermark&id='
                          .$row1->id.'&catid='.$this->catid._JOOM_ITEMID;
      }
      else
      {
        $source_cache[] = _JOOM_LIVE_SITE.$this->joom_picturepath. $row1->imgfilename;
      }
    }
    $fileinfo = array(
                      'id'          => $id_cache,
                      'source'      => $source_cache,
                      'title'       => $title_cache,
                      'description' => $description_cache,
                      'date'        => $date_cache,
                      'hits'        => $hits_cache,
                      'rating1'     => $rating1_cache,
                      'rating2'     => $rating2_cache,
                      'filesize'    => $filesize_cache,
                      'author'      => $author_cache
                     );

    $act_key = array_search($this->id, $id_cache);
    $nid     = (isset($id_cache[$act_key + 1])) ? $id_cache[$act_key + 1] : 0;
    $pid     = (isset($id_cache[$act_key - 1])) ? $id_cache[$act_key - 1] : 0;
    unset($id_cache);

    HTML_Joom_Detail::Joom_PagingCategory_HTML($pid, $nid, $fileinfo, $act_key);
  }//End function Joom_PagingCategory


  function Joom_ShowPicture()
  {
    HTML_Joom_Detail::Joom_ShowPicture_HTML();
  }//End function Joom_ShowPicture


  function Joom_ShowSlideshow()
  {
    $config = Joom_getConfig();
      //Slideshow controls
?>
  <div class="jg_displaynone">
    <script type="text/javascript">
      document.write('</div>');
      document.write('<div class="jg_detailnavislide">');
    </script>
<?php
    if(!$this->slideshow)
    {
?>
    <a href="javascript:joom_startslideshow()" onMouseOver="return overlib('<?php echo JText::_('JGS_START',true); ?>', CAPTION, '<?php echo JText::_('JGS_SLIDESHOW',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
      <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/control_play.png' ;?>" alt="<?php echo JText::_('JGS_START'); ?>" class="pngfile jg_icon" /></a>
    <span onMouseOver="return overlib('<?php echo JText::_('JGS_PAUSE',true); ?>', CAPTION, '<?php echo JText::_('JGS_SLIDESHOW',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
      <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/control_pause_gr.png' ;?>" class="pngfile jg_icon" alt="<?php echo JText::_('JGS_FULLSIZE_TOOLTIP_CAPTION'); ?>" />
    </span>
    <a href="javascript:photo.goon()" style="visibility:hidden;display:inline;"></a>
    <span onMouseOver="return overlib('<?php echo JText::_('JGS_STOP',true); ?>', CAPTION, '<?php echo JText::_('JGS_SLIDESHOW',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
      <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/control_stop_gr.png' ;?>" class="pngfile jg_icon" alt="<?php echo JText::_('JGS_FULLSIZE_TOOLTIP_CAPTION'); ?>" />
    </span>
<?php
      if($config->jg_showsliderepeater)
      {
?>
    <span onMouseOver="return overlib('<?php echo JText::_('JGS_REPEAT_ENDLESS',true); ?>', CAPTION, '<?php echo JText::_('JGS_SLIDESHOW',true); ?>', BELOW, RIGHT);" onmouseout="return nd();" >
      <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/control_repeat_gr.png' ;?>" class="pngfile jg_icon" alt="<?php echo JText::_('JGS_FULLSIZE_TOOLTIP_CAPTION'); ?>" />
    </span>
<?php
      }
    }
    else
    {
?>
    <a href="javascript:photo.pause()" id="jg_pause" style="visibility:visible;display:inline;" onMouseOver="return overlib('<?php echo JText::_('JGS_PAUSE',true); ?>', CAPTION, '<?php echo JText::_('JGS_SLIDESHOW',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
      <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/control_pause.png' ;?>" alt="<?php echo JText::_('JGS_PAUSE'); ?>" class="pngfile jg_icon" /></a>
    <a href="javascript:photo.goon()" id="jg_goon" style="visibility:hidden;display:inline;"></a>
    <a href="javascript:photo.stop()" onMouseOver="return overlib('<?php echo JText::_('JGS_STOP',true); ?>', CAPTION, '<?php echo JText::_('JGS_SLIDESHOW',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
      <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/control_stop.png' ;?>" alt="<?php echo JText::_('JGS_STOP'); ?>" class="pngfile jg_icon" /></a>
<?php
      if($config->jg_showsliderepeater)
      {
?>
    <a href="javascript:photo.repeatstatus()" id ="jg_repeat" onMouseOver="return overlib('<?php echo JText::_('JGS_REPEAT_ENDLESS',true); ?>', CAPTION, '<?php echo JText::_('JGS_SLIDESHOW',true); ?>', BELOW, RIGHT);" onmouseout="return nd();">
      <img src="<?php echo _JOOM_LIVE_SITE.$this->joom_assetspath.'images/control_repeat.png' ;?>" alt="<?php echo JText::_('JGS_STOP'); ?>" class="pngfile jg_icon" /></a>
<?php
      }
    }
?>
  </div>
  <script type="text/javascript">
    document.write('<div class="jg_displaynone">');
  </script>
  <div class="jg_detailnavislide">
    <div class="jg_no_script">
      <?php echo JText::_('JGS_SLIDESHOW_NO_SCRIPT'); ?>
    </div>
  </div>
  <script type="text/javascript">
    document.write('</div>');
  </script>
<?php
  }//End function Joom_ShowSlideshow


  function Joom_ShowIcons()
  {
    $config   = Joom_getConfig();
    $user     = & JFactory::getUser();
    $database = & JFactory::getDBO();
    jimport('joomla.filesystem.file');

    // Parameter fuer Icons:
    $showZoomIcon       = 0;
    $showDownloadIcon   = 0;
    $showTagIcon        = 'none';
    $showFavouritesIcon = 0;

    if(    JFile::exists(JPATH_ROOT.DS.$this->joom_originalsource)
        && ($this->srcWidth_ori  > $this->srcWidth)
        && ($this->srcHeight_ori > $this->srcHeight)
      )
    {
      if( ($config->jg_bigpic == 1 && $user->get('aid') > 0) || ($config->jg_bigpic == 2) )
      {
        $showZoomIcon = 1;
      }
      elseif ($config->jg_bigpic == 1 && $user->get('aid') < 1)
      {
        $showZoomIcon = -1;
      }
    }

    if(   (JFile::exists(JPATH_ROOT.DS.$this->joom_originalsource))
        || ($config->jg_downloadfile != 1)
      )
    {

      if(    (($config->jg_showdetaildownload == 1) && ($user->get('aid') >= 1))
          || (($config->jg_showdetaildownload == 2) && ($user->get('aid') == 2))
          ||  ($config->jg_showdetaildownload == 3)
        )
      {
        $showDownloadIcon = 1;
      }
      elseif(($config->jg_showdetaildownload == 1) && ($user->get('aid') < 1))
      {
        $showDownloadIcon = -1;
      }
    }

    if(   (!$this->slideshow && $config->jg_nameshields && $user->get('username'))
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
    }
    if($config->jg_nameshields && $user->get('username') && !$this->slideshow)
    {
      if($count < 1)
      {
        $showTagIcon = 'save';
      }
      elseif($count == 1)
      {
        $showTagIcon = 'delete';
      }
    }
    elseif($config->jg_nameshields && !$user->get('username')
            && $config->jg_show_nameshields_unreg)
    {
      $showTagIcon = 'login';
    }

    if($config->jg_favourites == 1)
    {
      if(   (($config->jg_showdetailfavourite == 0) && ($user->get('aid') >= 1))
         || (($config->jg_showdetailfavourite == 1) && ($user->get('aid') == 2))
         || (($config->jg_usefavouritesforpubliczip == 1) && ($user->get('aid') < 1))
        )
      {
        if(     $config->jg_usefavouritesforzip == 1
           || (($config->jg_usefavouritesforpubliczip == 1) && ($user->get('aid') < 1))
          )
        {
          $showFavouritesIcon = 2;
        }
        else
        {
          $showFavouritesIcon = 1;
        }
      }
      elseif($config->jg_favouritesshownotauth == 1)
      {
        if($config->jg_usefavouritesforzip == 1)
        {
          $showFavouritesIcon = -2;
        }
        else
        {
          $showFavouritesIcon = -1;
        }
      }
    }
    if(!$this->slideshow)
    {
      HTML_Joom_Detail::Joom_ShowIcons_HTML($showZoomIcon, $showDownloadIcon,
                                            $showTagIcon, $showFavouritesIcon);
    }
  }//End function Joom_ShowIcons


  function Joom_ShowMinis()
  {
    HTML_Joom_Detail::Joom_ShowMinis_HTML($this->rows);
  }//End function Joom_ShowMinis


  function Joom_ShowPictureData()
  {
    HTML_Joom_Detail::Joom_ShowPictureData_HTML();
  }//End function Joom_ShowPictureData


}//End class Joom_DetailView

?>
