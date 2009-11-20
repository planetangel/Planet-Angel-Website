<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/html/admin.pictures.html.php $
// $Id: admin.pictures.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_AdminPictures {


  /**
   * Picture manager
   *
   * @param array of objects $rows
   * @param string $clist
   * @param string $slist
   * @param string $search
   * @param object $pageNav
   * @param string $olist
   */
  function Joom_ShowPictures_HTML(&$rows, &$clist, &$slist, &$search,
                                  &$pageNav, &$olist) {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();
    jimport('joomla.filesystem.file');
?>
<script  type="text/javascript" src="<?php echo _JOOM_LIVE_SITE; ?>includes/js/overlib_mini.js"></script>
<form action="index.php" method="post" name="adminForm">
<table cellpadding="4" cellspacing="0" border="0" width="100%">
  <tr>
    <td width="100%"></td>
    <td><?php echo JText::_('JGA_SEARCH'); ?><br />
      <input type="text" name="search" value="<?php echo $search; ?>"
       class="inputbox" onChange="document.adminForm.submit();" />
    </td>
    <td nowrap>
      <?php echo JText::_('JGA_SORT_BY_ORDER'); ?><br />
      <?php echo $olist; ?>
    </td>
    <td nowrap>
      <?php echo JText::_('JGA_SORT_BY_CATEGORY'); ?><br />
      <?php echo $clist; ?>
    </td>
    <td><?php echo JText::_('JGA_SORT_BY_TYPE'); ?><br />
      <?php echo $slist; ?>
    </td>
  </tr>
</table>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
  <tr>
    <th width="20">
      <input type="checkbox" name="toggle" value=""
       onclick="checkAll(<?php echo count($rows); ?>);" />
    </th>
    <th width="5%"></th>
    <th width="5%" align="left">ID</th>
    <th class="title" width="20%">
      <?php echo JText::_('JGA_TITLE'); ?>
    </th>
    <th width="20%">
      <div align="left">
        <?php echo JText::_('JGA_CATEGORY'); ?>
      </div>
    </th>
    <th width="5%">
      <div align="center">
        <?php echo JText::_('JGA_HITS'); ?>
      </div>
    </th>
    <th width="10%" colspan="2">
      <div align="center">
        <?php echo JText::_('JGA_ORDERING'); ?>
      </div>
    </th>
    <th width="5%">
      <div align="center">
        <a href="javascript: saveorder( <?php echo count($rows)-1; ?> )">
          <img src="images/filesave.png" border="0" width="16" height="16"
           alt="<?php echo JText::_('JGA_SAVE_ORDER'); ?>" />
        </a>
      </div>
    </th>
    <th width="5%">
      <?php echo JText::_('JGA_PUBLISHED'); ?>
    </th>
    <th width="5%">
      <?php echo JText::_('JGA_APPROVED'); ?>
    </th>
    <th width="5%">
      <?php echo JText::_('JGA_OWNER'); ?>
    </th>
    <th width="5%">
      <?php echo JText::_('JGA_AUTHOR'); ?>
    </th>
    <th width="5%">
      <?php echo JText::_('JGA_TYPE'); ?>
    </th>
    <th width="15%">
      <?php echo JText::_('JGA_DATE'); ?>
    </th>
  </tr>
<?php
    $k = 0;
    for ($i=0, $n=count($rows); $i < $n; $i++) {
      $row = &$rows[$i];
      $taska = $row->approved ? 'rejectpic' : 'approvepic';
      $imga = $row->approved ? 'tick.png' : 'publish_x.png';
      $task = $row->published ? 'unpublishpic' : 'publishpic';
      $img = $row->published ? 'tick.png' : 'publish_x.png';

      $catpath = Joom_GetCatPath($row->catid);
      if (JFile::exists(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->imgthumbname)) {
        $imginfo = getimagesize(JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->imgthumbname));
        $imgsource = _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->imgthumbname;
        $srcWidth = $imginfo[0];
        $srcHeight = $imginfo[1];
        $thumbexists = 1;
      } else {
        $thumbexists = 0;
      }
?>
  <tr class="<?php echo "row$k"; ?>">
    <td>
      <input type="checkbox" id="cb<?php echo $i; ?>" name="id[]"
       value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
    </td>
    <td><?php
      if ($thumbexists) {
?>
      <a href="#edit" onmouseover="return overlib('<img src=\'<?php echo $imgsource; ?>\' />',WIDTH,<?php echo $srcWidth; ?>, HEIGHT,<?php echo $srcHeight; ?>)"  onmouseout="return nd()"; onclick="return listItemTask('cb<?php echo $i;?>','editpic')" alt=""/>
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
    <td>
      <?php echo $row->id; ?>
    </td>
    <td>
      <div align="left">
        <a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','editpic')">
          <?php echo $row->imgtitle; ?>
        </a>
      </div>
    </td>
    <td>
      <div align="left">
        <?php echo Joom_ShowCategoryPath($row->catid); ?>
      </div>
    </td>
    <td>
      <div align="center">
        <?php echo $row->imgcounter; ?>
      </div>
    </td>
    <td align="center">
      <?php echo $pageNav->orderUpIcon($i, ($row->catid == @$rows[$i-1]->catid)); ?>
      <?php echo $pageNav->orderDownIcon($i, $n, ($row->catid == @$rows[$i+1]->catid)); ?>
    </td>
    <td align="center" colspan="2">
      <input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"
       class="text_area" style="text-align: center" />
    </td>
    <td align='center'>
      <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
        <img src="images/<?php echo $img; ?>" border="0" alt="" />
      </a>
    </td>
    <td align='center'>
      <a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $taska; ?>')">
        <img src="images/<?php echo $imga; ?>" border="0" alt="" />
      </a>
    </td>
    <td width="5%" align="center">
      <?php echo Joom_GetDisplayName($row->owner, true); ?>
    </td>
    <td width="5%" align="center">
<?php
      if ($row->imgauthor) {
?>
        <?php echo $row->imgauthor; ?>
<?php
      }
?>
    </td>
    <td width="5%" align="center">
<?php
      if ($row->useruploaded) {
?>
      <img src="../includes/js/ThemeOffice/users.png"
       alt="<?php echo JText::_('JGA_USER_UPLOAD'); ?>"
       title="<?php echo JText::_('JGA_USER_UPLOAD'); ?>" />
<?php
      } else {
?>
      <img src="../includes/js/ThemeOffice/credits.png"
       alt="<?php echo JText::_('JGA_ADMIN_UPLOAD'); ?>"
       title="<?php echo JText::_('JGA_ADMIN_UPLOAD'); ?>" />
<?php
      }
?>
    </td>
    <td width="10%" align="center">
      <?php echo strftime($config->jg_dateformat, $row->imgdate); ?>
    </td>
<?php
      $k = 1 - $k;
?>
  </tr>
<?php
    }
?>
  <tr>
    <td colspan="15">
      <?php echo $pageNav->getListFooter(); ?>
    </td>
  </tr>
</table>
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
<input type="hidden" name="task" value="pictures" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="returntask" value="pictures" />
</form>
<?php
  }


  /**
   * New picture
   *
   * @param string $newpic_clist
   * @param string $newpic_cplist
   * @param string $newpic_ctlist
   * @param string $newpic_imagelist
   * @param string $newpic_thumblist
   * @param string $copy_original_list
   * @param string $original_message
   */
  function Joom_ShowNewPicture_HTML(&$Lists, $original_message) {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
?>
<script language="javascript" type="text/javascript">
  function submitbutton(pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'canceleditpic') {
      submitform(pressbutton);
      return;
    }
    // do field validation
    form.imgtitle.style.backgroundColor = '';
    form.catid.style.backgroundColor = '';
    form.imgfilename.style.backgroundColor = '';
    form.imgthumbname.style.backgroundColor = '';
    var ffwrong = '<?php echo $config->jg_wrongvaluecolor; ?>';
    if (form.imgtitle.value == '' || form.imgtitle.value == null){
      alert('<?php echo JText::_('JGA_ALERT_PICTURE_MUST_HAVE_TITLE',true); ?>');
      form.imgtitle.style.backgroundColor = ffwrong;
      form.imgtitle.focus();
    }
    else if (form.catid.value == "0"){
      alert('<?php echo JText::_('JGA_ALERT_YOU_MUST_SELECT_CATEGORY',true); ?>');
      form.catid.style.backgroundColor = ffwrong;
      form.catid.focus();
    }
    else if (form.imgfilename.value == ''|| form.imgfilename.value == null){
      alert('<?php echo JText::_('JGA_ALERT_YOU_MUST_SELECT_PICTURE_FILENAME',true); ?>');
      form.imgfilename.style.backgroundColor = ffwrong;
      form.imgfilename.focus();
    }
    else if (form.imgthumbname.value == '' || form.imgthumbname.value == null){
      alert('<?php echo JText::_('JGA_ALERT_YOU_MUST_SELECT_THUMBNAIL_FILENAME',true); ?>');
      form.imgthumbname.style.backgroundColor = ffwrong;
      form.imgthumbname.focus();
    }
    else {
      submitform(pressbutton);
    }
  }
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
  <tr>
    <td width="20%" align="right">
      <?php echo JText::_('JGA_TITLE') .': *' ; ?>
    </td>
    <td width="80%">
      <input class="inputbox" type="text" name="imgtitle" size="50"
      maxlength="100" value="<?php echo htmlspecialchars($this->imgtitle, ENT_QUOTES, 'UTF-8'); ?>" />
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_CATEGORY') .': *'; ?>
    </td>
    <td>
      <?php echo $Lists['clist']; ?>
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_DESCRIPTION') .':'; ?>
    </td>
    <td>
      <?php
      $editor =& JFactory::getEditor();
      echo $editor->display('imgtext', str_replace('&','&amp;',$this->imgtext) ,
                            '620', '200', '70', '10', false) ;
      ?>
    </td>
  </tr>
    <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_OWNER') .':'   ; ?>
    </td>
    <td>
      <?php echo $Lists['users'];?>
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_AUTHOR') .':'; ?>
    </td>
    <td>
      <input class="inputbox" type="text" name="imgauthor"
       value="<?php echo $this->imgauthor; ?>" size="50" maxlength="100" />
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_PICTURE') . '-' . JText::_('JGA_CATEGORY') .': *'; ?>
    </td>
    <td>
      <?php echo $Lists['cplist']; ?>
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_PICTURE') .': *'; ?>
    </td>
    <td>
      <?php echo $Lists['imagelist']; ?>
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_DOES_ORIGINAL_EXIST'); ?>
    </td>
    <td>
<?php
    if ($original_message == 1) {
      $orig_msg =  '<div style="color:green;">[ '.JText::_('JGA_ORIGINAL_EXIST').' ]</div>';
    } else if ($original_message == 0) {
      $orig_msg = '<div style="color:red;">[ '.JText::_('JGA_ORIGINAL_NOT_EXIST').' ]</div>';
    }
?>
    <script language="javascript" type="text/javascript">
      if (document.forms[0].imgfilename.options.value=='') {
        document.write('<?php echo '<div>[ '.JText::_('JGA_NO_PICTURE_SELECTED',true).' ]</div>'; ?>');
      } else {
        document.write('<?php echo $orig_msg; ?>');
      }
    </script>
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_ASSIGN_ORIGINAL'); ?>&nbsp; &sup1;
    </td>
    <td>
      <?php echo $Lists['copy_original']; ?>
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_THUMBNAIL') . '-' . JText::_('JGA_CATEGORY') .': *'; ?>
    </td>
    <td>
      <?php echo $Lists['ctlist']; ?>
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_THUMBNAIL') .': *'; ?>
    </td>
    <td>
      <?php echo $Lists['thumblist']; ?>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
      <br />
      <div class="smallgrey">
        *&nbsp;<?php echo JText::_('JGA_COMPULSORYFIELDS'); ?><br />
        &sup1;&nbsp;<?php echo JText::_('JGA_ASSIGN_ORIGINAL_LONG'); ?>
      </div>
    </td>
  </tr>
</table>
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
<input type="hidden" name="task" value="newpic" />
<input type="hidden" name="imgdate" value="<?php echo mktime(); ?>" />
<input type="hidden" name="approved" value="<?php echo "1"; ?>" />
</form>
<p />
<?php
    $pcatpath = Joom_GetCatPath($this->pcatid);
    $tcatpath = Joom_GetCatPath($this->tcatid);
?>
<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
  <tr>
    <td valign="top" align="center">
      <b><?php echo JText::_('JGA_THUMBNAIL_PREVIEW') .':'; ?></b><br />
      <script language="javascript" type="text/javascript">
        if (document.forms[0].imgthumbname.options.value!='') {
          jsimg='<?php echo "../".$config->jg_paththumbs.$tcatpath; ?>' + getSelectedValue( 'adminForm', 'imgthumbname' );
        } else {
          jsimg='../images/M_images/blank.png';
        }
        document.write('<img src=' + jsimg + ' name="imagelib" border="2" alt="<?php echo JText::_('JGA_THUMBNAIL_PREVIEW'); ?>" />');
      </script>
    </td>
    <td valign="top" align="center">
      <b><?php echo JText::_('JGA_PICTURE_PREVIEW') .':'   ; ?></b><br />
      <script language="javascript" type="text/javascript">
        if (document.forms[0].imgfilename.options.value!='') {
          jsimg='<?php echo "../".$config->jg_pathimages.$pcatpath; ?>' + getSelectedValue( 'adminForm', 'imgfilename' );
        } else {
          jsimg='../images/M_images/blank.png';
        }
        document.write('<img src=' + jsimg + ' name="imagelib2" border="2" alt="<?php echo JText::_('JGA_PICTURE_PREVIEW'); ?>" />');
      </script>
    </td>
  </tr>
</table>
<?php
  }


  /**
   * Edit picture
   *
   * @param unknown_type $row
   * @param string $catname
   */
  function Joom_ShowEditPicture_HTML(&$row, $catname, &$Lists) {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
?>
<script language="javascript" type="text/javascript">
  function submitbutton(pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'canceleditpic') {
      submitform( pressbutton );
      return;
    }
    // do field validation
    form.imgtitle.style.backgroundColor = '';
    form.catid.style.backgroundColor = '';
    var ffwrong = '<?php echo $config->jg_wrongvaluecolor; ?>';
    if (form.imgtitle.value == '' || form.imgtitle.value == null){
      alert('<?php echo JText::_('JGA_ALERT_PICTURE_MUST_HAVE_TITLE',true); ?>');
      form.imgtitle.style.backgroundColor = ffwrong;
      form.imgtitle.focus();
    } else if (form.catid.value == "0"){
      alert('<?php echo JText::_('JGA_ALERT_YOU_MUST_SELECT_CATEGORY',true); ?>');
      form.catid.style.backgroundColor = ffwrong;
      form.catid.focus();
    } else {
      submitform( pressbutton );
    }
  }
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
  <tr>
    <td width="20%" align="right">
      <?php echo JText::_('JGA_TITLE') .':' ; ?>
    </td>
    <td width="80%">
      <input class="inputbox" type="text" name="imgtitle" size="50" maxlength="100" value="<?php echo htmlspecialchars( $row->imgtitle, ENT_QUOTES, 'UTF-8' ); ?>" />
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_CATEGORY') .':'   ; ?>
    </td>
    <td>
      <?php echo $catname; ?>&nbsp;(<?php echo $row->catid; ?>)
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_DESCRIPTION') .':'   ; ?>
    </td>
    <td>
      <?php
      $editor =& JFactory::getEditor();
      echo $editor->display('imgtext', str_replace('&','&amp;',$row->imgtext) ,
                            '620', '200', '70', '10', false);
      ?>
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_OWNER') .':'   ; ?>
    </td>
    <td>
      <?php echo $Lists['users'];?>
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_AUTHOR') .':'   ; ?>
    </td>
    <td>
      <input class="inputbox" type="text" name="imgauthor" value="<?php echo $row->imgauthor; ?>" size="50" maxlength="100" />
    </td>
  </tr>
  <tr>
    <td valign="top" align="right">
      <?php echo JText::_('JGA_PICTURE_RATING') .':'; ?> 
    </td>
    <td>
<?php 
      $voteavg = 0;
      if ($row->imgvotes > 0)
        $voteavg = number_format($row->imgvotesum / $row->imgvotes, 2, ',', '.');
?>
      <?php echo $voteavg .' (' .$row->imgvotes .' ' .  JText::_('JGA_PICTURE_VOTES') . ')'; ?>
      <br />
      <input type="checkbox" value="clearpicvotes" name="clearpicvotes"> 
        <?php echo JText::_('JGA_CLEAR_PICTURE_VOTES'); ?> 
    </td>
  </tr>
</table>
<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
<input type="hidden" name="catid" value="<?php echo $row->catid; ?>" />
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="approved" value="<?php if ($row->approved == '') {echo '1';} else {echo $row->approved;} ?>" />
<?php
  if ($row->id) {
?>
  <input type="hidden" name="returntask" value="edit" />
<?php
  } else {
?>
  <input type="hidden" name="returntask" value="new" />
<?php
  }
?>
</form>
<p />
<?php
    $catpath = Joom_GetCatPath($row->catid);
?>
<table align="center" cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
  <tr>
    <td>
      <b><?php echo JText::_('JGA_THUMBNAIL_PREVIEW') .':'; ?></b>
	  </td>
    <td>
      <b><?php echo JText::_('JGA_PICTURE_PREVIEW') .':'; ?></b><br />
    </td>
  </tr>
  <tr>
    <td valign="top" align="center">
      <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->imgthumbname; ?>" border="2" alt="<?php echo JText::_('JGA_THUMBNAIL_PREVIEW'); ?>" />
    </td>
    <td valign="top" >
      <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_pathimages.$catpath.$row->imgfilename; ?>" border="2" alt="<?php echo JText::_('JGA_PICTURE_PREVIEW'); ?>" />
    </td>
  </tr>
</table>
<?php
  }


  /**
   * Move pictures
   *
   * @param int $id
   * @param string $Lists
   * @param array of objects $items
   */
  function Joom_ShowMovePictures_HTML($id, &$Lists, &$items) {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();
    jimport('joomla.filesystem.file');
?>
<form action="index.php" method="post" name="adminForm" >
<table cellpadding="4" cellspacing="0" border="0" width="100%">
  <tr>
    <td align="center">
      <b><?php echo JText::_('JGA_MOVE_PICTURE_TO_CATEGORY'); ?>:</b> <?php echo $Lists['catgs'] ?>
    </td>
  </tr>
</table>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
  <tr>
    <td align="left" valign="top" width="20%">
      <strong>
        <?php echo JText::_('JGA_PICTURES_TO_MOVE'); ?>:
      </strong>
    </td>
  </tr>
</table>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
  <tr>
    <th width="5%"></th>
    <th class="title" width="40%">
      <?php echo JText::_('JGA_TITLE'); ?>
    </th>
    <th width="55%">
      <div align="left">
        <?php echo JText::_('JGA_PREVIOUS_CATEGORY'); ?>
      </div>
    </th>
  </tr>

<?php
    foreach ($items as $item) {
      $catpath = Joom_GetCatPath($item->catid);
?>
  <tr>
    <td>
      <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$item->imgfilename; ?>"
        border="0" width="24" height="24" alt="" />
    </td>
    <td align="left">
      <?php echo $item->imgtitle; ?>
    </td>
    <td>
      <div align="left">
        <?php echo Joom_ShowCategoryPath($item->catid); ?>
      </div>
    </td>
  </tr>
<?php
    }
?>
  <tr>
    <th align="center" colspan="3">
    </th>
  </tr>
</table>
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
<input type="hidden" name="task" value="savemovepic" />
<input type="hidden" name="boxchecked" value="1" />
<?php
    foreach ($id as $ids) {
      echo "\n <input type=\"hidden\" name=\"id[]\" value=\"$ids\" />";
    }
?>
</form>
<?php
  }
}
?>
