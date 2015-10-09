


function init_infos(){
	// Dummy function
}


function update_url(url,name){
	var doc = window.document;
	if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
		if(typeof history.pushState == 'function') { 
			var stateObj = { foo: "bar" };
			history.pushState(stateObj, "PhotoShow - " + name, url);
		}
	}
}


