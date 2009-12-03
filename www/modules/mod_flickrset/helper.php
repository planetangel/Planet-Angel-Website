<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modFlickrSetHelper
{
	function getParams(&$params)
	{
		$params->def('setid', '');
		$params->def('userid', '');
		$params->def('apikey', '');
		$params->def('url', '');
		$params->def('width', '');
		return $params;
	}
}
