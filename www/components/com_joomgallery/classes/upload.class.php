<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/classes/upload.class.php $
// $Id: upload.class.php 449 2009-06-14 11:57:04Z aha $
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

class Joom_Upload
{

  var $filecounter;
  var $file_delete;
  var $original_delete;
  var $create_special_gif;
  var $arrscreenshot;
  var $adminlogged;

/**
* Class Joom_Upload
* @param func
* @param Category ID
*/
  function Joom_Upload($func, $catid)
  {
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    $this->filecounter         = Joom_mosGetParam('filecounter', '', 'post');
    $this->original_delete     = Joom_mosGetParam('original_delete', '', 'post');
    $this->create_special_gif  = Joom_mosGetParam('create_special_gif', '', 'post');
    $this->arrscreenshot       = Joom_mosGetParam('arrscreenshot', '', 'files');

    if($user->get('usertype') == 'Administrator' || $user->get('usertype') == 'Super Administrator')
    {
      $this->adminlogged = true;
    }
    else
    {
      $this->adminlogged = false;
    }

    switch($func)
    {
      case 'uploadhandler':
        $this->Upload_Singles($catid);
        break;
      default:
        die('Wrong');
        break;
    }
  }//End function Joom_Upload


/**
* Single upload
* The user choose single picture files and upload them
* concurrent uploads can be modified in backend
* @param Category ID
*/
  function Upload_Singles($catid)
  {
    $config    = Joom_getConfig();
    $mainframe = & JFactory::getApplication('site');
    $database  = & JFactory::getDBO();
    $user      = & JFactory::getUser();
    jimport('joomla.filesystem.file');

    $debugoutput = '';
    //no user logged in
    if(!$user->get('id'))
    {
      $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery'._JOOM_ITEMID,false),
                                     JText::_('JGS_YOU_ARE_NOT_LOGGED'));
    }
    $catpath = Joom_GetCatPath($catid);

    $debugoutput .= '<p />';
    for($i=0; $i < $config->jg_maxuploadfields; $i++)
    {
      $screenshot          = $this->arrscreenshot["tmp_name"][$i];
      $screenshot_name     = $this->arrscreenshot["name"][$i];
      $screenshot_filesize = $this->arrscreenshot["size"][$i];
      $ii = $i+1;

      //Any picture entry at position?
      //(4=UPLOAD_ERR_NO_FILE constant since PHP 4.3.0)
      //if not continue with next entry
      if($this->arrscreenshot['error'][$i] == 4)
      {
        continue;
      }
      
      //Check for path exploits, and replace spaces
      $screenshot_name = Joom_FixFilename($screenshot_name);
      // Get extension
      $tag = strtolower(JFile::getExt($screenshot_name));

      if($config->jg_useruploadnumber == 1)
      {
        $filecounter = $i + 1;
        $praefix = substr($screenshot_name,0,strpos(strtolower($screenshot_name),$tag)-1);
        $newfilename = $this->Upload_GenFilename($praefix, $tag, $filecounter);
      }
      else
      {
        $newfilename = $this->Upload_GenFilename($screenshot_name, $tag);
      }

      //Picture size must not exceed the setting in backend
      //except for Admin/SuperAdmin
      if($screenshot_filesize > $config->jg_maxfilesize && !$this->adminlogged)
      {
        $debugoutput .= JText::_('JGS_ALERT_MAX_ALLOWED_FILESIZE') . " " . $config->jg_maxfilesize . " " . JText::_('JGS_ALERT_BYTES');
        continue;
      }
      //Check for right format
      if(   ($tag == 'jpeg') || ($tag == 'jpg') || ($tag == 'jpe') 
         || ($tag == 'gif')  || ($tag == 'png')
        )
      {
        $debugoutput .= '<hr />Position: '.$ii.'<br />';
        $debugoutput .= $ii . ". " . $screenshot_name . "<br />";

        //if picture already exists
        if(file_exists(JPath::clean(JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename)))
        {
          $debugoutput .= JText::_('JGS_ALERT_SAME_PICTURE_ALREADY_EXIST');
          continue;
        }
        // We'll assume that this file is ok because with open_basedir,
        // we can move the file, but may not be able to access it until it's moved
        $returnval=JFile::upload($screenshot,JPATH_ROOT.DS.$config->jg_pathoriginalimages.DS.$catpath.$newfilename);
        if(!$returnval)
        {
          $debugoutput .= JText::_('JGS_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename.'<br />';
          continue;
        }

        $debugoutput .= JText::_('JGS_UPLOAD_COMPLETE') . '...<br />';
        if(!$img_info = getimagesize(JPath::clean(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename)))
        {
          // getimagesize didn't find a valid image or this is
          // some sort of hacking attempt
          JFile::delete(JPATH_ROOT.DS.$config->jg_pathoriginalimages.DS.$catpath.$newfilename);
          jexit();
        }

        //check the possible available memory for picture resizing
        //if not available echo error message and continue with next picture
        if($this->Upload_CheckMemory($debugoutput,JPATH_ROOT.DS.$config->jg_pathoriginalimages.DS.$catpath.$newfilename, $tag) == false)
        {
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                 null, null);
          continue;
        }
        // create thumb
        $returnval=Joom_ResizeImage($debugoutput,JPATH_ROOT . DS . $config->jg_pathoriginalimages . $catpath . $newfilename,
                         JPATH_ROOT . DS . $config->jg_paththumbs . $catpath . $newfilename,
                         $config->jg_useforresizedirection, $config->jg_thumbwidth, $config->jg_thumbheight,
                         $config->jg_thumbcreation, $config->jg_thumbquality);
        if(!$returnval)
        {
          $debugoutput .= JText::_('JGS_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename;
          $this->Upload_Rollback($debugoutput,JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,null,null);
          continue;
        }
        $debugoutput .= JText::_('JGS_THUMBNAIL_CREATED') . '...<br />';

        //create detail picture
        if($config->jg_resizetomaxwidth && ($config->jg_special_gif_upload == 0 
           || $this->create_special_gif!=1 || ($tag!='gif' && $tag!='png'))
          )
        {
          $returnval = Joom_ResizeImage($debugoutput, JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                        JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                        false, $config->jg_maxwidth, false, $config->jg_thumbcreation, $config->jg_picturequality, true);
          if(!$returnval)
          {
            $debugoutput .= JText::_('JGS_WRONG_FILENAME').': '.JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename;
            $this->Upload_Rollback($debugoutput,
                                   JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                   null,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            continue;
          }
          $debugoutput .= JText::_('JGS_RESIZED_TO_MAXWIDTH') . '<br />';
        }
        else
        {
          $returnval = JFile::copy($config->jg_pathoriginalimages.$catpath.$newfilename,
                                   $config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT);
          if(!$returnval)
          {
            $debugoutput .= JText::_('JGS_PROBLEM_COPYING ').$config->jg_pathimages.$catpath.$newfilename;
            $this->Upload_Rollback($debugoutput,
                                   JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                   null,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            continue;
          }
        }

        if($config->jg_delete_original_user == 1 
           || ($config->jg_delete_original_user == 2 && $this->original_delete == 1)
          )
        {
          if(JFile::delete(JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename))
          {
            $debugoutput .= JText::_('JGS_ORIGINAL_DELETED') . '<br />';
          }
          else
          {
            $debugoutput .= JText::_('JGS_PROBLEM_DELETING_ORIGINAL') .' - '. JText::_('JGS_CHECK_PERMISSIONS');
            $this->Upload_Rollback($debugoutput,
                                   null,
                                   JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                   JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
            continue;
          }
       }

        $ordering = $this->Upload_GetOrdering ($config->jg_uploadorder, $catid);

        $row = new mosjoomgallery($database);
        if (!$row->bind($_POST, JText::_('JGS_APPROVED_OWNER_PUBLISHED')))
        {
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
          jexit();
        }
        $row->imgdate = mktime();
        $row->owner = $user->get('id');
        $row->published = 1;

        //Upload from admin/superadmin are approved
        if($config->jg_approve==1 &&  !$this->adminlogged)
        {
          $row->approved = 0;
        }
        else
        {
          $row->approved = 1;
        }
        $row->imgfilename  = $newfilename;
        $row->imgthumbname = $newfilename;
        $row->useruploaded = 1;
        $row->ordering     = $ordering;
  
        //Wenn im Backend die Vergabe von lfd. Nummern eingestellt wurde
        //wird dem Bildtitel die lfd. Nummer (+1) hinzugefügt
        if($config->jg_useruploadnumber)
        {
          $row->imgtitle = $row->imgtitle . '_' . $filecounter;
        }
  
        if(!$row->store())
        {
          $this->Upload_Rollback($debugoutput,
                                 JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$newfilename,
                                 JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$newfilename);
          $debugoutput .= $row->getError();
          continue;
        }
        else
        {
          // E-Mail ueber ein neues Bild an die User, die global als User Email-Empfang
          // erlaubt haben TODO -> In Backend-Konfig einstellen bzw. deaktivieren
  
          /* TODO
          // portierung: /administrator/components/com_messages/tables/message.php anstatt administrator/components/com_messages/messages.class.php
          require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_messages'.DS.'tables'.DS.'message.php' );
          $database->setQuery("SELECT id
              FROM #__users
              WHERE sendEmail='1'");
          $users = $database->loadResultArray();
          foreach ($users as $user_id) {
            $msg = new TableMessage($database); // portierung: TableMessage anstatt mosMessage
            $msg->send($user->get('id'),
            $user_id,
            JText::_('JGS_NEW_PICTURE_UPLOADED'),
            sprintf( JText::_('JGS_NEW_CONTENT_SUBMITTED') . " %s " . JText::_('JGS_TITLED') ." %s.",
            $user->get('username'),
            $row->imgtitle));
          }
          */

          $debugoutput .= JText::_('JGS_ALERT_PICTURE_SUCCESSFULLY_ADDED') . '<br />';
          $debugoutput .= JText::_('JGS_NEW_FILENAME') . ': ' . $newfilename . '<br /><br />';
        }
      }
      else
      {
        $debugoutput .= JText::_('JGS_ALERT_INVALID_IMAGE_TYPE') ;
        continue;
      }
    }
    echo $debugoutput;
?>
    <p>
      <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
      <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=showupload'._JOOM_ITEMID) ;?>">
        <?php echo JText::_('JGS_MORE_UPLOADS') ;?>
      </a>
    </p>
    <p>
      <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
      <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=userpanel'._JOOM_ITEMID) ;?>">
        <?php echo JText::_('JGS_BACK_TO_USER_PANEL') ;?>
      </a>
    </p>
    <p>
      <img src="<?php echo _JOOM_LIVE_SITE.'components/com_joomgallery/assets/images/arrow.png'; ?>" class="pngfile jg_icon" alt="arrow" />
      <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&startpage=1'._JOOM_ITEMID); ?>">
        <?php echo JText::_('JGS_BACK_TO_GALLERY') ?>
      </a>
    </p>
<?php
  }//End function Upload_Singles


/**
* Dateinamen generieren.
* Beispiel: <Name/gen. Titel>_<ggf. filecounter>_<20071031>_<Random Zahl>.jpg
* @param $filename Original-Upload-Name z.B. 'malta.jpg'
* @param $tag Dateiendung z.B. '.jpg'
* @param $this->filecounter ggf. bei Batch und FTP Upload
*/
  function Upload_GenFilename($filename, $tag, $filecounter = NULL)
  {
    $filedate = date("Ymd");
    //Neuen Startwert f?r Zufallszahl bestimmen
    mt_srand();
    $randomnumber = mt_rand(1000000000, 2099999999);

    //Suffix = $tag mit dem . entfernen
    //nur wenn in filename enthalten
    if(stristr($filename,$tag))
    {
      $filename = substr($filename,0,strlen($filename)-strlen($tag)-1);
    }

    //Neuer Filename
    if($filecounter == NULL)
    {
      $newfilename = $filename.'_'.$filedate.'_'.$randomnumber.'.'.$tag;
    }
    else
    {
      $newfilename = $filename.'_'.$filecounter.'_'.$filedate.'_'.$randomnumber.'.'.$tag;
    }
    return $newfilename;
  }//End function Upload_GenFilename


/**
* Ermittelt Ordering gemäß Vorgabe in $config->jg_uploadorder
* @param $type = $config->jg_uploadorder ASC,DESC
* @param Kategorie ID
* @return neues ordering
*/
  function Upload_GetOrdering($type, $catid)
  {
    $database = & JFactory::getDBO();
    switch($type)
    {
      case 1:
        $query = "  SELECT 
                      MIN(ordering)-1
                    FROM 
                      #__joomgallery
                    WHERE 
                      catid = $catid 
                  ";
        $database->setQuery($query);
        $result = $database->loadResult(); //TODO Ordering <0 erlaubt???
        break;
      case 2:
        $query = "  SELECT 
                      MAX(ordering)+1
                    FROM 
                      #__joomgallery
                    WHERE 
                      catid = $catid 
                  ";
        $database->setQuery($query);
        $result = $database->loadResult();
        break;
      default;
        $result = 0;
        break;
    }
    if($result == NULL)
    {
      $result = 0;
    }
    return $result;
  }//End function Upload_GetOrdering


  /**
  * Analyzes the error code
  * and outputs their text
  * @int errorcode
  */
  function Upload_CheckError($uploaderror)
  {
    //common PHP errors
    $uploadErrors = array(
      1=>JText::_('JGS_ERROR_PHP_MAXFILESIZE'),
      2=>JText::_('JGS_ERROR_HTML_MAXFILESIZE'),
      3=>JText::_('JGS_ERROR_FILE_PARTLY_UPLOADED'),
    );

    if(in_array($uploaderror, $uploadErrors))
    {
      echo JText::_('JGS_ERROR_CODE') . $uploadErrors[$uploaderror] . '<br />';
    }
    else
    {
      echo JText::_('JGS_ERROR_CODE') . JText::_('JGS_ERROR_UNKNOWN') . ' <br />';
    }
  }//End function Upload_CheckError


  /**
   * Calculate the memory limit
   */
  function Upload_CheckMemory(&$debugoutput, $filename, $format)
  {
    $config = Joom_getConfig();
    if((function_exists('memory_get_usage')) && (ini_get('memory_limit')))
    {
      $imageInfo = getimagesize($filename);
      $jpgpic = false;
      switch(strtoupper($format))
      {
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
      $MB  = 1048576;
      $K64 = 65536;

      if($config->jg_fastgd2thumbcreation && $jpgpic && $config->jg_thumbcreation == 'gd2')
      {
        //function of fast gd2 creation needs more memory
        $corrfactor = 2.1;
      }
      else
      {
        $corrfactor = 1.7;
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
      if(!empty($memory_limit) && $memory_limit != 0)
      {
        $memory_limit = substr($memory_limit, 0, -1) * 1024 * 1024;
      }

      if($memory_limit != 0 && $memoryNeeded > $memory_limit)
      {
        $memoryNeededMB = round ($memoryNeeded / 1024 / 1024, 0);
        $debugoutput .= JText::_('JGS_ERROR_MEM_EXCEED').
                        $memoryNeededMB." MByte ("
                        .$memoryNeeded.") Serverlimit: "
                        .$memory_limit/$MB."MByte (".$memory_limit.")<br/>" ;
        return false;
      }
    }
    return true;
  }//function Upload_CheckMemory


  /**
   * Rollback an erroneous upload
   *
   * @param string debug to output
   * @param string path to original picture
   * @param string path to detail picture
   * @param string path to thumbnail
   */
  function Upload_Rollback(&$debugoutput,$original, $detail, $thumb)
  {
    if(!is_null($original) && JFile::exists($original))
    {
      $returnval = JFile::delete($original);
      if($returnval)
      {
        $debugoutput .= JText::_('JGS_UPLOAD_RB_ORGDEL_OK').'<br/>';
      }
      else
      {
        $debugoutput .= JText::_('JGS_UPLOAD_RB_ORGDEL_NOK').'<br/>';
      }
    }

    if(!is_null($detail) && JFile::exists($detail))
    {
      $returnval = JFile::delete($detail);
      if($returnval)
      {
        $debugoutput .= JText::_('JGS_UPLOAD_RB_DTLDEL_OK').'<br/>';
      }
      else
      {
        $debugoutput .= JText::_('JGS_UPLOAD_RB_DTLDEL_NOK').'<br/>';
      }
    }

    if(!is_null($thumb) && JFile::exists($thumb))
    {
      $returnval=JFile::delete($thumb);
      if($returnval)
      {
        $debugoutput .= JText::_('JGS_UPLOAD_RB_THBDEL_OK').'<br/>';
      }
      else
      {
        $debugoutput .= JText::_('JGS_UPLOAD_RB_THBDEL_NOK').'<br/>';
      }
    }
  }//End function Upload_Rollback

}//End class Joom_Upload
?>
