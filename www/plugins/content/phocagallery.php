<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @plugin Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
if (!JComponentHelper::isEnabled('com_phocagallery', true)) {
	return JError::raiseError(JText::_('Phoca Gallery Error'), JText::_('Phoca Gallery is not installed on your system'));
}
if (! class_exists('PhocaGalleryLoader')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocagallery'.DS.'libraries'.DS.'loader.php');
}
phocagalleryimport('phocagallery.path.path');
phocagalleryimport('phocagallery.library.library');
phocagalleryimport('phocagallery.text.text');
phocagalleryimport('phocagallery.access.access');
phocagalleryimport('phocagallery.file.file');
phocagalleryimport('phocagallery.file.filethumbnail');
phocagalleryimport('phocagallery.image.image');
phocagalleryimport('phocagallery.image.imagefront');
phocagalleryimport('phocagallery.render.renderfront');
phocagalleryimport('phocagallery.render.renderadmin');

class plgContentPhocaGallery extends JPlugin
{	
	var $_plugin_number	= 0;
	
	function _setPluginNumber() {
		$this->_plugin_number = (int)$this->_plugin_number + 1;
	}
	
	function plgContentPhocaGallery( &$subject, $params ) {
        parent::__construct( $subject, $params  );
    }

	function onPrepareContent( &$article, &$params, $limitstart )
	{
		$user		= &JFactory::getUser();
		$gid 		= $user->get('aid', 0);
		$db 		= &JFactory::getDBO();
		$menu 		= &JSite::getMenu();
		$document	= &JFactory::getDocument();
		
		// PARAMS - direct from Phoca Gallery Global configuration
		$component 		= 'com_phocagallery';
		$table 			=& JTable::getInstance('component');
		$table->loadByOption( $component );
		$paramsC	 	= new JParameter( $table->params );
		
		// LIBRARY
		$library 							= &PhocaGalleryLibrary::getLibrary();
		$libraries['pg-css-sbox-plugin'] 	= $library->getLibrary('pg-css-sbox-plugin');
		$libraries['pg-css-pg-plugin'] 		= $library->getLibrary('pg-css-pg-plugin');
		$libraries['pg-css-ie'] 			= $library->getLibrary('pg-css-ie');
		$libraries['pg-group-shadowbox']	= $library->getLibrary('pg-group-shadowbox');
		$libraries['pg-group-highslide']	= $library->getLibrary('pg-group-highslide');
		$libraries['pg-overlib-group']		= $library->getLibrary('pg-overlib-group');
		
		// PicLens CSS and JS will be loaded only one time in the site (pg-pl-piclens)
		// BUT PicLens Category will be loaded everytime new category should be displayed on the site
		$libraries['pg-pl-piclens']	= $library->getLibrary('pg-pl-piclens');
		
		
		// Start Plugin
		$regex_one		= '/({phocagallery\s*)(.*?)(})/si';
		$regex_all		= '/{phocagallery\s*.*?}/si';
		$matches 		= array();
		$count_matches	= preg_match_all($regex_all,$article->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
		$cssPgPlugin	= '';
		$cssSbox		= '';
		
	// Start if count_matches
	if ($count_matches != 0) {
		
		// Start CSS
		$document->addStyleSheet(JURI::base(true).'/components/com_phocagallery/assets/phocagallery.css');
	
		for($i = 0; $i < $count_matches; $i++) {
			
			$this->_setPluginNumber();
			// Plugin variables
			$view 					= '';
			$catid					= 0;
			$imageid				= 0;
			$imagerandom			= 0;
			$image_background_shadow			= $paramsC->get( 'image_background_shadow', 'none');
			$limitstart				= 0;
			$limitcount				= 0;
			$switch_width			= $paramsC->get( 'switch_width', 640);
			$switch_height			= $paramsC->get( 'switch_height', 480);
			$basic_image_id			= $paramsC->get( 'switch_image', 0);
			$enable_switch			= 0;
			
			$display_name 			= $paramsC->get( 'display_name', 1);
			$display_icon_detail 	= $paramsC->get( 'display_icon_detail', 1);
			$display_icon_download 	= $paramsC->get( 'display_icon_download', 1);
			$detail_window 			= $paramsC->get( 'detail_window', 0);
			$detail_buttons			= $paramsC->get( 'detail_buttons', 1);
			$hide_categories		= $paramsC->get( 'hide_categories', '');
			
			$namefontsize			= $paramsC->get( 'font_size_name', 12);
			$namenumchar			= $paramsC->get( 'char_length_name', 11);
			
			$display_description	= $paramsC->get( 'display_description_detail', 0);
			$description_height		= $paramsC->get( 'description_detail_height', 16);
			$category_box_space		= $paramsC->get( 'category_box_space', 0);
			
			// CSS
			$font_color 			= $paramsC->get( 'font_color', '#b36b00');
			$background_color 		= $paramsC->get( 'background_color', '#fcfcfc');
			$background_color_hover = $paramsC->get( 'background_color_hover', '#f5f5f5');
			$image_background_color = $paramsC->get( 'image_background_color', '#f5f5f5');
			$border_color 			= $paramsC->get( 'border_color', '#e8e8e8');
			$border_color_hover 	= $paramsC->get( 'border_color_hover', '#b36b00');
			
			$highslide_class		= $paramsC->get( 'highslide_class', 'rounded-white');
			$highslide_opacity		= $paramsC->get( 'highslide_opacity', 0);
			$highslide_outline_type	= $paramsC->get( 'highslide_outline_type', 'rounded-white');
			$highslide_fullimg		= $paramsC->get( 'highslide_fullimg', 0);
			$highslide_slideshow	= $paramsC->get( 'highslide_slideshow', 1);
			$highslide_close_button	= $paramsC->get( 'highslide_close_button', 0);
			$display_rating_img		= $paramsC->get( 'display_rating_img', 0);
			
			
			$float					= '';
			$enable_piclens			= $paramsC->get( 'enable_piclens', 0);
			$enable_overlib			= $paramsC->get( 'enable_oberlib', 0);
			
			// Image categories
			$img_cat				= 1;
			$img_cat_size			= 'small';
			
			// Get plugin parameters
			$phocagallery	= $matches[0][$i][0];
			preg_match($regex_one,$phocagallery,$phocagallery_parts);
			$parts			= explode("|", $phocagallery_parts[2]);
			$values_replace = array ("/^'/", "/'$/", "/^&#39;/", "/&#39;$/", "/<br \/>/");

			foreach($parts as $key => $value) {
				$values = explode("=", $value, 2);
				
				foreach ($values_replace as $key2 => $values2) {
					$values = preg_replace($values2, '', $values);
				}
				
				// Get plugin parameters from article
					 if($values[0]=='view')				{$view					= $values[1];}
				else if($values[0]=='categoryid')		{$catid					= $values[1];}
				else if($values[0]=='imageid')			{$imageid				= $values[1];}
				else if($values[0]=='imagerandom')		{$imagerandom			= $values[1];}
				else if($values[0]=='imageshadow')		{$image_background_shadow			= $values[1];}
				else if($values[0]=='limitstart')		{$limitstart			= $values[1];}
				else if($values[0]=='limitcount')		{$limitcount			= $values[1];}
				else if($values[0]=='detail')			{$detail_window			= $values[1];}
				else if($values[0]=='displayname')		{$display_name			= $values[1];}
				else if($values[0]=='displaydetail')	{$display_icon_detail		= $values[1];}
				else if($values[0]=='displaydownload')	{$display_icon_download	= $values[1];}
				else if($values[0]=='displaybuttons')	{$detail_buttons		= $values[1];}
				else if($values[0]=='displayratingimg')	{$display_rating_img	= $values[1];}
				
				else if($values[0]=='namefontsize')		{$namefontsize			= $values[1];}
				else if($values[0]=='namenumchar')		{$namenumchar			= $values[1];}
				
				else if($values[0]=='displaydescription'){$display_description	= $values[1];}
				else if($values[0]=='descriptionheight'){$description_height	= $values[1];}
				else if($values[0]=='hidecategories')	{$hide_categories		= $values[1];}
				else if($values[0]=='boxspace')			{$category_box_space	= $values[1];}
				
				// CSS
				else if($values[0]=='fontcolor')		{$font_color				= $values[1];}
				else if($values[0]=='bgcolor')			{$background_color			= $values[1];}
				else if($values[0]=='bgcolorhover')		{$background_color_hover	= $values[1];}
				else if($values[0]=='imagebgcolor')		{$image_background_color	= $values[1];}
				else if($values[0]=='bordercolor')		{$border_color				= $values[1];}
				else if($values[0]=='bordercolorhover')	{$border_color_hover		= $values[1];}
				
				else if($values[0]=='hsclass')			{$highslide_class			= $values[1];}
				else if($values[0]=='hsopacity')		{$highslide_opacity			= $values[1];}
				else if($values[0]=='hsoutlinetype')	{$highslide_outline_type	= $values[1];}
				else if($values[0]=='hsfullimg')		{$highslide_fullimg			= $values[1];}
				else if($values[0]=='hsslideshow')		{$highslide_slideshow		= $values[1];}
				else if($values[0]=='hsclosebutton')	{$highslide_close_button	= $values[1];}
				
				else if($values[0]=='float')			{$float	= $values[1];}
				
				//Image categories
				else if($values[0]=='imagecategories')		{$img_cat				= $values[1];}
				else if($values[0]=='imagecategoriessize')	{$img_cat_size			= $values[1];}
				else if($values[0]=='switchwidth')			{$switch_width			= $values[1];}
				else if($values[0]=='switchheight')			{$switch_height			= $values[1];}
				else if($values[0]=='basicimageid')			{$basic_image_id		= $values[1];}
				else if($values[0]=='enableswitch')			{$enable_switch			= $values[1];}
				
				else if($values[0]=='piclens')				{$enable_piclens				= $values[1];}
				else if($values[0]=='overlib')				{$enable_overlib				= $values[1];}
			}
			
			
			// Every loop of plugin has own number
			// Add custom CSS for every image (every image can have other CSS, Hover doesn't work in IE6)
			$iCss = $this->_plugin_number;
			$cssPgPlugin	.= " .pgplugin".$iCss." {border:1px solid $border_color ; background: $background_color ;}\n"
							." .pgplugin".$iCss.":hover, .pgplugin".$i.".hover {border:1px solid $border_color_hover ; background: $background_color_hover ;}\n";
								
			
			// PARAMS - direct from Phoca Gallery Global configuration
			$component 		= 'com_phocagallery';
			$table 			=& JTable::getInstance('component');
			$table->loadByOption( $component );
			$paramsC	 	= new JParameter( $table->params );
			
			$tmpl['formaticon'] 		= $paramsC->get( 'icon_format', 'gif' );
			
			$medium_image_width 		= $paramsC->get( 'medium_image_width', 100 );
			$medium_image_height 		= $paramsC->get( 'medium_image_height', 100 );
			$popup_window_width 		= $paramsC->get( 'front_modal_box_width', 680 );
			$popup_window_height 		= $paramsC->get( 'front_modal_box_height', 560 );
			$small_image_width 			= $paramsC->get( 'small_image_width', 50 );
			$small_image_height 		= $paramsC->get( 'small_image_height', 50 );
			
			
			if ($display_description == 1) {
				$popup_window_height	= $popup_window_height + $description_height;
			}
			
			// Detail buttons in detail view
			if ($detail_buttons != 1) {
				$popup_window_height	= $popup_window_height - 45;
			}
			if ($display_rating_img == 1) {
		
				$popup_window_height	= $popup_window_height + 35;
			}
			
			$modal_box_overlay_color 	= $paramsC->get( 'modal_box_overlay_color','#000000' );
			$modal_box_overlay_opacity 	= $paramsC->get( 'modal_box_overlay_opacity', 0.3 );
			$modal_box_border_color 	= $paramsC->get( 'modal_box_border_color', '#6b6b6b' );
			$modal_box_border_width 	= $paramsC->get( 'modal_box_border_width', 2 );
			
			$tmpl['olbgcolor']				= $paramsC->get( 'ol_bg_color', '#666666' );
			$tmpl['olfgcolor']				= $paramsC->get( 'ol_fg_color', '#f6f6f6' );
			$tmpl['oltfcolor']				= $paramsC->get( 'ol_tf_color', '#000000' );
			$tmpl['olcfcolor']				= $paramsC->get( 'ol_cf_color', '#ffffff' );
			$tmpl['overliboverlayopacity']	= $paramsC->get( 'overlib_overlay_opacity', 0.7 );
			
			
			
			// Window
			// =======================================================
			// DIFFERENT METHODS OF DISPLAYING THE DETAIL VIEW
			// =======================================================
			
			// MODAL - will be displayed in case e.g. highslide or shadowbox too, because in there are more links 
			JHTML::_('behavior.modal', 'a.modal-button');

			if ($cssSbox == '') {
				$cssSbox .= " #sbox-window {background-color:".$modal_box_border_color.";padding:".$modal_box_border_width."px} \n"
				." #sbox-overlay {background-color:".$modal_box_overlay_color.";} \n";
			}
			
			// BUTTON (IMAGE - standard, modal, shadowbox)
			$button = new JObject();
			$button->set('name', 'image');

			// BUTTON (ICON - standard, modal, shadowbox)
			$button2 = new JObject();
			$button2->set('name', 'icon');

			// BUTTON OTHER (geotagging, downloadlink, ...)
			$buttonOther = new JObject();
			$buttonOther->set('name', 'other');

			$tmpl ['highslideonclick']	= '';// for using with highslide
			
			// -------------------------------------------------------
			// STANDARD POPUP
			// -------------------------------------------------------

			if ($detail_window == 1) {
				$button->set('methodname', 'js-button');
				$button->set('options', "window.open(this.href,'win2','width=".$popup_window_width.",height=".$popup_window_height.",menubar=no,resizable=yes'); return false;");
				
				$button2->methodname 		= &$button->methodname;
				$button2->options 			= &$button->options;
				$buttonOther->methodname  	= &$button->methodname;
				$buttonOther->options 		= &$button->options;
				
			}
			
			// -------------------------------------------------------
			// MODAL BOX
			// -------------------------------------------------------

			else if ($detail_window == 0 || $detail_window == 2) { 
				
				// Button
				$button->set('modal', true);
				$button->set('methodname', 'modal-button');
				
				$button2->modal 			= &$button->modal;
				$button2->methodname 		= &$button->methodname;
				$buttonOther->modal 		= &$button->modal;
				$buttonOther->methodname  	= &$button->methodname;
				
				// Modal - Image only
				if ($detail_window == 2) {
					
					$button->set('options', "{handler: 'image', size: {x: 200, y: 150}, overlayOpacity: ".$modal_box_overlay_opacity.", classWindow: 'phocagallery-random-window', classOverlay: 'phocagallery-random-overlay'}");
					$button2->options 		= &$button->options;
					$buttonOther->set('options', "{handler: 'iframe', size: {x: ".$popup_window_width.", y: ".$popup_window_height."}, overlayOpacity: ".$modal_box_overlay_opacity.", classWindow: 'phocagallery-random-window', classOverlay: 'phocagallery-random-overlay'}");
				
				// Modal - Iframe 			
				} else {
				
					$button->set('options', "{handler: 'iframe', size: {x: ".$popup_window_width.", y: ".$popup_window_height."}, overlayOpacity: ".$modal_box_overlay_opacity.", classWindow: 'phocagallery-random-window', classOverlay: 'phocagallery-random-overlay'}");
					$button2->options 		= &$button->options;
					$buttonOther->options  	= &$button->options;
				
				}
			}
			
			// -------------------------------------------------------
			// SHADOW BOX
			// -------------------------------------------------------

			else if ($detail_window == 3) {

				// Random Number - because of more plugins on the site
				$randName	= 'PhocaGalleryPl' . $iCss;
				$randName2	= 'PhocaGalleryPl2' . $iCss;
				
				$sb_slideshow_delay			= $paramsC->get( 'sb_slideshow_delay', 5 );
				$sb_lang					= $paramsC->get( 'sb_lang', 'en' );
				
				$button->set('methodname', 'shadowbox-button-rim');
				$button->set('options', "shadowbox[".$randName."];options={slideshowDelay:".$sb_slideshow_delay."}");
					
				$button2->methodname 		= &$button->methodname;
				$button2->set('options', "shadowbox[".$randName2."];options={slideshowDelay:".$sb_slideshow_delay."}");
				
				$buttonOther->set('modal', true);
				$buttonOther->set('methodname', 'modal-button');
				$buttonOther->set('options', "{handler: 'iframe', size: {x: ".$popup_window_width.", y: ".$popup_window_height."}, overlayOpacity: ".$modal_box_overlay_opacity.", classWindow: 'phocagallery-random-window', classOverlay: 'phocagallery-random-overlay'}");
				
				
				
					$document->addScript(JURI::base(true).'/components/com_phocagallery/assets/js/shadowbox/adapter/shadowbox-mootools.js');
					$document->addScript(JURI::base(true).'/components/com_phocagallery/assets/js/shadowbox/shadowbox.js');	
					
				if ( $libraries['pg-group-shadowbox']->value == 0 ) {
					$document->addCustomTag('<script type="text/javascript">
			Shadowbox.loadSkin("classic", "'.JURI::base(true).'/components/com_phocagallery/assets/js/shadowbox/src/skin");
			Shadowbox.loadLanguage("'.$sb_lang.'", "'.JURI::base(true).'/components/com_phocagallery/assets/js/shadowbox/src/lang");
			Shadowbox.loadPlayer(["img"], "'.JURI::base(true).'/components/com_phocagallery/assets/js/shadowbox/src/player");
			window.onload = function(){
			Shadowbox.init();
			}
			</script>');
					$library->setLibrary('pg-group-shadowbox', 1);
				}
			}
			
			// -------------------------------------------------------
		// HIGHSLIDE JS
		// -------------------------------------------------------

		else if ($detail_window == 4) {
			
			$button->set('methodname', 'highslide');
			$button2->methodname 		= &$button->methodname;
			$buttonOther->methodname 	= &$button->methodname;
			
			$document->addScript(JURI::base(true).'/components/com_phocagallery/assets/js/highslide/highslide-full.js');
			$document->addStyleSheet(JURI::base(true).'/components/com_phocagallery/assets/js/highslide/highslide.css');
					
			if ( $libraries['pg-group-highslide']->value == 0 ) {
				$document->addCustomTag( PhocaGalleryRenderFront::renderHighslideJSAll());
				$document->addCustomTag('<!--[if lt IE 7]><link rel="stylesheet" type="text/css" href="'.JURI::base(true).'/components/com_phocagallery/assets/js/highslide/highslide-ie6.css" /><![endif]-->');
		$library->setLibrary('pg-group-highslide', 1);
			}
			
			$document->addCustomTag( PhocaGalleryRenderFront::renderHighslideJS('pl', $popup_window_width, $popup_window_height, $highslide_outline_type, $highslide_opacity));
			$tmpl['highslideonclick'] = 'return hs.htmlExpand(this, phocaZoomPl )';
		}
		
		// -------------------------------------------------------
		// HIGHSLIDE JS IMAGE ONLY
		// -------------------------------------------------------

		else if ($detail_window == 5) {

			$button->set('methodname', 'highslide');
			$button2->methodname 		= &$button->methodname;
			$buttonOther->methodname 	= &$button->methodname;
			
			$document->addScript(JURI::base(true).'/components/com_phocagallery/assets/js/highslide/highslide-full.js');
		$document->addStyleSheet(JURI::base(true).'/components/com_phocagallery/assets/js/highslide/highslide.css');
			
			if ( $libraries['pg-group-highslide']->value == 0 ) {
				$document->addCustomTag( PhocaGalleryRenderFront::renderHighslideJSAll());
				$document->addCustomTag('<!--[if lt IE 7]><link rel="stylesheet" type="text/css" href="'.JURI::base(true).'/components/com_phocagallery/assets/js/highslide/highslide-ie6.css" /><![endif]-->');
				$library->setLibrary('pg-group-highslide', 1);
			}
			
			$document->addCustomTag( PhocaGalleryRenderFront::renderHighslideJS('pl', $popup_window_width, $popup_window_height, $highslide_slideshow, $highslide_class, $highslide_outline_type, $highslide_opacity, $highslide_close_button));
			$tmpl['highslideonclick2']	= 'return hs.htmlExpand(this, phocaZoomPl )';
			$tmpl['highslideonclick']	= 'return hs.expand(this, phocaImageRI )';
			$tmpl['highslideonclick']	= PhocaGalleryRenderFront::renderHighslideJSImage('pl', $highslide_class, $highslide_outline_type, $highslide_opacity, $highslide_fullimg);

		}

		$folderButton = new JObject();
		$folderButton->set('name', 'image');
		$folderButton->set('options', "");		
			
			
			
			// End open window parameters
			
			// ===============================
			// OUTPUT
			// ===============================
			$output	='';
			$output .= '<div class="phocagallery">' . "\n";
			
			
			//--------------------------
			// DISPLAYING OF CATEGORIES (link doesn't work if there is no menu link)
			//--------------------------
			
			
			$hideCat		= trim( $hide_categories );
			$hideCatArray	= explode( ';', $hide_categories );
			$hideCatSql		= '';
			if (is_array($hideCatArray)) {
				foreach ($hideCatArray as $value) {
					$hideCatSql .= ' AND cc.id != '. (int) trim($value) .' ';
				}
			}
			// by vogo
			$uniqueCatSql	= '';
			if ($catid > 0) {
				$uniqueCatSql	= ' AND cc.id = '. $catid .'';	
			}
			
			if ($view == 'categories') {
				//CATEGORIES
				$queryc = 'SELECT cc.*, a.catid, COUNT(a.id) AS numlinks,'
				. ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(\':\', cc.id, cc.alias) ELSE cc.id END as slug'
				. ' FROM #__phocagallery_categories AS cc'
				. ' LEFT JOIN #__phocagallery AS a ON a.catid = cc.id'
				. ' WHERE a.published = 1'
				. ' AND cc.published = 1'
				. $hideCatSql
				. $uniqueCatSql
				. ' GROUP BY cc.id'
				. ' ORDER BY cc.ordering';

				//SUBCATEGORIES
				$querysc = 'SELECT cc.title AS text, cc.id AS value, cc.parent_id as parentid'
				. ' FROM #__phocagallery_categories AS cc'
				. ' WHERE cc.published = 1'
				. ' ORDER BY cc.ordering';

				$data_outcome 		= '';
				$data_outcome_array = '';
			
				$db->setQuery($queryc);
				$outcome_data = $db->loadObjectList();
			
				$db->setQuery($querysc);
				$outcome_subcategories = $db->loadObjectList();
			
				$tree = array();
				$text = '';
				$tree = PhocaGalleryRenderAdmin::CategoryTreeOption($outcome_subcategories, $tree, 0, $text, -1);
				
				foreach ($tree as $key => $value) {
					foreach ($outcome_data as $key2 => $value2) {
						if ($value->value == $value2->id) {
							
							$data_outcome 					= new JObject();
							$data_outcome->id				= $value2->id;
							$data_outcome->parent_id		= $value2->parent_id;
							$data_outcome->title			= $value->text;
							$data_outcome->name				= $value2->name;
							$data_outcome->alias			= $value2->alias;
							$data_outcome->image			= $value2->image;
							$data_outcome->section			= $value2->section;
							$data_outcome->image_position	= $value2->image_position;
							$data_outcome->description		= $value2->description;
							$data_outcome->published		= $value2->published;
							$data_outcome->editor			= $value2->editor;
							$data_outcome->ordering			= $value2->ordering;
							$data_outcome->access			= $value2->access;
							$data_outcome->accessuserid		= $value2->accessuserid;
							$data_outcome->uploaduserid		= $value2->uploaduserid;
							$data_outcome->deleteuserid		= $value2->deleteuserid;
							$data_outcome->count			= $value2->count;
							$data_outcome->params			= $value2->params;
							$data_outcome->catid			= $value2->catid;
							$data_outcome->numlinks			= $value2->numlinks;
							$data_outcome->slug				= $value2->slug;
							$data_outcome->link				= "";
							$data_outcome->filename			= "";
							$data_outcome->linkthumbnailpath= "";
							
							//FILENAME
							$queryfn = 'SELECT filename AS filename FROM #__phocagallery WHERE catid='.$value2->id.' AND published=1 ORDER BY ordering LIMIT 1';
							$db->setQuery($queryfn);
							$outcome_filename	    = $db->loadObjectList();
							$data_outcome->filename	= $outcome_filename[0]->filename;
							$data_outcome_array[] 	= $data_outcome;
						}	
					}
				}
			
				if ($img_cat == 1) {
					$medium_image_height	= $medium_image_height + 18;
					$medium_image_width 	= $medium_image_width + 18;
					$small_image_width		= $small_image_width +18;
					$small_image_height		= $small_image_height +18;
						
					$output .= '<table border="0">';
					foreach ($data_outcome_array as $category) {
						// -------------------------------------------------------------- SEF PROBLEM
						// Is there a Itemid for category
						$items	 = $menu->getItems('link', 'index.php?option=com_phocagallery&view=category&id='. $category->id);
						$itemscat= $menu->getItems('link', 'index.php?option=com_phocagallery&view=categories');
						
						if(isset($itemscat[0]))
						{
							$itemid = $itemscat[0]->id;
							$category->link = JRoute::_('index.php?option=com_phocagallery&view=category&id='. $category->slug.'&Itemid='.$itemid );
						}
						else if(isset($items[0]))
						{
							$itemid = $items[0]->id;
							$category->link = JRoute::_('index.php?option=com_phocagallery&view=category&id='. $category->slug.'&Itemid='.$itemid );
						}
						else
						{							
							$itemid = 0;
							//$category->link = JRoute::_('index.php?option=com_phocagallery&view=category&id='. $category->slug.'&Itemid='.$itemid );
							$category->link = JRoute::_('index.php?option=com_phocagallery&view=category&id='. $category->slug );
							
						}
						// ---------------------------------------------------------------------------------

						$imgCatSizeHelper = 'small';
						
						$mediumCSS 	= 'background: url(\''.JURI::base(true).'/components/com_phocagallery/assets/images/shadow1.'.$tmpl['formaticon'].'\') 50% 50% no-repeat;height:'.$medium_image_height	.'px;width:'.$medium_image_width.'px;';
						$smallCSS	= 'background: url(\''.JURI::base(true).'/components/com_phocagallery/assets/images/shadow3.'.$tmpl['formaticon'].'\') 50% 50% no-repeat;height:'.$small_image_height	.'px;width:'.$small_image_width.'px;';
						
						switch ($img_cat_size) {	
							case 'mediumfoldershadow':			
								$imageBg = $mediumCSS;
								$imgCatSizeHelper = 7;
							break;
							
							case 'smallfoldershadow':
								$imageBg = $smallCSS;
								$imgCatSizeHelper = 6;
							break;
							
							case 'mediumshadow':		
								$imageBg = $mediumCSS;
								$imgCatSizeHelper = 5;
							break;
							
							case 'smallshadow':
								$imageBg = $smallCSS;
								$imgCatSizeHelper = 4;
							break;
							
							case 'mediumfolder':
								$imageBg = '';
								$imgCatSizeHelper = 3;
							break;
							
							case 'smallfolder':
							default:
								$imageBg = '';
								$imgCatSizeHelper = 2;
							break;
							
							case 'medium':
								$imageBg = '';
								$imgCatSizeHelper = 1;
							break;
							
							case 'small':
							default:
								$imageBg = '';
								$imgCatSizeHelper = 0;
							break;
						}
						
						// Display Key Icon (in case we want to display unaccessable categories in list view)
						$rightDisplayKey  = 1;
						
						// we simulate that we want not to display unaccessable categories
						// so we get rightDisplayKey = 0 then the key will be displayed
						if (isset($category)) {
							$rightDisplayKey = PhocaGalleryAccess::getUserRight ('accessuserid', $category->accessuserid ,$category->access, $user->get('aid', 0), $user->get('id', 0), 0);
						}
						
						$file_thumbnail = PhocaGalleryImageFront::displayCategoriesImageOrFolder($category->filename, $imgCatSizeHelper, $rightDisplayKey);
						
						$category->linkthumbnailpath = $file_thumbnail->rel;
						
						//Output
						$output .= '<tr>'
							.'<td align="center" valign="middle" style="'.$imageBg.'"><a href="'.$category->link.'">'
							.'<img src="'.$category->linkthumbnailpath.'" alt="'.$category->title.'" style="border:0" />'
							.'</a></td>'
							.'<td><a href="'.$category->link.'" class="category'.$params->get( 'pageclass_sfx' ).'">'.$category->title.'</a>&nbsp;'
							.'<span class="small">('.$category->numlinks.')</span></td>'
							.'</tr>';
					}
					$output .= '</table>';
				
				} else {
					$output .= '<ul>';
					
					foreach ($data_outcome_array as $category) {
						// -------------------------------------------------------------- SEF PROBLEM
						// Is there a Itemid for category
						$items	 = $menu->getItems('link', 'index.php?option=com_phocagallery&view=category&id='. $category->id);
						$itemscat= $menu->getItems('link', 'index.php?option=com_phocagallery&view=categories');
						
						if(isset($itemscat[0]))
						{
							$itemid = $itemscat[0]->id;
							$category->link = JRoute::_('index.php?option=com_phocagallery&view=category&id='. $category->slug.'&Itemid='.$itemid );
						}
						else if(isset($items[0]))
						{
							$itemid = $items[0]->id;
							$category->link = JRoute::_('index.php?option=com_phocagallery&view=category&id='. $category->slug.'&Itemid='.$itemid );
						}
						else
						{
							$itemid = 0;
							$category->link = JRoute::_('index.php?option=com_phocagallery&view=category&id='. $category->slug );
						}
						// ---------------------------------------------------------------------------------
					
					
						$output .='<li>'
								 .'<a href="'.$category->link.'" class="category'.$params->get( 'pageclass_sfx' ).'">'
								 . $category->title.'</a>&nbsp;<span class="small">('.$category->numlinks.')</span>'
								 .'</li>';
					}
					$output .= '</ul>';
				}
			}
				
			
			
			//-----------------------
			// DISPLAYING OF IMAGES
			//-----------------------
			if ($view == 'category') {
				
				$where = '';
				
				// Only one image
				if ($imageid > 0) {
					$where = ' AND id = '. $imageid;
				}
				
				// Random image
				if ($imagerandom == 1 && $catid > 0) {
					
					$query = 'SELECT id' .
					' FROM #__phocagallery' .
					' WHERE catid = '.(int) $catid .
					' AND published = 1' .
					' ORDER BY RAND()';
			
					$db->setQuery($query);
					$idQuery =& $db->loadObject();
					if (!empty($idQuery)) {
						$where = ' AND id = '. $idQuery->id;
					}
				}
				
				$limit = '';
				
				// Count of images (LIMIT 0, 20)
				if ($limitcount > 0) {
					$limit = ' LIMIT '.$limitstart.', '.$limitcount;
				}
				
				$query = 'SELECT *' .
				' FROM #__phocagallery' .
				' WHERE catid = '.(int) $catid .
				' AND published = 1' . $where .
				' ORDER BY ordering' . $limit;
			
				$db->setQuery($query);
				$category =& $db->loadObjectList();
				
				// current category info
				$query = 'SELECT c.*,' .
					' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug '.
					' FROM #__phocagallery_categories AS c' .
					' WHERE c.id = '. (int) $catid;
				//	' AND c.section = "com_phocagallery"';
	
				$db->setQuery($query, 0, 1);
				$category_info = $db->loadObject();
				
				// Output
				foreach ($category as $image) {
					
					
					// PicLens CATEGORY - loaded every time new category will be displayed on the site---------
					if ((int)$enable_piclens > 0) {
						$libName = 'pg-piclens-'.$image->catid;
						$libraries[$libName]	= $library->getLibrary($libName);
						if ($libraries[$libName]->value == 0) {			
							$document->addCustomTag("<link id=\"phocagallerypiclens\" rel=\"alternate\" href=\""
							.JURI::base(true)."/images/phocagallery/"
							.$image->catid.".rss\" type=\"application/rss+xml\" title=\"\" />");
							$library->setLibrary($libName, 1);
						}
						
						// PicLens CSS - will be loaded only one time per site
						$libraries[$libName]	= $library->getLibrary('pg-pl-piclens');
						if ($libraries['pg-pl-piclens']->value == 0) {
							
							$document->addScript('http://lite.piclens.com/current/piclens.js');
							$document->addCustomTag("<style type=\"text/css\">\n"
							." .mbf-item { display: none; }\n"
							." #phocagallery .mbf-item { display: none; }\n"
							." </style>\n");
							$library->setLibrary('pg-pl-piclens', 1);
						}
					}
					// END PICLENS -----------------------------------------------------------------------------
					
					$image->slug 	= $image->id.'-'.$image->alias;
					// Get file thumbnail or No Image
					$image->linkthumbnailpath	= PhocaGalleryImageFront::displayImageOrNoImage($image->filename, 'medium');
					$file_thumbnail 			= PhocaGalleryFileThumbnail::getThumbnailName($image->filename, 'medium');
					$image->linkthumbnailpathabs= $file_thumbnail->abs;
					
					
					// -------------------------------------------------------------- SEF PROBLEM
					// Is there a Itemid for category
					$items	 = $menu->getItems('link', 'index.php?option=com_phocagallery&view=category&id='.$category_info->id);
					$itemscat= $menu->getItems('link', 'index.php?option=com_phocagallery&view=categories');
					
					if(isset($itemscat[0]))
					{
						$itemid = $itemscat[0]->id;
						$siteLink = JRoute::_('index.php?option=com_phocagallery&view=detail&catid='. $category_info->slug .'&id='. $image->slug .'&Itemid='.$itemid . '&tmpl=component&detail='.$detail_window.'&buttons='.$detail_buttons );
					}
					else if(isset($items[0]))
					{
						$itemid = $items[0]->id;
						$siteLink = JRoute::_('index.php?option=com_phocagallery&view=detail&catid='. $category_info->slug .'&id='. $image->slug .'&Itemid='.$itemid . '&tmpl=component&detail='.$detail_window.'&buttons='.$detail_buttons );
					}
					else
					{
						$itemid = 0;
						$siteLink = JRoute::_('index.php?option=com_phocagallery&view=detail&catid='. $category_info->slug .'&id='. $image->slug . '&tmpl=component&detail='.$detail_window.'&buttons='.$detail_buttons );
					}
					// ---------------------------------------------------------------------------------
					
					
					// Different links for different actions: image, zoom icon, download icon
					$thumbLink	= PhocaGalleryFileThumbnail::getThumbnailName($image->filename, 'large');
					$imgLinkOrig= JURI::base(true) . '/' .PhocaGalleryFile::getFileOriginal($image->filename, 1);
					$imgLink	= $thumbLink->rel;
					
					if ($detail_window == 2 ) {
						$image->link 		= $imgLink;
						$image->link2		= $imgLink;
						$image->linkother	= $siteLink;
						$image->linkorig	= $imgLinkOrig;
					
					} else if ( $detail_window == 3 ) {
					
						$image->link 		= $imgLink;
						$image->link2 	= $imgLink;
						$image->linkother	= $siteLink;
						$image->linkorig	= $imgLinkOrig;
					
					} else if ( $detail_window == 5 ) {
						
						$image->link 		= $imgLink;
						$image->link2 	= $siteLink;
						$image->linkother	= $siteLink;
						$image->linkorig	= $imgLinkOrig;
						
					} else {
					
						$image->link 		= $siteLink;
						$image->link2 	= $siteLink;
						$image->linkother	= $siteLink;
						$image->linkorig	= $imgLinkOrig;
						
					}
					
					
					
					// Float
					$float_code	= '';
					if ($float != '') {
						$float_code = 'position:relative;float:'.$float.';';
					}

					// Maximum size of module image is 100 x 100
					jimport( 'joomla.filesystem.file' );
					$imageWidth['size']		= 100;
					$imageHeight['size']	= 100;
					$imageHeight['boxsize'] = 100;
					$imageWidth['boxsize'] 	= 120;
					$imageOrigHeight		= 100;
					$imageOrigWidth			= 100;
					
					if (JFile::exists($image->linkthumbnailpathabs)) {
						list($width, $height) = GetImageSize( $image->linkthumbnailpathabs );
						
						$imageHeight 	= PhocaGalleryImage::correctSize($height, 100, 100, 0);
						$imageWidth 	= PhocaGalleryImage::correctSize($width, 100, 120, 20);
						$imageOrigHeight		= $height;
						$imageOrigWidth			= $width;
					}
					$imageHeight['boxsize']	= PhocaGalleryImage::setBoxSize($imageHeight,$imageWidth, $display_name, $display_icon_detail, $display_icon_download, 0, 0, 0, 0, 0, 0, 0, 0,  $category_box_space, $image_background_shadow, 0);
					if ( $image_background_shadow != 'none' ) {		
						$imageHeight['size']	= $imageHeight['size'] + 18;
						$imageWidth['size'] 	= $imageWidth['size'] + 18;
						$image_background_color	= 'url(\''.JURI::base(true).'/components/com_phocagallery/assets/images/'.$image_background_shadow.'.'.$tmpl['formaticon'].'\') 0 0 no-repeat;';
					}
					

					$output .= '<div class="phocagallery-box-file pgplugin'.$iCss.'" style="height:'. $imageHeight['boxsize'] .'px; width:'. $imageWidth['boxsize'].'px;'.$float_code.'">' . "\n"
						.'<center>'  . "\n"
						.'<div class="phocagallery-box-file-first" style="background: '.$image_background_color.';height:'.$imageHeight['size'].'px;width:'.$imageWidth['size'].'px;">' . "\n"
						.'<div class="phocagallery-box-file-second">' . "\n"
						.'<div class="phocagallery-box-file-third">' . "\n"
						.'<center>' . "\n"
						.'<a class="'.$button->methodname.'" title="'.$image->title.'" href="'. JRoute::_($image->link).'"'; 
					
					
					// DETAIL WINDOW
					if ($detail_window == 1) {
						$output .= ' onclick="'. $button->options.'"';
					} else if ($detail_window == 4 || $detail_window == 5) {
						$highSlideOnClick	= str_replace('[phocahsfullimg]',$image->linkorig, $tmpl['highslideonclick']);
						$output .= ' onclick="'. $highSlideOnClick.'"';
					} else {
						$output .= ' rel="'.$button->options.'"';
					}
					
					// Enable the switch image
					if ($enable_switch == 1) {
						$output .=' onmouseover="PhocaGallerySwitchImage(\'PhocaGalleryobjectPicture\', \''. str_replace('phoca_thumb_m_','phoca_thumb_l_', JURI::root() . $image->linkthumbnailpath).'\');" onmouseout="PhocaGallerySwitchImage(\'PhocaGalleryobjectPicture\', \''. str_replace('phoca_thumb_m_','phoca_thumb_l_', JURI::root() . $image->linkthumbnailpath).'\');"';
						
					} else {
						// Overlib
						
						if (!empty($image->description)) {
							$divPadding = 'padding:5px;';
						} else {
							$divPadding = 'padding:0px;margin:0px;';
						}
						
						$document->addScript(JURI::base(true).'/includes/js/overlib_mini.js');
						$opacityPer = $opacityPer = (float)$tmpl['overliboverlayopacity'] * 100;
						
						if ( $libraries['pg-overlib-group']->value == 0 ) {
					
							$document->addCustomTag( "<style type=\"text/css\">\n"
				
							. ".bgPhocaClass{
								background:".$tmpl['olbgcolor'].";
								filter:alpha(opacity=".$opacityPer.");
								opacity: ".$tmpl['overliboverlayopacity'].";
								-moz-opacity:".$tmpl['overliboverlayopacity'].";
								z-index:1000;
								}
								.fgPhocaClass{
								background:".$tmpl['olfgcolor'].";
								filter:alpha(opacity=100);
								opacity: 1;
								-moz-opacity:1;
								z-index:1000;
								}
								.fontPhocaClass{
								color:".$tmpl['oltfcolor'].";
								z-index:1001;
								}
								.capfontPhocaClass, .capfontclosePhocaClass{
								color:".$tmpl['olcfcolor'].";
								font-weight:bold;
								z-index:1001;
								}"
							." </style>\n");
							
							
							$library->setLibrary('pg-overlib-group', 1);
						}
						
						
						
						if ($enable_overlib == 1) { 
							$output .=  " onmouseover=\"return overlib('".htmlspecialchars( addslashes('<center>' . JHTML::_( 'image.site', str_replace ('phoca_thumb_m_','phoca_thumb_l_',$image->linkthumbnailpath), '', '', '', $image->title ) . "</center>"))."', CAPTION, '". $image->title."', BELOW, RIGHT, BGCLASS,'bgPhocaClass', FGCOLOR, '".$tmpl['olfgcolor']."', BGCOLOR, '".$tmpl['olbgcolor']."', TEXTCOLOR, '".$tmpl['oltfcolor']."', CAPCOLOR, '".$tmpl['olcfcolor']."');\""
							. " onmouseout=\"return nd();\" ";
						} else if ($enable_overlib == 2){ 
							$image->description		= str_replace("\n", '<br />', $image->description);
							$output .=  " onmouseover=\"return overlib('".htmlspecialchars( addslashes('<div style="'.$divPadding.'">'.$image->description.'</div>'))."', CAPTION, '". $image->title."', BELOW, RIGHT, CSSCLASS, TEXTFONTCLASS, 'fontPhocaClass', FGCLASS, 'fgPhocaClass', BGCLASS, 'bgPhocaClass', CAPTIONFONTCLASS,'capfontPhocaClass', CLOSEFONTCLASS, 'capfontclosePhocaClass');\""
							. " onmouseout=\"return nd();\" ";				
						} else if ($enable_overlib == 3){ 
							$image->description		= str_replace("\n", '<br />', $image->description);
							$output .=  " onmouseover=\"return overlib('".PhocaGalleryText::strTrimAll(htmlspecialchars( addslashes( '<div style="text-align:center"><center>' . JHTML::_( 'image.site', str_replace ('phoca_thumb_m_','phoca_thumb_l_', $image->linkthumbnailpath), '', '', '', $image->title ) . '</center></div><div style="'.$divPadding.'">' . $image->description . '</div>')))."', CAPTION, '". $image->title."', BELOW, RIGHT, BGCLASS,'bgPhocaClass', FGCLASS,'fgPhocaClass', FGCOLOR, '".$tmpl['olfgcolor']."', BGCOLOR, '".$tmpl['olbgcolor']."', TEXTCOLOR, '".$tmpl['oltfcolor']."', CAPCOLOR, '".$tmpl['olcfcolor']."');\""
							. " onmouseout=\"return nd();\" ";				
						} 
					}
					// End Overlib
					
					$output .= ' >' . "\n";
					$output .= '<img src="'.$image->linkthumbnailpath.'" alt="'.$image->title.'" />';
					
					if ((int)$enable_piclens > 0) {
						$output .= '<span class="mbf-item">#phocagallerypiclens '.$image->catid .'-phocagallerypiclenscode-'. $image->filename.'</span>';
					}
					
					$output .='</a>'
						.'</center>' . "\n"
						.'</div>' . "\n"
						.'</div>' . "\n"
						.'</div>' . "\n"
						.'</center>' . "\n";

					if ($display_name == 1) {
						$output .= '<div class="name" style="color: '.$font_color.' ;font-size:'.$namefontsize.'px;margin-top:5px;text-align:center;">'.PhocaGalleryText::wordDelete($image->title, $namenumchar, '...').'</div>';
					}
		
					if ($display_icon_detail == 1 || $display_icon_download == 1 || $enable_piclens == 2) {
						
						$output .= '<div class="detail" style="text-align:right">';
						
						if ($enable_piclens == 2) {							
							$output .=' <a href="javascript:PicLensLite.start();" title="PicLens" ><img src="http://lite.piclens.com/images/PicLensButton.png" alt="PicLens" width="16" height="12" border="0" style="margin-bottom:2px" /></a>';
		  
						}
						
						
						if ($display_icon_detail == 1) {
							$output .= ' <a class="'.$button->methodname.'" title="'. JText::_('Image Detail').'" href="'.JRoute::_($image->link2).'"';
							// Detail Window
							if ($detail_window == 1) {
								$output .= ' onclick="'. $button->options.'"';
							} else if ($detail_window == 2) {
								$output .= ' rel="'. $button->options.'"';
							} else if ($detail_window == 4 ) {
								$output .= ' onclick="'. $tmpl['highslideonclick'].'"';
							} else if ($detail_window == 5 ) {
								$output .= ' onclick="'. $tmpl['highslideonclick2'].'"';
							} else {
								$output .= ' rel="'. $button2->options.'"';
							}
							
							
							$output .= ' >';
							$output .= '<img src="components/com_phocagallery/assets/images/icon-view.'.$tmpl['formaticon'].'" alt="'.$image->title.'" />';
							$output .= '</a>';
						}
						
						if ($display_icon_download == 1) {
							$output .= ' <a class="'. $button->methodname.'" title="'. JText::_('Image Download').'" href="'. JRoute::_($image->linkother . '&amp;phocadownload=1').'"';
							// Detail Window
							if ($detail_window == 1) {
								$output .= ' onclick="'. $buttonOther->options.'"';
							} else if ($detail_window == 4 ) {
								$output .= ' onclick="'. $tmpl['highslideonclick'].'"';
							} else if ($detail_window == 5 ) {
								$output .= ' onclick="'. $tmpl['highslideonclick2'].'"';
							} else {
								$output .= ' rel="'. $buttonOther->options.'"';
							}
				
							$output .= ' >';
							$output .= '<img src="components/com_phocagallery/assets/images/icon-download.'.$tmpl['formaticon'].'" alt="'.$image->title.'" />';
							$output .= '</a>';
						
						}
						
						$output .= '</div>';
						if ($float == '') {
							$output .= '<div style="clear:both"> </div>';
						}
					}
					$output .= '</div>';
				}
			}
			
			//--------------------------
			// DISPLAYING OF SWITCHIMAGE
			//--------------------------
			if ($view == 'switchimage') {
			
				$path	= PhocaGalleryPath::getPath();
				$waitImage 		= $path->image_rel . 'icon-switch.gif';
				$basicImage		= $path->image_rel  . 'phoca_thumb_l_no_image.' . $tmpl['formaticon'];
				
				if ($basic_image_id > 0) {
				
					$query = 'SELECT *' .
					' FROM #__phocagallery' .
					' WHERE id = '.(int) $basic_image_id;
			
					$db->setQuery($query);
					$basicImageArray =& $db->loadObject();
					if (isset($basicImageArray->filename)) { 
						$fileBasicThumb = PhocaGalleryFileThumbnail::getThumbnailName($basicImageArray->filename, 'large');
						$basicImage  	= $fileBasicThumb->rel;
					} else {
						$basicImage  = '';
					}
				}
				
				$switchHeight 	= $switch_height;//$this->switchheight;
				$switchCenterH	= ($switchHeight / 2) - 18;
				$switchWidth 	= $switch_width;//$this->switchwidth;
				$switchCenterW	= ($switchWidth / 2) - 18;
				
				$document->addCustomTag(PhocaGalleryRenderFront::switchImage($waitImage));
				$switchHeight	= $switchHeight + 5;
			
				$output .='<div><center class="main-switch-image" style="margin:0px;padding:7px 5px 7px 5px;margin-bottom:15px;"><table border="0" cellspacing="5" cellpadding="5" style="border:1px solid #c2c2c2;"><tr><td align="center" valign="middle" style="text-align:center;width:'. $switchWidth .'px;height:'. $switchHeight .'px; background: url(\''. JURI::root().'components/com_phocagallery/assets/images/icon-switch.gif\') '.$switchCenterW.'px '.$switchCenterH.'px no-repeat;margin:0px;padding:0px;">
'.JHTML::_( 'image.site', $basicImage , '', '', '', '', ' id="PhocaGalleryobjectPicture"  border="0"' ).'
</td></tr></table></center></div>';
			
				
			} else {
				// Overlib
				
			
			}
			
			//--------------------------
			// DISPLAYING OF Clear Both
			//--------------------------
			if ($view == 'clearboth') {
				$output .= '<div style="clear:both"> </div>';
			}
			if ($view == 'clearright') {
				$output .= '<div style="clear:right"> </div>';
			}
			if ($view == 'clearleft') {
				$output .= '<div style="clear:left"> </div>';
			}
			
			$output .= '</div>';
			if ($float == '') {
				$output .= '<div style="clear:both"> </div>';
			}
			
			$article->text = preg_replace($regex_all, $output, $article->text, 1);
			
		}
		
		// CUSTOM CSS - For all items it will be the same
		if ( $libraries['pg-css-sbox-plugin']->value == 0 ) {
			$document->addCustomTag( "<style type=\"text/css\">\n" . $cssSbox . "\n" . " </style>\n");
			$library->setLibrary('pg-css-sbox-plugin', 1);
		}
		// All custom CSS tags will be added into one CSS area
		if ( $libraries['pg-css-pg-plugin']->value == 0 ) {
			$document->addCustomTag( "<style type=\"text/css\">\n" . $cssPgPlugin . "\n" . " </style>\n");
			$library->setLibrary('pg-css-pg-plugin', 1);
		}
		
		if ( $libraries['pg-css-ie']->value == 0 ) {
			$document->addCustomTag("<!--[if lt IE 8]>\n<link rel=\"stylesheet\" href=\"".JURI::base(true)."/components/com_phocagallery/assets/phocagalleryieall.css\" type=\"text/css\" />\n<![endif]-->");
			$library->setLibrary('pg-css-ie', 1);
		}
		
	  } // end if count_matches
		return true;
	}
}
?>