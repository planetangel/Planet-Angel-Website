<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/html/joom.viewspecial.html.php $
// $Id: joom.viewspecial.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_Specials
{


  function Joom_ShowSpecials_HTML($tl_title, $rows, $sorting)
  {
    $config   = Joom_getConfig();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    $num_rows = ceil(count($rows ) / $config->jg_toplistcols);
    $index    = 0;
    $line     = 1;

?>
  <div class="jg_topview">
    <div class="sectiontableheader">
      <?php echo $tl_title; ?>&nbsp;
    </div>
<?php
  $count_rows = count($rows);
  if($count_rows)
  {
    for($row_count=0; $row_count < $num_rows; $row_count++ )
    {
      $line++;
      $linecolor = ($line % 2) + 1;
?>
    <div class="jg_row <?php if ($linecolor == 1) echo "sectiontableentry1"; else echo "sectiontableentry2";?>">
<?php
      for($col_count = 0; ($col_count < $config->jg_toplistcols) && ($index < $count_rows); $col_count++)
      {
        $row1 = $rows[$index];
?>
      <div class="jg_topelement">
<?php
        $catpath = Joom_GetCatPath($row1->catid);
        if( ($config->jg_showdetailpage == 0 && $user->get('aid')!= 0) 
           || $config->jg_showdetailpage == 1
          )
        {
         $link = Joom_OpenImage($config->jg_detailpic_open, $row1->id, $catpath, $row1->catid, $row1->imgfilename, $row1->imgtitle, $row1->imgtext);
        }
        else
        {
          $link = "javascript:alert('".JText::_('JGS_ALERT_NO_DETAILVIEW_FOR_GUESTS',true)."')";
        }
?>
        <div  class="jg_topelem_photo">
          <a href="<?php echo $link; ?>">
            <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row1->imgthumbname; ?>" class="jg_photo" alt="<?php echo $row1->imgtitle; ?>" />
          </a>
        </div>
        <div class="jg_topelem_txt">
          <ul>
            <li>
              <b><?php echo $row1->imgtitle; ?></b>
            </li>
            <li>
              <?php echo JText::_('JGS_CATEGORY').':'; ?>
              <a href="<?php echo JRoute::_('index.php?option=com_joomgallery&func=viewcategory&catid='.$row1->catid._JOOM_ITEMID); ?>">
                <?php echo $row1->name; ?>
              </a>
            </li>
<?php
          if($config->jg_showauthor)
          {
            if($row1->imgauthor)
            {
              $authorowner = $row1->imgauthor;
            }
            elseif($config->jg_showowner)
            {
              $authorowner = Joom_GetDisplayName($row1->owner);
            }
            else
            {
              $authorowner = JText::_('JGS_NO_DATA');
            }
?>
            <li>
              <?php echo JText::_('JGS_AUTHOR').': '.$authorowner; ?>&nbsp;
            </li>
<?php
          }

          if($config->jg_showhits)
          {
?>
            <li>
              <?php echo JText::_('JGS_HITS').': '.$row1->imgcounter; ?>&nbsp;
            </li>
<?php
          }
          if($config->jg_showcatrate)
          {
?>
            <li>
<?php
            if($row1->imgvotes > 0)
            {
              $fimgvotesum = number_format( $row1->imgvotesum / $row1->imgvotes, 2, ',', '' );
              if($row1->imgvotes == 1)
              {
                $frating = $fimgvotesum.' ('.$row1->imgvotes.' '.JText::_('JGS_ONE_VOTE').')';
              }
              else
              {
                $frating = $fimgvotesum.' ('.$row1->imgvotes.' '.JText::_('JGS_VOTES').')';
              }
            }
            else
            {
              $frating = '('.JText::_('JGS_NO_RATINGS').')';
            }
?>
              <?php echo JText::_('JGS_RATING').': '.$frating; ?>
            </li>
<?php
          }
          if($config->jg_showcatcom)
          {
            # Check how many comments exist
            $database->setQuery(" SELECT 
                                    COUNT(*)
                                  FROM 
                                    #__joomgallery_comments
                                  WHERE 
                                           cmtpic = '$row1->id' 
                                    AND approved  = '1' 
                                    AND published = '1'
                                ");
            $comments = $database->LoadResult();
?>
            <li>
<?php
          switch($comments)
          {
          case 0:
?>
              <?php echo JText::_('JGS_NO_COMMENTS'); ?>&nbsp;
<?php
            break;
          case 1:
?>
              <?php echo $comments.' '.JText::_('JGS_COMMENT'); ?>&nbsp;
<?php
            break;
          default:
?>
              <?php echo $comments.' '.JText::_('JGS_COMMENTS'); ?>&nbsp;
<?php
           break;
          }
?>
            </li>
<?php
            if($sorting == 'lastcomment' && $config->jg_showthiscomment)
            {
              for($ii=0; $ii < $comments; $ii++)
              {
                $userid  = $row1->userid;
                $cmtname = $row1->cmtname;
                if($userid > 0)
                {
                   $cmtname = $row1->username;
                }
                $cmttext = $row1->cmttext;
                $cmtdate = $row1->cmtdate;
                #$cmtdate = strftime( "%d-%m-%Y %H:%M:%S", $cmtdate );
                $cmtdate = strftime($config->jg_dateformat,$cmtdate);
              }
?>
            <li>
<?php
              if($userid > 0)
              {
?>
			        <?php echo Joom_GetDisplayName($userid, false); ?>
<?php
              }
              else
              {
                echo $cmtname;
              }

              echo ' '.JText::_('JGS_WROTE').' ('.JText::_('JGS_AT').' '. $cmtdate.'):';
              $cmttext = Joom_ProcessText($cmttext);
              if($config->jg_smiliesupport)
              {
                $smileys = Joom_GetSmileys();
                foreach($smileys as $i => $sm)
                {
                  $cmttext = str_replace($i, '<img src="'.$sm.'" border="0" alt="'.$i.'" title="'.$i.'" />', $cmttext);
                }
              }
?>
              <?php echo stripslashes($cmttext); ?>&nbsp;
            </li>
<?php
            }
          }
?>
          </ul>
        </div>
      </div>
<?php
      $index++;
      }
?>
      <div class="jg_clearboth"></div>
    </div>
<?php
      }
    }
?>
  </div>
<?php
  }//End function Joom_ShowSpecials_HTML

}//End class HTML_Joom_Specials
?>
