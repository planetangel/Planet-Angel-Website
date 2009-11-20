<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/html/admin.votes.html.php $
// $Id: admin.votes.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_AdminVotes {

  /**
   * Votes manager
   *
   * @return HTML_Joom_AdminVotes
   */
  function HTML_Joom_AdminVotes() {
?>
  <script type="text/javascript">
  function confirmation(){
    var msg = "<?php echo JText::_('JGA_ALERT_RESET_VOTES_CONFIRM',true) ?>";
    return(confirm(msg));
  }
  </script>
  <form action="index.php?option=<?php echo _JOOM_OPTION; ?>&amp;act=votes" name="adminForm" method="post" onsubmit="return confirmation()">
  <table width="100%" border="0" cellpadding="4" cellspacing="2" class="adminlist">
    <tr>
      <td style="padding:4px;" width="20%" align="center">
        <input type="submit" name="votes_sync" value="<?php echo JText::_('JGA_SYNCHRONIZE_VOTES'); ?>" style="width:160px;" />
      </td>
      <td style="padding:4px;" width="80%">
        <?php echo JText::_('JGA_SYNCHRONIZE_VOTES_LONG'); ?> 
    </td>
    </tr>
    <tr>
      <td style="padding:4px;" align="center">
        <input type="submit" name="votes_reset" value="<?php echo JText::_('JGA_RESET_VOTES'); ?>" style="width:160px;" />
      </td>
      <td style="padding:4px;">
        <?php echo JText::_('JGA_RESET_VOTES_LONG'); ?> 
      </td>
    </tr>
  </table>
  </form>
<?php
  }
}
?>
