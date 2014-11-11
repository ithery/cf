;

//this will return batch object
//{}

if(!$.cresenity) {
	$.cresenity = {};
}
$.cresenity.item_batch = {
	dialog : function(options) {
		var ibatch = {
			dlg:false,
			options:false,
			remain:0,
			batch_data:[],
			setup: function(options) {
				ibatch.dlg = ibatch.create_dialog('item_batch',options.title);
				ibatch.options = options;
				ibatch.remain = options.required_qty;
				if(ibatch.batch_data.length<=0) {
					ibatch.batch_data=options.default_batch;
				}
				ibatch.add_batch_select2();
				ibatch.add_batch_table();
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
					
					if(ibatch.check_save()) {
						$('#'+dlg_id+'').modal('hide');
						$('#'+dlg_id+'').remove();
						ibatch.batch_data = ibatch.get_data();
						
						if(ibatch.options.success_callback) {
							ibatch.options.success_callback(ibatch.get_data(),ibatch.options.param);
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
			add_batch_select2 : function() {
				var dlg = ibatch.dlg;
				var opt = ibatch.options;
				var rf = jQuery('<div class="row-fluid"></div>');
				var span12 = jQuery('<div class="span12"></div>');
				var ib_select2 = jQuery('<input type="hidden" name="item_batch_select2" id="item_batch_select2" />');
				
				
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
						
						var html_right = 'Exp:'+item.expired_date;
						if(item.qty_available) {
							html_right += ',Qty: '+item.qty_available;
							if(ibatch.options.use_unit) {
								html_right += ' '+ibatch.options.unit.unit_name;
							}
						}
						
						var html =  '<div>'+item.name+'<span class="pull-right">'+html_right+'</span></div>';
						//console.log(html);
						return html;
					}, 
					formatSelection: function(item) {
						return ''+item.name+'';
					}, 
					dropdownCssClass: 'bigdrop' // apply css that makes the dropdown taller
				}).change(function() {
					//add to table
					var batch_id = $(this).val();
					var data = jQuery(this).select2('data');
					ibatch.add_batch(data.batch_id,data.name,1,data.expired_date,data.qty_available);
					jQuery(this).select2("data",null);
				});
				
			},
			add_batch_table: function() {
				var dlg = ibatch.dlg;
				var opt = ibatch.options;
				var table = $('<table id="table_tr">');
				var thead = jQuery('<thead>');
				thead.append('<th>Batch</th>');
				if(ibatch.options.have_qty_available) {
					thead.append('<th>Available</th>');
				}
				thead.append('<th>Qty</th>');
				if(ibatch.options.use_unit) {
					thead.append('<th>Unit</th>');
				}
				thead.append('<th>Expired Date</th>');
				thead.append('<th>Action</th>');
				
				table.append(thead);
				var tr;
				var tbody=$('<tbody>');
				table.append(tbody);
				
				
				
				
				table.append('</tbody>');
				table.append('</table>');
				table.addClass('table table-bordered table-striped');
				dlg.append(table);
				
				for(k in ibatch.batch_data) {
					var data = ibatch.batch_data[k];
					
					ibatch.add_batch(data.batch_id,data.name,data.qty,data.expired_date,data.qty_available);
					
					
					
				}
				var rf_req = jQuery('<div class="row-fluid"></div>');
				var span12_req = jQuery('<div class="span12"></div>');
				var span_req = jQuery('<span class="item_batch_required_qty" >'+opt.required_qty+'</span>');
				
				
				span12_req.append('Required :').append(span_req);
				if(ibatch.options.use_unit) {
					span12_req.append(' '+ibatch.options.unit.unit_name);
				}
				rf_req.append(span12_req);
				rf_req.css('margin-bottom','10px');
				table.before(rf_req);
				
				var rf_rem = jQuery('<div class="row-fluid"></div>');
				var span12_rem = jQuery('<div class="span12"></div>');
				var span_rem = jQuery('<span class="item_batch_remain_qty" >'+ibatch.remain+'</span>');
				
				
				span12_rem.append('Remain :').append(span_rem);
				if(ibatch.options.use_unit) {
					span12_rem.append(' '+ibatch.options.unit.unit_name);
				}
				rf_rem.append(span12_rem);
				rf_rem.css('margin-bottom','10px');
				table.after(rf_rem);

			},
			add_batch: function(batch_id,name,qty,expired_date,qty_available) {
				var tr = jQuery('<tr>').attr('batch_id',batch_id);
				if(ibatch.options.have_qty_available) {
					tr.attr('qty-available',qty_available);
				} else {
					tr.attr('qty-available','-1');
				}
				
				tr.append(jQuery('<td class="tdname">').html(name));
				if(ibatch.options.have_qty_available) {
					var inner = qty_available;
					if(ibatch.options.use_unit) {
						inner+= ' '+ibatch.options.unit.unit_name;
					}
					tr.append(jQuery('<td class="tdqtyavailable">').html(inner));
				}
				var input_qty = jQuery('<input type="text" class="small" value='+qty+'>');
				tr.append(jQuery('<td class="tdqty">').html(input_qty));
				
				if(ibatch.options.use_unit) {
					var unit_input = ibatch.options.unit.unit_name;
					if(ibatch.options.use_unit_conversion) {
						unit_input = $('<select id="unit_id" name="unit_id[]" class="select unit_select" style="width:100px"></select>');
						
						var ucs = ibatch.options.unit.unit_conversion;
						for(var i=0; i<ucs.length; i++) {
							var uc = ucs[i];
							var selected = '';
							if(uc.unit_id==ibatch.options.unit.unit_id) selected = ' selected="selected"';
							unit_input.append($('<option value="'+uc.unit_id+'" data-qty_conversion="'+uc.qty_conversion+'"'+selected+'>'+uc.unit_name+'</option>'));
						}
						unit_input.change(function() {
							ibatch.refresh();
						});
					
					}
					tr.append(jQuery('<td>').addClass('align-center').addClass('tdunit').append(unit_input));
				}
			
				tr.append(jQuery('<td class="tdexpired">').html(expired_date));

				var btdel = $('<a>').addClass('btn btn-danger item_batch_delete').attr('href','javascript:void(0)').append('<i class="icon icon-trash"></i>');
				tr.append($('<td>').addClass('align-center').append(btdel));
				//tr.append('<td class="align-center"><a class="btn btn-danger note_av_delete" href="javascript:void(0);"><i class="icon icon-trash"></i></a></td>');
				btdel.click(function() {
					$(this).parent().parent().remove();
					ibatch.refresh();
				});
				
				input_qty.blur(function() {
					ibatch.refresh();
				});
				
				ibatch.dlg.find('table').append(tr);
				ibatch.refresh();
			},
			refresh: function() {
				var table = ibatch.dlg.find('table');
				var total_qty = 0;
				table.find('tbody tr').each(function() {
					var qty = jQuery(this).find('td.tdqty input').val();
					qty = parseFloat(qty);
					if(!qty) qty = 0;
					qty_conversion = 1;
					if(ibatch.options.use_unit&&ibatch.options.use_unit_conversion) {
						var unit_selected_id = jQuery(this).find('td.tdunit select').val();
						var ucs = ibatch.options.unit.unit_conversion;
						for(var i=0; i<ucs.length; i++) {
							var uc = ucs[i];
							if(uc.unit_id==unit_selected_id) {
								qty_conversion = uc.qty_conversion;
								break;
							}

						}
					}
					qty_after_conversion = parseFloat(qty_conversion)*parseFloat(qty);
					total_qty = parseFloat(total_qty)+parseFloat(qty_after_conversion);
				
				});
				//console.log(ibatch.options.required_qty);
				var rem = parseFloat(ibatch.options.required_qty) - parseFloat(total_qty);
				ibatch.remain = rem;
				
				jQuery('.item_batch_remain_qty',ibatch.dlg).html(ibatch.remain);
			},
			get_data: function() {
				var table = ibatch.dlg.find('table');
				var data = {};
				var i=0;
				table.find('tbody tr').each(function() {
					var tr = jQuery(this);
					var qty = tr.find('td.tdqty input').val();
					qty = parseFloat(qty);
					if(!qty) qty = 0;
					qty_conversion = 1;
					if(ibatch.options.use_unit&&ibatch.options.use_unit_conversion) {
						var unit_selected_id = jQuery(this).find('td.tdunit select').val();
						var ucs = ibatch.options.unit.unit_conversion;
						for(var j=0; j<ucs.length; j++) {
							var uc = ucs[j];
							if(uc.unit_id==unit_selected_id) {
								qty_conversion = uc.qty_conversion;
								break;
							}

						}
					}
					qty_after_conversion = parseFloat(qty_conversion)*parseFloat(qty);
					var d = {
						batch_id:tr.attr('batch_id'),
						name:tr.find('td.tdname').html(),
						unit_id:tr.find('td.tdunit select option:selected').val(),
						qty:qty_after_conversion,
						qty_available:tr.attr('qty-available'),
						expired_date:tr.find('td.tdexpired').html()
					}
					data[i]=d;
					i++;
				});
				
				return data;
			},
			check_save: function() {
				var error = 0;
				var error_message = '';
				ibatch.dlg.find('.item_batch_remain_qty').parent().removeClass('invalid');
				if(parseFloat(ibatch.remain)!=0) {
					error++;
					error_message = 'Required Qty not match';
					ibatch.dlg.find('.item_batch_remain_qty').parent().addClass('invalid');
				}
				if(error==0) {
					if(ibatch.options.have_qty_available) {
						ibatch.dlg.find('tbody tr td.tdqty').removeClass('invalid');
						
						ibatch.dlg.find('tbody tr').each(function() {
							var qty = jQuery(this).find('td.tdqty input').val();
							qty = parseFloat(qty);
							if(!qty) qty = 0;
							qty_conversion = 1;
							if(ibatch.options.use_unit&&ibatch.options.use_unit_conversion) {
								var unit_selected_id = jQuery(this).find('td.tdunit select').val();
								var ucs = ibatch.options.unit.unit_conversion;
								for(var i=0; i<ucs.length; i++) {
									var uc = ucs[i];
									if(uc.unit_id==unit_selected_id) {
										qty_conversion = uc.qty_conversion;
										break;
									}

								}
							}
							qty_after_conversion = parseFloat(qty_conversion)*parseFloat(qty);
							qty_available = parseFloat(jQuery(this).attr('qty-available'));
							if(qty_after_conversion>qty_available) {
								jQuery(this).find('td.tdqty').addClass('invalid');
								error++;
							}
						});
					}
					if(error>0) {
						error_message = 'Qty Not Available';
					}
				}
				
				if(error>0) {
					$.cresenity.message('error',error_message);
					return false;
				}
				return true;
			},
			showmodal: function() {
				ibatch.dlg.parent().modal();
			}
		}
		
		var option_default = {
			default_batch:{},
			title:'Item Batch',
			select2_placeholder:'Search Batch',
			select2_url:'',
			required_qty:'0',
			have_qty_available:false,
			use_unit:true,
			use_unit_conversion:true,
			unit:{},
			success_callback:null
		}
		options = $.extend({},option_default,options);
		
		var dialog_id = "item_batch_dialog";
		
		ibatch.setup(options);
		ibatch.showmodal();
		
	
	
	},
	create:function(selector) {
	
	}



};