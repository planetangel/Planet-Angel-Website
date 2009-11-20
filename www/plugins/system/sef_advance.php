<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');

class plgSystemSEF_Advance extends JPlugin
{
	function plgSystemSEF_Advance(&$subject, $config)  {
		parent::__construct($subject, $config);
		plgSystemSEF_Advance::trigger();
	}

	function trigger() {
		global $mainframe, $sef_config;

		if ($mainframe->isAdmin()) {
			return;
		}

		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sef'.DS.'sef_conf.php')) {
			// do not activate SEF for some cases
			if ( eregi( 'format=pdf', JRequest::getURI() ) ) {
				return;
			}
			require_once ( JPATH_SITE.DS.'components'.DS.'com_sef'.DS.'sef_conf.php' );
			$sef_config = new SEF_AdvanceConfig();

			if ($sef_config->mosConfig_sef && $sef_config->sef_enabled) {
				$router =& $mainframe->getRouter();
				require_once ( JPATH_SITE.DS.'components'.DS.'com_sef'.DS.'sef.php' );
				if (class_exists('SEF_AdvanceRouter')) {
					$router = new SEF_AdvanceRouter();
				}
			}
		} else {
			return;
		}
	}
}

?>