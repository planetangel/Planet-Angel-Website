<?php
defined('_JEXEC') or die( "Direct Access Is Not Allowed" );

jimport('joomla.event.plugin');

class plgContentFlickrAlbum extends JPlugin {
  // Parameters
  private $thumbsize;
  private $imagesize;
  private $rows;
  private $cols;
  private $type;
  private $photoset;
  private $keywords;
  private $user;
  private $group;
  private $tags;
  private $sort;
  private $color;
  private $title;

  private $thumbpadding;
  private $thumbborder;
  private $thumbmargin;

  private $flickr_api_key;

  private $inc_jquery;
  private $inc_jquery_flickr;
  private $debug;

  function plgContentFlickrAlbum( &$subject ) {
    parent::__construct( $subject );
  }

  function onPrepareContent(&$article, &$params, $limitstart) {
    $regex = '#{flickr\-album}(.*?){/flickr\-album}#s';
    $article->text = preg_replace_callback($regex,array($this,"displayFlickrAlbum"), $article->text);
    return true;
  }

  function initialize() {
    // Get Plugin info
    $plugin =& JPluginHelper::getPlugin('content', 'flickrAlbum');
    $pluginParams = new JParameter( $plugin->params );

    $this->thumbsize = $pluginParams->get('thumbsizedef', 1);
    $this->imagesize = $pluginParams->get('imagesizedef', 1);
    $this->rows      = $pluginParams->get('rowsdef', 1);
    $this->cols      = $pluginParams->get('colsdef', 1);
    $this->type      = '';
    $this->photoset  = '';
    $this->keywords  = '';
    $this->user      = $pluginParams->get('userdef', 1);
    $this->group     = '';
    $this->tags      = '';
    $this->sort      = $pluginParams->get('sortdef', 1);
    $this->color     = $pluginParams->get('colorthemedef', 1);
    $this->title     = $pluginParams->get('albumtitledef', 1);
    $this->linktext  = $pluginParams->get('linktextdef', 1);
    $this->debug     = (strtolower($pluginParams->get('enabledebug', 1))     == 'yes');

    $this->thumbpadding = $pluginParams->get('thumbpadding', 1);
    $this->thumbborder  = $pluginParams->get('thumbborder',  1);
    $this->thumbmargin  = $pluginParams->get('thumbmargin',  1);

    $this->flickr_api_key = $pluginParams->get('flickr_api_key', 1);

    $this->inc_jquery        = (strtolower($pluginParams->get('incjquery', 1))       == 'yes');
    $this->inc_jquery_flickr = (strtolower($pluginParams->get('incjqueryflickr', 1)) == 'yes');
  }

  function parseParameters( $parameterString ) {
    foreach (explode(',', $parameterString) as $nextParameter) {

      // The evaluation here allows for parameters without values at all (no =... bit)
      if (($paramPos = strpos($nextParameter, '=')) === false) {
        $nextKey = strtolower(trim($nextParameter));
        $nextVal = '';
      } else {
        $nextKey = strtolower(trim(substr($nextParameter, 0, $paramPos)));
        $nextVal = trim(substr($nextParameter, $paramPos+1, strlen($nextParameter)));
      }

      switch ($nextKey) {
        case 'thumbsize' : $this->thumbsize = strtolower($nextVal); break;
        case 'imagesize' : $this->imagesize = strtolower($nextVal); break;
        case 'rows'      : $this->rows      = $nextVal; break;
        case 'cols'      : $this->cols      = $nextVal; break;
        case 'type'      : $this->type      = strtolower($nextVal); break;
        case 'photoset'  : $this->photoset  = $nextVal; break;
        case 'keywords'  : $this->keywords  = $nextVal; break;
        case 'user'      : $this->user      = $nextVal; break;
        case 'group'     : $this->group     = $nextVal; break;
        case 'tags'      : $this->tags      = $nextVal; break;
        case 'sort'      : $this->sort      = strtolower($nextVal); break;
        case 'color'     : $this->color     = strtolower($nextVal); break;
        case 'title'     : $this->title     = $nextVal; break;
        case 'linktext'  : $this->linktext  = $nextVal; break;
        case 'debug'     : $this->debug     = true; break;
      }
    }
  }

  function displayFlickrAlbum($rawParameters) {
    $document =& JFactory::getDocument();

    // Initialize the plugin then parse flickr album parameters
    $this->initialize();
    $this->parseParameters($rawParameters[1]);

    // Choose an album id
    $albumid = mt_rand(10000000,99999999);

    // Prepare Album StyleSheet Arguments
    $dynStyleArgs  = '?albumwidth='   . $this->get_album_width();
    $dynStyleArgs .= '&thumbborder='  . $this->thumbborder;
    $dynStyleArgs .= '&thumbmargin='  . $this->thumbmargin;
    $dynStyleArgs .= '&thumbpadding=' . $this->thumbpadding;
    $dynStyleArgs .= '&albumid='      . $albumid;

    // Prepare Album Javascript Code
    $script  = "    jQuery(function() {\n";
    $script .= "        jQuery('#gallery-flickr-$albumid').flickr( {\n";
    $script .= "            callback: function(el) { jQuery('#gallery-flickr-$albumid a').lightBox({\n";
    $script .= "                imageBlank:    '" . $this->get_plugin_web_path('images/lightbox-blank.gif')       . "'\n";
    $script .= "              , imageBtnClose: '" . $this->get_plugin_web_path('images/lightbox-btn-close.gif')   . "'\n";
    $script .= "              , imageBtnPrev:  '" . $this->get_plugin_web_path('images/lightbox-btn-prev.gif')    . "'\n";
    $script .= "              , imageBtnNext:  '" . $this->get_plugin_web_path('images/lightbox-btn-next.gif')    . "'\n";
    $script .= "              , imageLoading:  '" . $this->get_plugin_web_path('images/lightbox-ico-loading.gif') . "'\n";
    $script .= "            }); }\n";
    $script .= "          , api_key: '"    . $this->flickr_api_key       . "'\n";
    $script .= "          , thumb_size: '" . $this->jflickr_thumb_size() . "'\n";
    $script .= "          , size: '"       . $this->jflickr_size()       . "'\n";
    $script .= "          , per_page: "    . $this->jflickr_per_page()   . "\n";
    $script .= "          , type: '"       . $this->jflickr_type()       . "'\n";
    $script .= "          , sort: '"       . $this->jflickr_sort()       . "'\n";

    switch ($this->type) {
      case 'photoset':
        $script .= "          , photoset_id: '" . $this->jflickr_photoset_id() . "'\n";
        break;
      case 'search':
        $script .= "          , tags: '" . $this->jflickr_tags() . "'\n";
        $script .= "          , text: '" . $this->jflickr_text() . "'\n";
        break;
      case 'user':
        $script .= "          , tags: '"    . $this->jflickr_tags()    . "'\n";
        $script .= "          , user_id: '" . $this->jflickr_user_id() . "'\n";
        break;
      case 'group':
        $script .= "          , tags: '"     . $this->jflickr_tags()     . "'\n";
        $script .= "          , group_id: '" . $this->jflickr_group_id() . "'\n";
        break;
    }

    $script .= "        });\n";
    $script .= "    });\n";
    $script .= "    jQuery.noConflict();\n";

    // Add stylesheet includes
    $document->addStyleSheet($this->get_plugin_web_path('css/jquery.lightbox-0.5.css'));
    $document->addStyleSheet($this->get_plugin_web_path('css/flickrAlbum.css.php' . $dynStyleArgs));

    // Add javascript includes
    if ( $this->inc_jquery )        { $document->addScript($this->get_plugin_web_path('jquery-1.3.1.min.js'));     }
    if ( $this->inc_jquery_flickr ) { $document->addScript($this->get_plugin_web_path('jquery.flickr-1.0-min.js')); }
    $document->addScript($this->get_plugin_web_path('jquery.lightbox-0.5.pjt1-min.js'));
    $document->addScriptDeclaration($script);

    // HTML Code
    $html  = "\n\n";

    // Add debugging header
    if ($this->debug) {
      $html .= "<!-- ---------------------------------------------------------------------------- -->\n";
      $html .= "<!-- captbunzo's Flickr Album Plugin - Generated Album ID = $albumid : HTML START -->\n";
      $html .= "<!-- ---------------------------------------------------------------------------- -->\n";
      $html .= "<!-- Set in Plugin Parameters (Joomla admin backend, Extensions > Plugin Manager) -->\n";
      $html .= "<!--   flickr_api_key    = " . str_pad("'$this->flickr_api_key'", 55) . "-->\n";
      $html .= "<!--   thumbpadding      = " . str_pad("'$this->thumbpadding'",   55) . "-->\n";
      $html .= "<!--   thumbborder       = " . str_pad("'$this->thumbborder'",    55) . "-->\n";
      $html .= "<!--   thumbmargin       = " . str_pad("'$this->thumbmargin'",    55) . "-->\n";
      $html .= "<!--   inc_jquery        = " . str_pad("'" . ($this->inc_jquery ? 'true' : 'false') . "'", 55) . "-->\n";
      $html .= "<!--   inc_jquery_flickr = " . str_pad("'" . ($this->inc_jquery_flickr ? 'true' : 'false') . "'", 55) . "-->\n";
      $html .= "<!--                                                                              -->\n";
      $html .= "<!-- Set in Plugin Parameters and then overridden by tags in the content item     -->\n";
      $html .= "<!--   title             = " . str_pad("'$this->title'",          55) . "-->\n";
      $html .= "<!--   linktext          = " . str_pad("'$this->linktext'",       55) . "-->\n";
      $html .= "<!--   user              = " . str_pad("'$this->user'",           55) . "-->\n";
      $html .= "<!--   color             = " . str_pad("'$this->color'",          55) . "-->\n";
      $html .= "<!--   thumbsize         = " . str_pad("'$this->thumbsize'",      55) . "-->\n";
      $html .= "<!--   imagesize         = " . str_pad("'$this->imagesize'",      55) . "-->\n";
      $html .= "<!--   sort              = " . str_pad("'$this->sort'",           55) . "-->\n";
      $html .= "<!--   cols              = " . str_pad("'$this->cols'",           55) . "-->\n";
      $html .= "<!--   rows              = " . str_pad("'$this->rows'",           55) . "-->\n";
      $html .= "<!--   debug             = " . str_pad("'$this->debug'",          55) . "-->\n";
      $html .= "<!--                                                                              -->\n";
      $html .= "<!-- Set in tags in the content item                                              -->\n";
      $html .= "<!--   type              = " . str_pad("'$this->type'",           55) . "-->\n";
      $html .= "<!--   photoset          = " . str_pad("'$this->photoset'",       55) . "-->\n";
      $html .= "<!--   keywords          = " . str_pad("'$this->keywords'",       55) . "-->\n";
      $html .= "<!--   group             = " . str_pad("'$this->group'",          55) . "-->\n";
      $html .= "<!--   tags              = " . str_pad("'$this->tags'",           55) . "-->\n";
      $html .= "<!-- ---------------------------------------------------------------------------- -->\n";
    }

    // Output the album HTML
    // Planet Angel Edit
    //if (trim($this->title) != '') {
    //  $html .= "<h3 class='gallery-flickr-title'>$this->title</h3>\n";
    //}

    // Planet Angel Edit
    //$html .= "<div id='gallery-flickr-$albumid' class='gallery_$this->color'>&nbsp;</div>\n";
	$html .= "<div id='gallery-flickr-$albumid' class='gallery_$this->color'></div>\n";

    // Planet Angel Edit
    //$html .= "<div class='gallery-flickr-link'><a href='" . $this->buildFlickrLink() . "' target='_blank'>$this->linktext</a></div>\n";
	$html .= "<div class='clear'></div>\n";

    // Add debugging trailer
    if ($this->debug) {
      $html .= "<!-- ---------------------------------------------------------------------------- -->\n";
      $html .= "<!-- captbunzo's Flickr Album Plugin - Generated Album ID = $albumid : HTML END   -->\n";
      $html .= "<!-- ---------------------------------------------------------------------------- -->\n";
    }

    // Return the result
    return $html . "\n";
  }

  function buildFlickrLink() {
    $url = 'http://www.flickr.com';

    switch ($this->type) {

      case 'photoset':
      /* http://www.flickr.com/photos/<USERID>/sets/<PHOTOSET> */
        $url .= "/photos/" . $this->user . "/sets/$this->photoset";
        break;

      case 'search':
      /* Keywords - http://www.flickr.com/search/?q=<KEYWORDS> */
      /* Tags     - http://www.flickr.com/photos/tags/<TAGS>   */
        if ($this->keywords != '') {
          $url .= "/search/?q=" . urlencode($this->keywords);
        } else {
          $url .= "/photos/tags/" . urlencode($this->tags);
        }
        break;

      case 'user':
      /* User      - http://www.flickr.com/photos/<USERID>             */
      /* With Tags - http://www.flickr.com/photos/<USERID>/tags/<TAGS> */
        $url .= "/photos/$this->user";
        if ($this->tags != '') { $url .= "/tags/" . $this->tags; }
        break;

      case 'group':
      /* Group     - http://www.flickr.com/groups/<GROUPID>/pool             */
      /* With Tags - http://www.flickr.com/groups/<GROUPID>/pool/tags/<TAGS> */
        $url .= "/groups/$this->group/pool";
        if ($this->tags != '') { $url .= "/tags/" . $this->tags; }
        break;
    }

    return $url;
  }

  /***************************************************************************/
  /* These functions provide general utility capabilities to the plugin.     */
  /* These are practical for reuse in other plugins.                         */
  /***************************************************************************/

  function get_web_path($relpath) {
    return JURI::root(true) . ($relpath[0] != '/' ? '/' : '') . $relpath;
  }

  function get_plugin_web_path($relpath) {
    return $this->get_web_path('/plugins/content/flickrAlbum' . ($relpath[0] != '/' ? '/' : '') . $relpath);
  }

  /***************************************************************************/
  /* These functions calculate the album width based on the values given for */
  /* thumbnail size, thumbnail padding, thumbnail border, thumbnail margin   */
  /* and the column count.                                                   */
  /***************************************************************************/

  function get_album_width() {
    $album_margin_allowance = 30;
    $thumb_width = $this->get_thumb_width()
                 + (2 * ($this->thumbpadding + $this->thumbborder))
                 + $this->thumbmargin;

    return ($album_margin_allowance + ($this->cols * $thumb_width));
  }

  function get_thumb_width() {
    $result = 0;

    switch ($this->thumbsize) {
      case 'square'    : $result = 75;  break;
      case 'thumbnail' : $result = 100; break;
      case 'small'     : $result = 240; break;
    }

    return $result;
  }

  /************************************************************************/
  /* These functions return values as needed for the jQuery Flickr plugin */
  /************************************************************************/

  function jflickr_thumb_size() {
    $result = 's';

    switch ($this->thumbsize) {
      case 'square'    : $result = 's'; break;
      case 'thumbnail' : $result = 't'; break;
      case 'small'     : $result = 'm'; break;
    }

    return $result;
  }

  function jflickr_size() {
    $result = 'medium';

    // There is not actually a symbol for medium.
    // However, anything not recognised will default to medium, so we'll use l for now.
    switch ($this->imagesize) {
      case 'small'  : $result = 'm'; break;
      case 'medium' : $result = 'l'; break;
      case 'large'  : $result = 'b'; break;
    }

    return $result;
  }

  function jflickr_per_page() {
    return $this->rows * $this->cols;
  }

  function jflickr_type() {
    $result = '';

    switch ($this->type) {
      case 'photoset' : $result = 'photoset'; break;
      case 'search'   : $result = 'search';   break;
      case 'user'     : $result = 'search';   break;
      case 'group'    : $result = 'search';   break;
    }

    return $result;
  }

  // These functions return values that require no transformation
  // However, we use functions in case we need to change something at some point (encapsulation)
  function jflickr_photoset_id() { $result = $this->photoset; return $result; }
  function jflickr_sort()        { $result = $this->sort;     return $result; }
  function jflickr_tags()        { $result = $this->tags;     return $result; }
  function jflickr_text()        { $result = $this->keywords; return $result; }
  function jflickr_user_id()     { $result = $this->user;     return $result; }
  function jflickr_group_id()    { $result = $this->group;    return $result; }

} // End of Class plgContentFlickrAlbum

?>