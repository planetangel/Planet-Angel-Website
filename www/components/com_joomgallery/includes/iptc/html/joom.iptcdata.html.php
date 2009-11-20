<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.0/JG/trunk/components/com_joomgallery/includes/iptc/html/joom.iptcdata.html.php $
// $Id: joom.iptcdata.html.php 801 2008-12-15 22:05:37Z mab $
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

class HTML_Joom_Iptc
{

  function Joom_ShowIptcData_HTML($iptc_array)
  {
    global $iptc_config_array;
    $config = Joom_getConfig();

    require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'adminiptc'.DS.'admin.iptcarray.php');
    $ii = 0;
?>
  <div class="jg_exif">
    <div class="sectiontableheader">
      <h4 <?php echo $config->jg_showdetailaccordion ? "class=\"joomgallery-toggler\"" : "class=\"joomgallery-notoggler\""; ?>>
        <?php echo JText::_('JGSI_DATA'); ?> 
      </h4>
    </div>
    <div <?php echo $config->jg_showdetailaccordion ? "class=\"joomgallery-slider\"" : ""; ?>>
      <p>
<?php
//var_dump ($iptc_array);
    $iptctags     = explode (',', $config->jg_iptctags);

    $charsets = array(
                      'macintosh',
                      'ASCII',
                      'ISO-8859-1',
                      'UCS-4',
                      'UCS-4BE',
                      'UCS-4LE',
                      'UCS-2',
                      'UCS-2BE',
                      'UCS-2LE',
                      'UTF-32',
                      'UTF-32BE',
                      'UTF-32LE',
                      'UTF-16',
                      'UTF-16BE',
                      'UTF-16LE',
                      'UTF-7',
                      'UTF7-IMAP',
                      'UTF-8',
                      'EUC-JP',
                      'SJIS',
                      'eucJP-win',
                      'SJIS-win',
                      'ISO-2022-JP',
                      'JIS',
                      'ISO-8859-2',
                      'ISO-8859-3',
                      'ISO-8859-4',
                      'ISO-8859-5',
                      'ISO-8859-6',
                      'ISO-8859-7',
                      'ISO-8859-8',
                      'ISO-8859-9',
                      'ISO-8859-10',
                      'ISO-8859-13',
                      'ISO-8859-14',
                      'ISO-8859-15',
                      'byte2be',
                      'byte2le',
                      'byte4be',
                      'byte4le',
                      'BASE64',
                      '7bit',
                      '8bit',
                      'EUC-CN',
                      'CP936',
                      'HZ',
                      'EUC-TW',
                      'CP950',
                      'BIG-5',
                      'EUC-KR',
                      'UHC',
                      'ISO-2022-KR',
                      'Windows-1251',
                      'Windows-1252',
                      'CP866',
                      'KOI8-R'
                      );

    if((isset($iptc_array['1#090'][0])) && in_array($charsets, $iptc_array['1#090']))
    {
      $from_charset = $iptc_array['1#090'][0];
    }
    else
    {
      $from_charset = '';
    }

    $to_charset = 'UTF-8';

    $k = 0;
    $output = '';
    foreach($iptctags as $iptctag)
    {
      $realiptctag = str_replace(":", "#",$iptc_config_array['IPTC'][$iptctag]['IMM']);
      if(isset($iptc_array[$realiptctag]))
      {
        $kk = $k%2+1;
        $output .= "      <div class=\"sectiontableentry".$kk."\">\n";
        $output .= "        <div class=\"jg_exif_left\">\n";
        $output .= "          ".$iptc_config_array['IPTC'][$iptctag]['Name']."\n";
        $output .= "        </div>\n";
        $output .= "        <div class=\"jg_exif_right\">\n";
        if(function_exists('iconv'))
        {
          $fixedenteties = htmlentities($iptc_array[$realiptctag][0]);
          $fixedcharset = iconv($from_charset, $to_charset, $fixedenteties);
        }
        else
        {
          $fixedcharset = $iptc_array[$realiptctag][0];
        }
        if(!Joom_IsUtf8($fixedcharset))
        {
          $tagdata = htmlspecialchars_decode(Joom_Utf8EncodeMix($fixedcharset, false));
        }
        else
        {
          $tagdata =  htmlspecialchars_decode($fixedcharset);
        }
        if($tagdata == '')
        {
          $tagdata = "&nbsp;";
        }
        $output .= "          ".$tagdata."";
        $output .= "        </div>\n";
        $output .= "      </div>\n";
        $k++;
        if($realiptctag == '2#025')
        {
          $num = count($iptc_array['2#025']);
          if($num > 1)
          {
            $i = 1;
            for($keywords = 1; $keywords < $num; $keywords++)
            {
              $kk = $k%2+1;
              $output .= "      <div class=\"sectiontableentry".$kk."\">\n";
              $output .= "        <div class=\"jg_exif_left\">\n";
              $output .= "          ".$iptc_config_array['IPTC'][$iptctag]['Name']." \n";
              $output .= "        </div>\n";
              $output .= "        <div class=\"jg_exif_right\">\n";
              if(function_exists('iconv'))
              {
                $fixedenteties = htmlentities($iptc_array[$realiptctag][$i]);
                $fixedcharset  = iconv($from_charset, $to_charset, $fixedenteties);
              }
              else
              {
                $fixedcharset = $iptc_array[$realiptctag][$i];
              }
              if(!Joom_IsUtf8($fixedcharset))
              {
                $tagdata = htmlspecialchars_decode(utf8_encode_mix($fixedcharset, false));
              }
              else
              {
                $tagdata =  htmlspecialchars_decode($fixedcharset);
              }
              if($tagdata == '')
              {
                $tagdata = "&nbsp;";
              } 
              $output .= "          ".$tagdata."";
              $output .= "        </div>\n";
              $output .= "      </div>\n";
              $k++;
              $i++;
            }
          } 
        }
      }
    }
    echo $output;
?>
  &nbsp;</p>
    </div>
  </div>
<?php
  }//End function Joom_ShowIptcData_HTML

}//End class HTML_Joom_Iptc
?>
