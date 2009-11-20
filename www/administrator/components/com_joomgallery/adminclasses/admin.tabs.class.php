<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/adminclasses/admin.tabs.class.php $
// $Id: admin.tabs.class.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC' ) or die( 'Direct Access to this location is not allowed.');

/**
The following class was taken from Joomla's Core and has been modified by including
the function startNestedTab, which was taken from
http://www.perfectdesigning.net/development_projects/support/joomla!_tab_system.html
*/

/**
* Tab Creation handler
* @package Joomla
*/
class jmosTabs   {
  /** @var int Use cookies */
  var $useCookies = 0;

  /**
  * Constructor
  * Includes files needed for displaying tabs and sets cookie options
  * @param int useCookies, if set to 1 cookie will hold last used tab between page refreshes
  */
  function jmosTabs($useCookies) {
    $document = & JFactory::getDocument();

    $document->addStyleSheet( _JOOM_LIVE_SITE.'includes/js/tabs/tabpane.css','text/css','all' );
    $document->addScript(_JOOM_LIVE_SITE.'includes/js/tabs/tabpane_mini.js');

    $css_style  = "    .dynamic-tab-pane-control .tab-row .tab {\n";
    $css_style .= "      width: auto;\n";
    $css_style .= "      padding: 5px 5px 2px 5px;\n";
    $css_style .= "      background: #E7E7E7;\n";
    $css_style .= "      border:1px solid #949a9c\n    }\n\n";
    $css_style .= "    .dynamic-tab-pane-control .tab-row .tab.selected {\n";
    $css_style .= "      width: auto !important;\n";
    $css_style .= "      background: #fff !important;\n";
    $css_style .= "      border-top-color:#949a9c;\n";
    $css_style .= "      border-top-width:1px;\n";
    $css_style .= "      border-top-style:solid;\n";
    $css_style .= "      border-left-color:#949a9c;\n";
    $css_style .= "      border-left-width:1px;\n";
    $css_style .= "      border-left-style:solid;\n";
    $css_style .= "      border-right-color:#949a9c;\n";
    $css_style .= "      border-right-width:1px;\n";
    $css_style .= "      border-right-style:solid;\n";
    $css_style .= "      border-bottom-color: #fff;\n";
    $css_style .= "      border-bottom-width:1px;\n";
    $css_style .= "      border-bottom-style:solid;\n";
    $css_style .= "      padding: 3px 2px 2px 5px;\n";
    $css_style .= "      margin: 1px 0px 1px 3px;\n";
    $css_style .= "      top: 0px;\n";
    $css_style .= "      height: 17px;\n    }\n\n";
    $css_style .= "    .dynamic-tab-pane-control .tab-row .tab.hover {\n";
    $css_style .= "      width: auto;\n";
    $css_style .= "      background: #fff;\n    }\n";
    $document->addStyleDeclaration($css_style);

    $this->useCookies = $useCookies;
  }

  /**
  * creates a tab pane and creates JS obj
  * @param string The Tab Pane Name
  */
  function startPane($id){
    echo "<div class=\"tab-page\" id=\"".$id."\">";
    echo "<script type=\"text/javascript\">\n";
    echo "  var tabPane1 = new WebFXTabPane( document.getElementById( \"".$id."\" ), ".$this->useCookies." )\n";
    echo "</script>\n";
  }

  /**
  * Ends Tab Pane
  */
  function endPane() {
    echo "</div>";
  }

  /*
  * Creates a tab with title text and starts that tabs page
  * @param tabText - This is what is displayed on the tab
  * @param paneid - This is the parent pane to build this tab on
  */
  function startTab( $tabText, $paneid ) {
    echo "<div class=\"tab-page\" id=\"".$paneid."\">";
    echo "<h2 class=\"tab\">".$tabText."</h2>";
    echo "<script type=\"text/javascript\">\n";
    echo "  tabPane1.addTabPage( document.getElementById( \"".$paneid."\" ) );";
    echo "</script>";
  }

  /*
  * Ends a tab page
  */
  function endTab() {
    echo "</div>";
  }

  function startNestedTab( $tabText ) {
          echo "<div class=\"tab-page\">";
          echo "<h2 class=\"tab\">".$tabText."</h2>";
  }

}
?>
