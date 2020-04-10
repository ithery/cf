<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CElement_Component_DataTable_Trait_JavascriptTrait {

    public function js($indent = 0) {
        $this->buildOnce();
        $ajax_url = "";
        if ($this->ajax) {
            $columns = array();
            foreach ($this->columns as $col) {
                $columns[] = $col;
            }


            $ajaxMethod = CAjax::createMethod();
            $ajaxMethod->setType('DataTable');
            $ajaxMethod->setData('columns', $columns);
            $ajaxMethod->setData('query', $this->query);
            $ajaxMethod->setData('table', serialize($this));
            $ajaxMethod->setData('dbConfig', $this->dbConfig);
            $ajaxMethod->setData('dbName', $this->dbName);
            $ajaxMethod->setData('domain', $this->domain);
            $ajaxMethod->setData('actionLocation', $this->actionLocation);
            $ajaxMethod->setData('checkbox', $this->checkbox);
            $ajaxMethod->setData('isElastic', $this->isElastic);
            $ajaxMethod->setData('isCallback', $this->isCallback);
            $ajaxMethod->setData('callbackRequire', $this->callbackRequire);
            $ajaxMethod->setData('callbackOptions', $this->callbackOptions);
            $ajax_url = $ajaxMethod->makeUrl();
        }

        foreach ($this->footer_action_list->childs() as $row_act) {
            $id = $row_act->id();
            if ((strpos($id, 'export_excel') !== false)) {
                $row_act->set_label('Download Excel')->set_icon('file');


                $action_url = CAjaxMethod::factory()->set_type('callback')
                        ->set_data('callable', array('CTable', 'action_download_excel'))
                        ->set_data('query', $this->query)
                        ->set_data('row_action_list', $this->rowActionList)
                        ->set_data('key_field', $this->key_field)
                        ->set_data('table', serialize($this))
                        ->makeurl();
                $row_act->add_listener('click')->add_handler('custom')->set_js("window.location.href='" . $action_url . "';");
            }
        }
        $js = new CStringBuilder();


        $js->setIndent($indent);


        $total_column = count($this->columns);
        if ($this->haveRowAction()) {
            $total_column++;
        }
        if ($this->checkbox) {
            $total_column++;
        }


        if ($this->apply_data_table > 0) {

            $length_menu = "";
            $km = "";
            $vm = "";
            foreach ($this->paging_list as $k => $v) {
                if (strlen($km) > 0)
                    $km .= ", ";
                if (strlen($vm) > 0)
                    $vm .= ", ";
                $km .= $k;
                $vm .= "'" . $v . "'";
            }
            $hs_val = $this->header_sortable ? "true" : "false";
            $js->appendln("var table = jQuery('#" . $this->id . "');")->br();
            $js->appendln("var header_sortable = " . $hs_val . ";")->br();
            $js->appendln("var vaoColumns = [];")->br();
            if ($this->numbering) {
                $aojson = array();
                $aojson["bSortable"] = false;
                $aojson["bSearchable"] = false;
                $aojson["bVisible"] = true;
                $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
                ;
            }
            if ($this->checkbox) {
                $aojson = array();
                $aojson["bSortable"] = false;
                $aojson["bSearchable"] = false;
                $aojson["bVisible"] = true;
                $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
            }

            if ($this->haveRowAction() && $this->actionLocation != 'last') {
                $aojson = array();
                $aojson["bSortable"] = false;
                $aojson["bSearchable"] = false;
                $aojson["bVisible"] = true;
                $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
            }
            foreach ($this->columns as $col) {
                $aojson = array();
                $aojson["bSortable"] = $col->sortable && $this->header_sortable;
                $aojson["bSearchable"] = $col->searchable;
                $aojson["bVisible"] = $col->visible;

                $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );");
            }
            if ($this->haveRowAction() && $this->actionLocation == 'last') {
                $aojson = array();
                $aojson["bSortable"] = false;
                $aojson["bSearchable"] = false;
                $aojson["bVisible"] = true;
                $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
            }



            $js->appendln("var tableStyled_" . $this->id . " = false;")->br()->
                    appendln("var oTable = table.dataTable({")->br()->incIndent();


//            $js->appendln("responsive: {
//        details: {
//            renderer: $.fn.dataTable.Responsive.renderer.tableAll()
//        }
//    },");
            if ($this->ajax) {
                $js->append("")
                        ->appendln("'bRetrieve': true,")->br()
                        ->appendln("'bProcessing': true,")->br()
                        ->appendln("'bServerSide': true,")->br()
                        ->appendln("'sAjaxSource': '" . $ajax_url . "',")->br()
                        ->appendln("'sServerMethod': '" . strtoupper($this->ajax_method) . "',")->br()
                        ->appendln("'fnServerData': function ( sSource, aoData, fnCallback, oSettings ) {
                                        var data_quick_search = [];
                                        jQuery('.data_table-quick_search').each(function(){
                                            if (jQuery(this).val() != '') {
                                                var input_name = jQuery(this).attr('name');
                                                var cur_transforms = jQuery(this).attr('transforms');
                                                data_quick_search.push({'name': input_name, 'value': jQuery(this).val(), 'transforms': cur_transforms});
                                            }
                                        });
                                        aoData.push({'name': 'dttable_quick_search', 'value': JSON.stringify(data_quick_search)});
                                        oSettings.jqXHR = $.ajax( {
                                            'dataType': 'json',
                                            'type': '" . strtoupper($this->ajax_method) . "',
                                            'url': sSource,
                                            'data': aoData,
                                            'success': function(data) {
                                                fnCallback(data.datatable);
                                                if(data.js && data.js.length>0) {
                                                    var script = $.cresenity.base64.decode(data.js);
                                                    eval(script);
                                                }
                                                jQuery('#" . $this->id . "-check-all').removeAttr('checked');
                                                jQuery('#" . $this->id . "-check-all').prop('checked',false);
                                            },
                                            'error': function(a,b,c) {
                                                $.cresenity.message(a);
                                            }
                                        })
                                    },
                                    ")
                        ->appendln("'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
						// Bold the grade for all 'A' grade browsers
						
							//$('td:eq(4)', nRow).html( '<b>A</b>' );
						//$.cresenity.set_confirm($('a.confirm',nRow));
						
						var footer_action = $('#" . $this->id . "_wrapper .footer_action');
						
						" . ($this->have_footer_action() ? "footer_action.html(" . json_encode($this->footer_action_list->html()) . ");" : "") . " 
           
						" . ($this->have_footer_action() ? "" . $this->footer_action_list->js() . "" : "") . " 
						
						footer_action.css('position','absolute').css('left','275px').css('margin','4px 8px 2px 10px');
						
						for(i=0;i<$(nRow).find('td').length;i++) {
							
							//get head data align
							var data_align = $('#" . $this->id . "').find('thead th:eq('+i+')').data('align');
							var data_action = $('#" . $this->id . "').find('thead th:eq('+i+')').data('action');
							var data_no_line_break = $('#" . $this->id . "').find('thead th:eq('+i+')').data('no-line-break');
							if(data_action) {
								$('td:eq('+i+')', nRow).addClass(data_action);
							}
							if(data_align) {
								$('td:eq('+i+')', nRow).addClass(data_align);
							}
							if(data_no_line_break) {
								$('td:eq('+i+')', nRow).addClass(data_no_line_break);
							}
						}
						
						
					},
				")
                        ->appendln("'fnInitComplete': function() {
                                this.fnAdjustColumnSizing(true);
                            },
                        ")
                ;
            }
            /*
              $js->append("")
              ->appendln("'sScrollX': '100%',")->br()
              ->appendln("'bScrollCollapse': true,")->br()
              ;
             */


            $jqueryui = "'bJQueryUI': false,";
            if (CClientModules::instance()->isRegisteredModule('jquery.ui') || CClientModules::instance()->isRegisteredModule('jquery-ui-1.12.1.custom')) {
                $jqueryui = "'bJQueryUI': true,";
            }
            if ($this->scrollX) {
                $scrollX = $this->scrollX;
                if (is_bool($scrollX)) {
                    $scrollX = 'true';
                }
                $js->appendln("scrollX:        " . $scrollX . ",")->br();
            }
            if ($this->scrollY) {
                $scrollY = $this->scrollY;
                if (is_bool($scrollY)) {
                    $scrollY = 'true';
                }
                $js->appendln("scrollY:        " . $scrollY . ",")->br();
            }
            if ($this->fixedColumn) {

                $js->appendln("scrollY:        300,")->br()
                        ->appendln("scrollX:        true,")->br()
                        ->appendln("scrollCollapse: true,")->br();
                if ($this->checkbox) {
                    $js->appendln("'fixedColumns': {
                        leftColumns: 2
                    },")->br();
                } else {
                    $js->appendln("'fixedColumns': " . ($this->fixedColumn ? "true" : "false") . ",")->br();
                }
            }

            /*
              $js->append("
              initComplete : function() {
              var input = $('#" . $this->id() . " .dataTables_filter input').unbind();
              var self = this.api();
              var searchButton = $('<button>')
              .text('search')
              .click(function() {
              self.search(input.val()).draw();
              });
              var clearButton = $('<button>')
              .text('clear')
              .click(function() {
              input.val('');
              searchButton.click();
              });

              $('#" . $this->id() . " .dataTables_filter').append(searchButton, clearButton);
              },
              ");
             * 
             */
            $js->appendln($jqueryui)->br()
                    ->appendln("'bStateSave': false,")->br()
                    ->appendln("'iDisplayLength': " . $this->display_length . ",")->br()
                    ->appendln("'bSortCellsTop': " . $hs_val . ",")->br()
                    ->appendln("'aaSorting': [],")->br()
                    ->appendln("'oLanguage': { 
						sSearch : '" . clang::__('Search') . "',
						sSearchPlaceholder : '" . clang::__($this->searchPlaceholder) . "',
						sProcessing : '" . clang::__('Processing') . "',
						sLengthMenu  : '" . clang::__('Show') . " _MENU_ " . clang::__('Entries') . "',
						oPaginate  : {
                                                    'sFirst' : '" . clang::__('First') . "',
                                                    'sLast' : '" . clang::__('Last') . "',
                                                    'sNext' : '" . clang::__('Next') . "',
                                                    'sPrevious' : '" . clang::__('Previous	') . "'
                                                },
                                                sInfo: '" . $this->infoText . "',
						sInfoEmpty  : '" . clang::__('No data available in table') . "',
						sEmptyTable  : '" . clang::__('No data available in table') . "',
						sInfoThousands   : '" . clang::__('') . "',
					},")->br()
                    ->appendln("'bDeferRender': " . ($this->getOption("bDeferRender") ? "true" : "false") . ",")->br()
                    ->appendln("'bFilter': " . ($this->getOption("bFilter") ? "true" : "false") . ",")->br()
                    ->appendln("'bInfo': " . ($this->getOption("bInfo") ? "true" : "false") . ",")->br()
                    ->appendln("'bPaginate': " . ($this->getOption("bPaginate") ? "true" : "false") . ",")->br()
                    ->appendln("'bLengthChange': " . ($this->getOption("bLengthChange") ? "true" : "false") . ",")->br()
                    ->appendln("'aoColumns': vaoColumns,")->br()
                    ->appendln("'autoWidth': false,")->br()
                    ->appendln("'aLengthMenu': [
					[" . $km . "],
					[" . $vm . "]
				],")->br()
            ;

            /*
              $js->append("")
              ->appendln("'sScrollX': '100%',")->br()
              ->appendln("'sScrollXInner': '100%',")->br()
              ->appendln("'bScrollCollapse': true,")->br()
              ;
             */

            // if ($this->bootstrap == '3') {
            if ($this->bootstrap >= '3') {
                if ($this->dom == null) {
                    $this->dom = "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>";
                }
            }

            if ($this->dom == null) {
                $this->dom = "<\"\"l>t<\"F\"<\".footer_action\">frp>";
            } else {
                $this->dom = str_replace("'", "\'", $this->dom);
            }
            $js->append("")
                    ->appendln("'sPaginationType': 'full_numbers',")->br()
                    ->appendln("'sDom': '" . $this->dom . "',")->br();



            $js->append("")
                    ->decIndent()->appendln("});")->br();



            $js->appendln('function buildFilters_' . $this->id . '() {')->br()
                    ->appendln("var quick_search = jQuery('<tr>');")->br()
                    ->appendln("
                        jQuery('#" . $this->id . " thead th').each( function (i) {
                            var title = jQuery('#" . $this->id . " thead th').eq( jQuery(this).index() ).text();
                            var have_action = " . ($this->haveRowAction() ? "1" : "0") . ";


                            var total_th = jQuery('#" . $this->id . " thead th').length;
                            var input = '';
                            var have_checkbox = " . ($this->checkbox ? "1" : "0") . ";

                            if((!(have_action==1&&(total_th-1==jQuery(this).index())))&& (!(have_checkbox==1&&(0==jQuery(this).index()))) ) {
                                var i2 = 0;
                                if(have_checkbox) {
                                        i2 = -1;
                                }

                                var all_column = " . json_encode($this->columns) . ";
                                var column = all_column[jQuery(this).index()+i2];
                                var transforms = {};
                                if(column) {
                                    if(hasOwnProperty.call(column, 'transforms')) {

                                        transforms = JSON.stringify(column.transforms);
                                    }

                                    if(column.searchable) {
                                        input = jQuery('<input>');
                                        input.attr('type', 'text');
                                        input.attr('name', 'dt_table_qs-' + jQuery(this).attr('field_name'));
                                        input.attr('class', 'data_table-quick_search');

                                        input.attr('transforms', transforms);
                                        input.attr('placeholder', 'Search ' + title );
                                    }
                                }

                            }
                            var td = jQuery('<td>').append(input);
                            quick_search.append(td);
                        });
                    ")->br()
                    ->appendln("table.children('thead').append(quick_search);")->br()
                    ->appendln('}')->br()
                    ->appendln('var dttable_quick_search = ' . ($this->quick_search ? "1" : "0") . ';')->br()
                    ->appendln('if (dttable_quick_search == "1") { buildFilters_' . $this->id . '(); }')
            ;

            $js->appendln("
                jQuery('.data_table-quick_search').on('keyup', function(){
                    table.fnClearTable( 0 );
                    table.fnDraw();
                });
            ");
        }
        if ($this->checkbox) {
            $js->appendln("
                jQuery('#" . $this->id . "-check-all').click(function() {
                        if(jQuery(this).is(':checked')) {
                                jQuery('.checkbox-" . $this->id . "').attr('checked','checked');
                                jQuery('.checkbox-" . $this->id . "').prop('checked',true);
                        } else {
                                jQuery('.checkbox-" . $this->id . "').removeAttr('checked');
                                jQuery('.checkbox-" . $this->id . "').prop('checked',false);
                        }
                });
            ");
        }
        $js->appendln($this->js_cell);
        if (!$this->ajax) {
            $js->append(parent::js($indent))->br();
            if (is_array($this->data)) {
                foreach ($this->data as $row) {
                    if ($row instanceof CRenderable) {
                        $js->appendln($row->js())->br();
                        continue;
                    }
                    foreach ($row as $row_k => $row_v) {
                        if ($row_v instanceof CRenderable) {
                            $js->appendln($row_v->js())->br();
                        }
                    }
                }
            }
        }

        if ($this->footer) {

            foreach ($this->footer_field as $f) {
                $fval = $f["value"];
                if ($fval instanceof CRenderable) {
                    $js->appendln($fval->js())->br();
                }
            }
        }

        if ($this->haveDataTableViewAction) {
            $js->append("
                $('#" . $this->id() . "-widget-box [name=\"" . $this->id() . "-data-table-view\"]').on('change', function() {
                    $('#" . $this->id() . "-widget-box > .widget-content')
                        .removeClass('data-table-col-view data-table-row-view')
                        .addClass(this.value);
                });
            ");
        }




        return $js->text();
    }

}
