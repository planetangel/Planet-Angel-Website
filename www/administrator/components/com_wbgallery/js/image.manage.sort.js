// ========================================================================
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
// ========================================================================

// ========================================================================
// Prepare for Image Dragging / Reordering
// ========================================================================

  if( firstRowId ){
    Sortable.create("order",{tag:'li',overlap:'horizontal',constraint: false});
    var sort_order=Sortable.sequence("order");
    // var order = Sortable.serialize('order', {tag:'li', name:'image'});
    var order = Sortable.serialize('order', {tag:'li'});
    order = order.replace(/image\[\]=/g, '');
    neworder = order.split('&');
    for (i=0;i< neworder.length; i++)
      document.write("<input type='hidden' name='order[]' value='" + neworder[i] +"' />");
    var lastQueryStr = 'option='+option+'&task=image.order&first='+firstRowId+'&order='+Sortable.sequence("order");
  }

// ========================================================================
// Update the Image Ordering for Drag-n-Drop
// ========================================================================

  function wbgImgOrder(){
    if( !lastQueryStr )return;
    document.getElementById('ajax_result').innerHTML = '&nbsp;';
    var form = document.getElementById('adminForm');
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
    self.xmlHttpReq.open('POST', form.action, true);
    self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    self.xmlHttpReq.onreadystatechange = function() {
      if (self.xmlHttpReq.readyState == 4) {
        document.getElementById('ajax_result').innerHTML = self.xmlHttpReq.responseText;
      }
    }
    var queryStr = 'option='+option+'&task=image.order&first='+firstRowId+'&order='+Sortable.sequence("order");
    if( lastQueryStr != queryStr ){
      self.xmlHttpReq.send( queryStr );
      document.getElementById('ajax_result').innerHTML = 'Updating Order...';
    }
    lastQueryStr = queryStr;
  }

// ========================================================================
// Update the Image Name
// ========================================================================

  function wbgImgRename(id,mObj){
    var name = trim(mObj.value);
    var form = document.getElementById('adminForm');
    if( name.length > 2 ){
      var queryStr = form.action+'?option='+option+'&task=image.rename&id='+id+'&name='+name;
      postURL(
        queryStr,
        'ajax_result',
        null,null
        );
    } else {
      alert('Please Provide a Valid Name');
    }
  }

// ========================================================================
// Edit the Image
// ========================================================================

  function wbgImgEdit(id){
    var form = document.getElementById('adminForm');
    var queryStr = form.action+'?option='+option+'&task=image.edit&id='+id+'&hidemenu=1';
    document.location = queryStr;
    return false;
  }

// ========================================================================
// Edit the Image
// ========================================================================

  function wbgImgDelete(id){
    if( confirm('Are you Sure you want to Delete this Image?') ){
      var form = document.getElementById('adminForm');
      var queryStr = form.action+'?option='+option+'&task=image.remove&id='+id+'&hidemenu=1';
      document.location = queryStr;
    }
    return false;
  }

