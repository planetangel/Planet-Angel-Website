<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/common.joomgallery.php $
// $Id: common.joomgallery.php 449 2009-06-14 11:57:04Z aha $
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

/**
 * Read the configuration from DB
 *
 * @param int $id
 * @return object $config
 */
function Joom_getConfig($id = 0) {
  static $config;

  if(!is_object($config)) {
    $database = & JFactory::getDBO();
    $query = 'SELECT * FROM #__joomgallery_config WHERE id = '.$id;
    $database->setQuery($query);
    $config = $database->loadObject();

    /* @TODO
    //only one config field ?
    $query = 'SELECT params FROM #__components WHERE admin_menu_link = \'option=com_joomgallery\'';
    $database->setQuery($query);
    $rows = explode("\n",$database->loadResult());
    foreach($rows as $row) {
      $splitted = explode('=',$row);
      $config->$splitted[0] = $splitted[1];
    }
    */
  }
  return $config;
}

/**
 * Wrapper function for JRequest
 * and in addition $database->getEscaped
 *
 * @param string $name name of config var
 * @param string $default default of var
 * @param string $art 'POST' 'GET'
 * @return string content of config var
 */
function Joom_mosGetParam($name,$default = '',$art = '') {
  $database = & JFactory::getDBO();
  $config_variable = JRequest::getVar($name,$default,$art);
  if(!is_array($config_variable) && $art != 'files') {
    $config_variable = $database->getEscaped($config_variable);
  }
  return $config_variable;
}

/**
 * Cleaning of file/category name
 * evtl. replace extension if present
 * replace special chars defined in backend
 *
 * @param string $orig
 * @param int $replace_extension 1=strip extension
 * @return string cleaned name (with or without extension)
 */
function Joom_FixFilename($orig,$replace_extension=0) {
  $config = Joom_getConfig();

  $orig = strtolower(basename($orig));
  // replace special chars
  if(!empty($config->jg_filenamesearch)) {
    $filenamesearch  = explode("|",$config->jg_filenamesearch);
  } else {
    $filenamesearch = array();
  }

  if(!empty($config->jg_filenamereplace)) {
    $filenamereplace = explode("|",$config->jg_filenamereplace);
  } else {
    $filenamereplace = array();
  }

  // replace whitespace with underscore
  array_push($filenamesearch,"\s");
  array_push($filenamereplace,"_");
  // replace other stuff
  array_push($filenamesearch,"[^a-z_0-9-]");
  array_push($filenamereplace,"");

  //checks for different array-length
  $lengthsearch  = sizeof($filenamesearch);
  $lengthreplace = sizeof($filenamereplace);
  if($lengthsearch>$lengthreplace) {
    while($lengthsearch>$lengthreplace) {
      array_push($filenamereplace,"");
      $lengthreplace = $lengthreplace+1;
    }
  } else if($lengthreplace>$lengthsearch) {
    while($lengthreplace>$lengthsearch) {
      array_push($filenamesearch,"");
      $lengthsearch = $lengthsearch+1;
    }
  }

  //checks for extension
  $extensions = array('.jpeg','.jpg','.jpe','.gif','.png');
  $extension=false;
  for($i=0;$i<sizeof($extensions);$i++) {
    $extensiontrue = substr_count($orig,$extensions[$i]);
    if($extensiontrue !=0) {
      $extension=true;
      //if extension found, break
      break;
    }
  }
  //replace extension if present
  if($extension) {
    $fileextension = ereg_replace('.*\.([^\.]*)$', '\\1', $orig);
    $fileextensionlength = strlen($fileextension);
    $filenamelength = strlen($orig);
    $filename = substr($orig,-$filenamelength,-$fileextensionlength-1);
   //no extension found (Batchupload)
  } else {
    $filename = $orig;
  }
  for ($i=0;$i<$lengthreplace;$i++) {
    $searchstring = "!".$filenamesearch[$i]."+!i";
    $filename = preg_replace($searchstring, $filenamereplace[$i] ,$filename);
  }
  if($extension && $replace_extension == 0) {
    //returns filename with extension for regular upload
    return $filename.".".$fileextension;
  } else {
    //returns filename without extension for batchupload
    return $filename;
  }
}

/**
 * If an image is not readable for getimagesize
 * we set the rights of this image to 777
 * and back to 644 afterwards
 *
 * @TODO This function will probably be never necessary
 *
 * @param string $file the file from which we want the info
 * @return array the image info or boolean false if an error occured
 */
function Joom_GetImageSize($file) {

  // ensure that path is valid and clean
  $file = JPath::clean($file);

  // maybe it works without changes
  $info = getimagesize($file);
  if($info != false) {
    return $info;
  }

  // Initialize variables
  static $ftpOptions;
  if(!isset($ftpOptions)) {
    jimport('joomla.client.helper');
    $ftpOptions = JClientHelper::getCredentials('ftp');
  }

  if ($ftpOptions['enabled'] == 1) {
    // Connect the FTP client
    jimport('joomla.client.ftp');
    $ftp = &JFTP::getInstance(
      $ftpOptions['host'], $ftpOptions['port'], null,
      $ftpOptions['user'], $ftpOptions['pass']
    );

    // Translate path to FTP path
    $ftp_file = JPath::clean(str_replace(JPATH_ROOT, $ftpOptions['root'], $file), '/');
    $ftp->chmod($ftp_file, 0777);

    $info = getimagesize($file);

    $ftp->chmod($ftp_file, 0644);
  } else {
    JPath::setPermissions($file, 0777);

    $info = getimagesize($file);

    JPath::setPermissions($file, 0644);
  }

  return $info;
}

/**
 * Changes the permissions of a directory (or file)
 * either by the FTP-Layer if enabled
 * or by JPath::setPermissions (chmod())
 *
 * not sure but probable: J! 1.6 will use
 * FTP-Layer automatically in setPermissions
 * so Joom_Chmod will become unnecessary
 *
 * @param string $dir directory
 * @param int or octal number $mode permissions which will be applied to $dir
 * @return boolean true on success, false otherwise
 */
function Joom_Chmod($dir, $mode = 0644) {

  static $ftpOptions;

  if(!isset($ftpOptions)) {
    // Initialize variables
    jimport('joomla.client.helper');
    $ftpOptions = JClientHelper::getCredentials('ftp');
  }

  if ($ftpOptions['enabled'] == 1) {
    // Connect the FTP client
    jimport('joomla.client.ftp');
    $ftp = &JFTP::getInstance(
      $ftpOptions['host'], $ftpOptions['port'], null,
      $ftpOptions['user'], $ftpOptions['pass']
    );
    // Translate path to FTP path
    $dir = JPath::clean(str_replace(JPATH_ROOT, $ftpOptions['root'], $dir), '/');
    return $ftp->chmod($dir, $mode);
  } else {
    return true;
  }
}

/**
 * Resize image with functions from gd/gd2/imagemagick
 *
 * @param string $src_file path to source file
 * @param string $dest_file path to destination file
 * @param string $useforresizedirection resize to width or height ratio
 * @param int $new_width   width to resize
 * @param int $thumbheight height to resize
 * @param int $method      1=gd1 2=gd2 3=im
 * @param int $dest_qual   $config->jg_thumbquality
 * @param bool $max_width    true=resize to maxwidth
 * @return bool true=resize succesful
 */
function Joom_ResizeImage(&$debugoutput,$src_file, $dest_file, $useforresizedirection,
                          $new_width, $thumbheight, $method, $dest_qual,
                          $max_width=false) {
  $config = Joom_getConfig();

  //Ensure that the pathes are valid and clean
  $src_file  = JPath::clean($src_file);
  $dest_file = JPath::clean($dest_file);

  //Doing resize instead of thumbnail, copy original and remove it.
  //@TODO check this extensions if needful
  $imagetype = array(1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD',
                     6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2',
                     11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF');
  $imginfo = getimagesize($src_file);

  if ($imginfo == null) die(JText::_('JG_FILE_NOT_FOUND'));
  $imginfo[2] = $imagetype[$imginfo[2]];
  // GD can only handle JPG & PNG images
  if ($imginfo[2] != 'JPG' && $imginfo[2] != 'PNG' && $imginfo[2] != 'GIF'
      && ($method == 'gd1' || $method == 'gd2')) die(JText::_('JG_GD_ONLY_JPG_PNG'));
    // height/width
    $srcWidth  = $imginfo[0];
    $srcHeight = $imginfo[1];
  if ($max_width) {
    $debugoutput .= JText::_('JG_RESIZE_TO_MAX') . "<br />";
    $ratio = max($srcHeight,$srcWidth) / $new_width ;
    //$ratio = $srcWidth / $new_width;
  } else {
    $debugoutput .= JText::_('JG_CREATE_THUMBNAIL_FROM') . " $imginfo[2], $imginfo[0] x $imginfo[1]...<br />";
    //convert to width ratio
    if ($useforresizedirection) {
      $ratio = ($srcWidth / $new_width);
      $testheight = ($srcHeight/$ratio);
      //if new height exceeds the setted max. height
      if($testheight>$thumbheight) {
        $ratio = ($srcHeight/$thumbheight);
      }
    //convert to height ratio
    } else {
      $ratio = ($srcHeight / $thumbheight);
      $testwidth = ($srcWidth/$ratio);
      //if new width exceeds setted max. width
      if($testwidth>$new_width) {
        $ratio = ($srcWidth/$new_width);
      }
    }
  }
  $ratio = max($ratio, 1.0);

  $destWidth  = (int)($srcWidth / $ratio);
  $destHeight = (int)($srcHeight / $ratio);

  // Method for creation of the resized image
  switch ($method) {
    case 'gd1' :
      if (!function_exists('imagecreatefromjpeg' )) {
        $debugoutput.=JText::_('JG_GD_LIBARY_NOT_INSTALLED');
        return false;
      }
      if ( $imginfo[2] == 'JPG' ) {
        $src_img = imagecreatefromjpeg($src_file);
      } else if ($imginfo[2] == 'PNG') {
        $src_img = imagecreatefrompng($src_file);
      } else {
        $src_img = imagecreatefromgif($src_file);
      }
      if (!$src_img) {
        $ERROR = $lang_errors['invalid_image'];
        return false;
      }
      $dst_img = imagecreate($destWidth, $destHeight);
      imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $destWidth,
                       (int)$destHeight, $srcWidth, $srcHeight);
      if(!@imagejpeg($dst_img, $dest_file, $dest_qual)) {
        // workaround for servers with wwwrun problem
        $dir = dirname($dest_file);
        Joom_Chmod($dir, 0777);
        imagejpeg($dst_img, $dest_file, $dest_qual);
        Joom_Chmod($dir, 0755);
      }
      imagedestroy($src_img);
      imagedestroy($dst_img);
    break;

    case 'gd2' :
      if (!function_exists('imagecreatefromjpeg')) {
        $debugoutput.=JText::_('JG_GD_LIBARY_NOT_INSTALLED');
        return false;
      }
      if (!function_exists('imagecreatetruecolor')) {
        $debugoutput.=JText::_('JG_GD_NO_TRUECOLOR');
        return false;
      }
      if ($imginfo[2] == 'JPG') {
        $src_img = imagecreatefromjpeg($src_file);
      } else if ($imginfo[2] == 'PNG'){
        $src_img = imagecreatefrompng($src_file);
      } else {
        $src_img = imagecreatefromgif($src_file);
      }

      if (!$src_img){
        $ERROR = $lang_errors['invalid_image'];
        return false;
      }
      $dst_img = imagecreatetruecolor($destWidth, $destHeight);

      if ($config->jg_fastgd2thumbcreation == 0) {
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $destWidth,
                           (int)$destHeight, $srcWidth, $srcHeight);
      } else {
        Joom_FastImageCopyResampled($dst_img, $src_img, 0, 0, 0, 0, $destWidth,
                           (int)$destHeight, $srcWidth, $srcHeight);
      }

      if(!@imagejpeg($dst_img, $dest_file, $dest_qual)) {
        // workaround for servers with wwwrun problem
        $dir = dirname($dest_file);
        Joom_Chmod($dir, 0777);
        imagejpeg($dst_img, $dest_file, $dest_qual);
        Joom_Chmod($dir, 0755);
      }
      imagedestroy($src_img);
      imagedestroy($dst_img);
    break;
    case 'im':
      $disabled_functions = explode(',', ini_get('disabled_functions'));
      foreach($disabled_functions as $disabled_function)
      {
        if(trim($disabled_function) == 'exec')
        {
          return false;
        }
      }

      if (!empty($config->jg_impath)) {
        $convert_path=$config->jg_impath.'convert';
      } else {
        $convert_path='convert';
      }
      $commands = ' -resize "'.$destWidth.'x'.$destHeight.'" -quality "'.$dest_qual.'"  -unsharp "3.5x1.2+1.0+0.10"';
      $convert = $convert_path.' '.$commands.' "'.$src_file.'" "'.$dest_file.'"';
      //echo $convert.'<br />';
      $return_var=null;
      $dummy=null;
      @exec($convert, $dummy, $return_var);
      if ($return_var != 0){
        // workaround for servers with wwwrun problem
        // TODO: necessary here? probably test required
        $dir = dirname($dest_file);
        Joom_Chmod($dir, 0777);
        @exec($convert, $dummy, $return_var);
        Joom_Chmod($dir, 0755);
        if ($return_var != 0){
          return false;
        }
      }
    break;
  }

  // We check that the image is valid
  $imginfo = getimagesize($dest_file);
  if ($imginfo == null) {
    return false;
  } else {
    return true;
  }
}

/**
 * Fast resizing of images with GD2
 * Notice: need up to 3/4 times more memory
 * http://de.php.net/manual/en/function.imagecopyresampled.php#77679
 * Plug-and-Play fastimagecopyresampled function replaces much slower
 * imagecopyresampled. Just include this function and change all
 * "imagecopyresampled" references to "fastimagecopyresampled".
 * Typically from 30 to 60 times faster when reducing high resolution
 * images down to thumbnail size using the default quality setting.
 * Author: Tim Eckel - Date: 09/07/07 - Version: 1.1 -
 * Project: FreeRingers.net - Freely distributable - These comments must remain.
 *
 * Optional "quality" parameter (defaults is 3). Fractional values are allowed,
 * for example 1.5. Must be greater than zero.
 * Between 0 and 1 = Fast, but mosaic results, closer to 0 increases the mosaic effect.
 * 1 = Up to 350 times faster. Poor results, looks very similar to imagecopyresized.
 * 2 = Up to 95 times faster.  Images appear a little sharp,
 *                              some prefer this over a quality of 3.
 * 3 = Up to 60 times faster.  Will give high quality smooth results very close to
 *                             imagecopyresampled, just faster.
 * 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
 * 5 = No speedup.             Just uses imagecopyresampled, no advantage over
 *                             imagecopyresampled.
 *
 * @param string $dst_image path to destination image
 * @param string $src_image path to source image
 * @param int $dst_x  destination x point left above
 * @param int $dst_y  destination y point left above
 * @param int $src_x  source x point left above
 * @param int $src_y  source y point left above
 * @param int $dst_w  destination width
 * @param int $dst_h  destination height
 * @param int $src_w  source width
 * @param int $src_h  source height
 * @param int $quality quality of destination (fix = 3) read instrcution above
 * @return bool true=resize succesful
 */
function Joom_FastImageCopyResampled (&$dst_image, $src_image, $dst_x, $dst_y,
                                 $src_x, $src_y, $dst_w, $dst_h,
                                 $src_w, $src_h, $quality = 3) {

  if (empty($src_image) || empty($dst_image) || $quality <= 0) {
    return false;
  }
  if ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
    $temp = imagecreatetruecolor ($dst_w * $quality + 1, $dst_h * $quality + 1);
    imagecopyresized   ($temp, $src_image, 0, 0, $src_x, $src_y, $dst_w * $quality + 1,
                        $dst_h * $quality + 1, $src_w, $src_h);
    imagecopyresampled ($dst_image, $temp, $dst_x, $dst_y, 0, 0, $dst_w,
                        $dst_h, $dst_w * $quality, $dst_h * $quality);
    imagedestroy ($temp);
  } else {
    imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w,
                        $dst_h, $src_w, $src_h);
  }
  return true;
}

/**
 * Replace bbcode tags to HTML tags
 * 1. replace linefeed to <br />
 * 2. replace b/u/i/url/email tags
 *
 * @param string $text text to be modified
 * @return string modified text
 */
function Joom_BBDecode($text) {
  $text = nl2br($text);
  static $bbcode_tpl = array();
  static $patterns = array();
  static $replacements = array();

  // First: If there isn't a "[" and a "]" in the message, don't bother.
  if ((strpos($text, '[') === false || strpos($text, ']') === false)) {
    return $text;
  }
  // [b] and [/b] for bolding text.
  $text=str_replace( "[b]", '<b>', $text );
  $text=str_replace( "[/b]", '</b>', $text );

  // [u] and [/u] for underlining text.
  $text=str_replace( "[u]", '<u>', $text );
  $text=str_replace( "[/u]", '</u>', $text );

  // [i] and [/i] for italicizing text.
  $text=str_replace( "[i]", '<i>', $text );
  $text=str_replace( "[/i]", '</i>', $text );

  if (!count($bbcode_tpl )) {
    // We do URLs in several different ways..
    $bbcode_tpl['url']='<span class="bblink"><a href="{URL}" target="_blank">{DESCRIPTION}</a></span>';
    $bbcode_tpl['email']='<span class="bblink"><a href="mailto:{EMAIL}">{EMAIL}</a></span>';
    $bbcode_tpl['url1']=str_replace( '{URL}', '\\1\\2', $bbcode_tpl['url'] );
    $bbcode_tpl['url1']=str_replace( '{DESCRIPTION}', '\\1\\2', $bbcode_tpl['url1'] );
    $bbcode_tpl['url2']=str_replace( '{URL}', 'http://\\1', $bbcode_tpl['url'] );
    $bbcode_tpl['url2']=str_replace( '{DESCRIPTION}', '\\1', $bbcode_tpl['url2'] );
    $bbcode_tpl['url3']=str_replace( '{URL}', '\\1\\2', $bbcode_tpl['url'] );
    $bbcode_tpl['url3']=str_replace( '{DESCRIPTION}', '\\3', $bbcode_tpl['url3'] );
    $bbcode_tpl['url4']=str_replace( '{URL}', 'http://\\1', $bbcode_tpl['url'] );
    $bbcode_tpl['url4']=str_replace( '{DESCRIPTION}', '\\2', $bbcode_tpl['url4'] );
    $bbcode_tpl['email']=str_replace( '{EMAIL}', '\\1', $bbcode_tpl['email'] );
    // [url]xxxx://www.phpbb.com[/url] code..
    $patterns[1] = '#\[url\]([a-z]+?://){1}([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+\(\)]+)\[/url\]#si';
    $replacements[1] = $bbcode_tpl['url1'];
    // [url]www.phpbb.com[/url] code.. (no xxxx:// prefix).
    $patterns[2] = '#\[url\]([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+\(\)]+)\[/url\]#si';
    $replacements[2] = $bbcode_tpl['url2'];
    // [url=xxxx://www.phpbb.com]phpBB[/url] code..
    $patterns[3] = '#\[url=([a-z]+?://){1}([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+\(\)]+)\](.*?)\[/url\]#si';
    $replacements[3] = $bbcode_tpl['url3'];
    // [url=www.phpbb.com]phpBB[/url] code.. (no xxxx:// prefix).
    $patterns[4] = '#\[url=([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+\(\)]+)\](.*?)\[/url\]#si';
    $replacements[4] = $bbcode_tpl['url4'];
    //[email]user@domain.tld[/email] code..
    $patterns[5] = '#\[email\]([a-z0-9\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#si';
    $replacements[5] = $bbcode_tpl['email'];
  }
  $text=preg_replace($patterns, $replacements, $text);
  return $text;
}

/**
 * Replace german special chars
 * @deprecated bad work with hard coded chars
 *
 * @param string $text
 * @return string modified text
 */
function Joom_FixCatname ($text) {
  $database = & JFactory::getDBO();

  $text = trim($text);
  if($text != "") {
    $text = strip_tags($text);
    $search = array("/\s/","/ä/","/ö/","/ü/","/Ä/","/Ö/","/Ü/","/ß/");
    $replace = array("_","ae","oe","ue","Ae","Oe","Ue","ss");
    $text = preg_replace($search, $replace, $text);
    $text = strtolower ($text);
    $text= preg_replace("/[^a-z0-9_]/","",$text);
    $text = $database->getEscaped($text);
  }
  return $text;
}

/**
 * Modify text
 * 1. trim spaces
 * 2. strip all html tags
 * 3. convert to htl entities
 * 4. escape them
 *
 * @param string $text
 * @return string modified text
 */
function Joom_FixUserEntrie($text) {
  $database = & JFactory::getDBO();

  $text = trim($text);
  if($text != "") {
    $text = strip_tags($text);
    $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
    $text = $database->getEscaped($text);
  }
  return $text;
}


/**
 * Modify text
 * 1. trim spaces
 * 2. strip all html tags
 * 3. escape them
 *
 * @param string $text
 * @return string modified text
 */
function Joom_FixUserEntrie2($text) {
  $database = & JFactory::getDBO();

  $text = trim($text);
  if($text != "") {
    $text = strip_tags($text);
    $text = $database->getEscaped($text);
  }
  return $text;
}

/**
 * Modify text
 * trim spaces from text
 *
 * @param string $text
 * @return string
 */
function Joom_FixAdminEntrie($text) {
  $text = trim($text);
  return $text;
}

/**
 * Modify path
 * 1. trim '/' from path
 *
 * @param string $path
 * @return string modified path or root warning
 */
function Joom_FixPathEntrie($path) {
  $path = trim($path,'/').'/';
  if($path == '/'){
    return 'PLEASE_DO_NOT_USE_JOOMLA_ROOT';
  }
  return $path;
}

/**
 * Construct category Path and show them in category manager
 * read path of category from static array, if not existent read them from DB
 * @param int $catid
 * @return string  path of category
 */
function Joom_ShowCategoryPath( $catid ) {
  static $path;

  if(!isset($path)) {
    $path = array();
  }

  $catid = intval($catid);
  if(empty($path[$catid])) {
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();

    $cat = $catid;
    if ($catid == 0){
      return;
    }
    $parent_id = true;
    while ($parent_id) {
      //read name and parent_id
      $query = "SELECT name, parent
          FROM #__joomgallery_catg
          WHERE cid=$cat AND access<='".$user->get('aid')."'";
      $database->setQuery( $query );
      $result = $database->loadObject();
      $parent_id = $result->parent;
      $name = $result->name;
      // add path to array
      if (empty($path[$catid])) {
        $path[$catid] = $name;
      } else {
        $path[$catid] = $name . ' &raquo; ' . $path[$catid];
      }
      // next loop
      $cat = $parent_id;
    }
  }
  return $path[$catid] . ' ';
}

/**
 * Callback function for sorting an array of objects to assembled names of
 * categories with alle parent categories
 * @see Joom_ShowDropDownCategoryList()
 *
 * @param object $a
 * @param object $b
 * @return 0 if names equal, -1 if a < b, 1 if a > b
 */
function Joom_SortCatArray($a,$b){
  return strcmp($a->name, $b->name);
}

/**
 * Construct HTML list of all categories
 *
 * @param int $cat catid
 * @param string $cname name of HTML select according to $_POST variable
 * @param string $extra HTML Code
 * @param int $orig catid - filled if category shall be excluded
 * @return string HTML Code
 */
function Joom_ShowDropDownCategoryList($cat, $cname = 'catid', $extra = null, $orig=null) {
  $database = & JFactory::getDBO();

  //get all categories from DB in array of objects and mark them all to 'not ready'
  $query = "SELECT cid, parent,name,'0' as ready
            FROM #__joomgallery_catg";
  $database->setQuery($query);
  $rows = $database->loadObjectList("cid");

  //if 'parent' set array of categories with parent = actual category
  //to ignore them
  if ($cname=='parent' && $orig != null) {
    $ignore=array();
  }

  //Head of HTML code
  $output = "<select name=\"$cname\" class=\"inputbox\" $extra >\n";
  $output .= "  <option value=\"0\"></option>\n";

  //no categories found, close the HTML and return
  if (count($rows)==0){
    $output .= "</select>\n";
    return $output;
  }

  //Loop through array of objects and construct the path
  foreach ($rows as $key => $obj) {
    $parent=$obj->parent;
    if ($cname=='parent' && $orig != null) {
      if ($parent==$orig || in_array($parent,$ignore)) {
        //act. category found, add them to ignore array
        if (!in_array($key,$ignore)) {
          $ignore[]=$key;
          continue;
        }
      }else{
        //check if in parent path way up there is the cat=$orig
        //then exclude all involved cats
        $parentcat=null;
        $parentcats=array();
        $parentcat=$rows[$key]->parent;
        while ($parentcat!=0 && $parentcat!=$orig){
          $parentcat=$rows[$parentcat]->parent;
          $parentcats[]=$parentcat;
        }
        if (!empty($parentcats) && in_array($orig,$parentcats)) {
          //if found add the collected cats to ignore array
          $ignore[]=$key;
          $ignore=array_merge($ignore,$parentcats);
          //free array of parentcats
          $parentcats=array();
          continue;
        }
      }
    }

    //if root category go to next element
    if ($parent != 0){
      //if path of parent already constructed, take them directly
      if ($rows[$parent]->ready){
        $rows[$key]->name = $rows[$parent]->name . ' &raquo; ' . $rows[$key]->name;
      } else {
        while ($parent!=0){
          $rows[$key]->name = $rows[$parent]->name . ' &raquo; ' . $rows[$key]->name;

          //if path of actual parent already constructed, break the while
          //othwerwise continue with next parent
          if ($rows[$parent]->ready){
            break;
          }else{
            $parent=$rows[$parent]->parent;
          }
        }
      }
    }
    //path completed, mark them as ready
    $rows[$key]->ready="1";
  }

  //remove from array the cats collected in ignore array
  if ($cname=='parent' && $orig != null) {
    foreach ($ignore as $catignore){
      unset ($rows[$catignore]);
    }
  }

  //sort the array by pathname
  usort( $rows , "Joom_SortCatArray" );

  //construct the HTML for each cat
  foreach ($rows as $key => $obj) {
    //category must not be parent to itself
    if($cname != 'parent' || ($cname == 'parent' && $obj->cid != $orig)){
      $output .="<option value=\"".$obj->cid."\"";
      if($cat==$obj->cid) {
        $output .= " selected=\"selected\"";
      }
      $output .=">".$obj->name."</option>\n";
    }
  }
  $output .= "</select>\n";
  $rows=array();
  return $output;
}

/**
 * Wrap text
 *
 * @param string $text
 * @param int $nr     number of chars to wrap
 * @return string   wrapped text
 */
function Joom_ProcessText($text,$nr=40) {

  $mytext=explode(" ",trim($text));
  $newtext=array();
  foreach($mytext as $k=>$txt) {
    if (strlen($txt)>$nr) {
      $txt=wordwrap($txt, $nr, "- ", 1);
    }
    $newtext[]=$txt;
  }
  return implode(" ",$newtext);
}

/**
 * Reads the category path from array
 * if not set read db and add to array
 *
 * @param int $catid
 * @return string catpath
 */
function Joom_GetCatPath($catid) {
  static $catpath;

  if(!isset($catpath)) {
    $catpath = array();
  }

  if(empty($catpath[$catid])) {
    $database = & JFactory::getDBO();
    $database->setQuery("SELECT catpath
        FROM #__joomgallery_catg
        WHERE cid= '".$catid."'");

    $catobj = $database->loadObject();

    if (empty($catobj->catpath)) {
      $catpath[$catid] = '/';
    } else {
      $catpath[$catid] = $catobj->catpath.'/';
    }
  }

  return $catpath[$catid];
}

/**
 * See if directory can be written to
 * @deprecated
 * @param string $path
 * @param unknown_type $dirpath
 * @return bool   true=writeable
 */
function Joom_CheckWriteable($path, $dirpath) {

  $fullpath = $path . $dirpath;
  if (@is_writable($fullpath)) {
    return true;
  } else {
    return false;
  }
}

/**
 * Attempts to determine if gd is configured, and if so,
 * what version is installed
 */
function Joom_GDVersion() {
  if (!extension_loaded('gd')) {
    return;
  }

  $phpver = substr(phpversion(), 0, 3);
  // gd_info came in at 4.3
  if ($phpver < 4.3)
    return -1;

  if (function_exists('gd_info')) {
    $ver_info = gd_info();
    preg_match('/\d/', $ver_info['GD Version'], $match);
    $gd_ver = $match[0];
    return $match[0];
  } else {
    return;
  }
}

/**
 * Attempts to determine if ImageMagick is configured, and if so,
 * what version is installed
 *
 */
function Joom_IMVersion() {
  $config = Joom_getConfig();
  $status = null;
  $output = array();

  $disabled_functions = explode(',', ini_get('disabled_functions'));
  foreach($disabled_functions as $disabled_function)
  {
    if(trim($disabled_function) == 'exec')
    {
      return '0';
    }
  }

  if (!empty($config->jg_impath)) {
    $execstring=$config->jg_impath.'convert -version';
  } else {
    $execstring='convert -version';
  }

  @exec($execstring, $output, $status);

  if (count($output)==0) {
    return '0';
  } else {
    return($output[0]);
  }
}

/**
* Create a new directory and copies a 'index.html' in it
*
* @param    string      $dir: absolute path to category
* @return   int/string  $error or 0
*/
function Joom_MakeDirectory($dir) {
  jimport('joomla.filesystem.file');

  // Ensure that the path is valid and clean
  $dir = JPath::clean($dir);

  //check if the directory already exists
  if (is_dir($dir)) {
    //if existent return error string '1111'
    //used by Joom_AlertErrorMessages
    $error = "1111";
    return $error;
  //if directory not existent
  } else {
    //try to create the new directory
    $res = JFolder::create($dir);
    //if not existent
    if (!$res ) {
      //if existent return error string '1112'
      //used by Joom_AlertErrorMessages
      $error = "1112";
      return $error;
    //if direcory succesful created
    } else {
      //copy the index.html from 'assets' in new directory and return 0
      $index = 'index.html';
      JFile::copy(JPATH_COMPONENT_SITE.DS.'assets'.DS.$index, JPath::clean($dir.DS.$index));
      return 0;
    }
  }
}

/**
 * Construct HTML list of allowed categories in backend
 *
 * @param int $cat
 * @param string $cname
 * @param string $extras
 * @param string $levellimit
 * @return string
 */
function Joom_ShowBackendAllowedCat($cat, $cname, $extras='', $levellimit='4') {
  $database = & JFactory::getDBO();

  //get all categories created in backend
  $database->setQuery("SELECT cid AS id, parent, name
      FROM #__joomgallery_catg
      WHERE owner IS NULL
      ORDER BY name");

  $items = $database->loadObjectList();
  //establish the hierarchy of the menu
  $children = array();
  //first pass - collect children
  foreach ($items as $v) {
    $pt = $v->parent;
    $list = @$children[$pt] ? $children[$pt] : array();
    array_push( $list, $v );
    $children[$pt] = $list;
  }
  //second pass - get an indent list of the items
  $list = Joom_CatTreeRecurse(0, '', array(), $children);
  // assemble menu items to the array
  $items   = array();
  $items[] = JHTML::_('select.option','', ' ');
  foreach ($list as $item) {
    $items[] = JHTML::_('select.option',$item->id, $item->treename);
  }
  asort($items);
  // build the html select list
  $parlist =Joom_SelectList2($items, $cname, 'class="inputbox" '.$extras,
                             'value', 'text', $cat);
  return $parlist;
}

/**
 * Construct an indent list of items
 * @see Joom_ShowBackendAllowedCat
 *
 * @param int $id
 * @param string $indent   indent chars
 * @param string $list
 * @param int $children
 * @param int $maxlevel recursion level
 * @param int $level
 * @param string $seperator
 * @return string indented list
 */
function Joom_CatTreeRecurse($id, $indent = "&nbsp;&nbsp;&nbsp;", $list,
                             &$children, $maxlevel = 9999, $level = 0 ,
                             $seperator = "&raquo;") {

  if (@$children[$id] && $level <= $maxlevel) {
    foreach ($children[$id] as $v) {
      $id = $v->id;
      $txt = $v->name;
      $pt = $v->parent;
      $list[$id] = $v;
      $list[$id]->treename = $indent . $txt;
      $list[$id]->children = count(@$children[$id]);
      $list = Joom_CatTreeRecurse($id, $indent . $txt . $seperator, $list,
                                  $children, $maxlevel, $level+1);
    }
  }
  return $list;
}

/**
 * Construct a multiple select list
 * @see Joom_ShowBackendAllowedCat
 * @param unknown_type $arr
 * @param unknown_type $tag_name
 * @param unknown_type $tag_attribs
 * @param unknown_type $key
 * @param unknown_type $text
 * @param unknown_type $selected
 * @return unknown
 */
function Joom_SelectList2(&$arr, $tag_name, $tag_attribs, $key, $text, $selected) {
  //set the internal pointer of $arr to its first element
  reset($arr);
  $html = "\n<select name=\"$tag_name\" $tag_attribs>";
  for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
    $k  = $arr[$i]->$key;
    $t  = $arr[$i]->$text;
    $id = @$arr[$i]->id;

    $extra  = '';
    $extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
    if (is_array( $selected)) {
      foreach ($selected as $obj) {
        $k2 = $obj;
        if ($k == $k2) {
          $extra .= ' selected="selected"';
          break;
        }
      }
    } else {
      $extra .= ($k == $selected ? ' selected="selected"' : '');
    }
    $html .= "\n\t<option value=\"" . $k . "\"$extra>" . $t . '</option>';
  }
  $html .= "\n</select>\n";
  return $html;
}

/**
 * Check if a directory only contains the index.html
 * @TODO: better handling with JFile/JFolder
 *
 * @param    string    $directory: absolute path to directory
 * @param    integer   $catid: id of category
 */
function Joom_CheckEmptyDirectory($directory, $catid) {

  //check if direcory exists, if no abort with error message
  if (!is_dir($directory)) Joom_AlertErrorMessages(0, $catid, $directory, 0);
  //open the directory
  $dir = opendir($directory);
  //if not succesful not enough write permissions, abort with error message
  if (!$dir) Joom_AlertErrorMessages(0, $catid, $directory, 0);
  $index = "index.html";
  while(false != ($entry = readdir($dir))) {
    //if entry is different from link to actual directory (.) or link to parent
    //directory (..) or index.html
    if($entry != '.' && $entry != '..' && $entry != $index) {
      //close the directory
      closedir($dir);
      //error message
      Joom_AlertErrorMessages(0, $catid, $directory, 0);
    }
  }
}

/**
 * The following function was taken from admin.frontpage.php Joomla 1.0
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * modification aHa - additional saveorder for categories
 * @see admin.joomgallery.php // only backend
 * @param int $cid
 * @param unknown_type $catg
 */
function Joom_SaveOrder(&$cid, &$catg) {
  $mainframe = & JFactory::getApplication('administrator');
  $database = & JFactory::getDBO();

  $returntask = Joom_mosGetParam('returntask', null);
  if ($returntask == 'pictures') {
    
    $total    = count($cid);
    $order    = Joom_mosGetParam('order', array(0), 'post');
    for($i=0; $i < $total; $i++ ) {
      //get catid
      $database->setQuery( "SELECT catid
          FROM #__joomgallery
          WHERE id=$cid[$i]");
      $piccatid = $database->loadResult();

      $query = "UPDATE #__joomgallery
          SET ordering = $order[$i]
          WHERE id = $cid[$i]";
      $database->setQuery( $query );
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."');
              window.history.go(-1); </script>\n";
        exit();
      }
      
      // update ordering
      $row = new mosjoomgallery($database);
      $row->load($cid[$i]);
      $row->reorder('catid='.$piccatid);
    }
    $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=pictures',JText::_('JG_NEW_ORDERING_SAVED'));
  } else {
    $total    = count($catg);
    $order    = Joom_mosGetParam('order', array(0), 'post');

    for($i=0; $i < $total; $i++ ) {
      $query = "UPDATE #__joomgallery_catg
          SET ordering = $order[$i]
          WHERE cid = $catg[$i]";
      $database->setQuery( $query );
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."');
              window.history.go(-1); </script>\n";
        exit();
      }
      // update ordering
      $row = new mosjoomgallery($database);
      $row->load($catg[$i]);
      $row->reorder('');
    }
    $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=categories',JText::_('JG_NEW_ORDERING_SAVED'));
  }
}

/**
 * Callback function for sorting the array in batchupload by filename
 * @see admin.upload.class.php
 * @TODO implement in upload class / get rid of preg_replace
 * @param string $a
 * @param string $b
 * @return int
 */
function Joom_SortBatch($a, $b) {
  $searchstring = "/[^0-9]/";
  $a = preg_replace($searchstring, '', $a['filename']);
  $b = preg_replace($searchstring, '', $b['filename']);
  if ($a<$b) {
    return 1;
  } elseif ($a>$b) {
    return -1;
  }else {
    return 0;
  }
}

/**
 * Check the upload time of picture and determine if it is within a setted span
 * of time and so marked as NEW
 *
 * @param    integer    $uptime: upload time in seconds
 * @param    integer    $daysnew: span of time in days
 * @return   string.....$isnew: path to new image or ''
 */
function Joom_CheckNew($uptime, $daysnew) {
  //gets the seconds from starting time of Unix Epoch (January 1 1970 00:00:00 GMT)
  //to now in seconds
  $thistime   = time();
  //calculates the seconds according to days setted for new
  //see configuration manager
  $timefornew = 86400*$daysnew;
  //if span of time since upload is lower than span of time setted in config
  if (($thistime - $uptime) < $timefornew) {
    //show the new image
    //TODO DS
    $isnewpng = _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/new.png';
    $isnew = '<img src="'.$isnewpng.'" class="jg_newpic pngfile jg_icon" alt="New" />';
  //otherwise
  } else {
    $isnew = '';
  }
  return $isnew;
}

/**
 * Check pictures of category and evtl. subcategories
 * call Joom_CheckNew() to decide if NEW
 * recursion call, premature terminate if 'NEW' applies to
 *
 * @param string $catids_values id's of cats 'x,y'
 * @return string.....$isnew
 */
function Joom_CheckNewCatg($catids_values) {
  $config = Joom_getConfig();
  $database = & JFactory::getDBO();
  $user = & JFactory::getUser();

  $isnewcat = "";
  //search in db the categories in $catids_values
  $database->setQuery( "SELECT MAX(imgdate)
      FROM #__joomgallery AS a
      LEFT JOIN #__joomgallery_catg AS c ON c.cid=a.catid
      WHERE a.catid in ($catids_values)
      AND a.published = '1' AND a.approved='1'
      AND c.access <= ".$user->get('aid')." AND c.published = '1'");
  $maxdate = $database->loadResult();
  if ($database->getErrorNum()) {
    //TODO error handling
    echo $database->stderr(true);
  }

  //if maxdate = NULL no picture found
  //otherwise check the date to 'new'
  if ( $maxdate != NULL ) {
    $isnewcat = Joom_CheckNew($maxdate, $config->jg_catdaysnew);
    //terminate if 'new' found
    if ( $isnewcat != "" ){
      return $isnewcat;
    }
  }

  //no picture found in cat marked as new
  //check subcategories with $parent=cid
  $database->setQuery( "SELECT cid
      FROM #__joomgallery_catg
      WHERE parent in ($catids_values)
      AND access <= ".$user->get('aid')." AND published = '1' " );

  //if 0 rows no existent subcategories
  //terminate with return of new=""
  $catids = $database->loadResultArray();
  if ($database->getErrorNum()) {
    echo $database->stderr(true);
  }

  if (count($catids) == 0) {
    return "";
  }
  //split array in comma separated string
  $catids_values = implode (",",$catids);

  //call function again and check 'new'
  $isnewcat = Joom_CheckNewCatg($catids_values);

  //if no new found at all
  //return empty string
  return $isnewcat;
}

/**
* Constructs the Pathway/Breadcrumbs for the category links
* @param $cat CategoryID db->cid
* @return $pathName fully constructed HTML pathname with links
*/
function Joom_CategoryPathLink($cat,$with_home = true) {
  $database = & JFactory::getDBO();
  $user = & JFactory::getUser();

  $cat=intval( $cat );
  $parent_id=$cat;

  while ( $parent_id ) {
    $query="SELECT *
        FROM #__joomgallery_catg
        WHERE cid=$cat AND access <= '".$user->get('aid')."'";
    $database->setQuery( $query );
    $result = $database->loadObject();
    if (!isset($result)) return '';
    $parent_id = $result->parent;
    $cid = $result->cid;
    $catname = $result->name;
    $name='    <a href="'. JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$cat._JOOM_ITEMID).'" class="jg_pathitem">' . $catname  . '</a>' . "\n";
    // write path
    if ( empty( $path ) ) {
      $path = $name;
    } else {
      $path = $name . '    &raquo; '."\n" . $path;
    }
    // next loop
    $cat=$parent_id;
  }
  if($with_home) {
    $home = '    <a href="'. JRoute::_('index.php?option=com_joomgallery&view=gallery'._JOOM_ITEMID) . '" class="jg_pathitem">' . JText::_('JG_HOME') . '</a>';
    $pathName = $home . "\n" . '    &raquo; '."\n" . $path . ' ';
  } else {
    $pathName = $path;
  }
  return $pathName;
}

/**
 * Counts all pics in a category and their subcategories
 * for DefaultView and CategoryView
 * @param int $cat CategoryID db->cid
 * @return int $val Number of pics in categories->subcategories....
 */
function Joom_GetNumberOfLinks($cat) {
  $database = & JFactory::getDBO();
  $user = & JFactory::getUser();

  $queue[] = intval($cat);

  //iteration for all subcategories, collect the category id's for later counting
  while (list($key, $cat) = each($queue)) {
    // get children
    $query="SELECT cid
        FROM #__joomgallery_catg
        WHERE parent=$cat AND published=1";
    $database->setQuery($query);
    $result = $database->loadResultArray();
    // put them in queue
    foreach ($result as $row) {
      $queue[] = $row;
    }
  }
  $array_values = implode (",",$queue);
  //count all pictures wich are in the categories
  $query="SELECT count(id) as count
      FROM #__joomgallery
      WHERE published = 1 AND approved = 1 AND catid in ($array_values)";

  $database->setQuery( $query );
  $val = $database->LoadResult();
  return $val;
}

/**
 * Construct page title
 *
 * @param string $text
 * @param string $catname
 * @param string $imgtitle
 * @return string modified title
 */
function Joom_MakePagetitle($text,$catname,$imgtitle) {
  preg_match_all("/(\[\!.*?\!\])/i",$text,$results);
  define('JGS_CATEGORY',JText::_('JGS_CATEGORY'));
  define('JGS_PICTURE',JText::_('JGS_PICTURE'));
  for($i=0;$i<count($results[0]);$i++) {
    $replace = str_replace("[!","",$results[0][$i]);
    $replace = str_replace("!]","",$replace);
    $replace = trim($replace);
    $replace2 = str_replace("[!","\[\!",$results[0][$i]);
    $replace2 = str_replace("!]","\!\]",$replace2);
    $text = preg_replace("/(".$replace2.")/ie",$replace,$text);
  }
  $text = str_replace("#cat",$catname,$text);
  $text = str_replace("#img",$imgtitle,$text);
  return $text;
}

/**
 * All categories and their subcategories with published pictures
 * @param int $cat catid db->cid
 * @param int showrandom random choice setted in backend
 * @return $allsubcats chosen categories
 */
function Joom_GetAllSubCategories ($cat, $showrandom) {
  $database = & JFactory::getDBO();
  $user = & JFactory::getUser();

  $cat=intval($cat);
  $allsubcats = array();

  //read all cats: cid und parent in static array
  //if not already done execute DB query,
  //according to $showrandom only special cats
  static $allcatsread=false;
  static $allcats = array();

  //showrandom=1 only check if there are pictures in cat
  if ( $showrandom == 1 ) {
    $database->setQuery("SELECT count(id)
        FROM #__joomgallery
        WHERE $cat = catid");
    $count = $database->loadResult();

    if ($count > 0) {
      $allsubcats[] = $cat;
    }
    return $allsubcats;
  }

  //$showrandom = 2 (only subcats) or 3 = cat and the subcats
  //DB-query only if array empty
  if (!$allcatsread){
    $database->setQuery("SELECT ca.cid,ca.parent,IFNULL(count(p.id),0) as piccount
        FROM #__joomgallery_catg AS ca
        LEFT JOIN #__joomgallery AS p
        ON p.catid = ca.cid
        WHERE ca.published = '1'
        AND ca.access <= '".$user->get('aid')."'
        AND (isnull(p.published) OR p.published=1)
        AND (isnull(p.approved) OR p.approved=1)
        /* unpublished approved picture for category, only working with own coice */
        OR ((isnull(p.published) OR p.published=0) AND (isnull(p.approved) OR p.approved=1))
        GROUP BY ca.cid
        ORDER BY cast(parent as unsigned)");

    $allcats=$database->loadObjectList("cid");
    $allcatsread=true;
  }

  //analyze the array of all cats
  //if $showrandom=2, check only the subcats
  //if $showrandom>2, check cat and subcats
  if ($showrandom>2){
    //only add cat if there are pictures
    if (array_key_exists($cat,$allcats) && $allcats[$cat]->piccount > 0) {
      $allsubcats[] = $cat;
    }
  }

  $alldone=false;
  $workparentarray=array();
  $workparentarray[]=$cat;
  $workchildsarray=array();

  while (!$alldone) {
    //check all subcats from cat and add them in $allsubcats and $workparentarray
    $maxparent=max($workparentarray);

    foreach ($allcats as $key => $catid){
      //break the iteration when beyond parent
      if ($catid->parent > $maxparent){
        break;
      }
      if (in_array($catid->parent,$workparentarray)){
        $workchildsarray[] = $key;
      }
    }
    if (count($workchildsarray)) {
      $allsubcats=array_merge($allsubcats,$workchildsarray);
      $workparentarray=$workchildsarray;
    } else {
      //no more cats to check
      $alldone=true;
    }
    $workchildsarray=array();

  }
  //remove from collected cats if not including any pictures
  if (count($allsubcats)) {
    $tempcats=array();
    foreach($allsubcats as $tempcat) {
      if ($allcats[$tempcat]->piccount > 0) {
        $tempcats[] = $tempcat;
      }
    }
    $allsubcats=$tempcats;
    $tempcats=array();
  }

  return $allsubcats;
}

/**
 * Counts the hits of all pics in a category and their subcategories
 * @param $allsubcats array of all subcats from the category
 * @return $numberoftotalhits
 */
function Joom_GetTotalHits ($allsubcats) {
  $database = & JFactory::getDBO();
  $numberoftotalhits=0;

  if ( $allsubcats ) {
    $array_values = implode (",", $allsubcats);
    $database->setQuery("SELECT sum(imgcounter) as result
        FROM #__joomgallery WHERE catid in ($array_values)");
    $numberoftotalhits = $database->LoadResult();
  } else {
    $numberoftotalhits = 0;
  }

  if ($numberoftotalhits == 0 || $numberoftotalhits == NULL ) {
    $numberoftotalhits = 0;
  }

  return $numberoftotalhits;
}

/**
 * Construct the pagination in detail/category/subcatagory view
 * @TODO: replace german variable names
 * @param string $url base url according to view, completion in this function
 * @param int $pageCount, total count of all pages
 * @param int $currentPage actual page
 * @param string $anchortag anchor to append
 * @return string, all completed url to pages
 */
function Joom_GenPagination($url, &$pageCount, &$currentPage,$anchortag) {
  $retVal = '';
  $ellipsis = "&hellip;";
  $aktpage=2;

  //variable for actual page found and assembled
  $currItemfound=false;

  //work on left edge
  if( $currentPage == 1 ) {
    $currItemfound=true;
    $retVal .= '<span class="jg_pagenav">[1]</span>&nbsp;';
    $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url,2)).$anchortag.'" title="'.JText::_('JG_PAGE').' 2" class="jg_pagenav">2</a>'."\n";
  } else {
    //actual page not 1
    $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, 1)).$anchortag.'" title="'.JText::_('JG_PAGE').' 1" class="jg_pagenav">1</a>'."\n";
    if ($currentPage==2) {
      $currItemfound=true;
      $retVal .= '&nbsp;<span class="jg_pagenav">[2]</span>';
    } else {
      $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url,2)).$anchortag.'" title="'.JText::_('JG_PAGE').' 2" class="jg_pagenav">2</a>'."\n";
    }
  }
  //range left from act. page to 1 not assembled yet
  if (!$currItemfound) {
    //Construct pages left to act. page
    //according to difference to left implement jumps
    //if difference to act. site too low, output them exactly
    if ($currentPage - $aktpage < 6) {
      $aktpage++;
      for ($i = $aktpage;$i < $currentPage;$i++){
        $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, $i)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.$i.'" class="jg_pagenav">'.$i.'</a>'."\n";
        $aktpage++;
      }
    } else {
      //otherwise output of remaining links evt. in steps
      //and in addition output of 2 left neighbours
      //completion of range at position 3 to (act. page -3)
      $endbereich=$currentPage-3;
      $jump=ceil(($endbereich-5)/4);
      if ($jump==0) $jump=1;
      $aktpage=$aktpage+$jump;
      for ($i = 1;$i < 4;$i++){
        if ($jump == 1) {
          $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, $aktpage)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.$aktpage.'" class="jg_pagenav">'.$aktpage.'</a>'."\n";
        } else {
          $retVal .= $ellipsis.'&nbsp;<a href="'.JRoute::_(sprintf($url, $aktpage)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.$aktpage.'" class="jg_pagenav">'.$aktpage.'</a>'."\n";
        }
        $aktpage=$aktpage+$jump;
      }
      if ($aktpage != ($currentPage-2) ) $retVal .= $ellipsis;
      //Output of 2 pages left beside act. page
      $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, $currentPage-2)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.($currentPage-2).'" class="jg_pagenav">'.($currentPage-2).'</a>'."\n";
      $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, $currentPage-1)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.($currentPage-1).'" class="jg_pagenav">'.($currentPage-1).'</a>'."\n";
    }
    //actual page
    $retVal .= '&nbsp;<span class="jg_pagenav">['.$currentPage.']</span>&nbsp;';
    $currItemfound=true;
    $aktpage=$currentPage;
  }
  //actual page found, right beside construct 2 pages
  //max to end
  if ($pageCount-$aktpage< 3) {
    $anzahl=$pageCount-$aktpage;
  } else {
    $anzahl=2;
  }
  $aktpage++;
  for ($i = 1;$i <= $anzahl;$i++){
    $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, $aktpage)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.$aktpage.'" class="jg_pagenav">'.$aktpage.'</a>'."\n";
    $aktpage++;
  }
  if ($aktpage == $pageCount) {
    $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url,$aktpage)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.$aktpage.'" class="jg_pagenav">'.$aktpage.'</a>'."\n";
    return $retVal;
  }
  //all ready
  if ($aktpage > $pageCount) {
    return $retVal;
  }
  //if only 3 pages to end remain
  if ($aktpage < $pageCount && ($pageCount-$aktpage) < 7) {
    for ($i = $aktpage;$i <= $pageCount;$i++){
      $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, $aktpage)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.$aktpage.'" class="jg_pagenav">'.$aktpage.'</a>'."\n";
      $aktpage++;
    }
  } else {
      //Output of remaining pages in steps
      //and in addition output of last page and the neighbour left
      //Complete the range (act. page+3) to (last page - 3)
      $startbereich=$aktpage;
      $endbereich=$pageCount-3;
      $jump=ceil(($endbereich-$startbereich)/4);
      $aktpage=$aktpage+$jump;
      for ($i = 1;$i < 4;$i++){
        if ($jump == 1) {
          $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, $aktpage)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.$aktpage.'" class="jg_pagenav">'.$aktpage.'</a>'."\n";
        } else {
          $retVal .= $ellipsis.'&nbsp;<a href="'.JRoute::_(sprintf($url, $aktpage)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.$aktpage.'" class="jg_pagenav">'.$aktpage.'</a>'."\n";
        }
        $aktpage=$aktpage+$jump;
      }
      $retVal .= $ellipsis;
      //Output of penultimate
      $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, $pageCount-1)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.($pageCount-1).'" class="jg_pagenav">'.($pageCount-1).'</a>'."\n";
      //Output of last
      $retVal .= '&nbsp;<a href="'.JRoute::_(sprintf($url, $pageCount)).$anchortag.'" title="'.JText::_('JG_PAGE').' '.($pageCount).'" class="jg_pagenav">'.($pageCount).'</a>'."\n";
  }
  return $retVal;
}

/**
 * Update of category path in DB for subcategories if a parent category has
 * been moved or the name changed
 * recursive call to each count of depth
 *
 * @param    integer  $catids_values: ID(s) of the modifie category
 * @param    string   $oldpath: former rel. category path
 * @param    string   $newpath: actual rel. category path
* */
function Joom_UpdateNewCatpath($catids_values,&$oldpath,&$newpath) {
  $database = & JFactory::getDBO();

  //query for subcategories with parent in $catids_values
  $database->setQuery( "SELECT cid
      FROM #__joomgallery_catg
      WHERE parent in ($catids_values) ");

  $subcatids = $database->loadResultArray();

  if ($database->getErrorNum()) {
    echo $database->stderr(true);
  }
  // nothing found, return
  if (count($subcatids) == 0){
    return;
  }

  $row = new mosCatgs($database);
  foreach ($subcatids as $subcatid) {
    $row->load($subcatid);
    $catpath = $row->catpath;

    //replace former category path with actual one
    $catpath = str_replace($oldpath.'/',$newpath.'/',$catpath);

    //and save it
    $row->catpath = $catpath;
    if (!$row->store()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
  }

  //split the array in comme seperated string
  $catids_values = implode (",",$subcatids);

  //call again with subcategories as parent
  Joom_UpdateNewCatpath($catids_values,$oldpath,$newpath);
}

/**
 * link to german forum.
 * http://www.joomlaportal.de/505887-post4.html
 *
 * EN:
 * The following two functions are adopted from an forum post from ecomeback
 * Thank you very much at this point!
 *
 * DE:
 * Die folgenden zwei Funktionen wurden einem Forums-Beitrag von ecomeback
 * entnommen.
 * Vielen Dank an dieser Stelle!
 *
 * Create an Object from an mixed var
 * we "need" this for the multiple joomla select list
 *
 * @param $row - mixed - the mixed var you want to convert
 * @param $take_key - bool - set true if you want to get the key into value object element
 * @param $value_name - string - the name of the value object element
 * @param $text_name - string - the name of the text object element
 *
 */
function Joom_CreateObject($row, $take_key=false, $value_name='value', $text_name='text'){

  if(!is_object($row)) {
    if(is_array($row)){
      $obj_array = array();
      foreach($row as $key => $value) {
        $obj = new stdClass;
        $value = trim($value);
        $obj->$value_name     = $take_key ? $key : $value;
        $obj->$text_name      = $value;
        $obj_array[$key]     = $obj;
      }
      return $obj_array;
    } else {
      $row = trim($row);
      $obj->$value_name     = $row;
      $obj->$text_name      = $row;
      return $obj;
    }
  }
  return $row;
}


/**
 * Create an Object Array from string
 * we need this for the multiple joomla select list
 *
 * @param $row - mixed - the mixed var you want to convert
 * @param $default - mixed - the returned var if row is empty
 * @param $explode - bool - explode string
 * @param $con - string - the string connector
 *
 */
function Joom_CreateArrayObject( $row, $default=array(), $explode=true, $con=',' ) {

  if(empty($row)) {
    return Joom_CreateObject($default);
  }
  if(!is_object($row)){
    if(!is_array($row) && $explode){
      $rows = explode( $con, $row );
      return Joom_CreateObject($rows);
    }
  return Joom_CreateObject($row);
  }
  return array($row);
}


/******************************************************************************\
*                                    Errors                                    *
\******************************************************************************/

/**
 * Outputs an error string according to an specific error number
 * @TODO avoid exit
 *
 * @param int $eid
 * @param int/array $catid
 * @param string $dir
 * @param string $name
 */
function Joom_AlertErrorMessages($eid, $catid, $dir, $name ) {

  $error[$eid] = JText::_('JGA_ALERT_ERROR_'.$eid,true);

  $output = JText::_('JGA_ALERT_ERROR',true)
    .JText::_('JGA_ALERT_ERROR_BREAK',true)
    .JText::_('JGA_ALERT_ERROR_BR',true)
    .JText::_('JGA_ALERT_ERROR_BR',true)
    .$error[$eid]
    .JText::_('JGA_ALERT_ERROR_BR',true)
    .JText::_('JGA_ALERT_ERROR_BR',true)
    .JText::_('JGA_ALERT_ERROR_NUMBER',true)
    .$eid
    .JText::_('JGA_ALERT_ERROR_BR',true);

  if ($catid ) {
    if (is_array($catid)) {
      $catids = implode(',',$catid);
    } else {
      $catids = $catid;
    }
    $output.=JText::_('JGA_ALERT_ERROR_CATID',true);
    $output.=$catids;
    $output.=JText::_('JGA_ALERT_ERROR_BR',true);
  }

  if ($name ) {
    $output.=JText::_('JGA_ALERT_ERROR_NAME',true);
    $output.=$name;
    $output.=JText::_('JGA_ALERT_ERROR_BR',true);
  }

  if ($dir ) {
    $output.=JText::_('JGA_ALERT_ERROR_DIRECTORY',true);
    $output.=$dir;
    $output.=JText::_('JGA_ALERT_ERROR_BR',true);
  }

  $output.=JText::_('JGA_ALERT_ERROR_BR',true);
  $output.=JText::_('JGA_ALERT_ERROR_SEE_FAQS',true);
  $output.=JText::_('JGA_ALERT_ERROR_BR',true);
  $output.=JText::_('JGA_ALERT_ERROR_FAQ',true).$eid.JText::_('JGA_ALERT_ERROR_HTML',true);
  $output.=JText::_('JGA_ALERT_ERROR_BR',true);
  $output.=JText::_('JGA_ALERT_ERROR_BR',true);
  $output.=JText::_('JGA_ALERT_ERROR_NOTE',true);
  $output.=JText::_('JGA_ALERT_ERROR_BR',true);
  $output.=JText::_('JGA_ALERT_ERROR_FORUM',true);
  $output.=JText::_('JGA_ALERT_ERROR_BR',true);


  echo "<script> alert('".str_replace("\n",'\n',$output)."'); window.history.go(-1); </script>\n";
  exit();
}

/**
 * Creates display string (HTML) for a user name to be displayed,
 * links to CB / CBe / JomSocial if configured in global JG config.
 *
 * Link to GalleryTab (if available) optional *
 * @param int $userId
 * @param bool $linkToTab
 * @return string
 */
function Joom_GetDisplayName($userId, $linkToTab = true){
  $config = Joom_getConfig();
  $mainframe = & JFactory::getApplication('site');

  if (is_null($userId))
    return JText::_('JGS_NO_DATA');
  $userId = intval($userId);

  $user = & JFactory::getUser($userId);
  if ($config->jg_realname) {
    $username = $user->get('name');
  } else {
    $username = $user->get('username');
  }

  $return = '';
  if ($config->jg_combuild){
   //directly link to a user gallery tab, if present:
    if($linkToTab && file_exists(JPATH_ROOT.DS.'components'.DS.'com_comprofiler'.DS.'plugin'.DS.'user'.DS.'plug_joomgallery-tab'.DS.'cb.joomtab.php')) {
      $tablink = '&amp;tab=getjoomtab';
    } else if($linkToTab && file_exists(JPATH_ROOT.DS.'components'.DS.'com_comprofiler'.DS.'plugin'.DS.'user'.DS.'plug_gallery-tab'.DS.'cb.gallerytab.php')) {
      $tablink = '&amp;tab=getgallerytab';
    } else {
      $tablink = '';
    }
    // TODO: Tab in CBE available?

    switch ($config->jg_combuild){
      // Joomlapolis Community Builder and old CBE
      case 1:
        $profile_url = 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user='.$userId.$tablink;
        break;
      //new CBe
      case 2:
        $profile_url = 'index.php?option=com_cbe&amp;task=userProfile&amp;user='.$userId;
        break;
      //JomSocial
      case 3:
        $profile_url = 'index.php?option=com_community&amp;view=profile&amp;userid='.$userId;
        break;
      default:
        $profile_url = '';
    }


    //TODO not avail. in backend
    if (defined('_JOOM_ITEMID')) {
      $profile_url = JRoute::_($profile_url);
    }

    $return .= '<a href ="'.$profile_url.'">';
  }
  $return .= $username;

  if ($config->jg_combuild > 0)
    $return .="</a>";

  return $return;
}

/**
 * Helper function to calculate the width of a nameshield,
 * returns char length of username
 *
 * @param int $userId
 * @return int length of name
 */
function Joom_GetDisplayNameLength($userId){
  $config = Joom_getConfig();
  $userId = intval($userId);
  $user = & JFactory::getUser($userId);
  if ($config->jg_realname) {
    return strlen($user->get('name'));
  } else {
    return strlen($user->get('username'));
  }
}

/**
 * Returns true if $string is valid UTF-8 and false otherwise.
 * from http://w3.org/International/questions/qa-forms-utf-8.html *
 * @since        1.14
 * @param string $string     string to be tested
 * @return bool  true=is utf-8
 */
function Joom_IsUtf8($string) {
  return preg_match('%^(?:
        [\x09\x0A\x0D\x20-\x7E]            # ASCII
      | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
      |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
      | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
      |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
      |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
      | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
      |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
  )*$%xs', $string);

}

/**
 * Encodes an ISO-8859-1 string to UTF-8
 * recursive call
 * @param string/array $input strings(s) to be modified
 * @param unknown_type $encode_keys
 * @return string
 */
function Joom_Utf8EncodeMix($input, $encode_keys=false) {
  if(is_array($input)) {
    $result = array();
    foreach($input as $k => $v) {
      $key = ($encode_keys)? utf8_encode($k) : $k;
      $result[$key] = Joom_Utf8EncodeMix( $v, $encode_keys);
    }
  } else {
    $result = utf8_encode($input);
  }

  return $result;
}

/**
 * Returns all available smileys in an array
 *
 * @return array An array with the smileys
 */
function Joom_GetSmileys(){
  $config = Joom_GetConfig();

  $path = _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/smilies/'. $config->jg_smiliescolor . '/';

  $smiley                     = array();
  $smiley[':smile:']          = $path.'sm_smile.gif';
  $smiley[':cool:']           = $path.'sm_cool.gif';
  $smiley[':grin:']           = $path.'sm_biggrin.gif';
  $smiley[':wink:']           = $path.'sm_wink.gif';
  $smiley[':none:']           = $path.'sm_none.gif';
  $smiley[':mad:']            = $path.'sm_mad.gif';
  $smiley[':sad:']            = $path.'sm_sad.gif';
  $smiley[':dead:']           = $path.'sm_dead.gif';

  if($config->jg_anismilie){
    $smiley[':yes:']            = $path.'sm_yes.gif';
    $smiley[':lol:']            = $path.'sm_laugh.gif';
    $smiley[':smilewinkgrin:']  = $path.'sm_smilewinkgrin.gif';
    $smiley[':razz:']           = $path.'sm_bigrazz.gif';
    $smiley[':roll:']           = $path.'sm_rolleyes.gif';
    $smiley[':eek:']            = $path.'sm_bigeek.gif';
    $smiley[':no:']             = $path.'sm_no.gif';
    $smiley[':cry:']            = $path.'sm_cry.gif';
  }

  $dispatcher = & JDispatcher::getInstance();
  $dispatcher->trigger('onJoomGetSmileys', array(&$smiley));

  return $smiley;
}

/**
 * At the moment just a wrapper function for JModuleHelper::getModules()
 *
 * @param string $pos position name
 * @return array An array of module objects
 */
function Joom_GetModules($pos){

  $joomambit  = Joom_Ambit();
  $func       = $joomambit->get('func', '');

  $position = 'jg_'.$pos;
  $modules  = &JModuleHelper::getModules($position);

  $views = array( ''              => 'gal',
                  'viewcategory'  => 'cat',
                  'detail'        => 'dtl',
                  'special'       => 'spc',
                  'showfavourites'=> 'fav',
                  'userpanel'     => 'usp',
                  'showupload'    => 'upl'
                );
  if(isset($views[$func])){
    $position = $position.'_'.$views[$func];
    $ind_mods = &JModuleHelper::getModules($position);
    $modules = array_merge($modules, $ind_mods);
  }

  if($func != ''){
    $ind_mods = &JModuleHelper::getModules($position.'_'.$func);
    $modules  = array_merge($modules, $ind_mods);
  }
  return $modules;
}

/**
 * returns all downloadable extensions developed by JoomGallery::ProjectTeam
 * with some additional information like the current version number or a
 * short description of the extension
 *
 * @return array two-dimensional array with extension information
 */
function Joom_GetAvailableExtensions() {

  static $extensions;

  if(isset($extensions)){
    return $extensions;
  }

  $site   = 'http://www.en.joomgallery.net';
  $site2  = 'http://en.joomgallery.net';
  $rssurl = $site.'/components/com_newversion/rss/extensions.rss';

  // get RSS parsed object
  $options = array();
  $options['rssUrl']       = $rssurl;
  $options['cache_time']  = 24*60*60;

  $rssDoc = & JFactory::getXMLparser('rss', $options);

  $extensions = array();
  if($rssDoc != false) {

    $items = $rssDoc->get_items();

    foreach($items as $item){
      $name = $item->get_title();
      $category = $item->get_category();
      $type = $category->get_term();
      switch($type){
        case 'general':
          $description  = $item->get_description();
          $link         = $item->get_link();
          if(!is_null($description) AND $description != ''){
            $extensions[$name]['description']  = $description;
          }
          if(!is_null($link) AND $link != $site AND $link != $site2){
            $extensions[$name]['downloadlink'] = $link;
          }
          break;
        case 'version':
          $version  = $item->get_description();
          $link     = $item->get_link();
          if(!is_null($version) AND $version != ''){
            $extensions[$name]['version']      = $version;
          }
          if(!is_null($link) AND $link != $site AND $link != $site2){
            $extensions[$name]['releaselink']  = $link;
          }
          break;
        case 'autoupdate':
          $xml  = $item->get_description();
          $link = $item->get_link();
          if(!is_null($xml) AND $xml != ''){
            $extensions[$name]['xml']        = $xml;
          }
          if(!is_null($link) AND $link != $site AND $link != $site2){
            $extensions[$name]['updatelink'] = $link;
          }
          break;
        default:
          break;
      }
    }
  }
  return $extensions;
}

/**
 * returns all installed JoomGallery extensions and JoomGallery itself
 * with additional information provided by Joom_GetAvailableExtensions
 *
 * @param   array extensions provided by Joom_GetAvailableExtensions
 * @return  array two-dimensional array with extension information
 */
function Joom_GetInstalledExtensions($extensions = null){

  static $installed_extensions;

  if(isset($installed_extensions)){
    return $installed_extensions;
  }

  if(is_null($extensions)){
    $extensions = Joom_GetAvailableExtensions();
  }

  $installed_extensions = array();
  foreach($extensions as $name => $extension){
    if(!isset($extension['xml'])){
      continue;
    }
    $xml_file = JPath::clean(JPATH_ROOT . DS . $extension['xml']);
    if(file_exists($xml_file)){
      $installed_extensions[$name] = $extension;

      $xml = & JFactory::getXMLParser('simple');
      $xml->loadFile($xml_file);
      // TODO: error handling, maybe there's no 'version' tag?
      $version_tag = $xml->document->getElementByPath('version');
      $installed_version = $version_tag->data();
      $installed_extensions[$name]['installed_version'] = $installed_version;
    }
  }
  return $installed_extensions;
}

/**
 * compares all installed extension versions with the current ones
 * and returns all dated JoomGallery extensions and JoomGallery itself
 * with additional information provided by Joom_GetAvailableExtensions
 *
 * @param   array installed extensions provided by Joom_GetInstalledExtensions
 * @return  array two-dimensional array with extension information
 */
function Joom_CheckUpdate($extensions = null){

  static $dated_extensions;

  if(isset($dated_extensions)){
    return $dated_extensions;
  }

  if(is_null($extensions)){
    $extensions = Joom_GetInstalledExtensions();
  }

  $dated_extensions = array();
  foreach($extensions as $name => $extension){
    // TODO: Error handling, check whether keys exist
    if($extension['version'] != $extension['installed_version']){
      $dated_extensions[$name] = $extension;
    }
  }
  return $dated_extensions;
}

/**
 * returns the currently installed version of JoomGallery
 *
 * @return string Version
 */
function Joom_GetGalleryVersion(){

  static $version;

  if(!isset($version)){
    $config = Joom_GetConfig();

    // do not read RSS file if update check is disabled
    if($config->jg_checkupdate){

      $extensions = Joom_GetInstalledExtensions();

      if(isset($extensions['JoomGallery']['installed_version'])){
        $version = $extensions['JoomGallery']['installed_version'];
      } else {
        $version = 'not found';
      }

    } else {
      $xml_file = JPATH_ADMINISTRATOR . DS . 'components' .DS. 'com_joomgallery' .DS .'joomgallery.xml';
      if(file_exists($xml_file)){

        $xml = & JFactory::getXMLParser('simple');
        $xml->loadFile($xml_file);
        // TODO: error handling, maybe there's no 'version' tag?
        $version_tag = $xml->document->getElementByPath('version');
        $version = $version_tag->data();
      }
    }
  }

  return $version;
}

/**
 * Stub for future holding all global gallery settings within actual ambit
 * not the config
 * static object
 *
 * @return object
 */
function Joom_Ambit() {
  static $joomambit;
  if(!isset($joomambit)){
    $joomambit = new Joom_Ambit();
  }
  return $joomambit;
}
class Joom_Ambit extends JObject{
  function __construct(){
    //TODO: parent::__construct necessary?
    parent::__construct();
  }

  function get($key, $default = null){

    $value = parent::get($key);

    //workaround for PHP4
    if(is_null($value)){
      $mainframe = &JFactory::getApplication('site');
      $value = $mainframe->getUserState('joom.ambit.'.$key);
    }

    if(is_null($value)){
      return $default;
    } else {
      return $value;
    }
  }

  function set($key, $value){

    $previous = parent::set($key, $value);

    //workaround for PHP4
    if(version_compare( phpversion(), '5.0' ) < 0){
      $mainframe = &JFactory::getApplication('site');
      $previous = $mainframe->setUserState('joom.ambit.'.$key, $value);
    }

    return $previous;
  }
}
?>
