<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/html/joom.comments.html.php $
// $Id: joom.comments.html.php 449 2009-06-14 11:57:04Z aha $
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


class HTML_Joom_Comments 
{

  //Loeschen von Kommentaren
  function Joom_DeleteComment_HTML()
  {
    $config    = Joom_getConfig();
    $user      = & JFactory::getUser();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();

    Joom_GalleryHeader();

    // Main Part of Subfunction

    if(($user->get('gid') ==  20) || ($user->get('gid')==  24) || ($user->get('gid' )== 25))
    {
      if(isset($_REQUEST['submit']) && $_REQUEST['submit'] != '')
      {
        $database->setQuery(" DELETE
                              FROM 
                                #__joomgallery_comments
                              WHERE 
                                cmtid = '".$this->cmtid."'
                            ");
        $database->query();
        $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&func=detail&id='.$this->cmtpic._JOOM_ITEMID,false),
                                       JText::_('JGS_ALERT_COMMENT_DELETED'));
      }
      else
      {
?>
  <div class="jg_clearboth"></div>
  <div class="sectiontableheader jg_cmtl">
    <?php echo JText::_('JGS_AUTHOR'); ?> 
  </div>
  <div class="sectiontableheader jg_cmtr">
    <?php echo JText::_('JGS_COMMENT'); ?> 
  </div>
<?php
        $database->setQuery(" SELECT 
                                cmtid, 
                                cmtip, 
                                userid, 
                                cmtname, 
                                cmttext, 
                                cmtdate, 
                                cmtpic, 
                                username
                              FROM 
                                #__joomgallery_comments AS cm
                              LEFT JOIN 
                                #__users AS u ON cm.userid=u.id
                              WHERE 
                                cmtid = '".$this->cmtid."'
                            ");
        $result1 = $database->LoadRow();
        list($cmtid, $cmtip, $userid,$cmtname, $cmttext, $cmtdate, $cmtpic,$username) = $result1;
?>
  <div class="sectiontableentry1">
    <div class="jg_cmtl">
      <b>
<?php 
        if($userid > 0)
        {
?>
        <?php echo $username; ?> 
<?php         }
        else
        {
?>
        <?php echo $cmtname; ?> 
<?php
        }
?>
      </b>
      <br />
      <a href="http://openrbl.org/query?i=<?php echo $cmtip;?>">
        <img src="<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/ip.gif" alt="<?php echo $cmtip; ?>" title="<?php echo $cmtip; ?>" hspace="3" border="0" />
      </a>
    </div>
<?php
        $signtime = strftime($config->jg_dateformat, $cmtdate );
        $origtext = $cmttext;
?>
    <div class="jg_cmtr small">
      <?php echo JText::_('JGS_COMMENT_ADDED') . ": " . $signtime; ?><hr>
      <?php echo $origtext; ?> 
    </div>
  </div>
  <div class="jg_cmtronly">
    <form action="<?php echo JRoute::_('index.php?option=com_joomgallery&func=deletecomment&cmtid='.$cmtid.'&cmtpic='.$cmtpic._JOOM_ITEMID); ?>" method="post">
      <input class="button" type="submit" name="submit" value="<?php echo JText::_('JGS_DELETE_COMMENT'); ?>" />
    </form>
  </div>
<?php
      }
    }
    else
    {
?>
  <p />
  <a href="<?php echo JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID); ?>">
    <?php echo JText::_('JGS_BACK'); ?> 
  </a>
<?php
    }
  }//End function Joom_DeleteComment_HTML

}//End class HTML_Joom_Comments
?>
