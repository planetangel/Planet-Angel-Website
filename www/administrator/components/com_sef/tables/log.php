<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableLog extends JTable {
	/** @var int Primary key */
	var $id=null;
	/** @var string */
	var $url=null;
	/** @var string */
	var $time=null;
	/** @var string */
	var $ip=null;
	/** @var string */
	var $referer=null;

	function __construct( &$_db ) {
		parent::__construct( '#__sef_log', 'id', $_db );
	}
}

?>