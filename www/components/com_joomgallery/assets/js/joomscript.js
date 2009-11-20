/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

//Javascript for SmilieInsert and Form Check

function joom_getcoordinates(){ 
  document.nameshieldform.xvalue.value=document.getElementById("u1").offsetTop; 
  document.nameshieldform.yvalue.value=document.getElementById("u1").offsetLeft;
  document.nameshieldform.submit();
}

function joom_validatecomment(){
  if (document.commentform.cmttext.value==''){
    alert(joomgallery_enter_comment);
  } else if(jg_use_code==1) {
    if (document.commentform.jgcode != null && document.commentform.jg_code.value==''){
      alert(joomgallery_enter_code);
    } else {
      document.commentform.submit();
    }
  } else {
    document.commentform.submit();
  }
}


function joom_smilie(thesmile) {
  document.commentform.cmttext.value += thesmile+' ';
  document.commentform.cmttext.focus();
}


function joom_validatesend2friend(){
  if ((document.send2friend.send2friendname.value=='') || (document.send2friend.send2friendemail.value=='')){
    alert(joomgallery_enter_name_email);
  } else {
    document.send2friend.submit();
  }
}


function joom_checkme() {
  var form = document.adminForm;
  form.imgtitle.style.backgroundColor = '';
  form.catid.style.backgroundColor = '';
  var doublefiles = false;
  // do field validation
  if (form.imgtitle.value == ''|| form.imgtitle.value == null) {
    alert(joomgallery_pic_must_have_title);
    form.imgtitle.style.backgroundColor = jg_ffwrong;
    form.imgtitle.focus();
    return false;
  } else if (form.catid.value == "0") {
    alert(joomgallery_select_category);
    form.catid.style.backgroundColor = jg_ffwrong;
    form.catid.focus();
    return false;
    //Prueft ob ueberhaupt Dateien angeben wurden.
  } else {
    var zaehl = 0;
    var arenofiles = true;
    var fullfields = new Array();
    var screenshotfieldname = new Array();
    var screenshotfieldvalue = new Array();
    for(i=0;i<jg_inputcounter;i++) {
      screenshotfieldname[i] = 'arrscreenshot['+i+']';
      screenshotfieldvalue[i] = document.getElementsByName(screenshotfieldname[i])[0].value;
      document.getElementsByName(screenshotfieldname[i])[0].style.backgroundColor='';
      if(screenshotfieldvalue[i] != "") {
        arenofiles = false;
        fullfields[zaehl] = i;
        zaehl++;
      }
    }
  }
  if(arenofiles) {
    alert(joomgallery_select_file);
    document.getElementsByName(screenshotfieldname[0])[0].focus();
    return false;
    //Prueft ob die Dateitypen auch .jpg,.gif und .png sind
  } else {
    var extensionsnotok = false;
    var searchextensiontest = new Array();
    var searchextension = new Array();
    //However you have to define this RegExp for each item.
    for (i=0;i<fullfields.length;i++) {
      searchextension[i] = new RegExp('\.jpg$|\.jpe$|\.jpeg$|\.gif$|\.png$','ig');
    }
    for(i=0;i<fullfields.length;i++) {
      searchextensiontest = searchextension[i].test(screenshotfieldvalue[fullfields[i]]);
      if(searchextensiontest!=true) {
        extensionsnotok = true;
        document.getElementsByName(screenshotfieldname[fullfields[i]])[0].style.backgroundColor = jg_ffwrong;
      }
    }
  }
  if(extensionsnotok) {
    alert(joomgallery_wrong_extension);
    document.getElementsByName(screenshotfieldname[0])[0].focus();
    return false;
    //Wenn eine Javascriptueberpruefung in den Configurations gewuenscht wurde wird der Dateinamen auf Sonderzeichen ueberprueft
  } else {
    var filenamesnotok = false;
    if(jg_filenamewithjs!=0) {
      var searchwrongchars = /[^ a-zA-Z0-9_-]/;
      var lastbackslash = new Array();
      var endoffilename = new Array();
      var filename = new Array();
      for(i=0;i<fullfields.length;i++) {
        lastbackslash[i] = screenshotfieldvalue[fullfields[i]].lastIndexOf('\\');
        endoffilename[i] = screenshotfieldvalue[fullfields[i]].lastIndexOf('\.')-screenshotfieldvalue[fullfields[i]].length;
        if(lastbackslash[i]<1) {
         lastbackslash[i] = screenshotfieldvalue[fullfields[i]].lastIndexOf('/');
        }
        filename[i] = screenshotfieldvalue[fullfields[i]].slice(lastbackslash[i]+1,endoffilename[i]);
        if(searchwrongchars.test(filename[i])) {
          filenamesnotok = true;
          document.getElementsByName(screenshotfieldname[fullfields[i]])[0].style.backgroundColor = jg_ffwrong;
        }
      }
    }
  }
  if(filenamesnotok) {
    alert(joomgallery_wrong_filename);
    document.getElementsByName(screenshotfieldname[0])[0].focus();
    return false;
  } else if(fullfields.length>1) {
    var feld1 = new Number();
    var feld2 = new Number();
    for(i=0;i<fullfields.length;i++) {
      for(j=fullfields.length-1;j>i;j--) {
        if(screenshotfieldvalue[fullfields[i]].indexOf(screenshotfieldvalue[fullfields[j]])==0) {
          doublefiles = true;
          document.getElementsByName(screenshotfieldname[fullfields[i]])[0].style.backgroundColor = jg_ffwrong;
          document.getElementsByName(screenshotfieldname[fullfields[j]])[0].style.backgroundColor = jg_ffwrong;
          feld1 = i+1;
          feld2 = j+1
          alert(joomgallery_filename_double1+' ' +feld1+' '+joomgallery_filename_double2+' '+feld2+'.');
        }
      }
    }
  }
  if(doublefiles) {
    document.getElementsByName(screenshotfieldname[0])[0].focus();
    return false;
  } else {
    form.submit();
    return true;
  }
}


function joom_checkme2() {
  var form = document.adminForm;
  form.imgtitle.style.backgroundColor = '';
  form.catid.style.backgroundColor = '';
  // do field validation
  if (form.imgtitle.value == '' || form.imgtitle.value == null) {
    alert(joomgallery_pic_must_have_title);
    form.imgtitle.style.backgroundColor = jg_ffwrong;
    form.imgtitle.focus();
    return false;
  } else if (form.catid.value == '0') {
    alert(joomgallery_select_category);
    form.catid.style.backgroundColor = jg_ffwrong;
    form.catid.focus();
    return false;
  } else {
    form.submit();
    return true;
  }
}


function joom_openjswindow(imgsource, imgtitle, imgwidth, imgheight) {
  var imgwidth = parseInt(imgwidth);
  var imgheight = parseInt(imgheight);
  var scrbar = (resizeJsImage>0) ? 0 : 1;
  pgwindow = window.open('', 'JoomGallery', 'toolbar=0,location=0,directories=0,status=0,menubar=0,resizable=0,scrollbars='+scrbar+',width='+imgwidth+',height='+imgheight+'');
  with(pgwindow.document) {
    write("<html><head><title>" + imgtitle + "<\/title>\n");
    write("<meta http-equiv='imagetoolbar' content='no' />\n");
    write("<script language='javascript' type='text/javascript'>\n");
    write("<!--\n");
    write("var disableclick = "+jg_disableclick+";\n");
    write("if (disableclick>0) {document.oncontextmenu = function(){return false;} }\n");
    write("function resize() {\n");
    write(" if("+resizeJsImage+">0) {\n");
    write("  var windowWidth, windowHeight, padleft, padtop;\n" );
    write("  if (self.innerHeight) {  // all except Explorer\n" );
    write("   windowWidth = self.innerWidth;\n" );
    write("   windowHeight = self.innerHeight;\n" );
    write("   padleft = 6;\n" );
    write("   padtop = 55;\n" );
    write("  } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode\n" );
    write("   windowWidth = document.documentElement.clientWidth;\n" );
    write("   windowHeight = document.documentElement.clientHeight;\n" );
    write("   padleft = 10;\n" );
    write("   padtop = 35;\n" );
    write("  } else if (document.body) { // other Explorers\n" );
    write("   windowWidth = document.body.clientWidth;\n" );
    write("   windowHeight = document.body.clientHeight;\n" );
    write("   padleft = 10;\n" );
    write("   padtop = 35;\n" );
    write("  }\n" );
    write("  var imgwidth = "+imgwidth+"+padleft;\n");
    write("  var imgheight = "+imgheight+"+padtop;\n");
    write("  if(imgwidth>windowWidth) {\n");
    write("    imgheight = (imgheight * windowWidth)/imgwidth;\n");
    write("    imgwidth = windowWidth;\n");
    write("  }\n");
    write("  if(imgheight>windowHeight) {\n");
    write("    imgwidth = (imgwidth * windowHeight)/imgheight;\n");
    write("    imgheight = windowHeight;\n");
    write("  }\n");    
    write("  self.resizeTo(imgwidth, imgheight);\n");
    write("  self.document.getElementById('js_window_image').width = imgwidth-padleft;\n");
    write("  self.document.getElementById('js_window_image').style.width = imgwidth-padleft;\n");
    write("  self.document.getElementById('js_window_image').height = imgheight-padtop;\n");
    write("  self.document.getElementById('js_window_image').style.height = imgheight-padtop;\n");
    write("  self.document.body.style.overflow='hidden'\n");
    write(" } else {\n");
    write("  self.document.body.style.overflow=''\n");
    write(" }\n");
    write(" self.focus();\n");
    write("}\n");
    write("function clicker() { \n");
    write("if (disableclick>0) {self.close(); } \n");
    write("}\n");
    write("\/\/-->\n");
    write("<\/script>\n");
    write("<\/head>\n");
    write("<body topmargin='0' marginheight='0' leftmargin='0' marginwidth='0' onload='resize()' onclick='clicker()' onblur='self.focus()'>\n");
    write("<img src='" + imgsource + "' border='0' hspace='0' vspace='0' onclick='self.close()' alt='"+imgtitle+"'\ id=\"js_window_image\" class=\"pngfile\" />\n");
    write("<\/body><\/html>");
    close();
  }
  pgwindow.moveTo(0,0);
}


// This Script was written by Benjamin Meier, b2m@gmx.de
// The DHTML-function for creating a overlaying div-layer uses parts of the Dynamic Image Mambot, written by Manuel Hirsch
// and Lightbox => core code quirksmode.org
function joom_opendhtml(imgsource, imgtitle, imgtext, imgwidth, imgheight) {
  imgwidth = parseInt(imgwidth);
  imgheight = parseInt(imgheight);

  var windowWidth, windowHeight;
  if (self.innerHeight) {  // all except Explorer
    windowWidth = self.innerWidth;
    windowHeight = self.innerHeight;
  } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
    windowWidth = document.documentElement.clientWidth;
    windowHeight = document.documentElement.clientHeight;
  } else if (document.body) { // other Explorers
    windowWidth = document.body.clientWidth;
    windowHeight = document.body.clientHeight;
  }

  var yScroll, xScroll;

  if (self.pageYOffset) {
    yScroll = self.pageYOffset;
    xScroll = self.pageXOffset;
  } else if (document.documentElement && document.documentElement.scrollTop){   // Explorer 6 Strict
    yScroll = document.documentElement.scrollTop;
    xScroll = document.documentElement.scrollLeft;
  } else if (document.body) {// all other Explorers
    yScroll = document.body.scrollTop;
    xScroll = document.body.scrollLeft;
  }

  if(resizeJsImage==1) {
   if((imgwidth+3*jg_padding)>windowWidth) {
     imgheight = (imgheight * (windowWidth-2*jg_padding))/imgwidth;
     imgwidth = windowWidth-2*jg_padding;
   }
   if((imgheight+2*jg_padding+80)>windowHeight) {
     imgwidth = (imgwidth * (windowHeight-2*jg_padding-80))/imgheight;
     imgheight = windowHeight-2*jg_padding-80;
   }
  }
  var postop =(windowHeight/2)-(imgheight/2)+yScroll+document.body.style.padding-10;
  var posleft =(windowWidth/2)-(imgwidth/2)+xScroll+document.body.style.padding;
  if(postop >= 30) { 
   postop = postop-30;
  }
  var bodyObj = document.getElementsByTagName('BODY')[0];
  if(!document.getElementById("jg_photocontainer")) {
    divObjContainer = document.createElement("div");
    divObjContainer.setAttribute("id", "jg_photocontainer");
    bodyObj.appendChild(divObjContainer);
  } else {
    divObjContainer = document.getElementById("jg_photocontainer");
  }

  var closeimg = new Image();
  closeimg.src = "components/com_joomgallery/assets/images/close.png";

  var dhtmltext, dhtmltext2="";

  divObjContainer.style.display = "block";
  dhtmltext  = "<div class=\"jg_photocontainer\" style=\"top:"+postop+"px; left:"+posleft+"px; position: absolute; display:block;z-index:99999;\" onclick=\"joom_photocontainershut()\">";
  dhtmltext += "<div class=\"photoborder\" style=\"background-color: "+jg_openjs_background+"; padding: "+jg_padding+"px; border: solid 1px "+jg_dhtml_border+";\">";
  dhtmltext += "<img onclick=\"joom_photocontainershut()\" style=\"cursor:pointer;border: solid 1px #000;width:"+imgwidth+"px;height:"+imgheight+"px;\" src=\""+imgsource+"\" alt=\""+imgtitle+"\" width=\""+imgwidth+"px\" height=\""+imgheight+"px\" class=\"pngfile\" \/>";
  dhtmltext += "<img onclick=\"joom_photocontainershut()\" style=\"cursor:pointer;position:absolute;bottom:"+jg_padding+"px;right:"+jg_padding+"px;width:"+closeimg.width+"px;height:"+closeimg.height+"px;\" src=\""+closeimg.src+"\" alt=\"close\" id=\"dhtml_close\" class=\"pngfile\" />";  
  dhtmltext += "<br /><div id=\"joom_dhtml_imgtext\" style=\"margin-top:"+jg_padding+"px;text-align: justify; width:"+imgwidth+"px;\">&nbsp;<br />&nbsp;</div>";
  dhtmltext += "<\/div></div>";
  divObjContainer.innerHTML = dhtmltext;
     document.getElementById("joom_dhtml_imgtext").style.width2 = document.getElementById("joom_dhtml_imgtext").style.width-document.getElementById("dhtml_close").style.width;
  if (jg_show_title_in_dhtml==1) {
   dhtmltext2 += "<strong>"+imgtitle+"</strong><br />";
  }
  if (jg_show_description_in_dhtml==1) {
   dhtmltext2 += imgtext;
  }
  if (dhtmltext2!="") {
   document.getElementById("joom_dhtml_imgtext").innerHTML = dhtmltext2;
  }

  if (jg_disableclick==1) { 
    divObjContainer.oncontextmenu = function(){return false;}
  }
}

function joom_photocontainershut() {
  document.getElementById("jg_photocontainer").style.display = "none";
}


function joom_cursorchange(e) {
  active_slimbox=document.getElementById("lbImage");
  
  if (active_slimbox != undefined){
    return
  }
  
  if(jg_comment_active!=1) {
    if(navigator.appName == "Microsoft Internet Explorer") {
      taste = window.event.keyCode;
    } else {
      taste = e.which;
    }
    switch (taste) {
      case 37:
        if(document.form_jg_back_link) {
          window.location=document.form_jg_back_link.action;
        }
        break;
      case 39:
        if(document.form_jg_forward_link) {
          window.location=document.form_jg_forward_link.action;
        }
        break;
      default:
        break;
    }
  }
}

