<?php
/**
 * @version	1.2
 * @author	Darkick	<darkick@darkick.ru>
 * @license	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;


// Include the syndicate functions only once
require_once(dirname(__FILE__).DS.'helper.php');

list($forums_list, $topics_list) = modDphpBB3LastTopicsHelper::getList($params);
require(JModuleHelper::getLayoutPath('mod_dphpbb3_last_topics'));
