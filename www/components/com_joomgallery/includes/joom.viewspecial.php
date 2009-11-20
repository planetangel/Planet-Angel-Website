<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.viewspecial.php $
// $Id: joom.viewspecial.php 449 2009-06-14 11:57:04Z aha $
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

include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'joom.viewspecial.html.php');

//Parameter
$sorting = trim(Joom_mosGetParam('sorting', ''));

//Suche
$sstring = trim(Joom_mosGetParam('sstring', '', 'post'));
if($sstring == '')
{
  $sstring = trim(Joom_mosGetParam('sstring', ''));
}

function strtolower_utf8($inputstring)
{
  $outputString    = utf8_decode($inputstring);
  $outputString    = strtolower($outputString);
  $outputString    = utf8_encode($outputString);
  return $outputString;
}

switch($sorting)
{
  case 'find':
    $searchstring  = trim(strtolower_utf8($sstring)) ;
    $searchstring2 = htmlentities(trim(strtolower_utf8($sstring )), ENT_QUOTES, 'UTF-8');

    $query1=" SELECT 
                a.*,
                a.owner AS owner,
                u.username, 
                ca.name AS name
              FROM 
                #__joomgallery AS a, 
                #__joomgallery_catg AS ca, 
                #__users AS u
              WHERE 
                    a.catid = ca.cid 
                AND a.owner = u.id 
                AND (u.username LIKE '%$searchstring%'
                  OR a.imgtitle LIKE '%$searchstring%'
                  OR a.imgtext  LIKE '%$searchstring2%')
                AND a.published   = '1' 
                AND ca.published  = '1' 
                AND a.approved    = '1' 
                AND ca.access    <= ".$user->get('aid')."
              GROUP BY 
                a.id
              ORDER BY 
                a.id DESC 
            ";
    $tl_title = JText::_('JGS_SEARCH_RESULTS')."<b> $sstring</b>";
    break;

  case 'lastcomment':
    $query1 = " SELECT 
                  a.*, 
                  cc.*, 
                  ca.*, 
                  u.username, 
                  a.owner AS owner 
                FROM 
                  #__joomgallery AS a, 
                  #__joomgallery_catg AS ca, 
                  #__joomgallery_comments AS cc 
                LEFT JOIN 
                  #__users AS u on cc.userid = u.id
                WHERE 
                              a.id = cc.cmtpic 
                  AND a.catid      = ca.cid 
                  AND a.published  = '1' 
                  AND a.approved   = '1' 
                  AND cc.published = '1' 
                  AND ca.published = '1' 
                  AND cc.approved  = '1' 
                  AND ca.access   <= ".$user->get('aid')."
                ORDER BY 
                  cc.cmtdate DESC 
                LIMIT ".$config->jg_toplist;
    $tl_title = JText::_('JGS_TOP').' '.$config->jg_toplist.' '.JText::_('JGS_LAST_COMMENTED_PICTURE');
    break;

  case 'lastadd':
    $query1 = " SELECT 
                  *, 
                  a.owner AS owner
                FROM 
                  #__joomgallery As a, 
                  #__joomgallery_catg AS ca
                WHERE 
                           a.catid = ca.cid 
                  AND a.published  = '1' 
                  AND a.approved   = '1' 
                  AND ca.published = '1' 
                  AND ca.access   <= ".$user->get('aid')."
                ORDER BY 
                  a.id DESC 
                LIMIT ".$config->jg_toplist;
    $tl_title = JText::_('JGS_TOP').' '.$config->jg_toplist.' '.JText::_('JGS_LAST_ADDED_PICTURE');
    break;

  case 'rating':
    $query1 = " SELECT 
                  *, 
                  a.owner AS owner, 
                  ROUND(imgvotesum/imgvotes, 2) AS rating
                FROM 
                  #__joomgallery AS a, 
                  #__joomgallery_catg AS ca
                WHERE 
                           a.catid = ca.cid 
                  AND a.imgvotes   > '0' 
                  AND a.published  = '1' 
                  AND a.approved   = '1' 
                  AND ca.published = '1' 
                  AND ca.access   <= ".$user->get('aid')."
                ORDER BY 
                  rating DESC,
                  imgvotesum DESC 
                LIMIT ".$config->jg_toplist;
    $tl_title = JText::_('JGS_TOP').' '.$config->jg_toplist.' '.JText::_('JGS_BEST_RATED_PICTURE');
    break;

  default:
    $query1 = " SELECT 
                  *, 
                  a.owner AS owner
                FROM 
                  #__joomgallery AS a, 
                  #__joomgallery_catg AS ca
                WHERE 
                      a.imgcounter > 0 
                  AND a.catid      = ca.cid 
                  AND a.published  = '1' 
                  AND a.approved   = '1' 
                  AND ca.published = '1' 
                  AND ca.access   <= ".$user->get('aid')."
                ORDER BY 
                  imgcounter DESC 
                LIMIT ".$config->jg_toplist;
    $tl_title = JText::_('JGS_TOP').' '.$config->jg_toplist.' '.JText::_('JGS_MOST_VIEWED_PICTURE');
    break;
}

# Database Query
$database->setQuery($query1);
$rows = $database->loadObjectList();

HTML_Joom_Specials::Joom_ShowSpecials_HTML($tl_title, $rows, $sorting);

?>
