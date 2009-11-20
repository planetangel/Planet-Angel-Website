/**
* @package		Joomla
* @subpackage	com_imagebrowser
* @copyright	Copyright (C) 2008 E-NOISE.COM LIMITED. All rights reserved.
* @license		GNU/GPL.
* @author 		Luis Montero [e-noise.com]
* @version 		0.1.7b
* Joomla! and com_imagebrowser are free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed they include or
* are derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

var ImageBrowserPluginPopup = {
	initialize: function() {
		o = this._getUriObject(window.self.location.href);
		//console.log(o);
		q = $H(this._getQueryObject(o.query));
		this.editor = decodeURIComponent(q.get('e_name'));
	},

	onok: function(thumb_url, full_url, width, height) {
		var tag = '';
		var popUpWinAttributes = '';
		if (thumb_url != '') {		  	
			tag += "<a href=\"Javascript:void open('components/com_imagebrowser/imagebrowser.popup.php?image=";
			tag += full_url+"', 'popUpWin', '";
			tag += "toolbar=no,location=no,directories=no,status=no,menubar=no";
			tag += ",scrollbars=no,resizable=no,copyhistory=yes,width="+width;
			tag += ",height="+height+",left=0,top=0,screenX=0,screenY=0";
			tag += "');\">";
			tag += "<img src=\""+thumb_url+"\" />";
			tag += "</a>";
		}
		window.parent.jInsertEditorText(tag, this.editor);
		return false;
	},

	_getQueryObject: function(q) {
		var vars = q.split(/[&;]/);
		var rs = {};
		if (vars.length) vars.each(function(val) {
			var keys = val.split('=');
			if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
		});
		return rs;
	},

	_getUriObject: function(u){
		var bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
		return (bits)
			? bits.associate(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'])
			: null;
	}
};

window.addEvent('domready', function() {
	ImageBrowserPluginPopup.initialize();
});
