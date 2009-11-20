<?php
/*
* Code written by Chris Monahan, portions copyright phpBB.
*
* Please make sure that you do not upload this file to your
* site if you have received it from an untrusted source.
* This script is provided as-is without any warranty. By
* using this script you agree that MessageForums.net and
* its owners will not be held responsible for any issues
* that may arise from using this script. USE AT YOUR OWN RISK.
*/

// phpBB3 initialization
define('IN_PHPBB', true);
define("JOOM15_PHPBB3", true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// setup variables
$debug = false;
$addMyForumToDirectory = false;
$connectorVersion = '1.6';
$appVersion = '1.0';
$appName = 'TouchBB';
$appURL = "http://phobos.apple.com/WebObjects/MZStore.woa/wa/viewSoftware?id=317752610&mt=8";
$fmfURL = "http://support.messageforums.net";
$UA = str_replace('TouchBBLite', 'TouchBB', $_SERVER['HTTP_USER_AGENT']);
$delims = array(chr(13), chr(10), chr(9));

// some localization is available
$strings = array("Last post by %s, %s",
                 "%d post%s in %d topic%s",
                 "No activity",
                 "By %s, %s",
                 "No replies",
                 "%d repl%s from %d view%s",
                 "%d unread message%s",
                 "%d read message%s",
                 "%s", // From %s
                 "%d sent message%s",
                 "%s", // To %s
);

// make sure these variables are clean
$output = '';

// turn off all error reporting
if (!$debug) {
  error_reporting(0);
}

// start session management
$user->session_begin();
$auth->acl($user->data);

// requested variables
$get = request_var('get', '');
$id = request_var('id', 0);
$username = request_var('user', '');
$password = request_var('pass', '');
$search = request_var('search', '');

// shorten the date format a little
$config['default_dateformat'] = str_replace('F', 'M', $config['default_dateformat']);
if ($user->date_format) {
  $user->date_format = str_replace('F', 'M', $user->date_format);
}

if ($user->data['user_dateformat']) {
  $user->data['user_dateformat'] = str_replace('F', 'M', $user->data['user_dateformat']);
}

// figure out what version of the software they are using
if (substr_count($UA, $appName)) {
  if (@preg_match("/$appName\/(.*?)\s/", $UA, $match)) {
    $appVersion = $match[1];
  } elseif (@preg_match("/$appName(.*?)\s/", $UA, $match)) {
    $appVersion = $match[1];
  }
} else {
  if (!$debug) {
    displayStatus();
  }
}

if ($get == 'file') {
  $sid = request_var('sid', '');
  $uid = request_var('uid', '');

  // make sure user is authorized to view file
  if ($sid && $uid) {
    $sql = 'SELECT COUNT(*) AS count FROM ' . SESSIONS_TABLE . " WHERE session_id='" . $db->sql_escape($sid) . "' AND session_user_id=" . $db->sql_escape($uid);
    $result = $db->sql_query($sql);
    $count = (int) $db->sql_fetchfield('count');
    $db->sql_freeresult($result);

    if (!$count) {
      exit;
    }
  } else {
    exit;
  }

  $sql = 'SELECT attach_id, is_orphan, in_message, post_msg_id, extension, physical_filename, real_filename, mimetype, filetime FROM ' . ATTACHMENTS_TABLE . " WHERE attach_id = " . $db->sql_escape($id);
  $result = $db->sql_query_limit($sql, 1);
  $attachment = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  $filename = $phpbb_root_path . $config['upload_path'] . '/' . $attachment['physical_filename'];
  if (!@file_exists($filename)) {
    if ($debug) {
      trigger_error($user->lang['ERROR_NO_ATTACHMENT'] . '<br /><br />' . sprintf($user->lang['FILE_NOT_FOUND_404'], $filename));
    } else {
      exit;
    }
  }

  // now send the file contents to the browser
  $size = @filesize($filename);

  // check if headers already sent or not able to get the file contents
  if (headers_sent() || !@file_exists($filename) || !@is_readable($filename)) {
    if ($debug) {
      trigger_error('UNABLE_TO_DELIVER_FILE');
    } else {
      exit;
    }
  }

  // now the tricky part... let's dance
  header('Pragma: public');

  // send out the headers. do not set content-disposition to inline please, it is a security measure for users using the internet explorer
  $is_ie8 = (strpos(strtolower($user->browser), 'msie 8.0') !== false);
  header('Content-Type: ' . $attachment['mimetype']);

  if ($is_ie8) {
    header('X-Content-Type-Options: nosniff');
  }

  if (empty($user->browser) || (!$is_ie8 && (strpos(strtolower($user->browser), 'msie') !== false))) {
    header('Content-Disposition: attachment; ' . header_filename(htmlspecialchars_decode($attachment['real_filename'])));
    if (empty($user->browser) || (strpos(strtolower($user->browser), 'msie 6.0') !== false)) {
      header('expires: -1');
    }
  } else {
    header('Content-Disposition: ' . ((strpos($attachment['mimetype'], 'image') === 0) ? 'inline' : 'attachment') . '; ' . header_filename(htmlspecialchars_decode($attachment['real_filename'])));
    if ($is_ie8 && (strpos($attachment['mimetype'], 'image') !== 0)) {
      header('X-Download-Options: noopen');
    }
  }

  if ($size) {
    header("Content-Length: $size");
  }

  // clean up properly before sending the file
  garbage_collection();

  if (!set_modified_headers($attachment['filetime'], $user->browser)) {
    // Try to deliver in chunks
    @set_time_limit(0);

    $fp = @fopen($filename, 'rb');

    if ($fp !== false) {
      while (!feof($fp)) {
        echo fread($fp, 8192);
      }

      fclose($fp);
    } else {
      @readfile($filename);
    }

    flush();
  }
}

// ****************************
// ********* REPLY PM *********
// ****************************
if ($get == 'replypm') {
  // don't allow anonymous posting or spam bots will go crazy
  if ($user->data['is_registered']) {
    include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
    include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

    $message_parser = new parse_message();

    $message = utf8_normalize_nfc(request_var('txt', '', true));
    $message_parser->message = &$message;

    $title = utf8_normalize_nfc(request_var('title', '', true));

    $msg_id = utf8_normalize_nfc(request_var('rid', '', true));

    $to = $_REQUEST['to'];
    $sql = "SELECT user_id,username FROM " . USERS_TABLE;
    $result = $db->sql_query($sql);
    while ($row = $db->sql_fetchrow($result)) {
      for ($i=0;$i<count($to);$i++) {
        if ($row['username'] == $to[$i]) {
          $address_list['u'][$row['user_id']] = 'to';
        }
      }
    }
    $db->sql_freeresult($result);

    $enable_bbcode = true;
    $enable_smilies = true;
    $enable_urls = true;

    $message_parser->parse($enable_bbcode, ($config['allow_post_links']) ? $enable_urls : false, $enable_smilies, $img_status, $flash_status, true, $config['allow_post_links']);

    // data to pass
    $pm_data = array(
      'msg_id' => (int) $msg_id,
      'from_user_id' => $user->data['user_id'],
      'from_user_ip' => $user->ip,
      'from_username' => $user->data['username'],
      'reply_from_root_level' => (isset($post['root_level'])) ? (int) $post['root_level'] : 0,
      'reply_from_msg_id' => (int) $msg_id,
      'icon_id' => (int) $icon_id,
      'enable_sig' => (bool) $enable_sig,
      'enable_bbcode' => (bool) $enable_bbcode,
      'enable_smilies' => (bool) $enable_smilies,
      'enable_urls' => (bool) $enable_urls,
      'bbcode_bitfield' => $message_parser->bbcode_bitfield,
      'bbcode_uid' => $message_parser->bbcode_uid,
      'message' => $message_parser->message,
      'attachment_data' => $message_parser->attachment_data,
      'filename_data' => $message_parser->filename_data,
      'address_list' => $address_list
    );

    // send the private message
    $msg_id = submit_pm('post', $title, $pm_data);

    // send success
    $output = clean('1');
  } else {
    // send failure
    $output = clean('0');
  }
}

// ****************************
// ********* READ PM **********
// ****************************
if ($get == 'pm') {
  // configure style, language, etc.
  $user->setup('viewforum', $user->data['user_style']);

  // grab the private message data
  $sql = "SELECT username,message_text,bbcode_uid,message_time FROM " . PRIVMSGS_TABLE . "," . USERS_TABLE . " WHERE user_id=author_id AND msg_id=" . $db->sql_escape($id);
  $result = $db->sql_query($sql);
  $row = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  if ($output) {
    $output .= $delims[0];
  }

  $output .= clean($row['msg_id']) . $delims[1];
  $output .= clean($row['username']) . $delims[1];
  $output .= clean($row['message_text'], $row['bbcode_uid']) . $delims[1];
  $output .= clean($user->format_date($row['message_time'], false, false));
}

// ****************************
// ********* POST PM **********
// ****************************
if ($get == 'postpm') {
  // don't allow anonymous posting or spam bots will go crazy
  if ($user->data['is_registered']) {
    include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
    include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

    $message_parser = new parse_message();

    $message = utf8_normalize_nfc(request_var('txt', '', true));
    $message_parser->message = &$message;

    $title = utf8_normalize_nfc(request_var('title', '', true));

    $to = $_REQUEST['to'];
    $sql = "SELECT user_id,username FROM " . USERS_TABLE;
    $result = $db->sql_query($sql);
    while ($row = $db->sql_fetchrow($result)) {
      for ($i=0;$i<count($to);$i++) {
        if ($row['username'] == $to[$i]) {
          $address_list['u'][$row['user_id']] = 'to';
        }
      }
    }
    $db->sql_freeresult($result);

    $enable_bbcode = true;
    $enable_smilies = true;
    $enable_urls = true;

    $message_parser->parse($enable_bbcode, ($config['allow_post_links']) ? $enable_urls : false, $enable_smilies, $img_status, $flash_status, true, $config['allow_post_links']);

    // data to pass
    $pm_data = array(
      'msg_id' => (int) $msg_id,
      'from_user_id' => $user->data['user_id'],
      'from_user_ip' => $user->ip,
      'from_username' => $user->data['username'],
      'reply_from_root_level' => (isset($post['root_level'])) ? (int) $post['root_level'] : 0,
      'reply_from_msg_id' => (int) $msg_id,
      'icon_id' => (int) $icon_id,
      'enable_sig' => (bool) $enable_sig,
      'enable_bbcode' => (bool) $enable_bbcode,
      'enable_smilies' => (bool) $enable_smilies,
      'enable_urls' => (bool) $enable_urls,
      'bbcode_bitfield' => $message_parser->bbcode_bitfield,
      'bbcode_uid' => $message_parser->bbcode_uid,
      'message' => $message_parser->message,
      'attachment_data' => $message_parser->attachment_data,
      'filename_data' => $message_parser->filename_data,
      'address_list' => $address_list
    );

    // send the private message
    $msg_id = submit_pm('post', $title, $pm_data);

    // send success
    $output = clean('1');
  } else {
    // send failure
    $output = clean('0');
  }
}

// ***************************
// ********** LOGIN **********
// ***************************
if ($username && $password) {
  $autologin = true;
  $viewonline = 1;
  $admin = 0;

  // v1.1 added base64 encoding to passwords. Simple encoding is used and not encryption.
  // The problem with a publically available wrapper is that the key used for encryption
  // would be available to anyone who downloaded this script thus not secure. The moral of
  // the story is not to use the same passwords used for bank accounts and secure data as
  // on forums. If this statement really bothers you, consider this. Almost all forums are
  // operated on http:// (non secure) URLs, and your password is sent out via plain text
  // anyway. If anyone has a solution to this that would work on multiple server configs
  // out there, please let me know.
  $password = base64_decode($password);

  $result = $auth->login($username, $password, $autologin, $viewonline, $admin);
  if ($result['status'] == LOGIN_SUCCESS) {
    // do something later maybe?
    #login_joomla15($username, $password);
  }
}

// ****************************
// ********** LOGOUT **********
// ****************************
if ($get == 'logout') {
  $user->session_kill();
  $user->session_begin();

  if ($user->data['is_registered']) {
    $output = 0;
  } else {
    $output = 1;
  }
}

// ****************************
// ********* ACTIVE ***********
// ****************************
if ($get == 'active') {
  // configure style, language, etc.
  $user->setup('viewforum', $user->data['user_style']);

  // figure out what kind of reply counter to use
  $replyStr = ($auth->acl_get('m_approve', $id)) ? 'topic_replies_real' : 'topic_replies';

  // topic approved
  $sql_approved = ($auth->acl_get('m_approve', $id)) ? '' : ' AND t.topic_approved = 1';

  $sort_days = 7;
  $sql = "SELECT t.topic_id,t.topic_title,t.topic_last_post_time,t.topic_last_poster_name,username,topic_time,topic_views,$replyStr,forum_id FROM " . TOPICS_TABLE . " t LEFT JOIN " . USERS_TABLE . " ON user_id=topic_poster WHERE t.topic_moved_id = 0 AND t.topic_last_post_time > " . (time() - ($sort_days * 24 * 3600)) . $sql_approved . " ORDER BY t.topic_last_post_time DESC";

  $result = $db->sql_query($sql);
  while ($row = $db->sql_fetchrow($result)) {
    $row['type'] = 'Active Topics';
    $data[] = $row;
  }
  $db->sql_freeresult($result);

  for ($i=0;$i<count($data);$i++) {
    $row = $data[$i];

    // if the user does not have permissions to list this forum, skip everything until next branch
    if (!$auth->acl_get('f_list', $row['forum_id'])) {
      continue;
    }

    if ($output) {
      $output .= $delims[0];
    }

    // tell the application that this is a topic
    $output .= clean(1) . $delims[1];

    $output .= clean($row['topic_id']) . $delims[1];

    if ($oldCategory != $row['type']) {
      $output .= clean($row['type']) . $delims[1];
    } else {
      $output .= $delims[1];
    }

    $output .= clean($row['topic_title']) . $delims[1];

    if ($row[$replyStr] > 0) {
      $output .= clean(sprintf($strings[0], $row['topic_last_poster_name'], $user->format_date($row['topic_last_post_time']))) . $delims[1];
    } else {
      $output .= clean(sprintf($strings[3], $row['username'], $user->format_date($row['topic_time']))) . $delims[1];
    }

    if ($row['topic_views'] > 0 && $row[$replyStr] > 0) {
      $output .= clean(sprintf($strings[5], $row[$replyStr], ($row[$replyStr]!=1)?'ies':'y', $row['topic_views'], ($row['topic_views']!=1)?'s':''));
    } else {
      $output .= clean($strings[4]);
    }

    $output .= $delims[1];

    $output .= clean($row['forum_id']);

    // save old topic id so we don't waste bandwidth repeating it
    $oldCategory = $row['type'];
  }
}

// ****************************
// ******* UNANSWERED *********
// ****************************
if ($get == 'unanswered') {
  // configure style, language, etc.
  $user->setup('viewforum', $user->data['user_style']);

  // figure out what kind of reply counter to use
  $replyStr = ($auth->acl_get('m_approve', $id)) ? 'topic_replies_real' : 'topic_replies';

  // topic approved
  $sql_approved = ($auth->acl_get('m_approve', $id)) ? '' : ' AND p.post_approved = 1';

  $sql = "SELECT t.topic_id,t.topic_title,t.topic_last_post_time,t.topic_last_poster_name,username,topic_time,topic_views,$replyStr,t.forum_id FROM " . POSTS_TABLE . " p, " . TOPICS_TABLE . " t LEFT JOIN " . USERS_TABLE . " ON user_id=topic_poster WHERE t.topic_replies = 0 AND t.topic_moved_id = 0 AND p.topic_id = t.topic_id" . $sql_approved . " ORDER BY t.topic_last_post_time DESC";

  $result = $db->sql_query($sql);
  while ($row = $db->sql_fetchrow($result)) {
    $row['type'] = 'Unanswered Posts';
    $data[] = $row;
  }
  $db->sql_freeresult($result);

  for ($i=0;$i<count($data);$i++) {
    $row = $data[$i];

    // if the user does not have permissions to list this forum, skip everything until next branch
    if (!$auth->acl_get('f_list', $row['forum_id'])) {
      continue;
    }

    if ($output) {
      $output .= $delims[0];
    }

    // tell the application that this is a topic
    $output .= clean(1) . $delims[1];

    $output .= clean($row['topic_id']) . $delims[1];

    if ($oldCategory != $row['type']) {
      $output .= clean($row['type']) . $delims[1];
    } else {
      $output .= $delims[1];
    }

    $output .= clean($row['topic_title']) . $delims[1];

    if ($row[$replyStr] > 0) {
      $output .= clean(sprintf($strings[0], $row['topic_last_poster_name'], $user->format_date($row['topic_last_post_time']))) . $delims[1];
    } else {
      $output .= clean(sprintf($strings[3], $row['username'], $user->format_date($row['topic_time']))) . $delims[1];
    }

    if ($row['topic_views'] > 0 && $row[$replyStr] > 0) {
      $output .= clean(sprintf($strings[5], $row[$replyStr], ($row[$replyStr]!=1)?'ies':'y', $row['topic_views'], ($row['topic_views']!=1)?'s':''));
    } else {
      $output .= clean($strings[4]);
    }

    $output .= $delims[1];

    $output .= clean($row['forum_id']);

    // save old topic id so we don't waste bandwidth repeating it
    $oldCategory = $row['type'];
  }
}

// ****************************
// ********* MY POSTS *********
// ****************************
if ($get == 'myposts') {
  // configure style, language, etc.
  $user->setup('viewforum', $user->data['user_style']);

  // make sure the sent topics array is empty
  $sentTopics = array();

  // figure out what kind of reply counter to use
  $replyStr = ($auth->acl_get('m_approve', $id)) ? 'topic_replies_real' : 'topic_replies';

  // topic approved
  $sql_approved = ($auth->acl_get('m_approve', $id)) ? '' : ' AND p.post_approved = 1';

  $sql = "SELECT t.topic_id,t.topic_moved_id,t.topic_title,t.topic_last_post_time,t.topic_last_poster_name,username,topic_time,topic_views,$replyStr FROM " . POSTS_TABLE . " p, " . TOPICS_TABLE . " t LEFT JOIN " . USERS_TABLE . " ON user_id=topic_poster WHERE p.topic_id = t.topic_id" . $sql_approved . " AND user_id=" . $db->sql_escape($user->data['user_id']) . " ORDER BY t.topic_last_post_time DESC";

  $result = $db->sql_query($sql);
  while ($row = $db->sql_fetchrow($result)) {
    $row['type'] = 'My Posts';
    $data[] = $row;
  }
  $db->sql_freeresult($result);

  for ($i=0;$i<count($data);$i++) {
    $row = $data[$i];
    $topic_id = ($row['topic_moved_id'])?$row['topic_moved_id']:$row['topic_id'];

    // if this topic has been sent, don't send again
    if (in_array($topic_id, $sentTopics)) {
      continue;
    }

    if ($output) {
      $output .= $delims[0];
    }

    // tell the application that this is a topic
    $output .= clean(1) . $delims[1];

    $output .= clean($topic_id) . $delims[1];

    if ($oldCategory != $row['type']) {
      $output .= clean($row['type']) . $delims[1];
    } else {
      $output .= $delims[1];
    }

    $output .= clean($row['topic_title']) . $delims[1];

    if ($row[$replyStr] > 0) {
      $output .= clean(sprintf($strings[0], $row['topic_last_poster_name'], $user->format_date($row['topic_last_post_time']))) . $delims[1];
    } else {
      $output .= clean(sprintf($strings[3], $row['username'], $user->format_date($row['topic_time']))) . $delims[1];
    }

    if ($row['topic_views'] > 0 && $row[$replyStr] > 0) {
      $output .= clean(sprintf($strings[5], $row[$replyStr], ($row[$replyStr]!=1)?'ies':'y', $row['topic_views'], ($row['topic_views']!=1)?'s':''));
    } else {
      $output .= clean($strings[4]);
    }

    $output .= $delims[1];

    $output .= clean($row['forum_id']);

    // save old topic id so we don't waste bandwidth repeating it
    $oldCategory = $row['type'];

    // array of sent topics
    $sentTopics[] = $topic_id;
  }
}

// **************************
// ********* ONLINE *********
// **************************
if ($get == 'online') {
  $sql = 'SELECT u.user_id, u.username, u.user_type, u.user_colour FROM ' . USERS_TABLE . ' u, ' . SESSIONS_TABLE . ' s WHERE u.user_id = s.session_user_id AND s.session_time >= ' . (time() - ($config['load_online_time'] * 60)) .  ((!$show_guests) ? ' AND s.session_user_id <> ' . ANONYMOUS : '') . ' GROUP BY u.username ORDER BY s.session_time DESC';
  $result = $db->sql_query($sql);

  while ($row = $db->sql_fetchrow($result)) {
    if ($output) {
      $output .= $delims[0];
    }

    $output .= clean($row['user_id']) . $delims[1];
    $output .= clean($row['username']);
  }

  $db->sql_freeresult($result);
}

// ***************************
// ********* MEMBERS *********
// ***************************
if ($get == 'members') {
  $sql = "SELECT user_id,username FROM " . USERS_TABLE . "," . GROUPS_TABLE . " WHERE " . GROUPS_TABLE . ".group_id=" . USERS_TABLE . ".group_id AND group_name!='GUESTS' AND group_name!='BOTS' AND group_type!=" . GROUP_HIDDEN . " AND user_type!=" . USER_INACTIVE . " ORDER BY LOWER(username)";
  $result = $db->sql_query($sql);
  while ($row = $db->sql_fetchrow($result)) {
    if ($output) {
      $output .= $delims[0];
    }

    $output .= clean($row['user_id']) . $delims[1];
    $output .= clean($row['username']);
  }

  $db->sql_freeresult($result);
}

// ****************************
// *********** SENT ***********
// ****************************
if ($get == 'sent') {
  include($phpbb_root_path . 'includes/ucp/ucp_pm_viewfolder.' . $phpEx);

  // configure style, language, etc.
  $user->setup('viewforum', $forum_data['forum_style']);

  // get the users private messages
  $pms = get_pm_from(PRIVMSGS_SENTBOX, '', $user->data['user_id']);

  // we only want the messages, not the index
  $pms = $pms['rowset'];

  // sort messages so newest is on top
  krsort($pms);

  $sentHeader = false;

  // separate messages into read and unread
  while (list($key, $msg) = each($pms)) {
    if ($output) {
      $output .= $delims[0];
    }

    // format output
    $output .= clean($msg['msg_id']) . $delims[1];
    $output .= ((!$i)?clean(sprintf($strings[9], count($pms), ((count($pms)==1)?'':'s'))):'') . $delims[1];
    $output .= clean($msg['message_subject']) . $delims[1];
    $output .= clean(sprintf($strings[10], $msg['username'])) . $delims[1];
    $output .= clean($user->format_date($msg['message_time']));

    $sentHeader = true;
  }
}

// ****************************
// ********** INBOX ***********
// ****************************
if ($get == 'inbox') {
  include($phpbb_root_path . 'includes/ucp/ucp_pm_viewfolder.' . $phpEx);
  include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

  $folder_id = PRIVMSGS_INBOX;

  // if new messages arrived, place them into the appropriate folder
  if ($user->data['user_new_privmsg']) {
    place_pm_into_folder($global_privmsgs_rules, $release);
  }

  // configure style, language, etc.
  $user->setup('viewforum', $forum_data['forum_style']);

  // get the users private messages
  $pms = get_pm_from($folder_id, '', $user->data['user_id']);

  // we only want the messages, not the index
  $pms = $pms['rowset'];

  // sort messages so newest is on top
  krsort($pms);

  // separate messages into read and unread
  while (list($key, $val) = each($pms)) {
    if ($val['pm_unread']) {
      $unread[] = $val;
    } else {
      $read[] = $val;
    }
  }

  // send unread messages
  for ($i=0;$i<count($unread);$i++) {
    if ($output) {
      $output .= $delims[0];
    }

    // grab the current message
    $msg = $unread[$i];

    // format output
    $output .= clean($msg['msg_id']) . $delims[1];
    $output .= ((!$i)?clean(sprintf($strings[6], count($unread), ((count($unread)==1)?'':'s'))):'') . $delims[1];
    $output .= clean($msg['message_subject']) . $delims[1];
    $output .= clean(sprintf($strings[8], $msg['username'])) . $delims[1];
    $output .= clean($user->format_date($msg['message_time']));
  }

  // send read messages
  for ($i=0;$i<count($read);$i++) {
    if ($output) {
      $output .= $delims[0];
    }

    // grab the current message
    $msg = $read[$i];

    // format output
    $output .= clean($msg['msg_id']) . $delims[1];
    $output .= ((!$i)?clean(sprintf($strings[7], count($read), ((count($read)==1)?'':'s'))):'') . $delims[1];
    $output .= clean($msg['message_subject']) . $delims[1];
    $output .= clean(sprintf($strings[8], $msg['username'])) . $delims[1];
    $output .= clean($user->format_date($msg['message_time']));
  }
}

// ****************************
// *********** POST ***********
// ****************************
if ($get == 'post') {
  // don't allow anonymous posting or spam bots will go crazy
  if ($user->data['is_registered']) {
    include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
    include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
    include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

    $message_parser = new parse_message();

    $message = utf8_normalize_nfc(request_var('txt', '', true));
    $message_parser->message = &$message;

    $title = utf8_normalize_nfc(request_var('title', '', true));
    $username = $user->data['username'];
    $update_message = true;
    $mode = 'post';
    $forum_id = request_var('fid', '');
    $post_data['poster_id'] = $user->data['user_id'];
    $post_data['enable_bbcode'] = true;
    $post_data['enable_smilies'] = true;
    $post_data['enable_urls'] = true;

    // parse message
    if ($update_message) {
      if (sizeof($message_parser->warn_msg)) {
        $error[] = implode('<br />', $message_parser->warn_msg);
        $message_parser->warn_msg = array();
      }

      $message_parser->parse($post_data['enable_bbcode'], ($config['allow_post_links']) ? $post_data['enable_urls'] : false, $post_data['enable_smilies'], $img_status, $flash_status, $quote_status, $config['allow_post_links']);

      // on a refresh we do not care about message parsing errors
      if (sizeof($message_parser->warn_msg) && $refresh) {
        $message_parser->warn_msg = array();
      }
    } else {
      $message_parser->bbcode_bitfield = $post_data['bbcode_bitfield'];
    }

    // grab md5 'checksum' of new message
    $message_md5 = md5($message_parser->message);

    $data = array(
      'topic_title' => $title,
      'topic_first_post_id' => (isset($post_data['topic_first_post_id'])) ? (int) $post_data['topic_first_post_id'] : 0,
      'topic_last_post_id' => (isset($post_data['topic_last_post_id'])) ? (int) $post_data['topic_last_post_id'] : 0,
      'topic_time_limit' => (int) $post_data['topic_time_limit'],
      'topic_attachment' => (isset($post_data['topic_attachment'])) ? (int) $post_data['topic_attachment'] : 0,
      'post_id' => (int) $post_id,
      'topic_id' => (int) $topic_id,
      'forum_id' => (int) $forum_id,
      'icon_id' => (int) $post_data['icon_id'],
      'poster_id' => (int) $post_data['poster_id'],
      'enable_sig' => (bool) $post_data['enable_sig'],
      'enable_bbcode' => (bool) $post_data['enable_bbcode'],
      'enable_smilies' => (bool) $post_data['enable_smilies'],
      'enable_urls' => (bool) $post_data['enable_urls'],
      'enable_indexing' => (bool) $post_data['enable_indexing'],
      'message_md5' => (string) $message_md5,
      'post_time' => (isset($post_data['post_time'])) ? (int) $post_data['post_time'] : $current_time,
      'post_checksum' => (isset($post_data['post_checksum'])) ? (string) $post_data['post_checksum'] : '',
      'post_edit_reason' => $post_data['post_edit_reason'],
      'post_edit_user' => ($mode == 'edit') ? $user->data['user_id'] : ((isset($post_data['post_edit_user'])) ? (int) $post_data['post_edit_user'] : 0),
      'forum_parents' => $post_data['forum_parents'],
      'forum_name' => $post_data['forum_name'],
      'notify' => $notify,
      'notify_set' => $post_data['notify_set'],
      'poster_ip' => (isset($post_data['poster_ip'])) ? $post_data['poster_ip'] : $user->ip,
      'post_edit_locked' => (int) $post_data['post_edit_locked'],
      'bbcode_bitfield' => $message_parser->bbcode_bitfield,
      'bbcode_uid' => $message_parser->bbcode_uid,
      'message' => $message_parser->message,
      'attachment_data' => $message_parser->attachment_data,
      'filename_data' => $message_parser->filename_data,
      'topic_approved' => (isset($post_data['topic_approved'])) ? $post_data['topic_approved'] : false,
      'post_approved' => (isset($post_data['post_approved'])) ? $post_data['post_approved'] : false,
    );

    $output = clean(submit_post($mode, $title, $username, POST_NORMAL, $poll, $data, $update_message, 0));
  }
}

// ****************************
// *********** REPLY **********
// ****************************
if ($get == 'reply') {
  // don't allow anonymous posting or spam bots will go crazy
  if ($user->data['is_registered']) {
    include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
    include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
    include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

    $message_parser = new parse_message();

    $message = utf8_normalize_nfc(request_var('txt', '', true));
    $message_parser->message = &$message;

    $title = utf8_normalize_nfc(request_var('title', '', true));
    $username = $user->data['username'];
    $update_message = true;
    $mode = 'reply';
    $forum_id = request_var('fid', '');
    $topic_id = request_var('tid', '');
    $post_data['poster_id'] = $user->data['user_id'];
    $post_data['enable_bbcode'] = true;
    $post_data['enable_smilies'] = true;
    $post_data['enable_urls'] = true;

    // parse message
    if ($update_message) {
      if (sizeof($message_parser->warn_msg)) {
        $error[] = implode('<br />', $message_parser->warn_msg);
        $message_parser->warn_msg = array();
      }

      $message_parser->parse($post_data['enable_bbcode'], ($config['allow_post_links']) ? $post_data['enable_urls'] : false, $post_data['enable_smilies'], $img_status, $flash_status, $quote_status, $config['allow_post_links']);

      // on a refresh we do not care about message parsing errors
      if (sizeof($message_parser->warn_msg) && $refresh) {
        $message_parser->warn_msg = array();
      }
    } else {
      $message_parser->bbcode_bitfield = $post_data['bbcode_bitfield'];
    }

    // grab md5 'checksum' of new message
    $message_md5 = md5($message_parser->message);

    $data = array(
      'topic_title' => $title,
      'topic_first_post_id' => (isset($post_data['topic_first_post_id'])) ? (int) $post_data['topic_first_post_id'] : 0,
      'topic_last_post_id' => (isset($post_data['topic_last_post_id'])) ? (int) $post_data['topic_last_post_id'] : 0,
      'topic_time_limit' => (int) $post_data['topic_time_limit'],
      'topic_attachment' => (isset($post_data['topic_attachment'])) ? (int) $post_data['topic_attachment'] : 0,
      'post_id' => (int) $post_id,
      'topic_id' => (int) $topic_id,
      'forum_id' => (int) $forum_id,
      'icon_id' => (int) $post_data['icon_id'],
      'poster_id' => (int) $post_data['poster_id'],
      'enable_sig' => (bool) $post_data['enable_sig'],
      'enable_bbcode' => (bool) $post_data['enable_bbcode'],
      'enable_smilies' => (bool) $post_data['enable_smilies'],
      'enable_urls' => (bool) $post_data['enable_urls'],
      'enable_indexing' => (bool) $post_data['enable_indexing'],
      'message_md5' => (string) $message_md5,
      'post_time' => (isset($post_data['post_time'])) ? (int) $post_data['post_time'] : $current_time,
      'post_checksum' => (isset($post_data['post_checksum'])) ? (string) $post_data['post_checksum'] : '',
      'post_edit_reason' => $post_data['post_edit_reason'],
      'post_edit_user' => ($mode == 'edit') ? $user->data['user_id'] : ((isset($post_data['post_edit_user'])) ? (int) $post_data['post_edit_user'] : 0),
      'forum_parents' => $post_data['forum_parents'],
      'forum_name' => $post_data['forum_name'],
      'notify' => $notify,
      'notify_set' => $post_data['notify_set'],
      'poster_ip' => (isset($post_data['poster_ip'])) ? $post_data['poster_ip'] : $user->ip,
      'post_edit_locked' => (int) $post_data['post_edit_locked'],
      'bbcode_bitfield' => $message_parser->bbcode_bitfield,
      'bbcode_uid' => $message_parser->bbcode_uid,
      'message' => $message_parser->message,
      'attachment_data' => $message_parser->attachment_data,
      'filename_data' => $message_parser->filename_data,
      'topic_approved' => (isset($post_data['topic_approved'])) ? $post_data['topic_approved'] : false,
      'post_approved' => (isset($post_data['post_approved'])) ? $post_data['post_approved'] : false,
    );

    $output = clean(submit_post($mode, $title, $username, POST_NORMAL, $poll, $data, $update_message, 0));
  }
}

// ***************************
// ********* SEARCH **********
// ***************************
if ($search) {
  include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

  // configure style, language, etc.
  $user->setup('viewforum', $user->data['user_style']);

  // figure out what kind of reply counter to use
  $replyStr = ($auth->acl_get('m_approve', $id)) ? 'topic_replies_real' : 'topic_replies';

  // topic approved
  $sql_approved = ($auth->acl_get('m_approve', $id)) ? '' : ' AND ' . TOPICS_TABLE . '.topic_approved = 1';

  $sql = "SELECT topic_id,topic_moved_id,topic_title,topic_last_post_time,topic_last_poster_name,username,topic_time,topic_views,$replyStr,forum_id FROM (" . TOPICS_TABLE . ") LEFT JOIN " . USERS_TABLE . " ON user_id=topic_poster WHERE topic_title LIKE '%" . $db->sql_escape($search) . "%' AND topic_type IN (" . POST_NORMAL . ")$sql_approved ORDER BY topic_type DESC,topic_last_post_time DESC";
  $result = $db->sql_query($sql);
  while ($row = $db->sql_fetchrow($result)) {
    $row['type'] = 'Matching Topics';
    $data[] = $row;
  }
  $db->sql_freeresult($result);

  // don't display data if there are more than a certain amount of results
  if (count($data) > 50) {
    unset($data);
  }

  // make sure the sent topics array is empty
  $sentTopics = array();

  // loop through search results
  for ($i=0;$i<count($data);$i++) {
    $row = $data[$i];
    $forum_id = $row['forum_id'];
    $topic_id = ($row['topic_moved_id'])?$row['topic_moved_id']:$row['topic_id'];

    // if the user does not have permissions to list this forum, skip everything until next branch
    if (!$auth->acl_get('f_list', $forum_id)) {
      continue;
    }

    // if this topic has been sent, don't send again
    if (in_array($topic_id, $sentTopics)) {
      continue;
    }

    if ($output) {
      $output .= $delims[0];
    }

    // tell the application that this is a topic
    $output .= clean(1) . $delims[1];

    $output .= clean($topic_id) . $delims[1];

    if ($oldCategory != $row['type']) {
      $output .= clean($row['type']) . $delims[1];
    } else {
      $output .= $delims[1];
    }

    $output .= clean($row['topic_title']) . $delims[1];

    if ($row[$replyStr] > 0) {
      $output .= clean(sprintf($strings[0], $row['topic_last_poster_name'], $user->format_date($row['topic_last_post_time']))) . $delims[1];
    } else {
      $output .= clean(sprintf($strings[3], $row['username'], $user->format_date($row['topic_time']))) . $delims[1];
    }

    if ($row['topic_views'] > 0 && $row[$replyStr] > 0) {
      $output .= clean(sprintf($strings[5], $row[$replyStr], ($row[$replyStr]!=1)?'ies':'y', $row['topic_views'], ($row['topic_views']!=1)?'s':''));
    } else {
      $output .= clean($strings[4]);
    }

    $output .= $delims[1];

    $output .= clean($forum_id);

    // save old topic id so we don't waste bandwidth repeating it
    $oldCategory = $row['type'];

    // array of sent topics
    $sentTopics[] = $topic_id;
  }
}

// ***************************
// ********** TOPIC **********
// ***************************
if ($get == 'topic') {
  include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
  include($phpbb_root_path . 'includes/bbcode.' . $phpEx);

  // initial var setup
  $forum_id = request_var('f', 0);
  $post_id = request_var('p', 0);
  $voted_id = request_var('vote_id', array('' => 0));
  $view = request_var('view', '');
  $showUserSig = false;
  $start = 0;

  $default_sort_days = (!empty($user->data['user_post_show_days'])) ? $user->data['user_post_show_days'] : 0;
  $default_sort_key = (!empty($user->data['user_post_sortby_type'])) ? $user->data['user_post_sortby_type'] : 't';
  $default_sort_dir = (!empty($user->data['user_post_sortby_dir'])) ? $user->data['user_post_sortby_dir'] : 'a';

  $sort_days = request_var('st', $default_sort_days);
  $sort_key = request_var('sk', $default_sort_key);
  $sort_dir = request_var('sd', $default_sort_dir);
  $hilit_words = request_var('hl', '', true);

  // do we have a topic or post id?
  if (!$id && !$post_id) {
    if ($debug) {
      trigger_error('NO_TOPIC');
    } else {
      exit;
    }
  }

  // This handles querying for topics but also allows for direct linking to a post (and the calculation of which page the post is on and the correct display of viewtopic)
  $sql_array = array(
    'SELECT' => 't.*, f.*',
    'FROM' => array(FORUMS_TABLE => 'f'),
  );

  // The FROM-Order is quite important here, else t.* columns can not be correctly bound.
  if ($post_id) {
    $sql_array['FROM'][POSTS_TABLE] = 'p';
  }

  // topics table need to be the last in the chain
  $sql_array['FROM'][TOPICS_TABLE] = 't';

  if ($user->data['is_registered']) {
    $sql_array['SELECT'] .= ', tw.notify_status';
    $sql_array['LEFT_JOIN'] = array();

    $sql_array['LEFT_JOIN'][] = array(
      'FROM' => array(TOPICS_WATCH_TABLE => 'tw'),
      'ON' => 'tw.user_id = ' . $user->data['user_id'] . ' AND t.topic_id = tw.topic_id'
    );

    if ($config['allow_bookmarks']) {
      $sql_array['SELECT'] .= ', bm.topic_id as bookmarked';
      $sql_array['LEFT_JOIN'][] = array(
        'FROM' => array(BOOKMARKS_TABLE => 'bm'),
        'ON' => 'bm.user_id = ' . $user->data['user_id'] . ' AND t.topic_id = bm.topic_id'
      );
    }

    if ($config['load_db_lastread']) {
      $sql_array['SELECT'] .= ', tt.mark_time, ft.mark_time as forum_mark_time';

      $sql_array['LEFT_JOIN'][] = array(
        'FROM' => array(TOPICS_TRACK_TABLE => 'tt'),
        'ON' => 'tt.user_id = ' . $user->data['user_id'] . ' AND t.topic_id = tt.topic_id'
      );

      $sql_array['LEFT_JOIN'][] = array(
        'FROM' => array(FORUMS_TRACK_TABLE => 'ft'),
        'ON' => 'ft.user_id = ' . $user->data['user_id'] . ' AND t.forum_id = ft.forum_id'
      );
    }
  }

  if (!$post_id) {
    $sql_array['WHERE'] = "t.topic_id = $id";
  } else {
    $sql_array['WHERE'] = "p.post_id = $post_id AND t.topic_id = p.topic_id" . ((!$auth->acl_get('m_approve', $forum_id)) ? ' AND p.post_approved = 1' : '');
  }

  $sql_array['WHERE'] .= ' AND (f.forum_id = t.forum_id';

  if (!$forum_id) {
    // If it is a global announcement make sure to set the forum id to a postable forum
    $sql_array['WHERE'] .= ' OR (t.topic_type = ' . POST_GLOBAL . ' AND f.forum_type = ' . FORUM_POST . ')';
  } else {
    $sql_array['WHERE'] .= ' OR (t.topic_type = ' . POST_GLOBAL . " AND f.forum_id = $forum_id)";
  }

  $sql_array['WHERE'] .= ')';

  // Join to forum table on topic forum_id unless topic forum_id is zero
  // whereupon we join on the forum_id passed as a parameter ... this
  // is done so navigation, forum name, etc. remain consistent with where
  // user clicked to view a global topic
  $sql = $db->sql_build_query('SELECT', $sql_array);
  $result = $db->sql_query($sql);
  $topic_data = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  if (!$topic_data) {
    if ($debug) {
      trigger_error('NO_TOPIC');
    } else {
      exit;
    }
  }

  // This is for determining where we are (page)
  if ($post_id) {
    if ($post_id == $topic_data['topic_first_post_id'] || $post_id == $topic_data['topic_last_post_id']) {
      $check_sort = ($post_id == $topic_data['topic_first_post_id']) ? 'd' : 'a';

      if ($sort_dir == $check_sort) {
        $topic_data['prev_posts'] = ($auth->acl_get('m_approve', $forum_id)) ? $topic_data['topic_replies_real'] : $topic_data['topic_replies'];
      } else {
        $topic_data['prev_posts'] = 0;
      }
    } else {
      $sql = 'SELECT COUNT(p1.post_id) AS prev_posts FROM ' . POSTS_TABLE . ' p1, ' . POSTS_TABLE . " p2 WHERE p1.topic_id = {$topic_data['topic_id']} AND p2.post_id = {$post_id} " . ((!$auth->acl_get('m_approve', $forum_id)) ? 'AND p1.post_approved = 1' : '') . ' AND ' . (($sort_dir == 'd') ? 'p1.post_time >= p2.post_time' : 'p1.post_time <= p2.post_time');

      $result = $db->sql_query($sql);
      $row = $db->sql_fetchrow($result);
      $db->sql_freeresult($result);

      $topic_data['prev_posts'] = $row['prev_posts'] - 1;
    }
  }

  $forum_id = (int) $topic_data['forum_id'];
  $id = (int) $topic_data['topic_id'];

  $topic_replies = ($auth->acl_get('m_approve', $forum_id)) ? $topic_data['topic_replies_real'] : $topic_data['topic_replies'];

  // Check sticky/announcement time limit
  if (($topic_data['topic_type'] == POST_STICKY || $topic_data['topic_type'] == POST_ANNOUNCE) && $topic_data['topic_time_limit'] && ($topic_data['topic_time'] + $topic_data['topic_time_limit']) < time()) {
    $sql = 'UPDATE ' . TOPICS_TABLE . ' SET topic_type = ' . POST_NORMAL . ', topic_time_limit = 0 WHERE topic_id = ' . $id;
    $db->sql_query($sql);

    $topic_data['topic_type'] = POST_NORMAL;
    $topic_data['topic_time_limit'] = 0;
  }

  // Setup look and feel
  $user->setup('viewtopic', $topic_data['forum_style']);

  if (!$topic_data['topic_approved'] && !$auth->acl_get('m_approve', $forum_id)) {
    if ($debug) {
      trigger_error('NO_TOPIC');
    } else {
      exit;
    }
  }

  // Start auth check
  if (!$auth->acl_get('f_read', $forum_id)) {
    if ($user->data['user_id'] != ANONYMOUS) {
      if ($debug) {
        trigger_error('SORRY_AUTH_READ');
      } else {
        exit;
      }
    }

    login_box('', $user->lang['LOGIN_VIEWFORUM']);
  }

  // Forum is passworded ... check whether access has been granted to this user this session, if not show login box
  if ($topic_data['forum_password']) {
    login_forum_box($topic_data);
  }

  // What is start equal to?
  if ($post_id) {
    $start = floor(($topic_data['prev_posts']) / $config['posts_per_page']) * $config['posts_per_page'];
  }

  // Get topic tracking info
  if (!isset($topic_tracking_info)) {
    $topic_tracking_info = array();

    // Get topic tracking info
    if ($config['load_db_lastread'] && $user->data['is_registered']) {
      $tmp_topic_data = array($id => $topic_data);
      $topic_tracking_info = get_topic_tracking($forum_id, $id, $tmp_topic_data, array($forum_id => $topic_data['forum_mark_time']));
      unset($tmp_topic_data);
    } else if ($config['load_anon_lastread'] || $user->data['is_registered']) {
      $topic_tracking_info = get_complete_topic_tracking($forum_id, $id);
    }
  }

  // Post ordering options
  $limit_days = array(0 => $user->lang['ALL_POSTS'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);

  $sort_by_text = array('a' => $user->lang['AUTHOR'], 't' => $user->lang['POST_TIME'], 's' => $user->lang['SUBJECT']);
  $sort_by_sql = array('a' => 'u.username_clean', 't' => 'p.post_time', 's' => 'p.post_subject');

  // Obtain correct post count and ordering SQL if user has requested anything different
  if ($sort_days) {
    $min_post_time = time() - ($sort_days * 86400);

    $sql = 'SELECT COUNT(post_id) AS num_posts FROM ' . POSTS_TABLE . " WHERE topic_id = $id AND post_time >= $min_post_time " . (($auth->acl_get('m_approve', $forum_id)) ? '' : 'AND post_approved = 1');
    $result = $db->sql_query($sql);
    $total_posts = (int) $db->sql_fetchfield('num_posts');
    $db->sql_freeresult($result);

    $limit_posts_time = "AND p.post_time >= $min_post_time ";

    if (isset($_POST['sort'])) {
      $start = 0;
    }
  } else {
    $total_posts = $topic_replies + 1;
    $limit_posts_time = '';
  }

  // Was a highlight request part of the URI?
  $highlight_match = $highlight = '';
  if ($hilit_words) {
    foreach (explode(' ', trim($hilit_words)) as $word) {
      if (trim($word)) {
        $word = str_replace('\*', '\w+?', preg_quote($word, '#'));
        $word = preg_replace('#(^|\s)\\\\w\*\?(\s|$)#', '$1\w+?$2', $word);
        $highlight_match .= (($highlight_match != '') ? '|' : '') . $word;
      }
    }

    $highlight = urlencode($hilit_words);
  }

  // Make sure $start is set to the last page if it exceeds the amount
  if ($start < 0 || $start >= $total_posts) {
    $start = ($start < 0) ? 0 : floor(($total_posts - 1) / $config['posts_per_page']) * $config['posts_per_page'];
  }

  // Are we watching this topic?
  $s_watching_topic = array(
    'link' => '',
    'title' => '',
    'is_watching' => false,
  );

  if (@version_compare($config['version'], '3.0.4', '>=')) {
    if (($config['email_enable'] || $config['jab_enable']) && $config['allow_topic_notify'] && $user->data['is_registered']) {
      watch_topic_forum('topic', $s_watching_topic, $user->data['user_id'], $forum_id, $id, $topic_data['notify_status'], $start);

      // Reset forum notification if forum notify is set
      if ($config['allow_forum_notify'] && $auth->acl_get('f_subscribe', $forum_id)) {
        $s_watching_forum = $s_watching_topic;
        watch_topic_forum('forum', $s_watching_forum, $user->data['user_id'], $forum_id, 0);
      }
    }
  }

  // Bookmarks
  if ($config['allow_bookmarks'] && $user->data['is_registered'] && request_var('bookmark', 0)) {
    if (check_link_hash(request_var('hash', ''), "topic_$id")) {
      if (!$topic_data['bookmarked']) {
        $sql = 'INSERT INTO ' . BOOKMARKS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
          'user_id' => $user->data['user_id'],
          'topic_id' => $id,
        ));
        $db->sql_query($sql);
      } else {
        $sql = 'DELETE FROM ' . BOOKMARKS_TABLE . " WHERE user_id = {$user->data['user_id']} AND topic_id = $id";
        $db->sql_query($sql);
      }
      $message = (($topic_data['bookmarked']) ? $user->lang['BOOKMARK_REMOVED'] : $user->lang['BOOKMARK_ADDED']) . '<br /><br />' . sprintf($user->lang['RETURN_TOPIC'], '<a href="' . $viewtopic_url . '">', '</a>');
    } else {
      $message = $user->lang['BOOKMARK_ERR'] . '<br /><br />' . sprintf($user->lang['RETURN_TOPIC'], '<a href="' . $viewtopic_url . '">', '</a>');
    }
    meta_refresh(3, $viewtopic_url);

    if ($debug) {
      trigger_error($message);
    } else {
      exit;
    }
  }

  // Replace naughty words in title
  $topic_data['topic_title'] = censor_text($topic_data['topic_title']);

  // If the user is trying to reach the second half of the topic, fetch it starting from the end
  $store_reverse = false;
  $sql_limit = $config['posts_per_page'];

  if ($start > $total_posts / 2) {
    $store_reverse = true;

    if ($start + $config['posts_per_page'] > $total_posts) {
      $sql_limit = min($config['posts_per_page'], max(1, $total_posts - $start));
    }

    // Select the sort order
    $sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'ASC' : 'DESC');
    $sql_start = max(0, $total_posts - $sql_limit - $start);
  } else {
    // Select the sort order
    $sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
    $sql_start = $start;
  }

  // Container for user details, only process once
  $post_list = $user_cache = $id_cache = $attachments = $attach_list = $rowset = $update_count = $post_edit_list = array();
  $has_attachments = $display_notice = false;
  $i = 0;

  // Go ahead and pull all data for this topic
  $sql = 'SELECT p.post_id FROM ' . POSTS_TABLE . ' p' . (($sort_by_sql[$sort_key][0] == 'u') ? ', ' . USERS_TABLE . ' u': '') . " WHERE p.topic_id = $id " . ((!$auth->acl_get('m_approve', $forum_id)) ? 'AND p.post_approved = 1' : '') . " " . (($sort_by_sql[$sort_key][0] == 'u') ? 'AND u.user_id = p.poster_id': '') . " $limit_posts_time ORDER BY $sql_sort_order";
  #$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);
  $result = $db->sql_query($sql);

  $i = ($store_reverse) ? $sql_limit - 1 : 0;
  while ($row = $db->sql_fetchrow($result)) {
    $post_list[$i] = (int) $row['post_id'];
    ($store_reverse) ? $i-- : $i++;
  }
  $db->sql_freeresult($result);

  if (!sizeof($post_list)) {
    if ($debug) {
      if ($sort_days) {
        trigger_error('NO_POSTS_TIME_FRAME');
      } else {
        trigger_error('NO_TOPIC');
      }
    } else {
      exit;
    }
  }

  // Holding maximum post time for marking topic read
  // We need to grab it because we do reverse ordering sometimes
  $max_post_time = 0;

  $sql = $db->sql_build_query('SELECT', array(
    'SELECT' => 'u.*, z.friend, z.foe, p.*',

    'FROM' => array(
      USERS_TABLE => 'u',
      POSTS_TABLE => 'p',
    ),

    'LEFT_JOIN' => array(
      array(
        'FROM' => array(ZEBRA_TABLE => 'z'),
        'ON' => 'z.user_id = ' . $user->data['user_id'] . ' AND z.zebra_id = p.poster_id'
      )
    ),

    'WHERE' => $db->sql_in_set('p.post_id', $post_list) . ' AND u.user_id = p.poster_id'
  ));

  $result = $db->sql_query($sql);

  $now = getdate(time() + $user->timezone + $user->dst - date('Z'));

  // Posts are stored in the $rowset array while $attach_list, $user_cache and the global bbcode_bitfield are built
  while ($row = $db->sql_fetchrow($result)) {
    // Set max_post_time
    if ($row['post_time'] > $max_post_time) {
      $max_post_time = $row['post_time'];
    }

    $poster_id = (int) $row['poster_id'];

    // Does post have an attachment? If so, add it to the list
    if ($row['post_attachment'] && $config['allow_attachments']) {
      $attach_list[] = (int) $row['post_id'];

      if ($row['post_approved']) {
        $has_attachments = true;
      }
    }

    $rowset[$row['post_id']] = array(
      'hide_post' => ($row['foe'] && ($view != 'show' || $post_id != $row['post_id'])) ? true : false,

      'post_id' => $row['post_id'],
      'post_time' => $row['post_time'],
      'user_id' => $row['user_id'],
      'username' => $row['username'],
      'user_colour' => $row['user_colour'],
      'topic_id' => $row['topic_id'],
      'forum_id' => $row['forum_id'],
      'post_subject' => $row['post_subject'],
      'post_edit_count' => $row['post_edit_count'],
      'post_edit_time' => $row['post_edit_time'],
      'post_edit_reason' => $row['post_edit_reason'],
      'post_edit_user' => $row['post_edit_user'],
      'post_edit_locked' => $row['post_edit_locked'],
      'post_attachment' => $row['post_attachment'],
      'post_approved' => $row['post_approved'],
      'post_reported' => $row['post_reported'],
      'post_username' => $row['post_username'],
      'post_text' => $row['post_text'],
      'bbcode_uid' => $row['bbcode_uid'],
      'bbcode_bitfield' => $row['bbcode_bitfield'],
      'enable_smilies' => $row['enable_smilies'],
      'enable_sig' => $row['enable_sig'],
      'friend' => $row['friend'],
      'foe' => $row['foe'],
    );

    // Cache various user specific data ... so we don't have to recompute this each time the same user appears on this page
    if (!isset($user_cache[$poster_id])) {
      if ($poster_id == ANONYMOUS) {
        $user_cache[$poster_id] = array(
          'joined' => '',
          'posts' => '',
          'from' => '',
          'sig' => '',
          'sig_bbcode_uid' => '',
          'sig_bbcode_bitfield' => '',
          'online' => false,
          'avatar' => ($user->optionget('viewavatars')) ? get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']) : '',
          'rank_title' => '',
          'rank_image' => '',
          'rank_image_src' => '',
          'sig' => '',
          'profile' => '',
          'pm' => '',
          'email' => '',
          'www' => '',
          'jabber' => '',
          'search' => '',
          'age' => '',
          'username' => $row['username'],
          'user_colour' => $row['user_colour'],
          'warnings' => 0,
          'allow_pm' => 0,
        );

        get_user_rank($row['user_rank'], false, $user_cache[$poster_id]['rank_title'], $user_cache[$poster_id]['rank_image'], $user_cache[$poster_id]['rank_image_src']);
      } else {
        $user_sig = '';

        // We add the signature to every posters entry because enable_sig is post dependant
        if ($row['user_sig'] && $config['allow_sig'] && $user->optionget('viewsigs')) {
          $user_sig = $row['user_sig'];
        }

        $id_cache[] = $poster_id;

        $user_cache[$poster_id] = array(
          'joined' => $user->format_date($row['user_regdate']),
          'posts' => $row['user_posts'],
          'warnings' => (isset($row['user_warnings'])) ? $row['user_warnings'] : 0,
          'from' => (!empty($row['user_from'])) ? $row['user_from'] : '',
          'sig' => $user_sig,
          'sig_bbcode_uid' => (!empty($row['user_sig_bbcode_uid'])) ? $row['user_sig_bbcode_uid'] : '',
          'sig_bbcode_bitfield' => (!empty($row['user_sig_bbcode_bitfield'])) ? $row['user_sig_bbcode_bitfield'] : '',
          'viewonline' => $row['user_allow_viewonline'],
          'allow_pm' => $row['user_allow_pm'],
          'avatar' => ($user->optionget('viewavatars')) ? get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']) : '',
          'rank_title' => '',
          'rank_image' => '',
          'rank_image_src' => '',
          'username' => $row['username'],
          'user_colour' => $row['user_colour'],
          'online' => false,
          'profile' => append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=$poster_id"),
          'www' => $row['user_website'],
          'jabber' => ($row['user_jabber'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=contact&amp;action=jabber&amp;u=$poster_id") : '',
          'search' => ($auth->acl_get('u_search')) ? append_sid("{$phpbb_root_path}search.$phpEx", "author_id=$poster_id&amp;sr=posts") : '',
        );

        get_user_rank($row['user_rank'], $row['user_posts'], $user_cache[$poster_id]['rank_title'], $user_cache[$poster_id]['rank_image'], $user_cache[$poster_id]['rank_image_src']);

        if (!empty($row['user_allow_viewemail']) || $auth->acl_get('a_email')) {
    $user_cache[$poster_id]['email'] = ($config['board_email_form'] && $config['email_enable']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=email&amp;u=$poster_id") : (($config['board_hide_emails'] && !$auth->acl_get('a_email')) ? '' : 'mailto:' . $row['user_email']);
        } else {
    $user_cache[$poster_id]['email'] = '';
        }
      }
    }
  }
  $db->sql_freeresult($result);

  // Generate online information for user
  if ($config['load_onlinetrack'] && sizeof($id_cache)) {
    $sql = 'SELECT session_user_id, MAX(session_time) as online_time, MIN(session_viewonline) AS viewonline FROM ' . SESSIONS_TABLE . ' WHERE ' . $db->sql_in_set('session_user_id', $id_cache) . ' GROUP BY session_user_id';
    $result = $db->sql_query($sql);

    $update_time = $config['load_online_time'] * 60;
    while ($row = $db->sql_fetchrow($result)) {
      $user_cache[$row['session_user_id']]['online'] = (time() - $update_time < $row['online_time'] && (($row['viewonline']) || $auth->acl_get('u_viewonline'))) ? true : false;
    }
    $db->sql_freeresult($result);
  }
  unset($id_cache);

  // Pull attachment data
  if (sizeof($attach_list)) {
    if ($auth->acl_get('u_download') && $auth->acl_get('f_download', $forum_id)) {
      $sql = 'SELECT * FROM ' . ATTACHMENTS_TABLE . ' WHERE ' . $db->sql_in_set('post_msg_id', $attach_list) . ' AND in_message = 0 ORDER BY filetime DESC, post_msg_id ASC';
      $result = $db->sql_query($sql);

      while ($row = $db->sql_fetchrow($result)) {
        $attachments[$row['post_msg_id']][] = $row;
      }
      $db->sql_freeresult($result);

      // No attachments exist, but post table thinks they do so go ahead and reset post_attach flags
      if (!sizeof($attachments)) {
        $sql = 'UPDATE ' . POSTS_TABLE . ' SET post_attachment = 0 WHERE ' . $db->sql_in_set('post_id', $attach_list);
        $db->sql_query($sql);

        // We need to update the topic indicator too if the complete topic is now without an attachment
        if (sizeof($rowset) != $total_posts) {
          // Not all posts are displayed so we query the db to find if there's any attachment for this topic
          $sql = 'SELECT a.post_msg_id as post_id FROM ' . ATTACHMENTS_TABLE . ' a, ' . POSTS_TABLE . " p WHERE p.topic_id = $id AND p.post_approved = 1 AND p.topic_id = a.topic_id";
          $result = $db->sql_query_limit($sql, 1);
          $row = $db->sql_fetchrow($result);
          $db->sql_freeresult($result);

          if (!$row) {
            $sql = 'UPDATE ' . TOPICS_TABLE . " SET topic_attachment = 0 WHERE topic_id = $id";
            $db->sql_query($sql);
          }
        } else {
          $sql = 'UPDATE ' . TOPICS_TABLE . " SET topic_attachment = 0 WHERE topic_id = $id";
          $db->sql_query($sql);
        }
      } else if ($has_attachments && !$topic_data['topic_attachment']) {
        // Topic has approved attachments but its flag is wrong
        $sql = 'UPDATE ' . TOPICS_TABLE . " SET topic_attachment = 1 WHERE topic_id = $id";
        $db->sql_query($sql);

        $topic_data['topic_attachment'] = 1;
      }
    } else {
      $display_notice = true;
    }
  }

  // Output the posts
  $first_unread = $post_unread = false;
  for ($i = 0, $end = sizeof($post_list); $i < $end; ++$i) {
    // a non-existing rowset only happens if there was no user present for the entered poster_id
    if (!isset($rowset[$post_list[$i]])) {
      continue;
    }

    $row =& $rowset[$post_list[$i]];
    $poster_id = $row['user_id'];

    // End signature parsing, only if needed
    if ($user_cache[$poster_id]['sig'] && $row['enable_sig'] && empty($user_cache[$poster_id]['sig_parsed'])) {
      $user_cache[$poster_id]['sig'] = censor_text($user_cache[$poster_id]['sig']);
      $user_cache[$poster_id]['sig'] = clean($user_cache[$poster_id]['sig'], $user_cache[$poster_id]['sig_bbcode_uid']);
      $user_cache[$poster_id]['sig_parsed'] = true;
    }

    // Parse the message and subject
    $message = censor_text($row['post_text']);

    // Replace naughty words such as farty pants
    $row['post_subject'] = censor_text($row['post_subject']);

    // Highlight active words (primarily for search)
    if ($highlight_match) {
      $message = preg_replace('#(?!<.*)(?<!\w)(' . $highlight_match . ')(?!\w|[^<>]*(?:</s(?:cript|tyle))?>)#is', '<span class="posthilit">\1</span>', $message);
      $row['post_subject'] = preg_replace('#(?!<.*)(?<!\w)(' . $highlight_match . ')(?!\w|[^<>]*(?:</s(?:cript|tyle))?>)#is', '<span class="posthilit">\1</span>', $row['post_subject']);
    }

    // display attached images in post
    if (!empty($attachments[$row['post_id']])) {
      if (empty($extensions) || !is_array($extensions)) {
        $extensions = $cache->obtain_attach_extensions($forum_id);
      }

      $sentAttachmentsLabel = false;
      foreach ($attachments[$row['post_id']] as $attachment) {
        if ($extensions[$attachment['extension']]['display_cat'] == ATTACHMENT_CATEGORY_IMAGE) {
          if (!$sentAttachmentsLabel) {
            $message .= '<br><br><b>' . $user->lang['ATTACHMENTS'] . '</b><br>';
            $sentAttachmentsLabel = true;
          }

          $resize = true;
          #$message .= '<img src="' . generate_board_url() . '/download/file.php?id=' . $attachment['attach_id'] . '"' . (($resize)?' onload="resizeIMG(this);"':'') . '>';
          $message .= '<img src="' . generate_board_url() . '/touchbb.php?get=file&id=' . $attachment['attach_id'] . '&sid=' . $user->session_id . '&uid=' . $user->data['user_id'] . '"' . (($resize)?' onload="resizeIMG(this);"':'') . '>';
        }
      }
    }

    $post_unread = (isset($topic_tracking_info[$id]) && $row['post_time'] > $topic_tracking_info[$id]) ? true : false;

    if ($showUserSig && $user_cache[$poster_id]['sig']) {
      $message .= '<hr>' . $user_cache[$poster_id]['sig'];
    }

    if ($output) {
      $output .= $delims[0];
    }

    $output .= clean($row['post_id']) . $delims[1];
    $output .= clean($row['username']) . $delims[1];
    $output .= clean($message, $row['bbcode_uid']) . $delims[1];
    $output .= clean('') . $delims[1]; // empty for now
    $output .= clean($user->format_date($row['post_time'], false, false));

    unset($rowset[$post_list[$i]]);
    unset($attachments[$row['post_id']]);
  }
  unset($rowset, $user_cache);

  // Update topic view and if necessary attachment view counters ... but only for humans and if this is the first 'page view'
  if (isset($user->data['session_page']) && !$user->data['is_bot'] && (strpos($user->data['session_page'], '&t=' . $id) === false || isset($user->data['session_created']))) {
    $sql = 'UPDATE ' . TOPICS_TABLE . ' SET topic_views = topic_views + 1, topic_last_view_time = ' . time() . " WHERE topic_id = $id";
    $db->sql_query($sql);

    // Update the attachment download counts
    if (sizeof($update_count)) {
      $sql = 'UPDATE ' . ATTACHMENTS_TABLE . ' SET download_count = download_count + 1 WHERE ' . $db->sql_in_set('attach_id', array_unique($update_count));
      $db->sql_query($sql);
    }
  }

  // Only mark topic if it's currently unread. Also make sure we do not set topic tracking back if earlier pages are viewed.
  if (isset($topic_tracking_info[$id]) && $topic_data['topic_last_post_time'] > $topic_tracking_info[$id] && $max_post_time > $topic_tracking_info[$id]) {
    markread('topic', $forum_id, $id, $max_post_time);

    // Update forum info
    $all_marked_read = update_forum_tracking_info($forum_id, $topic_data['forum_last_post_time'], (isset($topic_data['forum_mark_time'])) ? $topic_data['forum_mark_time'] : false, false);
  } else {
    $all_marked_read = true;
  }
}

// ***************************
// ********** FORUM **********
// ***************************
if ($get == 'forum') {
  include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

  // Start initial var setup
  $default_sort_days = (!empty($user->data['user_topic_show_days'])) ? $user->data['user_topic_show_days'] : 0;
  $default_sort_key = (!empty($user->data['user_topic_sortby_type'])) ? $user->data['user_topic_sortby_type'] : 't';
  $default_sort_dir = (!empty($user->data['user_topic_sortby_dir'])) ? $user->data['user_topic_sortby_dir'] : 'd';

  $sort_days = request_var('st', $default_sort_days);
  $sort_key = request_var('sk', $default_sort_key);
  $sort_dir = request_var('sd', $default_sort_dir);

  // Check if the user has actually sent a forum ID with his/her request
  // If not give them a nice error page.
  if (!$id) {
    if ($debug) {
      trigger_error('NO_FORUM');
    } else {
      exit;
    }
  }

  $sql_from = FORUMS_TABLE . ' f';
  $lastread_select = '';

  // Grab appropriate forum data
  if ($config['load_db_lastread'] && $user->data['is_registered']) {
    $sql_from .= ' LEFT JOIN ' . FORUMS_TRACK_TABLE . ' ft ON (ft.user_id = ' . $user->data['user_id'] . ' AND ft.forum_id = f.forum_id)';
    $lastread_select .= ', ft.mark_time';
  }

  if ($user->data['is_registered']) {
    $sql_from .= ' LEFT JOIN ' . FORUMS_WATCH_TABLE . ' fw ON (fw.forum_id = f.forum_id AND fw.user_id = ' . $user->data['user_id'] . ')';
    $lastread_select .= ', fw.notify_status';
  }

  $sql = "SELECT f.* $lastread_select FROM $sql_from WHERE f.forum_id = $id";
  $result = $db->sql_query($sql);
  $forum_data = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  if (!$forum_data) {
    if ($debug) {
      trigger_error('NO_FORUM');
    } else {
      exit;
    }
  }

  // Configure style, language, etc.
  $user->setup('viewforum', $forum_data['forum_style']);

  // Redirect to login upon emailed notification links
  if (isset($_GET['e']) && !$user->data['is_registered']) {
    login_box('', $user->lang['LOGIN_NOTIFY_FORUM']);
  }

  // Permissions check
  if (!$auth->acl_gets('f_list', 'f_read', $id) || ($forum_data['forum_type'] == FORUM_LINK && $forum_data['forum_link'] && !$auth->acl_get('f_read', $id))) {
    if ($user->data['user_id'] != ANONYMOUS) {
      if ($debug) {
        trigger_error('SORRY_AUTH_READ');
      } else {
        exit;
      }
    }

    login_box('', $user->lang['LOGIN_VIEWFORUM']);
  }

  // Forum is passworded ... check whether access has been granted to this
  // user this session, if not show login box
  if ($forum_data['forum_password']) {
    login_forum_box($forum_data);
  }

  // Is this forum a link? ... User got here either because the
  // number of clicks is being tracked or they guessed the id
  if ($forum_data['forum_type'] == FORUM_LINK && $forum_data['forum_link']) {
    // Does it have click tracking enabled?
    if ($forum_data['forum_flags'] & FORUM_FLAG_LINK_TRACK) {
      $sql = 'UPDATE ' . FORUMS_TABLE . ' SET forum_posts = forum_posts + 1 WHERE forum_id = ' . $id;
      $db->sql_query($sql);
    }

    // We redirect to the url. The third parameter indicates that external redirects are allowed.
    redirect($forum_data['forum_link'], false, true);
    return;
  }

  // Do we have subforums?
  $active_forum_ary = $moderators = array();

  if ($forum_data['left_id'] != $forum_data['right_id'] - 1) {
    list($active_forum_ary, $moderators) = display_forums($forum_data, $config['load_moderators'], $config['load_moderators']);
    $forums = $template->_tpldata['forumrow'];

    for ($i=0;$i<count($forums);$i++) {
      $forum = $forums[$i];

      if ($output) {
        $output .= $delims[0];
      }

      // tell the application that this is a forum
      $output .= clean(0) . $delims[1];

      $output .= clean($forum['FORUM_ID']) . $delims[1];

      if (!$i) {
        $output .= clean('Subforums') . $delims[1];
      } else {
        $output .= $delims[1];
      }

      $output .= clean($forum['FORUM_NAME']) . $delims[1];
      $output .= clean(sprintf($strings[0], $forum['LAST_POSTER'], $forum['LAST_POST_TIME'])) . $delims[1];

      if ($forum['POSTS'] > 0 && $forum['TOPICS'] > 0) {
        $output .= clean(sprintf($strings[1], $forum['POSTS'], ($forum['POSTS']!=1)?'s':'', $forum['TOPICS'], ($forum['TOPICS']!=1)?'s':''));
      } else {
        $output .= clean($strings[2]);
      }
    }
  } else {
    $template->assign_var('S_HAS_SUBFORUM', false);
    get_moderators($moderators, $id);
  }

  // Not postable forum or showing active topics?
  if (!($forum_data['forum_type'] == FORUM_POST || (($forum_data['forum_flags'] & FORUM_FLAG_ACTIVE_TOPICS) && $forum_data['forum_type'] == FORUM_CAT))) {
    if ($debug) {
      page_footer();
    } else {
      exit;
    }
  }

  // Ok, if someone has only list-access, we only display the forum list.
  // We also make this circumstance available to the template in case we want to display a notice. ;)
  if (!$auth->acl_get('f_read', $id)) {
    if ($debug) {
      $template->assign_vars(array(
        'S_NO_READ_ACCESS' => true,
        'S_AUTOLOGIN_ENABLED' => ($config['allow_autologin']) ? true : false,
        'S_LOGIN_ACTION' => append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login') . '&amp;redirect=' . urlencode(str_replace('&amp;', '&', build_url())),
      ));

      page_footer();
    } else {
      exit;
    }
  }

  // Is a forum specific topic count required?
  if ($forum_data['forum_topics_per_page']) {
    $config['topics_per_page'] = $forum_data['forum_topics_per_page'];
  }

  // Forum rules and subscription info
  $s_watching_forum = array(
    'link' => '',
    'title' => '',
    'is_watching' => false,
  );

  if (@version_compare($config['version'], '3.0.4', '>=')) {
    if (($config['email_enable'] || $config['jab_enable']) && $config['allow_forum_notify'] && $forum_data['forum_type'] == FORUM_POST && $auth->acl_get('f_subscribe', $id)) {
      $notify_status = (isset($forum_data['notify_status'])) ? $forum_data['notify_status'] : NULL;
      watch_topic_forum('forum', $s_watching_forum, $user->data['user_id'], $id, 0, $notify_status);
    }
  }

  // Topic ordering options
  $limit_days = array(0 => $user->lang['ALL_TOPICS'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);

  $sort_by_text = array('a' => $user->lang['AUTHOR'], 't' => $user->lang['POST_TIME'], 'r' => $user->lang['REPLIES'], 's' => $user->lang['SUBJECT'], 'v' => $user->lang['VIEWS']);
  $sort_by_sql = array('a' => 't.topic_first_poster_name', 't' => 't.topic_last_post_time', 'r' => 't.topic_replies', 's' => 't.topic_title', 'v' => 't.topic_views');

  $s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
  gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param, $default_sort_days, $default_sort_key, $default_sort_dir);

  // Limit topics to certain time frame, obtain correct topic count global announcements must not be counted, normal announcements have to be counted, as forum_topics(_real) includes them
  if ($sort_days) {
    $min_post_time = time() - ($sort_days * 86400);

    $sql = 'SELECT COUNT(topic_id) AS num_topics FROM ' . TOPICS_TABLE . " WHERE forum_id = $forum_id AND ((topic_type <> " . POST_GLOBAL . " AND topic_last_post_time >= $min_post_time) OR topic_type = " . POST_ANNOUNCE . ") " . (($auth->acl_get('m_approve', $forum_id)) ? '' : 'AND topic_approved = 1');
    $result = $db->sql_query($sql);
    $topics_count = (int) $db->sql_fetchfield('num_topics');
    $db->sql_freeresult($result);

    if (isset($_POST['sort'])) {
      $start = 0;
    }
    $sql_limit_time = "AND t.topic_last_post_time >= $min_post_time";

    // Make sure we have information about day selection ready
    $template->assign_var('S_SORT_DAYS', true);
  } else {
    $topics_count = ($auth->acl_get('m_approve', $forum_id)) ? $forum_data['forum_topics_real'] : $forum_data['forum_topics'];
    $sql_limit_time = '';
  }

  // Make sure $start is set to the last page if it exceeds the amount
  if ($start < 0 || $start > $topics_count) {
    $start = ($start < 0) ? 0 : floor(($topics_count - 1) / $config['topics_per_page']) * $config['topics_per_page'];
  }

  // Display active topics?
  $s_display_active = ($forum_data['forum_type'] == FORUM_CAT && ($forum_data['forum_flags'] & FORUM_FLAG_ACTIVE_TOPICS)) ? true : false;

  // Grab all topic data
  $rowset = $announcement_list = $topic_list = $global_announce_list = array();

  $sql_array = array(
    'SELECT' => 't.*',
    'FROM' => array(TOPICS_TABLE => 't'),
    'LEFT_JOIN' => array(),
  );

  $sql_approved = ($auth->acl_get('m_approve', $id)) ? '' : 'AND t.topic_approved = 1';

  if ($user->data['is_registered']) {
    if ($config['load_db_track']) {
      $sql_array['LEFT_JOIN'][] = array('FROM' => array(TOPICS_POSTED_TABLE => 'tp'), 'ON' => 'tp.topic_id = t.topic_id AND tp.user_id = ' . $user->data['user_id']);
      $sql_array['SELECT'] .= ', tp.topic_posted';
    }

    if ($config['load_db_lastread']) {
      $sql_array['LEFT_JOIN'][] = array('FROM' => array(TOPICS_TRACK_TABLE => 'tt'), 'ON' => 'tt.topic_id = t.topic_id AND tt.user_id = ' . $user->data['user_id']);
      $sql_array['SELECT'] .= ', tt.mark_time';

      if ($s_display_active && sizeof($active_forum_ary)) {
        $sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TRACK_TABLE => 'ft'), 'ON' => 'ft.forum_id = t.forum_id AND ft.user_id = ' . $user->data['user_id']);
        $sql_array['SELECT'] .= ', ft.mark_time AS forum_mark_time';
      }
    }
  }

  if ($forum_data['forum_type'] == FORUM_POST) {
    // Obtain announcements ... removed sort ordering, sort by time in all cases
    $sql = $db->sql_build_query('SELECT', array(
      'SELECT' => $sql_array['SELECT'],
      'FROM' => $sql_array['FROM'],
      'LEFT_JOIN' => $sql_array['LEFT_JOIN'],
      'WHERE' => 't.forum_id IN (' . $id . ', 0) AND t.topic_type IN (' . POST_ANNOUNCE . ', ' . POST_GLOBAL . ')',
      'ORDER_BY' => 't.topic_time DESC',
    ));
    $result = $db->sql_query($sql);

    while ($row = $db->sql_fetchrow($result)) {
      $rowset[$row['topic_id']] = $row;
      $announcement_list[] = $row['topic_id'];

      if ($row['topic_type'] == POST_GLOBAL) {
        $global_announce_list[$row['topic_id']] = true;
      } else {
        $topics_count--;
      }
    }
    $db->sql_freeresult($result);
  }

  // If the user is trying to reach late pages, start searching from the end
  $store_reverse = false;
  $sql_limit = $config['topics_per_page'];
  if ($start > $topics_count / 2) {
    $store_reverse = true;

    if ($start + $config['topics_per_page'] > $topics_count) {
      $sql_limit = min($config['topics_per_page'], max(1, $topics_count - $start));
    }

    // Select the sort order
    $sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'ASC' : 'DESC');
    $sql_start = max(0, $topics_count - $sql_limit - $start);
  } else {
    // Select the sort order
    $sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
    $sql_start = $start;
  }

  if ($forum_data['forum_type'] == FORUM_POST || !sizeof($active_forum_ary)) {
    $sql_where = 't.forum_id = ' . $id;
  } else if (empty($active_forum_ary['exclude_forum_id'])) {
    $sql_where = $db->sql_in_set('t.forum_id', $active_forum_ary['forum_id']);
  } else {
    $get_forum_ids = array_diff($active_forum_ary['forum_id'], $active_forum_ary['exclude_forum_id']);
    $sql_where = (sizeof($get_forum_ids)) ? $db->sql_in_set('t.forum_id', $get_forum_ids) : 't.forum_id = ' . $id;
  }

  // Grab just the sorted topic ids
  $sql = 'SELECT t.topic_id FROM ' . TOPICS_TABLE . " t WHERE $sql_where AND t.topic_type IN (" . POST_NORMAL . ', ' . POST_STICKY . ") $sql_approved $sql_limit_time ORDER BY t.topic_type " . ((!$store_reverse) ? 'DESC' : 'ASC') . ', ' . $sql_sort_order;
  $result = $db->sql_query_limit($sql, $sql_limit, $sql_start);
  #$result = $db->sql_query($sql);

  while ($row = $db->sql_fetchrow($result)) {
    $topic_list[] = (int) $row['topic_id'];
  }
  $db->sql_freeresult($result);

  // For storing shadow topics
  $shadow_topic_list = array();

  if (sizeof($topic_list)) {
    // SQL array for obtaining topics/stickies
    $sql_array = array(
      'SELECT' => $sql_array['SELECT'],
      'FROM' => $sql_array['FROM'],
      'LEFT_JOIN' => $sql_array['LEFT_JOIN'],
      'WHERE' => $db->sql_in_set('t.topic_id', $topic_list),
    );

    // If store_reverse, then first obtain topics, then stickies, else the other way around...
    // Funnily enough you typically save one query if going from the last page to the middle (store_reverse) because
    // the number of stickies are not known
    $sql = $db->sql_build_query('SELECT', $sql_array);
    $result = $db->sql_query($sql);

    while ($row = $db->sql_fetchrow($result)) {
      if ($row['topic_status'] == ITEM_MOVED) {
        $shadow_topic_list[$row['topic_moved_id']] = $row['topic_id'];
      }

      $rowset[$row['topic_id']] = $row;
    }
    $db->sql_freeresult($result);
  }

  // If we have some shadow topics, update the rowset to reflect their topic information
  if (sizeof($shadow_topic_list)) {
    $sql = 'SELECT * FROM ' . TOPICS_TABLE . ' WHERE ' . $db->sql_in_set('topic_id', array_keys($shadow_topic_list));
    $result = $db->sql_query($sql);

    while ($row = $db->sql_fetchrow($result)) {
      $orig_topic_id = $shadow_topic_list[$row['topic_id']];

      // If the shadow topic is already listed within the rowset (happens for active topics for example), then do not include it...
      if (isset($rowset[$row['topic_id']])) {
        // We need to remove any trace regarding this topic. :)
        unset($rowset[$orig_topic_id]);
        unset($topic_list[array_search($orig_topic_id, $topic_list)]);
        $topics_count--;

        continue;
      }

      // Do not include those topics the user has no permission to access
      if (!$auth->acl_get('f_read', $row['forum_id'])) {
        // We need to remove any trace regarding this topic. :)
        unset($rowset[$orig_topic_id]);
        unset($topic_list[array_search($orig_topic_id, $topic_list)]);
        $topics_count--;

        continue;
      }

      // We want to retain some values
      $row = array_merge($row, array(
        'topic_moved_id' => $rowset[$orig_topic_id]['topic_moved_id'],
        'topic_status' => $rowset[$orig_topic_id]['topic_status'],
        'topic_type' => $rowset[$orig_topic_id]['topic_type'],
      ));

      // Shadow topics are never reported
      $row['topic_reported'] = 0;

      $rowset[$orig_topic_id] = $row;
    }
    $db->sql_freeresult($result);
  }
  unset($shadow_topic_list);

  // Ok, adjust topics count for active topics list
  if ($s_display_active) {
    $topics_count = 1;
  }

  $topic_list = ($store_reverse) ? array_merge($announcement_list, array_reverse($topic_list)) : array_merge($announcement_list, $topic_list);
  $topic_tracking_info = $tracking_topics = array();

  // Okay, lets dump out the page ...
  if (sizeof($topic_list)) {
    $mark_forum_read = true;
    $mark_time_forum = 0;

    // Active topics?
    if ($s_display_active && sizeof($active_forum_ary)) {
      // Generate topic forum list...
      $topic_forum_list = array();
      foreach ($rowset as $t_id => $row) {
        $topic_forum_list[$row['forum_id']]['forum_mark_time'] = ($config['load_db_lastread'] && $user->data['is_registered'] && isset($row['forum_mark_time'])) ? $row['forum_mark_time'] : 0;
        $topic_forum_list[$row['forum_id']]['topics'][] = $t_id;
      }

      if ($config['load_db_lastread'] && $user->data['is_registered']) {
        foreach ($topic_forum_list as $f_id => $topic_row) {
          $topic_tracking_info += get_topic_tracking($f_id, $topic_row['topics'], $rowset, array($f_id => $topic_row['forum_mark_time']), false);
        }
      } else if ($config['load_anon_lastread'] || $user->data['is_registered']) {
        foreach ($topic_forum_list as $f_id => $topic_row) {
          $topic_tracking_info += get_complete_topic_tracking($f_id, $topic_row['topics'], false);
        }
      }

      unset($topic_forum_list);
    } else {
      if ($config['load_db_lastread'] && $user->data['is_registered']) {
        $topic_tracking_info = get_topic_tracking($id, $topic_list, $rowset, array($id => $forum_data['mark_time']), $global_announce_list);
        $mark_time_forum = (!empty($forum_data['mark_time'])) ? $forum_data['mark_time'] : $user->data['user_lastmark'];
      } else if ($config['load_anon_lastread'] || $user->data['is_registered']) {
        $topic_tracking_info = get_complete_topic_tracking($id, $topic_list, $global_announce_list);

        if (!$user->data['is_registered']) {
          $user->data['user_lastmark'] = (isset($tracking_topics['l'])) ? (int) (base_convert($tracking_topics['l'], 36, 10) + $config['board_startdate']) : 0;
        }
        $mark_time_forum = (isset($tracking_topics['f'][$id])) ? (int) (base_convert($tracking_topics['f'][$id], 36, 10) + $config['board_startdate']) : $user->data['user_lastmark'];
      }
    }

    foreach ($topic_list as $topic_id) {
      $row = &$rowset[$topic_id];

      // Replies
      $replies = ($auth->acl_get('m_approve', $id)) ? $row['topic_replies_real'] : $row['topic_replies'];

      if ($row['topic_status'] == ITEM_MOVED) {
        $topic_id = $row['topic_moved_id'];
        $unread_topic = false;
      } else {
        $unread_topic = (isset($topic_tracking_info[$topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$topic_id]) ? true : false;
      }

      // Get folder img, topic status/type related information
      $folder_img = $folder_alt = $topic_type = '';
      topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);

      if ($output) {
        $output .= $delims[0];
      }

      // tell the application that this is a topic
      $output .= clean(1) . $delims[1];

      $output .= clean($topic_id) . $delims[1];

      if ($oldCategory != $row['topic_type']) {
        switch ($row['topic_type']) {
          case POST_NORMAL:
            $type = 'Topics';
            break;

          case POST_STICKY:
            $type = 'Stickies';
            break;

          case POST_ANNOUNCE:
            $type = 'Announcements';
            break;

          case POST_GLOBAL:
            $type = 'Announcements';
            break;

          default:
            $type = 'Other';
            break;
        }

        $output .= clean($type) . $delims[1];
      } else {
        $output .= $delims[1];
      }

      $output .= clean($row['topic_title']) . $delims[1];

      if ($replies > 0) {
        $output .= clean(sprintf($strings[0], $row['topic_last_poster_name'], $user->format_date($row['topic_last_post_time']))) . $delims[1];
      } else {
        $output .= clean(sprintf($strings[3], $row['topic_first_poster_name'], $user->format_date($row['topic_time']))) . $delims[1];
      }

      if ($row['topic_views'] > 0 && $replies > 0) {
        $output .= clean(sprintf($strings[5], $replies, ($replies!=1)?'ies':'y', $row['topic_views'], ($row['topic_views']!=1)?'s':''));
      } else {
        $output .= clean($strings[4]);
      }

      // save old topic id so we don't waste bandwidth repeating it
      $oldCategory = $row['topic_type'];

      if ($unread_topic) {
        $mark_forum_read = false;
      }

      unset($rowset[$topic_id]);
    }
  }

  // This is rather a fudge but it's the best I can think of without requiring information
  // on all topics (as we do in 2.0.x). It looks for unread or new topics, if it doesn't find
  // any it updates the forum last read cookie. This requires that the user visit the forum
  // after reading a topic
  if ($forum_data['forum_type'] == FORUM_POST && sizeof($topic_list) && $mark_forum_read) {
    update_forum_tracking_info($id, $forum_data['forum_last_post_time'], false, $mark_time_forum);
  }
}

// ***************************
// ******* FORUM INDEX *******
// ***************************
if ($get == 'index') {
  include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

  // configure style, language, etc.
  $user->setup('viewforum');

  display_forums('', $config['load_moderators']);
  $forums = $template->_tpldata['forumrow'];

  for ($i=0;$i<count($forums);$i++) {
    $forum = $forums[$i];

    // category with no members
    if ($forum['S_IS_CAT'] == 1) {
      $cat_name = $forum['FORUM_NAME'];
      continue;
    }

    if ($output) {
      $output .= $delims[0];
    }

    $output .= clean($forum['FORUM_ID']) . $delims[1];

    if ($oldCategory != $cat_name || $i == 0) {
      $output .= clean(($cat_name)?$cat_name:'Forum') . $delims[1];
    } else {
      $output .= $delims[1];
    }

    $output .= clean($forum['FORUM_NAME']) . $delims[1];

    if ($forum['POSTS'] > 0 && $forum['TOPICS'] > 0) {
      $output .= clean(sprintf($strings[0], $forum['LAST_POSTER'], $forum['LAST_POST_TIME'])) . $delims[1];
      $output .= clean(sprintf($strings[1], $forum['POSTS'], ($forum['POSTS']!=1)?'s':'', $forum['TOPICS'], ($forum['TOPICS']!=1)?'s':''));
    } else {
      $output .= clean($strings[2]) . $delims[1];
      $output .= '';
    }

    // save old topic id so we don't waste bandwidth repeating it
    $oldCategory = $cat_name;
  }
}

// ***************************
// ********* VALIDATE ********
// ***************************
if ($get == 'status') {
  $output .= 'OK' . $delims[1];
  $output .= clean($config['sitename']);

  if ($addMyForumToDirectory) {
    // in the future this will connect to our server to add your forum
  }
}

// has the script been run with parameters?
if ($get || $search || ($username && $password)) {
  // tell TouchBB info about this board and user
  $header = '';

  // send users logged in status
  $header .= clean(($user->data['is_registered'])?1:0)  . $delims[1];

  // send new private message count
  $header .= clean($user->data['user_new_privmsg']);

  // insert this into the output
  $output = $header . $delims[2] . $output;

  // send the output to TouchBB
  echo $appName;

  if ($get == 'status') {
    echo count($delims) . implode($delims, '');
  }

  echo $output . $delims[1] . md5($output);

  // clean up properly
  garbage_collection();
} else {
  displayStatus();
}

// ***************************
// ******** FUNCTIONS ********
// ***************************
function clean($str, $uid = '') {
  global $fm, $config, $user, $phpbb_root_path, $delims;

  $str = stripslashes($str);
  $str = replace_all("\r\n", "\n", $str);

  if ($uid) {
    $str = str_replace("\n", '<br>', $str);

    // convert bbcode smilies to img sources, checking image width to see if it will fit on the phone or not
    if (preg_match_all('/<!-- s(.*?) --><img src="{SMILIES_PATH}\/(.*?)" alt="(.*?)" title="(.*?)" \/><!-- s(.*?) -->/is', $str, $matches)) {
      $tags = $matches[0];
      $icons = $matches[2];

      for ($i=0;$i<count($tags);$i++) {
        $resize = true;
        $local = $phpbb_root_path . $config['smilies_path'] . '/' . $icons[$i];
        if ($imagedata = @getimagesize($local)) {
          if ($imagedata[0] < 320) {
            $resize = false;
          }
        }

        $icon = '<img src="' . generate_board_url() . '/' . $config['smilies_path'] . '/' . $icons[$i] . '"' . (($resize)?' onload="resizeIMG(this);"':'') . '>';
        $str = str_replace($tags[$i], $icon, $str);
      }
    }

    // our list of bbcodes to change
    $bbcode = array('/\[i(|:' . $uid . ')\](.*?)\[\/i(|:' . $uid . ')\]/is' => '<i>$2</i>',
                    '/\[b(|:' . $uid . ')\](.*?)\[\/b(|:' . $uid . ')\]/is' => '<b>$2</b>',
                    '/\[u(|:' . $uid . ')\](.*?)\[\/u(|:' . $uid . ')\]/is' => '<u>$2</u>',
                    '/\[color=(.*?)(|:' . $uid . ')\](.*?)\[\/color(|:' . $uid . ')\]/is' => '<font color="$1">$3</font>',
                    '/\[size=(.*?)(|:' . $uid . ')\](.*?)\[\/size(|:' . $uid . ')\]/is' => '<span style="font-size:$1%;line-height:normal">$3</span>',
                    '/\[center(|:' . $uid . ')\](.*?)\[\/center(|:' . $uid . ')\]/is' => '<center>$2</center>',
                    '/\[hr(|:' . $uid . ')\](.*?)\[\/hr(|:' . $uid . ')\]/is' => '<hr>',
                    '/\[url(|:' . $uid . ')\](.*?)\[\/url(|:' . $uid . ')\]/is' => '<a href="$2">$2</a>',
                    '/\[url=(.*?)(|:' . $uid . ')\](.*?)\[\/url(|:' . $uid . ')\]/is' => '<a href="$1">$3</a>',
                    '/<!-- m --><a class="postlink" href="(.*?)">(.*?)<\/a><!-- m -->/is' => '<a href="$1">$2</a>',
                    '/<!-- w --><a class="postlink" href="(.*?)">(.*?)<\/a><!-- w -->/is' => '<a href="$1">$2</a>',
                    '/\[quote=(.*?)(|:' . $uid . ')\](.*?)\[\/quote(|:' . $uid . ')\]/is' => '<blockquote><cite>$1 wrote:</cite>$3</blockquote>',
                    '/\[quote(|:' . $uid . ')="(.*?)"\](.*?)\[\/quote(|:' . $uid . ')\]/is' => '<blockquote><cite>$2 wrote:</cite>$3</blockquote>',
                    '/\[quote(|:' . $uid . ')\](.*?)\[\/quote(|:' . $uid . ')\]/is' => '<blockquote>$2</blockquote>',
                    '/\[img(|:' . $uid . ')\](.*?)\[\/img(|:' . $uid . ')\]/is' => '<img src="$2" onload="resizeIMG(this);">',
                    '/\[code(|:' . $uid . ')\](.*?)\[\/code(|:' . $uid . ')\]/is' => '<dl class="codebox"><dt>Code:</dt><dd><code>$2</code></dd></dl>',
                    '/\[\*(|:' . $uid . ')\](.*?)\[\/\*:m(|:' . $uid . ')\]/is' => '&bull;$2',
                    '/\[list(|:' . $uid . ')\](.*?)\[\/list:u(|:' . $uid . ')\]([\n]|)/is' => '$2',
                    '/\[albumimg(|:' . $uid . ')\](.*?)\[\/albumimg(|:' . $uid . ')\]/is' => '<img src="' . generate_board_url() . '/album_thumbnail.php?pic_id=$2" alt="Image $2" title="Image $2" border="0" onload="resizeIMG(this);">',
    );

    // make the bbcode changes
    while (list($regex, $html) = each($bbcode)) {
      while (preg_match($regex, $str)) {
        $str = preg_replace($regex, $html, $str);
      }
    }
  } else {
    $str = replace_all("\n", ' ', $str);
    $str = replace_all('<br>', ' ', $str);
    $str = html_entity_decode($str);
  }

  $str = str_replace($delims, '', $str);
  $str = preg_replace('/\s\s+/', ' ', $str);
  $str = trim($str);

  return $str;
}

function replace_all($old, $new, $str) {
  while (substr_count($str, $old)) {
    $str = str_replace($old, $new, $str);
  }

  return $str;
}

function header_filename($file) {
  $user_agent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';

  // There be dragons here.
  // Not many follows the RFC...
  if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Safari') !== false || strpos($user_agent, 'Konqueror') !== false) {
    return "filename=" . rawurlencode($file);
  }

  // follow the RFC for extended filename for the rest
  return "filename*=UTF-8''" . rawurlencode($file);
}

function set_modified_headers($stamp, $browser) {
  // let's see if we have to send the file at all
  $last_load   =  isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime(trim($_SERVER['HTTP_IF_MODIFIED_SINCE'])) : false;
  if ((strpos(strtolower($browser), 'msie 6.0') === false) && (strpos(strtolower($browser), 'msie 8.0') === false)) {
    if ($last_load !== false && $last_load <= $stamp) {
      if (substr(strtolower(@php_sapi_name()),0,3) === 'cgi') {
        // in theory, we shouldn't need that due to php doing it. Reality offers a differing opinion, though
        header('Status: 304 Not Modified', true, 304);
      } else {
        header('HTTP/1.0 304 Not Modified', true, 304);
      }

      // seems that we need those too ... browsers
      header('Pragma: public');
      header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
      return true;
    } else {
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $stamp) . ' GMT');
    }
  }

  return false;
}

function displayStatus() {
  global $appName, $connectorVersion, $appURL, $fmfURL, $config, $phpEx, $phpbb_root_path, $debug;

  // script is working variable
  $works = true;

  // check for existance of these include files
  if (!@include($phpbb_root_path . 'includes/functions_posting.' . $phpEx)) { $works = false; }
  if (!@include($phpbb_root_path . 'includes/functions_display.' . $phpEx)) { $works = false; };
  if (!@include($phpbb_root_path . 'includes/message_parser.' . $phpEx)) { $works = false; };
  if (!@include($phpbb_root_path . 'includes/ucp/ucp_pm_viewfolder.' . $phpEx)) { $works = false; };

  // only continue if we didn't receive an error above
  if (!$works) {
    // list of functions to check for
    $functions[] = 'clean';
    $functions[] = 'submit_post';
    $functions[] = 'preg_replace';
    $functions[] = 'get_pm_from';
    $functions[] = 'display_forums';
    $functions[] = 'replace_all';
    $functions[] = 'array_keys';
    $functions[] = 'array_values';
    $functions[] = 'html_entity_decode';
    $functions[] = 'generate_board_url';

    // loop through functions to check
    for ($i=0;$i<count($functions);$i++) {
      if (!function_exists($functions[$i])) {
        $works = false;
      }
    }
  }

  echo "<html><head><title>TouchBB Status</title></head><body>$appName v$connectorVersion is ";
  echo ($works)?"<span style=\"color:green;font-weight:bold;\">installed</span>":"<span style=\"color:red;font-weight:bold;\">not working</span>";
  echo ' for ' . (($config['sitename'])?"<b>${config['sitename']}</b>":'your forum') . ' running phpBB v' . $config['version'] . '.<br>';
  echo ($works)?"You may download <a href=\"$appURL\">$appName</a> from the App Store.":"Please visit <a href=\"$fmfURL\">$fmfURL</a> for support.";

  // this is only shown when debug mode is on to help the TouchBB team
  if ($debug) {
    echo '<br><br><font color="white">';
    echo 'Script Path: ' . $config['script_path'] . '<br>';
    echo 'Cookie Domain: ' . $config['cookie_domain'] . '<br>';
    echo 'Cookie Name: ' . $config['cookie_name'] . '<br>';
    echo 'Cookie Path: ' . $config['cookie_path'] . '<br>';
    echo '</font>';
  }

  echo "</body></html>";

  exit;
}
