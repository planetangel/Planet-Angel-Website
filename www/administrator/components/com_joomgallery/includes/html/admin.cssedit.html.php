<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/html/admin.cssedit.html.php $
// $Id: admin.cssedit.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_AdminCssEdit {

  /**
   * Edit CSS
   *
   * @param string $content
   * @param string $path
   * @param bool $editExistingFile
   * @param string $msg
   * @return HTML_Joom_AdminCssEdit
   */
  function HTML_Joom_AdminCssEdit($content, $path, $editExistingFile, $msg) {
?>
  <script language="javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'cancelcss' || pressbutton == 'savecss') {
        submitform( pressbutton );
        return;
      }
      else if (pressbutton == 'deletecss'){
        if (confirm('<?php echo JText::_('JGA_CSS_CONFIRM_DELETE',true); ?>')){
          submitform(pressbutton);
        }
        return;
      }
    }
  </script>
  <table cellpadding="4" cellspacing="0" border="0" width="100%">
    <tr>
      <td>
        <p>
          <?php echo ($editExistingFile)? JText::_('JGA_EDIT_CSS_EXPLANATION') : JText::_('JGA_NEW_CSS_EXPLANATION'); ?>      
        </p>
      </td>
    </tr>
  </table>
  <form action="index.php" name="adminForm" method="post">
    <table class="adminform" width="100%" border="0" cellpadding="4" cellspacing="0">
      <tbody>
        <tr>
          <th><?php echo $path ?></th>
        </tr>
        <tr>
          <td>
            <textarea cols="110" rows="25" name="csscontent" class="inputbox"><?php echo $content ?></textarea>
          </td>
        </tr>
        <tr>
          <td class="error"><?php echo $msg; ?></td>
        </tr>
      </tbody>
    </table>
    <input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>">
    <input type="hidden" name="task" value="">
    <input type="hidden" name="hidemainmenu" value="" />
    <input type="hidden" name="boxchecked" value="1" />
  </form>
<?php
  }
}
?>
 
