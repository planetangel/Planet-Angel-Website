<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$params = modFlickrSetHelper::getParams($params);

$setid	= $params->get( 'setid');
$userid	= $params->get( 'userid' );
$apikey	= $params->get( 'apikey' );
$class	= $params->get( 'moduleclass_sfx' );

require(JModuleHelper::getLayoutPath('mod_flickrset'));
