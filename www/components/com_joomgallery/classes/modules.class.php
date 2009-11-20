<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/classes/modules.class.php $
// $Id: modules.class.php 449 2009-06-14 11:57:04Z aha $
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

class joommodule
{

  var $_config    = array();
  var $_stag      = null;
  var $_cmtcount  = '';

  function setConfig($config)
  {
    $this->_config = $config;
  }//End function setConfig

  function addconfig($key, $value='')
  {
    $this->_config[$key] = $value;
  }//End function addconfig

  function addshorttag($tag)
  {
    $this->_stag = $tag;
  }//End function addshorttag

  function cmttest($only_comments='')
  {
    if($only_comments=='')
    {
      if(   $this->_config['sorting'] == 'cmtcount ASC'
         || $this->_config['sorting'] == 'cmtcount DESC'
         || $this->_config['sorting'] == 'co.cmtdate ASC'
         || $this->_config['sorting'] == 'co.cmtdate DESC'
         || $this->_config['showcmtdate'] == 1
         || $this->_config['showcmttext'] == 1
         || $this->_config['showcmtcount'] == 1)
    {
         return true;
      }
      else
      {
        return false;
      }
    }
    elseif(   $this->_config['sorting'] == 'co.cmtdate ASC'
           || $this->_config['sorting'] == 'commentrand'
           || $this->_config['sorting'] == 'co.cmtdate DESC')
           {
      return true;
    }
    else
    {
      return false;
    }
  }//End function cmttest


  function dynctest($cid=0)
  {
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    if($this->_config['dynamiccats'] == 1)
    {
      if($this->_config['option'] == 'com_joomgallery' 
         && $this->_config['func'] == 'viewcategory' 
         && $this->_config['catid']!=0 && $this->_config['catid']!= ''
        )
      {
        if($cid == 1)
        {
          return $this->_config['catid'];
        }
        else
        {
          return true;
        }
      }
      elseif(   $this->_config['option'] == 'com_joomgallery' 
             && $this->_config['func'] == 'detail' && $this->_config['imgid']!=0 
             && $this->_config['imgid']!= ''
            )
      {
        if($cid == 1)
        {
          $query  = " SELECT 
                        p.catid 
                      FROM 
                        #__joomgallery AS p
                      LEFT JOIN 
                        #__joomgallery_catg AS c ON c.cid = p.catid
                      WHERE 
                            c.published = '1' 
                        AND c.access   <= ".$user->get('aid')."
                        AND p.published = '1' 
                        AND p.approved  = '1' 
                        AND p.id = ".$this->_config['imgid']."
                      LIMIT 1
                    ";
          $database->setQuery( $query );
          return $database->loadResult();
        }
        else
        {
          return true;
        }
      }
    }
    else
    {
      return false;
    }
  }//End function dynctest


  function getJoomId($Itemid_modul='')
  {
    $database = & JFactory::getDBO();

    $Itemid_param = JRequest::getInt('Itemid', '');
    if(trim(Joom_mosGetParam('option', '')) == 'com_joomgallery' && $Itemid_param != '' 
       && $Itemid_param != 0 && $Itemid_param != 99999
      )
    {
      return "&amp;Itemid=".$Itemid_param;
    }
    elseif($Itemid_modul != '')
    {
      return "&amp;Itemid=".$Itemid_modul;
    }
    else
    {
      $database->setQuery(" SELECT 
                              id 
                            FROM 
                              #__menu 
                            WHERE 
                              link LIKE '%com_joomgallery%' 
                              AND (published = '1' OR published = '0') 
                              AND access = '0' 
                            ORDER BY 
                              id DESC 
                            Limit 1
                          ");
      $Itemid_jg = $database->loadResult();
      if($Itemid_jg == '' || $Itemid_jg == NULL)
      {
        $database->setQuery(" SELECT 
                                id 
                              FROM 
                                #__menu 
                              WHERE 
                                link LIKE '%com_joomgallery%' 
                                AND (published = '1' OR published = '0') 
                                AND access = '1' 
                              ORDER BY 
                                id DESC 
                              Limit 1
                            ");
        $Itemid_jg = $database->loadResult();
      }
      $Itemid_jg = ($Itemid_jg == '' || $Itemid_jg == NULL) ? "" : "&amp;Itemid=".$Itemid_jg;
      return $Itemid_jg;
    }
  }//End function getJoomId


  function getCatgLink($cid)
  {
    return JRoute::_('index.php?option=com_joomgallery'.$this->getJoomId().'&func=viewcategory&catid='.$cid);
  }//End function getCatgLink


  function dbImages($sorting, $limit='', $cmt='')
  {
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    if(stristr($sorting, 'n.ndate'))
    {
       if($this->_config['shownameshields'] == 0)
       {
         return null;
       }
       elseif(!$user->get('aid') && !$this->_config['shownameshieldsunreg'])
       {
         return null;
       }
    }

    $query  = " SELECT 
                  p.id AS picid, 
                  p.catid, 
                  p.imgthumbname, 
                  p.imgfilename, 
                  p.imgdate, 
                  p.imgtitle, 
                  p.imgtext, 
                  p.imgcounter, 
                  p.imgvotes
                  (p.imgvotesum/p.imgvotes) AS vote, 
                  c.name AS cattitle,
                  c.catpath AS catpath
              ";
    if($this->cmttest())
    {
      $query .= ", 
                    COUNT(co.cmtid) AS cmtcount,
                    co.userid AS cmtuserid, 
                    co.cmttext, 
                    co.cmtdate, 
                    co.cmtname, 
                    co.cmtid AS commentid\n
                ";
    }
    $query .= " FROM 
                  #__joomgallery as p
                INNER JOIN 
                  #__joomgallery_catg AS c ON c.cid = p.catid
              ";
    if(stristr($sorting,'n.ndate'))
    {
      $query .= "\n INNER JOIN 
                      #__joomgallery_nameshields AS n ON n.npicid = p.id\n
                ";
    }
    if($this->cmttest())
    {
      $query .= " INNER JOIN 
                    #__joomgallery_comments AS co ON co.cmtpic = p.id\n
                ";
    }
    $query .= " WHERE 
                      c.published = '1' 
                  AND c.access   <= ".$user->get('aid')."
                  AND p.published = '1' 
                  AND p.approved  = '1'\n
              ";
    if($this->cmttest() && $cmt != '')
    {
      $query .= "   AND co.cmtpic IN (".$cmt.")\n";
    }
    if($this->dynctest())
    {
      $query .= "   AND p.catid = ".$this->dynctest(1)."\n";
    }
    if($this->_config['cats'] != '')
    {
      $query .= "  AND p.catid";
      $query .= ($this->_config['showorhidecats']==1) ? " IN" : " NOT IN";
      $query .= " (".$this->_config['cats'].")\n";
    }
    if($this->cmttest())
    {
      $query .= "   AND co.published = '1' 
                    AND co.approved  = '1'\n
                  GROUP BY 
                    co.cmtpic\n
                ";
    }
    $query .= " ORDER BY 
                  ".$sorting."\n
              ";
    if($limit != '')
    {
      $query .= " LIMIT ".$limit;
    }
    $database->setQuery($query);
    return $database->loadObjectList();
   }//End function dbImages


  function dbComments($sorting, $limit='', $img='', $count='')
  {

    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();

    if($count == '')
    {
      $query  = " SELECT 
                    co.cmttext, 
                    co.cmtdate, 
                    co.cmtname,
                    co.userid, 
                    co.cmtid AS commentid, 
                    p.id AS picid, 
                    p.catid,
                    p.imgthumbname, 
                    p.imgfilename, 
                    p.imgdate, 
                    p.imgtitle, 
                    p.imgtext, 
                    p.imgcounter, 
                    p.imgvotes,
                    (p.imgvotesum/p.imgvotes) AS vote, 
                    c.name AS cattitle,
                    c.catpath AS catpath\n
                ";
    }
    else
    {
      $query  = " SELECT 
                    co.cmtpic AS id, 
                    COUNT(co.cmtid) AS cmtcount\n
                ";
    }
    $query .= " FROM 
                  #__joomgallery_comments AS co
                INNER JOIN 
                  #__joomgallery AS p ON co.cmtpic = p.id
                INNER JOIN 
                  #__joomgallery_catg AS c ON c.cid = p.catid
                WHERE 
                      c.published = '1' 
                  AND c.access   <= ".$user->get('aid')."
                  AND p.published = '1' 
                  AND p.approved  = '1'
              ";
    if($img != '')
    {
      $query .= "   AND p.id IN (".$img.")";
    }
    if($this->dynctest()){
      $query .= "   AND p.catid = ".$this->dynctest(1);
    }
    if($this->_config['cats'] != "") {
      $query .= "   AND p.catid";
      $query .= ($this->_config['showorhidecats'] == 1) ? " IN" : " NOT IN";
      $query .= " (".$this->_config['cats'].")";
    }
    $query .= "   AND co.published = '1' 
                  AND co.approved  = '1'\n
              ";
    if($count == 1)
    {
      $query .= " GROUP BY 
                    co.cmtpic\n
                ";
    }
    $query .= " ORDER BY 
                ".$sorting."\n
              ";
    if($limit != '' && $count=='')
    {
      $query .= " LIMIT ".$limit;
    }
    $database->setQuery($query);
    $objects = $database->loadObjectList();
    if($this->_config['showcmtcount'] == 1 && $this->_config['detailpic_open'] == 0 
       && $count == '')
    {
     $cmtobj = $this->dbComments($sorting, '', $img, 1);
     $this->_cmtcount = array();
     foreach($cmtobj as $obj)
     {
       $this->_cmtcount[$obj->id] = $obj->cmtcount;
     }
    }
    return $objects;
  }//End function dbComments


  function showtext($obj, $tdclass)
  {
    $database = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    if($this->_config['showtext'] == 1)
    {
      $output = "   <td".$tdclass.">\n";
      if($this->_config['showtitle'] == 1)
      {
        if($this->_config['piclink'] == 1)
        {
          $link = $this->getCatgLink($obj->catid);
        }
        else
        {
          if(($this->_config['showdetailpage'] == 0 && $user->get('aid')!= 0 ) 
             || $this->_config['showdetailpage'] == 1
            )
          {
            $link = Joom_OpenImage($this->_config['detailpic_open'], $obj->picid, $obj->catpath, $obj->catid, $obj->imgfilename, $obj->imgtitle, $obj->imgtext);
            $link = str_replace("&amp;Itemid=&amp;Itemid=", "&amp;Itemid=",$link);
            if($this->_config['detailpic_open'] == 0)
            {
              $link .= "#joomimg";
            }
          }
          else
          {
            $link = "javascript:alert('".JText::_('JGS_ALERT_NO_DETAILVIEW_FOR_GUESTS',true)."')";
          }
          $link = str_replace("lightbox[joomgallery]","lightbox[".$this->_stag."2]",$link);
          $link = str_replace("rel=\"joomgallery\"","rel=\"".$this->_stag."2\"",$link);
        }
        $output .= "    <a href=\"".$link;
        $output .= "\" class=\"".$this->_stag."name\">\n          ".$obj->imgtitle ."\n        </a><br />\n";
      }
      if($this->_config['showcatg'] == 1)
      {
        $output .= "          ".JText::_('JGS_CATEGORY').":\n"
                ."        <a href=\"".$this->getCatgLink($obj->catid)."\" class=\"".$this->_stag."cat\" title=\"".$obj->cattitle."\">\n"
                ."          ".$obj->cattitle."\n        </a><br />\n";
      }
      if($this->_config['showdescription'] == 1 && $obj->imgtext !='')
      {
        $output .= "          <span class=\"".$this->_stag."description\">".JText::_('JGS_DESCRIPTION').": ".$this->decodetext($obj->imgtext,$this->_config['strdescount'])."</span><br />\n";
      }
      if($this->_config['showimgdate'] == 1)
      {
        $output .= "        <span class=\"".$this->_stag."imgdate\">".JText::_('JGS_UPLOAD_DATE').":<br /> "
                . strftime( $this->_config['dateformat'], $obj->imgdate )."</span><br />\n";
      }
      if($this->_config['showhits'] == 1)
      {
        $output .= "        <span class=\"".$this->_stag."hits\">".JText::_('JGS_HITS').": ".$obj->imgcounter."</span><br />\n";
      }
      if($this->_config['showvotesum'] == 1)
      {
        $output .= "        <span class=\"".$this->_stag."votes\">".JText::_('JGS_RATING').": ";
        if($obj->vote == '')
        {
          $output .= JText::_('JGS_NO_RATINGS');
        }
        else
        {
          $output .= number_format( $obj->vote, 2, ",", "." );
          if($this->_config['showvotes'] == 1)
          {
            $output .= " (".$obj->imgvotes.")";
          }
        }
        $output .= "<br /></span>\n";
      }
      if($this->_config['showcmtcount'] == 1 && $this->cmttest() 
         && $this->_config['detailpic_open'] == 0
        )
      {
        if(isset($obj->cmtcount))
        {
          $count = $obj->cmtcount;
        }
        elseif(count($this->_cmtcount) > 0)
        {
          $count = $this->_cmtcount[$obj->picid];
        }
        else
        {
          $count = 0;
        }
        if((  $this->_config['showdetailpage'] == 0 && $user->get('aid')!= 0) 
           || $this->_config['showdetailpage'] == 1
          )
        {
          $output .= "        <a href=\"".JRoute ::_($this->_config['link'].$obj->picid).'#joomcomments'."\" class=\"".$this->_stag."cmtcount\">".JText::_('JGS_NUMBER_OF_COMMENTS').": ".$count."</a><br />\n";
        }
        else
        {
          $output .= "        <a href=\"javascript:alert('".JText::_('JGS_ALERT_NO_DETAILVIEW_FOR_GUESTS',true)."')\" class=\"".$this->_stag."cmtcount\" >".JText::_('JGS_NUMBER_OF_COMMENTS').": ".$count."</a><br />\n";
        }
      }
      if($this->_config['showcmtdate'] == 1 && $obj->cmtdate != NULL)
      {
        $output .= "        <span class=\"".$this->_stag."cmtdate\">".JText::_('JGS_LAST_COMMENT_DATE').": <br />"
                . strftime( $this->_config['dateformat'], $obj->cmtdate )."</span><br />\n";
      }
      if($this->_config['showcmttext'] == 1 && $obj->cmtdate != NULL)
      {
        $cmtname = $obj->cmtname;
        $user2   = $obj->cmtuserid;
        if($user2 != 0)
        {
          $query = "  SELECT 
                        username
                      FROM 
                        #__users
                      WHERE 
                        id = '$user2'
                    ";
          $database->setQuery($query);
          $cmtname = $database->loadResult();
        }

        //TODO aHa: cb/cbe linking deactivated until the functions from JG V 1.0 are integrated
        //if ($this->_config['combuild'] == 1 && $user!=0) {
        //  $output .= "        <span class=\"".$this->_stag."cmttext\">".JText::_('JGS_LAST_COMMENT_BY')."\n        <a href=\"".JRoute ::_("index.php?option=com_comprofiler&amp;task=userProfile&amp;user=".$user)."\">\n          ".$result."\n        </a>";
        // } else {
          $output .= "        <span class=\"".$this->_stag."cmttext\">".JText::_('JGS_LAST_COMMENT_BY')." ".$cmtname;
        //}
        $output .= ":<br />\n        &quot; ".$this->decodetext($obj->cmttext,$this->_config['strcmtcount'],$this->_config['strcmtwrap']);
        if($this->_config['showcmtmore'] == 1 && $this->_config['strcmtcount'] > 0)
        {
          $output .= " <a href=\"".JRoute::_($this->_config['link'].$obj->picid)."#joomcomments"."\">\n          "._READ_MORE."</a>\n";
        }
        $output .= " &quot;\n        </span>\n";
      }
      $output .= "      </td>\n";
    }
    else
    {
      $output = '';
    }
    return $output;
  }//End function showtext


  function showimage($obj, $tdclass)
  {
    $user = & JFactory::getUser();

    $output ="   <td".$tdclass.">\n";

    if($obj->picid != '')
    {
      $thmsize   = getimagesize($this->_config['thumbnailabspath'].$obj->catpath.'/'.$obj->imgthumbname);
      $thmWidth  = $thmsize[0];
      $thmHeight = $thmsize[1];

      // if photos are resized by width
      if($this->_config['useforresizedirection'] == 1)
      {
        $ratio = ($thmWidth / $this->_config['imgwidth']);
        $testheight = ($thmHeight/$ratio);
        // if thumbheight is higher than maxheight
        if($testheight>$this->_config['imgheight'])
        {
          $ratio = ($thmHeight / $this->_config['imgwidth']);
        }
        // if photos are resized by height
      }
      else
      {
        $ratio = ($thmHeight / $this->_config['imgheight']);
        $testwidth = ($thmWidth/$ratio);
        // if thumbwidth is larger than maxwidth
        if($testwidth>$this->_config['imgheight'])
        {
          $ratio = ($thmWidth/$this->_config['imgheight']);
        }
      }
      // Checks if image has to be resized  
      $ratio = max($ratio, 1.0);
      $destWidth  = (int)($thmWidth / $ratio);
      $destHeight = (int)($thmHeight / $ratio);
      if($this->_config['piclink'] == 1)
      {
        $link = $this->getCatgLink($obj->catid);
      }
      else
      {
        if(($this->_config['showdetailpage'] == 0 && $user->get('aid')!= 0) 
           || $this->_config['showdetailpage'] == 1
          )
        {
          $link = Joom_OpenImage($this->_config['detailpic_open'], $obj->picid,$obj->catpath.'/', $obj->catid, $obj->imgfilename, $obj->imgtitle, $obj->imgtext);
          $link = str_replace("&amp;Itemid=&amp;Itemid=","&amp;Itemid=",$link);
        }
        else
        {
          $link = "javascript:alert('".JText::_('JGS_ALERT_NO_DETAILVIEW_FOR_GUESTS',true)."')";
        }
        if($this->_config['detailpic_open'] == 0)
        {
          $link .= "#joomimg";
        }
        $link = str_replace("lightbox[joomgallery]","lightbox[".$this->_stag."1]",$link);
        $link = str_replace("rel=\"joomgallery\"","rel=\"".$this->_stag."1\"",$link);
      }

      $output .= "    <a href=\"".$link."\">\n";
      $output .= "     <img src=\"".$this->_config['thumbnailpath'] . $obj->catpath . '/' . $obj->imgthumbname."\" alt=\"".$obj->imgtitle."\" width=\"".$destWidth."\" height=\"".$destHeight."\" class=\"";
      //$output .= ($this->_config['joomstyle']==1) ? $this->_config['myborder'] : "".$this->_stag."img" ;
      $output .= $this->_stag."img" ;
      $output .= "\" />\n";
      $output .= "    </a>\n";
    }
    else
    {
      $output .= "    &nbsp;\n";
    }
    $output .= "   </td>\n";
    return $output;
  }//End function showimage


  function decodetext($text, $newlength=0, $wrap=0, $more=0)
  {
    //Remove whitespace at start and end of the text
    $text      = trim($text);
    $newlength = ($newlength!=0) ? $newlength-1 : 0;

    //Smilies if smilies are supported
    $smiley = array();
    $smiley[':smile:']          = "sm_smile.gif";
    $smiley[':cool:']           = "sm_cool.gif";
    $smiley[':grin:']           = "sm_biggrin.gif";
    $smiley[':wink:']           = "sm_wink.gif";
    $smiley[':none:']           = "sm_none.gif";
    $smiley[':mad:']            = "sm_mad.gif";
    $smiley[':sad:']            = "sm_sad.gif";
    $smiley[':dead:']           = "sm_dead.gif";

    if($this->_config['anismilie'])
    {
        $smiley[':yes:']            = "sm_yes.gif";
        $smiley[':lol:']            = "sm_laugh.gif";
        $smiley[':smilewinkgrin:']  = "sm_smilewinkgrin.gif";
        $smiley[':razz:']           = "sm_bigrazz.gif";
        $smiley[':roll:']           = "sm_rolleyes.gif";
        $smiley[':eek:']            = "sm_bigeek.gif";
        $smiley[':no:']             = "sm_no.gif";
        $smiley[':cry:']            = "sm_cry.gif";
    }

    //Define replace tags
    $replace1  = array("[url]","[/url]","[email]","[/email]");
    $replace21 = array("[b]","[i]","[u]");
    $replace22 = array("[/b]","[/i]","[/u]");
    $replace2  = array_merge($replace21, $replace22);
    $replace3  = array("<b>","<i>","<u>","</b>","</i>","</u>");

    //replace url and emailtags because we do not show them in our modules
    foreach($replace1 as $replace)
    {
      $text = str_replace($replace, '', $text);
    }
    $textlength = strlen($text);
    //if text has to be in a range we abridge him
    if($newlength > 0 && $newlength < $textlength)
    {
      $add = '';

      //replace simple html-tags with bb_code
      for($i=0; $i<count($replace3); $i++)
      {
        $text = str_replace($replace3[$i],$replace2[$i],$text);
      }

      //replace smilies with shorttags or remove them
      if($this->_config['smiliesupport'] == 1)
      {
        $count = 0;
        $smileshort = array();
        foreach($smiley as $i=>$sm)
        {
          $text = str_replace ($i, "{".$count."}", $text);
          $smileshort[$count]["short"] = $i;
          $smileshort[$count]["long"]  = $sm;
          $count++;
        }
      }
      else
      {
        foreach($smiley as $i=>$sm)
        {
          $text = str_replace ($i, '', $text);
        }
      }
      $textlength = strlen($text);
    }
    //remove any html because it is too complicated to handle them
    //except the <br /> coming from an former allowed wordwrap
    if($wrap > 0)
    {
      $text = strip_tags($text,"<br>");
    }
    else
    {
      $text = strip_tags($text);
    }
    //if wrap is activated count the containing <br />
    // and add their length to $newlength
    if($wrap > 0)
    {
      $countbrstr = substr($text, 0, $newlength);
      //count the <br />
      $countbr = substr_count($countbrstr, '<br />');
      if($countbr > 0)
      {
        $newlength = $newlength + ($countbr*6);
      }
    }
    $textlength = strlen($text);

    // slice if needful
    if($newlength != 0 && $textlength > ($newlength+1))
    {
      //Check a sliced <br />
      if(($textlength-6) > 0 && ($newlength-6) > 0)
      {
        $strposfound=strpos($text,'<br />',$newlength-6);
      }
      else
      {
        $strposfound = 0;
      }
      if($strposfound > 0 && $strposfound < $newlength)
      {
        $newlength=$strposfound; //slice before the begin of the <br />
      }
      else
      {
        //check a sliced bbcode tag and shorten newlength
        foreach($replace2 as $replace)
        {
          $replacelength=strlen($replace);
          if($textlength > ($newlength-$replacelength) && ($newlength-$replacelength) > 0)
          {
            $strposfound = strpos($text, $replace, $newlength-$replacelength);
          }
          else
          {
            $strposfound = 0;
          }
          if($strposfound > 0 && $strposfound < $newlength)
          {
            $newlength = $strposfound;
            break;
          }
        }
        //check a sliced smilie tag and shorten newlength
        if(isset($smileshort))
        {
          for($i=0; $i<count($smileshort); $i++)
          {
            $replacelength = strlen($i)+2;
            if($textlength > ($newlength-$replacelength) && ($newlength-$replacelength) > 0)
            {
              $strposfound = strpos($text, "{".$i."}", $newlength-$replacelength);
            }
            else
            {
              $strposfound = 0;
            }
            if($strposfound > 0 && $strposfound < $newlength)
            {
              $newlength = $strposfound;
              break;
            }
          }
        }
      }
      //slice the text
      $text = substr($text, 0, $newlength);
    }


    //Adding mising tags at the end of the text
    if($this->_config['bbcodesupport'] == 1)
    {
      $prioarr = array();
      //builds an array for the priority in replacing
      $countreplace = count($replace21);
      for($i=0; $i < $countreplace; $i++)
      {
        //check if there is an unbalance
        //of opening and closing tags
        $countopen  = substr_count($text, $replace21[$i]);
        $countclose = substr_count($text, $replace22[$i]);
        $diff = $countopen-$countclose;
        $found = -1;
        while($diff > 0)
        {
          $found = strpos($text,$replace21[$i],$found+1);
          $prioarr[$found] = $replace22[$i]; //add the closing tag
          $diff--;
        }
      }
      if(count($prioarr))
      {
        //reverse the array to begin with the last element
        arsort($prioarr);
        foreach($prioarr as $key => $value)
        {
          $add .= $value;
        }
      }
    }
    //abridge text and add missing tags
    if(!empty($add))
    {
      $text = $text.$add;
    }
    //If text was sliced add the ellipsis
    if($newlength > 0 && $textlength > $newlength && $more == 0)
    {
      $text .= "...";
    }

    //decoding bb_code or remove tags
    if ($this->_config['bbcodesupport'] == 1)
    {
      //including common.joomgallery.php for decoding bb_code function
      if(!function_exists('Joom_BBDecode'))
      {
        include(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomgallery'.DS.'common.joomgallery.php');
      }
      $text = Joom_BBDecode($text);
    }
    else
    {
      foreach($replace2 as $replace)
      {
        $text = str_replace($replace, '', $text);
      }
    }

    //decoding smilies or remove them
    if($this->_config['smiliesupport'] == 1)
    {
      foreach($smiley as $i=>$sm)
      {
        $text = str_replace ($i, "<img src=\"".JURI::base()."components/com_joomgallery/assets/images/smilies/".$this->_config['smiliescolor']."/".$sm."\" border=\"0\" alt=\"".$i."\" title=\"".$i."\" />", $text);
      }
      if(isset($smileshort))
      {
        for($i=0; $i<count($smileshort); $i++)
        {
          $text = str_replace ("{".$i."}", "<img src=\"".JURI::base()."components/com_joomgallery/assets/images/smilies/".$this->_config['smiliescolor']."/".$smileshort[$i]['long']."\" border=\"0\" alt=\"".$smileshort[$i]['short']."\" title=\"".$smileshort[$i]['short']."\" />", $text);
        }
      }
    }
    else
    {
      foreach($smiley as $i=>$sm)
      {
        $text = str_replace ($i, '', $text);}
      if(isset($smileshort))
      {
        for($i=0; $i<count($smileshort); $i++)
        {
          $text = str_replace ("{".$i."}", '', $text);
        }
      }
    }
    return $text;
  }//End function decodetext

}//End class joommodule
?>
