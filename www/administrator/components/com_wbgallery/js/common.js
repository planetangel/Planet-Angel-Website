// ========================================================================
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
// ========================================================================

// ========================================================================
// Global Definitions
// ========================================================================

var option = 'com_wbgallery';

// ========================================================================
// Trim Functions
// ========================================================================

if( window.trim ){}else{
  function trim(stringToTrim) {
    return stringToTrim.replace(/^\s+|\s+$/g,"");
  }
}

// ========================================================================
// Check for Prototype & Scriptaculous Library
// .. reference the window object to find if Scriptalicious function exists
// ========================================================================

if( window.Prototype ){}else{
  document.write('<script type="text/javascript" src="components/'+option+'/js/scriptaculous/prototype.js"></script>');
}
if( window.Scriptaculous ){}else{
  document.write('<script type="text/javascript" src="components/'+option+'/js/scriptaculous/scriptaculous.js"></script>');
}

// ========================================================================
// Check for the postURL Function
// .. reference the window object to find if the wbPack postURL exists
// ========================================================================

if( window.postURL ){}else{
  function postURL( url, cell_id, preCall, postCall ) {
    if( preCall ){
      eval( 'result = '+preCall );
      if( !result ) return false;
    }
    var pre_url = url.replace(/\?.*/,'');
    var pos_url = url.replace(/^.*\?/,'&');
    var xmlHttpReq = false;
    var self = this;
    // Mozilla/Safari
    if (window.XMLHttpRequest) {
      self.xmlHttpReq = new XMLHttpRequest();
    }
    // IE
    else if (window.ActiveXObject) {
      self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
    }
    self.xmlHttpReq.open('POST', pre_url, true);
    self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    self.xmlHttpReq.onreadystatechange = function() {
      if (self.xmlHttpReq.readyState == 4) {
        if(cell_id)
          document.getElementById( cell_id ).innerHTML = self.xmlHttpReq.responseText;
        if( postCall ) eval( postCall );
      }
    }
    self.xmlHttpReq.send(pos_url);
    document.getElementById( cell_id ).innerHTML = 'Updating...';
  }
}

// ========================================================================
// Check for the isArray Function
// .. reference the window object to find if wbPack isArray exists
// ========================================================================

if( window.isArray ){}else{
  function isArray(mObj) {
    if(mObj.constructor.toString().indexOf("Array") == -1)
      return false;
    else
      return true;
  }
}