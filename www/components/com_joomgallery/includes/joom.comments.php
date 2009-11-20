<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.comments.php $
// $Id: joom.comments.php 449 2009-06-14 11:57:04Z aha $
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

class Joom_Comments
{
  var $cmtid;
  var $cmtname;
  var $cmtpic;
  var $userid;
  var $cmttext;
  var $jg_captcha_id;
  var $jg_code;

  function Joom_Comments(&$func,&$id)
  {
    $user = & JFactory::getUser();  // anstatt global $my;
    include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'joom.comments.html.php');

    $this->jg_code       = Joom_FixUserEntrie2(Joom_mosGetParam('jg_code', '', 'post'));
    $this->cmtname       = Joom_FixUserEntrie(Joom_mosGetParam('cmtname', '', 'post'));
    $this->cmttext       = Joom_FixUserEntrie(Joom_mosGetParam('cmttext', '', 'post'));
    $this->jg_captcha_id = JRequest::getInt('jg_captcha_id', '', 'post');
    $this->cmtid         = JRequest::getInt('cmtid', 0 );
    $this->userid        = $user->get('id');
    // do not save username when registered user:
    if($this->userid > 0)
    {
      $this->cmtname = '';
    }
    $this->cmtpic = JRequest::getInt('cmtpic', 0, 'post');
    if($this->cmtpic == '')
    {
      $this->cmtpic = JRequest::getInt('cmtpic', '');
    }

    switch($func)
    {
      case 'commentpic':
        $this->Joom_CommentPic($id);
        break;
      case 'deletecomment':
        $this->Joom_DeleteComment();
        break;
    }
  }// End function Joom_Comments



  function Joom_CommentPic($id)
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    //Check for hacking attempt
    $database->setQuery(" SELECT
                            COUNT(id)
                          FROM 
                            #__joomgallery AS a
                          LEFT JOIN 
                            #__joomgallery_catg AS c ON c.cid=a.catid
                          WHERE 
                                a.published = '1' 
                            AND a.approved  = '1'
                            AND a.id        = '".$id."' 
                            AND c.access   <= '".$user->get('aid')."'
                       ");
    $result = $database->loadResult();

   if(   $result != 1 
      || $config->jg_showcomment == 0 
      || $config->jg_anoncomment == 0 && $user->get('aid') < 1
     ) 
    {
      die('Hacking attempt, aborted!');
    }

    $codeisright = 1;
    if($config->jg_secimages == 2 || ($config->jg_secimages == 1 && $user->get('aid') < 1))
    {
      if(file_exists(JPATH_ROOT.DS.'components'.DS.'com_easycaptcha'.DS.'class.easycaptcha.php'))
      {
        include_once(JPATH_ROOT.DS.'components'.DS.'com_easycaptcha'.DS.'class.easycaptcha.php');
        $captcha     = new easyCaptcha($this->jg_captcha_id);
        $codeisright = $captcha->checkEnteredCode($this->jg_code) ? 1 : 0;
      }
    }

    if($codeisright == 1)
    {
      // Save new values
      $cmtip   = $_SERVER['REMOTE_ADDR'];
      $cmtdate = time();
      if(( $config->jg_approvecom == 0 ) || ( $config->jg_approvecom == 1 && $user->get('aid') > 0 ))
      {
        $approve = 1;
      }
      elseif(   ($config->jg_approvecom == 1 && $user->get('aid') < 1) 
             || ($config->jg_approvecom == 2) 
            )
      {
        $approve = 0;
        // message about new comment TODO
        $cmtsenderid = ($user->get('aid')<1) ? "62" : $user->get('id');
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_messages'.DS.'tables'.DS.'message.php' );
        $database->setQuery(" SELECT 
                                id 
                              FROM 
                                #__users 
                              WHERE 
                                sendEmail = '1'
                            ");
        $users = $database->loadResultArray();
        foreach($users as $user_id)
        {
          $msg = new TableMessage($database);
          $msg->send($cmtsenderid,$user_id, JText::_('JGS_ALERT_NEW_COMMENT'), 
                     JText::_('JGS_ALERT_NEW_COMMENT_MESSAGE_PARTONE').$this->cmtname
                    .JText::_('JGS_ALERT_NEW_COMMENT_MESSAGE_PARTTWO'));
        }
      }

      //change \r\n or \n to <br />
      $this->cmttext=nl2br(stripslashes($this->cmttext));
      $database->setQuery(" INSERT INTO 
                              #__joomgallery_comments
                            VALUES(
                                    '', 
                                    '$id', 
                                    '$cmtip',
                                    '$this->userid', 
                                    '$this->cmtname', 
                                    '$this->cmttext', 
                                    '$cmtdate', 
                                    '1', 
                                    '$approve'
                                   )
                         ");
      $database->query();

      # Get back to details page
      if(   ($config->jg_approvecom == 0) 
         || ($config->jg_approvecom == 1 && $user->get('aid') > 0)
        )
      {
        $mosmsg = JText::_('JGS_ALERT_COMMENT_SAVED');
      }
      else
      {
        $mosmsg = JText::_('JGS_ALERT_COMMENT_SAVED_BUT_NEEDS_ARROVAL');
      }
      $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&func=detail&id='.$id._JOOM_ITEMID,false),$mosmsg);
    }
    else
    {
?>
          <form id="send_form" name="commentform" action="<?php echo JRoute::_('index.php?option=com_joomgallery&func=detail&id='. $id._JOOM_ITEMID.'#joomcommentform'); ?>" method="post" class="jg_displaynone">
            <textarea cols="40" rows="8" name="cmttext" class="inputbox" wrap="virtual">
              <?php echo $this->cmttext?>
            </textarea>
          </form>
         <script type="text/javascript">
           alert("<?php echo JText::_('JGS_ALERT_SECURITY_CODE_WRONG',true); ?>");
           document.getElementById('send_form').submit();
         </script>
<?php
    }
  }// End function Joom_CommentPic


  function Joom_DeleteComment()
  {
    HTML_Joom_Comments::Joom_DeleteComment_HTML();
  }//End function Joom_DeleteComment

}//End class Joom_Comments
?>
