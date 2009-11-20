<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableConfig extends JTable {
	/** @var int Unique id*/
	var $id=1;
	/** @var string */
	var $archive=null;
	/** @var string */
	var $weblinks=null;
	/** @var string */
	var $poll=null;
	/** @var string */
	var $banners=null;
	/** @var string */
	var $contact=null;
	/** @var string */
	var $search=null;
	/** @var string */
	var $newsfeeds=null;
	/** @var string */
	var $custom_comp=null;
	/** @var int */
	var $enabled=null;
	/** @var string */
	var $space=null;
	/** @var string */
	var $sufix=null;
	/** @var int */
	var $alias=null;
	/** @var int */
	var $lowercase=null;
	/** @var int */
	var $inc_sec=null;
	/** @var int */
	var $inc_cat=null;
	/** @var int */
	var $uniqitem=null;
	/** @var int */
	var $fish=null;
	/** @var int */
	var $nupd=null;
	/** @var int */
	var $bird=null;
	/** @var int */
	var $nsrd=null;
	/** @var int */
	var $lgrd=null;
	/** @var int */
	var $www_redirect=null;
	/** @var int */
	var $debug=null;
	/** @var string */
	var $debugip=null;
	/** @var string */
	var $custom404=null;
	/** @var string */
	var $url_replace=null;
	/** @var string */
	var $url_exception=null;
	/** @var array */
	var $com_exception=null;
	/** @var int */
	var $cache=null;
	/** @var int */
	var $cachetime=null;
	/** @var int */
	var $log404=null;
	/** @var int */
	var $seo_h1=null;
	/** @var int */
	var $seo_title=null;
	/** @var int */
	var $seo_alt=null;
	/** @var int */
	var $seo_canonical=null;
	/** @var int */
	var $seo_nofollow=null;
	/** @var int */
	var $seo_blank=null;
	/** @var int */
	var $seo_icon=null;
	
	function __construct( &$_db ) {
		parent::__construct( '#__sef_config', 'id', $_db );
	}
}

?>