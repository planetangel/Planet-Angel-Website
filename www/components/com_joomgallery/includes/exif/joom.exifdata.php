<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/exif/joom.exifdata.php $
// $Id: joom.exifdata.php 449 2009-06-14 11:57:04Z aha $
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

if(file_exists(JPath::clean(JPATH_ROOT.DS.$this->joom_originalsource))) 
{
  $language = & JFactory::getLanguage();
  $language->load('com_joomgallery.exif');

  include_once(JPATH_COMPONENT.DS.'includes'.DS.'exif'.DS.'html'.DS.'joom.exifdata.html.php');

  // PHP's exif only accepts JPEGs or TIFFs
  $valid_extensions = array('jpg', 'jpeg', 'jpe');
  $fileextension    = strtolower(ereg_replace('.*\.([^\.]*)$', '\\1', $this->imgfilename));
  $exif_array       = array();
  if(in_array($fileextension, $valid_extensions))
  {
    $exif_array = @exif_read_data(JPath::clean(JPATH_ROOT.DS.$this->joom_originalsource), 
                                               'EXIF, IFD0, GPS', true);
    if(!$exif_array) return;
  }

  HTML_Joom_Exif::Joom_ShowExifData_HTML($exif_array);
}
?>
