<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/admin.joomgallery.php $
// $Id: admin.joomgallery.php 449 2009-06-14 11:57:04Z aha $
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

define('_JOOM_LIVE_SITE', JURI::root());

require_once(JPATH_COMPONENT.DS.'joomgallery.class.php');
require_once(JPATH_COMPONENT.DS.'admin.joomgallery.html.php');
require_once(JPATH_COMPONENT.DS.'common.joomgallery.php');

//configuration
$config = Joom_getConfig();

//sanitize other variables
$id                    = Joom_mosGetParam('id', null);
$act                   = Joom_mosGetParam('act', null);
$task                  = Joom_mosGetParam('task', null);
$cid                   = Joom_mosGetParam('cid', '');
$catid                 = JRequest::getInt('catid', '');

define('_JOOM_OPTION', JRequest::getCmd('option', 'com_joomgallery'));

  if($act)
  {
    $task = $act;
  }

  switch($task)
  {
    //Categories
    case 'categories':
    case 'orderupcatg':
    case 'orderdowncatg':
    case 'publishcatg':
    case 'unpublishcatg':
    case 'approvecat':
    case 'rejectcat':
    case 'newcatg':
    case 'savenewcatg':
    case 'editcatg':
    case 'saveeditcatg':
    case 'removecatg':
    case 'cancelcatg':
      require_once(JPATH_COMPONENT.DS.'includes'.DS.'admin.categories.php');
      $categoryclass = new Joom_AdminCategories($task, $cid);
      break;

    //Pictures
    case 'pictures':
    case 'newpic':
    case 'savenewpic':
    case 'editpic':
    case 'saveeditpic':
    case 'canceleditpic':
    case 'movepic':
    case 'savemovepic':
    case 'publishpic':
    case 'unpublishpic':
    case 'approvepic':
    case 'rejectpic':
    case 'orderup':
    case 'orderdown':
    case 'removepic':
      require_once(JPATH_COMPONENT.DS.'includes'.DS.'admin.pictures.php');
      $pictureclass = new Joom_AdminPictures($task, $cid, $id);
      break;

    //Categories & Pictures
    case 'saveorder':
      Joom_SaveOrder($id, $cid);
      break;

    //Comments
    case 'comments':
    case 'publishcmt':
    case 'unpublishcmt':
    case 'approvecmt':
    case 'rejectcmt':
    case 'removecmt':
      require_once(JPATH_COMPONENT.DS.'includes'.DS.'admin.comments.php');
      $commentsclass = new Joom_AdminComments($task, $id);
      break;

    // Votes
    case 'votes':
      require_once(JPATH_COMPONENT.DS.'includes'.DS.'admin.votes.php');
      $voteclass = new Joom_AdminVotes($task);
      break;

    //Uploads
    case 'upload':
    case 'batchupload':
    case 'ftpupload':
    case 'jupload':
      require_once(JPATH_COMPONENT.DS.'includes'.DS.'admin.uploads.php');
      $adminuploadclass = new Joom_AdminUploads($task);
      break;

    //Upload functions
    case 'uploadhandler':
    case 'batchuploadhandler':
    case 'ftpuploadhandler':
    case 'juploadhandler_receive':
      require_once(JPATH_COMPONENT.DS.'adminclasses'.DS.'admin.upload.class.php');
      $uploadclass = new Joom_Upload($task, $catid);
      break;

    //Configuration
    case 'configuration':
      //check update files from previous update and delete them
      jimport('joomla.filesystem.file');
      if(JFile::exists(JPATH_COMPONENT.DS.'joomgalleryupdate.xml')) {
        JFile::delete(JPATH_COMPONENT.DS.'joomgalleryupdate.xml');
      }
      if(JFile::exists(JPath::clean(JPATH_ROOT.DS.$config->jg_pathtemp.'update.zip'))){
        JFile::delete(JPath::clean(JPATH_ROOT.DS.$config->jg_pathtemp.'update.zip'));
      }
      if(JFolder::exists(JPath::clean(JPATH_ROOT.DS.$config->jg_pathtemp.'update'))){
        JFolder::delete(JPath::clean(JPATH_ROOT.DS.$config->jg_pathtemp.'update'));
      }
      require_once(JPATH_COMPONENT.DS.'adminclasses'.DS.'admin.tabs.class.php');
    case 'saveconfiguration':
      require_once(JPATH_COMPONENT.DS.'includes'.DS.'admin.configuration.php');
      $configurationclass = new Joom_AdminConfiguration($task);
      break;

    // CSS-modification
    case 'savecss':
    case 'cancelcss':
    case 'deletecss':
    case 'editcss':
      require_once(JPATH_COMPONENT.DS.'includes'.DS.'admin.cssedit.php');
      $cssclass = new Joom_AdminCssEdit($task);
      break;

    //Migration
    case 'migrate':
      require_once(JPATH_COMPONENT.DS.'includes'.DS.'admin.migration.php');
      $adminmigrateclass = new Joom_AdminMigration($task);
      break;

    //Help
    case 'help':
      require_once(JPATH_COMPONENT.DS.'includes'.DS.'admin.help.php');
      $adminhelpclass = new Joom_AdminHelp($task);
      break;

    //Auto-Update with curl
    case 'autoupdate':
      Joom_AutoUpdate();
      break;

    case 'recreate':
      $count  = Joom_Recreate();
      if(!$count[0])
      {
        $type = 'error';
        $msg  = $count[1];
      }
      else
      {
        $type = 'message';
        if($count[0] == 1)
        {
          $msg  = JText::_('JGA_THUMB_SUCCESSFULLY_RECREATED');
        }
        else
        {
          $msg  = JText::sprintf('JGA_THUMBS_SUCCESSFULLY_RECREATED', $count[0]);
        }
        if($count[1])
        {
          if($count[1] == 1)
          {
            $msg  .= '</li><li>'.JText::_('JGA_IMG_SUCCESSFULLY_RECREATED'); //additionally one image was recreated successfully
          }
          else
          {
            $msg  .= '</li><li>'.JText::sprintf('JGA_IMGS_SUCCESSFULLY_RECREATED', $count[1]); //additionally %d images were recreated successfully
          }
        }
      }

      //some messages are enqueued by the model
      $mainframe->redirect('index.php?option='._JOOM_OPTION.'&act=pictures', $msg, $type);
      break;

    //Default
    default:
      Joom_ShowMenu_HTML();
      break;
  }

$tasks = array('categories','pictures','comments','votes','upload','batchupload',
               'ftpupload','jupload','configuration','editcss','migrate','help');
if($config->jg_checkupdate && in_array($task, $tasks)){
  $dated_extensions = Joom_CheckUpdate();
  if(count($dated_extensions)){
    JError::raiseNotice('302', JText::_('JGA_SYSTEM_NOT_UPTODATE'));
  }
}
if(in_array($task, $tasks)){
  Joom_ShowFooter_HTML();
}

/**
 * fetches an update zip file from JoomGallery server and extracts it
 */
function Joom_AutoUpdate(){
  $extension  = Joom_mosGetParam('extension', 0, 'get');
  $extensions = Joom_CheckUpdate();

  if(!isset($extensions[$extension]['updatelink']) || !extension_loaded('curl')){
    $mainframe = & JFactory::getApplication('administrator');
    $mainframe->redirect('index.php?option='._JOOM_OPTION, 'Could not fetch update zip', 'error');
  }

  //create curl resource
  $ch = curl_init($extensions[$extension]['updatelink']);

  //some settings for curl
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  //create the zip file
  jimport('joomla.filesystem.file');
  $config = Joom_getConfig();
  $output = curl_exec($ch);
  JFile::write(JPath::clean(JPATH_ROOT.DS.$config->jg_pathtemp.'update.zip'), $output);

  //close curl resource to free up system resources
  curl_close($ch);

  //extract the zip file
  include(JPATH_ADMINISTRATOR.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php');
  $zipfile  = new PclZip(JPath::clean(JPATH_ROOT.DS.$config->jg_pathtemp.'update.zip'));
  $folder   = JPath::clean(JPATH_ROOT.DS.$config->jg_pathtemp.'update');
  $zipfile->extract(PCLZIP_OPT_PATH, $folder);
  if($zipfile->error_code != 1){
    $mainframe = & JFactory::getApplication('administrator');
    $mainframe->redirect('index.php?option='._JOOM_OPTION, $zipfile->errorInfo(), 'error');
  }

  JError::raiseNotice('301', JText::_('JGA_REDIRECT_NOTE'));

  //let's ask Joomla! to do the rest
?>
  <form action="index.php" method="post" name="JoomUpdateForm">
    <input type="hidden" name="installtype" value="folder" />
    <input type="hidden" name="install_directory" value="<?php echo $folder; ?>" />
    <input type="hidden" name="task" value="doInstall" />
    <input type="hidden" name="option" value="com_installer" />
    <?php echo JHTML::_('form.token'); ?>
  </form>
  <script type="text/javascript">
    document.JoomUpdateForm.submit();
  </script>
<?php
}

/**
 * recreates thumbnails of the selected images
 * if original image is existent, detail image will be recreated, too.
 */
function Joom_Recreate()
{
  jimport('joomla.filesystem.file');
  $mainframe  = & JFactory::getApplication('administrator');
  $database   = & JFactory::getDBO();
  $config     = Joom_GetConfig();

  $cids         = $mainframe->getUserStateFromRequest('joom.recreate.ids', 'id', array(), 'array');
  $thumb_count  = $mainframe->getUserState('joom.recreate.thumbcount');
  $img_count    = $mainframe->getUserState('joom.recreate.imgcount');

  $row  = new mosjoomgallery($database);

  // before first loop check for selected images
  if(is_null($thumb_count) AND !count($cids))
  {
    return array(false, JText::_('JGA_NO_IMAGES_SELECTED'));
  }

  //Check the maximum execution time of the script
  //set secure setting of the real execution time
  $max_execution_time = @ini_get('max_execution_time');

  //try to set the max execution time to 60s if lower than
  //if not succesful the return value will be the old time, so use this
  if ($max_execution_time < 60)
  {
    #@ini_set('max_execution_time','60');
    $max_execution_time = @ini_get('max_execution_time');
  }

  $maxtime            = (int) $max_execution_time * 0.8;
  $starttime          = time();

  $debugoutput = '';

  //loop through selected images
  foreach($cids as $key => $cid)
  {
    $row->load($cid);
    //catpath for category
    $catpath = Joom_GetCatPath($row->catid);

    $orig   = JPATH_ROOT.DS.$config->jg_pathoriginalimages.$catpath.$row->imgfilename;
    $img    = JPATH_ROOT.DS.$config->jg_pathimages.$catpath.$row->imgfilename;
    $thumb  = JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->imgthumbname;
    //check if there is an original image
    if(JFile::exists($orig))
    {
      $orig_existent = true;
    }
    else
    {
      //if not, use detail image to create thumbnail
      $orig_existent = false;
      if(JFile::exists($img))
      {
        $orig = $img;
      }
      else
      {
        JError::raiseWarning(100, JText::sprintf('JGA_IMG_NOT_EXISTENT', $img));
        $mainframe->setUserState('joom.recreate.cids', array());
        $mainframe->setUserState('joom.recreate.count', null);
        return false;
      }
    }

    //TODO: move image into a trash instead of deleting immediately for possible rollback
    JFile::delete($thumb);
    $return = Joom_ResizeImage( $debugoutput,
                                $orig,
                                $thumb,
                                $config->jg_useforresizedirection,
                                $config->jg_thumbwidth,
                                $config->jg_thumbheight,
                                $config->jg_thumbcreation,
                                $config->jg_thumbquality
                               );
    if(!$return)
    {
      JError::raiseWarning(100, JText::sprintf('JGA_COULD_NOT_CREATE_THUMB', $thumb));
      $mainframe->setUserState('joom.recreate.cids', array());
      $mainframe->setUserState('joom.recreate.thumbcount', null);
      $mainframe->setUserState('joom.recreate.imgcount', null);
      return false;
    }
    $mainframe->enqueueMessage(JText::sprintf('JGA_SUCCESSFULLY_CREATED_THUMB', $row->id, $row->imgtitle));
    $thumb_count++;

    if($orig_existent)
    {
      //TODO: move image into a trash instead of deleting immediately for possible rollback
      JFile::delete($img);
      $return = Joom_ResizeImage( $debugoutput,
                                  $orig,
                                  $img,
                                  false,
                                  $config->jg_maxwidth,
                                  false,
                                  $config->jg_thumbcreation,
                                  $config->jg_picturequality,
                                  true
                                 );
      if(!$return)
      {
        JError::raiseWarning(100, JText::sprintf('JGA_COULD_NOT_CREATE_IMG', $img));
        $this->_mainframe->setUserState('joom.recreate.cids', array());
        $this->_mainframe->setUserState('joom.recreate.thumbcount', null);
        $this->_mainframe->setUserState('joom.recreate.imgcount', null);
        return false;
      }
    }
    $mainframe->enqueueMessage(JText::sprintf('JGA_SUCCESSFULLY_CREATED_IMG', $row->id, $row->imgtitle));
    $img_count++;

    unset($cids[$key]);

    //check remaining time
    $timeleft = -(time() - $starttime - $maxtime);
    if($timeleft <= 0 AND count($cids))
    {
      $mainframe->setUserState('joom.recreate.cids', $cids);
      $mainframe->setUserState('joom.recreate.thumbcount', $thumb_count);
      $mainframe->setUserState('joom.recreate.imgcount', $img_count);
      $mainframe->redirect('index.php?option='._JOOM_OPTION.'&task=recreate', JText::_('JGA_REDIRECT'));
    }  
  }

  $mainframe->setUserState('joom.recreate.cids', array());
  $mainframe->setUserState('joom.recreate.thumbcount', null);
  $mainframe->setUserState('joom.recreate.imgcount', null);
  return array($thumb_count, $img_count);
}
