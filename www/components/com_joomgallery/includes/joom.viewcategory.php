<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.viewcategory.php $
// $Id: joom.viewcategory.php 449 2009-06-14 11:57:04Z aha $
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

class Joom_CategoryView
{

  var $catid;
  var $catname;
  var $order_dir;
  var $order_by;

  //Cat Navi
  var $catcount;
  var $catstart;
  var $catstartpage;
  var $catgesamtseiten;

  //Subcat Navi
  var $subcatcount;
  var $substart;
  var $substartpage;
  var $subgesamtseiten;

  //Cat Body
  var $catrows;
  var $catrowcounter;
  var $orderclause;

  //Cooliris
  var $cooliris;

  //URL
  var $viewcategory_url;
  var $assetsimages_url;

  function Joom_CategoryView(&$catid)
  {
    $database = & JFactory::getDBO();

    require_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'joom.viewcategory.html.php');
    $this->catstartpage = JRequest::getInt('startpage', 0);
    $this->substartpage = JRequest::getInt('substartpage', 0);

    //User ordered view
    $this->order_dir = trim(Joom_mosGetParam('orderdir', ''));
    $this->order_by =  trim(Joom_mosGetParam('orderby', ''));
    if($this->order_dir == '')
    {
      $this->order_dir = trim(Joom_mosGetParam('orderdir', '', 'post'));
    }
    if($this->order_by == '')
    {
      $this->order_by =  trim(Joom_mosGetParam('orderby', '', 'post'));
    }

    $this->catid = $catid;

    $this->viewcategory_url = 'index.php?option=com_joomgallery&func=viewcategory&catid=';
    $this->assetsimages_url = _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/';
    //Cooliris
    $this->cooliris = JRequest::getInt('cooliris', 0);
  }//End function Joom_CategoryView


  function Joom_ShowCategoryHead()
  {
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();

    $database->setQuery(" SELECT
                            name,
                            cid
                          FROM
                            #__joomgallery_catg
                          WHERE
                            cid = '$this->catid'
                        ");
    $this->catname = $database->loadResult();

    //push catname into the ambit
    $joomambit = Joom_Ambit();
    $joomambit->set('cattitle', $this->catname);

    $database->setQuery(" SELECT
                            COUNT(*)
                          FROM
                            #__joomgallery AS a
                          LEFT JOIN
                            #__joomgallery_catg AS c ON c.cid=a.catid
                          WHERE
                               a.published = '1'
                            AND a.catid    = '$this->catid'
                            AND a.approved = '1'
                            AND c.access  <= '".$user->get('aid')."'
                        ");
    $this->count = $database->loadResult();

    HTML_Joom_Category::Joom_ShowCategoryHead_HTML($this->catname, $config->jg_colnumb,
                                                   $this->count, $this->catid,
                                                   $this->order_by, $this->order_dir);
  }//End function Joom_ShowCategoryHead


  function Joom_ShowCategoryBody()
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    if($config->jg_secondorder != '' && $config->jg_thirdorder == '')
    {
      $this->orderclause = "a.".$config->jg_firstorder.", a.".$config->jg_secondorder;
    }
    elseif($config->jg_secondorder != '' && $config->jg_thirdorder != '')
    {
      $this->orderclause = "a.".$config->jg_firstorder.", a.".$config->jg_secondorder.", a.".$config->jg_thirdorder;
    }
    else
    {
      $this->orderclause = "a.".$config->jg_firstorder;
    }

    if($config->jg_usercatorder)
    {
      switch($this->order_by)
      {
        case 'user':
          $this->orderclause = 'a.owner';
          break;
        case 'date':
          $this->orderclause = 'a.imgdate';
          break;
        case 'rating':
          $this->orderclause = 'rating';
          break;
        case 'title':
          $this->orderclause = 'a.imgtitle';
          break;
        case 'hits':
          $this->orderclause = 'a.imgcounter';
          break;
        default:
          break;
      }
      if($this->order_by == 'title'
          || $this->order_by == 'hits'
          || $this->order_by == 'user'
          || $this->order_by == 'date'
          || $this->order_by == 'rating'
        )
      {
        if($this->order_dir == 'desc')
        {
          $this->orderclause .= ' DESC';
        }
        elseif($this->order_dir == 'asc')
        {
          $this->orderclause .= ' ASC';
        }
      }
      if($this->order_by == 'rating')
      {
        $moreorderclause = ',imgvotesum DESC';
      }
      else
      {
        $moreorderclause = '';
      }
    }
    else
    {
      $moreorderclause = '';
    }

    $database->setQuery(" SELECT
                            *,
                            a.owner AS owner,
                            ROUND(imgvotesum/imgvotes, 2) AS rating
                          FROM
                            #__joomgallery AS a
                          LEFT JOIN
                            #__joomgallery_catg AS c ON c.cid=a.catid
                          WHERE
                               a.published = '1'
                            AND a.catid    = '$this->catid'
                            AND a.approved = '1'
                            AND c.access  <= '".$user->get('aid')."'
                          ORDER BY
                            ".$this->orderclause." ".$moreorderclause."
                          LIMIT
                            ".$this->catstart.",".$config->jg_perpage);
    $this->catrows = $database->loadObjectList();
    $this->catrowcounter = 0;

    //wenn jg_cooliris und this->cooliris = true, dann gerade laufende
    //Cooliris Darstellung. In diesem Fall nur den XML Feed ausgeben
    if($config->jg_cooliris && $this->cooliris)
    {
      require_once(JPATH_COMPONENT.DS.'classes'.DS.'cooliris.class.php');
      $coolirisclass = new Joom_Cooliris();
      $coolirisclass->Joom_GetXMLFeed($this->catid, $this->catgesamtseiten,
                                      $this->catstartpage, $this->catrows);
    }
    HTML_Joom_Category::Joom_ShowCategoryBody_HTML($this->catrows, $this->catrowcounter,
                                                   $config->jg_colnumb, $this->order_by,
                                                   $this->order_dir );
  }//End function Joom_ShowCategoryBody


  function Joom_ShowSubcategories()
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    $query = "  SELECT
                  d.*
                FROM
                  #__joomgallery_catg AS d
                WHERE
                         d.parent = $this->catid
                  AND d.published = '1'
              ";

    if($config->jg_showrmsmcats == 0)
    {
      $query .= " and d.access<= '".$user->get('aid')."'";
    }

    if($config->jg_ordersubcatbyalpha)
    {
      $query .= " ORDER BY d.name LIMIT ".$this->substart.",".$config->jg_subperpage;
    }
    else
    {
      $query .= " ORDER BY d.ordering LIMIT ".$this->substart.",".$config->jg_subperpage;
    }

    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if($rows != NULL)
    {
      HTML_Joom_Category::Joom_ShowSubCategories_HTML($rows);
    }
  }//End function Joom_ShowSubcategories


  function Joom_CatPageNav()
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    if($config->jg_showrmsmcats && $user->get('aid') == 0)
    {
      $andaccess = "";
    }
    else
    {
      $andaccess = "AND c.access <= '".$user->get('aid')."'";
    }
    // Kategorie
    // Navigation und Feststellen der Anzahl der verfuegbaren Datensaetze
    $database->setQuery(" SELECT
                            COUNT(id)
                          FROM
                            #__joomgallery AS a
                          LEFT JOIN
                            #__joomgallery_catg AS c ON c.cid=a.catid
                          WHERE
                               a.published = '1'
                            AND a.catid    = '$this->catid'
                            AND a.approved = '1'
                            $andaccess
                        ");
    $this->catcount = $database->loadResult();
    // Berechnen der Gesamtseiten
    $this->catgesamtseiten = floor($this->catcount / $config->jg_perpage);
    $seitenrest = $this->catcount % $config->jg_perpage;
    if($seitenrest > 0)
    {
      $this->catgesamtseiten++;
    }
    $this->catcount = number_format($this->catcount, 0, ',', '.');
    // Feststellen der aktuellen Seite
    if(isset($this->catstartpage))
    {
      if($this->catstartpage > $this->catgesamtseiten)
      {
        $this->catstartpage = $this->catgesamtseiten;
      }
      if($this->catstartpage < 1)
      {
        $this->catstartpage = 1;
      }
    }
    else
    {
      $this->catstartpage = 1;
    }
    // Limit und Seite Vor- & Rueckfunktionen
    $this->catstart = ($this->catstartpage - 1) * $config->jg_perpage;
  }//End function Joom_CatPageNav


  function Joom_SubcatPageNav()
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    // Subkategorien
    // Navigation und Anzahl der verfuegbaren Datensaetze
    if($config->jg_showrmsmcats && $user->get('aid') == 0)
    {
      $andaccess = "";
    }
    else
    {
      $andaccess = "AND d.access <= '".$user->get('aid')."'";
    }

    $database->setQuery(" SELECT
                            COUNT(cid)
                          FROM
                            #__joomgallery_catg AS d
                          WHERE
                                   d.parent = $this->catid
                            AND d.published = '1'
                            $andaccess
                        ");
    $this->subcatcount = $database->loadResult();
    // Berechnen der Gesamtseiten
    $this->subgesamtseiten = floor($this->subcatcount / $config->jg_subperpage);
    $subseitenrest = $this->subcatcount % $config->jg_subperpage;
    if($subseitenrest > 0)
    {
      $this->subgesamtseiten++;
    }
    $this->subcatcount = number_format($this->subcatcount, 0, ',', '.');
    // Feststellen der aktuellen Seite
    if(isset($this->substartpage))
    {
      if($this->substartpage > $this->subgesamtseiten)
      {
        $this->substartpage = $this->subgesamtseiten;
      }
      elseif($this->substartpage < 1)
      {
        $this->substartpage=1;
      }
    }
    else
    {
      $this->substartpage=1;
    }
    // Limit und Seite Vor- & Rueckfunktionen
    $this->substart = ($this->substartpage - 1) * $config->jg_subperpage;

    HTML_Joom_Category::Joom_ShowSubCategoryPageNav_HTML($this->subcatcount,
                                                         $this->substart,
                                                         $this->substartpage,
                                                         $this->subgesamtseiten,
                                                         $this->catid);
  }//End function Joom_SubcatPageNav


  function Joom_ShowCategoryPageNav()
  {
    HTML_Joom_Category::Joom_ShowCategoryPageNav_HTML($this->catcount, $this->catstart,
                                                      $this->catstartpage,
                                                      $this->catgesamtseiten,
                                                      $this->catid);
  }//End function Joom_ShowCategoryPageNav

}//End class Joom_CategoryView
?>
