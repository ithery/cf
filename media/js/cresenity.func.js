;(function($, window, document, undefined)
{
	$.cresenity = {
		_filesadded : "",
		
		_loadjscss:function(filename, filetype, callback){
			if (filetype=="js"){ //if filename is a external JavaScript file
				var fileref=document.createElement('script')
				fileref.setAttribute("type","text/javascript")
				fileref.setAttribute("src", filename)
			} else if (filetype=="css"){ //if filename is an external CSS file
				var fileref=document.createElement("link")
				fileref.setAttribute("rel", "stylesheet")
				fileref.setAttribute("type", "text/css")
				fileref.setAttribute("href", filename)
			}
			if (typeof fileref!="undefined") {
				//fileref.onload = callback;
				// IE 6 & 7
				fileref.onload=$.cresenity._handle_response_callback(callback);
				if (typeof(callback) === 'function') {
					fileref.onreadystatechange = function() {
						
						if (this.readyState == 'complete') {
							$.cresenity._handle_response_callback(callback);
						}
					}
				}
				document.getElementsByTagName("head")[0].appendChild(fileref);
				
			}
		},
		_removejscss: function (filename, filetype){
			var targetelement=(filetype=="js")? "script" : (filetype=="css")? "link" : "none"; //determine element type to create nodelist from
			var targetattr=(filetype=="js")? "src" : (filetype=="css")? "href" : "none"; //determine corresponding attribute to test for
			var allsuspects=document.getElementsByTagName(targetelement);
			for (var i=allsuspects.length; i>=0; i--){ //search backwards within nodelist for matching elements to remove
				if (allsuspects[i] && allsuspects[i].getAttribute(targetattr)!=null && allsuspects[i].getAttribute(targetattr).indexOf(filename)!=-1) {
					allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
				}
			}
		},
		_handle_response: function(data,callback) {
			
			
			
			if(data.css_require&&data.css_require.length>0) {
				for(var i=0;i<data.css_require.length;i++) {
					$.cresenity.require(data.css_require[i],'css');
				}
			}
			require(data.js_require,callback);
			return;
			$.cresenity._filesloaded=0;
			$.cresenity._filesneeded=0;
			if(data.css_require&&data.css_require.length>0) $.cresenity._filesneeded+=data.css_require.length;
			if(data.js_require&&data.js_require.length>0) $.cresenity._filesneeded+=data.js_require.length;
			console.log('needed:'+$.cresenity._filesneeded);
			if(data.css_require&&data.css_require.length>0) {
				for(var i=0;i<data.css_require.length;i++) {
					$.cresenity.require(data.css_require[i],'css',callback);
				}
			}
			if(data.js_require&&data.js_require.length>0) {
				for(var i=0;i<data.js_require.length;i++) {
					$.cresenity.require(data.js_require[i],'js',callback);
				}
			}
			if($.cresenity._filesloaded==$.cresenity._filesneeded) {
				callback();
			}
		},
		_handle_response_callback: function (callback) {
			$.cresenity._filesloaded++;
			console.log('dynamic loaded:'+$.cresenity._filesloaded);
			if($.cresenity._filesloaded==$.cresenity._filesneeded) {
				callback();
			}
		},
		require:function(filename,filetype,callback) {
			if ($.cresenity._filesadded.indexOf("["+filename+"]")==-1){
				$.cresenity._loadjscss(filename,filetype,callback);
				$.cresenity._filesadded+="["+filename+"]" //List of files added in the form "[filename1],[filename2],etc"
			} else {
				$.cresenity._filesloaded++;
				console.log('already loaded:'+$.cresenity._filesloaded);
				if($.cresenity._filesloaded==$.cresenity._filesneeded) {
					callback();
				}
			}
		},
		
		days_between:function(date1, date2) {

		  // The number of milliseconds in one day
		  var ONE_DAY = 1000 * 60 * 60 * 24

		  // Convert both dates to milliseconds
		  var date1_ms = date1.getTime()
		  var date2_ms = date2.getTime()

		  // Calculate the difference in milliseconds
		  var difference_ms = Math.abs(date1_ms - date2_ms)

		  // Convert back to days and return
		  return Math.round(difference_ms/ONE_DAY)

		},	
		set_confirm: function(selector) {
			$(selector).click(function(e) {
				var ahref = $(this).attr('href');
				e.preventDefault();
				e.stopPropagation();
				bootbox.confirm("Are you sure?", function(confirmed) {
					if(confirmed) {
						window.location.href=ahref;
					}
				});
			});
		},
		is_number : function (n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		},
		get_dialog: function(dlg_id,title) {
			
			var div_content = $('body #'+dlg_id+' #'+dlg_id+'_content');
			if(div_content.length) {
				$('body #'+dlg_id+' #'+dlg_id+'_header h3').html(title);
				return div_content;
			}
			if(title=="undefined") title = "";
			if(!title) title = "";
			//not exists create the modal div
			var div = $('<div>').attr('id',dlg_id);
			var btnClose = '<a href="'+'javascript:;'+'" class="close" data-dismiss="modal">&times;</a>';
			btnClose = '';
			div.append('<div class="modal-header" id="'+dlg_id+'_header">'+btnClose+'<h3>'+title+'</h3></div>')
			div_content = $('<div class="modal-body" id="'+dlg_id+'_content"></div>');
			div.append(div_content);

			var btn_close = $('<a id="'+dlg_id+'_close">').addClass('btn').attr('href','javascript:void(0)');
			btn_close.append('<i class="icon icon-close"></i> Close');
			btn_close.click(function() {
				
				$('#'+dlg_id+'').modal('hide');
				$('#'+dlg_id+'').remove();
			});
			div_footer = $('<div class="modal-footer" id="suspended_dlg_footer"></div>');

			div_footer.append(btn_close);
			div.append(div_footer);
			div.css('overflow','hidden');
			div.addClass('modal');
			// stick the modal right at the bottom of the main body out of the way
			$("body").append(div);
			
			
			return div_content;
			

		},
		message: function(type,message,alert_type) {
			alert_type = typeof alert_type !== 'undefined' ? alert_type : 'notify';
			var container = $('#container');
			if(alert_type=='bootbox') {
				bootbox.alert(message);
			}
			
			if(alert_type=='notify') {
				obj = $('<div>');
				container.prepend(obj);
				obj.addClass('notifications');
				obj.addClass('top-right');
				
				
				obj.notify({
					'message': { text: message },
					'type': type
				}).show();	
			}
			
		},
		thousand_separator: function(rp) {

			rp =""+rp;
			var rupiah = "";
			var vfloat = "";
			
			var minus_str = "";
			
			if (rp.indexOf("-")>=0) {
				minus_str = rp.substring(rp.indexOf("-"),1);
				rp = rp.substring(rp.indexOf("-")+1);
			}
			
			if (rp.indexOf(".")>=0) {
				vfloat = rp.substring(rp.indexOf("."));
				rp = rp.substring(0,rp.indexOf("."));
			}
			p = rp.length;
			while(p > 3) {
				rupiah = "," + rp.substring(p-3) + rupiah;
				l = rp.length - 3;
				rp = rp.substring(0,l);
				p = rp.length;
			}
			rupiah = rp + rupiah;
			if (vfloat.length>2) vfloat = vfloat.substring(0,3);
			return minus_str+rupiah+vfloat;
		},


		base64: {
			
			encode: function(input) {
				var keyStr = "ABCDEFGHIJKLMNOP" +
					"QRSTUVWXYZabcdef" +
					"ghijklmnopqrstuv" +
					"wxyz0123456789+/" +
					"=";

				input = escape(input);
				var output = "";
				var chr1, chr2, chr3 = "";
				var enc1, enc2, enc3, enc4 = "";
				var i = 0;
	
				do {
					chr1 = input.charCodeAt(i++);
					chr2 = input.charCodeAt(i++);
					chr3 = input.charCodeAt(i++);

					enc1 = chr1 >> 2;
					enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
					enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
					enc4 = chr3 & 63;

					if (isNaN(chr2)) {
					   enc3 = enc4 = 64;
					} else if (isNaN(chr3)) {
					   enc4 = 64;
					}

					output = output +
					   keyStr.charAt(enc1) +
					   keyStr.charAt(enc2) +
					   keyStr.charAt(enc3) +
					   keyStr.charAt(enc4);
					chr1 = chr2 = chr3 = "";
					enc1 = enc2 = enc3 = enc4 = "";
				} while (i < input.length);

				return output;
			},
			decode: function(input) {
				var keyStr = "ABCDEFGHIJKLMNOP" +
					"QRSTUVWXYZabcdef" +
					"ghijklmnopqrstuv" +
					"wxyz0123456789+/" +
					"=";
				var output = "";
				var chr1, chr2, chr3 = "";
				var enc1, enc2, enc3, enc4 = "";
				var i = 0;

				// remove all characters that are not A-Z, a-z, 0-9, +, /, or =
				var base64test = /[^A-Za-z0-9\+\/\=]/g;
				if (base64test.exec(input)) {
					alert("There were invalid base64 characters in the input text.\n" +
						  "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
						  "Expect errors in decoding.");
				}
				input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

				do {
					enc1 = keyStr.indexOf(input.charAt(i++));
					enc2 = keyStr.indexOf(input.charAt(i++));
					enc3 = keyStr.indexOf(input.charAt(i++));
					enc4 = keyStr.indexOf(input.charAt(i++));

					chr1 = (enc1 << 2) | (enc2 >> 4);
					chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
					chr3 = ((enc3 & 3) << 6) | enc4;

					output = output + String.fromCharCode(chr1);

					if (enc3 != 64) {
					   output = output + String.fromCharCode(chr2);
					}
					if (enc4 != 64) {
					   output = output + String.fromCharCode(chr3);
					}

					chr1 = chr2 = chr3 = "";
					enc1 = enc2 = enc3 = enc4 = "";

				} while (i < input.length);

				return unescape(output);
			}
		},
		url: {
			add_query_string: function(url,key,value) {
				key = encodeURI(key); 
				value = encodeURI(value);
				var url_array = url.split('?');
				var query_string = '';
				var base_url = url_array[0];
				if(url_array.length>1) query_string =  url_array[1];
				var kvp = query_string.split('&');
				var i=kvp.length; var x; 
				
				while(i--) {
					x = kvp[i].split('=');

					if (x[0]==key) {
						x[1] = value;
						kvp[i] = x.join('=');
						break;
					}
				}

				if(i<0) {kvp[kvp.length] = [key,value].join('=');}

				
				query_string =  kvp.join('&'); 
				if(query_string.substr(0,1)=='&') query_string=query_string.substr(1);
				return base_url+'?'+query_string;
			},
			replace_param: function(url) {
				var available = true;
				while (available) {
					matches = url.match(/{([\w]*)}/);
					
					if(matches!=null) {
						var key = matches[1];
						
						var val = null;
						if($('#'+key).length>0) {
							var val = $.cresenity.value('#'+key);
							
						}
						
						if(val==null) {
							val = key;
						}
						
						url = url.replace('{'+key+'}',val);
						
						
					} else {
						available = false;
					}
					
				}
				return url;
			}
		},
		reload : function(id_target,url,method,data_addition) {
			
			if(!method) method="get";
			var xhr = jQuery('#'+id_target).data('xhr');
			if(xhr) xhr.abort();
			
			url = $.cresenity.url.replace_param(url);
			
			
			if(typeof data_addition == 'undefined') data_addition={};
			url = $.cresenity.url.add_query_string(url,'capp_current_container_id',id_target);
			jQuery('#'+id_target).addClass('loading');
			jQuery('#'+id_target).empty();
			jQuery('#'+id_target).append(jQuery('<div>').attr('id',id_target+'-loading').css('text-align','center').css('margin-top','100px').css('margin-bottom','100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x')))
			
			jQuery('#'+id_target).data('xhr',jQuery.ajax({
					type: method,
					url: url,
					dataType: 'json',
					data: data_addition,
					
				}).done(function( data ) {
					
					$.cresenity._handle_response(data,function() {
						jQuery('#'+id_target).html(data.html);
						var script = $.cresenity.base64.decode(data.js);
						console.log(script);
						eval(script);
						
						jQuery('#'+id_target).removeClass('loading');
						jQuery('#'+id_target).data('xhr',false);
						if(jQuery('#'+id_target).find('.prettyprint').length>0) {
							window.prettyPrint && prettyPrint();

						}
					});
				}).error(function(obj,t,msg) {
					if(msg!='abort') {
						$.cresenity.message('error','Error, please call administrator... (' + msg + ')');
					}
				})
			);
		},
		show_dialog : function(id_target,url,method,title,data_addition) {
			if(!title) title = 'Dialog';
			
			if(typeof data_addition == 'undefined') data_addition={};
			
			var _dialog_html = "<div class='modal' style=\"display: none;\">" + 
				"<div class='modal-header loading'>" +
				"<a href='#' class='close'></a>" + 
				"<span class='loader'></span><h3></h3>" + 
				"</div>" + 
				"<div class='modal-body'>" + 
				"</div>" + 
				"<div class='modal-footer'>" + 
				"</div>" + 
				"</div>";
			
			
			var selection = jQuery('#'+id_target);
            var handle;
			var dialog_is_remove = false;
			if(selection.length==0) {
				selection = jQuery('<div/>').attr('id',id_target);
				dialog_is_remove = true;
			}
			
			
			
			
			url = $.cresenity.url.add_query_string(url,'capp_current_container_id',id_target);
			if (!selection.is(".modal-body")) {
				var overlay = $('<div class="modal-backdrop"></div>').hide();
				var parent = $(_dialog_html);
				
				jQuery(".modal-header a.close", parent).text(unescape("%D7")).click(function(event) {
					event.preventDefault();
					if(dialog_is_remove) {
						handle.parent().prev(".modal-backdrop").remove();
						jQuery(this).parents(".modal").find(".modal-body").parent().remove();
					} else {
						handle.parent().prev(".modal-backdrop").hide();
						jQuery(this).parents(".modal").find(".modal-body").parent().hide();

					}
				});
				
				jQuery("body").append(overlay).append(parent);
				jQuery(".modal-header h3", parent).html(title);
				handle = $(".modal-body", parent);
				// Create dialog body from current jquery selection
				// If specified body is a div element and only one element is 
				// specified, make it the new modal dialog body
				// Allows us to do something like this 
				// $('<div id="foo"></div>').dialog2(); $("#foo").dialog2("open");
				if (selection.is("div") && selection.length == 1) {
					handle.replaceWith(selection);
					selection.addClass("modal-body").show();
					handle = selection;
				}
				// If not, append current selection to dialog body
				else {
					handle.append(selection);
				}
			} else {
                handle = selection;
            }
			if(!method) method="get";
			var xhr = handle.data('xhr');
			if(xhr) xhr.abort();
			
			url = $.cresenity.url.replace_param(url);
			jQuery('#'+id_target).append(jQuery('<div>').attr('id',id_target+'-loading').css('text-align','center').css('margin-top','100px').css('margin-bottom','100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x')))
			if (!handle.is(".opened")) {
                overlay.show();
                
                handle.addClass("opened").parent().show();
				
                    
               
            }
			handle.data('xhr',jQuery.ajax({
					type: method,
					url: url,
					dataType: 'json',
					data: data_addition,
					
				}).done(function( data ) {
					
					$.cresenity._handle_response(data,function() {
						jQuery('#'+id_target).html(data.html);
						var script = $.cresenity.base64.decode(data.js);
						eval(script);
						jQuery('#'+id_target).removeClass('loading');
						jQuery('#'+id_target).data('xhr',false);
						if(jQuery('#'+id_target).find('.prettyprint').length>0) {
							window.prettyPrint && prettyPrint();

						}
						if(data.title) {
							jQuery('#'+id_target+'').parent().find('.modal-header h3').html(data.title);
						}
					
					});
				}).error(function(obj,t,msg) {
					if(msg!='abort') {
						$.cresenity.message('error','Error, please call administrator... (' + msg + ')');
					}
				})
			);
			
			
            
           
		},
		value : function(elm) {
			elm = jQuery(elm);
			if(elm.length==0) return null;
			if(elm.val()!='undefined') {
				return elm.val();
			}
			if(elm.attr('value')!='undefined') {
				return elm.attr('value');
			}
			return elm.html();
		},
		dialog: {
			alert: function(message,options) {
				$.fn.dialog2.helpers.alert(message, {} );
			},
			prompt: function(message,options) {
        		$.fn.dialog2.helpers.prompt(message, {} );
			},
			confirm: function(message,options) {
        		$.fn.dialog2.helpers.confirm(message, {} );
			},
			show: function(selector,options) {
				
        		$(selector).dialog2(options);
			}
		},
		
		fullscreen: function(element) {
			
			
			if (!$('body').hasClass("full-screen")) {

				$('body').addClass("full-screen");

				if (element.requestFullscreen) {
					element.requestFullscreen();
				} else if (element.mozRequestFullScreen) {
					element.mozRequestFullScreen();
				} else if (element.webkitRequestFullscreen) {
					element.webkitRequestFullscreen();
				} else if (element.msRequestFullscreen) {
					element.msRequestFullscreen();
				}

			} else {
				
				$('body').removeClass("full-screen");
				
				if (document.exitFullscreen) {
					document.exitFullscreen();
				} else if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen();
				} else if (document.webkitExitFullscreen) {
					document.webkitExitFullscreen();
				}

			}

		}
	}
})(this.jQuery, window, document);
