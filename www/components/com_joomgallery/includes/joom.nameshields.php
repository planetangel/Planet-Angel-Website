<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.nameshields.php $
// $Id: joom.nameshields.php 1343 2009-04-30 09:58:05Z mab $
/******************************************************************************\
**   JoomGallery  1.5.0                                                       **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class Joom_Nameshields 
{
  var $length;
  var $height;
  var $yvalue;
  var $xvalue;
  var $zindex;
  var $nuserid;
  var $npicid;
  var $userid;
  var $picid;

  var $details_url;

  /**
  * Class constructor
  *
  */
  function Joom_Nameshields(&$func)
  {
    $this->length      = JRequest::getInt('length',  0, 'post');
    $this->height      = JRequest::getInt('height',  0, 'post');
    $this->yvalue      = JRequest::getInt('yvalue',  0, 'post');
    $this->xvalue      = JRequest::getInt('xvalue',  0, 'post');
    $this->zindex      = JRequest::getInt('zindex',  0, 'post');
    $this->nuserid     = JRequest::getInt('nuserid', 0);
    $this->npicid      = JRequest::getInt('npicid',  0);
    $this->userid      = JRequest::getInt('uid', 0);
    $this->picid       = JRequest::getInt('id',  0);

    $this->details_url = 'index.php?option=com_joomgallery&func=detail&id='; 

    switch ($func)
    {
      case 'savenameshield':
        $this->Joom_SaveNameshield();
        break;
      case 'deletenameshield':
        $this->Joom_DeleteNameshield();
        break;
      default:
        break;
    }
  }//End function Joom_Nameshields


  function Joom_SaveNameshield() 
  {
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();

    if(($this->xvalue < $this->height) && ($this->yvalue < $this->length))
    {
      $mainframe->redirect(JRoute::_($this->details_url.$this->picid._JOOM_ITEMID,false),
                                     JText::_('JGS_ALERT_NAMESHIELD_NOT_SAVED'));
    }
    else
    {
      $row = new mosNameshields($database);

      if (!$row->bind($_POST, "npicid nuserid nxvalue nyvalue nuserip ndate nzindex"))
      {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
      }

      $row->npicid  = $this->picid;
      $row->nuserid = $this->userid;
      $row->nxvalue = $this->xvalue;
      $row->nyvalue = $this->yvalue;
      $row->nuserip = $_SERVER['REMOTE_ADDR'];
      $row->ndate   = mktime();
      $row->nzindex = $this->zindex;

      if (!$row->store())
      {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
      }
      $mainframe->redirect(JRoute::_($this->details_url.$this->picid._JOOM_ITEMID,false),
                                     JText::_('JGS_ALERT_NAMESHIELD_SAVED'));
    }
  }//End function Joom_SaveNameshield


  function Joom_DeleteNameshield()
  {
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    if($user->get('id') == $this->nuserid)
    {
      $database->setQuery(" DELETE
                            FROM 
                              #__joomgallery_nameshields
                            WHERE 
                                  npicid  = '".$this->npicid."' 
                              AND nuserid = '".$this->nuserid."'
                          ");
      if(!$database->query())
      {
        echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
      }
    }
    $mainframe->redirect(JRoute::_($this->details_url.$this->npicid._JOOM_ITEMID,false),
                                   JText::_('JGS_ALERT_NAMESHIELD_DELETED'));
  }//End function Joom_DeleteNameshield

}//End class Joom_Nameshields
?>
