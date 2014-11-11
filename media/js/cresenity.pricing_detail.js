;

//this will return transaction object
//{}

if(!$.cresenity) {
	$.cresenity = {};
}

$.cresenity.pricing_detail = {
	dialog : function(options) {
		var itransaction = {
			dlg:false,
			options:false,
			total:0,
			transaction_data:[],
			setup: function(options) {
				itransaction.dlg = itransaction.create_dialog('pricing_detail',options.title);
				itransaction.options = options;
				if(itransaction.transaction_data.length<=0) {
					itransaction.transaction_data=options.default_data;
				}
				itransaction.add_transaction_select2();
				itransaction.add_transaction_table();
				itransaction.refresh();
			},
			create_dialog: function(dlg_id,title) {
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

				var btn_close = $('<a id="'+dlg_id+'_close">').addClass('btn btn-danger').attr('href','javascript:void(0)');
				btn_close.append('<i class="icon icon-close"></i> Close');
				btn_close.click(function() {
					
					$('#'+dlg_id+'').modal('hide');
					$('#'+dlg_id+'').remove();
				});
				var btn_save = $('<a id="'+dlg_id+'_save">').addClass('btn btn-success').attr('href','javascript:void(0)');
				btn_save.append('<i class="icon icon-ok"></i> Save');
				btn_save.click(function() {
					
					if(itransaction.check_save()) {
						$('#'+dlg_id+'').modal('hide');
						$('#'+dlg_id+'').remove();
						itransaction.transaction_data = itransaction.get_data();
						
						if(itransaction.options.success_callback) {
							itransaction.options.success_callback(itransaction.get_data(),itransaction.total,itransaction.options.param);
						}
					}
				});
				div_footer = $('<div class="modal-footer" id="suspended_dlg_footer"></div>');

				div_footer.append(btn_save);
				div_footer.append(btn_close);
				div.append(div_footer);
				div.css('overflow','hidden');
				div.addClass('modal');
				// stick the modal right at the bottom of the main body out of the way
				$("body").append(div);
				
				
				return div_content;
			},
			add_transaction_select2 : function() {
				var dlg = itransaction.dlg;
				var opt = itransaction.options;
				var rf = jQuery('<div class="row-fluid"></div>');
				var span12 = jQuery('<div class="span12"></div>');
				var ib_select2 = jQuery('<input type="hidden" name="pricing_detail_select2" id="pricing_detail_select2" />');
				
				
				span12.append(ib_select2);
				rf.append(span12);
				dlg.append(rf);
				rf.css('margin-bottom','10px');
				ib_select2.css('width','400px');
				ib_select2.select2({
					placeholder: opt.select2_placeholder,
					minimumInputLength: 0,
					ajax: { 
						url: opt.select2_url,
						dataType: 'jsonp',
						data: function (term, page) {
							return {
								term: term, // search term
								page: page,
								limit: 10
							};
						},
						
						results: function (data, page) { // parse the results into the format expected by Select2.
							var more = (page * 10) < data.total; // whether or not there are more results available

							// notice we return the value of more so Select2 knows if more results can be loaded
							return {results: data.data, more: more};
						}
					},
					formatResult: function(item) {
						//return '['+item.barcode+'] '+item.name+'';
						
						var html_right = '';
						if(itransaction.options.have_max_amount) {
							html_right = html_right+$.cresenity.thousand_separator(item.max_amount);
						}
						var html =  '<div>'+item.transaction_code+'<span class="pull-right">'+html_right+'</span></div>';
						//console.log(html);
						return html;
					}, 
					formatSelection: function(item) {
						return ''+item.transaction_code+'';
					}, 
					dropdownCssClass: 'bigdrop' // apply css that makes the dropdown taller
				}).change(function() {
					//add to table
					var transaction_id = $(this).val();
					var data = jQuery(this).select2('data');
					itransaction.add_transaction(data);
					jQuery(this).select2("data",null);
				});
				
			},
			add_transaction_table: function() {
				var dlg = itransaction.dlg;
				var opt = itransaction.options;
				var table = $('<table id="table_tr">');
				var thead = jQuery('<thead>');
				
				if(opt.have_transaction_code) {
					thead.append('<th>Code</th>');
				}
				if(opt.have_transaction_type) {
					thead.append('<th>Type</th>');
				}
				if(opt.have_transaction_date) {
					thead.append('<th>Date</th>');
				}
				if(opt.have_max_amount) {
					thead.append('<th>Max Amount</th>');
				}
				thead.append('<th>Amount</th>');
				thead.append('<th>Action</th>');
				
				table.append(thead);
				var tr;
				var tbody=$('<tbody>');
				table.append(tbody);
				
				
				
				
				table.append('</tbody>');
				table.append('</table>');
				table.addClass('table table-bordered table-striped');
				dlg.append(table);
				
				for(k in itransaction.transaction_data) {
					var data = itransaction.transaction_data[k];
					
					itransaction.add_transaction(data);
					
					
					
				}
				
				
				var rf_total = jQuery('<div class="row-fluid"></div>');
				var span12_total = jQuery('<div class="span12"></div>');
				var span_total = jQuery('<span class="pricing_detail_total_amount" >'+$.cresenity.thousand_separator(itransaction.total)+'</span>');
				
				
				span12_total.append('Total : ').append(span_total);
				
				rf_total.append(span12_total);
				rf_total.css('margin-bottom','10px');
				table.after(rf_total);

			},
			add_transaction: function(data) {
				var opt =itransaction.options;
				var tr = jQuery('<tr>').attr('id',data.id);
				if(opt.have_max_amount) {
					tr.attr('max-amount',data.max_amount);
				} else {
					tr.attr('max-amount','-1');
				}
				if(opt.have_transaction_code) {
					tr.append(jQuery('<td class="td_transaction_code">').html(data.transaction_code));
				}
				if(opt.have_transaction_type) {
					tr.append(jQuery('<td class="td_transaction_type">').html(data.transaction_type));
				}
				if(opt.have_transaction_date) {
					tr.append(jQuery('<td class="td_transaction_date">').html(data.transaction_date));
				}
				if(opt.have_max_amount) {
					tr.append(jQuery('<td class="td_transaction_max_amount">').html($.cresenity.thousand_separator(data.max_amount)));
				}
				
				var amount = 0;
				if(data.amount) {
					amount = data.amount;
				}
				
				var input_qty = jQuery('<input type="text" class="small input-amount" value='+amount+'>');
				tr.append(jQuery('<td class="td_amount">').html(input_qty));
				
			
				var btdel = $('<a>').addClass('btn btn-danger pricing_detail_delete').attr('href','javascript:void(0)').append('<i class="icon icon-trash"></i>');
				tr.append($('<td>').addClass('align-center').append(btdel));
				//tr.append('<td class="align-center"><a class="btn btn-danger note_av_delete" href="javascript:void(0);"><i class="icon icon-trash"></i></a></td>');
				btdel.click(function() {
					$(this).parent().parent().remove();
					itransaction.refresh();
				});
				
				input_qty.blur(function() {
					itransaction.refresh();
				});
				
				itransaction.dlg.find('table').append(tr);
				itransaction.refresh();
			},
			refresh: function() {
				var table = itransaction.dlg.find('table');
				var total_amount = 0;
				table.find('tbody tr').each(function() {
					var amount = jQuery(this).find('td.td_amount input').val();
					amount = parseFloat(amount);
					if(!amount) amount = 0;
					total_amount = parseFloat(total_amount)+parseFloat(amount);
				
				});
				//console.log(itransaction.options.required_qty);
				var tot = parseFloat(total_amount);
				itransaction.total = tot;
				
				jQuery('.pricing_detail_total_amount',itransaction.dlg).html($.cresenity.thousand_separator(itransaction.total));
			},
			get_data: function() {
				var table = itransaction.dlg.find('table');
				var data = {};
				var i=0;
				table.find('tbody tr').each(function() {
					var tr = jQuery(this);
					var amount = tr.find('td.td_amount input').val();
					amount = parseFloat(amount);
					
					var d = {
						id:tr.attr('id'),
						amount:amount
					};
					var opt = itransaction.options;
					if(opt.have_transaction_code) {
						d.transaction_code = tr.find('td.td_transaction_code').html();
					}
					if(opt.have_transaction_type) {
						d.transaction_type = tr.find('td.td_transaction_type').html();
					}
					if(opt.have_transaction_date) {
						d.transaction_date = tr.find('td.td_transaction_date').html();
					}
					if(opt.have_max_amount) {
						d.max_amount = tr.attr('max-amount')
					}
						
					data[i]=d;
					i++;
				});
				
				return data;
			},
			check_save: function() {
				var error = 0;
				var error_message = '';
				if(error==0) {
					if(itransaction.options.have_max_amount) {
						itransaction.dlg.find('tbody tr td.td_amount').removeClass('invalid');
						
						itransaction.dlg.find('tbody tr').each(function() {
							var amount = jQuery(this).find('td.td_amount input').val();
							amount = parseFloat(amount);
							if(!amount) amount = 0;
							
							max_amount = parseFloat(jQuery(this).attr('max-amount'));
							if(amount>max_amount) {
								jQuery(this).find('td.td_amount').addClass('invalid');
								error++;
							}
						});
					}
					if(error>0) {
						error_message = 'Amount Insufficient';
					}
				}
				
				if(error>0) {
					$.cresenity.message('error',error_message);
					return false;
				}
				return true;
			},
			showmodal: function() {
				itransaction.dlg.parent().modal();
			}
		}
		
		var option_default = {
			default_data:{},
			title:'Pricing Detail',
			select2_placeholder:'Search Transaction',
			select2_url:'',
			have_max_amount:true,
			have_transaction_type:true,
			have_transaction_code:true,
			have_transaction_date:true,
			success_callback:null
		}
		options = $.extend({},option_default,options);
		
		var dialog_id = "pricing_detail_dialog";
		
		itransaction.setup(options);
		itransaction.showmodal();
		
	
	
	},
	create:function(selector) {
	
	}



};