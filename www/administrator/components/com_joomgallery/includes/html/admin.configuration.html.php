<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/html/admin.configuration.html.php $
// $Id: admin.configuration.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_AdminConfig {

  /**
   * Configuration manager
   *
   * @param string $clist
   * @param string $clist2
   * @param string $write_pathimages
   * @param string $write_pathoriginalimages
   * @param string $write_paththumbs
   * @param string $write_pathtemp
   * @param string $write_wmpath
   * @param string $write_pathftpupload
   * @param string $write_wmfile
   * @param string $gdmsg
   * @param string $immsg
   * @param string $easycaptchamsg
   * @param string $exifmsg
   * @param string $configmsg
   */
  function Joom_ShowConfig_HTML($clist, $clist2, $write_pathimages,
                                $write_pathoriginalimages, $write_paththumbs,
                                $write_pathtemp, $write_wmpath,
                                $write_pathftpupload, $write_wmfile,
                                $gdmsg,$immsg, $easycaptchamsg,
                                $exifmsg, $configmsg) {
    $config = Joom_getConfig();
    $display = true;
?>
<form action="index.php" method="post" name="adminForm">
<?php

// instantiate new tab system
$tabs = new jmosTabs(1);

// start nested MainPane
$tabs->startPane("NestedmainPane");
// start first nested MainTab "Grundlegende Einstellungen"
$tabs->startNestedTab(JText::_('JGA_GENERAL_BACKEND_SETTINGS'));
// start first nested tabs pane
$tabs->startPane("NestedPaneTwo");
// start Tab "Grundlegende Einstellungen->Pfade und Verzeichnisse"
$tabs->startTab(JText::_('JGA_PATH_DIRECTORIES'), "nested-three");
HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page1');
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro(JText::_('JGA_CSS_CONFIGURATION_INTRO') . $configmsg);
    if($display) {
      HTML_Joom_AdminConfig::Joom_ShowConfigIntro(JText::_('JGA_PATH_DIRECTORIES_INTRO'));
?>
    <tr align="center" valign="middle">
      <td width="15%" align="left" valign="top"><strong><?php echo JText::_('JGA_PICTURES_PATH') . ':'; ?></strong></td>
      <td width="35%" align="left" valign="top"><input size="50" type="text" name="jg_pathimages" value="<?php echo $config->jg_pathimages; ?>" /><br />[<?php echo $write_pathimages; ?>]</td>
      <td width="50%" align="left" valign="top"><?php echo JText::_('JGA_PATH_PICTURES_STORED'); ?></td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_('JGA_ORIGINALS_PATH') . ':'; ?></strong></td>
      <td align="left" valign="top"><input size="50" type="text" name="jg_pathoriginalimages" value="<?php echo $config->jg_pathoriginalimages; ?>" /><br />[<?php echo $write_pathoriginalimages; ?>]</td>
      <td align="left" valign="top"><?php echo JText::_('JGA_PATH_ORIGINALS_STORED'); ?> </td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_('JGA_THUMBNAILS_PATH') . ':'; ?></strong></td>
      <td align="left" valign="top"><input size="50" type="text" name="jg_paththumbs" value="<?php echo $config->jg_paththumbs; ?>" /><br />[<?php echo $write_paththumbs; ?>]</td>
      <td align="left" valign="top"><?php echo JText::_('JGA_PATH_THUMBNAILS_STORED'); ?></td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_('JGA_FTPUPLOAD_PATH') . ':'; ?></strong></td>
      <td align="left" valign="top"><input size="50" type="text" name="jg_pathftpupload" value="<?php echo $config->jg_pathftpupload; ?>" /><br />[<?php echo $write_pathftpupload; ?>]</td>
      <td align="left" valign="top"><?php echo JText::_('JGA_PATH_FOR_FTPUPLOAD'); ?></td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_('JGA_TEMP_PATH') . ':'; ?></strong></td>
      <td align="left" valign="top"><input size="50" type="text" name="jg_pathtemp" value="<?php echo $config->jg_pathtemp; ?>" /><br />[<?php echo $write_pathtemp; ?>]</td>
      <td align="left" valign="top"><?php echo JText::_('JGA_PATH_FOR_TEMP'); ?></td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_('JGA_WATERMARK_PATH') . ':'; ?></strong></td>
      <td align="left" valign="top"><input size="50" type="text" name="jg_wmpath" value="<?php echo $config->jg_wmpath; ?>" /><br />[<?php echo $write_wmpath; ?>]</td>
      <td align="left" valign="top"><?php echo JText::_('JGA_PATH_WATERMARK_STORED'); ?></td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_('JGA_WATERMARK_FILE') . ':'; ?></strong></td>
      <td align="left" valign="top"><input size="50" type="text" name="jg_wmfile" value="<?php echo $config->jg_wmfile; ?>" /><br />[<?php echo $write_wmfile; ?>]</td>
      <td align="left" valign="top"><?php echo JText::_('JGA_WATERMARK_FILE_LONG'); ?></td>
    </tr>
<?php
    }
    $date[] = JHTML::_('select.option','%d-%m-%Y %H:%M:%S', strftime("%d-%m-%Y %H:%M:%S"));
    $date[] = JHTML::_('select.option','%d.%m.%Y %H:%M:%S', strftime("%d.%m.%Y %H:%M:%S"));
    $date[] = JHTML::_('select.option','%m-%d-%Y %H:%M:%S', strftime("%m-%d-%Y %H:%M:%S"));
    $date[] = JHTML::_('select.option','%m.%d.%Y %H:%M:%S', strftime("%m.%d.%Y %H:%M:%S"));
    $date[] = JHTML::_('select.option','%m/%d/%Y %I:%M:%S %p', strftime("%m/%d/%Y %I:%M:%S %p"));
    $date[] = JHTML::_('select.option','%c', strftime("%c"));
    $mc_jg_dateformat= JHTML::_('select.genericlist', $date, 'jg_dateformat', 'class="inputbox" size="1"', 'value', 'text', $config->jg_dateformat);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_dateformat', 'custom', 'JGA_TIME', $mc_jg_dateformat);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_checkupdate', 'yesno', 'JGA_CHECKUPDATE', $config->jg_checkupdate);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();
// end Tab "Grundlegende Einstellungen->Pfade und Verzeichnisse"
$tabs->endTab();
// start Tab "Grundlegende Einstellungen->Ersetzungen"
$tabs->startTab(JText::_('JGA_BACKEND_REPLACEMENTS'), "nested-twentyeight");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page26');
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro(JText::_('JGA_BACKEND_REPLACEMENTS_INTRO'));
    $yesno[] = JHTML::_('select.option','0', JText::_('NO'));
    $yesno[] = JHTML::_('select.option','1', JText::_('YES'));
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_filenamewithjs', 'yesno', 'JGA_FILENAME_WITHJS', $config->jg_filenamewithjs);
    $tl_jg_filenamesearch = '<input type="text" name="jg_filenamesearch" value="'.$config->jg_filenamesearch.'" size="50" />';
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_filenamesearch', 'custom', 'JGA_FILENAME_SEARCH', $tl_jg_filenamesearch);
    $tl_jg_filenamereplace = '<input type="text" name="jg_filenamereplace" value="'.$config->jg_filenamereplace.'" size="50" />';
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_filenamereplace', 'custom', 'JGA_FILENAME_REPLACE', $tl_jg_filenamereplace);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Grundlegende Einstellungen->Ersetzungen"
$tabs->endTab();
// start Tab "Grundlegende Einstellungen->Bildmanipulation"
$tabs->startTab(JText::_('JGA_PICTURE_PROCESSING'), "nested-four");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page2');
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro('<div align="center"><strong>'.$gdmsg.'</strong></div>');
    $thumbcreator[] = JHTML::_('select.option','none', JText::_('JGA_NONE'));
    $thumbcreator[] = JHTML::_('select.option','gd1', JText::_('JGA_GDLIB'));
    $thumbcreator[] = JHTML::_('select.option','gd2', JText::_('JGA_GD2LIB'));
    $thumbcreator[] = JHTML::_('select.option','im', JText::_('JGA_IMAGEMAGICK'));
    $mc_jg_thumbcreation = JHTML::_('select.genericlist',$thumbcreator, 'jg_thumbcreation', 'class="inputbox" size="4"', 'value', 'text', $config->jg_thumbcreation);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_thumbcreation', 'custom', 'JGA_PICTURE_CREATOR', $mc_jg_thumbcreation);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_fastgd2thumbcreation', 'yesno', 'JGA_FAST_GD2_THUMBCREATION', $config->jg_fastgd2thumbcreation);
    /*$tl_jg_impath = '<input type="text" name="jg_impath" value="'.$config->jg_impath.'" size="50" />';
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_impath', 'custom', 'JGA_PATH_TO_IMAGEMAGICK', $tl_jg_impath);*/
?>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><b><?php echo JText::_('JGA_PATH_TO_IMAGEMAGICK'); ?></b></td>
      <td align="left" valign="top"><input size="50" type="text" name="jg_impath" value="<?php echo $config->jg_impath; ?>" /></td>
      <td align="left" valign="top"><?php echo $immsg; ?></td>
    </tr>
<?php
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_resizetomaxwidth', 'yesno', 'JGA_RESIZING', $config->jg_resizetomaxwidth);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_maxwidth', 'text', 'JGA_MAX_WIDTH', $config->jg_maxwidth);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_picturequality', 'text', 'JGA_PICTURE_QUALITY', $config->jg_picturequality);
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro(JText::_('JGA_THUMBNAILS_INTRO'));
    $directionresize[] = JHTML::_('select.option','0', JText::_('JGA_SAMEHIGHT'));
    $directionresize[] = JHTML::_('select.option','1', JText::_('JGA_SAMEWIDTH'));
    $mc_jg_useforresizedirection = JHTML::_('select.genericlist',$directionresize, 'jg_useforresizedirection', 'class="inputbox" size="2"', 'value', 'text', $config->jg_useforresizedirection);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_useforresizedirection', 'custom', 'JGA_DIRECTION_RESIZE', $mc_jg_useforresizedirection);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_thumbwidth', 'text', 'JGA_THUMBNAIL_WIDTH', $config->jg_thumbwidth);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_thumbheight', 'text', 'JGA_THUMBNAIL_HEIGHT', $config->jg_thumbheight);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_thumbquality', 'text', 'JGA_THUMBNAIL_QUALITY', $config->jg_thumbquality);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Grundlegende Einstellungen->Bildmanipulation"
$tabs->endTab();
// start Tab "Grundlegende Einstellungen->Backend-Upload"
$tabs->startTab(JText::_('JGA_BACKEND_UPLOAD'), "nested-seven");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page5');
    $uploadordering[] = JHTML::_('select.option','0', JText::_('JGA_NO_ORDER'));
    $uploadordering[] = JHTML::_('select.option','1', JText::_('JGA_DESCENDING'));
    $uploadordering[] = JHTML::_('select.option','2', JText::_('JGA_ASCENDING'));
    $mc_jg_uploadorder = JHTML::_('select.genericlist',$uploadordering, 'jg_uploadorder', 'class="inputbox" size="3"', 'value', 'text', $config->jg_uploadorder);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_uploadorder', 'custom', 'JGA_UPLOAD_ORDER', $mc_jg_uploadorder);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_useorigfilename', 'yesno', 'JGA_ORIGINAL_FILENAME', $config->jg_useorigfilename);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_filenamenumber', 'yesno', 'JGA_FILENAMENUMBER', $config->jg_filenamenumber);
    $delete_original[] = JHTML::_('select.option','0', JText::_('NO'));
    $delete_original[] = JHTML::_('select.option','1', JText::_('JGA_DELETE_ALL_ORIGINALS'));
    $delete_original[] = JHTML::_('select.option','2', JText::_('JGA_DELETE_ORIGINAL_CHECKBOX'));
    $mc_jg_delete_original = JHTML::_('select.genericlist',$delete_original, 'jg_delete_original', 'class="inputbox" size="3"', 'value', 'text', $config->jg_delete_original);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_delete_original', 'custom', 'JGA_DELETE_ORIGINAL', $mc_jg_delete_original);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_wrongvaluecolor', 'text', 'JGA_WRONG_VALUE_COLOR', $config->jg_wrongvaluecolor);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Grundlegende Einstellungen->Backend-Upload"
$tabs->endTab();
// start Tab "Grundlegende Einstellungen->Zusaetzliche Funktionen"
$tabs->startTab(JText::_('JGA_MORE_FUNCTIONS'), "nested-eight");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page6');
    $combuild_options[] = JHTML::_('select.option','0', JText::_('NO'));
    $combuild_options[] = JHTML::_('select.option','1', JText::_('JGA_COMBUILDER_SETTING_CB'));
    $combuild_options[] = JHTML::_('select.option','2', JText::_('JGA_COMBUILDER_SETTING_CBE'));
    $combuild_options[] = JHTML::_('select.option','3', JText::_('JGA_COMBUILDER_SETTING_JOMSOCIAL'));
    $mc_jg_combuild = JHTML::_('select.genericlist',$combuild_options, 'jg_combuild', 'class="inputbox" size="4"', 'value', 'text', $config->jg_combuild);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_combuild', 'custom', 'JGA_COMBUILDER_SUPPORT', $mc_jg_combuild);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_realname', 'yesno', 'JGA_USERNAME_REALNAME', $config->jg_realname);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_bridge', 'yesno', 'JGA_BRIDGE_INSTALLED', $config->jg_bridge);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_cooliris', 'yesno', 'JGA_COOLIRIS_SUPPORT', $config->jg_cooliris);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_coolirislink', 'yesno', 'JGA_COOLIRIS_LINK', $config->jg_coolirislink);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Grundlegende Einstellungen->Zusaetzliche Funktionen"
$tabs->endTab();
// end first nested tabs pane NestedPaneTwo
$tabs->endPane();
// end first nested MainTab "Grundlegende Einstellungen"
$tabs->endTab();

// start second nested MainTab "Benutzer-Rechte"
$tabs->startNestedTab(JText::_('JGA_USER_RIGHTS'));
// start second nested tabs pane
$tabs->startPane("NestedPaneThree");
// start Tab "Benutzer-Rechte->Benutzer-Upload ueber "Meine Galerie""
$tabs->startTab(JText::_('JGA_USER_UPLOAD_SETTINGS'), "nested-ten");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page8');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_userspace', 'yesno', 'JGA_ALLOWED_USERSPACE', $config->jg_userspace);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_approve', 'yesno', 'JGA_APPROVAL_NEEDED', $config->jg_approve);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_category', 'custom', 'JGA_ALLOWED_CAT', $clist);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_usercat', 'yesno', 'JGA_ALLOWED_USERCAT', $config->jg_usercat);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_usercategory', 'custom', 'JGA_ALLOWED_USERCATPARENT', $clist2);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_usercatacc', 'yesno', 'JGA_USERCATACC', $config->jg_usercatacc);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_maxusercat', 'text', 'JGA_MAX_ALLOWED_USERCATS', $config->jg_maxusercat);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_userowncatsupload', 'yesno', 'JGA_USERCATSOWNUPLOAD', $config->jg_userowncatsupload);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_maxuserimage', 'text', 'JGA_MAX_ALLOWED_PICS', $config->jg_maxuserimage);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_maxfilesize', 'text', 'JGA_MAX_ALLOWED_FILESIZE', $config->jg_maxfilesize);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_maxuploadfields', 'text', 'JGA_MAX_UPLOAD_FIELDS', $config->jg_maxuploadfields);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_useruploadnumber', 'yesno', 'JGA_USERUPLOAD_NUMBERING', $config->jg_useruploadnumber);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_special_gif_upload', 'yesno', 'JGA_ALLOW_SPECIAL_GIF_UPLOAD', $config->jg_special_gif_upload);
    $delete_original_user[] = JHTML::_('select.option','0', JText::_('NO'));
    $delete_original_user[] = JHTML::_('select.option','1', JText::_('JGA_DELETE_ALL_ORIGINALS'));
    $delete_original_user[] = JHTML::_('select.option','2', JText::_('JGA_DELETE_ORIGINAL_CHECKBOX'));
    $mc_jg_delete_original_user = JHTML::_('select.genericlist',$delete_original_user, 'jg_delete_original_user', 'class="inputbox" size="3"', 'value', 'text', $config->jg_delete_original_user);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_delete_original_user', 'custom', 'JGA_DELETE_ORIGINAL', $mc_jg_delete_original_user);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_newpiccopyright', 'yesno', 'JGA_SHOW_COPYRIGHT', $config->jg_newpiccopyright);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_newpicnote', 'yesno', 'JGA_SHOW_UPLOADNOTE', $config->jg_newpicnote);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Benutzer-Rechte->Benutzer-Upload ueber "Meine Galerie""
$tabs->endTab();
// start Tab "Benutzer-Rechte->Bewertungen"
$tabs->startTab(JText::_('JGA_RATE_SETTINGS'), "nested-eleven");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page9');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showrating', 'yesno', 'JGA_ALLOW_RATING', $config->jg_showrating);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_maxvoting', 'text', 'JGA_HIGHEST_RATING', $config->jg_maxvoting);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_onlyreguservotes', 'yesno', 'JGA_ALLOW_RATING_ONLY_REGUSER', $config->jg_onlyreguservotes);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Benutzer-Rechte->Bewertungen"
$tabs->endTab();
// start Tab "Benutzer-Rechte->Kommentare"
$tabs->startTab(JText::_('JGA_COMMENT_SETTINGS'), "nested-twelve");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page10');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcomment', 'yesno', 'JGA_ALLOW_COMMENTS', $config->jg_showcomment);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_anoncomment', 'yesno', 'JGA_ALLOW_ANONYM_COMMENTS', $config->jg_anoncomment);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_namedanoncomment', 'yesno', 'JGA_NAMED_ANONYM_COMMENTS', $config->jg_namedanoncomment);
    $commentsapprove[] = JHTML::_('select.option','0', JText::_('NO'));
    $commentsapprove[] = JHTML::_('select.option','1', JText::_('JGA_ONLY_UNREGUSERS'));
    $commentsapprove[] = JHTML::_('select.option','2', JText::_('JGA_REG_AND_UNREGUSERS'));
    $mc_jg_approvecom = JHTML::_('select.genericlist',$commentsapprove, 'jg_approvecom', 'class="inputbox" size="3"', 'value', 'text', $config->jg_approvecom);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_approvecom', 'custom', 'JGA_COMMENTS_APPROVE_NEEDED', $mc_jg_approvecom);
?>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_('JGA_CAPTCHA_COMMENTS'); ?></strong></td>
      <td align="left" valign="top">
<?php
    $secimages[] = JHTML::_('select.option','0', JText::_('NO'));
    $secimages[] = JHTML::_('select.option','1', JText::_('JGA_ONLY_UNREGUSERS'));
    $secimages[] = JHTML::_('select.option','2', JText::_('JGA_REG_AND_UNREGUSERS'));
    $mc_jg_secimages = JHTML::_('select.genericlist',$secimages, 'jg_secimages', 'class="inputbox" size="3"', 'value', 'text', $config->jg_secimages);
    #HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_secimages', 'custom', 'JGA_CAPTCHA_COMMENTS', $mc_jg_secimages);
    echo $mc_jg_secimages;
?>
      </td>
      <td align="left" valign="top"><?php echo JText::_('JGA_CAPTCHA_COMMENTS_LONG') . $easycaptchamsg; ?></td>
    </tr>
<?php
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_bbcodesupport', 'yesno', 'JGA_ALLOW_COMMENTS_BBCODE', $config->jg_bbcodesupport);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_smiliesupport', 'yesno', 'JGA_ALLOW_COMMENTS_SMILIES', $config->jg_smiliesupport);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_anismilie', 'yesno', 'JGA_ALLOW_COMMENTS_ANISMILIES', $config->jg_anismilie);
    $smiliescolor[] = JHTML::_('select.option','grey', JText::_('JGA_GREY'));
    $smiliescolor[] = JHTML::_('select.option','orange', JText::_('JGA_ORANGE'));
    $smiliescolor[] = JHTML::_('select.option','yellow', JText::_('JGA_YELLOW'));
    $smiliescolor[] = JHTML::_('select.option','blue', JText::_('JGA_BLUE'));
    $mc_jg_smiliescolor = JHTML::_('select.genericlist',$smiliescolor, 'jg_smiliescolor', 'class="inputbox" size="4"', 'value', 'text', $config->jg_smiliescolor);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_smiliescolor', 'custom', 'JGA_SMILIES_COLOR', $mc_jg_smiliescolor);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Benutzer-Rechte->Kommentare"
$tabs->endTab();
// end second nested tabs pane NestedPaneThree
$tabs->endPane();
// end first nested MainTab "Benutzer-Rechte"
$tabs->endTab();

// start third nested MainTab "Frontend Einstellungen"
$tabs->startNestedTab(JText::_('JGA_FRONTEND_SETTINGS'));
// start third nested tabs pane
$tabs->startPane("NestedPaneFour");
// start Tab "Frontend Einstellungen->Anordnung der Bilder"
$tabs->startTab(JText::_('JGA_PICORDER'), "nested-thirteen");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page11');
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro(JText::_('JGA_PICORDER_INTRO'));
    $picorder[] = JHTML::_('select.option','ordering ASC', JText::_('JGA_ORDERBY_ORDERING_ASC'));
    $picorder[] = JHTML::_('select.option','ordering DESC', JText::_('JGA_ORDERBY_ORDERING_DESC'));
    $picorder[] = JHTML::_('select.option','imgdate ASC', JText::_('JGA_ORDERBY_UPLOADTIME_ASC'));
    $picorder[] = JHTML::_('select.option','imgdate DESC', JText::_('JGA_ORDERBY_UPLOADTIME_DESC'));
    $picorder[] = JHTML::_('select.option','imgtitle ASC', JText::_('JGA_ORDERBY_PICTITLE_ASC'));
    $picorder[] = JHTML::_('select.option','imgtitle DESC', JText::_('JGA_ORDERBY_PICTITLE_DESC'));
    $mc_jg_firstorder = JHTML::_('select.genericlist',$picorder, 'jg_firstorder', 'class="inputbox" size="1"', 'value', 'text', $config->jg_firstorder);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_firstorder', 'custom', 'JGA_PICORDER_FIRST', $mc_jg_firstorder);
    $picorder2[] = JHTML::_('select.option','', JText::_('JGA_PICORDER_NO'));
    $picorder2[] = JHTML::_('select.option','ordering ASC', JText::_('JGA_ORDERBY_ORDERING_ASC'));
    $picorder2[] = JHTML::_('select.option','ordering DESC', JText::_('JGA_ORDERBY_ORDERING_DESC'));
    $picorder2[] = JHTML::_('select.option','imgdate ASC', JText::_('JGA_ORDERBY_UPLOADTIME_ASC'));
    $picorder2[] = JHTML::_('select.option','imgdate DESC', JText::_('JGA_ORDERBY_UPLOADTIME_DESC'));
    $picorder2[] = JHTML::_('select.option','imgtitle ASC', JText::_('JGA_ORDERBY_PICTITLE_ASC'));
    $picorder2[] = JHTML::_('select.option','imgtitle DESC', JText::_('JGA_ORDERBY_PICTITLE_DESC'));
    $mc_jg_secondorder = JHTML::_('select.genericlist',$picorder2, 'jg_secondorder', 'class="inputbox" size="1"', 'value', 'text', $config->jg_secondorder);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_secondorder', 'custom', 'JGA_PICORDER_SECOND', $mc_jg_secondorder);
    $picorder3[] = JHTML::_('select.option','', JText::_('JGA_PICORDER_NO'));
    $picorder3[] = JHTML::_('select.option','ordering ASC', JText::_('JGA_ORDERBY_ORDERING_ASC'));
    $picorder3[] = JHTML::_('select.option','ordering DESC', JText::_('JGA_ORDERBY_ORDERING_DESC'));
    $picorder3[] = JHTML::_('select.option','imgdate ASC', JText::_('JGA_ORDERBY_UPLOADTIME_ASC'));
    $picorder3[] = JHTML::_('select.option','imgdate DESC', JText::_('JGA_ORDERBY_UPLOADTIME_DESC'));
    $picorder3[] = JHTML::_('select.option','imgtitle ASC', JText::_('JGA_ORDERBY_PICTITLE_ASC'));
    $picorder3[] = JHTML::_('select.option','imgtitle DESC', JText::_('JGA_ORDERBY_PICTITLE_DESC'));
    $mc_jg_thirdorder = JHTML::_('select.genericlist',$picorder3, 'jg_thirdorder', 'class="inputbox" size="1"', 'value', 'text', $config->jg_thirdorder);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_thirdorder', 'custom', 'JGA_PICORDER_THIRD', $mc_jg_thirdorder);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Frontend Einstellungen->Anordnung der Bilder"
$tabs->endTab();
// start Tab "Frontend Einstellungen->Seitentitel"
$tabs->startTab(JText::_('JGA_PAGETITLE_SETTINGS'), "nested-fourteen");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page12');
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro(JText::_('JGA_PAGETITLE_SETTINGS_INTRO'));
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_pagetitle_cat', 'text', 'JGA_PAGETITLE_CATVIEW', $config->jg_pagetitle_cat);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_pagetitle_detail', 'text', 'JGA_PAGETITLE_DETAILVIEW', $config->jg_pagetitle_detail);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Frontend Einstellungen->Seitentitel"
$tabs->endTab();
// start Tab "Frontend Einstellungen->Kopf- und Fussbereich"
$tabs->startTab(JText::_('JGA_HEADER_AND_FOOTER'), "nested-fifteen");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page13');
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro(JText::_('JGA_HEADER_AND_FOOTER_INTRO'));
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showgalleryhead', 'yesno', 'JGA_SHOW_GALLERYHEAD', $config->jg_showgalleryhead);
    $pathway[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $pathway[] = JHTML::_('select.option','1', JText::_('JGA_SHOW_IN_HEADER'));
    $pathway[] = JHTML::_('select.option','2', JText::_('JGA_SHOW_IN_FOOTER'));
    $pathway[] = JHTML::_('select.option','3', JText::_('JGA_SHOW_IN_HEADERFOOTER'));
    $mc_jg_showpathway = JHTML::_('select.genericlist',$pathway, 'jg_showpathway', 'class="inputbox" size="4"', 'value', 'text', $config->jg_showpathway);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showpathway', 'custom', 'JGA_SHOW_PATHWAY', $mc_jg_showpathway);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_completebreadcrumbs', 'yesno', 'JGA_COMPLETE_BREADCRUMBS', $config->jg_completebreadcrumbs);
    $search[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $search[] = JHTML::_('select.option','1', JText::_('JGA_SHOW_IN_HEADER'));
    $search[] = JHTML::_('select.option','2', JText::_('JGA_SHOW_IN_FOOTER'));
    $search[] = JHTML::_('select.option','3', JText::_('JGA_SHOW_IN_HEADERFOOTER'));
    $mc_jg_search = JHTML::_('select.genericlist',$search, 'jg_search', 'class="inputbox" size="4"', 'value', 'text', $config->jg_search);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_search', 'custom', 'JGA_SHOW_SEARCHFIELD', $mc_jg_search);
    $shownumbpics[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $shownumbpics[] = JHTML::_('select.option','1', JText::_('JGA_SHOW_IN_HEADER'));
    $shownumbpics[] = JHTML::_('select.option','2', JText::_('JGA_SHOW_IN_FOOTER'));
    $shownumbpics[] = JHTML::_('select.option','3', JText::_('JGA_SHOW_IN_HEADERFOOTER'));
    $mc_jg_showallpics = JHTML::_('select.genericlist',$shownumbpics, 'jg_showallpics', 'class="inputbox" size="4"', 'value', 'text', $config->jg_showallpics);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showallpics', 'custom', 'JGA_SHOW_ALLPICS', $mc_jg_showallpics);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showallhits', 'yesno', 'JGA_SHOW_ALLHITS', $config->jg_showallhits);
    $showbacklink[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $showbacklink[] = JHTML::_('select.option','1', JText::_('JGA_SHOW_IN_HEADER'));
    $showbacklink[] = JHTML::_('select.option','2', JText::_('JGA_SHOW_IN_FOOTER'));
    $showbacklink[] = JHTML::_('select.option','3', JText::_('JGA_SHOW_IN_HEADERFOOTER'));
    $mc_jg_showbacklink = JHTML::_('select.genericlist',$showbacklink, 'jg_showbacklink', 'class="inputbox" size="4"', 'value', 'text', $config->jg_showbacklink);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showbacklink', 'custom', 'JGA_SHOW_BACKLINK', $mc_jg_showbacklink);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_suppresscredits', 'yesno', 'JGA_SHOW_CREDITS', $config->jg_suppresscredits);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Frontend Einstellungen->Kopf- und Fussbereich"
$tabs->endTab();
// start Tab "Frontend Einstellungen->Meine Galerie"
$tabs->startTab(JText::_('JGA_USER_PANEL'), "nested-sixteen");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page14');
    $suserpanel[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $suserpanel[] = JHTML::_('select.option','1', JText::_('JGA_DISPLAY_TO_RMSM'));
    $suserpanel[] = JHTML::_('select.option','2', JText::_('JGA_DISPLAY_TO_SM'));
    $suserpanel[] = JHTML::_('select.option','3', JText::_('JGA_DISPLAY_TO_ALL'));
    $mc_jg_showuserpanel = JHTML::_('select.genericlist',$suserpanel, 'jg_showuserpanel', 'class="inputbox" size="4"', 'value', 'text', $config->jg_showuserpanel);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showuserpanel', 'custom', 'JGA_SHOW_USER_PANEL', $mc_jg_showuserpanel);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showallpicstoadmin', 'yesno', 'JGA_SHOW_ALLPICSTOADMIN', $config->jg_showallpicstoadmin);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showminithumbs', 'yesno', 'JGA_SHOW_MINITHUMBS', $config->jg_showminithumbs);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Frontend Einstellungen->Meine Galerie"
$tabs->endTab();
// start Tab "Frontend Einstellungen->PopUp-Funktionen"
$tabs->startTab(JText::_('JGA_POPUP_SETTINGS'), "nested-eighteen");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page16');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_openjs_padding', 'text', 'JGA_POPUP_OPENJS_BORDERPX', $config->jg_openjs_padding);
?>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_('JGA_POPUP_OPENJS_BACKGROUND'); ?></strong></td>
      <td align="left" valign="top"><input type="text" name="jg_openjs_background" value="<?php echo $config->jg_openjs_background; ?>" /></td>
      <td align="left" valign="top"><?php echo JText::_('JGA_POPUP_OPENJS_BACKGROUND_LONG') . ' ' . JText::_('JGA_STYLE_COLOR_HEX'); ?></td>
    </tr>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_('JGA_POPUP_DHTML_BORDER'); ?></strong></td>
      <td align="left" valign="top"><input type="text" name="jg_dhtml_border" value="<?php echo $config->jg_dhtml_border; ?>" /></td>
      <td align="left" valign="top"><?php echo JText::_('JGA_POPUP_DHTML_BORDER_LONG') . ' ' . JText::_('JGA_STYLE_COLOR_HEX'); ?></td>
    </tr>
<?php
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_show_title_in_dhtml', 'yesno', 'JGA_POPUP_DHTML_SHOW_TITLE', $config->jg_show_title_in_dhtml);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_show_description_in_dhtml', 'yesno', 'JGA_POPUP_DHTML_SHOW_DESCRIPTION', $config->jg_show_description_in_dhtml);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_lightbox_speed', 'text', 'JGA_POPUP_SLIMBOX_SPEED', $config->jg_lightbox_speed);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_lightbox_slide_all', 'yesno', 'JGA_POPUP_SLIDEALL', $config->jg_lightbox_slide_all);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_resize_js_image', 'yesno', 'JGA_POPUP_JS_IMAGERESIZE', $config->jg_resize_js_image);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_disable_rightclick_original', 'yesno', 'JGA_POPUP_DISABLE_RIGHTCLICK', $config->jg_disable_rightclick_original);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Frontend Einstellungen->PopUp-Funktionen"
$tabs->endTab();
// end third nested tabs pane NestedPaneFour
$tabs->endPane();
// end third nested MainTab "Frontend Einstellungen"
$tabs->endTab();

// start fourth nested MainTab "Galerie-Ansicht"
$tabs->startNestedTab(JText::_('JGA_GALLERY_VIEW'));
// start fourth nested tabs pane
$tabs->startPane("NestedPaneFive");
// start Tab "Galerie-Ansicht->Generelle Einstellungen"
$tabs->startTab(JText::_('JGA_GENERAL_SETTINGS'), "nested-nineteen");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page17');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showgallerysubhead', 'yesno', 'JGA_SHOW_GALLERY_PATHWAY', $config->jg_showgallerysubhead);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showallcathead', 'yesno', 'JGA_SHOW_GALLERYHEADER', $config->jg_showallcathead);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_colcat', 'text', 'JGA_NUMB_GALLERY_COLUMN', $config->jg_colcat);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_catperpage', 'text', 'JGA_GALLERYCATS_PER_PAGE', $config->jg_catperpage);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_ordercatbyalpha', 'yesno', 'JGA_ORDER_GALLERYCATS_BY_ALPHA', $config->jg_ordercatbyalpha);
    $showpagecatnavi[] = JHTML::_('select.option','1', JText::_('JGA_DISPLAY_TOP_ONLY'));
    $showpagecatnavi[] = JHTML::_('select.option','2', JText::_('JGA_DISPLAY_TOP_AND_BOTTOM'));
    $showpagecatnavi[] = JHTML::_('select.option','3', JText::_('JGA_DISPLAY_BOTTOM_ONLY'));
    $mc_jg_showgallerypagenav = JHTML::_('select.genericlist',$showpagecatnavi, 'jg_showgallerypagenav', 'class="inputbox" size="3"', 'value', 'text', $config->jg_showgallerypagenav);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showgallerypagenav', 'custom', 'JGA_GALLERY_PAGENAVIGATION', $mc_jg_showgallerypagenav);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcatcount', 'yesno', 'JGA_SHOW_NUMB_GALLERYCATS', $config->jg_showcatcount);
    $catthumbs[] = JHTML::_('select.option','0', JText::_('JGA_DISPLAY_NONE'));
    $catthumbs[] = JHTML::_('select.option','1', JText::_('JGA_DISPLAY_RANDOM'));
    $catthumbs[] = JHTML::_('select.option','2', JText::_('JGA_DISPLAY_MYCHOISE'));
    $mc_jg_showcatthumb = JHTML::_('select.genericlist',$catthumbs, 'jg_showcatthumb', 'class="inputbox" size="3"', 'value', 'text', $config->jg_showcatthumb);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcatthumb', 'custom', 'JGA_SHOW_CATEGORYTHUMBNAIL', $mc_jg_showcatthumb);
    $randomcatthumbs[] = JHTML::_('select.option','1', JText::_('JGA_FROM_PARENT_CAT_ONLY'));
    $randomcatthumbs[] = JHTML::_('select.option','2', JText::_('JGA_FROM_CHILD_CAT_ONLY'));
    $randomcatthumbs[] = JHTML::_('select.option','3', JText::_('JGA_FROM_FAMILY_CAT'));
    $mc_jg_showrandomcatthumb = JHTML::_('select.genericlist',$randomcatthumbs, 'jg_showrandomcatthumb', 'class="inputbox" size="3"', 'value', 'text', $config->jg_showrandomcatthumb);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showrandomcatthumb', 'custom', 'JGA_RANDOMCATTHUMB', $mc_jg_showrandomcatthumb);
    $cthumbalign[] = JHTML::_('select.option','1', JText::_('JGA_LEFT'));
    $cthumbalign[] = JHTML::_('select.option','2', JText::_('JGA_RIGHT'));
    $cthumbalign[] = JHTML::_('select.option','0', JText::_('JGA_CHANGE'));
    $cthumbalign[] = JHTML::_('select.option','3', JText::_('JGA_CENTER'));
    $mc_jg_ctalign = JHTML::_('select.genericlist',$cthumbalign, 'jg_ctalign', 'class="inputbox" size="4"', 'value', 'text', $config->jg_ctalign);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_ctalign', 'custom', 'JGA_CATTHUMB_ALIGN', $mc_jg_ctalign);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showtotalcathits', 'yesno', 'JGA_SHOW_CATEGORY_HITS', $config->jg_showtotalcathits);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcatasnew', 'yesno', 'JGA_SHOW_CATEGORY_ASNEW', $config->jg_showcatasnew);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_catdaysnew', 'text', 'JGA_SHOW_CATEGORY_DAYSNEW', $config->jg_catdaysnew);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_rmsm', 'yesno', 'JGA_SHOW_RMSM', $config->jg_rmsm);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showrmsmcats', 'yesno', 'JGA_SHOW_RMSM_CATEGORIES', $config->jg_showrmsmcats); 
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showsubsingalleryview', 'yesno', 'JGA_SHOW_SUBS_GALLERYVIEW', $config->jg_showsubsingalleryview);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Galerie-Ansicht->Generelle Einstellungen"
$tabs->endTab();
// end fourth nested tabs pane NestedPaneFive
$tabs->endPane();
// end fourth nested MainTab "Galerie-Ansicht"
$tabs->endTab();

// start fifth nested MainTab "Kategorie-Ansicht"
$tabs->startNestedTab(JText::_('JGA_CATEGORY_VIEW'));
// start fifth nested tabs pane
$tabs->startPane("NestedPaneSix");
// start Tab "Kategorie-Ansicht->Generelle Einstellungen"
$tabs->startTab(JText::_('JGA_GENERAL_SETTINGS'), "nested-twenty");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page18');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcathead', 'yesno', 'JGA_SHOW_CATEGORYTITLE', $config->jg_showcathead);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_usercatorder', 'yesno', 'JGA_CATEGORY_ORDERBY_USER', $config->jg_usercatorder);
    $obj_jg_usercatorderlist = Joom_CreateArrayObject($config->jg_usercatorderlist);
    $catorderlist[] = JHTML::_('select.option', 'date', JText::_('JGA_USER_ORDERBY_DATE'));
    $catorderlist[] = JHTML::_('select.option', 'user', JText::_('JGA_USER_ORDERBY_AUTHOR'));
    $catorderlist[] = JHTML::_('select.option', 'title', JText::_('JGA_USER_ORDERBY_TITLE'));
    $catorderlist[] = JHTML::_('select.option', 'hits', JText::_('JGA_USER_ORDERBY_HITS'));
    $catorderlist[] = JHTML::_('select.option', 'rating', JText::_('JGA_USER_ORDERBY_RATING'));
    $mc_jg_usercatorderlist = JHTML::_('select.genericlist', $catorderlist, 'jg_usercatorderlist[]', 'class="inputbox" size="5" multiple="multiple"', 'value', 'text', $obj_jg_usercatorderlist );
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_usercatorderlist', 'custom', 'JGA_CATEGORY_ORDERBY_USER_LIST', $mc_jg_usercatorderlist);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcatdescriptionincat', 'yesno', 'JGA_SHOW_CATDESCRIPTIONINCAT', $config->jg_showcatdescriptionincat);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_colnumb', 'text', 'JGA_NUMB_CATEGORY_COLUMN', $config->jg_colnumb);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_perpage', 'text', 'JGA_CATEGORYPICS_PER_PAGE', $config->jg_perpage);
    $catthumbalign[] = JHTML::_('select.option','1', JText::_('JGA_LEFT'));
    $catthumbalign[] = JHTML::_('select.option','3', JText::_('JGA_CENTER'));
    $catthumbalign[] = JHTML::_('select.option','2', JText::_('JGA_RIGHT'));
    $mc_jg_catthumbalign = JHTML::_('select.genericlist',$catthumbalign, 'jg_catthumbalign', 'class="inputbox" size="3"', 'value', 'text', $config->jg_catthumbalign);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_catthumbalign', 'custom', 'JGA_CATEGORY_THUMBALIGN', $mc_jg_catthumbalign);
    $showpagenavi[] = JHTML::_('select.option','1', JText::_('JGA_DISPLAY_TOP_ONLY'));
    $showpagenavi[] = JHTML::_('select.option','2', JText::_('JGA_DISPLAY_TOP_AND_BOTTOM'));
    $showpagenavi[] = JHTML::_('select.option','3', JText::_('JGA_DISPLAY_BOTTOM_ONLY'));
    $mc_jg_showpagenav = JHTML::_('select.genericlist',$showpagenavi, 'jg_showpagenav', 'class="inputbox" size="3"', 'value', 'text', $config->jg_showpagenav);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showpagenav', 'custom', 'JGA_CATEGORY_PAGENAVIGATION', $mc_jg_showpagenav);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showpiccount', 'yesno', 'JGA_SHOW_NUMB_CATEGORYPICS', $config->jg_showpiccount);
    $detailpic_open[] = JHTML::_('select.option','0', JText::_('JGA_OPEN_NORMAL'));
    $detailpic_open[] = JHTML::_('select.option','1', JText::_('JGA_OPEN_BLANK_WINDOW'));
    $detailpic_open[] = JHTML::_('select.option','2', JText::_('JGA_OPEN_JS_WINDOW'));
    $detailpic_open[] = JHTML::_('select.option','3', JText::_('JGA_OPEN_DHTML'));
    $detailpic_open[] = JHTML::_('select.option','5', JText::_('JGA_OPEN_THICKBOX3'));
    $detailpic_open[] = JHTML::_('select.option','6', JText::_('JGA_OPEN_SLIMBOX'));
    $mc_jg_detailpic_open = JHTML::_('select.genericlist',$detailpic_open, 'jg_detailpic_open', 'class="inputbox" size="6"', 'value', 'text', $config->jg_detailpic_open);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_detailpic_open', 'custom', 'JGA_OPEN_DETAIL_VIEW', $mc_jg_detailpic_open);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_lightboxbigpic', 'yesno', 'JGA_POPUP_ORIGINAL', $config->jg_lightboxbigpic);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showtitle', 'yesno', 'JGA_SHOW_PICTURE_TITLE', $config->jg_showtitle);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showpicasnew', 'yesno', 'JGA_SHOW_PICTURE_ASNEW', $config->jg_showpicasnew);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_daysnew', 'text', 'JGA_SHOW_PICTURE_DAYSNEW', $config->jg_daysnew);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showhits', 'yesno', 'JGA_SHOW_PICTURE_HITS', $config->jg_showhits);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showauthor', 'yesno', 'JGA_SHOW_PICTURE_AUTHOR', $config->jg_showauthor);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showowner', 'yesno', 'JGA_SHOW_PICTURE_OWNER', $config->jg_showowner);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcatcom', 'yesno', 'JGA_SHOW_PICTURE_COMMENTS', $config->jg_showcatcom);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcatrate', 'yesno', 'JGA_SHOW_PICTURE_RATINGS', $config->jg_showcatrate);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcatdescription', 'yesno', 'JGA_SHOW_PICTURE_DESCRIPTION', $config->jg_showcatdescription);
    $showcategorydownload[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $showcategorydownload[] = JHTML::_('select.option','1', JText::_('JGA_DISPLAY_TO_RMSM'));
    $showcategorydownload[] = JHTML::_('select.option','2', JText::_('JGA_DISPLAY_TO_SM'));
    $showcategorydownload[] = JHTML::_('select.option','3', JText::_('JGA_DISPLAY_TO_ALL'));
    $mc_jg_showcategorydownload = JHTML::_('select.genericlist',$showcategorydownload, 'jg_showcategorydownload', 'class="inputbox" size="4"', 'value', 'text', $config->jg_showcategorydownload);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcategorydownload', 'custom', 'JGA_SHOW_DETAIL_DOWNLOAD', $mc_jg_showcategorydownload);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcategoryfavourite', 'yesno', 'JGA_SHOW_FAVOURITES_LINK', $config->jg_showcategoryfavourite);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Kategorie-Ansicht->Generelle Einstellungen"
$tabs->endTab();
// start Tab "Kategorie-Ansicht->Unterkategorien"
$tabs->startTab(JText::_('JGA_SUBCAT_SETTINGS'), "nested-twentyone");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page19');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showsubcathead', 'yesno', 'JGA_SHOW_SUBCATEGORYHEADER', $config->jg_showsubcathead);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showsubcatcount', 'yesno', 'JGA_SHOW_NUMB_SUBCATEGORIES', $config->jg_showsubcatcount);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_colsubcat', 'text', 'JGA_NUMB_SUBCATEGORY_COLUMN', $config->jg_colsubcat);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_subperpage', 'text', 'JGA_CATEGORYSUBCATS_PER_PAGE', $config->jg_subperpage);
    $showpagenavisubs[] = JHTML::_('select.option','1', JText::_('JGA_DISPLAY_TOP_ONLY'));
    $showpagenavisubs[] = JHTML::_('select.option','2', JText::_('JGA_DISPLAY_TOP_AND_BOTTOM'));
    $mc_jg_showpagenavsubs = JHTML::_('select.genericlist',$showpagenavisubs, 'jg_showpagenavsubs', 'class="inputbox" size="2"', 'value', 'text', $config->jg_showpagenavsubs);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showpagenavsubs', 'custom', 'JGA_CATEGORY_PAGENAVIGATION', $mc_jg_showpagenavsubs);
    $subcatthumbalign[] = JHTML::_('select.option','1', JText::_('JGA_LEFT'));
    $subcatthumbalign[] = JHTML::_('select.option','3', JText::_('JGA_CENTER'));
    $subcatthumbalign[] = JHTML::_('select.option','2', JText::_('JGA_RIGHT'));
    $mc_jg_subcatthumbalign = JHTML::_('select.genericlist',$subcatthumbalign, 'jg_subcatthumbalign', 'class="inputbox" size="3"', 'value', 'text', $config->jg_subcatthumbalign);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_subcatthumbalign', 'custom', 'JGA_CATEGORY_THUMBALIGN', $mc_jg_subcatthumbalign);
    $subthumbs[] = JHTML::_('select.option','0', JText::_('JGA_DISPLAY_NONE'));
    $subthumbs[] = JHTML::_('select.option','1', JText::_('JGA_DISPLAY_MYCHOISE'));
    $subthumbs[] = JHTML::_('select.option','2', JText::_('JGA_DISPLAY_RANDOM'));
    $mc_jg_showsubthumbs = JHTML::_('select.genericlist',$subthumbs, 'jg_showsubthumbs', 'class="inputbox" size="3"', 'value', 'text', $config->jg_showsubthumbs);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showsubthumbs', 'custom', 'JGA_SHOW_CATEGORYTHUMBNAIL', $mc_jg_showsubthumbs);
    $randomsubthumbs[] = JHTML::_('select.option','1', JText::_('JGA_FROM_PARENT_CAT_ONLY'));
    $randomsubthumbs[] = JHTML::_('select.option','2', JText::_('JGA_FROM_CHILD_CAT_ONLY'));
    $randomsubthumbs[] = JHTML::_('select.option','3', JText::_('JGA_FROM_FAMILY_CAT'));
    $mc_jg_showrandomsubthumb = JHTML::_('select.genericlist',$randomsubthumbs, 'jg_showrandomsubthumb', 'class="inputbox" size="3"', 'value', 'text', $config->jg_showrandomsubthumb);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showrandomsubthumb', 'custom', 'JGA_RANDOMCATTHUMB', $mc_jg_showrandomsubthumb);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_ordersubcatbyalpha', 'yesno', 'JGA_ORDER_SUBCATEGORIES_BY_ALPHA', $config->jg_ordersubcatbyalpha);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showtotalsubcathits', 'yesno', 'JGA_SHOW_CATEGORY_HITS', $config->jg_showtotalsubcathits);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Kategorie-Ansicht->Unterkategorien"
$tabs->endTab();
// end fifth nested tabs pane NestedPaneSix
$tabs->endPane();
// end fifth nested MainTab "Kategorie-Ansicht"
$tabs->endTab();

// start sixth nested MainTab "Detail-Ansicht"
$tabs->startNestedTab(JText::_('JGA_DETAIL_VIEW'));
// start sixth nested tabs pane
$tabs->startPane("NestedPaneSeven");
// start Tab "Detail-Ansicht->Generelle Einstellungen"
$tabs->startTab(JText::_('JGA_GENERAL_SETTINGS'), "nested-twentytwo");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page2');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetailpage', 'yesno', 'JGA_ALLOW_DETAILPAGE', $config->jg_showdetailpage);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetailnumberofpics', 'yesno', 'JGA_SHOW_DETAIL_NUMBEROFPICS', $config->jg_showdetailnumberofpics);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_cursor_navigation', 'yesno', 'JGA_DETAIL_CURSOR_NAVIGATION', $config->jg_cursor_navigation);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_disable_rightclick_detail', 'yesno', 'JGA_DETAIL_DISABLE_RIGHTCLICK', $config->jg_disable_rightclick_detail);
    $showdetailtitle[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $showdetailtitle[] = JHTML::_('select.option','1', JText::_('JGA_TOP'));
    $showdetailtitle[] = JHTML::_('select.option','2', JText::_('JGA_BOTTOM'));
    $mc_jg_showdetailtitle = JHTML::_('select.genericlist',$showdetailtitle, 'jg_showdetailtitle', 'class="inputbox" size="3"', 'value', 'text', $config->jg_showdetailtitle);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetailtitle', 'custom', 'JGA_SHOW_DETAIL_TITLE', $mc_jg_showdetailtitle);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetail', 'yesno', 'JGA_SHOW_DETAIL_INFORMATION', $config->jg_showdetail);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetailaccordion', 'yesno', 'JGA_SHOW_DETAIL_ACCORDION', $config->jg_showdetailaccordion);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetaildescription', 'yesno', 'JGA_SHOW_DETAIL_DESCRIPTION', $config->jg_showdetaildescription);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetaildatum', 'yesno', 'JGA_SHOW_DETAIL_DATE', $config->jg_showdetaildatum);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetailhits', 'yesno', 'JGA_SHOW_DETAIL_HITS', $config->jg_showdetailhits);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetailrating', 'yesno', 'JGA_SHOW_DETAIL_RATING', $config->jg_showdetailrating);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetailfilesize', 'yesno', 'JGA_SHOW_DETAIL_FILESIZE', $config->jg_showdetailfilesize);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetailauthor', 'yesno', 'JGA_SHOW_DETAIL_AUTHOR', $config->jg_showdetailauthor);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showoriginalfilesize', 'yesno', 'JGA_SHOW_DETAIL_ORIGFILESIZE', $config->jg_showoriginalfilesize);
    $showdownload[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $showdownload[] = JHTML::_('select.option','1', JText::_('JGA_DISPLAY_TO_RMSM'));
    $showdownload[] = JHTML::_('select.option','2', JText::_('JGA_DISPLAY_TO_SM'));
    $showdownload[] = JHTML::_('select.option','3', JText::_('JGA_DISPLAY_TO_ALL'));
    $mc_jg_showdetaildownload = JHTML::_('select.genericlist',$showdownload, 'jg_showdetaildownload', 'class="inputbox" size="4"', 'value', 'text', $config->jg_showdetaildownload);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetaildownload', 'custom', 'JGA_SHOW_DETAIL_DOWNLOAD', $mc_jg_showdetaildownload);
    $downloadfile[] = JHTML::_('select.option','0', JText::_('JGA_RESIZED_ONLY'));
    $downloadfile[] = JHTML::_('select.option','1', JText::_('JGA_ORIGINAL_ONLY'));
    $downloadfile[] = JHTML::_('select.option','2', JText::_('JGA_RESIZED_IFNO_ORIGINAL'));
    $mc_jg_downloadfile = JHTML::_('select.genericlist',$downloadfile, 'jg_downloadfile', 'class="inputbox" size="3"', 'value', 'text', $config->jg_downloadfile);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_downloadfile', 'custom', 'JGA_DETAIL_DOWNLOADFILE', $mc_jg_downloadfile);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_downloadwithwatermark', 'yesno', 'JGA_DETAIL_DOWNLOADWITHWATERMARK', $config->jg_downloadwithwatermark);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_watermark', 'yesno', 'JGA_DETAIL_INSERT_WATERMARK', $config->jg_watermark);
    $watermarkpos[] = JHTML::_('select.option','1', JText::_('JGA_TOP_LEFT'));
    $watermarkpos[] = JHTML::_('select.option','2', JText::_('JGA_TOP_CENTER'));
    $watermarkpos[] = JHTML::_('select.option','3', JText::_('JGA_TOP_RIGHT'));
    $watermarkpos[] = JHTML::_('select.option','4', JText::_('JGA_MIDDLE_LEFT'));
    $watermarkpos[] = JHTML::_('select.option','5', JText::_('JGA_MIDDLE_CENTER'));
    $watermarkpos[] = JHTML::_('select.option','6', JText::_('JGA_MIDDLE_RIGHT'));
    $watermarkpos[] = JHTML::_('select.option','7', JText::_('JGA_BOTTOM_LEFT'));
    $watermarkpos[] = JHTML::_('select.option','8', JText::_('JGA_BOTTOM_CENTER'));
    $watermarkpos[] = JHTML::_('select.option','9', JText::_('JGA_BOTTOM_RIGHT'));
    $mc_jg_watermarkpos = JHTML::_('select.genericlist',$watermarkpos, 'jg_watermarkpos', 'class="inputbox" size="1"', 'value', 'text', $config->jg_watermarkpos);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_watermarkpos', 'custom', 'JGA_DETAIL_WATERMARK_POSITION', $mc_jg_watermarkpos);
    $showbigpic[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $showbigpic[] = JHTML::_('select.option','1', JText::_('JGA_DISPLAY_TO_RMSM'));
    $showbigpic[] = JHTML::_('select.option','2', JText::_('JGA_DISPLAY_TO_ALL'));
    $mc_jg_bigpic = JHTML::_('select.genericlist',$showbigpic, 'jg_bigpic', 'class="inputbox" size="3"', 'value', 'text', $config->jg_bigpic);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_bigpic', 'custom', 'JGA_SHOW_DETAIL_LINKTOORIGINAL', $mc_jg_bigpic);
    $showbigpic_open[] = JHTML::_('select.option','1', JText::_('JGA_OPEN_BLANK_WINDOW'));
    $showbigpic_open[] = JHTML::_('select.option','2', JText::_('JGA_OPEN_JS_WINDOW'));
    $showbigpic_open[] = JHTML::_('select.option','3', JText::_('JGA_OPEN_DHTML'));
    $showbigpic_open[] = JHTML::_('select.option','5', JText::_('JGA_OPEN_THICKBOX3'));
    $showbigpic_open[] = JHTML::_('select.option','6', JText::_('JGA_OPEN_SLIMBOX'));
    $mc_jg_bigpic_open = JHTML::_('select.genericlist',$showbigpic_open, 'jg_bigpic_open', 'class="inputbox" size="5"', 'value', 'text', $config->jg_bigpic_open);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_bigpic_open', 'custom', 'JGA_OPEN_ORIGINAL_VIEW', $mc_jg_bigpic_open);
    $bbcodelinks[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $bbcodelinks[] = JHTML::_('select.option','1', JText::_('JGA_BBCODE_IMG_ONLY'));
    $bbcodelinks[] = JHTML::_('select.option','2', JText::_('JGA_BBCODE_URL_ONLY'));
    $bbcodelinks[] = JHTML::_('select.option','3', JText::_('JGA_BBCODE_BOTH'));
    $mc_jg_bbcodelinks = JHTML::_('select.genericlist',$bbcodelinks, 'jg_bbcodelink', 'class="inputbox" size="4"', 'value', 'text',$config->jg_bbcodelink);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_bbcodelink', 'custom', 'JGA_SHOW_DETAIL_BBCODELINK', $mc_jg_bbcodelinks);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcommentsunreg', 'yesno', 'JGA_SHOW_DETAIL_COMMENTS', $config->jg_showcommentsunreg);
    $showcommentsarea[] = JHTML::_('select.option','1', JText::_('JGA_ABOVE_COMMENTS'));
    $showcommentsarea[] = JHTML::_('select.option','2', JText::_('JGA_UNDERNEATH_COMMENTS'));
    $mc_jg_showcommentsarea = JHTML::_('select.genericlist',$showcommentsarea, 'jg_showcommentsarea', 'class="inputbox" size="2"', 'value', 'text', $config->jg_showcommentsarea);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcommentsarea', 'custom', 'JGA_SHOW_DETAIL_COMMENTSAREA', $mc_jg_showcommentsarea);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_send2friend', 'yesno', 'JGA_SHOW_DETAIL_SEND2FRIEND', $config->jg_send2friend);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Detail-Ansicht->Generelle Einstellungen"
$tabs->endTab();
// start Tab "Detail-Ansicht->Motiongallery"
$tabs->startTab(JText::_('JGA_MOTIONGALLERY_SETTINGS'), "nested-twentythree");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page21');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_minis', 'yesno', 'JGA_SHOW_DETAIL_MOTIONGALLERY', $config->jg_minis);
    $joom_ShowMotionMinis[] = JHTML::_('select.option','1', JText::_('JGA_STATIC'));
    $joom_ShowMotionMinis[] = JHTML::_('select.option','2', JText::_('JGA_MOVEABLE'));
    $mc_jg_motionminis = JHTML::_('select.genericlist',$joom_ShowMotionMinis, 'jg_motionminis', 'class="inputbox" size="2"', 'value', 'text', $config->jg_motionminis);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_motionminis', 'custom', 'JGA_SHOW_DETAIL_COMMENTSAREA', $mc_jg_motionminis);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_motionminiWidth', 'text', 'JGA_MOTIONGALLERY_WIDTH', $config->jg_motionminiWidth);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_motionminiHeight', 'text', 'JGA_MOTIONGALLERY_HEIGHT', $config->jg_motionminiHeight);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_miniWidth', 'text', 'JGA_MOTIONMINIS_MAXWIDTH', $config->jg_miniWidth);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_miniHeight', 'text', 'JGA_MOTIONMINIS_MAXHEIGHT', $config->jg_miniHeight);
    $joom_minisprop[] = JHTML::_('select.option','0', JText::_('JGA_SAMEWIDTHANDHEIGHT'));
    $joom_minisprop[] = JHTML::_('select.option','1', JText::_('JGA_SAMEWIDTH'));
    $joom_minisprop[] = JHTML::_('select.option','2', JText::_('JGA_SAMEHIGHT'));
    $mc_jg_minisprop = JHTML::_('select.genericlist',$joom_minisprop, 'jg_minisprop', 'class="inputbox" size="3"', 'value', 'text', $config->jg_minisprop);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_minisprop', 'custom', 'JGA_MOTIONMINIS_PROPORTIONS', $mc_jg_minisprop);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Detail-Ansicht->Motiongallery"
$tabs->endTab();
// start Tab "Detail-Ansicht->Namensschilder"
$tabs->startTab(JText::_('JGA_NAMESHIELD_SETTINGS'), "nested-twentyfour");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page22');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_nameshields', 'yesno', 'JGA_SHOW_DETAIL_NAMESHIELDS', $config->jg_nameshields);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_nameshields_unreg', 'yesno', 'JGA_NAMESHIELDS_GUEST_VISIBLE', $config->jg_nameshields_unreg);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_show_nameshields_unreg', 'yesno', 'JGA_NAMESHIELDS_GUEST_INFORMATION', $config->jg_show_nameshields_unreg);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_nameshields_height', 'text', 'JGA_NAMESHIELDS_HEIGHT', $config->jg_nameshields_height);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_nameshields_width', 'text', 'JGA_NAMESHIELDS_WIDTH', $config->jg_nameshields_width);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Detail-Ansicht->Namensschilder"
$tabs->endTab();
// start Tab "Detail-Ansicht->Slideshow"
$tabs->startTab(JText::_('JGA_SLIDESHOW_SETTINGS'), "nested-twentyfive");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page23');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_slideshow', 'yesno', 'JGA_SHOW_DETAIL_SLIDESHOW', $config->jg_slideshow);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_slideshow_timer', 'text', 'JGA_SLIDESHOW_TIMEFRAME', $config->jg_slideshow_timer);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_slideshow_usefilter', 'yesno', 'JGA_SLIDESHOW_TRANSITION', $config->jg_slideshow_usefilter);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_slideshow_filterbychance', 'yesno', 'JGA_SLIDESHOW_TRANSITION_RANDOM', $config->jg_slideshow_filterbychance);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_slideshow_filtertimer', 'text', 'JGA_SLIDESHOW_TRANSITION_TIME', $config->jg_slideshow_filtertimer);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showsliderepeater', 'yesno', 'JGA_SLIDESHOW_ENDLESS_SLIDE', $config->jg_showsliderepeater);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Detail-Ansicht->Slideshow"
$tabs->endTab();
// start Tab "Detail-Ansicht->Exif-Daten"
$tabs->startTab(JText::_('JGA_EXIF_SETTINGS'), "nested-twentyseven");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page25');
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro(JText::_('JGA_EXIF_SETTINGS_INTRO').'<br />'.JText::_('JGA_EXIF_SETTINGS_INTRO2').'<br />'.$exifmsg);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showexifdata', 'yesno', 'JGA_SHOW_DETAIL_EXIFDATA', $config->jg_showexifdata);
?>
  </table><p/>
  <table width="100%" border="0" cellpadding="4" cellspacing="0" class="adminlist">
<?php
    $this->Joom_BuildExifConfig();
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Detail-Ansicht->Exif-Daten"
$tabs->endTab();
// start Tab "Detail-Ansicht->IPTC-Daten"
$tabs->startTab(JText::_('JGA_IPTC_SETTINGS'), "nested-thirty");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page25');
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro(JText::_('JGA_IPTC_SETTINGS_INTRO').'<br />'.JText::_('JGA_IPTC_SETTINGS_INTRO2'));
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showiptcdata', 'yesno', 'JGA_SHOW_DETAIL_IPTCDATA', $config->jg_showiptcdata);
?>
  </table><p/>
  <table width="100%" border="0" cellpadding="4" cellspacing="0" class="adminlist">
<?php
    $this->Joom_BuildIptcConfig();
?>
  </table>
  <table width="100%" border="0" cellpadding="4" cellspacing="0" class="adminlist">
<?php
    HTML_Joom_AdminConfig::Joom_ShowConfigIntro('&sup1;&nbsp;'.JText::_('JGA_IPTC_COPYRIGHT').'<br />'.JText::_('JGA_IPTC_COPYRIGHT_LANGUAGE'));
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Detail-Ansicht->IPTC-Daten"
$tabs->endTab();
// end sixth nested tabs pane NestedPaneSeven
$tabs->endPane();
// end sixth nested MainTab "Detail-Ansicht"
$tabs->endTab();

// start seventh nested MainTab "Toplisten"
$tabs->startNestedTab(JText::_('JGA_TOPLIST_SETTINGS'));
// start seventh nested tabs pane
$tabs->startPane("NestedPaneEight");
// start Tab "Toplisten->Generelle Einstellungen"
$tabs->startTab(JText::_('JGA_GENERAL_SETTINGS'), "nested-twentysix");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page15');
    $toplist[] = JHTML::_('select.option','0', JText::_('JGA_NO_DISPLAY'));
    $toplist[] = JHTML::_('select.option','1', JText::_('JGA_SHOW_IN_HEADER'));
    $toplist[] = JHTML::_('select.option','2', JText::_('JGA_SHOW_IN_HEADERFOOTER'));
    $toplist[] = JHTML::_('select.option','3', JText::_('JGA_SHOW_IN_FOOTER'));
    $mc_jg_showtoplist = JHTML::_('select.genericlist',$toplist, 'jg_showtoplist', 'class="inputbox" size="4"', 'value', 'text', $config->jg_showtoplist);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showtoplist', 'custom', 'JGA_SHOW_TOPLIST', $mc_jg_showtoplist);
    $wheretoplist[] = JHTML::_('select.option','0', JText::_('JGA_ALL_VIEWS'));
    $wheretoplist[] = JHTML::_('select.option','1', JText::_('JGA_ONLY_GALLERYVIEW'));
    $wheretoplist[] = JHTML::_('select.option','2', JText::_('JGA_GALLERY_AND_CATEGORYVIEW'));
    $mc_jg_whereshowtoplist = JHTML::_('select.genericlist', $wheretoplist, 'jg_whereshowtoplist', 'class="inputbox" size="3"', 'value', 'text', $config->jg_whereshowtoplist);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_whereshowtoplist', 'custom', 'JGA_SHOW_TOPLIST_ON_VIEWS', $mc_jg_whereshowtoplist);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_toplistcols', 'text', 'JGA_TOPLIST_NUMB_COLS', $config->jg_toplistcols);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_toplist', 'text', 'JGA_TOPLIST_NUMB_ENTRIES', $config->jg_toplist);
    $topthumbalign[] = JHTML::_('select.option', '1', JText::_('JGA_LEFT'));
    $topthumbalign[] = JHTML::_('select.option', '3', JText::_('JGA_CENTER'));
    $topthumbalign[] = JHTML::_('select.option', '2', JText::_('JGA_RIGHT'));
    $mc_jg_topthumbalign = JHTML::_('select.genericlist', $topthumbalign, 'jg_topthumbalign', 'class="inputbox" size="3"', 'value', 'text', $config->jg_topthumbalign );
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_topthumbalign', 'custom', 'JGA_TOPLIST_THUMBALIGN', $mc_jg_topthumbalign);
    $toptextalign[] = JHTML::_('select.option', '1', JText::_('JGA_LEFT'));
    $toptextalign[] = JHTML::_('select.option', '3', JText::_('JGA_CENTER'));
    $toptextalign[] = JHTML::_('select.option', '2', JText::_('JGA_RIGHT'));
    $mc_jg_toptextalign = JHTML::_('select.genericlist', $toptextalign, 'jg_toptextalign', 'class="inputbox" size="3"', 'value', 'text', $config->jg_toptextalign );
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_toptextalign', 'custom', 'JGA_TOPLIST_TEXTALIGN', $mc_jg_toptextalign);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showrate', 'yesno', 'JGA_TOPLIST_SHOW_RATING', $config->jg_showrate);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showlatest', 'yesno', 'JGA_TOPLIST_SHOW_LATEST', $config->jg_showlatest);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showcom', 'yesno', 'JGA_TOPLIST_SHOW_COMMENTS', $config->jg_showcom);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showthiscomment', 'yesno', 'JGA_TOPLIST_THISCOMMENT', $config->jg_showthiscomment);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showmostviewed', 'yesno', 'JGA_TOPLIST_SHOW_MOSTVIEWED', $config->jg_showmostviewed);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Frontend Einstellungen->Toplisten"
$tabs->endTab();
// end seventh nested tabs pane NestedPaneEight
$tabs->endPane();
// end seventh nested MainTab "Toplisten"
$tabs->endTab();

// start eighth nested MainTab "Favoriten"
$tabs->startNestedTab(JText::_('JGA_FAVOURITES_SETTINGS'));
// start eighth nested tabs pane
$tabs->startPane("NestedPaneNine");
// start Tab "Favoriten->Generelle Einstellungen"
$tabs->startTab(JText::_('JGA_GENERAL_SETTINGS'), "nested-twentynine");

HTML_Joom_AdminConfig::Joom_ShowConfigTableStart('page24');
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_favourites', 'yesno', 'JGA_USE_FAVOURITES', $config->jg_favourites);
    $showdetailfavourite[] = JHTML::_('select.option','0', JText::_('JGA_FAVOURITES_REG_SPEC'));
    $showdetailfavourite[] = JHTML::_('select.option','1', JText::_('JGA_FAVOURITES_ONLY_SPEC'));
    $mc_jg_showdetailfavourite = JHTML::_('select.genericlist',$showdetailfavourite, 'jg_showdetailfavourite', 'class="inputbox" size="2"', 'value', 'text', $config->jg_showdetailfavourite);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_showdetailfavourite', 'custom', 'JGA_FAVOURITES_USERS', $mc_jg_showdetailfavourite);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_favouritesshownotauth', 'yesno', 'JGA_FAVOURITES_GUEST_INFORMATION', $config->jg_favouritesshownotauth);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_maxfavourites', 'text', 'JGA_MAX_FAVOURITES', $config->jg_maxfavourites);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_zipdownload', 'yesno', 'JGA_ZIPDOWNLOAD', $config->jg_zipdownload);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_usefavouritesforpubliczip', 'yesno', 'JGA_FAVOURITES_FOR_PUBLIC_ZIP', $config->jg_usefavouritesforpubliczip);
    HTML_Joom_AdminConfig::Joom_ShowConfigRow('jg_usefavouritesforzip', 'yesno', 'JGA_FAVOURITES_FOR_ZIP', $config->jg_usefavouritesforzip);
HTML_Joom_AdminConfig::Joom_ShowConfigTableEnd();

// end Tab "Favoriten->Generelle Einstellungen"
$tabs->endTab();
// end eighth nested tabs pane NestedPaneNine
$tabs->endPane();
// end eighth nested MainTab "Favoriten"
$tabs->endTab();
// end nested MainPane
$tabs->endPane();
?>
  <input type="hidden" name="option" value="<?php echo _JOOM_OPTION; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
</form>
<br />
<?php
  }

  /**
   * Displays the title, the current setting and the description of
   * one single option of the configuration manager in a table row
   *
   * @param $key      string      the identifier of the configuration option, e.g. 'jg_pathimages'
   * @param $type     string      'text' => textfield, 'yesno' => yes/no selectbox, 'custom' => custom selectbox or textfield
   * @param $name     string      language constant for the title and the description (+_LONG) of the option, will be translated
   * @param $info     string/int  current setting of the option, if $type = 'custom', we will assume that it holds the complete HTML string
   * @param $display  boolean     if set to false, we won't display this option, defaults to true
   */
  function Joom_ShowConfigRow($key, $type, $name, $info, $display = true) {
    if(!$display){
      return;
    }
?>
    <tr align="center" valign="middle">
      <td align="left" valign="top"><strong><?php echo JText::_($name); ?></strong></td>
      <td align="left" valign="top"><?php
    switch($type) {
      case 'text':
        ?><input type="text" name="<?php echo $key; ?>" value="<?php echo $info; ?>" /><?php
        break;
      case 'yesno':
        static $yesno;
        if(!isset($yesno)){
          $yesno = array();
          $yesno[] = JHTML::_('select.option', '0', JText::_('NO'));
          $yesno[] = JHTML::_('select.option', '1', JText::_('YES'));
        }
        echo JHTML::_('select.genericlist', $yesno, $key, 'class="inputbox" size="2"', 'value', 'text', $info);
        break;
      case 'custom':
        echo $info;
        break;
      default:
        break;
    } ?></td>
      <td align="left" valign="top"><?php echo JText::_($name.'_LONG'); ?></td>
    </tr>
<?php
  }

  /**
   * Displays a row (colspan="3") in the config table for additional informations.
   * The text will not be translated, so please use JText::_() afore.
   *
   * @param $text string the text which will be displayed in the row
   */
  function Joom_ShowConfigIntro($text = '&nbsp;') {
?>
    <tr>
      <td colspan="3"><?php echo $text; ?></td>
    </tr>
<?php
  }

  /**
   * Displays the start of one table
   *
   * @param $id string the name of the id assigned to the div
   */
  function Joom_ShowConfigTableStart($id = 'page') {
?>
  <div id="<?php echo $id; ?>">
  <table width="100%" border="0" cellpadding="4" cellspacing="0" class="adminlist">
<?php
  }

  /**
   * Displays the end of one table
   */
  function Joom_ShowConfigTableEnd() {
?>
  </table>
  </div>
<?php
  }
}
?>
