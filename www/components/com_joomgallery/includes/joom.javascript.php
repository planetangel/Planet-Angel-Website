<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.javascript.php $
// $Id: joom.javascript.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

# Don't allow direct linking
defined ('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

if(!defined('_JOOM_LIVE_SITE'))
  define( '_JOOM_LIVE_SITE', JURI::base());

#if ($func=="detail" || $config->jg_favourites == 1) {
  $document->addScript(_JOOM_LIVE_SITE.'includes/js/overlib_mini.js');
  /* Joom-eigenes Tooltip-Aussehen ermoeglichen */
  $jg_own_overlib = 1;  // konfigurierbar machen?
  if($jg_own_overlib)
    $document->addScriptDeclaration("    overlib_pagedefaults(WIDTH,250,VAUTO,RIGHT,AUTOSTATUSCAP, CSSCLASS,TEXTFONTCLASS,'jl-tips-font',FGCLASS,'jl-tips-fg',BGCLASS,'jl-tips-bg',CAPTIONFONTCLASS,'jl-tips-capfont', CLOSEFONTCLASS, 'jl-tips-closefont');");
  /* */
#}

$document->addScript(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/joomscript.js');
$script = '    var resizeJsImage = '.$config->jg_resize_js_image.';'; // used by slimbox, thickbox, DHTML and javascript window

//Detail view in general
if($func == 'detail') {
  if($config->jg_secimages==2 || ($config->jg_secimages==1 && $user->get('aid') <1)) {
    $use_code = 1;
  } else {
    $use_code = 0;
  }
  $script .= '
    var jg_use_code = '.$use_code.';
    var joomgallery_enter_name_email = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_ENTER_NAME_EMAIL',true)).'";
    var joomgallery_enter_comment = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_ENTER_COMMENT',true)).'";
    var joomgallery_enter_code = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_ENTER_CODE',true)).'";';
}

//JS-Window
if( ( $func == 'detail' && $config->jg_bigpic_open == 2 ) || $config->jg_detailpic_open == 2 ) {
  $script .= '
    var jg_disableclick = '.$config->jg_disable_rightclick_original.';';
}

//DHTML
if( ( $func == 'detail' && $config->jg_bigpic_open == 3 ) || $config->jg_detailpic_open == 3 ) {
  $script .= '
    var jg_padding = '.$config->jg_openjs_padding.';
    var jg_dhtml_border = "'.$config->jg_dhtml_border.'";
    var jg_openjs_background = "'.$config->jg_openjs_background.'";
    var jg_show_title_in_dhtml = '.$config->jg_show_title_in_dhtml.';
    var jg_show_description_in_dhtml = '.$config->jg_show_description_in_dhtml.';
    var jg_disableclick = '.$config->jg_disable_rightclick_original.';';
}

//Thickbox3
if( ( $func == 'detail' && $config->jg_bigpic_open == 5 ) || $config->jg_detailpic_open == 5 ) {
  $document->addScript(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/thickbox3/js/jquery-latest.pack.js');
  $document->addScript(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/thickbox3/js/thickbox.js');
  $document->addStyleSheet(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/thickbox3/css/thickbox.css');
  $script .= '
    var joomgallery_image = "'.JText::_('JGS_PICTURE',true).'";
    var joomgallery_of = "'.JText::_('JGS_OF',true).'";
    var joomgallery_close = "'.JText::_('JGS_CLOSE',true).'";
    var joomgallery_prev = "'.JText::_('JGS_PREVIOUS',true).'";
    var joomgallery_next = "'.JText::_('JGS_NEXT',true).'";
    var joomgallery_press_esc = "'.JText::_('JGS_ESC',true).'";
    var tb_pathToImage = "'._JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/thickbox3/images/loadingAnimation.gif";';
}


//Slimbox
if( ( $func == 'detail' && $config->jg_bigpic_open == 6 ) || $config->jg_detailpic_open == 6 ) {
  JHTML::_('behavior.mootools'); // loads mootools only, if it hasn't already been loaded
  $document->addScript(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/slimbox/js/slimbox.js');
  $document->addStyleSheet(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/slimbox/css/slimbox.css');
  $script .= '
    var resizeSpeed = '.$config->jg_lightbox_speed.';
    var joomgallery_image = "'.JText::_('JGS_PICTURE',true).'";
    var joomgallery_of = "'.JText::_('JGS_OF',true).'";';
}

//MotionGallery
if ( $func=="detail" && $config->jg_minis==1 && $config->jg_motionminis==2 ) {
  $document->addScript(_JOOM_LIVE_SITE.'components/com_joomgallery/assets/js/motiongallery/js/joom_motiongallery.js');
  $script .= "\n"
          .  "   /***********************************************\n"
          .  "   * CMotion Image Gallery- © Dynamic Drive DHTML code library (www.dynamicdrive.com)\n"
          .  "   * Visit http://www.dynamicDrive.com for hundreds of DHTML scripts\n"
          .  "   * This notice must stay intact for legal use\n"
          .  "   * Modified by Jscheuer1 for autowidth and optional starting positions\n"
          .  "   ***********************************************/";

  $pngbehaviour = "  <!-- Do not edit IE conditional style below -->"
                . "\n"
                ."  <!--[if lte IE 6]>"
                . "\n"
                . "  <style type=\"text/css\">\n"
                . "    .pngfile {\n"
                . "      behavior:url('".JURI::root()."components/com_joomgallery/assets/js/pngbehavior.htc') !important;\n"
                . "    }\n"
                . "  </style>\n"
                . "  <![endif]-->"
                . "\n"
                . "  <!-- End Conditional Style -->";
  $document->addCustomTag($pngbehaviour);


  $files  = "  <!-- Do not edit IE conditional style below -->"
          . "\n"
          . "  <!--[if gte IE 5.5]>"
          . "\n"
          . "  <style type=\"text/css\">\n"
          . "     #motioncontainer {\n"
          . "       width:expression(Math.min(this.offsetWidth, maxwidth)+'px');\n"
          . "     }\n"
          . "  </style>\n"
          . "  <![endif]-->"
          . "\n"
          . "  <!-- End Conditional Style -->";
  $document->addCustomTag($files);
}

//Userpanel
if(   $func == 'newusercat' ||  $func == 'editusercat'
  ||  $func == 'editpic'  ||  $func == 'showupload') {
  $script .= '
    var jg_ffwrong = "'.$config->jg_wrongvaluecolor.'";
    var jg_filenamewithjs = '.$config->jg_filenamewithjs.';
    var joomgallery_select_category = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_YOU_MUST_SELECT_CATEGORY',true)).'";
    var joomgallery_select_file = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_YOU_MUST_SELECT_ONE_PICTURE',true)).'";
    var joomgallery_pic_must_have_title = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_PICTURE_MUST_HAVE_TITLE',true)).'";
    var joomgallery_filename_double1 = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_FILENAME_DOUBLE1',true)).'";
    var joomgallery_filename_double2 = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_FILENAME_DOUBLE2',true)).'";
    var joomgallery_wrong_filename = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_WRONG_FIILENAME',true)).'";
    var joomgallery_wrong_extension = "'.str_replace("\n",'\n',JText::_('JGS_ALERT_WRONG_EXTENSION',true)).'";';
}

if($func=='detail' || defined('_JOOM_PARENT_MODULE') ) {
  if($config->jg_disable_rightclick_detail==1 && !defined('_JOOM_PARENT_MODULE')) {

    $script .= '
    var jg_photo_hover = 0;
    document.oncontextmenu = function() {
      if(jg_photo_hover==1) {
        return false;
      } else {
        return true;
      }
    }
    function joom_hover() {
      jg_photo_hover = (jg_photo_hover==1) ? 0 : 1;
    }';

  }
  if($config->jg_cursor_navigation == 1) {
    $script .= '
    document.onkeydown = joom_cursorchange;';
  }

  $script .= '
    var jg_comment_active = 0;';
}

$document->addScriptDeclaration($script);


function Joom_FixForJS($text, $bbcode=0)
{
  $config = Joom_getConfig();

  if($bbcode == 1 && $config->jg_bbcodesupport == 1)
  {
    if(!function_exists('Joom_BBDecode'))
    {
      include(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomgallery'.DS.'common.joomgallery.php');
    }
  $text = Joom_BBDecode($text);
  }
  $text = str_replace("\"","\'",$text);
  $text = preg_replace("/[\n\t\r]*/","",$text);
  return $text;
}//End function Joom_FixForJS


function Joom_OpenImage($open, $id, $catpath, $catid, $imgfilename, $imgtitle='', $imgdescription='')
{
  global $func;
  $config = Joom_getConfig();

  // Sanitize $catpath for robustness:
  // assumed to have no starting and one trailing slash aftewards  
  $catpath = preg_replace("/^[\/]*(.*[^\/])[\/]*$/", "$1/", $catpath);

  $picturepath      = _JOOM_LIVE_SITE.$config->jg_pathimages;
  $origpicturepath  = _JOOM_LIVE_SITE.$config->jg_pathoriginalimages;

  // TODO: switch to interface when completed
  if(!defined('_JOOM_ITEMID'))
  {
    $Itemid_jg = joommodule::getJoomId();
    define('_JOOM_ITEMID', $Itemid_jg);
  }

  // TODO: necessary?
  $Itemid = '';
  if(defined('_JOOM_ITEMID'))
  {
    $Itemid_array = explode('=', _JOOM_ITEMID);
    if(isset($Itemid_array[1])) $Itemid = $Itemid_array[1];
  }
  $Itemid_jg = ($Itemid != '') ? "&amp;Itemid=".$Itemid : "";

  if($func == 'detail' && !defined('_JOOM_PARENT_MODULE'))
  {
    $imgpath = $config->jg_pathoriginalimages.$catpath;
  }
  else
  {
    $imgpath = $config->jg_pathimages.$catpath;
  }

  if ($config->jg_watermark == 1)
  {
    if (($config->jg_detailpic_open > 3) && $config->jg_lightboxbigpic)
    {
      $orig = 1;
    }
    else
    {
      $orig = ($func == 'detail') ? 1 : 0;
    }
    $js_imgpath = _JOOM_LIVE_SITE.'index.php?option=com_joomgallery&amp;func=watermark&amp;id='.$id.'&amp;catid='.$catid.'&amp;orig='.$orig.'&amp;no_html=1'.$Itemid_jg;
  }
  else
  {
    if(($config->jg_detailpic_open > 3) && $config->jg_lightboxbigpic)
    {
      $js_imgpath  = $origpicturepath.$catpath;
    }
    else
    {
      $js_imgpath  = ($func == 'detail') ? $origpicturepath.$catpath : $picturepath.$catpath;
    }
    $js_imgpath .= $imgfilename;
  }

  switch($open)
  {
    case 1:
      $link = $js_imgpath."\" target=\"_blank";
      break;
    case 2:
      $imginfo = getimagesize(JPath::clean(JPATH_ROOT.DS.$imgpath.$imgfilename));
      $link    = "javascript:joom_openjswindow('".$js_imgpath."','".Joom_FixForJS($imgtitle)."', '".$imginfo[0]."','".$imginfo[1]."')";
      break;
    case 3:
      $imginfo = getimagesize(JPath::clean(JPATH_ROOT.DS.$imgpath.$imgfilename));
      $link    = "javascript:joom_opendhtml('".$js_imgpath."','".Joom_FixForJS($imgtitle)."','";
      if($config->jg_show_description_in_dhtml)
      {
        $link .= Joom_FixForJS($imgdescription, 1) ."','";
      }
      else
      {
        $link .= "','";
      }
      $link .= $imginfo[0]."','".$imginfo[1]."')";
      break;
    case 5: //thickbox3
      $link = $js_imgpath."\" class=\"thickbox\" rel=\"joomgallery\" title=\"".$imgtitle;
      break;
    case 6: //slimbox
      $link = $js_imgpath."\" rel=\"lightbox[joomgallery]\" title=\"".$imgtitle;
      break;
    default:
      $link = JRoute::_('index.php?option=com_joomgallery&amp;func=detail&amp;id='.$id.$Itemid_jg);
      break;
  }
    return $link;
}//End function Joom_OpenImage


function Joom_LightboxImages($start, $end, $orderclause=null, $catid, $id)
{
  global $func;
  $config   = Joom_getConfig();
  $database = & JFactory::getDBO();
  $user     = & JFactory::getUser();

  if( ( ($func == 'viewcategory' && $config->jg_detailpic_open > 3 
         && ($config->jg_showdetailpage == 1 
             || ($config->jg_showdetailpage == 0 && $user->get('aid') > 0))
        )
       || ($func == 'detail' && (($config->jg_bigpic == 1 && $user->get('aid') > 0) || $config->jg_bigpic == 2 ) && $config->jg_bigpic_open > 3)
      )
     && ($end != 0) && ($start < $end) && ($config->jg_lightbox_slide_all == 1)
    )
  {
    if($orderclause == null)
    {
      if($config->jg_secondorder != '' && $config->jg_thirdorder == '')
      {
        $orderclause = "a.".$config->jg_firstorder.", a.".$config->jg_secondorder;
      }
      elseif($config->jg_secondorder != '' && $config->jg_thirdorder != '')
      {
        $orderclause = "a.".$config->jg_firstorder.", a.".$config->jg_secondorder.", a.".$config->jg_thirdorder;
      }
      else
      {
        $orderclause = "a.".$config->jg_firstorder;
      }
    }
    if($func == 'detail')
    {
      $type = ($end==1) ? "before" : "after";
      $database->setQuery(" SELECT 
                              COUNT(id)
                            FROM 
                              #__joomgallery
                            WHERE 
                                      catid = $catid 
                              AND approved  = '1' 
                              AND published = '1'
                          ");
       $end = $database->loadResult();
    }
    $database->setQuery(" SELECT 
                            id, 
                            imgfilename, 
                            imgthumbname, 
                            imgtitle
                          FROM 
                            #__joomgallery AS a
                          LEFT JOIN 
                            #__joomgallery_catg AS c ON c.cid=a.catid
                          WHERE 
                               a.published = '1' 
                            AND a.catid    = $catid 
                            AND a.approved = '1' 
                            AND c.access  <= '".$user->get('aid')."'
                          ORDER BY 
                            $orderclause
                          LIMIT $start,".($end-$start)
                        );
    $rows  = $database->loadObjectList();
    $zaehl = 0;
    $check = 0;

    if($func == 'detail' && $type == 'after')
    {
      while($rows[$zaehl]->id!=$id)
      {
        $zaehl++;
      }
      $zaehl++;
    }
    elseif($func == 'detail' && $type == 'before' && $rows[$zaehl]->id == $id)
    {
      $check = 1;
    }
    echo "  <div class=\"jg_displaynone\">\n";
    while($zaehl < sizeof($rows) && $check != 1)
    {
      if($func == 'detail' && $type == 'before' && $rows[$zaehl]->id == $id)
      {
        $check = 1;
      }
      $row     = $rows[$zaehl];
      $catpath = Joom_GetCatPath($catid);
      if( ($func == 'detail' && is_file(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$row->imgfilename)))
         || $func == 'viewcategory'
        )
      {
        $link = Joom_OpenImage($config->jg_bigpic_open, $row->id, $catpath, $catid, $row->imgfilename, $row->imgtitle,'');
        echo "    <a href=\"".$link."\">".$row->id."</a>\n";
      }
      $zaehl++;
    }
    echo "  </div>\n";
  }
}//End function Joom_LightboxImages
?>
