<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/includes/html/admin.help.html.php $
// $Id: admin.help.html.php 449 2009-06-14 11:57:04Z aha $
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

class HTML_Joom_AdminHelp {

  /**
   * Help and credits
   *
   * @return HTML_Joom_AdminHelp
   */
  function HTML_Joom_AdminHelp() {

    //cpanel
    echo "<script language = \"javascript\" type = \"text/javascript\">\n";
    echo "function submitbutton(pressbutton) {\n";
    echo "  var form = document.adminForm;\n";
    echo "  if (pressbutton == 'cpanel') {\n";
    echo "    location.href = \"index.php?option="._JOOM_OPTION."\";\n";
    echo "  }\n";
    echo "}\n";
    echo "</script>\n";
    echo "</form>";

?>
      <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
        <tr>
          <th colspan="2" align="center" bgcolor="#E7E7E7">
            <?php echo JText::_('JGA_HELP_TEAM'); ?>
          </th>
        </tr>
        <tr>
          <td width="50%" align="right">
            <a href="mailto:mab@joomgallery.net">M. Andreas B&ouml;ttcher (aka mab)</a>
          </td>
          <td width="50%" align="left">
            <?php echo JText::_('JGA_HELP_CHIEF').', '.JText::_('JGA_HELP_PROGRAMMING'); ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            <a href="mailto:aha@joomgallery.net">Andreas Hartmann (aka aHa)</a>
          </td>
          <td width="50%" align="left">
            <?php echo JText::_('JGA_HELP_PROGRAMMING'); ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            <a href="mailto:hypnotoad@joomgallery.net">Armin Hornung (aka hypnotoad)</a>
          </td>
          <td width="50%" align="left">
            <?php echo JText::_('JGA_HELP_PROGRAMMING'); ?>
          </td>
        </tr>
        <!--
        <tr>
          <td width="50%" align="right">
            <a href="mailto:djanesch@joomgallery.net">Daniel Janesch (aka deejay_)</a>
          </td>
          <td width="50%" align="left">
            <?php echo JText::_('JGA_HELP_PROGRAMMING'); ?>
          </td>
        </tr>
        -->
        <tr>
          <td width="50%" align="right">
            <a href="mailto:chraneco@joomgallery.net">Patrick Alt (aka chraneco)</a>
          </td>
          <td width="50%" align="left">
            <?php echo JText::_('JGA_HELP_PROGRAMMING'); ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Benjamin Malte Meier (aka b2m)
          </td>
          <td width="50%" align="left">
            <?php echo JText::_('JGA_HELP_ADVISORY').', '.JText::_('JGA_HELP_PROGRAMMING'); ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Dennis Rowedder (aka Wuslon)
          </td>
          <td width="50%" align="left">
            <?php echo JText::_('JGA_HELP_QUALITY'); ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Claudia Engel (aka Claudia E.)
          </td>
          <td width="50%" align="left">
            <?php echo JText::_('JGA_HELP_SUPPORT').', '.JText::_('JGA_HELP_QUALITY'); ?>
          </td>
        </tr>
        <!--
        <tr>
          <td width="50%" align="right">
            Anke Hartmann (aka Anke)
          </td>
          <td width="50%" align="left">
            <?php echo JText::_('JGA_LANGUAGE_COORDINATION'); ?>
          </td>
        </tr>
        -->
        <tr>
          <td width="50%" align="right">
            Ute Keller&nbsp;
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/gb.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
          <td rowspan="19" align="left">
            <?php echo JText::_('JGA_HELP_TRANSLATION'); ?> &sup1;
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            M. Andreas Böttcher&nbsp;
            <a href='http://www.joomgallery.net/downloads/task,doc_details/gid,90/' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/de.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Gerard Westerdijk&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/65-the-netherlands-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/nl.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Hermann Maurer&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/66-the-russian-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/ru.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            baijianpeng&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/59-the-chinese-traditional-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/cn.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Ernesto de la Fuente&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/62-the-spanish-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/es.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Istv&aacute;n Kathagen&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/67-the-hungarian-formal-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/hu.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Pereira Edgar & François-Xavier Duchène&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/63-the-french-language-files-1.5.x.html'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/fr.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Uffe Christoffersen&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/57-the-danish-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/dk.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Joomla Brasil&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/56-the-brazilian-portuguese-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/br.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            retromania&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/64-the-japanese-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/jp.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Arni Skulason&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/70-the-swedish-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/se.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Andrzej Budnik&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/73-the-polish-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/pl.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Antti Värre & Sami Haaranen&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/77-the-finnish-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/fi.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            S.H. Anvari&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/78-the-persian-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/ir.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Andrea Puddu&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/79-the-italian-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/it.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
             Andrius Balsevičius&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/80-the-lithuanian-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/lt.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            designer bt, Mustafa Bayraktar & Kadir Özcan&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/83-the-turkish-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/tr.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Dinko Rizvi&#x107;&nbsp;
            <a href='http://www.en.joomgallery.net/downloads/view-document-details/89-the-bosnian-language-files-1.5.x.html' target='_blank'>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/ba.png"  border="0" align="top" width="16" height="11" alt="" /></a>
          </td>
        </tr>
        <tr>
          <td colspan="2" align="center">
            &sup1; <?php echo JText::_('JGA_HELP_DOWNLOAD_TRANSLATIONS'); ?>
          </td>
        </tr>
        <tr>
          <th colspan="2" align="center" bgcolor="#E7E7E7">
            <?php echo JText::_('JGA_HELP_LINKS'); ?>
          </th>
        </tr>
        <tr>
          <td width="50%" align="right">
            <?php echo JText::_('JGA_HELP_PROJECT_WEBSITE'); ?>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/de.png"  border="0" align="top" width="16" height="11" alt="" /></a>&nbsp;
          </td>
          <td width="50%" align="left">
            <a href='http://www.joomgallery.net/' target='_blank'>http://www.joomgallery.net</a>
          </td>
        </tr>
          <td width="50%" align="right">
            <?php echo JText::_('JGA_HELP_PROJECT_WEBSITE'); ?>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/gb.png"  border="0" align="top" width="16" height="11" alt="" /></a>&nbsp;
          </td>
          <td width="50%" align="left">
            <a href='http://www.en.joomgallery.net/' target='_blank'>http://www.en.joomgallery.net/</a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            <?php echo JText::_('JGA_HELP_SUPPORT_FORUM'); ?>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/de.png"  border="0" align="top" width="16" height="11" alt="" /></a>&nbsp;
          </td>
          <td width="50%" align="left">
            <a href='http://www.joomgallery.net/forum/' target='_blank'>http://www.joomgallery.net/forum/</a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            <?php echo JText::_('JGA_HELP_SUPPORT_FORUM'); ?>
            <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/flags/gb.png"  border="0" align="top" width="16" height="11" alt="" /></a>&nbsp;
          </td>
          <td width="50%" align="left">
            <a href='http://www.forum.en.joomgallery.net/' target='_blank'>http://www.forum.en.joomgallery.net/</a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            <?php echo JText::_('JGA_HELP_PROJECTSITE'); ?>
          </td>
          <td width="50%" align="left">
            <a href='http://joomlacode.org/gf/project/joomgallery/' target='_blank'>http://joomlacode.org/gf/project/joomgallery/</a>
          </td>
        </tr>
        <tr>
          <th colspan="2" align="center" bgcolor="#E7E7E7">
            <?php echo JText::_('JGA_HELP_THANKS'); ?>
          </th>
        </tr>
        <tr>
          <td width="50%" align="right">
            Joomla!
          </td>
          <td width="50%" align="left">
            <a href='http://www.joomla.org' target='_blank'>http://www.joomla.org</a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            CMotion Image Gallery (modified) - Detail view<br />
            Author: Jscheuer1
          </td>
          <td width="50%" align="left">
            <a href='http://www.dynamicdrive.com/dynamicindex4/cmotiongallery.htm' target='_blank'>http://www.dynamicdrive.com/dynamicindex4/cmotiongallery.htm</a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Slimbox (modified) - Detail and Category view<br />
            Author: Christophe Beyls
          </td>
          <td width="50%" align="left">
            <a href='http://www.digitalia.be/software/slimbox' target='_blank'>http://www.digitalia.be/software/slimbox</a>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Thickbox3.1 (modified) - Detail and Category view<br />
            Author: Cody Lindley
          </td>
          <td width="50%" align="left">
            <a href='http://jquery.com/demo/thickbox/' target='_blank'>http://jquery.com/demo/thickbox/</a><br />
            <a href='http://www.codylindley.com' target='_blank'>http://www.codylindley.com</a>
          </td>
        </tr>
<!--
        <tr>
          <td width="50%" align="right">
            Lightbox2 (modified) - Detail and Category view<br />
            Author: Lokesh Dhakar
          </td>
          <td width="50%" align="left">
            <a href='http://www.lokeshdhakar.com/projects/lightbox2/' target='_blank'>http://www.lokeshdhakar.com/projects/lightbox2/</a><br />
          </td>
        </tr>
-->
        <tr>
          <td width="50%" align="right">
            wz_dragdrop.js - Nameshields in Detail view<br />
            Author: Walter Zorn
          </td>
          <td width="50%" align="left">
            <a href='http://www.walterzorn.com/dragdrop/dragdrop_e.htm' target='_blank'>http://www.walterzorn.com/dragdrop/dragdrop_e.htm</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            pngbehavior.htc (PNG in IE 6)<br />
            Author: Erik Arvidsson
          </td>
          <td width="50%" align="left">
            <a href='http://webfx.eae.net' target='_blank'>http://webfx.eae.net</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            ImageMagick<br />
            Author: ImageMagick Studio LLC
          </td>
          <td width="50%" align="left">
            <a href='http://www.imagemagick.org/script/index.php' target='_blank'>http://www.imagemagick.org/script/index.php</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Jupload - Java Applet for uploading<br />
            Author:  Etienne Gauthier
          </td>
          <td width="50%" align="left">
            <a href='http://jupload.sourceforge.net/' target='_blank'>http://jupload.sourceforge.net/</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            EasyCaptcha (Connector to EasyCaptcha in Detailview)<br />
            Author: Easy-Joomla.org Project
          </td>
          <td width="50%" align="left">
            <a href='http://www.easy-joomla.org/' target='_blank'>http://www.easy-joomla.org/</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            recursive creation of directories
          </td>
          <td width="50%" align="left">
            <a href='http://www.developers-guide.net/forums/3134,php-rekursives-erstellen-von-verzeichnissen' target='_blank'>http://www.developers-guide.net/forums/3134,php-rekursives-erstellen-von-verzeichnissen</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            multiple joomla select lists<br />
            Author: ecomeback
          </td>
          <td width="50%" align="left">
            <a href='http://www.joomlaportal.de/505887-post4.html' target='_blank'>http://www.joomlaportal.de/505887-post4.html</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Watermark (modified)<br />
            Author: Michael Mueller
          </td>
          <td width="50%" align="left">
            <a href='http://www.php4u.net' target='_blank'>http://www.php4u.net</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            fastimagecopyresampled (fast conversion of pictures in GD)<br>
            Author: Tim Eckel
          </td>
          <td width="50%" align="left">
            <a href='http://de.php.net/manual/en/function.imagecopyresampled.php#77679' target='_blank'>http://de.php.net/manual/en/function.imagecopyresampled.php#77679</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            TabPane (Backend)<br>
            Author: Erik Arvidsson
          </td>
          <td width="50%" align="left">
            <a href='http://webfx.eae.net/dhtml/tabpane/tabpane.html' target='_blank'>http://webfx.eae.net/dhtml/tabpane/tabpane.html</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            NestedTabs (Backend)<br>
            Author: PerfectDesigningLTD
          </td>
          <td width="50%" align="left">
            <a href='http://www.perfectdesigning.net/development_projects/support/joomla!_tab_system.html'>http://www.perfectdesigning.net/development_projects/support/joomla!_tab_system.html</a><br />
          </td>
        </tr>
        <tr>
          <td width="50%" align="right">
            Wonderful Icons<br />
            Author:  Mark James
          </td>
          <td width="50%" align="left">
            <a href='http://www.famfamfam.com' target='_blank'>http://www.famfamfam.com</a><br />
          </td>
        </tr>
        <tr>
          <th colspan="2" align="center" bgcolor="#E7E7E7">
            <?php echo JText::_('JGA_HELP_DONATIONS'); ?>
          </th>
        </tr>
        <tr>
          <td colspan="2" align="center">
            <?php echo JText::_('JGA_HELP_DONATIONS_LONG'); ?>
            <div style="text-align:center;padding:10px 0;margin:0;vertical-align:middle;">
              <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&amp;business=donate%40joomgallery%2enet&amp;item_name=JoomGallery&amp;tax=0&amp;currency_code=EUR&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8" title="Donate" target="_blank">
              <img src="../administrator/components/<?php echo _JOOM_OPTION; ?>/assets/images/others/donate.gif"  alt="Donate!" title="Donate!" border="0"/></a>
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2" align="center">
            <?php echo JText::_('JGA_HELP_SPONSORS'); ?>
            <a href="mailto:joom@joomgallery.net">joom@joomgallery.net</a>
          </td>
        </tr>
        <tr>
          <th colspan="2" align="center" bgcolor="#E7E7E7">
            <?php echo JText::_('JGA_HELP_LICENCE'); ?>
          </th>
        </tr>
        <tr>
          <td colspan="2" align="center">
            <?php echo JText::_('JGA_HELP_NO_GUARANTEE'); ?>
          </td>
        </tr>
          <th colspan="2" align="center" bgcolor="#E7E7E7">
            &nbsp;
          </th>
        </tr>
      </table>
<?php
  }


}

?>
