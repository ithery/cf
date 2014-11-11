;(function($, window, document, undefined)
{
	$.cresenity = {
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
		message: function(type,message) {
			
			var container = $('#container');
			
			obj = $('<div>');
			/*
			obj.addClass('alert');
			obj.addClass('alert-'+type);
			btnd = $('<button>').addClass('close').attr('data-dismiss','alert').html('&times;');;
			obj.append(btnd).append(message);
			*/
			container.prepend(obj);
			obj.addClass('notifications');
			obj.addClass('top-right');
			
			
			obj.notify({
				'message': { text: message },
				'type': type
			}).show();	
			
		},
		thousand_separator: function(rp) {

			rp =""+rp;
			var rupiah = "";
			var vfloat = "";
			
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
			return rupiah+vfloat;
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
		}

	}
})(this.jQuery, window, document);
