<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/classes/interface.class.php $
// $Id: interface.class.php 449 2009-06-14 11:57:04Z aha $
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

/**
 * The JoomGallery interface class provides an interface / API
 * to other Joomla extensions to use functions of the Gallery,
 * e.g. to display thumbnails in a Plugin or Module.
 *
 * You just need to include this file, create an interface object
 * and set some options if you want to adjust the output, before
 * using one of the functions.
 * If you display any HTML output, you should once call getPageHeader()
 * first
 *
 */
class joominterface
{

  /**
   * holds the interface configuration
   *
   * @var     array
   * @access  protected
   */
  var $_config = array();

  /**
   * holds the JoomGallery configuration
   *
   * @var     object
   * @access  protected
   */
  var $_jg_config  = null;

  /**
   * holds Itemid in a string like '&Itemid=X'
   *
   * @var     string
   * @access  protected
   */
  var $_itemId = null;


  /**
   * Class constructor
   *
   * @access  protected
   */
  function joominterface()
  {
    $mainframe = & JFactory::getApplication();  
    $database = & JFactory::getDBO(); 

    if(!defined('_JOOM_LIVE_SITE'))
    {
      define('_JOOM_LIVE_SITE',JURI::base());
    }

    // some definitions for joom_javascript
    if(!defined('_JOOM_PARENT_MODULE'))
    {
      define('_JOOM_PARENT_MODULE', 1);
    }
    $func = '';
    $document = & JFactory::getDocument();

    // include language for display
    $language = & JFactory::getLanguage();
    $language->load('com_joomgallery');

    // load JoomGallery plugins
    JPluginHelper::importPlugin('joomgallery');

    require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomgallery'.DS.'common.joomgallery.php');
    $config = Joom_getConfig();
    $this->_jg_config = $config;
    require_once(JPATH_ROOT.DS.'components'.DS.'com_joomgallery'.DS.'classes'.DS.'modules.class.php');
    require_once(JPATH_ROOT.DS.'components'.DS.'com_joomgallery'.DS.'includes'.DS.'joom.javascript.php');

    /**
     * set some default values for options given in global JG config
     * (may be overridden)
     */
    $this->_config['showhits']        = $this->_jg_config->jg_showhits;
    $this->_config['showpicasnew']    = $this->_jg_config->jg_showpicasnew;
    $this->_config['showtitle']       = $this->_jg_config->jg_showtitle;
    $this->_config['showauthor']      = $this->_jg_config->jg_showauthor;
    $this->_config['showrate']        = $this->_jg_config->jg_showcatrate;
    $this->_config['shownumcomments'] = $this->_jg_config->jg_showcatcom;
    $this->_config['showdescription'] = $this->_jg_config->jg_showcatdescription;
    
    /**
     * further defaults (not given by JG config)
     */
    // Category path links to category
    $this->_config['showcatlink']     = 1; 
    // comma-separated list of categories to filter from (empty: all categories, default)
    $this->_config['categoryfilter']  = ''; 
    // display last comment (see Module JoomImages) not implemented yet!
    $this->_config['showlastcomment'] = 0; 

    // export globals for backwards compat. (JG 1.0):
    global $jg_perpage, $jg_colnumb;
    $jg_perpage = $this->_jg_config->jg_perpage;
    $jg_colnumb = $this->_jg_config->jg_colnumb;
  }//End function joominterface


  /**
   * Passes a whole array of config items, existing (default)
   * values are overwritten if a new item with the same key
   * is passed.
   *
   * @param array $config
   */
  function setConfig($config)
  {
    $database = & JFactory::getDBO(); 
    foreach($config as $key => $value)
    {
      $config[$key] = $database->getEscaped($value);
    }
    //Merge new array into existing one, overwriting if needed:
    $this->_config = array_merge($this->_config, $config);
  }//End function setConfig


  /**
   * Sets a single option in the interface settings
   *
   * @param string $key
   * @param string $value
   */
  function addConfig($key, $value='')
  {
    $database = & JFactory::getDBO(); 
    $this->_config[$key] = $database->getEscaped($value);
  }//End function addConfig


  /**
   * Requests string (e.g. modification of a SQL query or true/false)
   * associated with config option $key.
   * If the according value has not been set with addConfig
   * before, a default is returned. Config options are not used
   * directly for security.
   *
   * @param  string $key
   *
   * @return string
   */
  function getConfig($key)
  {
     $database = & JFactory::getDBO();
    if(array_key_exists($key,$this->_config))
    {
      // access filtered to special keys (DB query strings):
      if($key == 'hidebackend')
      {
        if($this->_config['hidebackend'] == 'true')
        {
          return " AND jg.useruploaded = '1' ";
        }
        else
        {
          return '';
        }
      }
      elseif($key == 'categoryfilter')
      {
        $catids = trim($database->getEscaped($this->_config['categoryfilter']));
        if($catids != '')
        {
          return " AND jg.catid IN (".$catids.") ";
        }
        else
        {
          return '';
        }
      }
      else
      { // regular keys:
        return $this->_config[$key];
      }
    }
    else
    {
      return false;
    }
  }//End function getConfig


  /**
   * Returns config value associated
   * with config option $key.
   *
   * @param  string $key
   *
   * @return string/int or boolean false if $key was not found
   */
  function getJConfig($key)
  {
    if(isset($this->_jg_config->$key))
    {
      return $this->_jg_config->$key;
    }
    else
    {
      return false;
    }
  }//End function getJConfig


  /**
   * Returns version string of installed JoomGallery
   *
   * @return string
   */
  function getGalleryVersion() {
    return Joom_GetGalleryVersion();
  }//End function getGalleryVersion


  /**
   * Creates HTML for linked thumbnail of one picture-$obj,
   * with display options & style just like in JG
   *
   * @param db-obj  $obj DB-row coming from this interface, e.g. getPicsByCategory
   * @param boolean $linked if true, we will link the thumbnail, defaults to true
   * @param string  $class  optional, addional css class name which is assigned to the img tag
   * @param string  $div    optional css class name which is assigned to a div around the img tag
   * @param string  $extra optional, adddional HTML code, which is placed in the img tag
   * @return string HTML displaying thumbnail (linked, like configured in JG if $linked = true)
   */
  function displayThumb($obj, $linked = true, $class = null, $div = null, $extra = null)
  {
    $output = '';
    if($obj->picid != '')
    {
      if($div)
      {
        $output .= '<div class="'.$div.'">';
      }
      if($linked)
      {
        //check for link to category
        if(isset($this->_config['piclink']) && $this->_config['piclink'] == 1)
        {
          $link = JRoute::_('index.php?option=com_joomgallery'.$this->getJoomId().'&func=viewcategory&catid='.$obj->catid);
        }
        else
        {
          $link = Joom_OpenImage($this->_jg_config->jg_detailpic_open, $obj->picid,$obj->catpath, $obj->catid, $obj->imgfilename, $obj->imgtitle, $obj->imgtext);
        }
        $output .= '  <a href="'.$link.'" class="jg_catelem_photo">';
      }
      if($class)
      {
        $class = ' '.$class;
      }
      if($extra)
      {
        $extra = ' '.$extra;
      }
      $output   .= '    <img src="'._JOOM_LIVE_SITE . $this->_jg_config->jg_paththumbs .$obj->catpath.'/'.$obj->imgthumbname.'" class="jg_photo'.$class.'" alt="'.$obj->imgtitle.'"'.$extra.' />';
      if($linked)
      {
        $output .= '  </a>';
      }
      if($div)
      {
        $output .= '</div>';
      }
    }
    else
    {
      $output .= "    &nbsp;\n";
    }

    return $output;
  }//End function displayThumb


  /**
   * Creates HTML for linked detail image of one picture-$obj,
   * with display options & style just like in JG
   *
   * @param db-obj  $obj    DB-row coming from this interface, e.g. getPicsByCategory
   * @param boolean $linked if true, we will link the thumbnail, defaults to true
   * @param string  $class  optional, addional css class name which is assigned to the img tag
   * @param string  $div    optional css class name which is assigned to a div around the img tag
   * @param string  $extra  optional, addional HTML code, which is placed in the img tag
   * @return string HTML displaying detail image (linked, like configured in JG if $linked = true)
   */
  function displayDetail($obj, $linked = true, $class = null, $div = null, $extra = null)
  {
    $output = '';
    if($obj->picid != '')
    {
      if($div)
      {
        $output .= '<div class="'.$div.'">';
      }
      if($linked)
      {
        $link = Joom_OpenImage($this->_jg_config->jg_detailpic_open, $obj->picid,$obj->catpath, $obj->catid, $obj->imgfilename, $obj->imgtitle, $obj->imgtext);

        $output .= '  <a href="'.$link.'" class="jg_catelem_photo">';
      }
      if($class)
      {
        $class = ' '.$class;
      }
      if($extra)
      {
        $extra = ' '.$extra;
      }
      $output   .= '    <img src="'._JOOM_LIVE_SITE . $this->_jg_config->jg_pathimages .$obj->catpath.'/'.$obj->imgfilename.'" class="jg_photo'.$class.'" alt="'.$obj->imgtitle.'"'.$extra.' />';
      if($linked)
      {
        $output .= '  </a>';
      }
      if($div)
      {
        $output .= '</div>';
      }
    }
    else
    {
      $output .= "    &nbsp;\n";
    }

    return $output;
  }//End function displayDetail


  /**
   * Creates HTML for description of one picture-$obj,
   * with display options & style just like in JG.
   * Adjustments are possible via the interface options
   *
   * @param db-obj $obj DB-row coming from this interface, e.g. getPicsByCategory
   *
   * @return string HTML of thumb description (like configured in JG or in the interface)
   */
  function displayDesc($obj)
  {
    $output = "<ul>\n";

    if($this->getConfig('showtitle') || $this->getConfig('showpicasnew'))
    {
      $output .= "  <li>";
      if($this->getConfig('showtitle'))
      {
        $output .= "<b>$obj->imgtitle</b>";
      }
      if($this->getConfig('showpicasnew'))
      {
        $output.= Joom_CheckNew($obj->imgdate, $this->_jg_config->jg_daysnew);;
      }
      $output .= "  </li>\n";
    }

    if($this->getConfig('showauthor'))
    {
      if($obj->imgauthor)
      {
        $authorowner = $obj->imgauthor;
      }
      else
      {
        $authorowner = Joom_GetDisplayName($obj->owner);
      }

      $output .= "  <li>".JText::_('JGS_AUTHOR') . ": ".$authorowner;
      $output .= "</li>\n";
    }

    if($this->getConfig('showcategory'))
    {
      $catpath =
      $output .= "  <li>".JText::_('JGS_CATEGORY') . ": ";

      if($this->getConfig('showcatlink'))
      {
        $output .= "<a href=\"".JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$obj->catid). $this->getJoomId() ."\">";
      }
      $output .= $obj->cattitle;

      if($this->getConfig('showcatlink'))
      {
        $output .= "</a>";
      }
      $output .= "  </li>";
    }

    if($this->getConfig('showhits'))
    {
      $output .= "  <li>".JText::_('JGS_HITS') . ": ".$obj->imgcounter."</li>";
    }
    if($this->getConfig('showrate'))
    {
      if($obj->imgvotes > 0)
      {
        $fimgvotesum = number_format($obj->vote, 2, ',', '.');
        $frating = $fimgvotesum.' ('.$obj->imgvotes .  JText::_('JGS_VOTES') . ')';
      }
      else
      {
        $frating =JText::_('JGS_NO_VOTES');
      }

      $output .= '  <li>'. JText::_('JGS_RATING') . ': '.$frating.'</li>';
    }
    if($this->getConfig('shownumcomments'))
    {
      $output .='  <li>'. JText::_('JGS_COMMENTS') . ': '.$obj->cmtcount.'</li>';
    }
    if($this->getConfig('showdescription')  && $obj->imgtext)
    {
      $output .= '  <li>'. JText::_('JGS_DESCRIPTION') . ': '.$obj->imgtext.'</li>';
    }

    $output .= '</ul>';

    return $output;
  }//End function displayDesc


  /**
   * Returns the number of pictures of a user
   *
   * @param integer $userId Joomla-ID of user
   * @param integer $aid GroupID of user (for restricted access images)
   * @return integer number of pictures
   */
  function getNumPicsOfUser($userId, $aid = 0)
  {
    $database = & JFactory::getDBO();
    $userId   = intval($userId);
    $aid      = intval($aid);

    $query = "  SELECT 
                  COUNT(jg.id) 
                FROM 
                  #__joomgallery as jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
                WHERE 
                      jgc.published = '1'
                  AND jgc.access   <= $aid
                  AND jg.published  = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')." 
                  AND jg.approved = '1'
                  AND jg.owner = $userId
              ";
    $database->setQuery($query);
    return $database->loadResult();
  }//End function getNumPicsOfUser


  /**
   * Returns the number of pictures a user is tagged in
   *
   * @param integer $userId
   * @param integer $aid GroupID of user (for restricted access images)
   * @return integer
   */
  function getNumPicsUserTagged($userId, $aid=0)
  {
    $database = & JFactory::getDBO(); 
    $userId = intval($userId);
    $aid = intval($aid);

    $query = "  SELECT 
                  COUNT(nid) 
                FROM 
                  #__joomgallery_nameshields AS jgn
                LEFT JOIN 
                  #__joomgallery AS jg ON jgn.npicid = jg.id
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
                WHERE 
                   jgc.published = '1'
                AND jgc.access  <= $aid
                AND jg.published = '1'
                ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                AND jg.approved = '1'
                AND jgn.nuserid = $userId
            ";
    $database->setQuery($query);
    return $database->loadResult();
  }//End function getNumPicsUserTagged


  /**
   * Returns the number of pictures a user has favoured
   *
   * @param integer $userId
   * @param integer $aid GroupID of viewing user (for restricted access images)
   * @return integer
   */
  function getNumPicsUserFavoured($userId, $aid=0)
  {
    $database = & JFactory::getDBO(); 
    $userId   = intval($userId);
    $query    = " SELECT 
                    piclist 
                  FROM 
                    #__joomgallery_users
                  WHERE 
                    uuserid = $userId
                ";
    $database->setQuery($query);
    $piclist = $database->loadResult();

    if(!isset($piclist)) return 0;

    $query = "  SELECT 
                  COUNT(jg.id) 
                FROM 
                  #__joomgallery as jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
                WHERE 
                     jgc.published = '1'
                  AND jgc.access  <= $aid
                  AND jg.published = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                  AND jg.approved = '1'
                  AND jg.id IN ($piclist)
              ";

    $database->setQuery($query);
    return $database->loadResult();
  }//End function getNumPicsUserFavoured


  /**
   * Returns the number of pictures a user has commented on
   *
   * @param integer $userId
   * @param integer $aid GroupID of viewing user (for restricted access images)
   * @return integer
   */
  function getNumCommentsUser($userId, $aid=0)
  {
    $database = & JFactory::getDBO(); 
    $userId   = intval($userId);
    $aid      = intval($aid);

    $query = "  SELECT 
                  COUNT(cmtid) 
                FROM 
                  #__joomgallery_comments AS jgco
                LEFT JOIN 
                  #__joomgallery AS jg ON jgco.cmtpic = jg.id
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
                WHERE 
                       jgc.published = '1'
                  AND jgc.access    <= $aid
                  AND jgco.published = '1'
                  AND jgco.approved  = '1'
                  AND jg.published   = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                  AND jg.approved = '1'
                  AND jgco.userid = $userId
              ";
    $database->setQuery($query);

    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }
    
    return $database->loadResult();
  }//End function getNumCommentsUser


  /**
   * Returns the total number of comments (published) in the gallery
   *
   * @param integer $aid GroupID of viewing user (for restricted access images)
   * @return integer
   */
  function getNumComments($aid=0)
  {
    $database = & JFactory::getDBO(); 
    $userId   = intval($userId);
    $aid      = intval($aid);

    $query = "  SELECT 
                  COUNT(cmtid) 
                FROM 
                  #__joomgallery_comments AS jgco
                LEFT JOIN 
                  #__joomgallery AS jg ON jgco.cmtpic = jg.id
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
                WHERE 
                       jgc.published = '1'
                  AND jgc.access    <= $aid
                  AND jgco.published = '1'
                  AND jgco.approved  = '1'
                  AND jg.published   = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                  AND jg.approved = '1'
              ";
    $database->setQuery($query);

    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }
    
    return $database->loadResult();
  }//End function getNumComments


  /**
   * Returns pictures of a user
   *
   * @param integer $userId Joomla ID of user
   * @param integer $aid Joomla GroupID of viewing user (access rights). 0 for public viewable!
   * @param string $sorting string for DB sorting
   * @param integer $numPics limit number of pictures; leave away to return all
   * @param integer $limitStart where to start returning $numPics pictures
   * @return db-objs
   */
  function getPicsOfUser($userId, $aid, $sorting, $numPics = NULL, $limitStart = 0)
  {
    $database = & JFactory::getDBO(); 
    // validation:
    $userId   = intval($userId);
    $aid      = intval($aid);
    $sorting  = $database->getEscaped($sorting);
    if(is_null($numPics))
    { // no limit given: return all pictures
      $limit = '';
    }
    else
    {
      $limitStart = intval($limitStart);
      $numPics    = intval($numPics);

      $limit = "\n LIMIT ".$limitStart.",".$numPics;
    }

    $query = "  SELECT ";
    if($this->getConfig('shownumcomments'))
    {
      $query .= " (SELECT 
                    COUNT(cmtid) 
                  FROM 
                    #__joomgallery_comments 
                  WHERE 
                    cmtpic=jg.id) AS cmtcount, 
                ";
    }
    $query .= "   jg.id AS picid, 
                  jg.catid, 
                  jg.imgthumbname, 
                  jg.imgfilename,
                  jg.owner, 
                  jg.imgauthor,
                  jg.imgdate, 
                  jg.imgtitle, 
                  jg.imgtext, 
                  jg.imgcounter, 
                  jg.imgvotes,
                  (jg.imgvotesum/jg.imgvotes) AS vote,
                  jgc.name AS cattitle,
                  jgc.catpath as catpath \n
              ";
    if($this->getConfig('showlastcomment'))
    {
      $query .= ",
                  jgco.cmttext, 
                  jgco.cmtdate, 
                  jgco.userid , 
                  jgco.cmtid \n
                ";
    }
    $query .="  FROM 
                  #__joomgallery AS jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid \n
              ";
    if($this->getConfig('showlastcomment'))
    {
      $query .= " LEFT JOIN 
                    #__joomgallery_comments AS jgco ON jg.id = jgco.cmtpic
                  LEFT JOIN 
                    #__joomgallery_comments jgco2 ON jgco.cmtpic = jgco2.cmtpic
                    AND jgco.cmtdate < jgco2.cmtdate
                  WHERE 
                    jgco2.cmtpic IS NULL
                    AND 
                ";
    }
    else
    {
      $query .= " WHERE ";
    }
    $query .= "      jgc.published = '1'
                  AND jgc.access  <= $aid
                  AND jg.published = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                  AND jg.approved = '1'
                  AND jg.owner = $userId
                ORDER BY 
                  ".$sorting." \n"
                . $limit;

    $database->setQuery($query);
    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }

    return $database->loadObjectList();
  }//End function getPicsOfUser


   /**
   * Returns pictures a user is tagged in
   *
   * @param integer $userId Joomla ID of user
   * @param integer $aid Joomla GroupID of viewing user (access rights). 0 for public viewable!
   * @param string $sorting string for DB sorting
   * @param integer $numPics limit number of pictures; leave away to return all
   * @param integer $limitStart where to start returning $numPics pictures
   * @return db-objs
   */
  function getPicsUserTagged($userId, $aid, $sorting, $numPics = NULL, $limitStart = 0)
  {
    $database = & JFactory::getDBO(); 
    // validation:
    $userId   = intval($userId);
    $aid      = intval($aid);
    $sorting  = $database->getEscaped($sorting);
    if(is_null($numPics))
    { // no limit given: return all pictures
      $limit = '';
    }
    else
    {
      $limitStart = intval($limitStart);
      $numPics    = intval($numPics);

      $limit = "\n LIMIT ".$limitStart.",".$numPics;
    }
    $query = "  SELECT ";
    if($this->getConfig('shownumcomments'))
    {
      $query .= " (SELECT 
                    COUNT(cmtid) 
                  FROM 
                    #__joomgallery_comments 
                  WHERE 
                    cmtpic=jg.id) AS cmtcount, 
                ";
    }
    $query .= "   jg.id AS picid, 
                  jg.catid, 
                  jg.imgthumbname, 
                  jg.imgfilename,
                  jg.imgdate, 
                  jg.imgtitle, 
                  jg.imgtext, 
                  jg.imgcounter, 
                  jg.imgvotes,
                  jg.owner, 
                  jg.imgauthor,
                  (jg.imgvotesum/jg.imgvotes) AS vote,
                  jgc.name AS cattitle,
                  jgc.catpath as catpath \n
              ";
    if($this->getConfig('showlastcomment'))
    {
      $query .= ",
                    jgco.cmttext, 
                    jgco.cmtdate, 
                    jgco.userid , 
                    jgco.cmtid \n";
    }
    $query .= " FROM 
                  #__joomgallery_nameshields AS jgn
                LEFT JOIN 
                  #__joomgallery AS jg ON jgn.npicid = jg.id
                LEFT JOIN #__joomgallery_catg AS jgc ON jgc.cid = jg.catid \n
              ";
    if($this->getConfig('showlastcomment'))
    {
      $query .= " LEFT JOIN 
                    #__joomgallery_comments AS jgco ON jg.id = jgco.cmtpic
                  LEFT JOIN 
                    #__joomgallery_comments jgco2 ON jgco.cmtpic = jgco2.cmtpic
                    AND jgco.cmtdate < jgco2.cmtdate
                  WHERE 
                    jgco2.cmtpic IS NULL
                    AND ";
    }
    else
    {
      $query .= " WHERE ";
    }
      $query .= "      jgc.published = '1'
                    AND jgc.access  <= $aid
                    AND jg.published = '1'
                    ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                    AND jg.approved = '1'
                    AND jgn.nuserid = $userId
                  ORDER BY 
                    ".$sorting."\n"
                  . $limit;

    $database->setQuery($query);
    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }
    return $database->loadObjectList();
  }//End function getPicsUserTagged


  /**
   * Returns the pictures a user has favoured
   *
   * @param integer $userId Joomla ID of user
   * @param integer $aid Joomla GroupID of viewing user (access rights). 0 for public viewable!
   * @param string $sorting string for DB sorting
   * @param integer $numPics limit number of pictures; leave away to return all
   * @param integer $limitStart where to start returning $numPics pictures
   * @return db-objs
   */
  function getPicsUserFavoured($userId, $aid, $sorting, $numPics = NULL, $limitStart = 0)
  {
    $database = & JFactory::getDBO(); 
    // validation:
    $userId   = intval($userId);
    $aid      = intval($aid);
    $sorting  = $database->getEscaped($sorting);
    if(is_null($numPics))
    { // no limit given: return all pictures
      $limit = '';
    }
    else
    {
      $limitStart = intval($limitStart);
      $numPics    = intval($numPics);

      $limit = "\n LIMIT ".$limitStart.",".$numPics;
    }

    $query = "  SELECT 
                  piclist 
                FROM 
                  #__joomgallery_users
                WHERE 
                  uuserid = $userId
              ";
    $database->setQuery($query);
    $piclist = $database->loadResult();

    if(!isset($piclist) || $piclist == '') return NULL;

    $query = "  SELECT ";
    if($this->getConfig('shownumcomments'))
    {
      $query .= " (SELECT 
                    COUNT(cmtid) 
                  FROM 
                    #__joomgallery_comments 
                  WHERE 
                    cmtpic=jg.id) AS cmtcount, 
                ";
    }
    $query .= "   jg.id AS picid, 
                  jg.catid, 
                  jg.imgthumbname, 
                  jg.imgfilename,
                  jg.owner, 
                  jg.imgauthor,
                  jg.imgdate, 
                  jg.imgtitle, 
                  jg.imgtext, 
                  jg.imgcounter, 
                  jg.imgvotes,
                  (jg.imgvotesum/jg.imgvotes) AS vote,
                  jgc.name AS cattitle,
                  jgc.catpath AS catpath \n
              ";
    if($this->getConfig('showlastcomment'))
    {
      $query .= "   ,jgco.cmttext, 
                    jgco.cmtdate, 
                    jgco.userid , 
                    jgco.cmtid \n
                ";
    }
    $query .="  FROM 
                  #__joomgallery AS jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid \n
              ";
  if($this->getConfig('showlastcomment'))
  {
    $query .= " LEFT JOIN 
                  #__joomgallery_comments AS jgco ON jg.id = jgco.cmtpic
                LEFT JOIN 
                  #__joomgallery_comments AS jgco2 ON jgco.cmtpic = jgco2.cmtpic
                  AND jgco.cmtdate < jgco2.cmtdate
                WHERE 
                  jgco2.cmtpic IS NULL
                  AND 
              ";
  }
  else
  {
    $query .= " WHERE ";
  }
  $query .= "      jgc.published = '1'
                AND jgc.access  <= $aid
                AND jg.published = '1'
                ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                AND jg.approved = '1'
                AND jg.id IN (".$piclist .")
              ORDER BY 
                ".$sorting." \n"
              . $limit;

    $database->setQuery($query);
    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }

    return $database->loadObjectList();
  }//End function getPicsUserFavoured


  /**
   * Returns the comments of a user on pictures
   *
   * @param integer $userId Joomla ID of user
   * @param integer $aid Joomla GroupID of viewing user (access rights). 0 for public viewable!
   * @param string $sorting string for DB sorting (default: newest by ID)
   * @param integer $numPics limit number of pictures; leave away to return all
   * @param integer $limitStart where to start returning $numPics pictures
   * @return db-objs
   */
  function getCommentsUser($userId, $aid, $sorting = "jgco.cmtid DESC", $numComments = NULL, $limitStart = 0)
  {
    $database = & JFactory::getDBO(); 
    $userId   = intval($userId);
    $aid      = intval($aid);
    $sorting  = $database->getEscaped($sorting);
    if(is_null($numComments))
    { // no limit given: return all pictures
      $limit = '';
    }
    else
    {
      $limitStart  = intval($limitStart);
      $numComments = intval($numComments);

      $limit = "\n LIMIT ".$limitStart.",".$numComments;
    }
  
    $query = "  SELECT 
                  jgco.cmttext, 
                  jgco.cmtdate,
                  jg.id AS picid, 
                  jg.catid, 
                  jg.imgthumbname, 
                  jg.imgfilename,
                  jg.owner, 
                  jg.imgauthor,
                  jg.imgdate, 
                  jg.imgtitle, 
                  jg.imgtext, 
                  jg.imgcounter, 
                  jg.imgvotes,
                  (jg.imgvotesum/jg.imgvotes) AS vote,
                  jgc.name AS cattitle,
                  jgc.catpath as catpath
                FROM 
                  #__joomgallery_comments AS jgco
                LEFT JOIN 
                  #__joomgallery AS jg ON jgco.cmtpic = jg.id
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
                WHERE 
                       jgc.published = '1'
                  AND jgc.access    <= $aid
                  AND jgco.published = '1'
                  AND jgco.approved  = '1'
                  AND jg.published   = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                  AND jg.approved = '1'
                  AND jgco.userid = $userId
                ORDER BY 
                  ".$sorting."\n"
                . $limit;
    $database->setQuery($query);
  
    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }
  
    return $database->loadObjectList();
  }//End function getCommentsUser
  
  
/**
   * Returns the all (or some ;) ) comments in the gallery as
   * DB-rows
   *
   * @param integer $userId Joomla ID of user
   * @param integer $aid Joomla GroupID of viewing user (access rights). 0 for public viewable!
   * @param string $sorting string for DB sorting (default: newest by ID)
   * @param integer $numPics limit number of pictures; leave away to return all
   * @param integer $limitStart where to start returning $numPics pictures
   * @return db-objs
   */
  function getComments($aid = 0, $sorting = "jgco.cmtid DESC", $numComments = NULL, $limitStart = 0)
  {
    $database = & JFactory::getDBO(); 
    $aid      = intval($aid);
    $sorting  = $database->getEscaped($sorting);
    if(is_null($numComments))
    { // no limit given: return all pictures
      $limit = '';
    }
    else
    {
      $limitStart  = intval($limitStart);
      $numComments = intval($numComments);

      $limit = "\n LIMIT ".$limitStart.",".$numComments;
    }
  
    $query = "  SELECT 
                  jgco.cmttext, 
                  jgco.cmtdate,
                  jg.id AS picid, 
                  jg.catid, 
                  jg.imgthumbname, 
                  jg.imgfilename,
                  jg.owner, 
                  jg.imgauthor,
                  jg.imgdate, 
                  jg.imgtitle, 
                  jg.imgtext, 
                  jg.imgcounter, 
                  jg.imgvotes,
                  (jg.imgvotesum/jg.imgvotes) AS vote,
                  jgc.name AS cattitle,
                  jgc.catpath AS catpath
                FROM 
                  #__joomgallery_comments AS jgco
                LEFT JOIN 
                  #__joomgallery AS jg ON jgco.cmtpic = jg.id
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
                WHERE 
                       jgc.published = '1'
                  AND jgc.access    <= $aid
                  AND jgco.published = '1'
                  AND jgco.approved  = '1'
                  AND jg.published   = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                  AND jg.approved = '1'
                ORDER BY 
                  ".$sorting."\n"
                . $limit;
    $database->setQuery($query);
  
    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }
    
    return $database->loadObjectList();
  }//End function getComments


  /**
   * Fetches ItemID of JoomGallery from a Menu Item in the DB
   * and constructs "&ItemID=X"-Link from it. To prevent malformed
   * URLs e.g. for SEF, an empty string is returned if no valid
   * ItemID can be found in the database.
   *
   * Can also be called statically, not creating any joominterface-Object.
   * For efficency (prevent multiple DB-queries for multiple calles), an
   * instance of the interface caches the ItemID
   *
   * @return string
   */
  function getJoomId()
  {
    $database = & JFactory::getDBO(); 
    if(!isset($this->_itemId) || is_null($this->_itemId))
    {
      $database->setQuery(" SELECT 
                              id 
                            FROM 
                              #__menu 
                            WHERE 
                              link LIKE '%com_joomgallery%' 
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
                                AND access = '1' 
                              ORDER BY 
                                id DESC 
                              Limit 1
                            ");
        $Itemid_jg = $database->loadResult();
      }
      $Itemid_jg = ($Itemid_jg=="" || $Itemid_jg==NULL) ? "" : "&Itemid=".$Itemid_jg;

      $this->_itemId = $Itemid_jg;
    }
    return $this->_itemId;
  }//End function getJoomId


  /**
   * Simple forwarding of Joom_OpenImage:
   * Returns the link to the detail image, as set in JoomGallery
   *
   * @param integer $picid
   * @param string $catpath
   * @param integer $catid
   * @param string $imgfilename
   * @param string $imgtitle
   * @param string $imgtext
   * @return string
   */
  function getPictureLink($picid, $catpath, $catid, $imgfilename, $imgtitle, $imgtext)
  {
    return Joom_OpenImage($this->_jg_config->jg_detailpic_open, $picid,$catpath, $catid, $imgfilename, $imgtitle, $imgtext);
  }//End function getPictureLink


  /**
   * Simple forwarding of Joom_OpenImage:
   * Returns the link to the detail image, as set in JoomGallery
   *
   * @param db-row $obj
   *
   */
  function getPictureLinkO($obj)
  {
    return $this->getPictureLink($obj->picid, $obj->catpath, $obj->catid, $obj->imgfilename, $obj->imgtitle, $obj->imgtext);
  }//End function getPictureLinkO


  /**
  * Adds all elements needed to display JoomGallery pictures
  * properly like CSS. At the moment, Javascript is included in
  * the JG-javascript.
  *
  * Should be called before calling any output function!
  *
  */
  function getPageHeader()
  {
    $document = & JFactory::getDocument();

    $document->addStyleSheet(_JOOM_LIVE_SITE.'components'.DS.'com_joomgallery'.DS.'assets'.DS.'css'.DS.'joom_settings.css');

    // add the main css file
    $document->addStyleSheet(_JOOM_LIVE_SITE.'components'.DS.'com_joomgallery'.DS.'assets'.DS.'css'.DS.'joomgallery.css');

    //add invidual css file if exists
    if(file_exists(JPATH_ROOT.DS.'components'.DS.'com_joomgallery'.DS.'assets'.DS.'css'.DS.'joom_local.css'))
    {
      $document->addStyleSheet(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/css/joom_local.css');
    }
  }//End function getPageHeader


  /**
   * Returns db-row of one image, with optional access verification
   *
   * @param integer $picid ID of picture in gallery
   * @param integer $aid (optional, leave away for public access)
   * @return db-obj
   */
  function getPicture($picid, $aid=0)
  {
    $database = & JFactory::getDBO(); 
    $picid    = intval($picid);
    $aid      = intval($aid);

    $query = "  SELECT ";
    if($this->getConfig('shownumcomments'))
    {
      $query .= "(  SELECT 
                      COUNT(cmtid) 
                    FROM 
                      #__joomgallery_comments 
                    WHERE 
                      cmtpic=jg.id) AS cmtcount, 
                  ";
    }
    $query .= "   jg.id AS picid, 
                  jg.catid, 
                  jg.imgthumbname, 
                  jg.imgfilename,
                  jg.owner, 
                  jg.imgauthor,
                  jg.imgdate, 
                  jg.imgtitle, 
                  jg.imgtext, 
                  jg.imgcounter, 
                  jg.imgvotes,
                  (jg.imgvotesum/jg.imgvotes) AS vote,
                  jgc.name AS cattitle,
                  jgc.catpath AS catpath \n
              ";
    if($this->getConfig('showlastcomment'))
    {
      $query .= "   ,jgco.cmttext, 
                    jgco.cmtdate, 
                    jgco.userid , 
                    jgco.cmtid \n
                ";
    }
    $query .= " FROM 
                  #__joomgallery AS jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid \n
              ";
    if($this->getConfig('showlastcomment'))
    {
      $query .= " LEFT JOIN 
                    #__joomgallery_comments AS jgco ON jg.id = jgco.cmtpic
                  LEFT JOIN 
                    #__joomgallery_comments AS jgco2 ON jgco.cmtpic = jgco2.cmtpic
                    AND jgco.cmtdate < jgco2.cmtdate
                  WHERE jgco2.cmtpic IS NULL
                    AND 
                ";
    }
    else
    {
      $query .= " WHERE ";
    }
    $query .= "      jgc.published = '1'
                  AND jgc.access  <= $aid
                  AND jg.published = '1'
                  AND jg.approved  = '1'
                  AND jg.id        = $picid
              ";

    $database->setQuery($query);
    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }
    $row = $database->loadObject();
    return $row;
  }//End function getPicture


  /**
   * Returns the db-row of a random image, to which a
   * user with GroupID=$aid has access to
   * (e.g. for a simple 1pic module)
   *
   * @param integer $aid (optional access verification, leave away for public access)
   * @return db-objs
   */
  function getRandomPicture($aid=0)
  {
    $database = & JFactory::getDBO(); 
    $aid      = intval($aid);

    $query = "  SELECT ";
  if($this->getConfig('shownumcomments'))
  {
    $query .= " (SELECT 
                  COUNT(cmtid) 
                FROM 
                  #__joomgallery_comments 
                WHERE cmtpic=jg.id) AS cmtcount, 
              ";
  }
  $query .= "   jg.id AS picid, 
                jg.catid, 
                jg.imgthumbname, 
                jg.imgfilename,
                jg.owner, 
                jg.imgauthor,
                jg.imgdate, 
                jg.imgtitle, 
                jg.imgtext, 
                jg.imgcounter, 
                jg.imgvotes,
                (jg.imgvotesum/jg.imgvotes) AS vote,
                jgc.name AS cattitle,
                jgc.catpath AS catpath \n";
    if($this->getConfig('showlastcomment'))
    {
      $query .= "   ,jgco.cmttext, 
                    jgco.cmtdate, 
                    jgco.userid , 
                    jgco.cmtid \n
                ";
    }
    $query .="  FROM 
                  #__joomgallery AS jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid \n
             ";
  if($this->getConfig('showlastcomment'))
  {
    $query .= " LEFT JOIN 
                  #__joomgallery_comments AS jgco ON jg.id = jgco.cmtpic
                LEFT JOIN 
                  #__joomgallery_comments AS jgco2 ON jgco.cmtpic = jgco2.cmtpic
                  AND jgco.cmtdate < jgco2.cmtdate
                WHERE 
                  jgco2.cmtpic IS NULL
                  AND 
              ";
  }
  else
  {
    $query .= " WHERE ";
  }
  $query .= "      jgc.published = '1'
                AND jgc.access  <= $aid
                AND jg.published = '1'
                AND jg.approved  = '1'
              ORDER BY 
                RAND()
              LIMIT 1;";

    $database->setQuery($query);
    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }

    $row = $database->loadObject();
    return $row;


  }//End function getRandomPicture
 /**
  * Returns the number of pictures in a category
  *
  * @param integer $catid ID of category
  * @param integer $aid GroupID of user (for restricted access images)
  * @return integer
  */
  function getNumPicsByCategory($catid, $aid=0)
  {
    $database = & JFactory::getDBO(); 
    $catid    = intval($catid);
    $aid      = intval($aid);
    $query = "  SELECT 
                  COUNT(jg.id) 
                FROM 
                  #__joomgallery AS jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
                WHERE 
                     jgc.published = '1'
                  AND jgc.access  <= $aid
                  AND jg.published = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                  AND jg.approved  = '1'
                  AND jg.catid = $catid
              ";
    $database->setQuery($query);

    return $database->loadResult();
  }//End function getNumPicsByCategory


  /**
   * Returns picture-objs of all images in Category $catid
   *
   * @param integer $catid
   * @param integer $aid
   * @param string $sorting sorting string
   * @param integer $numPics limit number of pictures; leave away to return all
   * @param integer $limitStart where to start returning $numPics pictures
   * @return db-objs
   *
   */
  function getPicsByCategory($catid, $aid, $sorting, $numPics = NULL, $limitStart = 0)
  {
    $database = & JFactory::getDBO(); 
    // validation
    $catid    = intval($catid);
    $aid      = intval($aid);
    $sorting  = $database->getEscaped($sorting);
    if(is_null($numPics))
    { // no limit given: return all pictures
      $limit = '';
    }
    else
    {
      $limitStart = intval($limitStart);
      $numPics    = intval($numPics);

      $limit = "\n LIMIT ".$limitStart.",".$numPics;
    }

    $query = "  SELECT ";
  if($this->getConfig('shownumcomments'))
  {
    $query .= " (SELECT 
                  COUNT(cmtid) 
                FROM 
                  #__joomgallery_comments 
                WHERE 
                  cmtpic=jg.id) AS cmtcount, 
              ";
  }
  $query .= "   jg.id AS picid, 
                jg.catid, 
                jg.imgthumbname, 
                jg.imgfilename,
                jg.owner, 
                jg.imgauthor,
                jg.imgdate, 
                jg.imgtitle, 
                jg.imgtext, 
                jg.imgcounter, 
                jg.imgvotes,
                (jg.imgvotesum/jg.imgvotes) AS vote,
                jgc.name AS cattitle,
                jgc.catpath AS catpath \n
            ";
    if($this->getConfig('showlastcomment'))
    {
      $query .= "   ,jgco.cmttext, 
                    jgco.cmtdate, 
                    jgco.userid , 
                    jgco.cmtid \n
                ";
    }
    $query .= " FROM 
                  #__joomgallery AS jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid \n
              ";
  if($this->getConfig('showlastcomment'))
  {
    $query .= " LEFT JOIN 
                  #__joomgallery_comments AS jgco ON jg.id = jgco.cmtpic
                LEFT JOIN 
                  #__joomgallery_comments AS jgco2 ON jgco.cmtpic = jgco2.cmtpic
                  AND jgco.cmtdate < jgco2.cmtdate
                WHERE 
                  jgco2.cmtpic IS NULL
                AND 
              ";
  }
  else
  {
    $query .= " WHERE ";
  }
  $query .= "      jgc.published = '1'
                AND jgc.access  <= $aid
                AND jg.published = '1'
                ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                AND jg.approved = '1'
                AND jg.catid = $catid
              ORDER BY 
                ".$sorting." \n"
            . $limit;

    $database->setQuery($query);
    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }

    return $database->loadObjectList();
  }//End function getPicsByCategory


  /**
   * Returns number of DB-rows of pictures matching the search string
   * (e.g. for pre-filtering, pagination)
   *
   * @param string $searchstring
   * @param integer $aid Group of viewing user (for access rights)
   * @return integer
   */
  function getNumPicsBySearch($searchstring, $aid=0)
  {
    $database     = & JFactory::getDBO(); 
    $aid          = intval($aid);
    $searchstring = $database->getEscaped(strtolower(trim($searchstring)));

    $query = "  SELECT 
                  COUNT(jg.id) 
                FROM 
                  #__joomgallery AS jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid
                WHERE 
                     jgc.published = '1'
                  AND jgc.access  <= $aid
                  AND jg.published = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                  AND jg.approved = '1'
                  AND (jg.imgtitle LIKE '%$searchstring%'
                    OR jg.imgtext LIKE '%$searchstring%')
              ";
    $database->setQuery($query);

    return $database->loadResult();
  }//End function getNumPicsBySearch


  /**
   * Returns DB-rows of pictures matching the search string
   * E.g. useful for a search mambot
   *
   * @param string $searchstring
   * @param integer $aid
   * @param string $sorting sorting string
   * @param integer $numPics
   * @param integer $limitStart
   * @return db-objs
   */
  function getPicsBySearch($searchstring, $aid, $sorting, $numPics = NULL, $limitStart = 0)
  {
    $database     = & JFactory::getDBO(); 
    $aid          = intval($aid);
    $searchstring = $database->getEscaped(strtolower(trim($searchstring)));
    $sorting      = $database->getEscaped($sorting);

    $dispatcher = & JDispatcher::getInstance();
    $additional = $dispatcher->trigger('onJoomSearch', array(&$searchstring));

    if(is_null($numPics))
    { // no limit given: return all pictures
      $limit = '';
    }
    else
    {
      $limitStart = intval($limitStart);
      $numPics    = intval($numPics);

      $limit = "\n LIMIT ".$limitStart.",".$numPics;
    }

    $query = "  SELECT ";
    if($this->getConfig('shownumcomments'))
    {
      $query .= " (SELECT 
                    COUNT(cmtid) 
                  FROM 
                    #__joomgallery_comments 
                  WHERE 
                    cmtpic=jg.id) AS cmtcount, 
                ";
    }
    $query .= "   jg.id AS picid, 
                  jg.catid, 
                  jg.imgthumbname, 
                  jg.imgfilename,
                  jg.owner, 
                  jg.imgauthor,
                  jg.imgdate, 
                  jg.imgtitle, 
                  jg.imgtext, 
                  jg.imgcounter, 
                  jg.imgvotes,
                  (jg.imgvotesum/jg.imgvotes) AS vote,
                  jgc.name AS cattitle,
                  jgc.catpath AS catpath \n
              ";
    if($this->getConfig('showlastcomment'))
    {
      $query .= "   ,jgco.cmttext, 
                    jgco.cmtdate, 
                    jgco.userid , 
                    jgco.cmtid \n
                ";
    }
    foreach($additional as $add)
    {
      $query .= ', '.implode(', ',$add[0])."\n";
    }
    $query .= " FROM 
                  #__joomgallery AS jg
                LEFT JOIN 
                  #__joomgallery_catg AS jgc ON jgc.cid = jg.catid \n
              ";
    foreach($additional as $add)
    {
      $query .= implode(" \n", $add[1])."\n";
    }
    if($this->getConfig('showlastcomment'))
    {
      $query .= " LEFT JOIN 
                    #__joomgallery_comments AS jgco ON jg.id = jgco.cmtpic
                  LEFT JOIN 
                    #__joomgallery_comments AS jgco2 ON jgco.cmtpic = jgco2.cmtpic
                    AND jgco.cmtdate < jgco2.cmtdate
                  WHERE 
                    jgco2.cmtpic IS NULL
                    AND 
                ";
    }
    else
    {
      $query .= " WHERE ";
    }
    $query .= "      jgc.published = '1'
                  AND jgc.access  <= $aid
                  AND jg.published = '1'
                  ".$this->getConfig('categoryfilter').$this->getConfig('hidebackend')."
                  AND jg.approved = '1'
                  AND (jg.imgtitle LIKE '%$searchstring%'
                    OR jg.imgtext LIKE '%$searchstring%'
              ";
    foreach($additional as $add)
    {
      $query .= "OR ".implode(" \nOR ", $add[2]);
    }
    $query .= ")
              ORDER BY ".$sorting." \n"
              . $limit;
    $database->setQuery($query);
    if(!$database->query())
    {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>n";
      exit();
    }
    
    return $database->loadObjectList();   
  }//End function getPicsBySearch


  /**
   * creates a new category out of the information of the given object
   *
   * @param object  should hold all the information about the new category
   * @return int/boolean id of the created category on success, false otherwise
   */
  function createCategory($obj)
  {
    $database = & JFactory::getDBO();
    jimport('joomla.filesystem.file');

    /*JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomgallery'.DS.'tables');
    $row = & JTable::getInstance('joomgallerycategories', 'Table');*/
    /* deprecated (use JTable instead as shown above): */
    require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomgallery'.DS.'joomgallery.class.php');
    $row = new mosCatgs($database);

    $row->bind($obj);

    //store data in the database
    if(!$row->store())
    {
      return false;
    }

    //now we have the id of the new category
    //and the catpath can be built
    $row->catpath = Joom_FixFilename($row->name).'_'.$row->cid;
    if($row->parent)
    {
      $row->catpath = Joom_GetCatPath($row->parent).$row->catpath;
    }
    //so store again
    if(!$row->store())
    {
      return false;
    }

    //create necessary folders and files
    $origpath   = JPATH_ROOT.DS.$this->_jg_config->jg_pathoriginalimages.$row->catpath;
    $imgpath    = JPATH_ROOT.DS.$this->_jg_config->jg_pathimages.$row->catpath;
    $thumbpath  = JPATH_ROOT.DS.$this->_jg_config->jg_paththumbs.$row->catpath;
    $index      = JPATH_SITE.DS.'components'.DS.'com_joomgallery'.DS.'assets'.DS.'index.html';
    $result     = array();
    $result[]   = JFolder::create($origpath);
    $result[]   = JFile::copy($index, $origpath.DS.'index.html');
    $result[]   = JFolder::create($imgpath);
    $result[]   = JFile::copy($index, $imgpath.DS.'index.html');
    $result[]   = JFolder::create($thumbpath);
    $result[]   = JFile::copy($index, $thumbpath.DS.'index.html');

    if(in_array(false, $result))
    {
      return false;
    }
    else
    {
      return $row->cid;
    }
  }//End function createCategory


  /**
   * is automatically called when an unknown method is called
   * this will happen if JoomGallery is not uptodate
   * works only with PHP 5
   *
   * @param string  name of the unknown method
   * @param array   array of parameters given to the unknown method
   */
  function __call($name, $params){
    JError::raiseError('501', 'JoomGallery is not uptodate. Function '.$name.' does not exist');
  }//End function __call

}//End class joominterface
?>
