<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/html/admin.categories.html.php $
// $Id: admin.categories.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_AdminCategories {


  /**
   * Category manager
   *
   * @param array $rows database rows of categeories
   * @param string $search
   * @param string $slist
   * @param string $olist
   * @param object $pageNav
   */
  function Joom_ShowCategories_HTML(&$rows, $search,&$slist,&$olist, $pageNav) {
    $config = Joom_getConfig();
    $database = & JFactory::getDBO();
    jimport('joomla.filesystem.file');
?>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="Javascript" src="../includes/js/overlib_mini.js"></script>
<form action="index.php" method="post" name="adminForm">
<table cellpadding="4" cellspacing="0" border="0" width="100%">
  <tr>
    <td width="100%"></td>
    <td>
      <?php echo JText::_('JGA_SEARCH'). ':'; ?><br />
      <input type="text" name="search" value="<?php echo $search;?>"
       class="inputbox" onChange="document.adminForm.submit();" />
    </td>
    <td nowrap>
      <?php echo JText::_('JGA_SORT_BY_ORDER'); ?><br />
      <?php echo $olist;?>
    </td>
    <td>
      <?php echo JText::_('JGA_SORT_BY_TYPE'); ?><br />
      <?php echo $slist;?>
    </td>
  </tr>
  <tr>
    <td width="100%"></td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="4" cellspacing="0" class="adminlist">
  <tr>
    <th width="20">
      <input type="checkbox" name="toggle" value=""
       onclick="checkAll(<?php echo count($rows); ?>);" />
    </th>
    <th width="10%"></th>
    <th width="5%" align="left">ID</th>
    <th width="85%" class="title">
      <?php echo JText::_('JGA_CATEGORY'); ?>
    </th>
    <th nowrap align="left">
      <?php echo JText::_('JGA_PARENT_CATEGORY'); ?>
    </th>
    <th nowrap>
      <?php echo JText::_('JGA_PUBLISHED'); ?>
    </th>
    <th width="5%">
      <?php echo JText::_('JGA_OWNER'); ?>
    </th>
    <th width="5%">
      <?php echo JText::_('JGA_TYPE'); ?>
    </th>
    <th nowrap>
      <?php echo JText::_('JGA_HIT'); ?>
    </th>
    <th colspan="2" nowrap>
      <div align="center">
        <?php echo JText::_('JGA_REORDER'); ?>
      </div>
    </th>
    <th align="center">
      <a href="javascript: saveorder(<?php echo count($rows)-1; ?>)">
        <img src="images/filesave.png" border="0" width="16" height="16"
         alt="<?php echo JText::_('JGA_SAVE_ORDER'); ?>" />
      </a>
    </th>
  </tr>
<?php
    $k = 0;
    $i = 0;
    for ($i=0, $n=count($rows); $i < $n; $i++) {
      $row     = &$rows[$i];
      $catpath = Joom_GetCatPath($row->cid);
?>
  <tr class="row<?php echo $k; ?>">
    <td width="20">
      <input type="checkbox" id="cb<?php echo $i;?>" name="cid[]"
       value="<?php echo $row->cid; ?>" onClick="isChecked(this.checked);" />
    </td>
    <td width="10%">
<?php
      if ($row->catimage != '') {
        if (JFile::exists(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->catimage)) {
          $imginfo = getimagesize(JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->catimage));
          $imgsource = _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->catimage;
          $srcWidth = $imginfo[0];
          $srcHeight = $imginfo[1];
          $thumbexists = 1;
        } else {
          $thumbexists = 0;
        }

        if ($thumbexists) {
?>
      <a href="#" onmouseover="return overlib('<img src=\'<?php echo $imgsource; ?>\' />',WIDTH,<?php echo $srcWidth; ?>, HEIGHT,<?php echo $srcHeight; ?>)"  onmouseout="return nd()"; alt=""/>
        <img src="<?php echo $imgsource; ?>" border="0" width="24" height="24" />
      </a>
<?php
        } else {
?>
      &nbsp;
<?php
        }
      }
?>
    </td>
    <td width="5%" ><?php echo $row->cid ; ?></td>
    <td width="85%" >
      <div align="left">
        <a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>',
                                                     'editcatg')">
<?php
      if ( $row->parent > 0 ) {
?>
        &nbsp; &raquo;
<?php
      }
?>
        <?php echo $row->name; ?>
        </a>
      </div>
    </td>
    <td  align="center" nowrap>
      <?php echo Joom_ShowCategoryPath($row->parent); ?>
    </td>
<?php
        $task = $row->published ? 'unpublishcatg' : 'publishcatg';
        $img  = $row->published ? 'tick.png' : 'publish_x.png';
?>
    <td width="10%" align="center" nowrap>
      <a href="javascript: void(0);"
       onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
        <img src="images/<?php echo $img;?>" border="0" alt="" />
      </a>
    </td>
    <td width="5%" align="center">
<?php
      if ($row->owner != null) {
        $owner=JFactory::getUser($row->owner);
?>
    <?php echo Joom_GetDisplayName($row->owner, true); ?>
    [<?php echo $config->jg_realname ? $owner->get('username'):$owner->get('name');?>&nbsp;(<?php echo $row->owner; ?>)]
<?php
      } else {
?>
      Administrator[def]
<?php
      }
?>
    </td>
    <td width="5%" align="center">
<?php
      if ($row->owner != null) {
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
    
    <td width="10%" align="center" nowrap>
      <?php echo $row->groupname;?>
    </td>
    <td>
<?php
      if ($i > 0 || ($i+$pageNav->limitstart > 0)) {
?>
      <div align="center">
        <a href="#reorder" onclick="return listItemTask('cb<?php echo $i;?>',
                                                        'orderupcatg')">
          <img src="images/uparrow.png" border="0"<?php /* portierung: width und height entfernt */ ?>
           alt="<?php echo JText::_('JGA_UP'); ?>" />
        </a>
      </div>
<?php
      } else {
?>
          &nbsp;
<?php
      }
?>
    </td>
    <td>
<?php
      if ($i < $n-1 || $i+$pageNav->limitstart < $pageNav->total-1) {
?>
      <div align="center">
        <a href="#reorder" onclick="return listItemTask('cb<?php echo $i;?>',
                                                        'orderdowncatg')">
          <img src="images/downarrow.png" border="0"<?php /* portierung: width und height entfernt */ ?>
           alt="<?php echo JText::_('JGA_DOWN'); ?>" />
        </a>
      </div>
<?php
      }
?>
    </td>
    <td align="center">
      <input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>"
       class="text_area" style="text-align: center" />
    </td>
<?php
      $k = 1 - $k;
?>
  </tr>
<?php
    }
?>
  <tr>
    <td colspan="12">
      <?php echo $pageNav->getListFooter(); ?>
    </td>
  </tr>
</table>
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
<input type="hidden" name="task" value="categories" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="returntask" value="catg" />
</form>
<?php
  }


  /**
   * New Category
   *
   * @param string $publist
   * @param string $glist
   * @param string $Lists
   * @param string $orderlist
   * @param string $jg_wrongvaluecolor
   */
  function Joom_ShowNewCategory_HTML(&$publist, $glist , $Lists,
                                     $orderlist, $jg_wrongvaluecolor) {

    //TODO need of this values?
    $name = Joom_FixUserEntrie(Joom_mosGetParam('name', ''));
    $description = Joom_mosGetParam('description', '');
?>
<script language="javascript" type="text/javascript">
  function submitbutton(pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'cancelcatg') {
      submitform(pressbutton);
      return;
    }
    // do field validation
    form.name.style.backgroundColor = '';
    var ffwrong = '<?php echo $jg_wrongvaluecolor; ?>';
    try {
    document.adminForm.onsubmit();
    }
    catch(e){}
    if (form.name.value == '' || form.name.value == null) {
      alert('<?php echo JText::_('JGA_ALERT_CATEGORY_MUST_HAVE_TITLE',true); ?>');
      form.name.style.backgroundColor = ffwrong;
      form.name.focus();
    }
    else {
      <?php
      $editor =& JFactory::getEditor();
      echo $editor->getContent('description');
      ?>
      submitform(pressbutton);
    }
  }
</script>
<form action="index.php" method="post" name="adminForm">
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
  <tr class="row0">
    <td width="200">
    <?php echo JText::_('JGA_TITLE') . ':'; ?>
    </td>
    <td>
      <input class="inputbox" type="text" name="name" size="25"
       value="<?php echo $name; ?>" />
    </td>
  </tr>
  <tr class="row1">
    <td valign="top" >
      <?php echo JText::_('JGA_PARENT_CATEGORY') . ':'; ?>
    </td>
    <td nowrap >
      <?php echo $Lists["catgs"]; ?>
    </td>
  </tr>
  <tr class="row0">
    <td valign="top">
      <?php echo JText::_('JGA_DESCRIPTION') . ':'; ?>
    </td>
    <td>
<?php
      // parameters : areaname, content, hidden field, width, height, rows, cols
      echo $editor->display('description', str_replace('&','&amp;',$description) ,
                            '620', '200', '70', '10', false) ;
?>
    </td>
  </tr>
  <tr class="row1">
    <td valign="top" >
      <?php echo JText::_('JGA_HIT') . ':'; ?>
    </td>
    <td nowrap>
      <?php echo $glist;?>
    </td>
  </tr>
  <tr class="row0">
    <td valign="top" >
      <?php echo JText::_('JGA_PUBLISHED') . ':'; ?>
    </td>
    <td nowrap>
      <?php echo $publist; ?>
    </td>
  </tr>
  <tr class="row1">
    <td valign="top" >
      <?php echo JText::_('JGA_ORDERING') . ':'; ?>
    </td>
    <td nowrap >
      <?php echo $orderlist;?>
    </td>
  </tr>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
<input type="hidden" name="catimage" value="" />
</form>
<?php
  }


  /**
   * Edit Category
   *
   * @param string $row
   * @param string $publist
   * @param string $glist
   * @param string $Lists
   * @param string $orderlist
   * @param string $thumblist2
   * @param string $align_list
   * @param string $jg_paththumbs
   * @param string $catpath
   * @param string $jg_wrongvaluecolor
   */
  function Joom_ShowEditCategory_HTML(&$row, &$publist, $glist ,
                                      $Lists, $orderlist, &$thumblist2,
                                      &$align_list, $jg_paththumbs, $catpath,
                                      $jg_wrongvaluecolor,$ownerlist) {

  JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'description' );
?>
<script language="javascript" type="text/javascript">
  function submitbutton(pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'cancelcatg') {
      submitform( pressbutton );
      return;
    }
    // do field validation
    form.name.style.backgroundColor = '';
    var ffwrong = '<?php echo $jg_wrongvaluecolor; ?>';
    try {
    document.adminForm.onsubmit();
    }
    catch(e){}
    if (form.name.value == '' || form.name.value == null) {
      alert('<?php echo JText::_('JGA_ALERT_CATEGORY_MUST_HAVE_TITLE',true); ?>');
      form.name.style.backgroundColor = ffwrong;
      form.name.focus();
    }
    else {
      <?php
      $editor =& JFactory::getEditor();
      echo $editor->getContent('description');
      ?>
      submitform(pressbutton);
    }
  }
</script>
<form action="index.php" method="post" name="adminForm">
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
  <tr class="row0">
    <td width="200">
    <?php echo JText::_('JGA_TITLE') . ':'; ?>
    </td>
    <td>
      <input class="inputbox" type="text" name="name" size="25" value="<?php echo $row->name; ?>" />
    </td>
  </tr>
  <tr class="row1">
    <td valign="top" >
      <?php echo JText::_('JGA_PARENT_CATEGORY') . ':'; ?>
    </td>
    <td nowrap >
      <?php echo $Lists["catgs"]; ?>
    </td>
  </tr>
  <tr class="row0">
    <td valign="top">
      <?php echo JText::_('JGA_DESCRIPTION') . ':'; ?>
    </td>
    <td>
<?php
      // parameters : areaname, content, hidden field, width, height, rows, cols
      echo $editor->display('description', str_replace('&','&amp;',$row->description) ,
                 '620', '200', '70', '10', false);
?>
    </td>
  </tr>
  <tr class="row1">
    <td valign="top" >
      <?php echo JText::_('JGA_HIT') . ':'; ?>
    </td>
    <td nowrap>
      <?php echo $glist; ?>
    </td>
  </tr>
  <tr class="row0">
    <td valign="top" >
      <?php echo JText::_('JGA_ORDERING') . ':'; ?>
    </td>
    <td nowrap >
      <?php echo $orderlist; ?>
    </td>
  </tr>
  <tr class="row1">
    <td valign="top" align="right">
      <?php echo JText::_('JGA_THUMBNAIL_ALIGN') . ':' ; ?>
    </td>
    <td>
      <?php echo $align_list; ?>
    </td>
  </tr>
  <tr class="row0">
    <td valign="top" align="right">
      <?php echo JText::_('JGA_THUMBNAIL') .':'   ; ?>
    </td>
    <td>
      <?php echo $thumblist2; ?>
    </td>
  </tr>
  <tr>
    <td valign="top" align="center">
      <b><?php echo JText::_('JGA_THUMBNAIL_PREVIEW') .':'   ; ?></b><br />
      <script language="javascript" type="text/javascript">
        if (document.forms[0].catimage.options.value!='') {
          jsimg='<?php echo "../".$jg_paththumbs.$catpath; ?>' + getSelectedValue( 'adminForm', 'catimage' );
        } else {
          jsimg='../images/M_images/blank.png';
        }
        document.write('<img src=' + jsimg + ' name="imagelib" border="2" alt="<?php echo JText::_('JGA_THUMBNAIL_PREVIEW'); ?>" />');
      </script>
    </td>
  </tr>
  <tr class="row1">
    <td valign="top" align="right">
      <?php echo JText::_('JGA_OWNER') . ':' ; ?>
    </td>
    <td>
      <?php echo $ownerlist; ?>
    </td>
  </tr>
</table>

<input type="hidden" name="cid" value="<?php echo $row->cid; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
</form>
<?php
  }
}
?>
