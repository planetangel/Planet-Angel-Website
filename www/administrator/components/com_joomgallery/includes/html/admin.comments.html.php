<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/html/admin.comments.html.php $
// $Id: admin.comments.html.php 449 2009-06-14 11:57:04Z aha $
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

/******************************************************************************\
*                            Functions / Comments                              *
\******************************************************************************/

class HTML_Joom_AdminComments {


  /**
   * Comments manager
   *
   * @param string $rows
   * @param string $search
   * @param object $pageNav
   */
  function Joom_ShowComments_HTML(&$rows, &$search, &$pageNav) {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();
    jimport('joomla.filesystem.file');

?>
<script  type="text/javascript" src="<?php echo _JOOM_LIVE_SITE; ?>includes/js/overlib_mini.js"></script>
<form action="index.php" method="post" name="adminForm">
<table cellpadding="4" cellspacing="0" border="0" width="100%">
  <tr>
    <td width="100%"></td>
    <td>
      <?php echo JText::_('JGA_SEARCH') ; ?>
    </td>
    <td>
      <input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
    </td>
  </tr>
</table>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
  <tr>
    <th width="20">
      <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
    </th>
    <th class="title" width="15%">
      <div align="left">
        <?php echo JText::_('JGA_AUTHOR'); ?>
      </div>
    </th>
    <th width="35%">
      <div align="left">
        <?php echo JText::_('JGA_TEXT'); ?>
      </div>
    </th>
    <th width="10%">
      <div align="center">
      <?php echo JText::_('JGA_IP'); ?>
      </div>
    </th>
    <th width="10%">
      <?php echo JText::_('JGA_PUBLISHED'); ?>
    </th>
    <th width="10%">
      <?php echo JText::_('JGA_APPROVED'); ?>
    </th>
    <th width="10%">
      <?php echo JText::_('JGA_PICTURE'); ?>
    </th>
    <th width="24"></th>
    <th width="15%">
      <?php echo JText::_('JGA_DATE'); ?>
    </th>
  </tr>
<?php
      $k = 0;
      for ($i=0, $n=count($rows); $i < $n; $i++) {
        $row = &$rows[$i];
        $task = $row->published ? 'unpublishcmt' : 'publishcmt';
        $img = $row->published ? 'tick.png' : 'publish_x.png';
        $taska = $row->approved ? 'rejectcmt' : 'approvecmt';
        $imga = $row->approved ? 'tick.png' : 'publish_x.png';
?>
  <tr class="<?php echo "row$k"; ?>">
    <td>
      <input type="checkbox" id="cb<?php echo $i;?>" name="id[]" value="<?php echo $row->cmtid; ?>" onclick="isChecked(this.checked);" />
    </td>
    <td>
      <div align="left">
<?php
    if ($row->userid > 0) {
      echo Joom_GetDisplayName($row->userid, false);
    } else{
      echo $row->cmtname;
    }
?>
      </div>
    </td>
    <td>
      <div align="left">
<?php
        $cmttext = Joom_ProcessText($row->cmttext);
?>
        <?php echo $cmttext; ?>
      </div>
    </td>
    <td>
      <div align="center">
        <?php echo $row->cmtip; ?>
      </div>
    </td>
    <td align='center'>
      <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
        <img src="images/<?php echo $img;?>" border="0" alt="" /><?php /* portierung: width und height entfernt */ ?>
      </a>
    </td>
    <td align='center'>
      <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $taska;?>')">
        <img src="images/<?php echo $imga;?>" border="0" alt="" /><?php /* portierung: width und height entfernt */ ?>
      </a>
    </td>
    <td width="10%" align="center">
      <?php echo $row->cmtpic; ?>
    </td>
    <td>
<?php
        $database->setQuery("SELECT imgthumbname
            FROM #__joomgallery
            WHERE id = '$row->cmtpic'");
        $is_imgthumbname = $database->loadResult();
        $database->setQuery("SELECT catid
            FROM #__joomgallery
            WHERE id = '$row->cmtpic'");
        $is_catid = $database->loadResult();
        $catpath = Joom_GetCatPath($is_catid);

        if (JFile::exists(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$is_imgthumbname)) {
          $imginfo = getimagesize(JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$is_imgthumbname));
          $imgsource = _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$is_imgthumbname;
          $srcWidth = $imginfo[0];
          $srcHeight = $imginfo[1];
          $thumbexists = 1;
        } else {
          $thumbexists = 0;
        }
  
        if ($thumbexists) {
?>
      <a href="<?php echo _JOOM_LIVE_SITE; ?>index.php?option=<?php echo _JOOM_OPTION; ?>&amp;func=detail&amp;id=<?php echo $row->cmtpic; ?>" onmouseover="return overlib('<img src=\'<?php echo $imgsource; ?>\' />',WIDTH,<?php echo $srcWidth; ?>, HEIGHT,<?php echo $srcHeight; ?>)"  onmouseout="return nd()";target="_blank">
        <img src="<?php echo $imgsource; ?>" border="0" width="24" height="24" alt="" />
      </a>
<?php
        } else {
?>
      &nbsp;
<?php
        }
?>

    </td>
    <td width="10%" align="center">
      <?php echo strftime($config->jg_dateformat, $row->cmtdate); ?>
    </td>
<?php
        $k = 1 - $k;
?>
  </tr>
<?php
      }
?>
  <tr>
    <td colspan="9">
      <?php echo $pageNav->getListFooter(); ?>
    </td>
  </tr>
</table>
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION;?>" />
<input type="hidden" name="task" value="comments" />
<input type="hidden" name="boxchecked" value="0" />
</form>
<?php
  }

}
?>
