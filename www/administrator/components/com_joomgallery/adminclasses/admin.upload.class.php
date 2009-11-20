<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/adminclasses/admin.upload.class.php $
// $Id: admin.upload.class.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC' ) or die( 'Direct Access to this location is not allowed.');

/**
* Upload functions for backend
* - Batch (Zip)
* - FTP
* - Single upload
* - JAVA Applet (jupload)
*/
class Joom_Upload {
  var $debug;
  var $batchul;
  var $gentitle;
  var $gendesc;
  var $photocred;
  var $filecounter;
  var $file_delete;
  var $original_delete;
  var $create_special_gif;
  var $arrscreenshot;
  var $zippack;
  var $ftpfiles;
  var $subdirectory;
  var $imgname_separator;

  /**
   * Constructor
   *
   * @param string $task type of upload
   * @param int $catid destination category
   * @return Joom_Upload
   */
  function Joom_Upload($task, $catid) {
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
    jimport('joomla.filesystem.file');

    $this->debug               = JRequest::getInt('debug', 0);
    $this->batchul               = JRequest::getInt('batchul', 0);
    $this->subdirectory        = Joom_mosGetParam('subdirectory', DS, 'post');
    $this->gentitle            = Joom_mosGetParam('gentitle', '', 'post');
    $this->gendesc             = Joom_FixUserEntrie(Joom_mosGetParam('gendesc', ''));
    $this->photocred           = Joom_FixUserEntrie(Joom_mosGetParam('photocred', ''));

    $this->filecounter         = Joom_mosGetParam('filecounter', '', 'post');
    $this->file_delete         = Joom_mosGetParam('file_delete', '', 'post');
    $this->original_delete     = Joom_mosGetParam('original_delete', '', 'post');
    $this->create_special_gif  = Joom_mosGetParam('create_special_gif', '', 'post');
    $this->arrscreenshot       = Joom_mosGetParam('arrscreenshot', '', 'files');
    $this->zippack             = Joom_mosGetParam('zippack', '', 'files');
    $this->ftpfiles            = Joom_mosGetParam('ftpfiles', '', 'post');

    $this->imgname_separator   = '_';

    switch($task) {
      //Single upload
      case 'uploadhandler':
        $this->Upload_Singles_Backend($catid);
        break;
      //ZIP upload
      case 'batchuploadhandler':
        $this->Upload_Batch($catid);
        break;
      //FTP upload
      case 'ftpuploadhandler':
        $this->Upload_FTP($catid);
        break;
      //JAVA upload
      case 'juploadhandler_receive':
        $this->Upload_AppletReceive_Backend($catid);
        break;
      default:
        jexit('Wrong Task');
        break;
    }
  }

  
  /**
   * Extract pictures from zip
   *
   * @param int $catid id of destination category
   */
  function Upload_Batch($catid) {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
    $debugoutput='';

    if (JFolder::exists(JPATH::clean(JPATH_ROOT.DS.$config->jg_pathtemp))) {
      $temp_dir = $config->jg_pathtemp;
    } else {
      $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=batchupload', JText::_('JGA_ERROR_TEMP_MISSING'), 'error');
    }

    //include zip class
    require_once(JPATH_ADMINISTRATOR.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php');

    //check existence of uploaded zip
    if (!JFile::exists($this->zippack["tmp_name"])) {
      $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=batchupload', JText::_('JGA_ERROR_FILE_NOT_UPLOADED'), 'error');
    }

    //make temp path writeable if it is not, workaround for servers with wwwrun-problem
    if(!is_writeable(JPath::clean(JPATH_ROOT.DS.$temp_dir))){
      Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$temp_dir), 0777);
      $permissions_changed = true;
    }

    //Create ZIP object, make array containing file info, and extract files to temporary location
    $this->zippack = $this->zippack["tmp_name"];
    $zipfile = new PclZip($this->zippack);
    $ziplist=$zipfile->extract(PCLZIP_OPT_PATH,JPath::clean(JPATH_ROOT.DS.$temp_dir),
                               PCLZIP_OPT_REMOVE_ALL_PATH,
                               PCLZIP_OPT_BY_PREG, "/^(.*).((jpg)|(JPG)|(jpeg)|(JPEG)|(jpe)|(JPE)|(png)|(PNG)|(gif)|(GIF))$/");

    //set back temp path permissions if they were changed before
    if(isset($permissions_changed)){
      Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$temp_dir), 0755);
    }

    //check error code of extraction
    if ($zipfile->error_code != 1) {
      $ziperror = str_replace("'","",$zipfile->errorInfo());
      $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=batchupload', $ziperror, 'error');
    }

    $sizeofzip = sizeof($ziplist);

    //For each file extracted from zip get original filename and create unique filename
    //copy to new location, delete file in temp. location, make thumbnail and add to database
    $debugoutput .= '<hr />';
    $debugoutput .= $sizeofzip . ' ' . JText::_('JGA_FILES_IN_BATCH');
    $debugoutput .= '<hr />';
    usort($ziplist, "Joom_SortBatch");
    $ziplist = array_reverse($ziplist);

    //Path of category
    $catpath = Joom_GetCatPath($catid);

    for ($i=0; $i < $sizeofzip; $i++) {
      //get the filename without path, JFile::getName() does not
      //work on local installations
      $filepathinfos=pathinfo($ziplist[$i]['filename']);
      $origfilename = $filepathinfos["basename"];
      $fileextension = strtolower(JFile::getExt($origfilename));

      //check the possible available memory for picture resizing
      //if not available echo error message and continue with next picture
      if ($this->Upload_CheckMemory($debugoutput,JPATH_ROOT.DS.$temp_dir.$origfilename,$fileextension)==false){
        $this->debug=true;
        continue;
      }

      //Check for path exploits, and replace spaces
      if ( $config->jg_useorigfilename ) {
        $compacttitle = Joom_FixFilename($origfilename,1);
      } else {
        $compacttitle = Joom_FixFilename($this->gentitle);
      }

      //get the serial number if use of original name is deactivated
      //and numbering is activated
      if(!$config->jg_useorigfilename && $config->jg_filenamenumber) {
        $picserial=$this->Upload_GetSerial($sizeofzip);
        $newfilename = $this->Upload_GenFilename($compacttitle,$fileextension,$picserial);
      } else {
        $newfilename = $this->Upload_GenFilename($compacttitle,$fileextension);
      }

      $debugoutput .= '<hr />'."\n";
      $debugoutput .= JText::_('JGA_FILENAME') . ': ';
      $debugoutput .= "$origfilename <br />\n";
      $debugoutput .= JText::_('JGA_NEW_FILENAME') . ": $newfilename <br />";

      //Move the picture from temp. folder to originals folder
      $returnval=JFile::move($temp_dir.DS.$origfilename,
                      $config->jg_pathoriginalimages.$catpath.$newfilename,
                      JPATH_ROOT);
      if (!$returnval){
        $debugoutput .= JText::_('JGA_PROBLEM_COPYING') . ': ' . JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages) . '. ' . JText::_('JGA_CHECK_PERMISSIONS');
        $this->debug=1;
        continue;
      }

      //Set permissions to 644
      $returnval = Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename));
      if (!$returnval){
        $debugoutput .= JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename.' '.JText::_('JGA_CHECK_PERMISSIONS');
        $this->Upload_Rollback($debugoutput,
                               JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                               null,null);
        $this->debug=1;
        continue;
      }
      
      $debugoutput .= JText::_('JGA_UPLOAD_START') . '<br />';
      $debugoutput .= JText::_('JGA_UPLOAD_COMPLETE') . '<br />';

      //Create the thumb from original picture
      $returnval=Joom_ResizeImage($debugoutput,JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                       JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename,
                       $config->jg_useforresizedirection, $config->jg_thumbwidth, $config->jg_thumbheight,
                       $config->jg_thumbcreation, $config->jg_thumbquality);
      if (!$returnval){
        $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename;
        $this->Upload_Rollback($debugoutput,
                               JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                               null,
                               JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
        $this->debug=1;
        continue;
      }
      $debugoutput .= JText::_('JGA_THUMBNAIL_CREATED') . '<br />';

      //Create the detail picture from original
      if ($config->jg_resizetomaxwidth) {
        $returnval=Joom_ResizeImage($debugoutput,JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                         JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                         false, $config->jg_maxwidth, false,$config->jg_thumbcreation,
                         $config->jg_picturequality, true);
        if (!$returnval){
          $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename;
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                 null,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $this->debug=1;
          continue;
        }
        $debugoutput .= JText::_('JGA_RESIZED_TO_MAXWIDTH') . '...<br />';
      } else {
        //Otherwise only copy the picture from original to detail
        $returnval=JFile::copy($config->jg_pathoriginalimages.$catpath.$newfilename,
                    $config->jg_pathimages.$catpath.$newfilename,
                    JPATH_ROOT);

        if (!$returnval){
          $debugoutput .= JText::_('JGA_PROBLEM_COPYING ').$config->jg_pathimages.$catpath.$newfilename;
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $this->debug=1;
          continue;
        }
        $returnval = Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename));
        if (!$returnval){
          $debugoutput .= JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename.' '.JText::_('JGA_CHECK_PERMISSIONS');
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $this->debug=1;
          continue;
        }
      }

      if($config->jg_delete_original == 1 || ($config->jg_delete_original== 2 && $this->original_delete==1)) {
        //Delete original if setted in backend
        $returnval=JFile::delete(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename);
        if (!$returnval){
          $debugoutput .= JText::_('JGA_PROBLEM_DELETING_ORIGINAL') . ': ' . JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages) . ' ' . JText::_('JGA_CHECK_PERMISSIONS');
          $this->Upload_Rollback($debugoutput,null,
                                 JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $this->debug=1;
          continue;
        }
       $debugoutput .= JText::_('JGA_ORIGINAL_DELETED') . '<br />';
      }
      //New entry for ordering
      $ordering = $this->Upload_GetOrdering($config->jg_uploadorder,$catid);

      if ($config->jg_useorigfilename) {
        $fileextensionlength = strlen($fileextension);
        $filenamelength = strlen($origfilename);
        $imgname = substr($origfilename,-$filenamelength,-$fileextensionlength-1);
      } else {
        if ($config->jg_filenamenumber) {
          $imgname = $this->gentitle.$this->imgname_separator.$picserial;
        } else {
          $imgname = $this->gentitle;
        }
      }

      $batchtime = mktime();
      $database->setQuery( "INSERT INTO #__joomgallery(id, catid, imgtitle, imgauthor,
          imgtext, imgdate, imgcounter, imgvotes,
          imgvotesum, published, imgfilename, imgthumbname,
          checked_out,owner,approved, ordering)
          VALUES
          (NULL, '$catid', '$imgname', '$this->photocred',
          '$this->gendesc', '$batchtime', '0', '0',
          '0', '1', '$newfilename', '$newfilename',
          '0', '".$user->get('id')."', 1, '$ordering')");

      if (!$database->query()) {
        $debugoutput .= $database->getErrorMsg();
        $this->Upload_Rollback($debugoutput,
                               JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                               JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                               JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
        $this->debug=1;
        continue;
      }
    }
    $debugoutput .= '<hr /><br />' . "\n";
    if(!$this->debug) {
      $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=batchupload',JText::_('JGA_UPLOAD_SUCCESSFULL'));
    }else{
      echo $debugoutput;
    }
  }

  /**
  * FTP Upload
  * several picture uploaded via FTP before are moved to an category
  * HTML function: Joom_ShowFTPUpload()
  * @param string id of destination category
  */
  function Upload_FTP ($catid) {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
    $debugoutput="";

    $sizeftpfiles = sizeof($this->ftpfiles);

    //Path of category
    $catpath = Joom_GetCatPath($catid);

    foreach ($this->ftpfiles as $screenshot_name) {
      $fileextension = strtolower(JFile::getExt($screenshot_name));

      if ( $config->jg_useorigfilename ) {
        $compacttitle = Joom_FixFilename($screenshot_name, 1);
      } else {
        $compacttitle = Joom_FixFilename($this->gentitle);
      }

      //check the possible available memory for picture resizing
      //if not available echo error message and continue with next picture
      if ($this->Upload_CheckMemory($debugoutput,JPATH_ROOT.DS.$config->jg_pathftpupload.$this->subdirectory.$screenshot_name,$fileextension)==false){
        $this->debug=true;
        continue;
      }

      //get the serial number if use of original name is deactivated
      //and numbering is activated
      if (!$config->jg_useorigfilename && $config->jg_filenamenumber){
        $picserial=$this->Upload_GetSerial($sizeftpfiles);
      }

      if(!$config->jg_useorigfilename && $config->jg_filenamenumber) {
        $newfilename = $this->Upload_GenFilename($compacttitle,$fileextension,$picserial);
      } else {
        $newfilename = $this->Upload_GenFilename($compacttitle,$fileextension);
      }

      $debugoutput .= '<p />';
      $debugoutput .= "$screenshot_name<br />";

      //Create thumbnail
      $returnval=Joom_ResizeImage($debugoutput,JPATH_ROOT.DS.$config->jg_pathftpupload.$this->subdirectory.$screenshot_name,
                       JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename,
                       $config->jg_useforresizedirection,$config->jg_thumbwidth, $config->jg_thumbheight,
                       $config->jg_thumbcreation, $config->jg_thumbquality);

      if (!$returnval){
        $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename.'<br />';
        $this->debug=1;
        continue;
      }

      $debugoutput .= JText::_('JGA_THUMBNAIL_CREATED') . '<br />';

      //Create detail picture only if jpg and maxwidth setted
      if ($config->jg_resizetomaxwidth  && ($this->create_special_gif!=1 ||
         ($fileextension!='gif' && $fileextension!='png'))) {
        $returnval=Joom_ResizeImage($debugoutput,JPATH_ROOT.DS.$config->jg_pathftpupload.$this->subdirectory.$screenshot_name,
                         JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                         false, $config->jg_maxwidth,false, $config->jg_thumbcreation,
                         $config->jg_picturequality, true);
        if (!$returnval){
          $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename.'<br />';
          $this->Upload_Rollback($debugoutput,null,null,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $this->debug=1;
          continue;
        }

        $debugoutput .= JText::_('JGA_RESIZED_TO_MAXWIDTH') . '...<br />';
      } else {
        //Otherwise only copy the picture
        $returnval=JFile::copy($config->jg_pathftpupload.$this->subdirectory.$screenshot_name,
                    $config->jg_pathimages.$catpath.$newfilename,
                    JPATH_ROOT);

        if (!$returnval){
          $debugoutput .= JText::_('JGA_PROBLEM_COPYING ').$config->jg_pathimages.$catpath.$newfilename;
          $this->debug=1;
          continue;
        }
        $returnval = Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename));
        if (!$returnval){
          $debugoutput .= JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename.': '.JText::_('JGA_CHECK_PERMISSIONS');
          $this->Upload_Rollback($debugoutput,null,
                                 JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $this->debug=1;
          continue;
        }
      }

      //Copy or move picture in originals?
      if(!($config->jg_delete_original == 1 || ($config->jg_delete_original== 2 && $this->original_delete==1))) {
        //Create original picture file in original
        //if file has to be deleted from upload directory move them to originals
        if ($this->file_delete == 1) {
          if (!JFile::move($config->jg_pathftpupload.$this->subdirectory.$screenshot_name,
                           $config->jg_pathoriginalimages.$catpath.$newfilename,
                           JPATH_ROOT)) {
            $debugoutput .= JText::_('JGA_COULD_NOT_DELETE_PICTURE') . '<br />';
            $this->Upload_Rollback($debugoutput,null,
                                   JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            $this->debug = 1;
            continue;
          } else {
            $returnval = Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename));
          }
          if (!$returnval){
            $debugoutput .= JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename.' '.JText::_('JGA_CHECK_PERMISSIONS');
            $this->Upload_Rollback($debugoutput,
                                   JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            $this->debug=1;
            continue;
          }
        } else {
          //Otherwise copy them into originals
          $returnval=JFile::copy($config->jg_pathftpupload.$this->subdirectory.$screenshot_name,
                          $config->jg_pathoriginalimages.$catpath.$newfilename,
                          JPATH_ROOT);
          if (!$returnval){
            $this->debug = 1;
            $debugoutput .= JText::_('JGA_WRONG_FILENAME');
            $this->Upload_Rollback($debugoutput,null,
                                   JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            $this->debug=1;
            continue;
          }
          $returnval = Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename));
          if (!$returnval){
            $debugoutput .= JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename.' '.JText::_('JGA_CHECK_PERMISSIONS');
            $this->Upload_Rollback($debugoutput,
                                   JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            $this->debug=1;
            continue;
          }
        }
      } else {
        //Original picture shall not be created
        //evtl delete them from upload directory
        if ($this->file_delete == 1) {
          if (!JFile::delete(JPATH_ROOT.DS.$config->jg_pathftpupload.$this->subdirectory.$screenshot_name)) {
            $debugoutput .= JText::_('JGA_COULD_NOT_DELETE_PICTURE') . '<br />';
            $this->Upload_Rollback($debugoutput,null,
                                   JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            $this->debug = 1;
            continue;
          }else{
            $debugoutput .= JText::_('JGA_ORIGINAL_DELETED') . '<br />';
          }
        }
      }

      $ordering = $this->Upload_GetOrdering ($config->jg_uploadorder,$catid);

      if ($config->jg_useorigfilename) {
        $fileextensionlength = strlen($fileextension);
        $filenamelength = strlen($screenshot_name);
        $imgname = substr($screenshot_name,-$filenamelength,-$fileextensionlength-1);
      } else {
        if ($config->jg_filenamenumber == 1) {
          $imgname = $this->gentitle.$this->imgname_separator.$picserial;
        } else {
          $imgname = $this->gentitle;
        }
      }

      $batchtime = mktime();
      $database->setQuery( "INSERT INTO #__joomgallery(id, catid, imgtitle, imgauthor,
          imgtext, imgdate, imgcounter, imgvotes,
          imgvotesum, published, imgfilename, imgthumbname,
          checked_out,owner,approved, ordering)
          VALUES
          (NULL, '$catid', '$imgname', '$this->photocred',
          '$this->gendesc', '$batchtime', '0', '0',
          '0', '1', '$newfilename', '$newfilename',
          '0', '".$user->get('id')."', 1, '$ordering')");

      if (!$database->query()) {
        $debugoutput .= $database->getErrorMsg();
        $this->Upload_Rollback($debugoutput,
                               JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                               JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                               JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
        $this->debug=1;
        continue;
      }
    }
    if(!$this->debug) {
      $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=ftpupload&batchul=1',JText::_('JGA_UPLOAD_SUCCESSFULL'));
    }else{
      echo $debugoutput;
    }
  }

  /**
  * Single upload
  * up to 10 pictures are chosen and uploaded before
  * @param string id from destination category
  */
  function Upload_Singles_Backend ($catid) {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();
    $debugoutput='';

    //Path of category
    $catpath = Joom_GetCatPath($catid);

    for ($i=0; $i < 10; $i++) {
      $debugoutput .=  '<hr />';
      $pos=$i+1;
      $debugoutput .= 'Position: '.$pos.'<br />';
      //Any picture entry at position?
      //(4=UPLOAD_ERR_NO_FILE constant since PHP 4.3.0)
      //if not continue with next entry
      if ($this->arrscreenshot['error'][$i]==4) {
        $debugoutput .= JText::_('JGA_ERROR_FILE_NOT_UPLOADED').'<br />';
        continue;
      }
      //check all other error codes except UPLOAD_ERR_NO_FILE
      if ($this->arrscreenshot["error"][$i] > 0) {
        $debugoutput .= $this->Upload_CheckError($this->arrscreenshot["error"][$i]).'<br />';
        $this->debug = 1;
        continue;
      }

      $screenshot       =$this->arrscreenshot["tmp_name"][$i];
      $screenshot_name  =$this->arrscreenshot["name"][$i];
      $screenshot_name  = Joom_FixFilename($screenshot_name);
      $debugoutput .= $screenshot_name.'<br />';

      $tag = strtolower(JFile::getExt($screenshot_name));

      //check the possible available memory for picture resizing
      //if not available echo error message and continue with next picture
      if ($this->Upload_CheckMemory($debugoutput,$screenshot,$tag)==false){
        $this->debug=1;
        continue;
      }

      //Create new filename
      //if generic filename setted in backend use them
      if ( $config->jg_useorigfilename ) {
        $screenshot_name = Joom_FixFilename($screenshot_name);
      } else {
        $screenshot_name = Joom_FixFilename($this->gentitle);
      }
      $newfilename = $this->Upload_GenFilename($screenshot_name,$tag);

      //move uploaded picture to originals
      if ((($tag == 'jpeg') || ($tag == 'jpg') || ($tag == 'jpe') || ($tag == 'gif') || ($tag == 'png'))
          && strlen($screenshot) > 0 && $screenshot != 'none'){

        $returnval = JFile::upload($screenshot,JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename);
        if (!$returnval){
          $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename.'<br />';
          $this->debug=1;
          continue;
        }

        Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename));
        if (!$returnval){
          $debugoutput .= JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename.': '.JText::_('JGA_CHECK_PERMISSIONS');
          $this->Upload_Rollback($debugoutput,JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,null,null);
          $this->debug=1;
          continue;
        }

        //create thumbnail
        $returnval=Joom_ResizeImage($debugoutput,JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                         JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename,
                         $config->jg_useforresizedirection, $config->jg_thumbwidth, $config->jg_thumbheight,
                         $config->jg_thumbcreation, $config->jg_thumbquality);

        if (!$returnval){
          $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename;
          $this->Upload_Rollback($debugoutput,JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,null,null);
          $this->debug=1;
          continue;
        }
        $debugoutput .= JText::_('JGA_THUMBNAIL_CREATED') . '<br />';

        //evtl create detail picture
        if ($config->jg_resizetomaxwidth && ($this->create_special_gif!=1 || ($tag!='gif' && $tag!='png'))) {
          $returnval=Joom_ResizeImage($debugoutput,JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                           JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename, false,
                           $config->jg_maxwidth, false, $config->jg_thumbcreation,$config->jg_picturequality, true);
          if (!$returnval){
            $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename;
            $this->Upload_Rollback($debugoutput,
                                   JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                   null,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            $this->debug=1;
            continue;
          }
          $debugoutput .= JText::_('JGA_RESIZED_TO_MAXWIDTH') . '...<br />';
        } else {
          $returnval=JFile::copy($config->jg_pathoriginalimages.$catpath.$newfilename,
                      $config->jg_pathimages.$catpath.$newfilename,
                      JPATH_ROOT);
          if (!$returnval){
            $debugoutput .= JText::_('JGA_PROBLEM_COPYING ').$config->jg_pathimages.$catpath.$newfilename;
            $this->Upload_Rollback($debugoutput,
                                   JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                   null,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            $this->debug=1;
            continue;
          }
          $returnval = Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename));
          if (!$returnval){
            $debugoutput .= JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename.' '.JText::_('JGA_CHECK_PERMISSIONS');
            $this->Upload_Rollback($debugoutput,
                                   JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            $this->debug=1;
            continue;
          }
        }

        if($config->jg_delete_original == 1 || ($config->jg_delete_original== 2 && $this->original_delete==1)) {
          //Remove picture from originals if chosen in backend
          if(JFile::delete(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename)) {
            $debugoutput .= JText::_('JGA_ORIGINAL_DELETED') . '<br />';
          } else {
            $debugoutput .= JText::_('JGA_PROBLEM_DELETING_ORIGINAL')
                         . ': '
                         . JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages)
                         . ' '
                         . JText::_('JGA_CHECK_PERMISSIONS');
            $this->Upload_Rollback($debugoutput,
                                   null,
                                   JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);

            $this->debug=1;
            continue;
          }
        }

        //New entry for ordering
        $ordering = $this->Upload_GetOrdering($config->jg_uploadorder,$catid);

        $batchtime = mktime();
        if ( $config->jg_useorigfilename ) {
          $fileextensionlength = strlen($tag);
          $filenamelength = strlen($screenshot_name);
          $imgname = substr($screenshot_name,-$filenamelength,-$fileextensionlength-1);
        } else {
          $imgname = $this->gentitle;
        }
        $query= "INSERT INTO #__joomgallery(id, catid, imgtitle, imgauthor,
            imgtext, imgdate, imgcounter, imgvotes,
            imgvotesum, published, imgfilename, imgthumbname,
            checked_out,owner,approved, ordering)
            VALUES
            (NULL, '$catid', '$imgname', '$this->photocred',
            '$this->gendesc', '$batchtime', '0', '0',
            '0', '1', '$newfilename', '$newfilename',
            '0', '".$user->get('id')."', 1, '$ordering')";

        $database->setQuery($query);

        if (!$database->query()) {
          $debugoutput .= $database->getErrorMsg();
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $this->debug=1;
          continue;
        }
      } else {
        $debugoutput .= JText::_('JGA_WRONG_FILENAME') ;
        $this->debug=1;
        continue;
      }
    }
    if(!$this->debug) {
      $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=upload&batchul=1',JText::_('JGA_UPLOAD_SUCCESSFULL'));
    }else{
      echo $debugoutput;
    }
  }

  /**
  * JAVA Applet upload
  * @param Kategorie id of destination category
  */
  function Upload_AppletReceive_Backend ($catid) {
    // If the applet checks for the serverProtocol, it issues a HEAD request
    // -> Simply return an empty doc.
    if ($_SERVER['REQUEST_METHOD'] == 'HEAD'){
      jexit();
    }

    $config = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user = & JFactory::getUser();

    $debugoutput='';

    //The Applet recognize an error with the text 'JOOMGALLERYUPLOADERROR'
    //and shows them within an JS alert box

    //check common requirements
    //no catid
    if ($catid == 0) {
      jexit('JOOMGALLERYUPLOADERROR '.JText::_('JGA_JUPLOAD_YOU_MUST_SELECT_CATEGORY'));
    }
    //non common title
    if (!$config->jg_useorigfilename && empty($this->gentitle)) {
      jexit('JOOMGALLERYUPLOADERROR '.JText::_('JGA_JUPLOAD_PICTURE_MUST_HAVE_TITLE'));
    }
    //Category path
    $catpath = Joom_GetCatPath($catid);

    foreach ($_FILES as $file => $fileArray) {
      //If 'delete originals' chosen in backend and the picture
      //shall be uploaded resized this will be done locally in the applet
      //then only the detail picture will be uploaded
      //therefore adjust path of destination category
      if ($config->jg_delete_original && $config->jg_resizetomaxwidth) {
        $no_original = true;
        $picpath = $config->jg_pathimages;
      } else {
        $no_original = false;
        $picpath = $config->jg_pathoriginalimages;
      }

      $screenshot=$fileArray["tmp_name"];
      $screenshot_name=$fileArray["name"];
      $screenshot_name = Joom_FixFilename($screenshot_name);
      $tag = strtolower(JFile::getExt($screenshot_name));

      //check the possible available memory for picture resizing
      //if not available echo error message and continue with next picture
      if ($this->Upload_CheckMemory($debugoutput,$screenshot,$tag)==false){
        $this->debug=1;
        continue;
      }

      //Create new filename
      //if generic filename setted in backend use them
      if ( $config->jg_useorigfilename ) {
        $screenshot_name = Joom_FixFilename($screenshot_name);
        $newfilename = $this->Upload_GenFilename($screenshot_name,$tag);
      } else {
        $screenshot_name = Joom_FixFilename($this->gentitle);
        $newfilename = $this->Upload_GenFilename($screenshot_name,$tag);
      }

      //Move uploaded picture in destination folder (original or details)
      if (strlen($screenshot) > 0 && $screenshot != 'none') {
        $returnval=JFile::upload($screenshot,JPATH_ROOT.DS.$picpath.$catpath.$newfilename);
        if (!$returnval){
          $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$picpath.$catpath.$newfilename.'<br />';
          $this->debug=1;
          continue;
        }

        Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$picpath.$catpath.$newfilename));
        if (!$returnval){
          $debugoutput .= JPath::clean(JPATH_ROOT.DS.$picpath.$catpath.$newfilename).': '.JText::_('JGA_CHECK_PERMISSIONS');
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$picpath.$catpath.$newfilename,
                                 null,
                                 null);
          $this->debug=1;
          continue;
        }

        //Create thumbnail
        $returnval=Joom_ResizeImage($debugoutput,JPATH_ROOT.DS.$picpath.$catpath.$newfilename,
                         JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename,
                         $config->jg_useforresizedirection, $config->jg_thumbwidth, $config->jg_thumbheight,
                         $config->jg_thumbcreation, $config->jg_thumbquality);
        if (!$returnval){
          $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename;
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$picpath.$catpath.$newfilename,
                                 null,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $this->debug=1;
          continue;
        }
        $debugoutput .= JText::_('JGA_THUMBNAIL_CREATED')."\n";

        //evtl. create detail picture
        //not if 'delete originals' and resize setted in backend
        //In this case the applet made the resize and upload the detail picture
        if (!$no_original) {
          if ($config->jg_resizetomaxwidth && ($this->create_special_gif != 1 || ($tag != 'gif' && $tag != 'png'))) {
            $returnval=Joom_ResizeImage($debugoutput,JPATH_ROOT.DS.$picpath.$catpath.$newfilename,
                             JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename, false,
                             $config->jg_maxwidth, false, $config->jg_thumbcreation, $config->jg_picturequality, true);
            if (!$returnval){
              $debugoutput .= JText::_('JGA_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename;
              
              continue;
            }
            $debugoutput .=  JText::_('JGA_RESIZED_TO_MAXWIDTH')."\n";
          } else {
            $returnval=JFile::copy($picpath.$catpath.$newfilename,
                        $config->jg_pathimages.$catpath.$newfilename,
                        JPATH_ROOT);
            if (!$returnval){
              $debugoutput .= JText::_('JGA_PROBLEM_COPYING ').$config->jg_pathimages.$catpath.$newfilename;
              $this->Upload_Rollback($debugoutput,
                                     JPATH_ROOT.DS.$picpath.$catpath.$newfilename,
                                     null,
                                     JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
              $this->debug=1;
              continue;
            }
          }
          $returnval = Joom_Chmod(JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename));
          if (!$returnval){
            $debugoutput .= JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename.' '.JText::_('JGA_CHECK_PERMISSIONS');
            $this->Upload_Rollback($debugoutput,
                                   JPATH_ROOT.DS.$picpath.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            $this->debug=1;
            continue;
          }
        }
        //Delete original picture only if setted in upload window
        //not if setted in backend
        if($config->jg_delete_original== 2 && $this->original_delete==1) {
          if(JFile::delete(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename)) {
            $debugoutput .=  JText::_('JGA_ORIGINAL_DELETED');
          } else {
            $debugoutput.= JText::_('JGA_PROBLEM_DELETING_ORIGINAL') .
                ': ' .
                JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages) .
                ' ' .
                JText::_('JGA_CHECK_PERMISSIONS');
                $this->Upload_Rollback($debugoutput,
                                       JPATH_ROOT.DS.$picpath.$catpath.$newfilename,
                                       JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                       JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
                $this->debug=1;
          }
        }

        //new entry for ordering
        $ordering = $this->Upload_GetOrdering($config->jg_uploadorder,$catid);

        $batchtime = mktime();
        if ( $config->jg_useorigfilename ) {
          $fileextensionlength = strlen($tag);
          $filenamelength = strlen($screenshot_name);
          $imgname = substr($screenshot_name,-$filenamelength,-$fileextensionlength-1);
        } else {
          $imgname = $this->gentitle;
        }
        $query= "INSERT INTO #__joomgallery(id, catid, imgtitle, imgauthor,
            imgtext, imgdate, imgcounter, imgvotes,
            imgvotesum, published, imgfilename, imgthumbname,
            checked_out,owner,approved, ordering)
            VALUES
            (NULL, '$catid', '$imgname', '$this->photocred',
            '$this->gendesc', '$batchtime', '0', '0',
            '0', '1', '$newfilename', '$newfilename',
            '0', '".$user->get('id')."', 1, '$ordering')";

        $database->setQuery($query);

        if (!$database->query()) {
          $debugoutput.=$database->getErrorMsg();
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$picpath.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $this->debug=1;
        }
      } else {
        $debugoutput.=JText::_('JGA_WRONG_FILENAME');
        $this->debug=1;
      }
    }
    if ($this->debug){
      echo("\nJOOMGALLERYUPLOADERROR\n");
    }else{
      echo "\nJOOMGALLERYUPLOADSUCCESS\n";
    }
    echo $debugoutput;
    jexit();
  }

  /**
  * Generate filename
  * Example: <name/gen. tile>_<evtl. filecounter>_<20071031>_<random number>.jpg
  * @param $filename string original name e.g. 'malta.jpg'
  * @param $tag string filetag e.g. '.jpg'
  * @param $this->filecounter int evtl. for batch and FTP upload
  */
  function Upload_GenFilename ($filename,$tag,$filecounter = NULL) {
    $filedate = date("Ymd");
    mt_srand();
    $randomnumber = mt_rand(1000000000, 2099999999);

    //remove filetag = $tag incl '.'
    //only if exists in filename
    if (stristr($filename,$tag)) {
      $filename = substr($filename,0,strlen($filename)-strlen($tag)-1);
    }

    //new filename
    if ( $filecounter == NULL) {
      $newfilename = $filename.'_'.$filedate.'_'.$randomnumber.'.'.$tag;
    } else {
      $newfilename = $filename.'_'.$filecounter.'_'.$filedate.'_'.$randomnumber.'.'.$tag;
    }
    return $newfilename;
  }

  /**
  * Sets new ordering according to $config->jg_uploadorder
  * @param $type int  $config->jg_uploadorder ASC,DESC
  * @param int category id
  * @return int new ordering
  */
  function Upload_GetOrdering ($type, $catid) {
    $database = & JFactory::getDBO();
    switch ($type) {
      case 1:
        $query = "SELECT MIN(ordering)-1
            FROM #__joomgallery
            WHERE catid=$catid ";
        $database->setQuery($query);
        $result = $database->loadResult();
        if (!$result){
          $result=-1;
        }
        break;
      case 2:
        $query = "SELECT MAX(ordering)+1
            FROM #__joomgallery
            WHERE catid=$catid ";
        $database->setQuery($query);
        $result = $database->loadResult();
        break;
      default;
        $result = 1;
        break;
    }
    if ($result == NULL) {
      $result = 1;
    }
    return $result;
  }

  /**
   * Calculates the serial number for picture files and title
   * in FTP and Batchupload
   * @param int total count of all pictures to upload
   * @return int new serial number
   */
  function Upload_GetSerial ($totalcount=0) {
    $config = Joom_getConfig();
    static $picserial;

    //check if the initial value is already calculated
    if (isset($picserial)){
      $picserial++;
      return $picserial;
    }

    //calculate the initial value
    $picserial=0;

    //Start value setted in backend
    //no negative or 0 starting value
    if($this->filecounter < 1) {
      $picserial = 1;
    } else {
      $picserial=$this->filecounter;
    }

    return $picserial;
  }

  /**
  * Analyzes the error code
  * and outputs their text
  * @int errorcode
  */
  function Upload_CheckError ($uploaderror) {
    //common PHP errors
    $uploadErrors = array(
      1=>JText::_('JGA_ERROR_PHP_MAXFILESIZE'),
      2=>JText::_('JGA_ERROR_HTML_MAXFILESIZE'),
      3=>JText::_('JGA_ERROR_FILE_PARTLY_UPLOADED'),
    );

    if (in_array($uploaderror, $uploadErrors)) {
      echo JText::_('JGA_ERROR_CODE') . $uploadErrors[$uploaderror] . '<br />';
    } else {
      echo JText::_('JGA_ERROR_CODE') . JText::_('JGA_ERROR_UNKNOWN') . ' <br />';
    }
  }
  /**
   * Calculate the memory limit
   */
  function Upload_CheckMemory(&$debugoutput,$filename,$format){
    $config = Joom_getConfig();
    if ( (function_exists('memory_get_usage')) && (ini_get('memory_limit')) ) {
      $imageInfo = getimagesize($filename);
      $jpgpic=false;
      switch(strtoupper($format)) {
        case 'GIF':
          // measured factor 1 is better
          $channel = 1;
          break;
        case 'JPG':
        case 'JPEG':
        case 'JPE':
          $channel = $imageInfo['channels'];
          $jpgpic=true;
          break;
        case 'PNG':
          // no channel for png
          $channel = 3;
          break;
      }
      $MB = 1048576;
      $K64 = 65536;

      if ($config->jg_fastgd2thumbcreation && $jpgpic && $config->jg_thumbcreation == 'gd2'){
        //function of fast gd2 creation needs more memory
        $corrfactor=2.1;
      }else{
        $corrfactor=1.7;
      }

      $memoryNeeded = round(($imageInfo[0]
                             * $imageInfo[1]
                             * $imageInfo['bits']
                             * $channel / 8
                             + $K64)
                             * $corrfactor);

      $memoryNeeded = memory_get_usage() + $memoryNeeded;
      // get memory limit
      $memory_limit = @ini_get('memory_limit');
      if (!empty($memory_limit) && $memory_limit != 0) {
        $memory_limit = substr($memory_limit, 0, -1) * 1024 * 1024;
      }

      if ($memory_limit != 0 && $memoryNeeded > $memory_limit) {
        $memoryNeededMB = round ($memoryNeeded / 1024 / 1024, 0);
        $debugoutput .= JText::_('JGA_ERROR_MEM_EXCEED').
                        $memoryNeededMB." MByte ("
                        .$memoryNeeded.") Serverlimit: "
                        .$memory_limit/$MB."MByte (".$memory_limit.")<br/>" ;
        return false;
      }
    }
    return true;
  }

  /**
   * Rollback an erroneous upload
   *
   * @param string debug to output
   * @param string path to original picture
   * @param string path to detail picture
   * @param string path to thumbnail
   */
  function Upload_Rollback(&$debugoutput,$original, $detail, $thumb){
    if (!is_null($original) && JFile::exists($original)){
      $returnval=JFile::delete($original);
      if ($returnval){
        $debugoutput .= '<p>'.JText::_('JGA_UPLOAD_RB_ORGDEL_OK').'</p>';
      }else{
        $debugoutput .= '<p>'.JText::_('JGA_UPLOAD_RB_ORGDEL_NOK').'</p>';
      }
    }

    if (!is_null($detail) && JFile::exists($detail)){
      $returnval=JFile::delete($detail);
      if ($returnval){
        $debugoutput .= '<p>'.JText::_('JGA_UPLOAD_RB_DTLDEL_OK').'</p>';
      }else{
        $debugoutput .= '<p>'.JText::_('JGA_UPLOAD_RB_DTLDEL_NOK').'</p>';
      }
    }

    if (!is_null($thumb) && JFile::exists($thumb)){
      $returnval=JFile::delete($thumb);
      if ($returnval){
        $debugoutput .= '<p>'.JText::_('JGA_UPLOAD_RB_THBDEL_OK').'</p>';
      }else{
        $debugoutput .= '<p>'.JText::_('JGA_UPLOAD_RB_THBDEL_NOK').'</p>';
      }
    }
  }
}
?>
