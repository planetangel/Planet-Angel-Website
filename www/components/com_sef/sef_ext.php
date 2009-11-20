<?php
/**
* SEF Advance component extension
*
* This extension will give the SEF Advance style URLs to the example component
* Place this file (sef_ext.php) in the main component directory
* Note that the class must be named: sef_componentname
*
* Copyright (C) 2003-2007 Emir Sakic, http://www.sakic.net, All rights reserved.
*
* Comments: for SEF Advance > v3.6
**/

class sef_example {

	/**
	* Creates the SEF Advance URL out of the request
	* Input: $string, string, The request URL (index.php?option=com_example&Itemid=$Itemid)
	* Output: $sefstring, string, SEF Advance URL ($var1/$var2/)
	**/
	function create ($string) {
		// $string == "index.php?option=com_example&Itemid=$Itemid&var1=$var1&var2=$var2"
		$sefstring = '';
		if (eregi('&amp;var1=',$string)) {
			$temp = explode('&amp;var1=', $string);
			$temp = explode('&', $temp[1]);
			$sefstring .= sefencode($temp[0]).'/';
		}
		if (eregi('&amp;var2=',$string)) {
			$temp = explode('&amp;var2=', $string);
			$temp = explode('&', $temp[1]);
			$sefstring .= sefencode($temp[0])."/";
		}
		// $sefstring == "$var1/$var2/"
		return $sefstring;
	}

	/**
	* Reverts to the query string out of the SEF Advance URL
	* Input:
	*    $url_array, array, The SEF Advance URL split in arrays
	*    $pos, int, The position offset for virtual directories (first virtual directory, which is the component name, begins at $pos+1)
	* Output: $QUERY_STRING, string, query string (var1=$var1&var2=$var2)
	*    Note that this will be added to already defined first part (option=com_example&Itemid=$Itemid)
	**/
	function revert ($url_array, $pos) {
		if (( ini_get('register_globals')==1 && (!defined('RG_EMULATION') || RG_EMULATION==1) ) ||
			( ini_get('register_globals')==0 && (defined('RG_EMULATION') && RG_EMULATION==1) ) ) {
			// if register globals on, emulation on OR register globals off, emulation on
			// then define all variables you pass as globals
			global $var1, $var2;
		}
 		// Examine the SEF Advance URL and extract the variables building the query string
		$QUERY_STRING = '';
		if (isset($url_array[$pos+2]) && $url_array[$pos+2]!='') {
			// .../example/$var1/
			$var1 = sefdecode($url_array[$pos+2]);
			$_GET['var1'] = $_REQUEST['var1'] = $var1;
			$QUERY_STRING .= "&var1=$var1";
		}
		if (isset($url_array[$pos+3]) && $url_array[$pos+3]!='') {
			// .../example/$var1/$var2/
			$var2 = sefdecode($url_array[$pos+3]);
			$_GET['var2'] = $_REQUEST['var2'] = $var2;
			$QUERY_STRING .= "&var2=$var2";
		}
		// $QUERY_STRING == "var1=$var1&var2=$var2";
		return $QUERY_STRING;
	}

}
?>