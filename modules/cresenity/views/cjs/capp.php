// require requirejs, jquery

var capp;
if(!capp) {
	capp = {};
}


(function () {
	capp.js_base_url = '<?php curl::base(); ?>capp/resource/js/';

	if (typeof capp.require !== 'function') {
		capp.require = function(name) {
			
		}
	}

}());



