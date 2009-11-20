<?php
/**
 * @version	1.2
 * @author	Darkick	<darkick@darkick.ru>
 * @license	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;


class modDphpBB3LastTopicsHelper
{
	/**
	 *
	 *
	 * @param object		$params
	 * @return array|null	
	 */
	function getList($params)
	{
		$forums = array();
		$f_groups = explode("\n", $params->get('forums_groups', '0:10'));
		$all_forums_flag = false;
		foreach ($f_groups as $value)
		{
			@list($f_group, $count) = explode(':', $value, 2);
			$f_group = explode(',', $f_group);
			JArrayHelper::toInteger($f_group, 0);
			// allow only single group for ALL forums
			if (in_array(0, $f_group) && $all_forums_flag) {
				continue;
			} else if (in_array(0, $f_group))
			{
				$f_group = array(0);
				$all_forums_flag = true;
			}
			$forums[] = array(
				'f_group'	=> $f_group,
				'count'		=> ($count ? abs((int)$count) : 1)
			);
		}
		if (!$forums) {
			$forums[] = array('f_group' => array(0), 'count' => $count);
		}
		
		$exclude_forums = explode(',', $params->get('exclude_forums'));
		foreach ($exclude_forums as $key => $value)
		{
			if (!is_numeric($value)) {
				$value = -1;
			}
			$exclude_forums[$key] = (int)$value;
		}
		
		// Get forum database connection
		if ($params->def('forum_db', 'joomla') == 'joomla')
		{
			$config = &JFactory::getConfig();
			$options = array (
				'driver'	=> $config->getValue('config.dbtype'),
				'host'		=> $config->getValue('config.host'),
				'database'	=> $config->getValue('config.db'),
				'user'		=> $config->getValue('config.user'),
				'password'	=> $config->getValue('config.password'),
			);
		}
		else
		{
			$options = array (
				'driver'	=> $params->get('phpbb_driver'),
				'host'		=> $params->get('phpbb_host'),
				'database'	=> $params->get('phpbb_database'),
				'user'		=> $params->get('phpbb_user'),
				'password'	=> $params->get('phpbb_password'),
			);
		}
		$options['prefix'] = $params->get('phpbb_prefix');
		$forum_db = &JDatabase::getInstance($options);
		if (JError::isError($forum_db))
		{
			JError::handleLog($forum_db, null);
			return array(null, null);
		}
		
		// Forums
		$query = 'SELECT forum_id, forum_name FROM #__forums';
		$forum_db->setQuery($query);
		$forums_list = $forum_db->loadAssocList('forum_id');
		if ($forum_db->getErrorMsg())
		{
			JError::handleLog(JError::raiseWarning($forum_db->getErrorNum(), $forum_db->getErrorMsg()) , null);
			return array(null, null);
		}
		$forums_list[0] = array('forum_id' => 0, 'forum_name' => JText::_('DPLT Forum'));
		
		// Topics
		$topics_list = array();
		foreach ($forums as $f_group)
		{
			$query = 'SELECT forum_id, topic_id, topic_title, topic_last_post_id, topic_last_poster_id, topic_last_poster_name, topic_last_post_subject, topic_last_post_time, topic_replies'.
					'	FROM #__topics'.
					'	WHERE topic_approved = 1 ';
			if (!in_array(0, $f_group['f_group'])) {
				$query .= '		AND forum_id IN ('.implode(',', $f_group['f_group']).')'."\n";
			} else if ($exclude_forums) {
				$query .= '		AND forum_id NOT IN ('.implode(',', $exclude_forums).')'."\n";
			}
			$query .= '	ORDER BY topic_last_post_time DESC';
			$forum_db->setQuery($query, 0, $f_group['count']);
			$rows = $forum_db->loadAssocList();
			if ($forum_db->getErrorMsg())
			{
				JError::handleLog(JError::raiseWarning($forum_db->getErrorNum(), $forum_db->getErrorMsg()) , null);
				return array(null, null);
			}
			if ($rows) {
				foreach ($rows as $key => $row) {
					$rows[$key]['forum_name'] = $forums_list[$row['forum_id']]['forum_name'];
				}
				$topics_list[current($f_group['f_group'])] = $rows;
			}
		}
		
		return array($forums_list, $topics_list);
	}
	
	function truncTitle($title, $length = 0)
	{
		$title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
		$length = abs((int)$length);
		if ($length && (JString::strlen($title) > $length)) {
			$title = JString::substr($title, 0, $length - 1).html_entity_decode('&hellip;', ENT_COMPAT, 'UTF-8');
		}
		
		return htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8');
	}
}
