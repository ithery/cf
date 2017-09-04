<?php
	csess::refresh_user_session();
	$app = CApp::instance();
	$user = $app->user();
	$user_photo = $user->user_photo;
	$imgsrc= curl::base().'cresenity/noimage/40/40';
	if(strlen($user_photo)>0) {
		$imgsrc=cimage::get_image_src("user_photo",$user->user_id,"thumbnail",$user_photo);
	}
?>
var myphoto = '<?php echo $imgsrc; ?>';
var chathb;
var hbcount = 0;
function add_message(user_msg_id,name,img,msg,time,clear,append) {
	i = i + 1;
	var  inner = $('#chat-messages-inner');
	var id = 'msg-'+i;
	var idname = name.replace(' ','-').toLowerCase();
	if(append) {
		inner.append('<p id="'+id+'" class="user-'+idname+' user-msg" user-msg-id="'+user_msg_id+'"><img src="'+img+'" alt="" />'+'<span class="msg-block"><strong>'+name+'</strong> <span class="time">- '+time+'</span>'+'<span class="msg">'+msg+'</span></span></p>');
		
	} else {
		inner.prepend('<p id="'+id+'" class="user-'+idname+' user-msg" user-msg-id="'+user_msg_id+'"><img src="'+img+'" alt="" />'+'<span class="msg-block"><strong>'+name+'</strong> <span class="time">- '+time+'</span>'+'<span class="msg">'+msg+'</span></span></p>');
		
	}
	$('#'+id).hide().fadeIn(800);
	if(clear) {
		$('.chat-message input').val('').focus();
	}
	
}

function check_message() {
	var  inner = $('#chat-messages-inner');
	var last_p = inner.find('p.user-msg:last');
	var last_id = '';
	if(last_p.length>0) {
		last_id = last_p.attr('user-msg-id');
	}
	user_to_id = '';
	var title_h5 = $('.widget-chat .widget-title h5');
	var user_to_id = title_h5.attr('user-id');
	
	hbcount++;
	chathb = $.ajax({
		type: 'GET',
		url: '<?php echo curl::base(); ?>index.php/cwidget/check_msg',
		async: true,
		cache: false,
		ifModified:true,
		dataType: 'json',
		data: { 'last_id':last_id, 'user_to_id':user_to_id},
		success: function(data) {
			if(data) {
				
				for(var i=data.length-1;i>=0;i--) {
					
					add_message(data[i].user_msg_id,data[i].username,data[i].user_photo,data[i].msg,data[i].created,false,true);
				}
				var  inner = $('#chat-messages-inner');
				$('#chat-messages').animate({ scrollTop: inner.height() },800);
			}
			
			var  inner = $('#chat-messages-inner');
			loading = inner.find('.loading');
			if(loading.length>0) {
				loading.remove();
				$('.chat-message input').focus();
			}
			setTimeout(check_message, 1000);
			
		},
		complete: function(data) {
			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			
			if (textStatus == 'abort') {
				setTimeout(check_message, 1000);
			}else if (textStatus == 'timeout') {
				setTimeout(check_message, 1000);
				//check_alarm();
			} else {
				//error
				setTimeout(check_message, 1000);
			}
			
		}
	});
}


function send_message(msg) {
	var  inner = $('#chat-messages-inner');
	if(inner.find('.loading').length==0) {
		var cl = $('<p class="align-center"></p>');
		cl.html('<i class=\"icon-spinner icon-spin icon-large center\"></i>');
		cl.addClass('loading');
		inner.append(cl);
	}
	var user_to_id = "";
	var title = $('.widget-chat .widget-title');
	title_h5 = title.find('h5');
	user_to_id = title_h5.attr('user-id');
	jQuery.ajax({
		type: 'post',
		url: '<?php echo curl::base(); ?>cwidget/add_user_msg',
		dataType: 'json',
		data: {'msg':msg,'user_to_id':user_to_id}
	}).complete(function( data ) {
		
		var  inner = $('#chat-messages-inner');
		$('#chat-messages').animate({ scrollTop: inner.height() },1000);
	});
}


function chat_with(li) {
	var title = $('.widget-chat .widget-title');
	title.find('#cmd-bc-message').show();
	title.find('.icon.icon-title').html('<img src="'+li.attr('user-photo')+'" title="'+li.attr('username')+'"/>');
	title_h5 = title.find('h5');
	title_h5.html(li.attr('username'));
	title_h5.attr('username',li.attr('username'));
	title_h5.attr('user-photo',li.attr('user-photo'));
	title_h5.attr('user-id',li.attr('user-id'));
	var  inner = $('#chat-messages-inner');
	inner.html('');
	if(hbcount>0) {
		chathb.abort();
	}
	
}

function chat_bc() {
	var title = $('.widget-chat .widget-title');
	title.find('#cmd-bc-message').hide();
	title.find('.icon.icon-title').html('<i class="icon-comment"></i>');
	title_h5 = title.find('h5');
	title_h5.html('Broadcast Message');
	title_h5.removeAttr('username');
	title_h5.removeAttr('user-photo');
	title_h5.removeAttr('user-id');
	var  inner = $('#chat-messages-inner');
	inner.html('');
	if(hbcount>0) {
		chathb.abort();
	}
	
	
	
}

function refresh_online_user() {
	var cl = $('.contact-list');
	cl.html('<i class=\"icon-spinner icon-spin icon-large center\"></i>');
	cl.addClass('loading');
	jQuery.ajax({
		type: 'get',
		url: '<?php echo curl::base(); ?>cwidget/online_user_json',
		dataType: 'json',
		data: {}
	}).done(function( data ) {
		var cl = $('.contact-list');
		cl.html('');
		cl.removeClass('loading');
		for(i=0;i<data.length;i++) {
			var li = jQuery('<li class="li-user"><a href="javascript:;"><img src="'+data[i].user_photo+'"><span>'+data[i].username+'</span></a></li>');
			li.addClass(data[i].user_online);
			li.attr('user-id',data[i].user_id);
			li.attr('username',data[i].username);
			li.attr('user-photo',data[i].user_photo);
			li.click(function() {
				chat_with($(this));
			});
			cl.append(li);
		}
		
	});
}

function load_prev_message() {
	var prev_btn = $('.chat-prev-messages');
	if(prev_btn.hasClass('no-message')) return;
	var inner = $('#chat-messages-inner');
	var first_p = inner.find('p.user-msg:first');
	var first_id = '';
	if(first_p.length>0) {
		first_id = first_p.attr('user-msg-id');
	}
	user_to_id = '';
	var title_h5 = $('.widget-chat .widget-title h5');
	var user_to_id = title_h5.attr('user-id');
	$('.chat-prev-messages').html('<i class=\"icon-spinner icon-spin icon-large center\"></i>');
	
	$.ajax({
		type: 'GET',
		url: '<?php echo curl::base(); ?>index.php/cwidget/prev_msg',
		async: true,
		cache: false,
		ifModified:true,
		dataType: 'json',
		data: { 'first_id':first_id, 'user_to_id':user_to_id},
		success: function(data) {
			if(data) {
				if(data.length==0) {
					$('.chat-prev-messages').addClass('no-message');
					$('.chat-prev-messages').html('No Previous Message');
				} else {
					$('.chat-prev-messages').html('Load Previous Message');
					for(var i=0;i<data.length;i++) {
						
						add_message(data[i].user_msg_id,data[i].username,data[i].user_photo,data[i].msg,data[i].created,false,false);
					}
					var  inner = $('#chat-messages-inner');
					$('#chat-messages').animate({ scrollTop: 0 },800);
				}
			}
			
			
			
		},
		complete: function(data) {
			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			
			
			
		}
	});
}

jQuery(document).ready(function() {
	refresh_online_user();
	
	$('.chat-message button').click(function(){
		var input = $(this).siblings('span').children('input[type=text]');		
		if(input.val() != ''){
			//add_message('You',myphoto,input.val(),true);
			send_message(input.val());
			$('.chat-message input').val('');
		}		
	});
	
	$('.chat-message input').keypress(function(e){
		if(e.which == 13) {	
			if($(this).val() != ''){
				//add_message('You',myphoto,$(this).val(),true);
				send_message($(this).val());
				$('.chat-message input').val('');
			}		
		}
	});
	$('#cmd-refresh-online-user').click(function(e) {
		refresh_online_user();
	});
	$('#cmd-bc-message').click(function(e) {
		chat_bc();
	});
	$('.chat-prev-messages').click(function(e) {
		load_prev_message();
	});
	setTimeout(check_message,1000);
});