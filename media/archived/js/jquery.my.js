function show_message(msg,close_func) {
    var id = "dialog_message";
	$('body').append('<div id="'+id+'" title="Message" style="display:none;">'+msg+'</div>');
	$('#dialog_message').dialog({
		buttons : {
			"Close": function() {
				$('#'+id).dialog('close');
			},						  
		},
		width:'400px',
		height:'80px',
		modal:true,
		close:function(event,ui) {
		  $('#'+id).remove();
		  if(close_func) close_func();
		}
	});
}



function show_loading(content) {
    var id = "dialog_loading";
	$('body').append('<div id="'+id+'" title="Loading" style="display:none;">'+content+'</div>');
	$('#'+id).dialog({
		buttons : {
		  
		},
		width:'auto',
		height:'auto',
		modal:true,
		close:function(event,ui) {
		  $('#'+id).remove();
		}
	});
}

function remove_loading() {
	var id = "dialog_loading";
	$('#'+id).remove();
}