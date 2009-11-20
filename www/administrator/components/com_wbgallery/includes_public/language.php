<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Language Loader
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class wbGallery_lang {

  var $_default = 'english';
  var $_lang    = null;
  var $_files   = Array();
  var $_data    = Array();

  function wbGallery_lang( $lang ){
    global $mainframe, $wbGallery_public;
    if(file_exists($wbGallery_public.'language/'.$lang.'.ini'))
      $this->files[] = $wbGallery_public.'language/'.$lang.'.ini';
    elseif(file_exists($wbGallery_public.'language/'.$this->_default.'.ini'))
      $this->files[] = $wbGallery_public.'language/'.$this->_default.'.ini';
    elseif(file_exists($wbGallery_public.'language/'.$mainframe->getCfg('lang').'.ini'))
      $this->files[] = $wbGallery_public.'language/'.$mainframe->getCfg('lang').'.ini';
    if( count($this->files) )
      $this->load();
  }

  function load(){
    if( count($this->files) ){
      foreach( $this->files AS $file ){
        $fh = fopen( $file, 'r' );
        while( !feof($fh) ){
          $line = fgets($fh);
          preg_match('/^([\w\d\_]+)=(.*)$/',$line,$matches);
          if( count($matches) > 2 )
            $this->_data[ $matches[1] ] = $matches[2];
        }
      }
    }
  }

  function _( $key ){
    if( array_key_exists( $key, $this->_data ) )
      return $this->_data[ $key ];
    else
      return "$key not found";
  }

}