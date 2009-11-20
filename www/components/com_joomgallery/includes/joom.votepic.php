<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.votepic.php $
// $Id: joom.votepic.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

//Check for hacking attempt
$query = "  SELECT
              COUNT(id)
            FROM
              #__joomgallery AS a
            LEFT JOIN
              #__joomgallery_catg AS c ON c.cid=a.catid
            WHERE
                 a.published = '1'
              AND a.approved = '1'
              AND a.id       = '$id'
              AND c.access  <= '".$user->get('aid')."'
          ";

$database->setQuery($query);
$result = $database->loadResult();

if($result != 1)
{
  die('Stop Hacking attempt!');
}

//check if vote was manipulated with modifying the HTML code
if ($imgvote < 1 || $imgvote > $config->jg_maxvoting)
{
  die('Stop Hacking attempt!');
}


$ip   = getenv('REMOTE_ADDR');
$date = date('Y-m-d');
$time = time ();

if($config->jg_onlyreguservotes && $user->get('aid') > 0 )
{
  // get voted or not
  $query = "  SELECT
                *
              FROM
                #__joomgallery_votes
              WHERE
                   userid = '".$user->get('id')."'
                AND picid = '$id'
            ";
  $database->setQuery($query);
  $rows = $database->loadObjectList();

  // vote or print error
  if(count($rows) >= 1)
  {
    # Print Error and Get back to details page
    $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&func=detail&id='.$id._JOOM_ITEMID, false),
                                   JText::_('JGS_ALERT_YOUR_VOTE_NOT_COUNTED'));
  }
}

if( ($config->jg_onlyreguservotes && $user->get('aid') > 0 && (count($rows) == 0))
   || !$config->jg_onlyreguservotes
  )
{
  # Get old values from database
  $database->setQuery(" SELECT
                          imgvotes,
                          imgvotesum
                        FROM
                          #__joomgallery
                        WHERE
                          id = '$id'
                      ");
  $result1 = $database->LoadRow();
  list($imgvotes, $imgvotesum) = $result1;

  # Recalculate with the new vote
  $imgvotes++;
  $imgvotesum = $imgvotesum + $imgvote;
  # Save new values
  $database->setQuery(" UPDATE
                          #__joomgallery
                        SET
                          imgvotes   = '$imgvotes',
                          imgvotesum = '$imgvotesum'
                        WHERE
                          id = '$id'
                      ");
  $database->query();

  // store log of vote:
  $query = "  INSERT INTO
                #__joomgallery_votes
              VALUES(
                '',
                '$id',
                '".$user->get('id')."',
                '$ip',
                '$date',
                '$time',
                '$imgvote')
            ";
  $database->setQuery($query);
  $database->query();

  # Get back to details page
  $mainframe->redirect(JRoute::_('index.php?option=com_joomgallery&func=detail&id='.$id._JOOM_ITEMID, false),
                                 JText::_('JGS_ALERT_YOUR_VOTE_COUNTED'));
}
?>
