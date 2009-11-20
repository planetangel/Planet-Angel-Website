<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/html/joom.userpanel.html.php $
// $Id: joom.userpanel.html.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined ('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

class HTML_Joom_UserPanel
{

  function Joom_User_PanelShow_HTML(&$showcats, &$showpicupload, &$olist, &$slist, &$rows, &$pageNav)
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    if($config->jg_showminithumbs)
    {
?>
  <script  type="text/javascript" src="<?php echo _JOOM_LIVE_SITE; ?>includes/js/overlib_mini.js"></script>
<?php
    }
?>
  <div class="jg_userpanelview">
    <div class="jg_up_head">
<?php
    if($showpicupload)
    {
?>
      <input type="button" name="Button" value="<?php echo JText::_('JGS_NEW_PICTURE'); ?>"
      onclick = "javascript:location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=showupload'._JOOM_ITEMID,false); ?>';"
      class="button" />

<?php
  }
  if($showcats)
  {
?>
      <input type="button" name="Button" value="<?php echo JText::_('JGS_CATEGORIES'); ?>"
      onclick = "javascript:location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=showusercats&uid='.$user->get('id')._JOOM_ITEMID,false); ?>';"
      class="button" />
<?php
  }
?>
      <form action="<?php echo JRoute::_('index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID); ?>" method="post" name="form">
        <p>
<?php
      //navigation, only if pictures exist
    if(!is_null($pageNav))
    {
?>
          <?php echo $pageNav->getListFooter(); ?> 
<?php
    }
?>
          <?php echo $slist; ?> 
          <?php echo $olist; ?> 
        </p>
      </form>
    </div>
    <div class="sectiontableheader">
      <div class="jg_up_entry">
        <div class="jg_up_ename">
          <?php echo JText::_('JGS_PICTURE_NAME'); ?> 
        </div>
        <div class="jg_up_ehits">
          <?php echo JText::_('JGS_HITS'); ?> 
        </div>
        <div class="jg_up_ecat">
          <?php echo JText::_('JGS_CATEGORY'); ?> 
        </div>
        <div class="jg_up_eact">
          <?php echo JText::_('JGS_ACTION'); ?> 
        </div>
<?php
    if($config->jg_approve)
    {
?>
        <div class="jg_up_eappr">
          <?php echo JText::_('JGS_APPROVED'); ?> 
        </div>
<?php
    }
?>
      </div>
    </div>
<?php
    $k = 0;
    if(count($rows))
    {
      foreach($rows as $row)
      {
        $k = 1 - $k;
        $p = $k+1;
        $catpath = Joom_GetCatPath($row->catid);
?>
    <div class="<?php echo "sectiontableentry".$p; ?>">
      <div class="jg_up_entry">
<?php
        if($row->approved)
        {
          $link = Joom_OpenImage($config->jg_detailpic_open, $row->id, $catpath, $row->catid, $row->imgfilename, $row->imgtitle, $row->imgtext);
        }
?>
        <div class="jg_up_ename">
<?php
        if($config->jg_showminithumbs)
        {
          if($row->imgthumbname != '' 
             && (is_file(JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->imgthumbname))))
          {
            $tnfile    = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->imgthumbname);
            $imginfo   = getimagesize($tnfile);
            $srcWidth  = $imginfo[0];
            $srcHeight = $imginfo[1];
            if($row->approved)
            {
?>
            <a href="<?php echo $link; ?>" onmouseover="return overlib('<img src=\'<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->imgthumbname; ?>\' />',WIDTH,<?php echo $srcWidth; ?>, HEIGHT,<?php echo $srcHeight; ?>)" onmouseout="return nd()" >
<?php
            }
            else
            {
?>
            <a href="#" onmouseover="return overlib('<img src=\'<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->imgthumbname; ?>\' />',WIDTH,<?php echo $srcWidth; ?>, HEIGHT,<?php echo $srcHeight; ?>)" onmouseout="return nd()" >
<?php
            }
?>
              <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->imgthumbname; ?>" border="0" height="30" alt="" />
            </a>
<?php
          }
          else
          {
?>
            &nbsp;
<?php
          }
        }
        else
        {
?>
          <div class="jg_floatleft">
            <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
          </div>
<?php
        }
        if($row->approved)
        {
?>
          <a href="<?php echo $link; ?>"> 
<?php
        }
?>
          <?php echo $row->imgtitle; ?> 
<?php        
          if($row->approved)
          {
?>
          </a>
<?php
          }
?>
        </div>
        <div class="jg_up_ehits">
        <?php echo $row->imgcounter; ?> 
        </div>
        <div class="jg_up_ecat">
        <?php echo Joom_ShowCategoryPath( $row->catid ); ?> 
        </div>
        <div class="jg_up_esub1">
          <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=editpic&uid='.$user->get('id').'&id='.$row->id._JOOM_ITEMID); ?>" title="<?php echo JText::_('JGS_EDIT'); ?>">
            <img src= "<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/edit.png" class="pngfile jg_icon" border="0" width="16" height="16" alt="<?php echo JText::_('JGS_EDIT'); ?>" class="pngfile" />
          </a>
        </div>
        <div class="jg_up_esub2">
          <a href="javascript:if (confirm('<?php echo JText::_('JGS_ALERT_SURE_DELETE_SELECTED_ITEM',true); ?>')){ location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=deletepic&uid='.$user->get('id').'&id='.$row->id._JOOM_ITEMID,false);?>';}" title="<?php echo JText::_('JGS_DELETE'); ?>">
            <img src="<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/edit_trash.png" class="pngfile jg_icon" border="0" width="16" height="16" alt="<?php echo JText::_('JGS_DELETE'); ?>" class="pngfile" />
          </a>
        </div>
<?php
        if( $config->jg_approve )
        {
          if($row->approved)
          {
            $a_pic = 'tick.png';
          }
          else
          {
            $a_pic = 'cross.png';
          }
?>
        <div class="jg_up_eappr">
          <img src="<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/<?php echo $a_pic; ?>" height="16" width="16" border="0" alt="pngfile" class="pngfile jg_icon" />
        </div>
<?php
        }
?>
      </div>
    </div>
<?php
      }
    }
    else
    {
      $p = $k+1;
?>
    <div class="jg_txtrow">
      <div class="<?php echo "sectiontableentry".$p; ?>">
        <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
      <?php echo JText::_('JGS_YOU_DO_NOT_HAVE_PICTURE'); ?>
      </div>
    </div>
<?php
    }
?>
  </div>
<?php
  }//End function Joom_User_PanelShow_HTML


  function Joom_User_CatsShow_HTML(&$rows)
  {
    $config = Joom_getConfig();
    $user   = & JFactory::getUser();

    $count_cat = count($rows);
?>
  <div class="jg_userpanelview">
<?php
    if($config->jg_newpicnote && !$this->adminlogged)
    {
?>
    <div class="jg_uploadquotas">
      <span class="jg_quotatitle">
        <?php echo JText::_('JGS_NEW_CATEGORY_NOTE'); ?> 
      </span><br />
      <?php echo JText::_('JGS_NEW_CATEGORY_MAXCOUNT') . $config->jg_maxusercat;?><br />
      <?php echo JText::_('JGS_NEW_CATEGORY_YOURCOUNT') . $count_cat;?><br />
      <?php echo JText::_('JGS_NEW_CATEGORY_REMAINDER') . ($config->jg_maxusercat- $count_cat);?><br />
    </div>
<?php
    }
    //wenn max. Anzahl Kategorien erreicht, Button nicht anzeigen
    //nicht fuer Admins
    if($this->adminlogged || ($config->jg_maxusercat-$count_cat) > 0)
    {
?> 
    <div class="jg_up_head">
      <input type="button" name="Button" value="<?php echo JText::_('JGS_NEW_CATEGORY');?>"
      onclick = "javascript:location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=newusercat'._JOOM_ITEMID,false); ?>';"
      class="button" />
    </div>
<?php
    }
?>
    <div class="sectiontableheader">
      <div class="jg_up_entry">
        <div class="jg_up_ename">
        <?php echo JText::_('JGS_CATEGORY'); ?> 
        </div>
        <div class="jg_up_ehits">
        <?php echo JText::_('JGS_PICTURES'); ?> 
        </div>
        <div class="jg_up_ecat">
        <?php echo JText::_('JGS_PARENT_CATEGORY');?> 
        </div>
        <div class="jg_up_eact">
        <?php echo JText::_('JGS_ACTION');?> 
        </div>
        <div class="jg_up_eappr">
        <?php echo JText::_('JGS_PUBLISHED');?> 
        </div>
      </div>
    </div>
<?php
      $k = 0;
    if(count($rows))
    {
      foreach($rows as $row)
      {
        $k = 1 - $k;
        $p = $k+1;
        $catpath = Joom_GetCatPath($row->cid);
        //Link auf Kategorieansicht in Usergalerie, nur wenn published
        if($row->published)
        {
          $catlink = JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$row->cid._JOOM_ITEMID);
        }
?>
    <div class="<?php echo "sectiontableentry".$p; ?>">
      <div class="jg_up_entry">
        <div class="jg_up_ename">
<?php
          if($config->jg_showminithumbs)
          {
            if($row->catimage != '')
            {
?>
            <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->catimage; ?>" border="0" height="30" alt="" />
<?php
            }
            else
            {
?>
          <div class="jg_floatleft">
            <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
          </div>
<?php
            }
          }
          if($row->published)
          {
?>
          <a href="<?php echo $catlink; ?>"> 
<?php
          }
?>
          <?php echo $row->name; ?> 
<?php
          if($row->published)
          {
?>
          </a>
<?php
          }
?>
        </div>
        <div class="jg_up_ehits">
          <?php echo $row->piccount; ?> 
        </div>
        <div class="jg_up_ecat">
<?php
          if($row->parent==0)
          {
?>
            <?php echo '-'; ?> 
<?php
          }
          else
          {
?>
            <?php echo Joom_ShowCategoryPath( $row->parent ); ?> 
<?php
          }
?>
        </div>
        <div class="jg_up_esub1"> 
          <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=editusercat&catid='.$row->cid._JOOM_ITEMID); ?>" title="<?php echo JText::_('JGS_EDIT'); ?>">
            <img src="<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/edit.png" border="0" width="16" height="16" alt="<?php echo JText::_('JGS_EDIT'); ?>" class="pngfile jg_icon" />
          </a>
        </div>
        <div class="jg_up_esub2">
<?php
        if($row->allowdel == true)
        {
?>
          <a href="javascript:if (confirm('<?php echo JText::_('JGS_ALERT_SURE_DELETE_SELECTED_ITEM',true); ?>')){ location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=deleteusercat&catid='.$row->cid._JOOM_ITEMID,false); ?>';}" title="<?php echo JText::_('JGS_DELETE'); ?>">
            <img src="<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/edit_trash.png" border="0" width="16" height="16" alt="<?php echo JText::_('JGS_DELETE'); ?>" class="pngfile jg_icon" />
          </a>
<?php
        }
        else
        {
?>
         <?php echo '&nbsp;'; ?> 
<?php
        }
?>
        </div>
<?php
        if($row->published)
        {
          $a_pic = 'tick.png';
        }
        else
        {
          $a_pic = 'cross.png';
        }
?>
        <div class="jg_up_eappr">
          <img src="<?php echo _JOOM_LIVE_SITE; ?>components/com_joomgallery/assets/images/<?php echo $a_pic; ?>" width="16" height="16" border="0" alt="pngfile" class="pngfile jg_icon" />
        </div> 
      </div>
    </div>
<?php
      }
    }
    else
    {
      $p = $k+1;
?>
    <div class="jg_txtrow">
      <div class="<?php echo "sectiontableentry".$p; ?>">
        <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
      <?php echo JText::_('JGS_YOU_NOT_HAVE_CATEGORY'); ?>
      </div>
    </div>
<?php
    }
?>
  </div>
  <div class="jg_txtrow">
    <input type="button" name="Button" value="<?php echo JText::_('JGS_BACK_TO_USER_PANEL');?>"
    onclick = "javascript:location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID,false); ?>';"
    class="button" />
  </div>
<?php
  }//End function Joom_User_CatsShow_HTML


  /**
   * new category or modify existing category
   *
   * @param unknown_type $publist
   * @param unknown_type $Lists
   * @param unknown_type $orderlist
   */
  function Joom_User_EditUserCat_HTML($cid, &$publist, &$glist, $Lists, $orderlist,
                                      &$thumblist, &$description, &$name)
  {
    $config = Joom_getConfig();
    $catpath = Joom_GetCatPath($cid);
?>
<script language="javascript" type="text/javascript">
  function submit_button() {
    var form = document.usercatForm;
    // do field validation
    form.name.style.backgroundColor = '';
    var ffwrong = '<?php echo $config->jg_wrongvaluecolor; ?>';
    try {
      document.usercatForm.onsubmit();
    }
    catch(e){}
    if (form.name.value == '' || form.name.value == null) {
      alert('<?php echo JText::_('JGS_ALERT_CATEGORY_MUST_HAVE_TITLE',true); ?>');
      form.name.style.backgroundColor = ffwrong;
      form.name.focus();
    } else {
      form.Button[0].disabled=true;//save
      form.Button[1].disabled=true;//cancel
      form.submit();
    } 
  }
</script>
  <div class="sectiontableheader">
    <?php if ($cid == 0) echo JText::_('JGS_NEW_CATEGORY'); else echo JText::_('JGS_MODIFY_CATEGORY'); ?>
  </div>
  <form action="<?php echo JRoute::_('index.php?option=com_joomgallery&func=saveusercat&catid='.$cid._JOOM_ITEMID); ?>" method="post" name="usercatForm">
  <div class="jg_editpicture">
    <div class="jg_uprow">
      <div class="jg_uptext">
      <?php echo JText::_('JGS_TITLE') . ':'; ?> 
      </div>
      <input class="inputbox" type="text" name="name" size="25" value="<?php echo $name; ?>" />
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
      <?php echo JText::_('JGS_PARENT_CATEGORY') . ':'; ?> 
      </div>
    <?php echo $Lists["catgs"]; ?> 
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
      <?php echo JText::_('JGS_DESCRIPTION') . ':'; ?> 
      </div>
      <textarea name="description" rows="5" cols="40" class="inputbox"><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>
<?php
    if($glist != null)
    {
?>
    <div class="jg_uprow">
      <div class="jg_uptext">
      <?php echo JText::_('JGS_ACCESS') . ':'; ?> 
      </div>
      <?php echo $glist;?> 
    </div>
<?php
    }
?>
    <div class="jg_uprow">
      <div class="jg_uptext">
      <?php echo JText::_('JGS_PUBLISHED') . ':'; ?> 
      </div>
    <?php echo $publist; ?> 
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <?php echo JText::_('JGS_ORDERING') . ':'; ?> 
      </div>
      <?php echo $orderlist;?> 
    </div>
<?php
      if($cid != 0)
      {
?>
    <div class="jg_uprow">
      <div class="jg_uptext">
    <?php echo JText::_('JGS_THUMBNAIL') .':'; ?> 
      </div>
    <?php echo $thumblist; ?>
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">      
      <?php echo JText::_('JGS_THUMBNAIL_PREVIEW') .':'   ; ?> 
      </div>
    <script language="javascript" type="text/javascript">
      if (document.usercatForm.catimage.options.value!='') {
        jsimg='<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath; ?>' + getSelectedValue( 'usercatForm', 'catimage' );
      } else {
        jsimg='<?php echo _JOOM_LIVE_SITE."images/M_images/blank.png"?>';
      }
      document.write('<img src=' + jsimg + ' name="imagelib" height="80" border="1" alt="<?php echo JText::_('JGS_THUMBNAIL_PREVIEW'); ?>" />');
    </script>
    </div>
<?php
      }
?>
    <div class="jg_txtrow">
      <input type="button" name="Button" value="<?php echo JText::_('JGS_SAVE'); ?>"
      onclick = "javascript:submit_button();" class="button" />
      <input type="button" name="Button" value="<?php echo JText::_('JGS_CANCEL'); ?>"
      onclick = "javascript:location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=showusercats'._JOOM_ITEMID,false); ?>';"
      class="button" />
    </div>
  </div>
</form>
<?php
  }//End function Joom_User_EditUserCat_HTML


  function Joom_User_ShowUpload_HTML(&$clist)
  {
    global $row;
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();

    //fuer Admin/SuperAdmin gelten keine Beschraenkungen
    if(!$this->adminlogged)
    {
      $database->setQuery(" SELECT 
                              COUNT(id)
                            FROM 
                              #__joomgallery
                            WHERE 
                              owner = ".$user->get('id'));
      $count_pic      = $database->loadResult();
      $inputcounter   = $config->jg_maxuserimage - $count_pic;
      $remainder      = $inputcounter;
      $inputcounter   = ($inputcounter<$config->jg_maxuploadfields) ? $inputcounter : $config->jg_maxuploadfields;
      $maxfilesizekb = number_format($config->jg_maxfilesize / 1024, 2, ',', '.');
      if($count_pic >= $config->jg_maxuserimage)
      {
        $mosmsg = JText::_('JGS_ALERT_MAY_ADD_MAX_OF_PARTONE') . " " . $config->jg_maxuserimage . " " . JText::_('JGS_ALERT_MAY_ADD_MAX_OF_PARTTWO');
        $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID,false), $mosmsg);
      }
    }
    else
    {
      $inputcounter = $config->jg_maxuploadfields;
    }
?>
  <script language="javascript" type="text/javascript">
     var jg_inputcounter = <?php echo $inputcounter; ?>;
  </script>
  <div class="sectiontableheader">
    <?php echo JText::_('JGS_NEW_PICTURE'); ?> 
  </div>
  <div class="jg_uploadform">
    <form action="<?php echo JRoute::_('index.php?option=com_joomgallery&func=uploadhandler'._JOOM_ITEMID); ?>" method="post" name="adminForm" enctype="multipart/form-data" onsubmit="return joom_checkme()" >
<?php
    if($config->jg_newpiccopyright && !$this->adminlogged)
    {
?>
    <div class="jg_uploadcopyright sectiontableentry2">
    <?php echo JText::_('JGS_NEW_PICTURE_COPYRIGHT'); ?>
    </div>
<?php
    }
    if($config->jg_newpicnote && !$this->adminlogged)
    {
?>
    <div class="jg_uploadquotas">
      <span class="jg_quotatitle"><?php echo JText::_('JGS_NEW_PICTURE_NOTE'); ?></span><br />
      <?php echo JText::_('JGS_NEW_PICTURE_MAXSIZE') . $config->jg_maxfilesize . JText::_('JGS_BYTES') . ' (' . $maxfilesizekb . ' KB)'; ?><br />
      <?php echo JText::_('JGS_NEW_PICTURE_MAXCOUNT') . $config->jg_maxuserimage; ?><br />
      <?php echo JText::_('JGS_NEW_PICTURE_YOURCOUNT') . $count_pic; ?><br />
      <?php echo JText::_('JGS_NEW_PICTURE_REMAINDER') . $remainder; ?><br />
    </div>
<?php
    }
?>
    <div class="jg_uprow">
      <div class="jg_uptext">
      <?php echo JText::_('JGS_TITLE'); ?>:
      </div>
      <input class="inputbox" type="text" name="imgtitle" size="42" maxlength="100" value="<?php if(isset($_POST['$row->imgtitle'])) echo htmlspecialchars( $row->imgtitle, ENT_QUOTES, 'UTF-8'); ?>" />
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <?php echo JText::_('JGS_CATEGORY'); ?>:
      </div>
      <?php echo $clist; ?> 
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <?php echo JText::_('JGS_DESCRIPTION'); ?>:
      </div>
      <textarea class="inputbox" cols="40" rows="5" name="imgtext"><?php if(isset($_POST['$row->imgtext'])) echo htmlspecialchars( $row->imgtext, ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <?php echo JText::_('JGS_AUTHOR_OWNER'); ?>:
      </div>
      <b><?php echo $user->get('username'); ?></b>
    </div>
<?php
    for($i=0; $i < $inputcounter; $i++)
    {
?>
    <div class="jg_uprow">
      <div class="jg_uptext">
      <?php echo JText::_('JGS_PICTURE_PATH'); ?>:
      </div>
    <?php echo "<input type ='file' name = 'arrscreenshot[$i]' class='inputbox'/>"; ?>
    </div>
<?php
    }
    if($config->jg_delete_original_user != 2)
    {
      $sup2 = "&sup1;";
    }
    else
    {
      $sup2 = "&sup2;";
    }
    if($config->jg_delete_original_user == 2)
    {
?>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <input type="checkbox" name="original_delete" value="1" />
      </div>
    <?php echo JText::_('JGS_DELETE_ORIGINAL_AFTER_UPLOAD'); ?>&nbsp;&sup1;
    </div>
<?php
    }
    if($config->jg_special_gif_upload == 1)
    {
?>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <input type="checkbox" name="create_special_gif" value="1" />
      </div>
    <?php echo JText::_('JGS_CREATE_SPECIAL_GIF'); ?>&nbsp;<?php echo $sup2; ?>
    </div>
<?php
    }
?>
    <div class="jg_txtrow">
      <input type="submit" value="<?php echo JText::_('JGS_UPLOAD'); ?>" class="button" />
      <input type="button" name="Button" value="<?php echo JText::_('JGS_CANCEL'); ?>"
        onclick = "javascript:location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID,false); ?>';"
        class="button" />
    </div>
<?php
    if($config->jg_delete_original_user == 2)
    {
?>
    <div class="jg_uploadnotice small sectiontableentry2">
      &sup1;&nbsp;<?php echo JText::_('JGS_DELETE_ORIGINAL_AFTER_UPLOAD_ASTERISK'); ?>
    </div>
<?php
    }
    if($config->jg_special_gif_upload == 1)
    {
?>
    <div class="jg_uploadnotice small sectiontableentry2">
    <?php echo $sup2; ?>&nbsp;<?php echo JText::_('JGS_CREATE_SPECIAL_GIF_ASTERISK'); ?>
    </div>
<?php
    }
?>
    <input type="hidden" name="id" value="<?php if(isset($_POST['$row->id'])) echo $row->id;?>" />
<?php /*
    TODO: BUGTEST
    <input type="hidden" name="bugtest" value="<?php echo $bugtest; ?>" />*/ ?>
    </form>
  </div>
<?php
  }//End function Joom_User_ShowUpload_HTML


  /**
   * Aenderung der Eintraege zu dem Bild
   *
   * @param unknown_type $row
   */
  function Joom_User_EditPic_HTML(&$row, &$clist)
  {
    $config  = Joom_getConfig();
    $user    = & JFactory::getUser();
    $catpath = Joom_GetCatPath($row->catid);
?>
  <div class="sectiontableheader">
    <?php echo JText::_('JGS_EDIT_PICTURE'); ?> 
  </div>
  <div class="jg_editpicture">
    <form action = "<?php echo JRoute::_('index.php?option=com_joomgallery&func=savepic'._JOOM_ITEMID); ?>" method="post" name="adminForm"
    enctype="multipart/form-data" onsubmit="return joom_checkme2();" >
    <div class="jg_uprow">
      <div class="jg_uptext">
        <?php echo JText::_('JGS_TITLE') ; ?>:
      </div>
      <input class="inputbox" type="text" name="imgtitle" size="42" maxlength="100"
      value = "<?php echo htmlspecialchars($row->imgtitle, ENT_QUOTES, 'UTF-8'); ?>" />
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <?php echo JText::_('JGS_CATEGORY'); ?>:
      </div>
      <?php echo $clist; ?>
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <?php echo JText::_('JGS_DESCRIPTION'); ?>:
      </div>
      <textarea class="inputbox" cols="40" rows="5" name="imgtext"><?php echo htmlspecialchars($row->imgtext, ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <?php echo JText::_('JGS_AUTHOR_OWNER'); ?>:
      </div>
      <b><?php echo $user->get('username'); ?></b>
    </div>
    <div class="jg_uprow">
      <div class="jg_uptext">
        <?php echo JText::_('JGS_PICTURE'); ?>:
      </div>
      <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->imgthumbname; ?>" name="imagelib" width="80" border="2" alt="<?php echo JText::_('JGS_THUMBNAIL_PREVIEW'); ?>" />
    </div>
    <div class="jg_txtrow">
      <input type="submit" value="<?php echo JText::_('JGS_SAVE'); ?>" class="button" />
      <input type="button" name="Button" value="<?php echo JText::_('JGS_CANCEL'); ?>"
        onclick = "javascript:location.href='<?php echo JRoute::_('index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID,false); ?>';"
        class="button" />
    </div>
    <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
    </form>
  </div>
<?php
  }//End function Joom_User_EditPic_HTML

}//End class HTML_Joom_UserPanel
?>
