<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/admin.configuration.php $
// $Id: admin.configuration.php 449 2009-06-14 11:57:04Z aha $
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

include_once(JPATH_COMPONENT.DS.'includes'.DS.'html'.DS.'admin.configuration.html.php');

class Joom_AdminConfiguration {
  var $jg_pathimages;
  var $jg_pathoriginalimages;
  var $jg_paththumbs;
  var $jg_pathftpupload;
  var $jg_pathtemp;
  var $jg_wmpath;
  var $jg_wmfile;
  var $jg_dateformat;
  var $jg_checkupdate;
  var $jg_filenamewithjs;
  var $jg_filenamesearch;
  var $jg_filenamereplace;
  var $jg_thumbcreation;
  var $jg_fastgd2thumbcreation;
  var $jg_impath;
  var $jg_resizetomaxwidth;
  var $jg_maxwidth;
  var $jg_picturequality;
  var $jg_useforresizedirection;
  var $jg_thumbwidth;
  var $jg_thumbheight;
  var $jg_thumbquality;
  var $jg_uploadorder;
  var $jg_useorigfilename;
  var $jg_filenamenumber;
  var $jg_delete_original;
  var $jg_wrongvaluecolor;
  var $jg_combuild ;
  var $jg_realname;
  var $jg_bridge;
  var $jg_cooliris;
  var $jg_coolirislink;
  var $jg_userspace;
  var $jg_approve;
  var $jg_usercat;
  var $jg_maxusercat;
  var $jg_userowncatsupload;
  var $jg_maxuserimage;
  var $jg_maxfilesize;
  var $jg_category;
  var $jg_usercategory;
  var $jg_usercatacc;
  var $jg_maxuploadfields;
  var $jg_useruploadnumber;
  var $jg_special_gif_upload;
  var $jg_delete_original_user;
  var $jg_newpiccopyright;
  var $jg_newpicnote;
  var $jg_showrating;
  var $jg_maxvoting;
  var $jg_onlyreguservotes;
  var $jg_showcomment;
  var $jg_anoncomment;
  var $jg_namedanoncomment;
  var $jg_approvecom;
  var $jg_secimages;
  var $jg_bbcodesupport;
  var $jg_smiliesupport;
  var $jg_anismilie;
  var $jg_smiliescolor;
  var $jg_firstorder;
  var $jg_secondorder;
  var $jg_thirdorder;
  var $jg_pagetitle_cat;
  var $jg_pagetitle_detail;
  var $jg_showgalleryhead;
  var $jg_showpathway;
  var $jg_completebreadcrumbs;
  var $jg_search;
  var $jg_showallpics;
  var $jg_showallhits;
  var $jg_showbacklink;
  var $jg_suppresscredits;
  var $jg_showuserpanel;
  var $jg_showallpicstoadmin;
  var $jg_showminithumbs;
  var $jg_openjs_padding;
  var $jg_openjs_background;
  var $jg_dhtml_border;
  var $jg_show_title_in_dhtml;
  var $jg_show_description_in_dhtml;
  var $jg_lightbox_speed;
  var $jg_lightbox_slide_all;
  var $jg_resize_js_image;
  var $jg_disable_rightclick_original;
  var $jg_showgallerysubhead;
  var $jg_showallcathead;
  var $jg_colcat;
  var $jg_catperpage;
  var $jg_ordercatbyalpha;
  var $jg_showgallerypagenav;
  var $jg_showcatcount;
  var $jg_showcatthumb;
  var $jg_showrandomcatthumb;
  var $jg_ctalign;
  var $jg_showtotalcathits;
  var $jg_showcatasnew;
  var $jg_catdaysnew;
  var $jg_rmsm;
  var $jg_showrmsmcats;
  var $jg_showsubsingalleryview;
  var $jg_showcathead;
  var $jg_usercatorder;
  var $jg_usercatorderlist;
  var $jg_showcatdescriptionincat;
  var $jg_showpagenav;
  var $jg_showpiccount;
  var $jg_perpage;
  var $jg_catthumbalign;
  var $jg_colnumb;
  var $jg_detailpic_open;
  var $jg_lightboxbigpic;
  var $jg_showtitle;
  var $jg_showpicasnew;
  var $jg_daysnew;
  var $jg_showhits;
  var $jg_showauthor;
  var $jg_showowner;
  var $jg_showcatcom;
  var $jg_showcatrate;
  var $jg_showcatdescription;
  var $jg_showcategorydownload;
  var $jg_showcategoryfavourite;
  var $jg_showsubcathead;
  var $jg_showsubcatcount;
  var $jg_colsubcat;
  var $jg_subperpage;
  var $jg_showpagenavsubs;
  var $jg_subcatthumbalign;
  var $jg_showsubthumbs;
  var $jg_showrandomsubthumb;
  var $jg_ordersubcatbyalpha;
  var $jg_showtotalsubcathits;
  var $jg_showdetailpage;
  var $jg_showdetailnumberofpics;
  var $jg_cursor_navigation;
  var $jg_disable_rightclick_detail;
  var $jg_showdetailtitle;
  var $jg_showdetail;
  var $jg_showdetailaccordion;
  var $jg_showdetaildescription;
  var $jg_showdetaildatum;
  var $jg_showdetailhits;
  var $jg_showdetailrating;
  var $jg_showdetailfilesize;
  var $jg_showdetailauthor;
  var $jg_showoriginalfilesize;
  var $jg_showdetaildownload;
  var $jg_downloadfile;
  var $jg_downloadwithwatermark;
  var $jg_watermark;
  var $jg_watermarkpos;
  var $jg_bigpic;
  var $jg_bigpic_open;
  var $jg_bbcodelink;
  var $jg_showcommentsunreg;
  var $jg_showcommentsarea;
  var $jg_send2friend;
  var $jg_minis;
  var $jg_motionminis;
  var $jg_motionminiWidth;
  var $jg_motionminiHeight;
  var $jg_miniWidth;
  var $jg_miniHeight;
  var $jg_minisprop;
  var $jg_nameshields;
  var $jg_nameshields_unreg;
  var $jg_show_nameshields_unreg;
  var $jg_nameshields_height;
  var $jg_nameshields_width;
  var $jg_slideshow;
  var $jg_slideshow_timer;
  var $jg_slideshow_usefilter;
  var $jg_slideshow_filterbychance;
  var $jg_slideshow_filtertimer;
  var $jg_showsliderepeater;
  var $jg_showexifdata;
  var $jg_subifdtags;
  var $jg_ifdotags;
  var $jg_gpstags;
  var $jg_showiptcdata;
  var $jg_iptctags;
  var $jg_showtoplist;
  var $jg_toplist;
  var $jg_topthumbalign;
  var $jg_toptextalign;
  var $jg_toplistcols;
  var $jg_whereshowtoplist;
  var $jg_showrate;
  var $jg_showlatest;
  var $jg_showcom;
  var $jg_showthiscomment;
  var $jg_showmostviewed;
  var $jg_favourites;
  var $jg_showdetailfavourite;
  var $jg_favouritesshownotauth;
  var $jg_maxfavourites;
  var $jg_zipdownload;
  var $jg_usefavouritesforpubliczip;
  var $jg_usefavouritesforzip;

  /**
   * Constructor of class Joom_AdminConfiguration
   *
   * @param string $task
   * @return Joom_AdminConfiguration
   */
  function Joom_AdminConfiguration($task) {
    switch($task) {
      case 'configuration':
        $this->Joom_ShowConfig();
        break;
      case 'saveconfiguration':
        $this->Joom_LoadConfigPost_and_SaveConfig ();
        break;
    }
  }

  /**
   * Read any changed configuration variables from $_POST
   *
   */
  function Joom_LoadConfigPost_and_SaveConfig() {
    $mainframe = & JFactory::getApplication('administrator');
    
    //read existing configuration
    $config = Joom_getConfig();

    foreach($config as $key => $value) {
      $this->$key = $value;
    }
    jimport('joomla.filesystem.file');

    //read from $_POST

    //Description of Tabs in german
    //*** Grundlegende Einstellungen ***
    //Grundlegende Einstellungen->Pfade und Verzeichnisse
    if (isset($_POST['jg_pathimages']))
      $this->jg_pathimages         = Joom_FixPathEntrie(Joom_FixAdminEntrie(Joom_mosGetParam('jg_pathimages','components/'._JOOM_OPTION.'/img_pictures/','post')));
    if (isset($_POST['jg_pathoriginalimages']))
      $this->jg_pathoriginalimages = Joom_FixPathEntrie(Joom_FixAdminEntrie(Joom_mosGetParam('jg_pathoriginalimages','components/'._JOOM_OPTION.'/img_originals/','post')));
    if (isset($_POST['jg_paththumbs']))
      $this->jg_paththumbs         = Joom_FixPathEntrie(Joom_FixAdminEntrie(Joom_mosGetParam('jg_paththumbs','components/'._JOOM_OPTION.'/img_thumbnails/','post')));
    if (isset($_POST['jg_pathftpupload']))
      $this->jg_pathftpupload      = Joom_FixPathEntrie(Joom_FixAdminEntrie(Joom_mosGetParam('jg_pathftpupload','components/'._JOOM_OPTION.'/ftp_upload/','post')));
    if (isset($_POST['jg_pathtemp']))
      $this->jg_pathtemp           = Joom_FixPathEntrie(Joom_FixAdminEntrie(Joom_mosGetParam('jg_pathtemp','administrator/components/'._JOOM_OPTION.'/temp/','post')));
    if (isset($_POST['jg_wmpath']))
      $this->jg_wmpath             = Joom_FixPathEntrie(Joom_FixAdminEntrie(Joom_mosGetParam('jg_wmpath','components/'._JOOM_OPTION.'/assets/images/','post')));
    if (isset($_POST['jg_wmfile']))
      $this->jg_wmfile             = Joom_FixAdminEntrie(Joom_mosGetParam('jg_wmfile', 'watermark.png','post'));
    if (isset($_POST['jg_dateformat']))
      $this->jg_dateformat                   = Joom_mosGetParam('jg_dateformat', '%d-%m-%Y%H:%M:%S','post');
    if (isset($_POST['jg_checkupdate']))
      $this->jg_checkupdate                  = JRequest::getInt('jg_checkupdate', 1,'post');
    //Grundlegende Einstellungen->Ersetzungen
    if (isset($_POST['jg_filenamewithjs']))
      $this->jg_filenamewithjs               = JRequest::getInt('jg_filenamewithjs', 1,'post');
    if (isset($_POST['jg_filenamesearch']))
      $this->jg_filenamesearch               = Joom_mosGetParam('jg_filenamesearch', '','post');
    if (isset($_POST['jg_filenamereplace']))
      $this->jg_filenamereplace              = Joom_mosGetParam('jg_filenamereplace', '','post');
    //Grundlegende Einstellungen->Bildmanipulation
    if (isset($_POST['jg_thumbcreation']))
      $this->jg_thumbcreation                = Joom_mosGetParam('jg_thumbcreation', 'gd2','post');
    if (isset($_POST['jg_fastgd2thumbcreation']))
      $this->jg_fastgd2thumbcreation         = JRequest::getInt('jg_fastgd2thumbcreation', 1,'post');
    if (isset($_POST['jg_impath']))
      $this->jg_impath                       = Joom_mosGetParam('jg_impath', '','post');
    if (isset($_POST['jg_resizetomaxwidth']))
      $this->jg_resizetomaxwidth             = JRequest::getInt('jg_resizetomaxwidth', 1,'post');
    if (isset($_POST['jg_maxwidth']))
      $this->jg_maxwidth                     = JRequest::getInt('jg_maxwidth', 400,'post');
    if (isset($_POST['jg_picturequality']))
      $this->jg_picturequality               = JRequest::getInt('jg_picturequality', 100,'post');
    if (isset($_POST['jg_useforresizedirection']))
      $this->jg_useforresizedirection        = JRequest::getInt('jg_useforresizedirection', 0,'post');
    if (isset($_POST['jg_thumbwidth']))
      $this->jg_thumbwidth                   = JRequest::getInt('jg_thumbwidth', 133,'post');
    if (isset($_POST['jg_thumbheight']))
      $this->jg_thumbheight                  = JRequest::getInt('jg_thumbheight', 100,'post');
    if (isset($_POST['jg_thumbquality']))
      $this->jg_thumbquality                 = JRequest::getInt('jg_thumbquality', 75,'post');
    //Grundlegende Einstellungen->Backend-Upload
    if (isset($_POST['jg_uploadorder']))
      $this->jg_uploadorder                  = JRequest::getInt('jg_uploadorder', 0,'post');
    if (isset($_POST['jg_useorigfilename']))
      $this->jg_useorigfilename              = JRequest::getInt('jg_useorigfilename', 0,'post');
    if (isset($_POST['jg_filenamenumber']))
      $this->jg_filenamenumber               = JRequest::getInt('jg_filenamenumber', 0,'post');
    if (isset($_POST['jg_delete_original']))
      $this->jg_delete_original              = JRequest::getInt('jg_delete_original', 0,'post');
    if (isset($_POST['jg_wrongvaluecolor']))
      $this->jg_wrongvaluecolor              = Joom_FixAdminEntrie(Joom_mosGetParam('jg_wrongvaluecolor', 'red','post'));
    if (isset($_POST['jg_filenamewithjs']))
      $this->jg_filenamewithjs               = JRequest::getInt('jg_filenamewithjs', 1,'post');
    if (isset($_POST['jg_filenamesearch']))
      $this->jg_filenamesearch               = Joom_mosGetParam('jg_filenamesearch', '','post');
    if (isset($_POST['jg_filenamereplace']))
      $this->jg_filenamereplace              = Joom_mosGetParam('jg_filenamereplace', '','post');
    //Grundlegende Einstellungen->Zusaetzliche Funktionen
    if (isset($_POST['jg_combuild']))
      $this->jg_combuild                     = JRequest::getInt('jg_combuild', 0,'post');
    if (isset($_POST['jg_realname']))
      $this->jg_realname                     = JRequest::getInt('jg_realname', 0,'post');
    if (isset($_POST['jg_bridge']))
      $this->jg_bridge                       = JRequest::getInt('jg_bridge', 0,'post');
    if (isset($_POST['jg_cooliris']))
      $this->jg_cooliris                     = JRequest::getInt('jg_cooliris', 0,'post');
    if (isset($_POST['jg_coolirislink']))
      $this->jg_coolirislink                 = JRequest::getInt('jg_coolirislink', 0,'post');

    //*** Benutzer-Rechte ***
    //Benutzer-Rechte->Benutzer-Upload ueber "Meine Galerie"
    if (isset($_POST['jg_userspace']))
      $this->jg_userspace                    = JRequest::getInt('jg_userspace', 0,'post');
    if (isset($_POST['jg_approve']))
      $this->jg_approve                      = JRequest::getInt('jg_approve', 1,'post');
    if (isset($_POST['jg_usercat']))
      $this->jg_usercat                      = JRequest::getInt('jg_usercat', 0,'post');

    if (isset($_POST['jg_usercategory'])) {
      $this->jg_usercategory = array();
      $usercats = Joom_mosGetParam('jg_usercategory', 0,'post');
      foreach($usercats as $usercat) {
        $usercat = intval($usercat);
        if($usercat > 0) {
          array_push($this->jg_usercategory, $usercat);
        }
      }
      $this->jg_usercategory = implode(',',$this->jg_usercategory);
    }
    if (isset($_POST['jg_usercatacc']))
      $this->jg_usercatacc                   = JRequest::getInt('jg_usercatacc', 0,'post');
    if (isset($_POST['jg_maxusercat']))
      $this->jg_maxusercat                   = JRequest::getInt('jg_maxusercat', 10,'post');
    if (isset($_POST['jg_userowncatsupload']))
      $this->jg_userowncatsupload            = JRequest::getInt('jg_userowncatsupload', 400,'post');
    if (isset($_POST['jg_maxuserimage']))
      $this->jg_maxuserimage                 = JRequest::getInt('jg_maxuserimage', 400,'post');
    if (isset($_POST['jg_maxfilesize']))
      $this->jg_maxfilesize                  = JRequest::getInt('jg_maxfilesize', 2000000,'post');

    if (isset($_POST['jg_category'])) {
      $this->jg_category = array();
      $cats = Joom_mosGetParam('jg_category', '','post');
      foreach($cats as $cat) {
        $cat = intval($cat);
        if($cat > 0) {
          array_push($this->jg_category, $cat);
        }
      }
      $this->jg_category = implode(',',$this->jg_category);
    }
    if (isset($_POST['jg_maxuploadfields']))
      $this->jg_maxuploadfields              = JRequest::getInt('jg_maxuploadfields', 3,'post');
    if (isset($_POST['jg_useruploadnumber']))
      $this->jg_useruploadnumber             = JRequest::getInt('jg_useruploadnumber', 0 ,'post');
    if (isset($_POST['jg_special_gif_upload']))
      $this->jg_special_gif_upload           = JRequest::getInt('jg_special_gif_upload', 0,'post');
    if (isset($_POST['jg_delete_original_user']))
      $this->jg_delete_original_user         = JRequest::getInt('jg_delete_original_user', 0,'post');
    if (isset($_POST['jg_newpiccopyright']))
      $this->jg_newpiccopyright              = JRequest::getInt('jg_newpiccopyright', 0,'post');
    if (isset($_POST['jg_newpicnote']))
      $this->jg_newpicnote                   = JRequest::getInt('jg_newpicnote', 0,'post');
    //Benutzer-Rechte->Bewertungen
    if (isset($_POST['jg_showrating']))
      $this->jg_showrating                   = JRequest::getInt('jg_showrating', 0,'post');
    if (isset($_POST['jg_maxvoting']))
      $this->jg_maxvoting                    = JRequest::getInt('jg_maxvoting', 5,'post');
    if (isset($_POST['jg_onlyreguservotes']))
      $this->jg_onlyreguservotes             = JRequest::getInt('jg_onlyreguservotes', 1,'post');
    //Benutzer-Rechte->Kommentare
    if (isset($_POST['jg_showcomment']))
      $this->jg_showcomment                  = JRequest::getInt('jg_showcomment', 0,'post');
    if (isset($_POST['jg_anoncomment']))
      $this->jg_anoncomment                  = JRequest::getInt('jg_anoncomment', 0,'post');
    if (isset($_POST['jg_namedanoncomment']))
      $this->jg_namedanoncomment             = JRequest::getInt('jg_namedanoncomment', 0,'post');
    if (isset($_POST['jg_approvecom']))
      $this->jg_approvecom                   = JRequest::getInt('jg_approvecom', 2,'post');
    if (isset($_POST['jg_secimages']))
      $this->jg_secimages                    = JRequest::getInt('jg_secimages', 2,'post');
    if (isset($_POST['jg_bbcodesupport']))
      $this->jg_bbcodesupport                = JRequest::getInt('jg_bbcodesupport', 0,'post');
    if (isset($_POST['jg_smiliesupport']))
      $this->jg_smiliesupport                = JRequest::getInt('jg_smiliesupport', 0,'post');
    if (isset($_POST['jg_anismilie']))
      $this->jg_anismilie                    = JRequest::getInt('jg_anismilie', 0,'post');
    if (isset($_POST['jg_smiliescolor']))
      $this->jg_smiliescolor                 = Joom_mosGetParam('jg_smiliescolor', 'grey','post');
    
    //*** Frontend Einstellungen ***
    //Frontend Einstellungen->Anordnung der Bilder
    if (isset($_POST['jg_firstorder']))
      $this->jg_firstorder                   = Joom_mosGetParam('jg_firstorder', 'ordering ASC','post');
    if (isset($_POST['jg_secondorder']))
      $this->jg_secondorder                  = Joom_mosGetParam('jg_secondorder', 'imgdate DESC','post');
    if (isset($_POST['jg_thirdorder']))
      $this->jg_thirdorder                   = Joom_mosGetParam('jg_thirdorder', 'imgtitle DESC','post');
    //Frontend Einstellungen->Seitentitel
    if (isset($_POST['jg_pagetitle_cat']))
      $this->jg_pagetitle_cat                = Joom_mosGetParam('jg_pagetitle_cat', '','post');
    if (isset($_POST['jg_pagetitle_detail']))
      $this->jg_pagetitle_detail             = Joom_mosGetParam('jg_pagetitle_detail', '','post');
    //Frontend Einstellungen->Kopf- und Fussbereich
    if (isset($_POST['jg_showgalleryhead']))
      $this->jg_showgalleryhead              = JRequest::getInt('jg_showgalleryhead', 1,'post');
    if (isset($_POST['jg_showpathway']))
      $this->jg_showpathway                  = JRequest::getInt('jg_showpathway', 1,'post');
    if (isset($_POST['jg_completebreadcrumbs']))
      $this->jg_completebreadcrumbs          = JRequest::getInt('jg_completebreadcrumbs', 0,'post');
    if (isset($_POST['jg_search']))
      $this->jg_search                       = JRequest::getInt('jg_search', 1,'post');
    if (isset($_POST['jg_showallpics']))
      $this->jg_showallpics                  = JRequest::getInt('jg_showallpics', 0,'post');
    if (isset($_POST['jg_showallhits']))
      $this->jg_showallhits                  = JRequest::getInt('jg_showallhits', 0,'post');
    if (isset($_POST['jg_showbacklink']))
      $this->jg_showbacklink                 = JRequest::getInt('jg_showbacklink', 0,'post');
    if (isset($_POST['jg_suppresscredits']))
      $this->jg_suppresscredits              = JRequest::getInt('jg_suppresscredits', 1,'post');
    //Frontend Einstellungen->Meine Galerie
    if (isset($_POST['jg_showuserpanel']))
      $this->jg_showuserpanel                = JRequest::getInt('jg_showuserpanel', 1,'post');
    if (isset($_POST['jg_showallpicstoadmin']))
      $this->jg_showallpicstoadmin           = JRequest::getInt('jg_showallpicstoadmin', 0,'post');
    if (isset($_POST['jg_showminithumbs']))
      $this->jg_showminithumbs               = JRequest::getInt('jg_showminithumbs', 1,'post');
    //Frontend Einstellungen->Toplisten
    if (isset($_POST['jg_showtoplist']))
      $this->jg_showtoplist                  = JRequest::getInt('jg_showtoplist', 1,'post');
    if (isset($_POST['jg_toplist']))
      $this->jg_toplist                      = JRequest::getInt('jg_toplist', 10,'post');
    if (isset($_POST['jg_topthumbalign']))
      $this->jg_topthumbalign                = JRequest::getInt('jg_topthumbalign', 1,'post');
    if (isset($_POST['jg_toptextalign']))
      $this->jg_toptextalign                 = JRequest::getInt('jg_toptextalign', 1,'post');
    if (isset($_POST['jg_whereshowtoplist']))
      $this->jg_whereshowtoplist             = JRequest::getInt('jg_whereshowtoplist', 0,'post');
    if (isset($_POST['jg_toplistcols']))
      $this->jg_toplistcols                  = JRequest::getInt('jg_toplistcols', 0,'post');
    if (isset($_POST['jg_showrate']))
      $this->jg_showrate                     = JRequest::getInt('jg_showrate', 1,'post');
    if (isset($_POST['jg_showlatest']))
      $this->jg_showlatest                   = JRequest::getInt('jg_showlatest', 1,'post');
    if (isset($_POST['jg_showcom']))
      $this->jg_showcom                      = JRequest::getInt('jg_showcom', 1,'post');
    if (isset($_POST['jg_showthiscomment']))
      $this->jg_showthiscomment              = JRequest::getInt('jg_showthiscomment', 1,'post');
    if (isset($_POST['jg_showmostviewed']))
      $this->jg_showmostviewed               = JRequest::getInt('jg_showmostviewed', 1,'post');
    //Frontend Einstellungen->PopUp-Funktionen
    if (isset($_POST['jg_openjs_padding']))
      $this->jg_openjs_padding               = JRequest::getInt('jg_openjs_padding', 10,'post');
    if (isset($_POST['jg_openjs_background']))
      $this->jg_openjs_background            = Joom_FixAdminEntrie(Joom_mosGetParam('jg_openjs_background', '#fff','post'));
    if (isset($_POST['jg_dhtml_border']))
      $this->jg_dhtml_border                 = Joom_FixAdminEntrie(Joom_mosGetParam('jg_dhtml_border', '#808080','post'));
    if (isset($_POST['jg_show_title_in_dhtml']))
      $this->jg_show_title_in_dhtml          = JRequest::getInt('jg_show_title_in_dhtml', 0,'post');
    if (isset($_POST['jg_show_description_in_dhtml']))
      $this->jg_show_description_in_dhtml    = JRequest::getInt('jg_show_description_in_dhtml', 0,'post');
    if (isset($_POST['jg_lightbox_speed']))
      $this->jg_lightbox_speed               = JRequest::getInt('jg_lightbox_speed', 5,'post');
    if (isset($_POST['jg_lightbox_slide_all']))
      $this->jg_lightbox_slide_all           = JRequest::getInt('jg_lightbox_slide_all', 0,'post');
    if (isset($_POST['jg_resize_js_image']))
      $this->jg_resize_js_image              = JRequest::getInt('jg_resize_js_image', 1,'post');
    if (isset($_POST['jg_disable_rightclick_original']))
      $this->jg_disable_rightclick_original  = JRequest::getInt('jg_disable_rightclick_original', 1,'post');

    //*** Galerie-Ansicht ***
    //Galerie-Ansicht->Generelle Einstellungen
    if (isset($_POST['jg_showgallerysubhead']))
      $this->jg_showgallerysubhead           = JRequest::getInt('jg_showgallerysubhead', 1,'post');
    if (isset($_POST['jg_showallcathead']))
      $this->jg_showallcathead               = JRequest::getInt('jg_showallcathead', 1,'post');
    if (isset($_POST['jg_colcat']))
      $this->jg_colcat                       = JRequest::getInt('jg_colcat', 1,'post');
    if (isset($_POST['jg_catperpage']))
      $this->jg_catperpage                   = JRequest::getInt('jg_catperpage', 10,'post');
    if (isset($_POST['jg_ordercatbyalpha']))
      $this->jg_ordercatbyalpha              = JRequest::getInt('jg_ordercatbyalpha', 0 ,'post');
    if (isset($_POST['jg_showgallerypagenav']))
      $this->jg_showgallerypagenav           = JRequest::getInt('jg_showgallerypagenav', 1,'post');
    if (isset($_POST['jg_showcatcount']))
      $this->jg_showcatcount                 = JRequest::getInt('jg_showcatcount', 1,'post');
    if (isset($_POST['jg_showcatthumb']))
      $this->jg_showcatthumb                 = JRequest::getInt('jg_showcatthumb', 1,'post');
    if (isset($_POST['jg_showrandomcatthumb']))
      $this->jg_showrandomcatthumb           = JRequest::getInt('jg_showrandomcatthumb', 2,'post');
    if (isset($_POST['jg_ctalign']))
      $this->jg_ctalign                      = JRequest::getInt('jg_ctalign', 1,'post');
    if (isset($_POST['jg_showtotalcathits']))
      $this->jg_showtotalcathits             = JRequest::getInt('jg_showtotalcathits', 1,'post');
    if (isset($_POST['jg_showcatasnew']))
      $this->jg_showcatasnew                 = JRequest::getInt('jg_showcatasnew', 0 ,'post');
    if (isset($_POST['jg_catdaysnew']))
      $this->jg_catdaysnew                   = JRequest::getInt('jg_catdaysnew', 7 ,'post');
    if (isset($_POST['jg_rmsm']))
      $this->jg_rmsm                         = JRequest::getInt('jg_rmsm', 1,'post');
    if (isset($_POST['jg_showrmsmcats']))
      $this->jg_showrmsmcats                 = JRequest::getInt('jg_showrmsmcats', 0,'post');
    if (isset($_POST['jg_showsubsingalleryview']))
      $this->jg_showsubsingalleryview        = JRequest::getInt('jg_showsubsingalleryview', 0,'post');

    //*** Kategorie-Ansicht ***
    //Kategorie-Ansicht->Generelle Einstellungen
    if (isset($_POST['jg_showcathead']))
      $this->jg_showcathead                  = JRequest::getInt('jg_showcathead', 1,'post');
    if (isset($_POST['jg_usercatorder']))
      $this->jg_usercatorder                 = JRequest::getInt('jg_usercatorder', 0 ,'post');
    if (isset($_POST['jg_usercatorderlist']))
      $this->jg_usercatorderlist             = implode(',',Joom_mosGetParam('jg_usercatorderlist', 'date,title','post'));
    if (isset($_POST['jg_showcatdescriptionincat']))
      $this->jg_showcatdescriptionincat      = JRequest::getInt('jg_showcatdescriptionincat', 0 ,'post');
    if (isset($_POST['jg_showpagenav']))
      $this->jg_showpagenav                  = JRequest::getInt('jg_showpagenav', 1,'post');
    if (isset($_POST['jg_showpiccount']))
      $this->jg_showpiccount                 = JRequest::getInt('jg_showpiccount', 1,'post');
    if (isset($_POST['jg_perpage']))
      $this->jg_perpage                      = JRequest::getInt('jg_perpage', 9,'post');
    if (isset($_POST['jg_catthumbalign']))
      $this->jg_catthumbalign                = JRequest::getInt('jg_catthumbalign', 1,'post');
    if (isset($_POST['jg_colnumb']))
      $this->jg_colnumb                      = JRequest::getInt('jg_colnumb', 3,'post');
    if (isset($_POST['jg_detailpic_open']))
      $this->jg_detailpic_open               = JRequest::getInt('jg_detailpic_open', 0,'post');
    if (isset($_POST['jg_lightboxbigpic']))
      $this->jg_lightboxbigpic               = JRequest::getInt('jg_lightboxbigpic', 0,'post');
    if (isset($_POST['jg_showtitle']))
      $this->jg_showtitle                    = JRequest::getInt('jg_showtitle', 1,'post');
    if (isset($_POST['jg_showpicasnew']))
      $this->jg_showpicasnew                 = JRequest::getInt('jg_showpicasnew', 0,'post');
    if (isset($_POST['jg_daysnew']))
      $this->jg_daysnew                      = JRequest::getInt('jg_daysnew', 7,'post');
    if (isset($_POST['jg_showhits']))
      $this->jg_showhits                     = JRequest::getInt('jg_showhits', 1,'post');
    if (isset($_POST['jg_showauthor']))
      $this->jg_showauthor                   = JRequest::getInt('jg_showauthor', 1,'post');
    if (isset($_POST['jg_showowner']))
      $this->jg_showowner                    = JRequest::getInt('jg_showowner', 1,'post');
    if (isset($_POST['jg_showcatcom']))
      $this->jg_showcatcom                   = JRequest::getInt('jg_showcatcom', 1,'post');
    if (isset($_POST['jg_showcatrate']))
      $this->jg_showcatrate                  = JRequest::getInt('jg_showcatrate', 1,'post');
    if (isset($_POST['jg_showcatdescription']))
      $this->jg_showcatdescription           = JRequest::getInt('jg_showcatdescription', 1,'post');
    if (isset($_POST['jg_showcategorydownload']))
      $this->jg_showcategorydownload         = JRequest::getInt('jg_showcategorydownload', 0,'post');
    if (isset($_POST['jg_showcategoryfavourite']))
      $this->jg_showcategoryfavourite        = JRequest::getInt('jg_showcategoryfavourite', 0,'post');
    //Kategorie-Ansicht->Unterkategorien
    if (isset($_POST['jg_showsubcathead']))
      $this->jg_showsubcathead               = JRequest::getInt('jg_showsubcathead', 1,'post');
    if (isset($_POST['jg_showsubcatcount']))
      $this->jg_showsubcatcount              = JRequest::getInt('jg_showsubcatcount', 1,'post');
    if (isset($_POST['jg_colsubcat']))
      $this->jg_colsubcat                    = JRequest::getInt('jg_colsubcat', 3,'post');
    if (isset($_POST['jg_subperpage']))
      $this->jg_subperpage                   = JRequest::getInt('jg_subperpage', 2,'post');
    if (isset($_POST['jg_showpagenavsubs']))
      $this->jg_showpagenavsubs              = JRequest::getInt('jg_showpagenavsubs', 1,'post');
    if (isset($_POST['jg_subcatthumbalign']))
      $this->jg_subcatthumbalign             = JRequest::getInt('jg_subcatthumbalign', 1,'post');
    if (isset($_POST['jg_showsubthumbs']))
      $this->jg_showsubthumbs                = JRequest::getInt('jg_showsubthumbs', 2,'post');
    if (isset($_POST['jg_showrandomsubthumb']))
      $this->jg_showrandomsubthumb           = JRequest::getInt('jg_showrandomsubthumb', 3,'post');
    if (isset($_POST['jg_ordersubcatbyalpha']))
      $this->jg_ordersubcatbyalpha           = JRequest::getInt('jg_ordersubcatbyalpha', 0,'post');
    if (isset($_POST['jg_subcatdetailsalign']))
      $this->jg_subcatdetailsalign           = JRequest::getInt('jg_subcatdetailsalign', 0,'post');
    if (isset($_POST['jg_showtotalsubcathits']))
      $this->jg_showtotalsubcathits          = JRequest::getInt('jg_showtotalsubcathits', 1,'post');

    //*** Detail-Ansicht ***
    //Detail-Ansicht->Generelle Einstellungen
    if (isset($_POST['jg_showdetailpage']))
      $this->jg_showdetailpage               = JRequest::getInt('jg_showdetailpage', 1,'post');
    if (isset($_POST['jg_showdetailnumberofpics']))
      $this->jg_showdetailnumberofpics       = JRequest::getInt('jg_showdetailnumberofpics', 1,'post');
    if (isset($_POST['jg_cursor_navigation']))
      $this->jg_cursor_navigation            = JRequest::getInt('jg_cursor_navigation', 1,'post');
    if (isset($_POST['jg_disable_rightclick_detail']))
      $this->jg_disable_rightclick_detail    = JRequest::getInt('jg_disable_rightclick_detail', 1,'post');
    if (isset($_POST['jg_showdetailtitle']))
      $this->jg_showdetailtitle              = JRequest::getInt('jg_showdetailtitle', 1,'post');
    if (isset($_POST['jg_showdetail']))
      $this->jg_showdetail                   = JRequest::getInt('jg_showdetail', 1,'post');
    if (isset($_POST['jg_showdetailaccordion']))
      $this->jg_showdetailaccordion          = JRequest::getInt('jg_showdetailaccordion', 0,'post');
    if (isset($_POST['jg_showdetaildescription']))
      $this->jg_showdetaildescription        = JRequest::getInt('jg_showdetaildescription', 1,'post');
    if (isset($_POST['jg_showdetaildatum']))
      $this->jg_showdetaildatum              = JRequest::getInt('jg_showdetaildatum', 1,'post');
    if (isset($_POST['jg_showdetailhits']))
      $this->jg_showdetailhits               = JRequest::getInt('jg_showdetailhits', 1,'post');
    if (isset($_POST['jg_showdetailrating']))
      $this->jg_showdetailrating             = JRequest::getInt('jg_showdetailrating', 1,'post');
    if (isset($_POST['jg_showdetailfilesize']))
      $this->jg_showdetailfilesize           = JRequest::getInt('jg_showdetailfilesize', 1,'post');
    if (isset($_POST['jg_showdetailauthor']))
      $this->jg_showdetailauthor             = JRequest::getInt('jg_showdetailauthor', 1,'post');
    if (isset($_POST['jg_showoriginalfilesize']))
      $this->jg_showoriginalfilesize         = JRequest::getInt('jg_showoriginalfilesize', 1,'post');
    if (isset($_POST['jg_showdetaildownload']))
      $this->jg_showdetaildownload           = JRequest::getInt('jg_showdetaildownload', 1,'post');
    if (isset($_POST['jg_downloadfile']))
      $this->jg_downloadfile                 = JRequest::getInt('jg_downloadfile', 1,'post');
    if (isset($_POST['jg_downloadwithwatermark']))
      $this->jg_downloadwithwatermark        = JRequest::getInt('jg_downloadwithwatermark', 1,'post');
    if (isset($_POST['jg_watermark']))
      $this->jg_watermark                    = JRequest::getInt('jg_watermark', 1,'post');
    if (isset($_POST['jg_watermarkpos']))
      $this->jg_watermarkpos                 = JRequest::getInt('jg_watermarkpos', 9,'post');
    if (isset($_POST['jg_bigpic']))
      $this->jg_bigpic                       = JRequest::getInt('jg_bigpic', 1,'post');
    if (isset($_POST['jg_bigpic_open']))
      $this->jg_bigpic_open                  = JRequest::getInt('jg_bigpic_open', 0,'post');
    if (isset($_POST['jg_bbcodelink']))
      $this->jg_bbcodelink                   = JRequest::getInt('jg_bbcodelink', 3,'post');
    if (isset($_POST['jg_showcommentsunreg']))
      $this->jg_showcommentsunreg            = JRequest::getInt('jg_showcommentsunreg', 1,'post');
    if (isset($_POST['jg_showcommentsarea']))
      $this->jg_showcommentsarea             = JRequest::getInt('jg_showcommentsarea', 3,'post');
    if (isset($_POST['jg_send2friend']))
      $this->jg_send2friend                  = JRequest::getInt('jg_send2friend', 1,'post');
    //Detail-Ansicht->Motiongallery
    if (isset($_POST['jg_minis']))
      $this->jg_minis                        = JRequest::getInt('jg_minis', 1,'post');
    if (isset($_POST['jg_motionminis']))
      $this->jg_motionminis                  = JRequest::getInt('jg_motionminis', 1,'post');
    if (isset($_POST['jg_motionminiWidth']))
      $this->jg_motionminiWidth              = JRequest::getInt('jg_motionminiWidth', 400,'post');
    if (isset($_POST['jg_motionminiHeight']))
      $this->jg_motionminiHeight             = JRequest::getInt('jg_motionminiHeight', 50,'post');
    if (isset($_POST['jg_miniWidth']))
      $this->jg_miniWidth                    = JRequest::getInt('jg_miniWidth', 30,'post');
    if (isset($_POST['jg_miniHeight']))
      $this->jg_miniHeight                   = JRequest::getInt('jg_miniHeight', 30,'post');
    if (isset($_POST['jg_minisprop']))
      $this->jg_minisprop                    = JRequest::getInt('jg_minisprop', 0,'post');
    //Detail-Ansicht->Namensschilder
    if (isset($_POST['jg_nameshields']))
      $this->jg_nameshields                  = JRequest::getInt('jg_nameshields', 0 ,'post');
    if (isset($_POST['jg_nameshields_unreg']))
      $this->jg_nameshields_unreg            = JRequest::getInt('jg_nameshields_unreg', 0 ,'post');
    if (isset($_POST['jg_show_nameshields_unreg']))
      $this->jg_show_nameshields_unreg       = JRequest::getInt('jg_show_nameshields_unreg', 0 ,'post');
    if (isset($_POST['jg_nameshields_height']))
      $this->jg_nameshields_height           = JRequest::getInt('jg_nameshields_height', 12 ,'post');
    if (isset($_POST['jg_nameshields_width']))
      $this->jg_nameshields_width            = JRequest::getInt('jg_nameshields_width', 8 ,'post');
    //Detail-Ansicht->Slideshow
    if (isset($_POST['jg_slideshow']))
      $this->jg_slideshow                    = JRequest::getInt('jg_slideshow', 1,'post');
    if (isset($_POST['jg_slideshow_timer']))
      $this->jg_slideshow_timer              = JRequest::getInt('jg_slideshow_timer', 5,'post');
    if (isset($_POST['jg_slideshow_usefilter']))
      $this->jg_slideshow_usefilter          = JRequest::getInt('jg_slideshow_usefilter', 1,'post');
    if (isset($_POST['jg_slideshow_filterbychance']))
      $this->jg_slideshow_filterbychance     = JRequest::getInt('jg_slideshow_filterbychance', 1,'post');
    if (isset($_POST['jg_slideshow_filtertimer']))
      $this->jg_slideshow_filtertimer        = JRequest::getInt('jg_slideshow_filtertimer', 3,'post');
    if (isset($_POST['jg_showsliderepeater']))
      $this->jg_showsliderepeater            = JRequest::getInt('jg_showsliderepeater', 1,'post');
    //Detail-Ansicht->Exif-Daten
    if (isset($_POST['jg_showexifdata']))
      $this->jg_showexifdata                 = JRequest::getInt('jg_showexifdata', 0,'post');

    if (isset($_POST['jg_subifdtags'])) {
      $this->jg_subifdtags = array();
      $subifdtags = Joom_mosGetParam('jg_subifdtags', '','post');
      if ($subifdtags != NULL) {
        foreach($subifdtags as $subifdtag) {
          $subifdtag = intval($subifdtag);
          if($subifdtag > 0) {
            array_push($this->jg_subifdtags, $subifdtag);
          }
        }
        $this->jg_subifdtags = implode(',',$this->jg_subifdtags);
      }
    }
    if (isset($_POST['jg_ifdotags'])) {
      $this->jg_ifdotags = array();
      $ifdotags = Joom_mosGetParam('jg_ifdotags', '','post');
      if ($ifdotags != NULL) {
        foreach($ifdotags as $ifdotag) {
          $ifdotag = intval($ifdotag);
          if($ifdotag > 0) {
            array_push($this->jg_ifdotags, $ifdotag);
          }
        }
        $this->jg_ifdotags = implode(',',$this->jg_ifdotags);
      }
    }
    if (isset($_POST['jg_gpstags'])) {
      $this->jg_gpstags = array();
      $gpstags = Joom_mosGetParam('jg_gpstags', '','post');
      if ($gpstags != NULL) {
        foreach($gpstags as $gpstag) {
          $gpstag = intval($gpstag);
          if($gpstag >= 0) {
            array_push($this->jg_gpstags, $gpstag);
          }
        }
        $this->jg_gpstags = implode(',',$this->jg_gpstags);
      }
    }
    //Detail-Ansicht->IPTC-Daten
    if (isset($_POST['jg_showiptcdata']))
      $this->jg_showiptcdata                 = JRequest::getInt('jg_showiptcdata', 0,'post');
    if (isset($_POST['jg_iptctags'])) {
      $this->jg_iptctags = array();
      $iptctags = Joom_mosGetParam('jg_iptctags', '','post');
      if ($iptctags != NULL) {
        foreach($iptctags as $iptctag) {
          $iptctag = intval($iptctag);
          if($iptctag >= 0) {
            array_push($this->jg_iptctags, $iptctag);
          }
        }
        $this->jg_iptctags = implode(',',$this->jg_iptctags);
      }
    }

    //*** Favouriten ***
    //Favouriten->Generelle Einstellungen
    if (isset($_POST['jg_favourites']))
      $this->jg_favourites                   = JRequest::getInt('jg_favourites', 0,'post');
    if (isset($_POST['jg_showdetailfavourite']))
      $this->jg_showdetailfavourite          = JRequest::getInt('jg_showdetailfavourite', 0,'post');
    if (isset($_POST['jg_favouritesshownotauth']))
      $this->jg_favouritesshownotauth        = JRequest::getInt('jg_favouritesshownotauth', 0,'post');
    if (isset($_POST['jg_maxfavourites']))
      $this->jg_maxfavourites                = JRequest::getInt('jg_maxfavourites', 30,'post');
    if (isset($_POST['jg_zipdownload']))
      $this->jg_zipdownload                  = JRequest::getInt('jg_zipdownload', 1,'post');
    if (isset($_POST['jg_usefavouritesforpubliczip']))
      $this->jg_usefavouritesforpubliczip    = JRequest::getInt('jg_usefavouritesforpubliczip', 1,'post');
    if (isset($_POST['jg_usefavouritesforzip']))
      $this->jg_usefavouritesforzip          = JRequest::getInt('jg_usefavouritesforzip', 0,'post');

    // write CSS-File
    if (!$this->Joom_SaveCSS()) {
      $error = JText::_('JGA_CSS_NOT_WRITEABLE');
      $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=config',$error,'error');
    }

    //Grundlegende Einstellungen
    //Grundlegende Einstellungen->Pfade und Verzeichnisse
    if (!isset($this->jg_pathimages) || ($this->jg_pathimages == '')) {
      $this->jg_pathimages = 'components/'._JOOM_OPTION.'/img_pictures/';
    }
    if (!isset($this->jg_pathoriginalimages) || ($this->jg_pathoriginalimages == '')) {
      $this->jg_pathoriginalimages = 'components/'._JOOM_OPTION.'/img_originals/';
    }
    if (!isset($this->jg_paththumbs) || ($this->jg_paththumbs == '')) {
      $this->jg_paththumbs = 'components/'._JOOM_OPTION.'/img_thumbnails/';
    }
    if (!isset($this->jg_pathftpupload) || ($this->jg_pathftpupload == '')) {
      $this->jg_pathftpupload = 'components/'._JOOM_OPTION.'/ftp_upload/';
    }
    if (!isset($this->jg_pathtemp) || ($this->jg_pathtemp == '')) {
      $this->jg_pathtemp = 'administrator/components/'._JOOM_OPTION.'/temp/';
    }
    if (!isset($this->jg_wmpath) || ($this->jg_wmpath == '')) {
      $this->jg_wmpath = 'components/'._JOOM_OPTION.'/images/';
    }
    if (!isset($this->jg_wmfile) || ($this->jg_wmfile == '')) {
      $this->jg_wmfile = 'watermark.png';
    }
    if (!isset($this->jg_dateformat)) {
      $this->jg_dateformat = '%d-%m-%Y %H:%M:%S';
    }
    if (!isset($this->jg_checkupdate)) {
      $this->jg_checkupdate = 1;
    }
  //Grundlegende Einstellungen->Ersetzungen
    if (!isset($this->jg_filenamewithjs)) {
      $this->jg_filenamewithjs = 1;
    }
    if (!isset($this->jg_filenamesearch)) {
      $this->jg_filenamesearch = '';
    }
    if (!isset($this->jg_filenamereplace)) {
      $this->jg_filenamereplace = '';
    }
  //Grundlegende Einstellungen->Bildmanipulation
    if (!isset($this->jg_thumbcreation)) {
      $this->jg_thumbcreation = 'gd2';
    }
    if (!isset($this->jg_fastgd2thumbcreation)) {
      $this->jg_fastgd2thumbcreation = 1;
    }
    if (!isset($this->jg_impath)) {
      $this->jg_impath = '';
    }
    if (!isset($this->jg_resizetomaxwidth)) {
      $this->jg_resizetomaxwidth = 1;
    }
    if (!isset($this->jg_maxwidth) || ($this->jg_maxwidth == 0)) {
      $this->jg_maxwidth = 400;
    }
    if (!isset($this->jg_picturequality) || ($this->jg_picturequality == 0)) {
      $this->jg_picturequality = 100;
    }
    if (!isset($this->jg_useforresizedirection)) {
      $this->jg_useforresizedirection = 0;
    }
    if (!isset($this->jg_thumbwidth) || ($this->jg_thumbwidth == 0)) {
      $this->jg_thumbwidth = 133;
    }
    if (!isset($this->jg_thumbheight) || ($this->jg_thumbheight == 0)) {
      $this->jg_thumbheight = 100;
    }
    if (!isset($this->jg_thumbquality) || ($this->jg_thumbquality == 0)) {
      $this->jg_thumbquality = 75;
    }
    //Grundlegende Einstellungen->Backend-Upload
    if (!isset($this->jg_uploadorder)) {
      $this->jg_uploadorder = 0;
    }
    if (!isset($this->jg_useorigfilename)) {
      $this->jg_useorigfilename = 0;
    }
    if (!isset($this->jg_filenamenumber)) {
      $this->jg_filenamenumber = 0;
    }
    if (!isset($this->jg_delete_original)) {
      $this->jg_delete_original = 0;
    }
    if (!isset($this->jg_wrongvaluecolor) || ($this->jg_wrongvaluecolor == '')) {
      $this->jg_wrongvaluecolor = 'red';
    }
    //Grundlegende Einstellungen->Zusaetzliche Funktionen
    if (!isset($this->jg_combuild)) {
      $this->jg_combuild = 0;
    }
    if (!isset($this->jg_realname)) {
      $this->jg_realname = 0;
    }
    if (!isset($this->jg_bridge)) {
      $this->jg_bridge = 0;
    }
    if (!isset($this->jg_cooliris)) {
      $this->jg_cooliris = 0;
    }
    if (!isset($this->jg_coolirislink)) {
      $this->jg_coolirislink = 0;
    }
    //Benutzer-Rechte
    //Benutzer-Rechte->Benutzer-Upload ueber "Meine Galerie"
    if (!isset($this->jg_userspace)) {
      $this->jg_userspace = 0;
    }
    if (!isset($this->jg_approve)) {
      $this->jg_approve = 1;
    }
    if (!isset($this->jg_usercat)) {
      $this->jg_usercat = 0;
    }
    if (!isset($this->jg_maxusercat) || ($this->jg_maxusercat == 0)) {
      $this->jg_maxusercat = 10;
    }
    if (!isset($this->jg_userowncatsupload)) {
      $this->jg_userowncatsupload = 0;
    }
    if (!isset($this->jg_maxuserimage) || ($this->jg_maxuserimage == 0)) {
      $this->jg_maxuserimage = 400;
    }
    if (!isset($this->jg_maxfilesize) || ($this->jg_maxfilesize == 0)) {
      $this->jg_maxfilesize = 2000000;
    }
    if (!isset($this->jg_category)) {
      $this->jg_category = '';
    }
    if (!isset($this->jg_usercategory)) {
      $this->jg_usercategory = '';
    }
    if (!isset($this->jg_usercatacc)) {
      $this->jg_usercatacc = 0;
    }
    if (!isset($this->jg_maxuploadfields) || ($this->jg_maxuploadfields == 0)) {
      $this->jg_maxuploadfields = 3;
    }
    if (!isset($this->jg_useruploadnumber)) {
      $this->jg_useruploadnumber = 0;
    }
    if (!isset($this->jg_special_gif_upload)) {
      $this->jg_special_gif_upload = 0;
    }
    if (!isset($this->jg_delete_original_user)) {
      $this->jg_delete_original_user = 0;
    }
    if (!isset($this->jg_newpiccopyright)) {
      $this->jg_newpiccopyright = 0;
    }
    if (!isset($this->jg_newpicnote)) {
      $this->jg_newpicnote = 0;
    }
    //Benutzer-Rechte->Bewertungen
    if (!isset($this->jg_showrating)) {
      $this->jg_showrating = 0;
    }
    if (!isset($this->jg_maxvoting) || ($this->jg_maxvoting == 0)) {
      $this->jg_maxvoting = 5;
    }
    if (!isset($this->jg_onlyreguservotes)) {
      $this->jg_onlyreguservotes = 1;
    }
    //Benutzer-Rechte->Kommentare
    if (!isset($this->jg_showcomment)) {
      $this->jg_showcomment = 0;
    }
    if (!isset($this->jg_anoncomment)) {
      $this->jg_anoncomment = 0;
    }
    if (!isset($this->jg_namedanoncomment)) {
      $this->jg_namedanoncomment = 0;
    }
    if (!isset($this->jg_approvecom)) {
      $this->jg_approvecom = 2;
    }
    if (!isset($this->jg_secimages)) {
      $this->jg_secimages = 2;
    }
    if (!isset($this->jg_bbcodesupport)) {
      $this->jg_bbcodesupport = 0;
    }
    if (!isset($this->jg_smiliesupport)) {
      $this->jg_smiliesupport = 0;
    }
    if (!isset($this->jg_anismilie)) {
      $this->jg_anismilie = 0;
    }
    if (!isset($this->jg_smiliescolor) || ($this->jg_smiliescolor == '')) {
      $this->jg_smiliescolor = 'grey';
    }
  //Frontend Einstellungen
  //Frontend Einstellungen->Anordnung der Bilder
    if (!isset($this->jg_firstorder)) {
      $this->jg_firstorder = 'ordering ASC';
    }
    if (!isset($this->jg_secondorder)) {
      $this->jg_secondorder = 'imgdate DESC';
    }
    if (!isset($this->jg_thirdorder)) {
      $this->jg_thirdorder = 'imgtitle DESC';
    }
  //Frontend Einstellungen->Seitentitel
    if (!isset($this->jg_pagetitle_cat)) {
      $this->jg_pagetitle_cat = '[! JGS_CATEGORY!]: #cat';
    }
    if (!isset($this->jg_pagetitle_detail)) {
      $this->jg_pagetitle_detail = '[! JGS_CATEGORY!]: #cat - [! JGS_PICTURE!]:  #img';
    }
  //Frontend Einstellungen->Kopf- und Fussbereich
    if (!isset($this->jg_showgalleryhead)) {
      $this->jg_showgalleryhead = 1;
    }
    if (!isset($this->jg_showpathway)) {
      $this->jg_showpathway = 1;
    }
    if (!isset($this->jg_completebreadcrumbs)) {
      $this->jg_completebreadcrumbs = 1;
    }
    if (!isset($this->jg_search)) {
      $this->jg_search = 1;
    }
    if (!isset($this->jg_showallpics)) {
      $this->jg_showallpics = 0;
    }
    if (!isset($this->jg_showallhits)) {
      $this->jg_showallhits = 0;
    }
    if (!isset($this->jg_showbacklink)) {
      $this->jg_showbacklink = 0;
    }
    if (!isset($this->jg_suppresscredits)) {
      $this->jg_suppresscredits = 1;
    }
  //Frontend Einstellungen->Meine Galerie
    if (!isset($this->jg_showuserpanel)) {
      $this->jg_showuserpanel = 1;
    }
    if (!isset($this->jg_showallpicstoadmin)) {
      $this->jg_showallpicstoadmin = 0;
    }
    if (!isset($this->jg_showminithumbs)) {
      $this->jg_showminithumbs = 1;
    }
  //Frontend Einstellungen->PopUp-Funktionen
    if (!isset($this->jg_openjs_padding) || ($this->jg_openjs_padding == 0)) {
      $this->jg_openjs_padding = 10;
    }
    if (!isset($this->jg_openjs_background) || ($this->jg_openjs_background == '')) {
      $this->jg_openjs_background = '#ffffff';
    }
    if (!isset($this->jg_dhtml_border) || ($this->jg_dhtml_border == '')) {
      $this->jg_dhtml_border = '#808080';
    }
    if (!isset($this->jg_show_title_in_dhtml)) {
      $this->jg_show_title_in_dhtml = 0;
    }
    if (!isset($this->jg_show_description_in_dhtml)) {
      $this->jg_show_description_in_dhtml = 0;
    }
    if (!isset($this->jg_lightbox_speed) || ($this->jg_lightbox_speed == 0)) {
      $this->jg_lightbox_speed = 5;
    }
    if (!isset($this->jg_lightbox_slide_all)) {
      $this->jg_lightbox_slide_all = 0;
    }
    if (!isset($this->jg_resize_js_image)) {
      $this->jg_resize_js_image = 1;
    }
    if (!isset($this->jg_disable_rightclick_original)) {
      $this->jg_disable_rightclick_original = 1;
    }
  //Galerie-Ansicht
  //Galerie-Ansicht->Generelle Einstellungen
    if (!isset($this->jg_showgallerysubhead)) {
      $this->jg_showgallerysubhead = 1;
    }
    if (!isset($this->jg_showallcathead)) {
      $this->jg_showallcathead = 1;
    }
    if (!isset($this->jg_colcat)) {
      $this->jg_colcat = 1;
    }
    if (!isset($this->jg_catperpage) || ($this->jg_catperpage == 0)) {
      $this->jg_catperpage = 10;
    }
    if (!isset($this->jg_ordercatbyalpha)) {
      $this->jg_ordercatbyalpha = 0;
    }
    if (!isset($this->jg_showgallerypagenav)) {
      $this->jg_showgallerypagenav = 1;
    }
    if (!isset($this->jg_showcatcount)) {
      $this->jg_showcatcount = 1;
    }
    if (!isset($this->jg_showcatthumb)) {
      $this->jg_showcatthumb = 1;
    }
    if (!isset($this->jg_showrandomcatthumb)) {
      $this->jg_showrandomcatthumb = 2;
    }
    if (!isset($this->jg_ctalign)) {
      $this->jg_ctalign = 1;
    }
    if (!isset($this->jg_showtotalcathits)) {
      $this->jg_showtotalcathits = 1;
    }
    if (!isset($this->jg_showcatasnew)) {
      $this->jg_showcatasnew = 0;
    }
    if (!isset($this->jg_catdaysnew) || ($this->jg_catdaysnew == 0)) {
      $this->jg_catdaysnew = 7;
    }
    if (!isset($this->jg_rmsm)) {
      $this->jg_rmsm = 1;
    }
    if (!isset($this->jg_showrmsmcats)) {
      $this->jg_showrmsmcats = 0;
    }
    if (!isset($this->jg_showsubsingalleryview)) {
      $this->jg_showsubsingalleryview = 0;
    }
  //Kategorie-Ansicht
  //Kategorie-Ansicht->Generelle Einstellungen
    if (!isset($this->jg_showcathead)) {
      $this->jg_showcathead = 1;
    }
    if (!isset($this->jg_usercatorder)) {
      $this->jg_usercatorder = 0;
    }
    if (!isset($this->jg_usercatorderlist) || $this->jg_usercatorderlist == '') {
      $this->jg_usercatorderlist = 'date, title';
    }
    if (!isset($this->jg_showcatdescriptionincat)) {
      $this->jg_showcatdescriptionincat = 0;
    }
    if (!isset($this->jg_showpagenav)) {
      $this->jg_showpagenav = 1;
    }
    if (!isset($this->jg_showpiccount)) {
      $this->jg_showpiccount = 1;
    }
    if (!isset($this->jg_perpage) || ($this->jg_perpage == 0)) {
      $this->jg_perpage = 9;
    }
    if (!isset($this->jg_catthumbalign)) {
      $this->jg_catthumbalign = 1;
    }
    if (!isset($this->jg_colnumb) || ($this->jg_colnumb == 0)) {
      $this->jg_colnumb = 3;
    }
    if (!isset($this->jg_detailpic_open)) {
      $this->jg_detailpic_open = 0;
    }
    if (!isset($this->jg_lightboxbigpic)) {
      $this->jg_lightboxbigpic = 0;
    }
    if (!isset($this->jg_showtitle)) {
      $this->jg_showtitle = 1;
    }
    if (!isset($this->jg_showpicasnew)) {
      $this->jg_showpicasnew = 0;
    }
    if (!isset($this->jg_daysnew) || ($this->jg_daysnew == 0)) {
      $this->jg_daysnew = 7;
    }
    if (!isset($this->jg_showhits)) {
      $this->jg_showhits = 1;
    }
    if (!isset($this->jg_showauthor)) {
      $this->jg_showauthor = 1;
    }
    if (!isset($this->jg_showowner)) {
      $this->jg_showowner = 1;
    }
    if (!isset($this->jg_showcatcom)) {
      $this->jg_showcatcom = 1;
    }
    if (!isset($this->jg_showcatrate)) {
      $this->jg_showcatrate = 1;
    }
    if (!isset($this->jg_showcatdescription)) {
      $this->jg_showcatdescription = 1;
    }
    if (!isset($this->jg_showcategorydownload)) {
      $this->jg_showcategorydownload = 0;
    }
    if (!isset($this->jg_showcategoryfavourite)) {
      $this->jg_showcategoryfavourite = 0;
    }
  //Kategorie-Ansicht->Unterkategorien
    if (!isset($this->jg_showsubcathead)) {
      $this->jg_showsubcathead = 1;
    }
    if (!isset($this->jg_showsubcatcount)) {
      $this->jg_showsubcatcount = 1;
    }
    if (!isset($this->jg_colsubcat)) {
      $this->jg_colsubcat = 3;
    }
    if (!isset($this->jg_subperpage) || $this->jg_subperpage == 0) {
      $this->jg_subperpage = 2;
    }
    if (!isset($this->jg_showpagenavsubs)) {
      $this->jg_showpagenavsubs = 1;
    }
    if (!isset($this->jg_subcatthumbalign)) {
      $this->jg_subcatthumbalign = 1;
    }
    if (!isset($this->jg_showsubthumbs)) {
      $this->jg_showsubthumbs = 2;
    }
    if (!isset($this->jg_showrandomsubthumb)) {
      $this->jg_showrandomsubthumb = 3;
    }
    if (!isset($this->jg_ordersubcatbyalpha)) {
      $this->jg_ordersubcatbyalpha = 0;
    }
    if (!isset($this->jg_showtotalsubcathits)) {
      $this->jg_showtotalsubcathits = 1;
    }
  //Detail-Ansicht
  //Detail-Ansicht->Generelle Einstellungen
    if (!isset($this->jg_showdetailpage)) {
      $this->jg_showdetailpage = 1;
    }
    if (!isset($this->jg_showdetailnumberofpics)) {
      $this->jg_showdetailnumberofpics = 1;
    }
    if (!isset($this->jg_cursor_navigation)) {
      $this->jg_cursor_navigation = 1;
    }
    if (!isset($this->jg_disable_rightclick_detail)) {
      $this->jg_disable_rightclick_detail = 1;
    }
    if (!isset($this->jg_showdetailtitle)) {
      $this->jg_showdetailtitle = 1;
    }
    if (!isset($this->jg_showdetail)) {
      $this->jg_showdetail = 1;
    }
    if (!isset($this->jg_showdetailaccordion)) {
      $this->jg_showdetailaccordion = 0;
    }
    if (!isset($this->jg_showdetaildescription)) {
      $this->jg_showdetaildescription = 1;
    }
    if (!isset($this->jg_showdetaildatum)) {
      $this->jg_showdetaildatum = 1;
    }
    if (!isset($this->jg_showdetailhits)) {
      $this->jg_showdetailhits = 1;
    }
    if (!isset($this->jg_showdetailrating)) {
      $this->jg_showdetailrating = 1;
    }
    if (!isset($this->jg_showdetailfilesize)) {
      $this->jg_showdetailfilesize = 1;
    }
    if (!isset($this->jg_showdetailauthor)) {
      $this->jg_showdetailauthor = 1;
    }
    if (!isset($this->jg_showoriginalfilesize)) {
      $this->jg_showoriginalfilesize = 1;
    }
    if (!isset($this->jg_showdetaildownload)) {
      $this->jg_showdetaildownload = 1;
    }
    if (!isset($this->jg_downloadfile)) {
      $this->jg_downloadfile = 1;
    }
    if (!isset($this->jg_downloadwithwatermark)) {
      $this->jg_downloadwithwatermark = 1;
    }
    if (!isset($this->jg_watermark)) {
      $this->jg_watermark = 1;
    }
    if (!isset($this->jg_watermarkpos) || ($this->jg_watermarkpos == 0)) {
      $this->jg_watermarkpos = 9;
    }
    if (!isset($this->jg_bigpic)) {
      $this->jg_bigpic = 1;
    }
    if (!isset($this->jg_bigpic_open)) {
      $this->jg_bigpic_open = 0;
    }
    if (!isset($this->jg_bbcodelink)) {
      $this->jg_bbcodelink = 3;
    }
    if (!isset($this->jg_showcommentsunreg)) {
      $this->jg_showcommentsunreg = 1;
    }
    if (!isset($this->jg_showcommentsarea)) {
      $this->jg_showcommentsarea = 3;
    }
    if (!isset($this->jg_send2friend)) {
      $this->jg_send2friend = 1;
    }
  //Detail-Ansicht->Motiongallery
    if (!isset($this->jg_minis)) {
      $this->jg_minis = 1;
    }
    if (!isset($this->jg_motionminis)) {
      $this->jg_motionminis = 1;
    }
    if (!isset($this->jg_motionminiWidth) || ($this->jg_motionminiWidth == 0)) {
      $this->jg_motionminiWidth = 400;
    }
    if (!isset($this->jg_motionminiHeight) || ($this->jg_motionminiHeight == 0)) {
      $this->jg_motionminiHeight = 50;
    }
    if (!isset($this->jg_miniWidth) || ($this->jg_miniWidth == 0)) {
      $this->jg_miniWidth = 28;
    }
    if (!isset($this->jg_miniHeight) || ($this->jg_miniHeight == 0)) {
      $this->jg_miniHeight = 28;
    }
    if (!isset($this->jg_minisprop)) {
      $this->jg_minisprop = 0;
    }
  //Detail-Ansicht->Namensschilder
    if (!isset($this->jg_nameshields)) {
      $this->jg_nameshields = 0;
    }
    if (!isset($this->jg_nameshields_unreg)) {
      $this->jg_nameshields_unreg = 0;
    }
    if (!isset($this->jg_show_nameshields_unreg)) {
      $this->jg_show_nameshields_unreg = 0;
    }
    if (!isset($this->jg_nameshields_height)) {
      $this->jg_nameshields_height = 12;
    }
    if (!isset($this->jg_nameshields_width)) {
      $this->jg_nameshields_width = 8;
    }
  //Detail-Ansicht->Slideshow
    if (!isset($this->jg_slideshow)) {
      $this->jg_slideshow = 1;
    }
    if (!isset($this->jg_slideshow_timer) || ($this->jg_slideshow_timer == 0)) {
      $this->jg_slideshow_timer = 5;
    }
    if (!isset($this->jg_slideshow_usefilter)) {
      $this->jg_slideshow_usefilter = 1;
    }
    if (!isset($this->jg_slideshow_filterbychance)) {
      $this->jg_slideshow_filterbychance = 1;
    }
    if (!isset($this->jg_slideshow_filtertimer) || ($this->jg_slideshow_filtertimer == 0)) {
      $this->jg_slideshow_filtertimer = 3;
    }
    if (!isset($this->jg_showsliderepeater)) {
      $this->jg_showsliderepeater = 1;
    }
  //Detail-Ansicht->Exif-Daten
    if (!isset($this->jg_showexifdata)) {
      $this->jg_showexifdata = 0;
    }
    //For all array based variables, e.g. EXIF
    //1. when there are changes in one or more of the tags, $jg_*tags will be an array and filled with the $_POST content
    //   $jg_*tags2 contains the actualized activated tags in a string formerly read and changed to string
    //   from $jg_*tags
    //   so use the content of $jg_*tags2 for writing in config file
    //
    //2. when there are now changes in the data $jg_*tags2 will be empty
    //   and $jg_*tags is an string including the formerly read config not an array
    //   so use the content of $jg_*tags for writing in config file
    if (!isset($this->jg_subifdtags)) {
      $this->jg_subifdtags = '';
    }
    if (!isset($this->jg_ifdotags)) {
      $this->jg_ifdotags = '';
    }
    if (!isset($this->jg_gpstags)) {
      $this->jg_gpstags = '';
    }
    //Detail-Ansicht->IPTC-Daten
    if (!isset($this->jg_showiptcdata)) {
      $this->jg_showiptcdata = 0;
    }
    if (!isset($this->jg_iptctags)) {
      $this->jg_iptctags = '';
    }
  //Toplisten
  //Toplisten->Generelle Einstellungen
    if (!isset($this->jg_showtoplist)) {
      $this->jg_showtoplist = 1;
    }
    if (!isset($this->jg_toplist) || ($this->jg_toplist == 0)) {
      $this->jg_toplist = 10;
    }
    if (!isset($this->jg_topthumbalign)) {
      $this->jg_topthumbalign = 1;
    }
    if (!isset($this->jg_toptextalign)) {
      $this->jg_toptextalign = 1;
    }
    if (!isset($this->jg_toplistcols) || ($this->jg_toplistcols == 0)) {
      $this->jg_toplistcols = 3;
    }
    if (!isset($this->jg_whereshowtoplist)) {
      $this->jg_whereshowtoplist = 0;
    }
    if (!isset($this->jg_showrate)) {
      $this->jg_showrate = 1;
    }
    if (!isset($this->jg_showlatest)) {
      $this->jg_showlatest = 1;
    }
    if (!isset($this->jg_showcom)) {
      $this->jg_showcom = 1;
    }
    if (!isset($this->jg_showthiscomment)) {
      $this->jg_showthiscomment = 1;
    }
    if (!isset($this->jg_showmostviewed)) {
      $this->jg_showmostviewed = 1;
    }
  //Favourites
  //Favouriten->Generelle Einstellungen
    if (!isset($this->jg_favourites)) {
      $this->jg_favourites = 0;
    }
    if (!isset($this->jg_showdetailfavourite)) {
      $this->jg_showdetailfavourite = 0;
    }
    if (!isset($this->jg_favouritesshownotauth)) {
      $this->jg_favouritesshownotauth = 0;
    }
    if (!isset($this->jg_maxfavourites)) {
      $this->jg_maxfavourites = 30;
    }
    if (!isset($this->jg_zipdownload)) {
      $this->jg_zipdownload = 1;
    }
    if (!isset($this->jg_usefavouritesforpubliczip)) {
      $this->jg_usefavouritesforpubliczip = 1;
    }
    if (!isset($this->jg_usefavouritesforzip)) {
      $this->jg_usefavouritesforzip = 0;
    }

    //save in database and redirect
    $this->Joom_SaveConfig($this);
    $mainframe->redirect('index.php?option='. _JOOM_OPTION .'&act=configuration', JText::_('JGA_SETTINGS_SAVED'));
  }

  /**
   * Save configuration in database
   *
   * @param object $config
   * @param int $id
   * @return bool  true=successful insert/update of config
   */
  function Joom_SaveConfig($config, $id = 0) {
    $database = & JFactory::getDBO();
    //switch(1) {
      //case 1:
      //if 0 go to update query
      if($id == 0) {
        $config->id = 0;
      } else {
        //if not 0 check whether config row already exists
        $query = 'SELECT id FROM #__joomgallery_config
            WHERE id = '.$id;
        $database->setQuery($query);

        //if so go to update query, if not insert new row
        if(!$database->loadResult()) {
          $query = 'INSERT INTO #__joomgallery_config
                    VALUES (\''.$id.'\'';
          foreach($config as $key => $value) {
            $query .= ', \''.$value.'\'';
          }
          $query .= ')';
          $database->setQuery($query);
          if($database->query()){
            return true;
          }else{
            return false;
          }
        }
      }
      //update query
      $query = 'UPDATE #__joomgallery_config SET ';
      foreach($config as $key => $value) {
        $query .= $key.' = \''.$value.'\', ';
      }
      $query = trim(trim($query),',');
      $query .= ' WHERE id = '.$id;
      $database->setQuery($query);
      if($database->query()){
        return true;
      }else{
        return false;
      }
      //break;
      /*case 2:
        $config_string = '';
        foreach($config as $key => $value) {
          $config_string .= $key.'='.$value."\n";
        }
        $config_string = trim($config_string,"\n");
        $query = 'UPDATE #__components SET params = \''.$config_string.'\' WHERE admin_menu_link = \'option='._JOOM_OPTION.'\'';
        $database->setQuery($query);
        $database->query();
      break;
    }*/
  }

  /**
   * Save joom_settings.css according to settings in configuration
   *
   */
  function Joom_SaveCSS() {
    //common settings

    //calculation of colum widths
    //gallery view
    $colwidth_gal = floor( 99 / $this->jg_colcat);
    //category view
    $colwidth_cat = floor( 99 / $this->jg_colnumb );
    //subcategory view
    $colwidth_subcat = floor( 99 / $this->jg_colsubcat );

    //Alignment of container for text and picture
    //if ct_align=0, alternating alignments
    //jg_element_gal
    $jg_gal_container = "";
    //jg_photo_container
    $jg_gal_elemimg = "";
    //-> jg_element_txt
    $jg_gal_elemtxt = "";   //-> jg_element_txt
    $jg_gal_elemtxt_subs = "";

    //gallery view
    //alignment on one columned view not with float, instead text-align
    switch ($this->jg_ctalign) {
      case 1:
        //left aligned
        //one column -> text-align
        if ($this->jg_colcat == 1){
          $jg_gal_container = "  text-align:left !important;\n";
          $jg_gal_elemtxt = "  text-align:left !important; \n";
          $jg_gal_elemtxt_subs = "  text-align:left !important; \n";
        } else {
          $jg_gal_container = "  float:left;\n";
          $jg_gal_elemtxt = "  float:left;\n";
          $jg_gal_elemtxt_subs = "  float:left;\n";
        }
        break;
      case 2:
        //right aligned
        //one column -> text-align
        if ($this->jg_colcat == 1 || $this->jg_catperpage == 1){
          $jg_gal_container = "  text-align:right !important;\n";
        } else {
          $jg_gal_container = "  float:right;\n";
        }
        $jg_gal_elemtxt = "  text-align:right !important;\n";
        $jg_gal_elemtxt_subs =  "  float:right;\n  text-align:right !important;";
        break;
      case 3:
        //centered
        if ($this->jg_colcat == 1 || $this->jg_catperpage == 1){
          $jg_gal_container = "  text-align:center;\n";
        } else {
          $jg_gal_container = "  float:left;\n";
        }
        $jg_gal_elemtxt      = "  text-align:center !important;\n";
        $jg_gal_elemtxt_subs = "  text-align:center !important;\n";
        break;

      default:
        //=0 alternating, classes with *_r implied right placement
        //in joomgallery.css
        $jg_gal_container    = "  float:left;\n";
        $jg_gal_elemtxt      = "  text-align:left !important;\n";
        $jg_gal_elemtxt_subs = "  text-align:left !important;\n";
        break;
    }

    //if so alignment of thumb
    if ($this->jg_showcatthumb == 1 ) {
      switch ($this->jg_ctalign) {
        case 1:
          //left aligned
          //only one column -> text-align
          if ($this->jg_colcat == 1 || $this->jg_catperpage == 1){
            $jg_gal_elemimg = "  text-align:left !important;\n";
          } else {
            $jg_gal_elemimg = "  float:left;\n";
          }
          break;
        case 2:
          //right aligned
          //one column -> text-align
          if ($this->jg_colcat == 1 || $this->jg_catperpage == 1){
            $jg_gal_elemimg = "  text-align:right !important;\n";
          } else {
            $jg_gal_elemimg = "  float:right;\n";
          }
          break;
        case 3:
          //centered
          $jg_gal_elemimg = "  text-align:center !important;\n";
          break;
        default:
          //alternating
          $jg_gal_elemimg = "  float:left;\n";
          break;
      }
    }

    //category view
    switch ($this->jg_catthumbalign) {
      case 1:
        //left aligned
        $cat_container="  float:left;";
        $cat_photo="  float:left;";
        $cat_txt="  text-align:left !important;";
        break;
      case 2:
        //right aligned
        $cat_container="  float:right;\n  text-align:right !important;";
        $cat_photo="  text-align:right !important;";
        $cat_txt="  text-align:right !important;";
        break;
      case 3:
        //centered
        if ($this->jg_colnumb == 1) {
          $cat_container="  text-align:center !important;";
          $cat_photo="  text-align:center !important;";
          $cat_txt="  display:block;\n  text-align:center !important;";
        } else {
          $cat_container="  float:left;\n  text-align:center !important;";
          $cat_photo="  text-align:center !important;";
          $cat_txt="  text-align:center !important;";
        }
        break;
    }

    //subcategory view
    switch ($this->jg_subcatthumbalign) {
      case 1:
        //left aligned
        if ($this->jg_colsubcat == 1) {
          $subcat_container="  text-align:left !important;";
          $subcat_photo="  float:left;";
          $subcat_txt="  text-align:left !important;";
        } else {
          $subcat_container="  float:left;";
          $subcat_photo="  float:left;";
          $subcat_txt="  text-align:left !important;";
        }
        break;
      case 2:
        //right aligned
        if ($this->jg_colsubcat == 1) {
          $subcat_container="  text-align:right !important;";
          $subcat_photo="  float:right;";
          $subcat_txt="  text-align:right !important;";
        } else {
          $subcat_container="  float:right;\n  text-align:right !important;";
          $subcat_photo="  float:right;";
          $subcat_txt="  text-align:right !important;";
        }
        break;
      case 3:
        //centered
        if ($this->jg_colsubcat == 1) {
          $subcat_container="  text-align:center !important;";
          $subcat_photo="  text-align:center !important;";
          $subcat_txt="  display:block;\n  text-align:center !important;";
        } else {
          $subcat_container="  float:left;\n  text-align:center !important;";
          $subcat_photo="  text-align:center !important;\n";
          $subcat_txt="  clear:both;\n  text-align:center !important;";
        }
        break;
    }

    //toplist view
    $colwidth_top = floor ( 99 / $this->jg_toplistcols );

    //only if activated
    if ($this->jg_showtoplist != 0) {
      switch ($this->jg_topthumbalign) {
        case 1:
          //picture left aligned
          if ($this->jg_toplistcols == 1) {
            $top_container="";
            $top_photo="  width:49%;\n  float:left;";

            switch ($this->jg_toptextalign) {
              //alignment of text
              case 1:
                //left aligned
                $top_txt="  text-align:left !important;";
                break;
              case 2:
                //right aligned
                $top_txt="  text-align: right !important;";
                break;
              case 3:
                //centered
                $top_txt="  text-align: center !important;";
                break;
            }
            $top_txt .= "\n  width:49%;\n  float:left;";
          } else {
            $top_container="  float:left;\n  height:100%;";
            $top_photo="";
            $top_txt="  text-align:left !important;";
          }
          break;

        case 2:
          //picture right aligned
          if ($this->jg_toplistcols == 1) {
            $top_container="";
            $top_photo="  width:49%;\n  float:left;\n  text-align:right !important;";

            switch ($this->jg_toptextalign) {
              //alignment of text
              case 1:
                //left aligned
                $top_txt="  text-align:left !important;";
                break;
              case 2:
                //right aligned
                $top_txt="  text-align: right !important;";
                break;
              case 3:
                //centered
                $top_txt="  text-align: center !important;";
                break;
            }
            $top_txt .= "\n  width:49%;\n  float:left;";
          } else {
            $top_container="  float:left;\n  height:100%;\n  text-align:right !important;";
            $top_photo="";
            $top_txt="  text-align: right !important;";
          }
          break;

        case 3:
          //picture centerd
          if ($this->jg_toplistcols == 1) {
            $top_container="";
            $top_photo="  width:49%;\n  float:left;\n  text-align:center;";

            switch ($this->jg_toptextalign) {
              //alignment of text
              case 1:
                //left aligned
                $top_txt="  text-align:left !important;";
                break;
              case 2:
                //right aligned
                $top_txt="  text-align: right !important;";
                break;
              case 3:
                //centered
                $top_txt="  text-align: center !important;";
                break;
            }
            $top_txt .= "\n  width:49%;\n  float:left;";
          } else {
            $top_container="  float:left;\n  height:100%;\n  text-align:center !important;";
            $top_photo="";
            $top_txt="  text-align: center !important;";
          }
          break;
      }
    }

    //detail view
    if ($this->jg_minis != 0 && $this->jg_minisprop == 2 ) {
      $minidimensions  = "height:".$this->jg_miniHeight."px";
    } else if ($this->jg_minisprop == 1 ) {
      $minidimensions  = "width:".$this->jg_miniWidth."px";
    } else {
      $minidimensions  = "width:".$this->jg_miniWidth."px;\n";
      $minidimensions .= "height:".$this->jg_miniHeight."px;\n";
    }

    //Composing and output of CSS

    $css_settings = "
/* Joomgallery CSS
CSS Styles generated by settings in the Joomgallery backend.
DO NOT EDIT - this file will be overwritten every time the config is saved.
Adjust your styles in joom_local.css instead.

CSS Styles, die ueber die Speicherung der Konfiguration im Backend erzeugt werden.
BITTE NICHT VERAENDERN - diese Datei wird  mit dem naechsten Speichern ueberschrieben.
Bitte nehmen Sie Aenderungen in der Datei joom_local.css in diesem
Verzeichnis vor. Sie koennen sie neu erstellen oder die schon vorhandene
joom_local.css.README umbenennen und anpassen
*/\n\n";

    //gallery view
    $css_settings .= "/* Gallery view */\n";

    //container with eventually picture and categorytext
    $css_settings .= ".jg_element_gal, .jg_element_gal_r {\n";
    $css_settings .= $jg_gal_container;
    $css_settings .= "  width:".$colwidth_gal."%;\n";
    $css_settings .= "}\n";

    //text
    $css_settings .= ".jg_element_txt {\n";
    $css_settings .= $jg_gal_elemtxt;
    $css_settings .= "}\n";

    //text subcategories
    $css_settings .= ".jg_element_txt_subs {\n";
    $css_settings .= $jg_gal_elemtxt_subs;
    $css_settings .= "  font-size: 0.9em;\n";
    $css_settings .= "}\n";

    //picture if activated
    if ($this->jg_showcatthumb == 1 && !empty($jg_gal_elemimg)) {
      $css_settings .= ".jg_photo_container {\n";
      $css_settings .= $jg_gal_elemimg;
      $css_settings .= "}\n";
    }

    //category view
    $css_settings .= "\n/* Category view */\n";
    $css_settings .= ".jg_element_cat {\n";
    $css_settings .= "  width:".$colwidth_cat."%;\n";
    $css_settings .= $cat_container."\n";
    $css_settings .= "}\n";
    $css_settings .= ".jg_catelem_cat a{\n";
    $css_settings .= "  height:".$this->jg_thumbheight."px;\n";
    $css_settings .= "}\n";
    $css_settings .= ".jg_catelem_photo {\n";
    $css_settings .= $cat_photo."\n";
    $css_settings .= "}\n";
    $css_settings .= ".jg_catelem_txt {\n";
    $css_settings .= $cat_txt."\n";
    $css_settings .= "}\n";

    //subcategory view
    $css_settings .= "\n/* Subcategory view */\n";
    $css_settings .= ".jg_subcatelem_cat {\n";
    $css_settings .= "  width:".$colwidth_subcat."%;\n";
    $css_settings .= $subcat_container."\n";
    $css_settings .= "}\n";
    $css_settings .= ".jg_subcatelem_cat a{\n";
    $css_settings .= "  height:".$this->jg_thumbheight."px;\n";
    $css_settings .= "}\n";
    $css_settings .= ".jg_subcatelem_photo {\n";
    $css_settings .= $subcat_photo."\n";
    $css_settings .= "}\n";
    $css_settings .= ".jg_subcatelem_txt {\n";
    $css_settings .= $subcat_txt."\n";
    $css_settings .= "}\n";

    //detail view
    $css_settings .= "\n/* Detail view */\n";
    //motiongallery only if activated
    if ($this->jg_minis != 0) {
      $css_settings .= ".jg_minipic {\n";
      $css_settings .= "  ".$minidimensions.";\n";
      $css_settings .= "}\n";

      $css_settings .= "#motioncontainer {\n";
      $css_settings .= "  width:".$this->jg_motionminiWidth."px; /* Set to gallery width, in px or percentage */\n";
      $css_settings .= "  height:".$this->jg_motionminiHeight."px;/* Set to gallery height */\n";
      $css_settings .= "}\n";
    }

    //name tags only if activated
    if ($this->jg_nameshields != 0) {
      $css_settings .=".nameshield {\n";
      $css_settings .="  line-height:".$this->jg_nameshields_height."px;\n";
      $css_settings .="}\n";
    }

    //toplist view (special)
    $css_settings .= "\n/* Special view - Toplists*/\n";
    $css_settings .= ".jg_topelement, .jg_favelement {\n";
    $css_settings .= "  width:".$colwidth_top."%;\n";
    $css_settings .= "  height:auto;\n";
    $css_settings .= $top_container."\n";
    $css_settings .= "}\n";

    $css_settings .= ".jg_topelem_photo, .jg_favelem_photo {\n";
    $css_settings .= $top_photo."\n";
    $css_settings .= "}\n";

    $css_settings .= ".jg_topelem_txt, .jg_favelem_txt {\n";
    $css_settings .= $top_txt."\n";
    $css_settings .= "}\n";

    $css_settings_file = JPATH_COMPONENT_SITE.DS.'assets'.DS.'css'.DS.'joom_settings.css';
    if(JFile::write($css_settings_file,$css_settings)) {
    } else {
      return false;
    }
    return true;
  }

  /**
   * Build configuration or Exif data
   *
   */
  function Joom_BuildExifConfig () {
    $config = Joom_getConfig();

    require_once(JPATH_COMPONENT.DS.'adminexif'.DS.'admin.exifarray.php');

    $ifdotags   = explode (',', $config->jg_ifdotags);
    $subifdtags = explode (',', $config->jg_subifdtags);
    $gpstags    = explode (',', $config->jg_gpstags);

    $definitions = array(
      1 => array ('TAG' => "IFD0", 'JG' => $ifdotags, 'NAME' => "jg_ifdotags[]", 'HEAD' => JText::_('JGSE_IFD0TAGS')),
      2 => array ('TAG' => "EXIF", 'JG' => $subifdtags, 'NAME' => "jg_subifdtags[]", 'HEAD' => JText::_('JGSE_SUBIFDTAGS')),
      3 => array ('TAG' => "GPS",  'JG' => $gpstags,  'NAME' => "jg_gpstags[]",  'HEAD' => JText::_('JGSE_GPSTAGS'))
    );
    $count  =count($definitions);
    $output = '';

    for($ii=1; $ii <= $count; $ii++) {
      $tags     = count($exif_config_array[$definitions[$ii]['TAG']]);
      $jgtags   = $definitions[$ii]['JG'];
      $tagname  = $definitions[$ii]['NAME'];
      $header   = $definitions[$ii]['HEAD'];

      $output .= "    <tr>\n";
//       $output .= "      <th>\n";
//       $output .= "        <input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$tags.")\" />\n";
//       $output .= "      </th>\n";
      $output .= "      <th colspan=\"5\" width=\"100%\" align=\"center\" class=\"title\">\n";
      $output .= "        ".$header."\n";
      $output .= "      </th>\n";
      $output .= "    </tr>\n";
      $output .= "    <tr>\n";
      $output .= "      <th>\n";
      $output .= "        &nbsp;\n";
      $output .= "      </th>\n";
      $output .= "      <th nowrap=\"nowrap\">\n";
      $output .= "        ".JText::_('JGSE_TAGNR')."\n";
      $output .= "      </th>\n";
      $output .= "      <th>\n";
      $output .= "        ".JText::_('JGSE_TAGNAME')."\n";
      $output .= "      </th>\n";
      $output .= "      <th nowrap=\"nowrap\">\n";
      $output .= "        ".JText::_('JGSE_TAG')."\n";
      $output .= "      </th>\n";
      $output .= "      <th>\n";
      $output .= "        ".JText::_('JGSE_TAGDESCRIPTION')."\n";
      $output .= "      </th>\n";
      $output .= "    </tr>\n";

      $i=1;

      foreach($exif_config_array[$definitions[$ii]['TAG']] as $key => $value) {

        if ((in_array($key, $jgtags)) && $jgtags[0] !='') {
          $checked = 'checked="checked"';
        } else {
          $checked = "";
        }

        $output .= "    <tr>\n";
        $output .= "      <td>\n";
        $output .= "        <input type=\"checkbox\" id=\"cb".$i."\" name=\"".$tagname."\" value=\"".$key."\" onclick=\"isChecked(this.checked);\" ".$checked." />\n";
        $output .= "      </td>\n";
        $output .= "      <td nowrap=\"nowrap\">\n";
        $output .= "        ".$key."\n";
        $output .= "      </td>\n";
        $output .= "      <td width=\"30%\">\n";
        $output .= "        ".$value['Name']."\n";
        $output .= "      </td>\n";
        $output .= "      <td width=\"20%\">\n";
        $output .= "        ".$value['Attribute']."\n";
        $output .= "      </td>\n";
        $output .= "      <td width=\"50%\">\n";
        $output .= "        ".$value['Description']."\n";
        $output .= "      </td>\n";
        $output .= "    </tr>\n";
        $i++;
      }

      $output .= "    <tr>\n";
      $output .= "      <th colspan=\"5\">\n";
      $output .= "        &nbsp;\n";
      $output .= "      </th>\n";
      $output .= "    </tr>\n";
    }
    echo $output;
  }

  /**
   * Build configuration for IPTC data
   *
   */
  function Joom_BuildIptcConfig () {
    $config = Joom_getConfig();

    require_once(JPATH_COMPONENT.DS.'adminiptc'.DS.'admin.iptcarray.php');

    $iptctags   = explode (',', $config->jg_iptctags);
    $definitions = array(
    1 => array ('TAG' => "IPTC", 'JG' => $iptctags, 'NAME' => "jg_iptctags[]", 'HEAD' => JText::_('JGSI_IPTCTAGS')),
    );
    $count=count($definitions);
    $output  = '';
    for($ii=1; $ii <= $count; $ii++) {
      $tags     = count($iptc_config_array[$definitions[$ii]['TAG']]);
      $jgtags   = $definitions[$ii]['JG'];
      $tagname  = $definitions[$ii]['NAME'];
      $header   = $definitions[$ii]['HEAD'];
      $output .= "    <tr>\n";
//       $output .= "      <th>\n";
//       $output .= "        <input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$tags.")\" />\n";
//       $output .= "      </th>\n";
      $output .= "      <th colspan=\"5\" width=\"100%\" align=\"center\" class=\"title\">\n";
      $output .= "        ".$header."\n";
      $output .= "      </th>\n";
      $output .= "    </tr>\n";
      $output .= "    <tr>\n";
      $output .= "      <th>\n";
      $output .= "        &nbsp;\n";
      $output .= "      </th>\n";
      $output .= "      <th nowrap=\"nowrap\">\n";
      $output .= "        ".JText::_('JGSI_TAGNR')."\n";
      $output .= "      </th>\n";
      $output .= "      <th>\n";
      $output .= "        ".JText::_('JGSI_TAGNAME')."\n";
      $output .= "      </th>\n";
      $output .= "      <th nowrap=\"nowrap\">\n";
      $output .= "        ".JText::_('JGSI_TAG')."\n";
      $output .= "      </th>\n";
      $output .= "      <th>\n";
      $output .= "        ".JText::_('JGSI_TAGDESCRIPTION')."\n";
      $output .= "      </th>\n";
      $output .= "    </tr>\n";
      $j=1;
      foreach($iptc_config_array[$definitions[$ii]['TAG']] as $key => $value) {
        if ((in_array($key, $jgtags)) && $jgtags[0] !='') {
          $checked = ' checked="checked"';
        } else {
          $checked = "";
        }
        $output .= "    <tr>\n";
        $output .= "      <td>\n";
        $output .= "        <input type=\"checkbox\" id=\"cb".$j."\" name=\"".$tagname."\" value=\"".$key."\" onclick=\"isChecked(this.checked);\"".$checked." />\n";
        $output .= "      </td>\n";
        $output .= "      <td nowrap=\"nowrap\">\n";
        $output .= "        ".$value['IMM']."\n";
        $output .= "      </td>\n";
        $output .= "      <td width=\"20%\">\n";
        $output .= "        ".$value['Name']."\n";
        $output .= "      </td>\n";
        $output .= "      <td width=\"20%\">\n";
        $output .= "        ".$value['Attribute']."\n";
        $output .= "      </td>\n";
        $output .= "      <td width=\"60%\">\n";
        $output .= "        ".$value['Description']."\n";
        $output .= "      </td>\n";
        $output .= "    </tr>\n";
        $j++;
      }
      $output .= "    <tr>\n";
      $output .= "      <th colspan=\"5\">\n";
      $output .= "        &nbsp;\n";
      $output .= "      </th>\n";
      $output .= "    </tr>\n";
    }
    echo $output;
  }

  /**
   * Check some settings and build the output for showing
   * configuration manager with Joom_ShowConfig_HTML
   *
   */
  function Joom_ShowConfig() {
    $config = Joom_getConfig();

    //load language files from frontend for exif and iptc data
    $language = & JFactory::getLanguage();
    $language->load(_JOOM_OPTION.'.exif',JPATH_SITE);
    $language->load(_JOOM_OPTION.'.iptc',JPATH_SITE);

    //check the existence of component-xml from com_easycaptcha
    $xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easycaptcha'.DS.'com_easycaptcha.xml';
    if (is_file($xmlfile)) {
      $easycaptchamsg = '<div style="color:#080;">[' . JText::_('JGA_EASYCAPTCHA_INSTALLED') . ']</div>';
    } else {
      $easycaptchamsg = '<div style="color:#f00;font-weight:bold">[' . JText::_('JGA_EASYCAPTCHA_NOT_INSTALLED') . ']</div>';
    }

    //check the installation of GD
    $gdver = Joom_GDVersion();
    // Returns version, 0 if not installed, or -1 if appears to be installed but
    // not verified
    if ($gdver > 0) {
      $gdmsg = JText::_('JGA_GD_INSTALLED_PARTONE') .  $gdver  . JText::_('JGA_GD_INSTALLED_PARTTWO');
    } elseif ($gdver == -1) {
      $gdmsg = JText::_('JGA_GD_NO_VERSION');
    } else {
      $gdmsg = JText::_('JGA_GD_NOT_INSTALLED') .
               '<a href="http://www.php.net/gd" target="_blank">http://www.php.net/gd</a>'
               . JText::_('JGA_GD_MORE_INFO');
    }

    //check the installation of ImageMagick
    $imver = Joom_IMVersion();
    //Returns version, 0 if not installed or path not properly configured
    if ($imver != "0") {
      $immsg = JText::_('JGA_IM_INSTALLED') .  $imver;
    } else {
      $immsg = JText::_('JGA_IM_NOT_INSTALLED');
    }

    //check the installation of Exif
    $exifmsg = "";
    if (!extension_loaded('exif')) {
      $exifmsg    = '<div style="color:#f00;font-weight:bold; text-align:center;">[' . JText::_('JGA_EXIF_NOT_INSTALLED') . ' ' . JText::_('JGA_EXIF_NO_OPTIONS') . ']</div>';
    } else {
      $exifmsg    = '<div style="color:#080; text-align:center;">[' . JText::_('JGA_EXIF_INSTALLED') . ']</div>';
      if (!function_exists('exif_read_data')) {
        $exifmsg = '<div style="color:#f00;font-weight:bold; text-align:center;">[' . JText::_('JGA_EXIF_INSTALLED_BUT') . ' ' . JText::_('JGA_EXIF_NO_OPTIONS') . ']</div>';
      }
    }

    //check pathes and watermark file
    $writeable   = '<span style="color:#080;">'
      . JText::_('JGA_DIRECTORY_WRITEABLE') .
      '</span>';
    $unwriteable = '<span style="color:#f00;">'
      . JText::_('JGA_DIRECTORY_UNWRITEABLE') .
      '</span>';

    if (Joom_CheckWriteable(JPATH_ROOT.DS,JPath::clean($config->jg_pathimages))) {
      $write_pathimages = $writeable;
    } else {
      $write_pathimages = $unwriteable;
    }
    if (Joom_CheckWriteable(JPATH_ROOT.DS,JPath::clean($config->jg_pathoriginalimages))) {
      $write_pathoriginalimages = $writeable;
    } else {
      $write_pathoriginalimages = $unwriteable;
    }
    if (Joom_CheckWriteable(JPATH_ROOT.DS,JPath::clean($config->jg_paththumbs))) {
      $write_paththumbs = $writeable;
    } else {
      $write_paththumbs = $unwriteable;
    }
    if (Joom_CheckWriteable(JPATH_ROOT.DS,JPath::clean($config->jg_pathftpupload))) {
      $write_pathftpupload = $writeable;
    } else {
      $write_pathftpupload = $unwriteable;
    }
    if (Joom_CheckWriteable(JPATH_ROOT.DS,JPath::clean($config->jg_pathtemp))) {
      $write_pathtemp = $writeable;
    } else {
      $write_pathtemp = $unwriteable;
    }
    if (Joom_CheckWriteable(JPATH_ROOT.DS,JPath::clean($config->jg_wmpath))) {
      $write_wmpath = $writeable;
    } else {
      $write_wmpath = $unwriteable;
    }
    if (is_file(JPATH_ROOT.DS.JPath::clean($config->jg_wmpath).DS.$config->jg_wmfile)) {
      $write_wmfile = '<span style="color:#080;">'
        . JText::_('JGA_FILE_EXIST') .
        '</span>';
    } else {
      $write_wmfile = '<span style="color:#f00;">'
        . JText::_('JGA_ALERT_FILE_NOT_EXIST') .
        '</span>';
    }

    //check whether CSS file (joom_settigns.css) is writeable
    if (Joom_CheckWriteable(JPATH_COMPONENT_SITE.DS,'assets'.DS.'css'.DS.'joom_settings.css')) {
      $configmsg = '<div style="color:#080; text-align:center;">[' . JText::_('JGA_CSS_CONFIGURATION_WRITEABLE') . ']</div>';
    } else {
      $configmsg = '<div style="color:#f00;font-weight:bold; text-align:center;">[' . JText::_('JGA_CSS_CONFIGURATION_NOT_WRITEABLE') . ' ' . JText::_('JGA_CHECK_PERMISSIONS') . ']</div>';
    }

    //categories for frontend upload
    $arr_jg_category  = explode(',', $config->jg_category);
    $clist = Joom_ShowBackendAllowedCat($arr_jg_category, "jg_category[]",
                               $extras=" multiple=\"multiple\"  size=\"6\"", $levellimit="4");

    //categories in which user categories can be created
    $arr_jg_usercategory  = explode(',', $config->jg_usercategory);
    $clist2 = Joom_ShowBackendAllowedCat($arr_jg_usercategory, "jg_usercategory[]",
                               $extras=" multiple  size=\"6\"", $levellimit="4");

    //include javascripts for checking changes in variables
    //with joom_testDefaultValues()
    $document = & JFactory::getDocument();
    $document->addScript("../includes/js/joomla.javascript.js");

    $document->addScript(_JOOM_LIVE_SITE.'administrator/components/com_joomgallery/assets/js/admin.joomscript.js');

    $submitbtns="function submitbutton(pressbutton) {\n"
                ."  var form = document.adminForm;\n"
                ."  if (pressbutton == 'cpanel') {\n"
                ."    submitform(pressbutton);\n"
                ."    return;\n"
                ."  }\n"
                ."  if (form.jg_paththumbs.value == ''){\n"
                ."    alert('".JText::_('JGA_ALERT_THUMBNAIL_PATH_SUPPORT',true)."');\n"
                ."  } else {\n"
                ."    joom_testDefaultValues();\n"
                ."    submitform(pressbutton);\n"
                ."  }\n"
                ."};";

    $document->addScriptDeclaration($submitbtns);

    HTML_Joom_AdminConfig::Joom_ShowConfig_HTML($clist, $clist2, $write_pathimages,
                                                $write_pathoriginalimages,
                                                $write_paththumbs, $write_pathtemp,
                                                $write_wmpath, $write_pathftpupload,
                                                $write_wmfile, $gdmsg,$immsg,
                                                $easycaptchamsg, $exifmsg, $configmsg);
  }
}
?>

