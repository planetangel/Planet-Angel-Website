<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/adminclasses/admin.migratep2j.class.php $
// $Id: admin.migratep2j.class.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

/*******************************************************************************
**   Migration of DB and Files from Ponygallery ML to Joomgallery             **
**   On the fly generating of categories in db and file system                **
**   moving the pictures in the new categories                                **
*******************************************************************************/

/*********************************************************************************************
 ** Credits:                                                                                **
 ** recursive creation of directories:                                                      **
 ** http://www.developers-guide.net/forums/3134,php-rekursives-erstellen-von-verzeichnissen **
 **                                                                                         **
 ** handling of PHP-header calls:                                                           **
 ** http://www.expertsrt.com/tutorials/Matt/HTTP_headers.html                               **
*********************************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class Joom_Migrate_P2J {
  var $safemode;
  var $max_execution_time;
  var $starttime;
  var $maxtime;
  var $joomdb;
  var $ponydb;
  var $galvars;
  var $sitestatus;
  var $prefix;
  var $logfile;
  var $logfilename;

/**
 * Constructor of class
 *
 * @param string $action (checks,starts or continues a migration)
 */
  function Joom_Migrate_P2J($action) {
    $config = Joom_getConfig();
    $mainframe = & JFactory::getApplication('administrator');

    if ($action != 'check') {
      $this->logfilename='migration.ponytojoom.txt';

      //Check the maximum execution time of the script
      //set secure setting of the real execution time
      $max_execution_time = @ini_get('max_execution_time');

      //try to set the max execution time to 60s if lower than
      // if not succesful the return value will be the old time, so use this
      if ($max_execution_time < 60) {
        @ini_set('max_execution_time','60');
        $max_execution_time = @ini_get('max_execution_time');
        $this->max_execution_time = $max_execution_time;
      }else {
        $this->max_execution_time = $max_execution_time;
      }
      $this->maxtime = (int) $this->max_execution_time * 0.8;
      $this->starttime = time();
    }

    $this->prefix = $mainframe->getCfg('dbprefix'); // anstatt global $mosConfig_dbprefix;
    $this->sitestatus = $mainframe->getCfg('offline'); // anstatt global $mosConfig_offline;

    $this->joomdb['main'] = $this->prefix . 'joomgallery';
    $this->joomdb['cat'] = $this->prefix . 'joomgallery_catg';
    $this->joomdb['comments'] = $this->prefix . 'joomgallery_comments';
    $this->joomdb['votes'] = $this->prefix . 'joomgallery_votes';
    $this->joomdb['nameshields'] = $this->prefix . 'joomgallery_nameshields';

    $this->ponydb['main'] = $this->prefix . 'ponygallery';
    $this->ponydb['cat'] = $this->prefix . 'ponygallery_catg';
    $this->ponydb['comments'] = $this->prefix . 'ponygallery_comments';
    $this->ponydb['votes'] = $this->prefix . 'ponygallery_votes';
    $this->ponydb['nameshields'] = $this->prefix . 'ponygallery_nameshields';

    //Include configurations of JoomGallery and PonyGallery ML
    $ponyconfig = JPATH_ADMINISTRATOR .
                  DS .
                  'components' .
                  DS .
                  'com_ponygallery' .
                  DS .
                  'config.ponygallery.php';

    if (file_exists($ponyconfig)) {
      include_once ($ponyconfig);
      $this->galvars['pgimgpic'] = JPATH_ROOT.DS . $ag_pathimages;
      $this->galvars['pgorig'] = JPATH_ROOT.DS . $ag_pathoriginalimages;
      $this->galvars['pgthumb'] = JPATH_ROOT.DS . $ag_paththumbs;
    } else {
      $this->galvars['pgimgpic'] = JText::_('JGA_PONYGALLERY') . JText::_('JGA_PICTURES_DIRECTORY');
      $this->galvars['pgorig']   = JText::_('JGA_PONYGALLERY') . JText::_('JGA_ORIGINALS_DIRECTORY');
      $this->galvars['pgthumb']  = JText::_('JGA_PONYGALLERY') . JText::_('JGA_THUMBNAILS_DIRECTORY');
    }

    $this->galvars['jgimgpic'] = JPATH_ROOT.DS . $config->jg_pathimages;
    $this->galvars['jgorig'] = JPATH_ROOT.DS . $config->jg_pathoriginalimages;
    $this->galvars['jgthumb'] = JPATH_ROOT.DS . $config->jg_paththumbs;

    switch ($action) {
      case 'check':
      $this->Joom_Migrate_Check();
      break;

      case 'start':
      $this->Joom_Migrate_OpenLogfile("w");
      $this->Joom_Migrate_WriteLogfile ("max. execution time: ".$this->max_execution_time." seconds");
      $this->Joom_Migrate_WriteLogfile ("calculated refresh time: ".$this->maxtime." seconds");
      $this->Joom_Migrate_WriteLogfile ("*****************************");
      $this->Joom_Migrate_CreateJoomCats();
      break;

      case 'continue';
      $this->Joom_Migrate_OpenLogfile("a");
      $this->Joom_Migrate_WriteLogfile ("*****************************");
      $this->Joom_Migrate_Move();
      break;

      default:
      die();
      break;
    }
  }

  /**
   * Check the requirements for migration
   *
   * @return bool true=migration can be started
   */
  function Joom_Migrate_Check() {
    $info = '';
    $ready = true;

    //Site offline?
    $img = $this->sitestatus ? 'tick.png' : 'publish_x.png';
    $info .= "\n";
    $info .= "    <td colspan=\"3\">\n";
    $info .= "      <h4>".JText::_('JGA_SITESTATUS')."</h4>\n";
    $info .= "    </td>\n";
    $info .= "  </tr>\n";
    $info .= "  <tr>\n";
    $info .= "    <td width=\"80%\" align=\"left\">\n";
    $info .= "      ".JText::_('JGA_SITE_OFFLINE')."\n";
    $info .= "    </td>\n";
    if ($this->sitestatus == 0) {
      $info .= "    <td width=\"10%\" align=\"center\">\n";
      $info .= "      &nbsp;\n";
      $info .= "    </td>\n";
      $info .= "    <td align=\"center\">\n";
      $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
      $info .= "    </td>\n";

      $ready = false;
    } else {
      $info .= "    <td width=\"10%\" align=\"center\">\n";
      $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
      $info .= "    </td>\n";
      $info .= "    <td align=\"center\">\n";
      $info .= "      &nbsp;\n";
      $info .= "    </td>\n";

    }
    $info .= "  </tr>\n";

    //Check source and destination directories
    $info .= "  <tr>\n";
    $info .= "    <td colspan=\"3\">\n";
    $info .= "      <h4>".JText::_('JGA_CHECK_DIRECTORIES')."</h4>\n";
    $info .= "    </td>\n";
    $info .= "  </tr>\n";
    foreach ($this->galvars as $galv) {
      if ( file_exists($galv) ) {
        $img   = 'tick.png';
        $info .= "  <tr>\n";
        $info .= "    <td align=\"left\">\n";
        $info .= "      ".$galv."\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      &nbsp;\n";
        $info .= "    </td>\n";
        $info .= "  </tr>\n";
      } else {
        $ready = false;
        $img   = 'publish_x.png';
        $info .= "  <tr>\n";
        $info .= "    <td align=\"left\">\n";
        $info .= "      ".$galv."\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      &nbsp;\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
        $info .= "    </td>\n";
        $info .= "  </tr>\n";
      }
    }


    //DB Tables
    $info .= "  <tr>\n";
    $info .= "    <td colspan=\"3\">\n";
    $info .= "      <h4>".JText::_('JGA_CHECK_DATABASETABLES')."</h4>\n";
    $info .= "    </td>\n";
    $info .= "  </tr>\n";

    //JoomGallery
    foreach ($this->joomdb as $jdb) {
      $query = 'SELECT COUNT(*) FROM ' . $jdb;
      $ct = $this->Joom_Migrate_DBOP($query);
      $ctcount = mysql_result($ct['result'],0);

      if ($ct['result'] == TRUE && $ctcount == 0 ) {
        $img   = 'tick.png';
        $info .= "  <tr>\n";
        $info .= "    <td align=\"left\">\n";
        $info .= "      ".$jdb.": <b><span style=\"color:#080; font-size:12px; font-weight:bold;\">".JText::_('JGA_EMPTY') . "</span></b>\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      &nbsp;\n";
        $info .= "    </td>\n";
        $info .= "  </tr>\n";
      } else {
        $ready = false;
        $img   = 'publish_x.png';
        $info .= "  <tr>\n";
        $info .= "    <td align=\"left\">\n";
        $info .= "      ".$jdb.": <b><span style=\"color:#f30; font-weight:bold;\">".$ctcount ." ".JText::_('JGA_ROWS') . "</span></b>\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      &nbsp;\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
        $info .= "    </td>\n";
        $info .= "  </tr>\n";
      }
    }

    //Ponygallery ML
    $ponyready = false;
    foreach ($this->ponydb as $pdb) {
      $query = 'SELECT COUNT(*) FROM ' . $pdb;
      $ct = $this->Joom_Migrate_DBOP($query);
      if ($ct['result'] == TRUE) {
        $count = mysql_result($ct['result'],0);
        if ($ct['result'] == TRUE && $count == 0 ) {
          $img   = 'tick.png';
          $info .= "  <tr>\n";
          $info .= "    <td align=\"left\">\n";
          $info .= "      ".$pdb.": <b><span style=\"color:#080; font-size:12px; font-weight:bold;\">".JText::_('JGA_EMPTY') . "</span></b>\n";
          $info .= "    </td>\n";
          $info .= "    <td align=\"center\">\n";
          $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
          $info .= "    </td>\n";
          $info .= "    <td align=\"center\">\n";
          $info .= "      &nbsp;\n";
          $info .= "    </td>\n";
          $info .= "  </tr>\n";
        } else {
          $ponyready = true;
          $img   = 'tick.png';
          $info .= "  <tr>\n";
          $info .= "    <td align=\"left\">\n";
          $info .= "      ".$pdb.": <b><span style=\"color:#080; font-weight:bold;\">".$count ." ".JText::_('JGA_ROWS') . "</span></b>\n";
          $info .= "    </td>\n";
          $info .= "    <td align=\"center\">\n";
          $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
          $info .= "    </td>\n";
          $info .= "    <td align=\"center\">\n";
          $info .= "      &nbsp;\n";
          $info .= "    </td>\n";
          $info .= "  </tr>\n";
        }
      } else {
        $img   = 'publish_x.png';
        $info .= "  <tr>\n";
        $info .= "    <td align=\"left\">\n";
        $info .= "      ".$pdb.": <b><span style=\"color:#f30; font-weight:bold;\">".$ct['error']."</span></b>\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      &nbsp;\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
        $info .= "    </td>\n";
        $info .= "  </tr>\n";
      }
    }
    //if there are no rows in all tables then deny migration
    if (!$ponyready){
      $ready = false;
    }

    //check if there are any orphans in PonyGallery ML,
    //pictures with no existing username
    $query = 'SELECT p.id FROM '. $this->ponydb['main'] . ' AS p
        LEFT JOIN '. $this->prefix .'users u
        ON p.owner = u.username
        WHERE u.username IS NULL';

    $resultarr = $this->Joom_Migrate_DBOP($query);

    if ($resultarr['result'] != FALSE) {
      $ctcount = mysql_num_rows($resultarr['result']);

      if ($ctcount == 0 ) {
        $img   = 'tick.png';
        $info .= "  <tr>\n";
        $info .= "    <td align=\"left\">\n";
        $info .= "      ".JText::_('JGA_MIGRATION_ORPHANPICS')." <b><span style=\"color:#080; font-size:12px; font-weight:bold;\">".JText::_('NO')."</span></b>\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      &nbsp;\n";
        $info .= "    </td>\n";
        $info .= "  </tr>\n";
      } else {
        $ready = false;
        $img   = 'publish_x.png';
        $info .= "  <tr>\n";
        $info .= "    <td align=\"left\">\n";
        $info .= "      ".JText::_('JGA_MIGRATION_ORPHANPICS')."<b><span style=\"color:#f30; font-weight:bold;\">".$ctcount."</span></b>\n";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      &nbsp;";
        $info .= "    </td>\n";
        $info .= "    <td align=\"center\">\n";
        $info .= "      <img src=\"images/$img\" border=\"0\" alt=\"\" />\n";
        $info .= "    </td>\n";
        $info .= "  </tr>\n";
      }
    }

    if (!$ready) {
      $this->Joom_Migrate_HTML(false,$info,
                              '<div style="text-align:center;color:#f30;padding:1em 0;font:bold 1.2em Verdana;">
                              '. JText::_('JGA_MIGRATION_FALSE').'</div><div style="text-align:center;">'.JText::_('JGA_MIGRATION_FALSE_LONG').'</div>');
    } else {
      $this->Joom_Migrate_HTML(true,$info,
                               '<div style="text-align:center;color:#080;padding:1em 0;font:bold 1.2em Verdana;">
                               '. JText::_('JGA_MIGRATION_TRUE').'</div><div style="text-align:center;">'.JText::_('JGA_MIGRATION_TRUE_LONG').'</div>');
    }
    return $ready;
  }

  /**
   * Migration of categories
   *
   */
  function Joom_Migrate_CreateJoomCats() {
    //get all category entries from ponygallery
    $query = 'select * from ' . $this->ponydb['cat'] . ' order by parent';
    $result = $this->Joom_Migrate_DBOP($query);

    if (mysql_num_rows($result['result']) ==0) {
      $this->Joom_Migrate_WriteLogfile ('nothing to do');
    }

    while ($row = mysql_fetch_object($result['result'])) {
      $sourcearray[] = $row;
      $cid = (int) $row->cid;
      $parentcid = (int) $row->parent;

      //save the catpath = name in temp array
      $arrayref[$cid] = array (
        'catpath' => $this->Joom_Migrate_FixCatname($row->name . '_' . $cid),
        'parent' => (int) $row->parent
      );

      //if root category with parent of parent category = 0 then set the final catpath
      if (isset ($arrayref[$parentcid]) && $arrayref[$parentcid]['parent'] == 0) {
        $arrayref[$cid]['catpath'] = $arrayref[$parentcid]['catpath']
                                     . '/'
                                     . $arrayref[$cid]['catpath'];
        $arrayref[$cid]['parent'] = 0;
      }
    }

    //Iterate through temp array to complete the catpath of all categories
    $ready = false;
    while ($ready == false) {
      $ready = true;
      foreach (array_keys($arrayref) as $key) {
        $cat = & $arrayref[$key];
        if ($cat['parent'] != 0) {
          if ($arrayref[$cat['parent']]['parent'] == 0) {
            $cat['catpath'] = $arrayref[$cat['parent']]['catpath']
                              . '/'
                              . $cat['catpath'];
            $cat['parent'] = 0;
          } else {
            $ready = false;
          }
        }
        unset ($cat);
      }
    }

    //Create physical directory structures and create an index.html in each
    foreach ($arrayref as $catdir) {
      JFolder::create($this->galvars['jgimgpic'] . DS . $catdir['catpath']);
      touch($this->galvars['jgimgpic'] . DS . $catdir['catpath'] . '/index.html');

      JFolder::create($this->galvars['jgorig'] . DS . $catdir['catpath']);
      touch($this->galvars['jgorig'] . DS . $catdir['catpath'] . '/index.html');

      JFolder::create($this->galvars['jgthumb'] . DS . $catdir['catpath']);
      touch($this->galvars['jgthumb'] . DS . $catdir['catpath'] . '/index.html');
    }

    //fill joomgallery_catgs in a db transaction (begin..commit)
    $this->Joom_Migrate_WriteLogfile ( 'insert categories in joomgallery_catg');
    $result = $this->Joom_Migrate_DBOP('begin');

    foreach ($sourcearray as $row) {
      $query = "INSERT INTO " . $this->joomdb['cat'] .
          " (cid,name,parent,description,ordering,access,published,catimage,img_position,catpath)
          \nVALUES (".
      $row->cid
      . ",\n'"
      . mysql_escape_string($row->name)
      . "',\n"
      . $row->parent
      . ",\n'"
      . mysql_escape_string($row->description)
      . "',\n"
      . $row->ordering
      . ",\n"
      . $row->access
      . ",\n"
      . $row->published
      . ",\n'"
      . $row->catimage
      . "',\n"
      . $row->img_position
      . ",\n'"
      . mysql_escape_string($arrayref[$row->cid]['catpath'])
      . "')";

      $result = $this->Joom_Migrate_DBOP($query);
      $this->Joom_Migrate_WriteLogfile ("Category ".$row->cid. " created: ".$row->name);
      $result = $this->Joom_Migrate_DBOP($query);

      //check if there are any pics in category to migrate
      //otherwise delete it in db
      $query= 'select count(id) from ' . $this->ponydb['main'] . ' where catid = ' . $row->cid;
      $result = $this->Joom_Migrate_DBOP($query);
      $count = mysql_result($result['result'],0);

      if ($count==0) {
        $this->Joom_Migrate_WriteLogfile ("no pictures in category");
        $query = 'DELETE FROM ' . $this->ponydb['cat'] . ' where cid=' . $row->cid;
        $result = $this->Joom_Migrate_DBOP($query);
        $this->Joom_Migrate_WriteLogfile ("Pony Category deleted, id: " . $row->cid);
      }
    }
    $result = $this->Joom_Migrate_DBOP('commit');

    $this->Joom_Migrate_WriteLogfile ("All Categories migrated");
    $this->Joom_Migrate_WriteLogfile ("***********************");

    //check the time, if true, we start with migration of the pictures directly
    //without refresh
    if ($this->Joom_Migrate_Checktime() == true) {
      $this->Joom_Migrate_WriteLogfile ("Starting with migration of pictures directly");
      $this->Joom_Migrate_Move();
    } else {
      //Make a refresh to start new with moving the pictures
      $this->Joom_Migrate_Refresh('continue');
    }
  }

/**
 * Migrate pictures with comments/votes/nametags
 *
 */
  function Joom_Migrate_Move() {
    $limitpics=100;

    $cat_migrate=true;

    while($cat_migrate){
      //get cat from pony
      $query = 'SELECT * FROM ' . $this->ponydb['cat'] . ' LIMIT 1';
      $result = $this->Joom_Migrate_DBOP($query);

      //Migration finished, if no category left
      if (mysql_num_rows($result['result'])==0) {
        $cat_migrate=false;
        continue;
      }

      $ponycatg = mysql_fetch_object($result['result']);

      $this->Joom_Migrate_WriteLogfile ("Migration of category ".$ponycatg->cid);
      $pic_migrate=true;

      //get the corresponding row from joomcatg, because of the new catpath
      $query = 'SELECT * FROM ' . $this->joomdb['cat'] . ' WHERE cid = '.$ponycatg->cid;
      $result = $this->Joom_Migrate_DBOP($query);
      $joomcatg = mysql_fetch_object($result['result']);

      $pic_migrate=true;

      while($pic_migrate){
        //get pics from ponygallery cid=catid
        $query = 'SELECT * FROM ' . $this->ponydb['main'] . ' WHERE catid = ' . $ponycatg->cid . ' LIMIT '.$limitpics;
        $result = $this->Joom_Migrate_DBOP($query);

        $pic_count = mysql_num_rows($result['result']);

        //if no pics available, all pics moved or nothing there
        //delete pony category from db
        if ($pic_count==0) {
          $this->Joom_Migrate_WriteLogfile ("no more pictures in category");
          $query = 'DELETE FROM ' . $this->ponydb['cat'] . ' WHERE cid=' . $ponycatg->cid;
          $result = $this->Joom_Migrate_DBOP($query);
          $this->Joom_Migrate_WriteLogfile ("Pony Category deleted, id: ".$ponycatg->cid);

          if ($this->Joom_Migrate_Checktime() == true) {
            $this->Joom_Migrate_WriteLogfile ("next category without refresh");
            continue;
          } else {
            $this->Joom_Migrate_WriteLogfile ("refresh");
            $this->Joom_Migrate_Refresh('continue');
          }
        }

        $this->Joom_Migrate_WriteLogfile ("Migration of ".$pic_count." pictures begins...");
        $pic_counter = 0;
        $pony_pics=array();
        while ($row = mysql_fetch_object($result['result'])) {
          $pony_pics[] = $row;
        }

        foreach ($pony_pics as $ponypic) {
          //SQL BEGIN of transaction
          $query = 'BEGIN';
          $result = $this->Joom_Migrate_DBOP($query);

          //**********************COMMENTS*******************************************//
          //read all the comments for the pic
          $query = 'SELECT * FROM ' . $this->ponydb['comments'] . ' WHERE cmtpic = ' . $ponypic->id;
          $result = $this->Joom_Migrate_DBOP($query);

          $comment_count = mysql_num_rows($result['result']);

          if ($comment_count != 0) {
            $pony_comments = array();
            while ($row = mysql_fetch_object($result['result'])) {
              $pony_comments[] = $row;
            }
            //for each comment look for the username and search in __users for userid
            foreach ($pony_comments as $ponycomment) {
              $cmt_userid = '0';

              //get the userid from __users = cmtname
              $query = 'Select id FROM ' . $this->prefix . 'users WHERE username = "' . $ponycomment->cmtname.'"';
              $result = $this->Joom_Migrate_DBOP($query);

              //if success, otherwise remain '0' (no fatal error)
              if (mysql_num_rows($result['result']) != 0) {
                $row = mysql_fetch_row($result['result']);
                $cmt_userid = $row[0];
              }

              //insert in joom
              $query = "INSERT INTO " . $this->joomdb['comments'] .
                  "\n (cmtid,cmtpic,cmtip,userid,cmtname,cmttext,cmtdate,published,approved) "
                  . "\n VALUES("
                  . $ponycomment->cmtid
                  . "\n,"
                  . $ponycomment->cmtpic
                  . "\n,'"
                  . mysql_escape_string($ponycomment->cmtip)
                  . "',\n"
                  . $cmt_userid
                  . ",\n'"
                  . mysql_escape_string($ponycomment->cmtname)
                  . "',\n'"
                  . mysql_escape_string($ponycomment->cmttext)
                  . "',\n'"
                  . mysql_escape_string($ponycomment->cmtdate)
                  . "',\n"
                  . $ponycomment->published
                  . "\n,"
                  . $ponycomment->approved
                  . ")";

              $result = $this->Joom_Migrate_DBOP($query);

            }

            //Delete the ponycomments
            $this->Joom_Migrate_WriteLogfile ($comment_count." comment(s) inserted");
            $query = 'DELETE FROM ' . $this->ponydb['comments'] . ' WHERE cmtpic = ' . $ponypic->id;
            $result = $this->Joom_Migrate_DBOP($query);
          }

          //**********************VOTES**********************************************//
          //look for pic in ponyvotes and insert them in joomvotes, delete the ponyrows
          $query = 'INSERT INTO ' . $this->joomdb['votes'] .
              ' (voteid,picid,userid,userip,datevoted,timevoted,vote) SELECT voteid,picid,userid,userip,datevoted,timevoted,vote FROM ' .
              $this->ponydb['votes'] . ' WHERE ' . $this->ponydb['votes'] . '.picid = ' . $ponypic->id;

          $result = $this->Joom_Migrate_DBOP($query);
          $db_rows_count=mysql_affected_rows();

          //Delete the ponyvotes if necessary
          if ($db_rows_count > 0) {
            $this->Joom_Migrate_WriteLogfile ($db_rows_count." vote(s) inserted");
            $query = 'DELETE FROM ' . $this->ponydb['votes'] . ' WHERE picid = ' . $ponypic->id;
            $result = $this->Joom_Migrate_DBOP($query);
          }

          //**********************NAMETAGS**********************************************//
          //look for entries in nameshields and insert them in joomnameshields, delete the rows
          $query = 'INSERT INTO ' . $this->joomdb['nameshields'] .
              ' (nid,npicid,nuserid,nxvalue,nyvalue,nuserip,ndate,nzindex) SELECT nid,npicid,nuserid,nxvalue,nyvalue,nuserip,ndate,nzindex FROM ' .
              $this->ponydb['nameshields'] . ' WHERE ' . $this->ponydb['nameshields'] . '.npicid = ' . $ponypic->id;

          $result = $this->Joom_Migrate_DBOP($query);
          $db_rows_count=mysql_affected_rows();

          //Delete the ponynameshields if necessary
          if ( $db_rows_count > 0) {
            $this->Joom_Migrate_WriteLogfile ($db_rows_count." nameshield(s) inserted");
            $query = 'DELETE FROM ' . $this->ponydb['nameshields'] . ' WHERE npicid = ' . $ponypic->id;
            $result = $this->Joom_Migrate_DBOP($query);
          }

          //get the userid from #__users = username
          $query = 'Select id FROM ' . $this->prefix . 'users WHERE username = "' . $ponypic->owner.'"';
          $result = $this->Joom_Migrate_DBOP($query);

          if (mysql_num_rows($result['result'])==0) {
            $this->Joom_Migrate_WriteLogfile ("DB Error: User: '".$ponypic->owner."' not exists. Migration of Picture '".$ponypic->imgtitle."' aborted (Rollback)");
            $query = 'ROLLBACK';
            $result = $this->Joom_Migrate_DBOP($query);
            continue; //next picture
          }

          $userid = mysql_fetch_row($result['result']);

          //insert pic in joomgallery
          $query = "INSERT INTO " . $this->joomdb['main'] .
              "\n (id,catid,imgtitle,imgauthor,imgtext,imgdate,imgcounter,imgvotes,imgvotesum,published,imgfilename,
              imgthumbname,checked_out,owner,approved,useruploaded,ordering) "
              . "\n VALUES("
              .$ponypic->id
              . "\n,"
              . $ponypic->catid
              . "\n,'"
              . mysql_escape_string($ponypic->imgtitle)
              . "',\n'"
              . mysql_escape_string($ponypic->imgauthor)
              . "',\n'"
              . mysql_escape_string($ponypic->imgtext)
              . "',\n"
              . $ponypic->imgdate
              . "\n,"
              . $ponypic->imgcounter
              . "\n,"
              . $ponypic->imgvotes
              . "\n,"
              . $ponypic->imgvotesum
              . "\n,"
              . $ponypic->published
              . "\n,'"
              . mysql_escape_string($ponypic->imgfilename)
              . "',\n'"
              . mysql_escape_string($ponypic->imgthumbname)
              . "',\n"
              . $ponypic->checked_out
              . ","
              . $userid[0]
              . ","
              . $ponypic->approved
              . ","
              . $ponypic->useruploaded
              . ","
              . $ponypic->ordering
              . ")";
          $result = $this->Joom_Migrate_DBOP($query);

          //delete the pic from the ponydb
          $query = 'DELETE FROM ' . $this->ponydb['main'] . ' WHERE id = ' . $ponypic->id;
          $result = $this->Joom_Migrate_DBOP($query);

          //SQL End of transaction
          $query = 'COMMIT';
          $result = $this->Joom_Migrate_DBOP($query);

          //move the pic's in the three directories by renaming
          $this->Joom_Migrate_Rename($ponypic->imgfilename, $ponypic->imgthumbname, $joomcatg->catpath);

          $pic_counter++;
          $this->Joom_Migrate_WriteLogfile ('Picture migrated in DB: Number: ' . $pic_counter . ' Title: ' . $ponypic->imgtitle);

          //Check the remaining time
          //if false we have to refresh the site, avoiding an timeout because of
          //reaching the limits
          if ($this->Joom_Migrate_Checktime() == false) {
            $this->Joom_Migrate_WriteLogfile ('Refresh time reached before migration of '.$limitpics.' pictures');
            $this->Joom_Migrate_Refresh('continue');
          }
        }//end foreach

        //Work for pics ended
        $this->Joom_Migrate_WriteLogfile ($pic_counter.' Pictures and corresponding Comments/Nameshields/Votes successfully migrated');

        //if less than $limitpics pictures migrated, delete the pony category
        if ($pic_counter < $limitpics ) {
          $query = 'DELETE FROM ' . $this->ponydb['cat'] . ' where cid=' . $ponycatg->cid;
          $result = $this->Joom_Migrate_DBOP($query);
          $this->Joom_Migrate_WriteLogfile ("Pony Category deleted, id: ".$ponycatg->cid);
          $this->Joom_Migrate_WriteLogfile ("*****************************");

          if ($this->Joom_Migrate_Checktime() == true) {
            $this->Joom_Migrate_WriteLogfile ("next category without refresh");
            $pic_migrate=false;
            continue;
          } else {
            $this->Joom_Migrate_WriteLogfile ('Refresh');
            $this->Joom_Migrate_Refresh('continue');
          }
        }

        //continue with migration of pics in the actual category
        if ($this->Joom_Migrate_Checktime() == true) {
          //initialize array with migrated pictures
          $this->Joom_Migrate_WriteLogfile ('next max. '.$limitpics.' Pictures without refresh');
        } else {
          $this->Joom_Migrate_WriteLogfile ('Refresh');
          $this->Joom_Migrate_Refresh('continue');
        }
      } //while $pic_migrate
    } //while $cat_migrate

    //end of migration
    $this->Joom_Migrate_WriteLogfile ("end of migration - exiting");
    $this->Joom_Migrate_WriteLogfile ("*****************************");
    $this->Joom_Migrate_CloseLogfile();
    $this->Joom_Migrate_Refresh('exit');
  }

/**
 * Move the pictures with PHP rename
 *
 * @param string $sourcefilename  picture from PonyGallery ML
 * @param string $sourcethumbname picture name for destination in JoomGallery
 * @param string $destcatpath     path for destination in JoomGallery
 */
  function Joom_Migrate_Rename($sourcefilename, $sourcethumbname, $destcatpath) {
    jimport('joomla.filesystem.file');
    //Originals
    if (file_exists($this->galvars['pgorig'] . DS . $sourcefilename)) {
      $result = JFile::move($this->galvars['pgorig'] . DS . $sourcefilename, $this->galvars['jgorig'] . DS .$destcatpath . DS . $sourcefilename);
      if (!$result) {
        var_dump($result);
        return false;
      }
    }
    //Details
    $result = JFile::move($this->galvars['pgimgpic'] . DS . $sourcefilename, $this->galvars['jgimgpic'] . DS .$destcatpath . DS . $sourcefilename);
    if (!$result) {
      var_dump($result);
      return false;
    }
    //Thumbs
    $result = JFile::move($this->galvars['pgthumb'] . DS . $sourcethumbname, $this->galvars['jgthumb'] . DS .$destcatpath . DS . $sourcethumbname);
    if (!$result) {
      var_dump($result);
      return false;
    }
    $this->Joom_Migrate_WriteLogfile ("'".$sourcethumbname."' : Image-Files moved");
  }

/**
 * Checks the remaining time of actual migration step
 *
 * @return bool true=time remaining for migration false=no more time left
 */
  function Joom_Migrate_Checktime() {
    $timeleft=-(time() - $this->starttime - $this->maxtime);
    if ($timeleft > 0) {
      return true;
    } else {
      return false;
    }
  }

/**
 * make a redirect to continue/end migration
 *
 * @param string $action redirect to continue or end
 */
  function Joom_Migrate_Refresh($action) {
    $mainframe = & JFactory::getApplication('administrator');
    if ($action == 'continue') {
      $scriptname = 'index.php?option='._JOOM_OPTION.'&act=migrate&migration=p2j&migration_action=continue';
      $mosmsg = '';
      $this->Joom_Migrate_WriteLogfile ("Refresh to continue the migration");
    } else {
      $scriptname = 'index.php?option='._JOOM_OPTION;
      $mosmsg = 'Migration successfully ended';
      $this->Joom_Migrate_WriteLogfile ('Migration ended');
    }
    $this->Joom_Migrate_CloseLogfile();
    $mainframe->redirect($scriptname,$mosmsg);
  }

/**
 * Make DB queries
 *
 * @param string $query
 * @return array (result,error)
 */
  function Joom_Migrate_DBOP($query) {
    $dbop['result'] = mysql_query($query);
    $dbop['error'] = mysql_error();
    return $dbop;
  }

/**
 * Shows the result of checking the migration requirements
 * if all fullfilled show the Button to start the migration
 *
 * @param string $action true=migration can be started
 * @param string $status
 * @param string $result
 */
  function Joom_Migrate_HTML($action, $status, $result) {
    echo "<script language = \"javascript\" type = \"text/javascript\">\n";
    echo "<!--\n";
    echo "function submitbutton(pressbutton) {\n";
    echo "  var form = document.adminForm;\n";
    echo "  if (pressbutton == 'cpanel') {\n";
    echo "    location.href = \"index.php?option="._JOOM_OPTION."\";\n";
    echo "  }\n";
    echo "}\n";
    echo "//-->\n";
    echo "</script>\n";
    echo "</form>";
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
  <tr>
    <th colspan="3" align="center">
      <?php echo JText::_('JGA_MIGRATION_CHECK'); ?>
    </th>
  </tr>
  <tr><?php echo $status; ?></tr>
  <tr>
    <td colspan="3">
      <hr /><?php echo $result; ?>
      <hr />
    </td>
  </tr>
  <tr>
<?php
  //
  if ($action == true) {
?>
    <th colspan="3" style="text-align:center;">
      <form action="index.php?option=<?php echo _JOOM_OPTION; ?>&amp;act=migrate" method="post">
        <input type="hidden" name="migration" value="p2j">
        <input type="hidden" name="migration_action" value="start" />
        <input type="submit" value="<?php echo JText::_('JGA_MIGRATION_STARTMIGRATION'); ?>" style="width: 100px" />
      </form>
      <hr />
    </th>
  <?php
    }
?>
  </tr>
</table>
<?php
  }


/**
 * replace special chars of catname of PonyGallery ML
 *
 * @param string  $text  catname
 * @return string modified catname
 */
  function Joom_Migrate_FixCatname ($text) {
    $text = trim($text);
    if($text != "") {
      $text = strip_tags($text);
      $search = array("/\s/","/ä/","/ö/","/ü/","/Ä/","/Ö/","/Ü/","/ß/");
      $replace = array("_","ae","oe","ue","Ae","Oe","Ue","ss");
      $text = preg_replace($search, $replace, $text);
      $text = strtolower ($text);
      $text= preg_replace("/[^a-z0-9_]/","",$text);
    }
    return $text;
  }

/**
 * Opens the logfile
 *
 * @param string $openmode a=append, otherwise new file
 */
  function Joom_Migrate_OpenLogfile($openmode="a") {
    $logfile = JPATH_COMPONENT.DS.'adminclasses'.DS.$this->logfilename;
    $this->logfile = fopen($logfile, $openmode);
    $this->Joom_Migrate_WriteLogfile("Migration Step started");
  }

/**
 * Close logfile
 *
 */
  function Joom_Migrate_CloseLogfile() {
    fclose($this->logfile);
  }

  /**
   * write into logfile
   *
   * @param string $line
   */
  function Joom_Migrate_WriteLogfile($line) {
    $timestring = strftime("%Y-%m-%d %H:%M:%S", time());
    fwrite($this->logfile,$timestring." - ".$line."\n");
  }
}

if(isset($show_jmtablerow)){
?>
          <tr>
            <td>
              <h4><?php echo JText::sprintf('JGA_CHECKMIGRATION', 'PonyGallery ML'); ?></h4>
            </td>
            <td align="center">
              <form action="index.php?option=<?php echo _JOOM_OPTION; ?>&amp;act=migrate" method="post">
                <input type="hidden" name="migration" value="p2j" />
                <input type="hidden" name="migration_action" value="check" />
                <input type="submit" value="<?php echo JText::_('JGA_MIGRATION_CHECKMIGRATION'); ?>" style="width:100px" />
              </form>
            </td>
          </tr>
<?php
}
