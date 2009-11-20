<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableAlias extends JTable {
	/** @var int Primary key */
	var $id=null;
	/** @var string */
	var $non_sef_url=null;
	/** @var string */
	var $alias=null;
	/** @var string */
	var $title=null;
	/** @var string */
	var $metakey=null;
	/** @var string */
	var $metadesc=null;
	/** @var string */
	var $canonical=null;
	/** @var int */
	var $published=null;

	function __construct( &$_db ) {
		parent::__construct( '#__sef_alias', 'id', $_db );
	}
}

?>