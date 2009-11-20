<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.viewminijoom.php $
// $Id: joom.viewminijoom.php 1444 2009-06-11 16:42:45Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined ('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

include(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'joom.viewminijoom.html.php');

/**
 * Provides mini thumbnails with overlib effect
 * is used by the JoomGallery Editorbutton Plugin
 *
 * @package JoomGallery
 * @since 1.5
 */
class Joom_ShowMiniJoom# extends Joom_ShowMiniJoom_HTML
{
  /**
   * Id of the selected category
   *
   * @var int
   */
  var $catid = null;

  /**
   * catpath of the selected category
   *
   * @var int
   */
  var $catpath = null;

  /**
   * Parameter object of JoomBu
   *
   * @var object
   */
  var $_params = null;

  /**
   * Pagination object
   *
   * @var object
   */
  var $_pagination = null;

  /**
   * Constructor
   *
   */
  function __construct()
  {
    $this->_db = & JFactory::getDBO();

    $this->Joom_ShowMinis();
  }//End function __construct


  /**
   * Helper function for PHP 4 compatibility
   *
   */
  function Joom_ShowMiniJoom()
  {
    $this->__construct();
  }//End function Joom_ShowMiniJoom

  /**
   * gets the DB rows for all images and calls the HTML output function
   *
   */
  function Joom_ShowMinis()
  {
    $user       = & JFactory::getUser();
    $mainframe  = & JFactory::getApplication('site');

    // selected category
    $this->catid = $mainframe->getUserStateFromRequest('joom.button.catid', 'catid', 0, 'int');

    // pagination
    $limitstart = JRequest::getInt('limitstart', 0);
    $limit      = $mainframe->getUserStateFromRequest( 'joom.button.limit', 'limit', 50, 'int');

    // ensure that image can be seen later on
    $where = "WHERE 
                   jgc.published = '1'
                AND jgc.access  <= ".$user->get('aid')."
                AND jg.published = '1'
                AND jg.approved  = '1'";
    if($this->catid)
    {
      $where .= "
                AND jg.catid = '".$this->catid."'";

      $this->catpath = Joom_getCatPath($this->catid);
    }

    // query
    $query = "  SELECT 
                  jg.id, 
                  jg.catid, 
                  jg.imgtitle, 
                  jg.imgthumbname, 
                  jgc.name 
                FROM 
                  #__joomgallery AS jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
              ".$where;

    // execute the query if list_limit = 0 -> all pictures
    if
    ($limit == 0)
    {
      $this->_db->setQuery($query);
      $rows   = $this->_db->loadObjectList();
      $total  = count($rows);
    }
    else
    {
      // take the query and replace the 'select *' with 'select count(jg.id)' -> $querycount
      // to count total rows for navigation
      $countquery = str_replace('SELECT jg.id, jg.catid, jg.imgtitle, jg.imgthumbname, jgc.name','SELECT COUNT(id)',$query);
      $this->_db->setQuery($countquery);
      $total = $this->_db->loadResult();

      if($total <= $limit)
      {
        $limitstart = 0;
      }

      $this->_db->setQuery($query, $limitstart, $limit);
      $rows = $this->_db->loadObjectList();
    }

    jimport('joomla.html.pagination');
    $this->_pagination = new JPagination($total, $limitstart, $limit);

    Joom_ShowMiniJoom_HTML::Joom_ShowMinis_HTML($rows);
  }//End function Joom_ShowMinis

}//End class Joom_ShowMiniJoom
