<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.0/JG/trunk/components/com_joomgallery/includes/iptc/joom.iptcdata.php $
// $Id: joom.iptcdata.php 801 2008-12-15 22:05:37Z mab $
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
  include_once(JPATH_COMPONENT.DS.'includes'.DS.'iptc'.DS.'html'.DS.'joom.iptcdata.html.php');

  $language = & JFactory::getLanguage();
  $language->load('com_joomgallery.iptc');

  $valid_extensions = array('jpg','jpeg','jpe');
  $fileextension    = strtolower(ereg_replace('.*\.([^\.]*)$', '\\1', $this->imgfilename));
  $iptc_array       = array();
  if(in_array($fileextension, $valid_extensions))
  {
    $iptcimage = getimagesize(JPath::clean(JPATH_ROOT.DS.$this->joom_originalsource), $info);
    if(isset($info['APP13'])) 
    {
      $iptc_array = iptcparse($info['APP13']);
    }
    if(!$iptc_array) return;
  }

  HTML_Joom_Iptc::Joom_ShowIptcData_HTML($iptc_array);
}
?>
