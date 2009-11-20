<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/admin.votes.php $
// $Id: admin.votes.php 449 2009-06-14 11:57:04Z aha $
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

class Joom_AdminVotes  {

  /**
   * Constructor for class Joom_AdminVotes
   * Vote manager
   *
   * @return Joom_AdminVotes
   */
  function Joom_AdminVotes(){
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();

    //cpanel
    echo "<script language = \"javascript\" type = \"text/javascript\">\n";
    echo "<!--\n";
    echo "function submitbutton(pressbutton) {\n";
    echo "  var form = document.adminForm;\n";
    echo "  if (pressbutton == 'cpanel') {\n";
    echo "    location.href = \"index.php?option="._JOOM_OPTION."\";\n";
    echo "  }\n";
    echo "}\n";
    echo "//-->\n";
    echo "</script>\n";

    $sync = Joom_mosGetParam('votes_sync', '', 'post');
    $reset = Joom_mosGetParam('votes_reset', '', 'post');

    if ($sync){
      //Synchronize user-votes
      $query = "DELETE v from #__joomgallery_votes AS v \n"
          ."LEFT JOIN #__users AS u ON v.userid = u.id \n"
          ."WHERE v.userid != 0 \n"
          ."AND u.id IS NULL";

      $database->setQuery($query);
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        return false;
      }

      $query = "UPDATE #__joomgallery AS p set \n"
          ."p.imgvotes = \n"
          ."(select count(*) FROM #__joomgallery_votes as v \n"
          . "WHERE v.picid = p.id), \n"
          . "p.imgvotesum = \n"
          . "(select sum(vote) FROM #__joomgallery_votes as v \n"
          . "WHERE v.picid = p.id)";

      $database->setQuery($query);
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        return false;
      }
      $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=votes',JText::_('JGA_USERVOTES_SYNCHRONIZED'));

    } else if ($reset){
      //delete all votes
      $query = "DELETE FROM #__joomgallery_votes";
      $database->setQuery($query);
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg() ."'); window.history.go(-1); </script>\n";
        return false;
      }

      $query = "UPDATE #__joomgallery SET imgvotes = 0, imgvotesum = 0";
      $database->setQuery($query);
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg() ."'); window.history.go(-1); </script>\n";
        return;
      }

      //done:
      $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=votes',JText::_('JGA_ALL_VOTES_DELETED'));

    }

    require_once (JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'admin.votes.html.php');
    $htmladminvotes=new HTML_Joom_AdminVotes();
  }
}
?>
