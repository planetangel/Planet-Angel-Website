<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/joomgallery.php $
// $Id: joomgallery.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/


defined ('_JEXEC') or die('Direct Access to this location is not allowed.');

$mainframe = & JFactory::getApplication('site');
$database  = & JFactory::getDBO();
$user      = & JFactory::getUser();
$document  = & JFactory::getDocument();
define('_JOOM_LIVE_SITE',JURI::base());
JPluginHelper::importPlugin('joomgallery');

//add the css file generated from backend settings
$document->addStyleSheet(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/css/joom_settings.css');

//add the main css file
$document->addStyleSheet(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/css/joomgallery.css');

//add invidual css file if exists
if(file_exists(JPATH_ROOT.DS.'components'.DS.'com_joomgallery'.DS.'assets'.DS.'css'.DS.'joom_local.css'))
{
  $document->addStyleSheet(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/css/joom_local.css');
}

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'joomgallery.class.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'common.joomgallery.php');
require_once(JPATH_COMPONENT.DS.'joomgallery.html.php');

//Konfiguration
$config = Joom_getConfig();

//Ambit
$ambit = Joom_Ambit();

//TODO globals
global $id, $catid, $func;

$func = trim(Joom_mosGetParam('func', '', 'post'));
if($func == '')
{
  $func = trim(Joom_mosGetParam('func', ''));
}
$ambit->set('func', $func);

$id    = JRequest::getInt('id', 0);
$catid = JRequest::getInt('catid', 0);
$orig  = JRequest::getInt('orig', 0);

$ambit->set('id', $id);
$ambit->set('catid', $catid);

// Itemid
$Itemid = JRequest::getInt('Itemid', '');
if ($Itemid == '')
{
  $Itemid = JRequest::getInt('Itemid', '', 'post');
}
if(( $Itemid == 1) || ($Itemid == 99999999) || ($Itemid == ''))
{
  $database->setQuery(" SELECT
                          id
                        FROM
                          #__menu
                        WHERE
                          link = 'index.php?option=com_joomgallery'
                      ");
  $Itemid = intval($database->loadResult());
}
if($Itemid)
{
  define('_JOOM_ITEMID', '&Itemid='.$Itemid);
}
else
{
  define('_JOOM_ITEMID', '');
}

// Allgemeine Includes
include_once (JPATH_COMPONENT.DS.'includes'.DS.'joom.javascript.php');

// Eventuell Zip's loeschen
if($config->jg_favourites && ($config->jg_zipdownload
   || $config->jg_usefavouritesforpubliczip)
  )
{
  $database->setQuery(" SELECT
                          uid,
                          uuserid,
                          zipname
                        FROM
                          #__joomgallery_users
                        WHERE
                           zipname != ''
                          AND time != ''
                          AND time < NOW()-INTERVAL 60 SECOND
                      ");
  $ziprows = $database->loadObjectList();
  if(count($ziprows))
  {
    jimport('joomla.filesystem.file');
    foreach($ziprows as $row)
    {
      if(JFile::exists($row->zipname))
      {
        JFile::delete($row->zipname);
      }
      if($row->uuserid != 0)
      {
        $database->setQuery(" UPDATE
                                #__joomgallery_users
                              SET
                                time = '',
                                zipname = ''
                              WHERE
                                uid = '".$row->uid."'
                            ");
      }
      else
      {
        $database->setQuery(" DELETE
                              FROM
                                #__joomgallery_users
                              WHERE
                                    uuserid = '0'
                                AND zipname = '".$row->zipname."'
                            ");
      }
      $database->query();
    }
  }
}

##############################################################
switch($func)
{
  case 'special':
    Joom_GalleryHeader();
    include(JPATH_COMPONENT.DS.'includes'.DS.'joom.viewspecial.php' );
    break;

  case 'detail':
    if($user->get('aid') == 0 && $config->jg_showdetailpage == 0)
    {
      $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID, false),
                                     JText::_('JGS_ALERT_NO_DETAILVIEW_FOR_GUESTS'));
    }
    elseif($config->jg_detailpic_open != 0)
    {
      $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID, false),
                                     JText::_('JGS_ALERT_NO_DETAILVIEW_FOR_GUESTS'));
    } else {
      Joom_GalleryHeader();
      include(JPATH_COMPONENT.DS.'includes'.DS.'joom.viewdetails.php');
      $detailclass = new Joom_DetailView();
      //title-tag
      $pagetitle = Joom_MakePagetitle($config->jg_pagetitle_detail,
                                      $detailclass->cattitle,$detailclass->imgtitle);
      $document->setTitle(JText::_('JGS_GALLERY').' - '.$pagetitle);
      //Accordion
      //wenn Slimbox aktiviert nur das more js mit den zusaetzlichen Funktionen laden
      //sonst das komplette Mootools Paket, falls es nicht schon geladen wurde
      //TODO: realize accordion with JPane
      if(!$detailclass->slideshow && $config->jg_showdetailaccordion)
      {
        if($config->jg_bigpic_open != 6)
        {
          JHTML::_('behavior.mootools');
        }
        $document->addScript('components/com_joomgallery/assets/js/accordion/js/accordion.js');
      }
    }
    break;

  case 'savenameshield':
  case 'deletenameshield':
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'joom.nameshields.php');
    $nameshieldsclass = new Joom_Nameshields($func);
    break;

  case 'votepic':
    $imgvote = JRequest::getInt('imgvote', 0);
    include(JPATH_COMPONENT.DS.'includes'.DS.'joom.votepic.php');
    break;

  case 'userpanel':
  case 'showusercats':  //Overview user categories
  case 'newusercat':    //New user category
  case 'editusercat':   //Modify an existing user category
  case 'saveusercat':   //Save new or modified user category
  case 'deleteusercat': //Delete user category
  case 'editpic':
  case 'savepic':
  case 'deletepic':
  case 'showupload':
    if($config->jg_userspace == 0
       || ($config->jg_showuserpanel == 2 && $user->get('aid') != 2)
      )
    {
      //sie sind nicht berechtigt...
      echo JText::_('ALERTNOTAUTH');
      if($user->get('id') < 1)
      {
      echo "<br />" . JText::_('You need to login.');
      }
      #mosNotAuth();
      return;
    }
    
    if(!$user->get('id'))
    {
      $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID, false),
                                     JText::_('JGS_YOU_ARE_NOT_LOGGED'));
    }
    if(   $func == 'userpanel'  || $func == 'showusercats'
       || $func == 'newusercat' || $func == 'editusercat'
       || $func == 'editpic'    || $func == 'showupload')
    {
      Joom_GalleryHeader();
    }
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'joom.userpanel.php');
    $userpanelclass = new Joom_UserPanel($func, $catid);
    break;

  case 'commentpic':
  case 'deletecomment':
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'joom.comments.php');
    $commentsclass = new Joom_Comments($func, $id);
    break;

  case 'viewcategory':
    Joom_GalleryHeader();
    //ueberpruefen der Berechtigung
    $database->setQuery(" SELECT
                            COUNT(cid)
                          FROM
                            #__joomgallery_catg
                          WHERE
                                      cid = '$catid'
                            AND access   <= '".$user->get('aid')."'
                            AND published = '1'
                        ");
    $is_allowed = $database->loadResult();
    if($is_allowed < 1)
    {
      $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID, false),
                                     JText::_('JGS_ALERT_YOU_NOT_ACCESS_THIS_DIRECTORY'));
    }
    //Kategorieklasse
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'joom.viewcategory.php');
    $categoryclass = new Joom_CategoryView($catid);

    //Navigation fuer Unterkategorien oben
    if($config->jg_showpagenavsubs <= 2)
    {
      $categoryclass->Joom_SubcatPageNav();
    }

    //Ausgabe der Unterkategorien
    $categoryclass->Joom_ShowSubcategories();

    //Navigation fuer Unterkategorien unten
    if($config->jg_showpagenavsubs == 2)
    {
      $categoryclass->Joom_SubcatPageNav();
    }

    //Navigation fuer Kategorie
    $categoryclass->Joom_CatPageNav();

    $count        = $categoryclass->catcount;
    $orderclause  = $categoryclass->orderclause;
    $catstart     = $categoryclass->catstart;
    $startpage    = $categoryclass->catstartpage;
    $gesamtseiten = $categoryclass->catgesamtseiten;

    //Ausgabe der Kategorienavigation im Header
    if(( $config->jg_showpagenav == 1 ) || ( $config->jg_showpagenav == 2))
    {
      $categoryclass->Joom_ShowCategoryPageNav();
    }

    //Kategorie Kopfbereich
    $categoryclass->Joom_ShowCategoryHead();

    Joom_LightboxImages(0, $catstart, $orderclause, $catid, 0);

    //Kategorie Hauptbereich
    $categoryclass->Joom_ShowCategoryBody();

    Joom_LightboxImages($catstart+$config->jg_perpage, $count, $orderclause, $catid, 0);

    //Kategorie Navi im Footer
    if(($config->jg_showpagenav == 2 ) || ( $config->jg_showpagenav == 3))
    {
      $categoryclass->Joom_ShowCategoryPageNav();
    }

    //title-tag
    $pagetitle = Joom_MakePagetitle($config->jg_pagetitle_cat, $categoryclass->catname,'');
    $document->setTitle(JText::_('JGS_GALLERY')." - ".$pagetitle);

    break;

  case 'uploadhandler':
    include(JPATH_COMPONENT.DS.'classes'.DS.'upload.class.php' );
    $uploadclass = new Joom_Upload( $func, $catid );
    break;

  case 'send2friend':
    //TODO
    $send2friendname  = Joom_mosGetParam( 'send2friendname', '', 'post' );
    $send2friendemail = Joom_mosGetParam( 'send2friendemail', '', 'post' );
    #$from2friendname = Joom_mosGetParam( 'from2friendname', '', 'post' );
    #$from2friendemail = Joom_mosGetParam( 'from2friendemail', '', 'post' );
    #$id = JRequest::getInt( 'post', 'id', '' );
    $text  = $user->get('name') . ' (' . $user->get('email') . ')'.' '.JText::_('JGS_INVITE_YOU_VIEW_PICTURE')."\r \n";
    $text .= _JOOM_LIVE_SITE.'index.php?option=com_joomgallery&func=detail&id='.$id._JOOM_ITEMID."\r\n";
    $subject = $mainframe->getCfg('sitename') . ' - ' . JText::_('JGS_RECOMMENDED_PICTURE_FROM_FRIEND');
    JUtility::sendMail( $mainframe->getCfg('mailfrom'), $mainframe->getCfg('fromname'), $send2friendemail, $subject, $text);
    $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&func=detail&id='.$id._JOOM_ITEMID, false),JText::_('JGS_MAIL_SENT'));
    break;

  case 'download':
    include(JPATH_COMPONENT.DS.'includes'.DS.'joom.specialimages.php');
    $download = new Joom_SpecialImages;
    $download->Joom_CreateDownload($id, $orig, $catid);
  break;

  case 'addpicture':
  case 'removepicture':
  case 'removeall':
  case 'switchlayout':
  case 'createzip':
  case 'showfavourites':
    include(JPATH_COMPONENT.DS.'includes'.DS.'joom.favourites.php');
    $favourites = new Joom_Favourites($func, $id, $catid);
  break;

  case 'watermark':
    include(JPATH_COMPONENT.DS.'includes'.DS.'joom.specialimages.php');
    $watermark = new Joom_SpecialImages;
    $watermark->Joom_CreateWatermark($id, $catid, $orig);
  break;

  case 'joomplu':
    include(JPATH_COMPONENT.DS.'includes'.DS.'joom.viewminijoom.php');
    $minijoom = new Joom_ShowMiniJoom();
    return; // nothing else matters because the outputs above are usually shown in a popup window
  break;

  default:
    // include dTree script, dTree styles and treeview styles, if neccessary
    if($config->jg_showsubsingalleryview)
    {
      $document->addStyleSheet(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/dTree/css/jg_dtree.css');
      $document->addStyleSheet(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/dTree/css/jg_treeview.css');
      $document->addScript(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/dTree/js/jg_dtree.js');
    }
    
    $func = ''; // for some pollings later on
    Joom_GalleryHeader();
    // set default page title
    $document->setTitle(JText::_('JGS_GALLERY'));

    if (!$config->jg_showrmsmcats) {
      $access = "AND access <= '".$user->get('aid')."'";
    } else {
      $access = '';
    }

    # Feststellen der Anzahl der darstellbaren Kategorien
    $database->setQuery(" SELECT
                            COUNT(cid)
                          FROM
                            #__joomgallery_catg
                          WHERE
                            published = '1'
                            AND parent='0'
                            $access
                        ");
    $count2 = $database->loadResult();
    # Berechnen der Gesamtseiten
    if($config->jg_catperpage == 0) $config->jg_catperpage = 10;
    $gesamtseiten = floor($count2 / $config->jg_catperpage);
    $seitenrest   = $count2 % $config->jg_catperpage;
    if($seitenrest > 0)
    {
      $gesamtseiten++;
    }

    $count2 = number_format($count2, 0, ',', '.');
    # Feststellen der aktuellen Seite
    $startpage = JRequest::getInt('startpage', 0);
    if(isset($startpage))
    {
      if($startpage > $gesamtseiten)
      {
        $startpage=$gesamtseiten;
      }
      elseif($startpage < 1)
      {
        $startpage = 1;
      }
    }
    else
    {
      $startpage = 1;
    }

    # Limit und Seite Vor- & Rueckfunktionen
    $start = ($startpage - 1) * $config->jg_catperpage;
    #if (!$func) {
      if(($config->jg_showgallerypagenav == 1) || ($config->jg_showgallerypagenav == 2))
      {
        Joom_ShowGalleryPageNav_HTML($count2, $start, $startpage, $gesamtseiten);
      }
    #}
    Joom_GalleryDefault($start);
    #if (!$func) {
      if(($config->jg_showgallerypagenav == 2) || ($config->jg_showgallerypagenav == 3))
      {
        Joom_ShowGalleryPageNav_HTML($count2, $start, $startpage, $gesamtseiten);
      }
    #}
    break;
}

if($func != 'uploadhandler') Joom_GalleryFooter();

if($config->jg_completebreadcrumbs) Joom_CompleteBreadcrumbs($catid, $id, $func);
?>
