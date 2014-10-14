jQuery(document).ready(function($) {
	var idjrpc = 1;
    $('#shell_console').terminal(function(command, term) {
		term.pause();
		
		$.jrpc("<?php echo curl::base(); ?>admin/core/shell_rpc",idjrpc++,'shell',[command],function(data) {
			term.resume();
			if (data.error) {
				term.error(data.error.message);
			} else {
				
				term.echo(data.result);
				
			}
		}, function(xhr, status, error) {
			term.error('[AJAX] ' + status + ' - Server reponse is: \n' + xhr.responseText);
			term.resume();
		});
	},{
		prompt: '>', 
		greetings: "Welcome to Shell Console\nOperating System: <?php echo PHP_OS; ?>\n\nPlease do not call windowable program !!!"
	});
});