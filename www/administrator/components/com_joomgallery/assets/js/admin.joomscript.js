/******************************************************************************\
**   JoomGallery  1.5.0                                                       **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

//test the values in configuration manager and delete the not modified values in DOM
function joom_testDefaultValues() {
  var what = document.adminForm;
  var result;
  var todelete = Array();
  var todeletecount = 0;
  var elemcount=what.elements.length;
  
  var gpstagschecked=false;
  var ifdotagschecked=false;
  var subifdtagschecked=false;
  var iptctagschecked=false;
  
  var elem=null;
  var elemType=null;
  var myName=null;
  
  for (var i=0; i<elemcount; i++) {
    result=false;
    elem = what.elements[i];
    elemType = what.elements[i].type;  
    myName = what.elements[i].name;
    
    if (myName.substr(0,3) == "jg_")  {
    //check node list gpstags,ifdotags,subifdtags,iptctags
    //gpstags
    if (myName=="jg_gpstags[]"){
      if (gpstagschecked==false){
        do {        
	        if (String(elem.checked) != String(elem.defaultChecked)) {
	          //at least on element to save, so the array
	          //nothing more to check
	          gpstagschecked=true;
	          result = true; //save
	          i++;
	          break;//leave the do..while
	        }
	        i++;//next element
	        elem = what.elements[i];
	        elemType = what.elements[i].type;
	        myName = what.elements[i].name;	
        } while (myName=="jg_gpstags[]");
        if (!result){
          //no element in array to save, so delete the array
          todelete[todeletecount++] = "jg_gpstags[]";       
        }
        i--;
        gpstagschecked=true;
        continue;               

      }else{
        continue;
      }
    } else if (myName=="jg_ifdotags[]"){
      if (ifdotagschecked==false){
        do {        
          if (String(elem.checked) != String(elem.defaultChecked)) {
            //at least on element to save, so the array
            //nothing more to check
            ifdotagschecked=true;
            result = true; //save
            i++;
            break;//leave the do..while
          }
	        i++;//next element
	        elem = what.elements[i];
	        elemType = what.elements[i].type;
	        myName = what.elements[i].name;

        } while (myName=="jg_ifdotags[]");

        if (!result){
          //no element in array to save, so delete the array
          todelete[todeletecount++] = "jg_ifdotags[]";        
        }
        i--;
        ifdotagschecked=true;
        continue;               

      }else{
        continue;
      }
    } else if (myName=="jg_subifdtags[]"){
      if (subifdtagschecked==false){
        do {        
          if (String(elem.checked) != String(elem.defaultChecked)) {
            //at least on element to save, so the array
            //nothing more to check
            subifdtagschecked=true;
            result = true; //save
            i++;
            break;//leave the do..while
          }
	        i++;//next element
	        elem = what.elements[i];
	        elemType = what.elements[i].type;
	        myName = what.elements[i].name;

        } while (myName=="jg_subifdtags[]");

        if (!result){
          //no element in array to save, so delete the array
          todelete[todeletecount++] = "jg_subifdtags[]";        
        }
        i--;
        subifdtagschecked=true;
        continue;               

      }else{
        continue;
      }
    } else if (myName=="jg_iptctags[]"){
      if (iptctagschecked==false){
        do {        
          if (String(elem.checked) != String(elem.defaultChecked)) {
            //at least on element to save, so the array
            //nothing more to check
            iptctagschecked=true;
            result = true; //save
            i++;
            break;//leave the do..while
          }
	        i++;//next element
	        elem = what.elements[i];
	        elemType = what.elements[i].type;
	        myName = what.elements[i].name;
        } while (myName=="jg_iptctags[]");

        if (!result){
          //no element in array to save, so delete the array
          todelete[todeletecount++] = "jg_iptctags[]";        
        }
        i--;
        iptctagschecked=true;
        continue;
      }else{
        continue;
      }
    }
      if (elemType == "checkbox" ) {
        if (String(elem.checked) != String(elem.defaultChecked)) {
          result = true; //save           
        } else {
          todelete[todeletecount++] = myName;
        } 
      } else if (elemType == "text") {
        if (String(elem.value) == String(elem.defaultValue)) {
          todelete[todeletecount++] = myName;        
        } else {
          result = true; //save
        }
      } else if (elemType == "select-one" || elemType == "select-multiple" ) {
        var l=elem.options.length;
        for (var k=0; k<l; k++) {
          if (String(elem.options[k].selected) != String(elem.options[k].defaultSelected)) {
            result = true; //save
            break;
          }
        }
        if (!result) {
          todelete[todeletecount++] = myName;        
        }
      }   
    }
  }

  for (var i=0; i < todeletecount; i++) {
    var elem = document.getElementsByName(todelete[i])[0];
	  if (elem.name=="jg_ifdotags[]" || elem.name=="jg_subifdtags[]" || 
	      elem.name=="jg_gpstags[]" || elem.name=="jg_iptctags[]"){
	    //get all elements with this name
	    var listelements = document.getElementsByName(elem.name);
	    var listlength = listelements.length;
	    //and delete them
	    for (var j=0;j < listlength;j++){
	      var listelem = document.getElementsByName(elem.name)[0];
	      listelem.parentNode.removeChild(listelem);
	    }
	  } else {
	    elem.parentNode.removeChild(elem);
	  }
  }
}
