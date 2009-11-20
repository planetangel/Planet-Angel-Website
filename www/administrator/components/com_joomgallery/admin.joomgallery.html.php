<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/admin.joomgallery.html.php $
// $Id: admin.joomgallery.html.php 449 2009-06-14 11:57:04Z aha $
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
*                            Functions / Menu                                  *
\******************************************************************************/

function Joom_ShowMenu_HTML() {
  $database = & JFactory::getDBO();
  $document = & JFactory::getDocument();
  $config   = Joom_GetConfig();
  jimport('joomla.html.pane');

  $document->addStyleDeclaration('
.joom_cpanel img {
  padding:21px 0px !important;
}');

  $database->setQuery("SELECT id
      FROM #__components
      WHERE link = 'option="._JOOM_OPTION."' AND parent=''");
  $id = $database->loadResult();

  $database->setQuery("SELECT *
      FROM #__components
      WHERE parent='".$id."' ORDER BY id ASC");
  $rows = $database->loadObjectList();
?>
<table border="0" cellpadding="10" style="margin-right:auto; margin-left:auto;" class="adminform">
  <tbody>
    <tr>
      <td width="55%" valign="top">
        <div id="cpanel" class="joom_cpanel">
<?php
  foreach($rows as $row) {
    Joom_QuickIconButton($row->admin_menu_link, $row->admin_menu_img, $row->name);
  }
?>
        </div>
      </td>
      <td width="45%" valign="top">
<?php
  $modules  = & JModuleHelper::getModules('joom_cpanel');
  // TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
  $pane     = & JPane::getInstance('sliders', array('allowAllClose' => true));
  echo $pane->startPane("content-pane");
  if($config->jg_checkupdate){
    Joom_ShowDatedExtensions($pane);
  }
  foreach($modules as $module) {
    echo $pane->startPanel( $module->title, 'cpanel-panel-'.$module->name );
    echo JModuleHelper::renderModule( $module );
    echo $pane->endPanel();
  }
  echo $pane->endPane();
  if($config->jg_checkupdate){
    if(!count(Joom_CheckUpdate())){
?>
  <div style=" weight:100%; text-align:center; color:#008000; font-weight:bold;">
    <?php echo JText::_('JGA_SYSTEM_UPTODATE'); ?>
  </div>
<?php
    }
  }
?>
      </td>
    </tr>
  </tbody>
</table>
<?php
}

/**
 * displays a single QuickIconButton of the JoomGallery CPanel
 *
 * @param string page the button will be connected with
 * @param string image of the button
 * @param string label of the button, will be translated
 */
function Joom_QuickIconButton( $link, $image, $text ) {
  $lang = & JFactory::getLanguage();
  $lang->load('com_joomgallery.menu');
  $text = JText::_('com_joomgallery.'.$text);
?>
          <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
            <div class="icon">
              <a href="index.php?<?php echo $link; ?>">
                <?php echo JHTML::_('image.site',  $image, '', NULL, NULL, $text ); ?>
                <span><?php echo $text; ?></span></a>
            </div>
          </div>
<?php
}

/**
 * displays a pane slider with all dated extensions and update information
 *
 * @param   object instance of JPane to use
 * @param   array dated extensions provided by Joom_CheckUpdate
 */
function Joom_ShowDatedExtensions($pane, $extensions = null){
  if(is_null($extensions)){
    $extensions = Joom_CheckUpdate();
  }
  $entries = count($extensions);
  if(!$entries){
    return;
  }
  echo $pane->startPanel(JText::_('JGA_UPDATECHECK_TITLE'), 'cpanel-panel-joom_update');
  $entry = 1;
  foreach($extensions as $name => $extension){
?>
  <div style="padding:10px;">
    <div style="color:red;"><?php echo sprintf(JText::_('JGA_EXTENSION_NOT_UPTODATE'), '<span style="font-weight:bold;">'.$name.'</span>'); ?></div>
    <div style="margin-left:10px;">
      <table>
        <tr>
          <td><?php echo JText::_('JGA_YOUR_VERSION'); ?></td>
          <td><?php echo $extension['installed_version']; ?></td>
        </tr>
        <tr>
          <td><?php echo JText::_('JGA_CURRENT_VERSION'); ?></td>
          <td><?php echo $extension['version']; ?></td>
<?php
    if(isset($extension['releaselink'])){
?>
          <td><a href="<?php echo $extension['releaselink']; ?>" target="_blank"><?php echo JText::_('JGA_MORE_INFO_LINK'); ?></a></td>
<?php
    }
?>
        </tr>
      </table>
    </div>
<?php
    if(isset($extension['downloadlink'])){
?>
    <div style="font-weight:bold;"><a href="<?php echo $extension['downloadlink']; ?>" target="_blank"><?php echo JText::_('JGA_DOWNLOAD_UPDATE_LINK'); ?></a></div>
<?php
    }
    if(isset($extension['updatelink'])){
      //check which method we will use to get the update zip
      if(@ini_get('allow_url_fopen')){
?>
    <div style="margin:5px 0px;"><?php echo JText::_('JGA_AUTOUPDATE_TEXT'); ?>
      <form enctype="multipart/form-data" action="index.php" method="post" name="JoomUpdateForm<?php echo $entry; ?>">
        <input type="hidden" name="installtype" value="url" />
        <input type="hidden" name="install_url" value="<?php echo $extension['updatelink']; ?>" />
        <input type="hidden" name="task" value="doInstall" />
        <input type="hidden" name="option" value="com_installer" />
        <?php echo JHTML::_('form.token'); ?>
      </form>
    </div>
    <div style="font-weight:bold;">
      <a href="javascript:document.JoomUpdateForm<?php echo $entry; ?>.submit();"><?php echo JText::_('JGA_AUTOUPDATE_LINK'); ?></a></div>
<?php
      } else {
        if(extension_loaded('curl')){
?>
    <div style="margin:5px 0px;"><?php echo JText::_('JGA_AUTOUPDATE_TEXT'); ?></div>
    <div style="font-weight:bold;">
      <a href="index.php?option=<?php echo _JOOM_OPTION; ?>&amp;task=autoupdate&amp;extension=<?php echo $name; ?>"><?php echo JText::_('JGA_AUTOUPDATE_LINK'); ?></a></div>
<?php
        } else {
?>
    <div style="margin:5px 0px;"><?php echo JText::_('JGA_AUTOUPDATE_NOT_POSSIBLE'); ?></div>
<?php
        }
      }
    }
    if($entry < $entries){
      $entry++;
?>
    <hr />
<?php
    }
?>
  </div>
<?php
  }
  echo $pane->endPanel();
}

function Joom_ShowFooter_HTML() {
?>
        <div class="footer" align="center">
          <p><br />
            <a href="http://www.joomgallery.net" target="_blank">
              <img src='../components/<?php echo _JOOM_OPTION; ?>/assets/images/powered_by.gif'  style=" border-color:#666; border-style:solid; border-width:1px; padding:2px;" alt="Powered by JoomGallery" />
            </a>
          </p>
          By:
          <a href="mailto:team@joomgallery.net">
            JoomGallery::ProjectTeam
          </a>
          <br />
          [Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam]
          <br />
          <?php echo 'Version '.Joom_GetGalleryVersion(); ?>
        </div>
<?php
}
?>
