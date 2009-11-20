<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/html/joom.viewminijoom.html.php $
// $Id: joom.viewminijoom.html.php 1444 2009-06-11 16:42:45Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined ('_JEXEC') or die('Direct Access to this location is not allowed.');

class Joom_ShowMiniJoom_HTML #extends Joom_ShowMiniJoom
{

  /**
   * HTML output of the mini thumbnails for the JoomBu
   *
   *@param array DB result
   */
  function Joom_ShowMinis_HTML($rows)
  {
    $mainframe = & JFactory::getApplication('site');
    $document  = & JFactory::getDocument();
    $config    = Joom_getConfig();

    // template CSS is usually not loaded now, but it's better to have it
    $load_template_css = true; // make available in the plugin params?
    if($load_template_css)
    {
      $template      = $mainframe->getTemplate();
      $template_file = false;
      if(is_file(JPATH_THEMES.DS.$template.DS.'css'.DS.'template.css'))
      {
        $template_file = 'templates/'.$template.'/css/template.css';
      }
      else
      {
        if(is_file(JPATH_THEMES.DS.$template.DS.'css'.DS.'template_css.css'))
        {
          $template_file = 'templates/'.$template.'/css/template_css.css';
        }
      }
      if($load_template_css)
      {
        $document->addStyleSheet(_JOOM_LIVE_SITE.$template_file);
        // to avoid scroll bar with some templates
        $document->addStyleDeclaration("    body{\n      height:90%;\n    }");
      }
    }

    // for overlib effect # at the moment always loaded by joom.javascript.php
    #$document->addScript(_JOOM_LIVE_SITE.'includes/js/overlib_mini.js');

    // for accordion
    $document->addStyleSheet('components/com_joomgallery/assets/css/joom_detail.css');
    JHTML::_('behavior.mootools');
    #$document->addScript('components/com_joomgallery/assets/js/accordion/js/accordion.js');
    $script = "
    window.addEvent('domready', function(){ 
      new Accordion($$('h4.joomgallery-toggler'), $$('div.joomgallery-slider'), 
        {onActive: function(toggler, i) { 
          toggler.addClass('joomgallery-toggler-down'); 
          toggler.removeClass('joomgallery-toggler'); },
         onBackground: function(toggler, i) { 
          toggler.addClass('joomgallery-toggler'); 
          toggler.removeClass('joomgallery-toggler-down'); },
          duration: 300,display:-1,show:1,opacity: false,alwaysHide: true}); });";
    $document->addScriptDeclaration($script);

    // JavaScript for inserting the tag
    $script = "
    function insertJoomPluWithId(jg_id) {
      jg_detail = document.getElementById('jg_bu_detail').checked;
      jg_linked = document.getElementById('jg_bu_linked').checked;
      jg_align  = document.getElementById('jg_bu_align').value;
      if(jg_detail) {
        jg_detail = ' detail';
      } else {
        jg_detail = '';
      }
      if(jg_linked) {
        jg_linked = '';
      } else {
        jg_linked = ' not linked';
      }
      if(jg_align) {
        jg_align  = ' ' + jg_align;
      } else {
        jg_align  = '';
      }
      jg_plu_tag  = '{joomplu:' + jg_id + jg_detail + jg_linked + jg_align + '}';
      window.parent.jInsertEditorText(jg_plu_tag, 'text');
      window.parent.document.getElementById('sbox-window').close();
    }";
    $document->addScriptDeclaration($script);
?>
<div class="gallery minigallery">
  <div class="jg_header">
    <?php echo JText::_('JGS_BU_INTRO'); ?>
  </div>
  <div class="sectiontableheader">
    <h4 class="joomgallery-toggler">
      <?php echo JText::_('JGS_BU_EXTENDED'); ?>&nbsp;
    </h4>
  </div>
  <div class="joomgallery-slider">
    <div class="jg_bu_extended_options">
      <span class="jg_bu_extended_option_left">
        <?php echo JText::_('JGS_BU_DETAIL'); ?>&nbsp;
        <input type="checkbox" id="jg_bu_detail">
      </span>
      <span class="jg_bu_extended_option_middle">
        <?php echo JText::_('JGS_BU_LINKED'); ?>&nbsp;
        <input type="checkbox" id="jg_bu_linked" checked="checked">
      </span>
      <span class="jg_bu_extended_option_right">
        <?php echo JText::_('JGS_BU_ALIGN'); ?>
        <select id="jg_bu_align">
          <option value="">
          </option>
          <option value="right">
            <?php echo JText::_('JGS_BU_ALIGN_RIGHT'); ?>&nbsp;
          </option>
          <option value="left">
            <?php echo JText::_('JGS_BU_ALIGN_LEFT'); ?>&nbsp;
          </option>
        </select>
      </span>
    </div>
  </div>
  <div class="sectiontableheader">
    <h4 class="joomgallery-toggler">
      <?php echo JText::_('JGS_BU_SEARCH'); ?>
    </h4>
  </div>
  <div class="joomgallery-slider">
    <div class="jg_bu_search">
    <form action="index.php?option=com_joomgallery&func=joomplu&tmpl=component&e_name=text" method="post">
<?php
    echo $this->_pagination->getListFooter();
?>
      <div class="jg_bu_filter">
        <?php echo Jtext::_('JGS_FILTER_BY_CATEGORY'); ?>&nbsp;
<?php
    echo Joom_ShowDropDownCategoryList($this->catid,'catid','onchange="this.form.submit();"');
?>
      </div>
    </form>
    </div>
  </div>
  <div class="jg_bu_minis">
<?php
    foreach($rows as $row)
    {
      if(!$this->catid)
      {
        $catpath  = Joom_GetCatPath($row->catid);
        $cat_name = true;
      }
      else
      {
        $catpath  = $this->catpath;
        $cat_name = false;
      }
?>
    <div class="jg_bu_mini">
<?php
      if($row->imgthumbname != '' && (is_file(JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->imgthumbname))))
      {
        $tnfile     = JPath::clean(JPATH_ROOT.DS.$config->jg_paththumbs.$catpath.$row->imgthumbname);
        $tnfile     = str_replace(' ', '%20', $tnfile);
        $imginfo    = getimagesize($tnfile);
        $srcLink    = _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->imgthumbname;
        $srcWidth   = $imginfo[0];
        $srcHeight  = $imginfo[1];
        $overlib    = Joom_ShowMiniJoom_HTML::getOverlibHtml($row, $srcLink, $cat_name);
?>
      <a href="javascript:insertJoomPluWithId('<?php echo $row->id; ?>');" onmouseover="return overlib('<?php echo $overlib; ?>',WIDTH,<?php echo $srcWidth; ?>,<?php /* HEIGHT,<?php echo $srcHeight; ?>,*/ ?>ABOVE)" onmouseout="return nd()" >
        <img src="<?php echo _JOOM_LIVE_SITE.$config->jg_paththumbs.$catpath.$row->imgthumbname; ?>" border="0" height="40" width="40" alt="Thumbnail" /></a>
<?php
      }
      else
      {
?>
      <div class="jg_bu_no_mini" onmouseover="return overlib('<?php echo sprintf(JText::_('JGS_NO_THUMB_TOOLTIP_TEXT',true), $row->id, $row->imgtitle); ?>', CAPTION, '<?php echo JText::_('JGS_NO_THUMB'); ?>', BELOW, RIGHT)" onmouseout="return nd()" >
        <?php echo JText::_('JGS_NO_THUMB'); ?>&nbsp;
      </div>
<?php
      }
?>
    </div>
<?php
    }
    if(!count($rows))
    {
?>
    <div class="jg_bu_no_images">
      <?php echo JText::_('JGS_NO_IMAGES'); ?>&nbsp;
    </div>
<?php
    }
?>
  </div>
</div>
<?php
  }//End function Joom_ShowMinis_HTML


  /**
   * HTML output of the overlib window
   *
   *@param  object  DB row of an image
   *@param  string  link to the Thumbnail
   *@param  boolean will show the cat name, too
   *@return string  the HTML output
   */
  function getOverlibHtml($row, $srcLink, $cat_name)
  {
    $return  =  '<div class="jg_overlib">';
    $return .=    '<div>';# style="float:left;">';
    $return .=      '<img src="'.$srcLink.'" />';
    $return .=    '</div>';
    $return .=    '<div>';
    $return .=      '<div class="jg_title">';
    $return .=        addslashes($row->imgtitle);
    $return .=      '</div>';
    if($cat_name)
    {
      $return .=    '<div class="jg_catname">';
      $return .=      addslashes($row->name);
      $return .=    '</div>';
    }
    $return .=    '</div>';
    $return .=  '</div>';

    // XHTML
    $current  = array('<', '>', '"');
    $substit  = array('&lt;', '&gt;', '&quot;');
    $return   = str_replace($current, $substit, $return);

    return $return;
  }//End function getOverlibHtml}

}//End class Joom_ShowMiniJoom_HTML
