<?php
/*
// "Simple Image Gallery" (in content items) Plugin for Joomla 1.5 - Version 1.2.1
// License: http://www.gnu.org/copyleft/gpl.html
// Authors: Fotis Evangelou - George Chouliaras
// Copyright (c) 2008 JoomlaWorks.gr - http://www.joomlaworks.gr
// Project page at http://www.joomlaworks.gr - Demos at http://demo.joomlaworks.gr
// ***Last update: January 6th, 2007***
*/


defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.event.plugin');

// Simple Language Defines for English
// <--------------- English language Defines ------------------------------------->
define('_SIGPRO_GD_LIBMISSING','<b>Error</b>: GD2 library is not enabled in your server!');
define('_SIGPRO_GD_LIBNOJPG','<b>Error</b>: GD2 library does not support JPG!');
define('_SIGPRO_GD_LIBNOGIF','<b>Error</b>: GD2 library does not support GIF!');
define('_SIGPRO_GD_LIBNOPNG','<b>Error</b>: GD2 library does not support PNG!');

define('_SIGPRO_JVERSION','<b>Error</b>: JoomlaWorks "Simple Image Gallery (j!1.5)" Plugin functions only under Joomla! 1.5');
define('_SIGPRO_SIGFREECHECK','<b>Error</b>: "Simple Image Gallery" (free version) has been located in your system. You need to uninstall it first, before using the PRO version. Thank you.');
define('_SIGPRO_TEMPFOLDERERROR','<b>Error</b>: "Simple Image Gallery Pro" could not create the required "temp" folder (used for thumbnail storage). Please create this folder manually and set its permissions (chmod) to 644 or 777. Thank you.');
define('_SIGPRO_THUMBERROR','<span>Error creating thumbnail!</span>');
// <--------------- END --------------------------------------------------------->



class plgContentJwsig extends JPlugin
{
	//Constructor
	function plgContentJwsig( &$subject )
	{
		parent::__construct( $subject );
		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'content', 'jwsig' );
		$this->_params = new JParameter( $this->_plugin->params );
	}


	function onPrepareContent(&$row, &$params, $limitstart) {

		// just startup
		global $mainframe;
		
		// root folder
		$rootfolder = '/images/stories/';

		// checking
		if ( !preg_match("#{gallery}(.*?){/gallery}#s", $row->text) ) {
			return;
		}

		$plugin =& JPluginHelper::getPlugin('content', 'jwsig');
		$pluginParams = new JParameter( $plugin->params );

		// j!1.5 paths
		$mosConfig_absolute_path = JPATH_SITE;
		$mosConfig_live_site = JURI :: base();
		if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);

		// Parameters
		$_width_ 			= $pluginParams->get('th_width', 200);
		$_height_ 			= $pluginParams->get('th_height', 200);
		$_quality_ 			= $pluginParams->get('th_quality', 80);
		$displaynavtip 		= $pluginParams->get('displaynavtip', 1);
		$navtip 			= $pluginParams->get('navtip', 'Navigation tip: Hover mouse on top of the right or left side of the image to see the next or previous image respectively.');
		$displaymessage 	= $pluginParams->get('displaymessage', 1);
		$message 			= $pluginParams->get('message', 'You are browsing images from the article:');

		// GD2 Library Check
		if(function_exists("gd_info")) {
			$gdinfo = gd_info();
			$gdsupport = array();
			$version = intval(ereg_replace('[[:alpha:][:space:]()]+', '', $gdinfo['GD Version']));
			if($version!=2) $gdsupport[] = '<div class="message">'._SIGPRO_GD_LIBMISSING.'</div>';
			if (!$gdinfo['JPG Support']) $gdsupport[] = '<div class="message">'._SIGPRO_GD_LIBNOJPG.'</div>';
			if (!$gdinfo['GIF Create Support']) $gdsupport[] = '<div class="message">'._SIGPRO_GD_LIBNOGIF.'</div>';
			if (!$gdinfo['PNG Support']) $gdsupport[] = '<div class="message">'._SIGPRO_GD_LIBNOPNG.'</div>';
			if(count($gd_support)) {
				foreach ($gdsupport as $k=>$v) {echo $v;}
			}
		}

		// Version check
		$version = new JVersion();
		if( $version->PRODUCT=="Joomla!" && $version->RELEASE!="1.5") { echo '<div class="message">'._SIGPRO_JVERSION.'</div>'; }

		if (preg_match_all("#{gallery}(.*?){/gallery}#s", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
			$sigcount = -1;
			foreach ($matches[0] as $match) {
				$sigcount++;
				$_images_dir_ = preg_replace("/{.+?}/", "", $match);
				unset($images);
				$noimage = 0;
				// read directory
				if ($dh = opendir($mosConfig_absolute_path.$rootfolder.$_images_dir_)) {
					while (($f = readdir($dh)) !== false) {
						if((substr(strtolower($f),-3) == 'jpg') || (substr(strtolower($f),-3) == 'gif') || (substr(strtolower($f),-3) == 'png')) {
							$noimage++;
							$images[] = array('filename' => $f);
							array_multisort($images, SORT_ASC, SORT_REGULAR);
						}
					}
					closedir($dh);
				}
				$itemtitle = preg_replace("/\"/", "'", $row->title);
				if($noimage) {
					$html = '<!-- JW "Simple Image Gallery" Plugin (v1.2.1) starts here --><link href="'.$mosConfig_live_site.'/plugins/content/plugin_jw_sig/sig.css" rel="stylesheet" type="text/css" />
					<style type="text/css">.sig_cont {width:'.($_width_+30).'px;height:'.($_height_+20).'px;}</style>
					<script type="text/javascript" src="'.$mosConfig_live_site.'/plugins/content/plugin_jw_sig/mootools.js"></script>
					<script type="text/javascript" src="'.$mosConfig_live_site.'/plugins/content/plugin_jw_sig/slimbox.js"></script>
					<div class="sig">';
					for($a = 0;$a<$noimage;$a++) {
						if($images[$a]['filename'] != '') {
							$html .= '<div class="sig_cont"><div class="sig_thumb"><a href="'.$mosConfig_live_site.$rootfolder.$_images_dir_.'/'.$images[$a]['filename'].'" rel="lightbox[sig'.$sigcount.']" title="';
							if ($displaynavtip) {$html .= $navtip.'<br /><br />';}
							if ($displaymessage) {$html .= $message.'<br /><b>'.$itemtitle.'</b>';}
							else {$html .= '<b>'.$images[$a]['filename'].'</b>';}
							$html .= '" alt="';
							if ($displaymessage) {$html .= $message.' '.$itemtitle.'';}
							else {$html .= $images[$a]['filename'];}
							$html .= '" target="_blank"><img src="'.$mosConfig_live_site.'/plugins/content/plugin_jw_sig/showthumb.php?img='.$_images_dir_.'/'.$images[$a]['filename'].'&width='.$_width_.'&height='.$_height_.'&quality='.$_quality_.'"></a></div></div>';
						}
					}
					$html .="\n<div class=\"sig_clr\"></div>\n</div>\n<!-- JW \"Simple Image Gallery\" Plugin (v1.2.1) ends here -->";
				}
				$row->text = preg_replace( "#{gallery}".$_images_dir_."{/gallery}#s", $html , $row->text );
			}
		}



	}


}

?>
