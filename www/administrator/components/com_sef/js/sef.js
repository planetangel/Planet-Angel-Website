/* SEF Advance AJAX calls */

var big = document.createElement('img');
big.src = 'components/com_sef/ajax.php?image=1';
big.width = '130';
big.height = '60';

var little = document.createElement('img');
little.src = 'components/com_sef/ajax.php?image=2';
little.width = '13';
little.height = '13';

function makeGETRequest(url, opt) {
	var http_request = false;
	
	if (opt=='DivRss') {
	    var img = big;
	} else {
	    var img = little;
	}

	if (window.XMLHttpRequest) { // Mozilla, Safari,...
	    http_request = new XMLHttpRequest();
	    if (http_request.overrideMimeType) {
		http_request.overrideMimeType('text/html');
	    }
	} else if (window.ActiveXObject) { // IE
	    try {
		http_request = new ActiveXObject("Msxml2.XMLHTTP");
	    } catch (e) {
		try {
		    http_request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {}
	    }
	}

	if (!http_request) {
	    alert('Giving up :( Can not create XMLHTTP instance');
	    return false;
	}

	document.getElementById(opt).innerHTML = '';
	document.getElementById(opt).appendChild(img);
	document.getElementById(opt).ajaxInProgress = true;

	http_request.onreadystatechange = function() {
		processContents(http_request, opt);
	}
	http_request.open('GET', url, true);
	http_request.send(null);
}

function processContents(http_request, opt) {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			document.getElementById(opt).innerHTML = http_request.responseText;
		} else {
			document.getElementById(opt).innerHTML = 'Request failed.';
		}
	}
}

// META char counters
function add_meta_counters() {
	update_cnt('title');
	update_cnt('metadesc');
	update_cnt('metakey');
}

function update_cnt(id) {
	if (id=='title') {
		var max = 64;
	} else if (id=='metadesc') {
		var max = 150;
	} else {
		var max = 250;
	}
	var fd = document.getElementById(id);
	var el = document.getElementById(id+'_cnt');
	el.innerHTML = max - fd.value.length;
	if (max - fd.value.length < 0) {
		el.className += ' red';
	} else {
		el.className = 'cnt';
	}
}