<?php
/**
 * @version	1.2
 * @author	Darkick	<darkick@darkick.ru>
 * @license	http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;


jimport('joomla.utilities.date');


$HTML = '';
if (is_null($topics_list)) {
	$HTML .= JText::_('DPLT Database error');
}
else if ($topics_list)
{
	// Define topic output format
	if (!($topic_output_format = JString::trim($params->get('topic_output_format')))) {
		$topic_output_format = JText::_('DPLT TOPIC_OUTPUT_FORMAT');
	}
	$TOPIC_OUTPUT_FORMAT = JString::str_ireplace(
		array('$forum_name', '$title_subject', '$title', '$subject', '$username', '$date', '$posts_count', '$replies_count'),
		array('%1$s', '%2$s', '%3$s', '%4$s', '%5$s', '%6$s', '%7$s', '%8$s'),
		$topic_output_format
	);
	// Define basic settings
	$forum_url				= trim($params->get('forum_url'));
	$viewforum_format		= trim($params->get('viewforum_format'));
	$viewtopic_format		= trim($params->get('viewtopic_format'));
	$memberlist_format		= trim($params->get('memberlist_format'));
	$forum_target			= htmlspecialchars(trim($params->get('forum_target')));
	$profilelink_target		= htmlspecialchars(trim($params->get('profilelink_target')));
	$max_titles_length		= abs((int)$params->get('max_titles_length'));

	if (!($date_format = trim($params->get('date_format')))) {
		$date_format = JText::_('DATE_FORMAT_LC2');
	}
	// time offset
	$server_timezone = (int)date('Z') / 3600;
	$timezone = null;
	if ($params->get('timezone', 'user'))
	{
		$user = &JFactory::getUser();
		$timezone = $user->getParam('timezone');
	}
	if (is_null($timezone))
	{
		$config = &JFactory::getConfig();
		$timezone = $config->getValue('config.offset');
	}
	$time_offset = $timezone - $server_timezone;

	// Columns and Rows count
	$total_topics_count = 0;
	foreach ($topics_list as $value) {
		$total_topics_count += count($value);
	}
	$columns_rows		= $params->get('columns_rows', 'columns');
	$columns_rows_count	= abs((int)$params->get('columns_rows_count', 1));
	if (!$columns_rows_count)
	{
		$columns_rows = 'columns';
		$columns_rows_count = 1;
	}
	if ($columns_rows == 'columns')
	{
		$columns_count = $columns_rows_count;
		$rows_count = (int)ceil($total_topics_count / $columns_count);
	}
	else
	{
		$rows_count = $columns_rows_count;
		$columns_count = (int)ceil($total_topics_count / $rows_count);
	}

	if ($params->get('ul_columns')) {
		$HTML .= '<ul class="bb-lasttopics">'."\n";
	}

	$row_number = 0;
	$column_number = 0;
	foreach ($topics_list as $f_id => $f_group)
	{
		$forum_group = JArrayHelper::getValue($forums_list, $f_id, null);
		foreach ($f_group as $topic)
		{
			$row_number++;
			// close previous column
			if ($row_number > $rows_count)
			{
				$HTML .= '</ul>';
				if ($params->get('ul_columns')) {
					$HTML .= '</li>';
				}
				$HTML .= "\n";
				$row_number = 1;
			}
			// open new column
			if ($row_number == 1)
			{
				$column_number++;
				if ($params->get('ul_columns')) {
					$HTML .= '<li class="bb-lasttopics-col'.$column_number.'">';
				}
				if (($params->get('embed_style') == 'vertical') && ($columns_count > 1))
				{
					$style_ul = ' style="float: left; width: '.(floor(100 / $columns_count) + (float)$params->get('width_correction')).'%"';
					$style_li = '';
				}
				else if ($params->get('embed_style') == 'horizontal')
				{
					$style_ul = '';
					$style_li = ' style="float: left; width: '.(floor(100 / $rows_count) + (float)$params->get('width_correction')).'%"';
				}
				else
				{
					$style_ul = '';
					$style_li = '';
				}
				$HTML .= '<ul class="bb-lasttopics-col bb-lasttopics-col-'.$column_number.'"'.$style_ul.'>'."\n";
			}
			// Forums groups titles
			if ($forum_group)
			{
				if ($forum_group['forum_name'])
				{
					$forum_group['forum_name'] = modDphpBB3LastTopicsHelper::truncTitle($forum_group['forum_name'], $max_titles_length);
					if ($forum_group['forum_id'])
					{
						if ($viewforum_format)
						{
							$forum_name =	'<a href="'.htmlentities($forum_url.str_replace('$forum_id', $forum_group['forum_id'], $viewforum_format)).'"'.($forum_target ? ' target="'.$forum_target.'"' : '').'>'.
											$forum_group['forum_name'].
											'</a>';
						} else {
							$forum_name = $forum_group['forum_name'];
						}
					}
					else
					{
						if ($forum_url)
						{
							$forum_name =	'<a href="'.htmlentities($forum_url).'"'.($forum_target ? ' target="'.$forum_target.'"' : '').'>'.
											$forum_group['forum_name'].
											'</a>';
						} else {
							$forum_name = $forum_group['forum_name'];
						}
					}
					$HTML .= '<li class="bb-lasttopics-forum"'.$style_li.'>'.$forum_name.'</li>'."\n";
				}
				$forum_group = null;
			}

			// Prepare string items for topic output
			$topic['forum_name'] = modDphpBB3LastTopicsHelper::truncTitle($topic['forum_name'], $max_titles_length);
			$topic['topic_title'] = modDphpBB3LastTopicsHelper::truncTitle($topic['topic_title'], $max_titles_length);
			$topic['topic_last_post_subject'] = modDphpBB3LastTopicsHelper::truncTitle($topic['topic_last_post_subject'], $max_titles_length);
			// posts/replies count
			if ($params->get('show_posts_count', 'hide') == 'hide') {
				$posts_replies = '';
			}
			else
			{
				$posts_replies = '<span class="bb-lasttopics-replycount">&nbsp;(';
				if ($params->get('show_posts_count') == 'posts') {
					$posts_replies .= ($topic['topic_replies'] + 1);
				} else {
					$posts_replies .= $topic['topic_replies'];
				}
				$posts_replies .= ')</span>';
			}
			// forum name
			if ($viewforum_format)
			{
				$forum_name =	'<a href="'.htmlentities($forum_url.($topic['forum_id'] ? str_replace('$forum_id', $topic['forum_id'], $viewforum_format) : '')).'"'.($forum_target ? ' target="'.$forum_target.'"' : '').'>'.
								$topic['forum_name'].
								'</a>';
			} else {
				$forum_name	= $topic['forum_name'];
			}
			// topic title and post subject
			$title_subject = $topic['topic_last_post_subject'] ? $topic['topic_last_post_subject'] : $topic['topic_title'];
			if ($viewtopic_format)
			{
				$viewtopic_link = '<a href="'.htmlentities($forum_url.str_replace(array('$forum_id', '$topic_id', '$post_id'), array($topic['forum_id'], $topic['topic_id'], $topic['topic_last_post_id']), $viewtopic_format)).'"'.($forum_target ? ' target="'.$forum_target.'"' : '').'>';
				$title_subject = $viewtopic_link.$title_subject.'</a>'.$posts_replies;;
				$title = $viewtopic_link.$topic['topic_title'].'</a>'.$posts_replies;;
				$subject = $viewtopic_link.$topic['topic_last_post_subject'].'</a>'.$posts_replies;;
			}
			else
			{
				$title_subject .= $posts_replies;
				$title = $topic['topic_title'].$posts_replies;
				$subject = $topic['topic_last_post_subject'].$posts_replies;
			}
			// username
			if ($memberlist_format)
			{
				$username =	'<a href="'.htmlentities($forum_url.str_replace(array('$user_id', '$username'), array($topic['topic_last_poster_id'], $topic['topic_last_poster_name']), $memberlist_format)).'"'.($profilelink_target ? ' target="'.$profilelink_target.'"' : '').'>'.
							$topic['topic_last_poster_name'].
							'</a>';
			} else {
				$username = $topic['topic_last_poster_name'];
			}
			// date
			$date = &JFactory::getDate($topic['topic_last_post_time']);
			$date->setOffset((double)$time_offset);
			$date = $date->toFormat($date_format);

			$HTML .= '	<li class="bb-lasttopics-topic"'.$style_li.'>'.sprintf($TOPIC_OUTPUT_FORMAT, $forum_name, $title_subject, $title, $subject, $username, $date, $topic['topic_replies']+1, $topic['topic_replies']).'</li>'."\n";
		}
	}
	// close last column
	$HTML .= '</ul>';
	if ($params->get('ul_columns')) {
		$HTML .= '</li>';
	}
	$HTML .= "\n";
	// close wrap UL
	if ($params->get('ul_columns')) {
		$HTML .= '</ul>'."\n";
	}
	// Clear floats
	if ($params->get('clear_floats')) {
		$HTML .= '<div class="bb-lasttopics-clear" style="clear: left;"></div>'."\n";
	}
}

print($HTML);
