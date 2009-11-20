<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/classes/cooliris.class.php $
// $Id: cooliris.class.php 449 2009-06-14 11:57:04Z aha $
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

// based on code example at http://developer.cooliris.com/rss/php_rss.html

class Joom_Cooliris 
{

  var $thumbnailpath;
  var $picturepath;
  var $origpicturepath;
  var $absolut_origpicturepath;

  var $catid;
  var $catpath;
  var $catallpages;
  var $currentpage;
  var $rows;

  /**
   * dynamically output of  RSS feeds to display them in Cooliris
   *
   * @param integer $catid
   * @param integer $catallpages, Count of pages, if navigation active, otherwise 1
   * @param integer $currentpage, current site if navigation active, otherwise 0
   * @param array $rows, Pictures
   */
  function Joom_GetXMLFeed($catid, $catallpages, $currentpage, $rows)
  {
    $config = Joom_getConfig();

    header('Content-type: text/xml');

    $this->thumbnailpath           = _JOOM_LIVE_SITE . $config->jg_paththumbs;
    $this->picturepath             = _JOOM_LIVE_SITE . $config->jg_pathimages;
    $this->origpicturepath         = _JOOM_LIVE_SITE . $config->jg_pathoriginalimages;
    $this->absolut_origpicturepath = JPATH_ROOT . DS . $config->jg_pathoriginalimages;

    $this->catid        = $catid;
    $this->catpath      = Joom_GetCatPath($this->catid);
    $this->catallpages  = $catallpages;
    $this->currentpage  = $currentpage;
    $this->rows         = $rows;

    ob_clean();
    echo $this->Joom_GetRSS();
    exit;
  }//End function Joom_GetXMLFeed


  /**
   * create RSS in Cooliris
   *
   */
  function Joom_GetRSS()
  {
    $rss  = $this->Joom_GetRSSHeader();
    $rss .= $this->Joom_GetRSSItems();
    $rss .= $this->Joom_GetRSSFooter();
    return $rss;
  }//End function Joom_GetRSS


  /**
   * create RSS-Header
   *
   */
  function Joom_GetRSSHeader()
  {
    $mainframe = & JFactory::getApplication('site');

    $rssHeader   = '<?xml version="1.0" encoding="utf-8" standalone="yes"?> 
                    <rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/"
                      xmlns:atom="http://www.w3.org/2005/Atom">
                    <channel>
                    <title>'.$mainframe->getCfg( 'sitename' ).'</title>
                    <link>'._JOOM_LIVE_SITE.'</link>
                    <description>JoomGallery</description>
                    ';

    if($this->catallpages == 1)
    {
      $rssHeader .= '<atom:link rel="self" href="'
                      .htmlspecialchars(_JOOM_LIVE_SITE.'index.php?option=com_joomgallery&func=viewcategory&catid='
                      .$this->catid.'&cooliris=1').'" />
                    ';
    }
    else
    {
      $rssHeader .= '<atom:link rel="self" href="'
                    .htmlspecialchars(_JOOM_LIVE_SITE.'index.php?option=com_joomgallery&func=viewcategory&catid='
                    .$this->catid.'&startpage='.$this->currentpage.'&cooliris=1').'" />
                    ';
    }

    //only if showing more than one site analyze prev-next links
    //und output them 
    if($this->catallpages > 1)
    {
      //prev link only if currentpage > 1
      if ($this->currentpage > 1) {
        $prevpage   =$this->currentpage-1;
        $rssHeader .= '<atom:link rel="previous" href="'
                      .htmlspecialchars(_JOOM_LIVE_SITE.'index.php?option=com_joomgallery&func=viewcategory&catid='
                      .$this->catid.'&startpage='.$prevpage.'&cooliris=1').'" />
                      ';
      }

      //next link only if not reached last site
      if($this->currentpage < $this->catallpages)
      {
        $nextpage   = $this->currentpage + 1;
        $rssHeader .= '<atom:link rel="next" href="'
                      .htmlspecialchars(_JOOM_LIVE_SITE.'index.php?option=com_joomgallery&func=viewcategory&catid='
                      .$this->catid.'&startpage='.$nextpage.'&cooliris=1').'" />
                      ';
      }
    }

    return $rssHeader;
  }//End function Joom_GetRSSHeader


  /**
   * create RSS-Footer
   *
   */
  function Joom_GetRSSFooter()
  {
    $rssFooter  = '</channel>'; 
    $rssFooter .= '</rss>';
    return $rssFooter;
  }//End function Joom_GetRSSFooter


  /**
   * create RSS-Items
   *       *
   */
  function Joom_GetRSSItems() 
  {
    global $func;
    $rssItems = '';
    $rss_thumbnailpath    = $this->thumbnailpath.$this->catpath;
    $rss_picturepath      = $this->picturepath.$this->catpath;
    $rss_origpicturepath  = $this->absolut_origpicturepath.$this->catpath;

    foreach($this->rows as $picture)
    {
      $id = $picture->id;

      if(defined('_JEXEC'))
      {
        $title = $this->Joom_GetBareText($picture->imgtitle);
        $text  = $this->Joom_GetBareText($picture->imgtext);
      }
      else
      {
        $title = utf8_encode($this->Joom_GetBareText($picture->imgtitle));
        $text  = utf8_encode($this->Joom_GetBareText($picture->imgtext));
      }

      $link   = JRoute::_('index.php?option=com_joomgallery&func=detail&id='.$picture->id);
      $name   = $picture->imgauthor;
      $img_id = $picture->id;

      $link_big = 1;

      //check if original picture exists, otherwise use detail picture
      if(file_exists(JPath::clean($rss_origpicturepath.$picture->imgfilename)))
      {
        $func = 'detail';
      }

      $contenturl = Joom_OpenImage($link_big, $picture->id, $this->catpath, 
                                   $this->catid, $picture->imgfilename, 
                                   $picture->imgtitle, $picture->imgtext);
      $contenturl = str_replace('" target="_blank', '', $contenturl);

      $rssItems .= "<item>
                    <title>".$title."</title>
                    <link>".$link."</link>
                    <media:thumbnail url=\"".$rss_thumbnailpath.$picture->imgthumbname ."\" />
                    <media:content url=\"". $contenturl ."\" />
                    <media:description>".$text."</media:description>
                    <guid isPermaLink=\"false\">"._JOOM_LIVE_SITE.'joomgallery-'.$picture->id."</guid>
                    </item>\n";
    }
    return $rssItems;
  }//End function Joom_GetRSSItems


  /**
   * create bare text
   *
 * @param string $text
   */
  function Joom_GetBareText($text)
  {
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $text = trim($text);
    return $text;
  }//End function Joom_GetBareText

}//End class Joom_Cooliris

?>
