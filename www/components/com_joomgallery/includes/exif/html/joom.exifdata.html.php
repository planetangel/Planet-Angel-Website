<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/exif/html/joom.exifdata.html.php $
// $Id: joom.exifdata.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_Exif
{

  function Joom_ShowExifData_HTML($exif_array)
  {
    global $exif_config_array;
    $config = Joom_getConfig();

    require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'adminexif'.DS.'admin.exifarray.php');
    $ii = 0;
?>
  <div class="jg_exif">
    <div class="sectiontableheader">
      <h4 <?php echo $config->jg_showdetailaccordion?"class=\"joomgallery-toggler\"":"class=\"joomgallery-notoggler\""; ?>>
        <?php echo JText::_('JGSE_DATA'); ?> 
      </h4>
    </div>
    <div <?php echo $config->jg_showdetailaccordion ? "class=\"joomgallery-slider\"" : ""; ?>>
      <p>
<?php
    $ifdotags        = explode (',', $config->jg_ifdotags);
    $subifdtags      = explode (',', $config->jg_subifdtags);
    $gpstags         = explode (',', $config->jg_gpstags);
    $countifdotags   = count($ifdotags);
    $countsubifdtags = count($subifdtags);
    $countgpstags    = count($gpstags);

    $definitions = array(
    1 => array ('TAG' => "IFD0", 'FORS' => $ifdotags,   'FOR' => '$ifdotag'),
    2 => array ('TAG' => "EXIF", 'FORS' => $subifdtags, 'FOR' => '$subifdtag'),
    3 => array ('TAG' => "GPS",  'FORS' => $gpstags,    'FOR' => '$gpstags')
    );
    $count  = count($definitions);
    $output = '';

    for($ii=1; $ii <= $count; $ii++)
    {
      $tagcat   = $definitions[$ii]['TAG'];
      $jgtags   = $definitions[$ii]['FORS'];
      $jgtag    = $definitions[$ii]['FOR'];

      $k = 0;
      foreach($jgtags as $jgtag)
      {
        if(!empty($jgtag) && isset($exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']]))
        {
          $kk      = $k%2+1;
          $tagdata = $exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']];
          $output .= "      <div class=\"sectiontableentry".$kk."\">\n";
          $output .= "        <div class=\"jg_exif_left\">\n";
//           $output .= "        ".$jgtag."\n";
//           $output .= "        &nbsp;\n";
          $output .= "          ".$exif_config_array[$tagcat][$jgtag]['Name']."\n";
          $output .= "        </div>\n";
          $output .= "        <div class=\"jg_exif_right\">\n";
          if($exif_config_array[$tagcat][$jgtag]['Calculation'] =='Denum')
          {
            list($numerator, $denumerator) = explode("/", $tagdata);
            $tagdata = ($numerator/$denumerator);
            $tagdata = round($tagdata,2);
            if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'FNumber')
            {
              $tagdata = JText::_('JGSE_SUBIFD_FNUMBER_F').$tagdata;
            }
          }
          if($exif_config_array[$tagcat][$jgtag]['Calculation'] == 'Array')
          {
            $tagdata = $exif_config_array[$tagcat][$jgtag][$exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']]];
          }
          if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'ImageDescription' 
             || $exif_config_array[$tagcat][$jgtag]['Attribute'] == 'Artist' 
             || $exif_config_array[$tagcat][$jgtag]['Attribute'] == 'Copyright')
          {
            $tagdata = $exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']];
            $from_charset = 'ASCII';
            $to_charset   = 'UTF-8';
            if(function_exists('iconv'))
            {
              $fixedenteties = htmlentities($tagdata);
              $fixedcharset  = iconv($from_charset, $to_charset, $fixedenteties);
            }
            else
            {
              $fixedcharset = $tagdata;
            }
            if(!Joom_IsUtf8($fixedcharset))
            {
              $tagdata = htmlspecialchars_decode(Joom_Utf8EncodeMix($fixedcharset, false));
            }
            else
            {
              $tagdata =  htmlspecialchars_decode($fixedcharset);
            }
          }
          if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'ReferenceBlackWhite'
             || $exif_config_array[$tagcat][$jgtag]['Attribute'] == 'PrimaryChromaticities'
             || $exif_config_array[$tagcat][$jgtag]['Attribute'] == 'WhitePoint'
             || $exif_config_array[$tagcat][$jgtag]['Attribute'] == 'YCbCrCoefficients'
            )
          {
            if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'WhitePoint')
            {
              $arraynum = 2;
              $counter  = 1;
            }
            elseif($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'YCbCrCoefficients')
            { 
              $arraynum = 3;
              $counter  = 2;
            }
            else
            {
              $arraynum = 6;
              $counter  = 5;
            }
            $tagdata  = "[";
            for($num = 0; $num < $arraynum; $num++)
            {
              $data = $exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']][$num];
              list($numerator, $denumerator) = explode("/", $data);
              $data = ($numerator/$denumerator);
              $tagdata .= $data;
              if($num < $counter)
              {
                $tagdata .= ", ";
              }
            }
            $tagdata .= "]";
          }
          if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'ExifVersion')
          {
            if($exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']] == '0220')
            {
              $tagdata  = JText::_('JGSE_SUBIFD_EXIFVERSION_VERSION') . ' 2.2';
            }
            elseif($exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']] == '0210')
            {
              $tagdata  = JText::_('JGSE_SUBIFD_EXIFVERSION_VERSION') . ' 2.1';
            }
          }
          if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'ComponentsConfiguration')
          {
            $tagdata = '';
            for($num = 0; $num < 4; $num++ )
            {
              $value = ord($exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']]{$num});
              $tagdata .= JText::_('JGSE_SUBIFD_COMPONENTSCONFIGURATION_COMPONENT') . ( $num + 1 ) . ': ';
              switch($value)
              {
                case 0:
                  $tagdata .= JText::_('JGSE_SUBIFD_COMPONENTSCONFIGURATION_0');
                  break;
                case 1:
                  $tagdata .= JText::_('JGSE_SUBIFD_COMPONENTSCONFIGURATION_1');
                  break;
                case 2:
                  $tagdata .= JText::_('JGSE_SUBIFD_COMPONENTSCONFIGURATION_2');
                  break;
                case 3:
                  $tagdata .= JText::_('JGSE_SUBIFD_COMPONENTSCONFIGURATION_3');
                  break;
                case 4:
                  $tagdata .= JText::_('JGSE_SUBIFD_COMPONENTSCONFIGURATION_4');
                  break;
                case 5:
                  $tagdata .= JText::_('JGSE_SUBIFD_COMPONENTSCONFIGURATION_5');
                  break;
                case 6:
                  $tagdata .= JText::_('JGSE_SUBIFD_COMPONENTSCONFIGURATION_6');
                  break;
                default:
                  $tagdata .= JText::_('JGSE_SUBIFD_COMPONENTSCONFIGURATION_UNKNOWN') . $value;
              }
              $tagdata .= '<br />';
            }
          }
          if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'FileSource')
          {
            $tagdata = '';
            $value = ord($exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']]{0});
            switch($value)
            {
              case 3:
                $tagdata .= JText::_('JGSE_SUBIFD_FILESOURCE_3');
              break;
            default:
              $tagdata = JText::_('JGSE_SUBIFD_FILESOURCE_UNKNOWN') . $value;
            }
          }
          if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'SceneType')
          {
            $tagdata = '';
            $value = ord($exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']]{0});
            switch($value)
            {
              case 1:
                $tagdata .= JText::_('JGSE_SUBIFD_SCENETYPE_1');
              break;
            default:
              $tagdata = JText::_('JGSE_SUBIFD_SCENETYPE_UNKNOWN') . $value;
            }
          }
          if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'GPSLatitudeRef')
          {
            $tagdata = '';
            $value = $exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']];
            switch($value)
            {
              case 'N':
                $tagdata .= JText::_('JGSE_GPS_GPSLATITUDEREF_N');
              break;
              case 'S':
                $tagdata .= JText::_('JGSE_GPS_GPSLATITUDEREF_S');
              break;
            }
          }
          if($exif_config_array[$tagcat][$jgtag]['Calculation'] == 'DegMinSec')
          {
            $tagdata  = '';
            $degree   = $exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']][0];
            list($numerator,$denumerator) = explode("/",$degree);
            $degree   = ($numerator/$denumerator);
            $tagdata .= $degree."&deg;";
            $tagdata .= "&nbsp;&nbsp;";
            $minutes  = $exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']][1];
            list($numerator,$denumerator) = explode("/",$minutes);
            $minutes  = ($numerator/$denumerator);
            $tagdata .= $minutes."'";
            $tagdata .= "&nbsp;&nbsp;";
            $seconds  = $exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']][2];
            list($numerator,$denumerator) = explode("/",$seconds);
            $seconds  = ($numerator/$denumerator);
            $tagdata .= $seconds."''";
          }
          if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'GPSLongitudeRef')
          {
            $tagdata  = '';
            $value = $exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']];
            switch($value)
            {
              case 'E':
                $tagdata .= JText::_('JGSE_GPS_GPSLONGITUDEREF_E');
              break;
              case 'W':
                $tagdata .= JText::_('JGSE_GPS_GPSLONGITUDEREF_W');
              break;
            }
          }
          if($exif_config_array[$tagcat][$jgtag]['Attribute'] == 'GPSAltitudeRef')
          {
            $tagdata = "";
            $value = $exif_array[$tagcat][$exif_config_array[$tagcat][$jgtag]['Attribute']]{0};
            $value = bindec($value);
            switch($value)
            {
              case '0':
                $tagdata .= JText::_('JGSE_GPS_GPSALTITUDEREF_0');
              break;
              case '1':
                $tagdata .= JText::_('JGSE_GPS_GPSALTITUDEREF_1');
              break;
            }
          }

          if($tagdata == '') {
            $tagdata = '&nbsp;';
          }

          $tagdata = str_replace('&Acirc;', '', $tagdata);

          $output .= '          '.$tagdata.'';

          if($exif_config_array[$tagcat][$jgtag]['Units'] != '')
          {
            $output .= "&nbsp;";
            $output .= "".$exif_config_array[$tagcat][$jgtag]['Units']."\n";
          }
          else
          {
            $output .= "\n";
          }
          $output .= "        </div>\n";
          $output .= "      </div>\n";
          $k++;
        }
//           else {
//           $kk = $k%2+1;
//           $output .= "    <div class=\"sectiontableentry".$kk."\">\n";
//           $output .= "      <div class=\"jg_exif_left\">\n";
//           $output .= "        ".$jgtag."\n";
//           $output .= "        &nbsp;\n";
//           $output .= "        ".$exif_config_array[$tagcat][$jgtag]['Name']."\n";
//           $output .= "      </div>\n";
//           $output .= "      <div class=\"jg_exif_right\">\n";
//           $output .= "        nicht definiert";
//           $output .= "      </div>\n";
//           $output .= "    </div>\n";
//           $k++;
//         }
      }
    }
    echo $output;
?>
  &nbsp;</p>
    </div>
  </div>
<?php
  }//End function Joom_ShowExifData_HTML

}//End class HTML_Joom_Exif
?>
