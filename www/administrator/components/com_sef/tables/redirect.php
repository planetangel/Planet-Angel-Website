<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableRedirect extends JTable {
	/** @var int Primary key */
	var $id=null;
	/** @var string */
	var $source=null;
	/** @var string */
	var $target=null;
	/** @var string */
	var $type=null;
	/** @var int */
	var $published=null;

	function __construct( &$_db ) {
		parent::__construct( '#__sef_redirect', 'id', $_db );
	}
}

?>