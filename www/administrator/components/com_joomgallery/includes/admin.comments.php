<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/admin.comments.php $
// $Id: admin.comments.php 449 2009-06-14 11:57:04Z aha $
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

include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'admin.comments.html.php');

class Joom_AdminComments {

  /**
   * Constructor of class Joom_AdminComments
   *
   * @param string $task
   * @param int $id  id of comment
   * @return Joom_AdminComments
   */
  function Joom_AdminComments($task, $id) {
    switch($task) {
      case 'comments':
        $this->Joom_ShowComments();
        break;
      case 'publishcmt':
        $this->Joom_PublishComments($id, 1);
        break;
      case 'unpublishcmt':
        $this->Joom_PublishComments($id, 0);
        break;
      case 'approvecmt':
        $this->Joom_ApproveComments($id, 1);
        break;
      case 'rejectcmt':
        $this->Joom_ApproveComments($id, 0);
        break;
      case 'removecmt':
        $this->Joom_RemoveComments($id);
        break;
    }
  }

  /**
   * Show all or specific comments in comment manager
   *
   */
  function Joom_ShowComments() {
    $database = & JFactory::getDBO();

    //Prepare pagelimit choices
    $limit = JRequest::getInt('limit', 10, 'post');
    $limitstart = JRequest::getInt('limitstart', 0, 'post');

    //Prepare search choices
    $search = trim(strtolower(Joom_mosGetParam('search', '', 'post')));
    $where = array();
    if ($search) {
      $where[] = "LOWER(cmttext) LIKE '%$search%' OR LOWER(u.username) LIKE '%$search%' OR LOWER(cmtname) LIKE '%$search%' ";
    }

    //Get total number of records
    $database->setQuery("SELECT count(*)
        FROM #__joomgallery_comments AS a "
        ."LEFT JOIN #__users AS u ON userid = u.id "
        .(count( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : ''));
    $total = $database->loadResult();
    echo $database->getErrorMsg();
    if ($limit > $total) {
      $limitstart = 0;
    }
    //new PageNavigation
    jimport('joomla.html.pagination');
    $pageNav = new JPagination( $total, $limitstart, $limit );

    // do the main database query
    $database->setQuery("SELECT *
        FROM #__joomgallery_comments "
        ."LEFT JOIN #__users AS u ON userid = u.id "
        . (count( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '')
        . " ORDER BY cmtdate DESC",$pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();
    if ($database->getErrorNum()) {
      echo $database->stderr();
      return false;
    }

    //Bring it all to the screen
    HTML_Joom_AdminComments::Joom_ShowComments_HTML($rows, $search,$pageNav);
  }

  /**
   * Publish/unpublish one or more comments
   *
   * @param int/array $cid id(s) of comment(s) to publish/unpublish
   * @param int $publish 1=publish 0=unpublish
   */
  function Joom_PublishComments($cid=null, $publish=1) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    if (!is_array($cid) || count($cid) < 1) {
      $action = $publish ? 'publish' : 'unpublish';
      echo "<script> alert('".JText::_('JGA_ALERT_SELECT_AN_ITEM',true)." $action');
            window.history.go(-1);</script>\n";
      exit;
    }

    $cids = implode(',', $cid);

    $database->setQuery( "UPDATE #__joomgallery_comments
        SET published='$publish'
        WHERE cmtid IN ($cids)" );
    if (!$database->query()) {
      echo "<script> alert('".$database->getErrorMsg()."');
            window.history.go(-1); </script>\n";
      exit();
    }
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=comments');
  }

  /**
   * Approve/unapprove one or more comments
   *
   * @param int/array $cid id(s) of comment(s) to approve/unapprove
   * @param int $approve 1=approve 0=unapprove
   */
  function Joom_ApproveComments($cid=null, $approve=1) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    if (!is_array($cid) || count($cid) < 1) {
      $action = $approve ? 'approvecmt' : 'rejectcmt';
      echo "<script> alert('" . JText::_('JGA_ALERT_SELECT_AN_ITEM',true) . " $action');
            window.history.go(-1);</script>\n";
      exit;
    }

    $cids = implode(',', $cid);

    $database->setQuery( "UPDATE #__joomgallery_comments
        SET approved='$approve'
        WHERE cmtid IN ($cids)" );
    if (!$database->query()) {
      echo "<script> alert('".$database->getErrorMsg()."');
            window.history.go(-1); </script>\n";
      exit();
    }
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=comments');
  }

  /**
   * Remove one or more comments
   *
   * @param int/array $cid id(s) of comment(s) to be removed
   */
  function Joom_RemoveComments($cid) {
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    if (!is_array( $cid ) || count( $cid ) < 1) {
      echo "<script> alert('".JText::_('JGA_ALERT_SELECT_AN_ITEM_TO_DELETE',true)."');
            window.history.go(-1);</script>\n";
      exit;
    }
    if (count($cid)) {
      $cids = implode(',', $cid);
      $database->setQuery( "DELETE
          FROM #__joomgallery_comments
          WHERE cmtid IN ($cids)" );
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."');
              window.history.go(-1); </script>\n";
      }
    }
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=comments');
  }

}
?>
