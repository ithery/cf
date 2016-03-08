<?php

    class CFormInputSelectSearch extends CFormInput {

        protected $query;
        protected $format_selection;
        protected $format_result;
        protected $key_field;
        protected $search_field;
        protected $multiple;
        protected $placeholder;
        protected $auto_select;
        protected $min_input_length;

        public function __construct($id) {
            parent::__construct($id);

            $this->type = "selectsearch";
            $this->query = "";
            $this->format_selection = "";
            $this->format_result = "";
            $this->key_field = "";
            $this->search_field = "";
            $this->placeholder = "Search for a item";
            $this->multiple = false;
            $this->auto_select = false;
            $this->min_input_length = 0;
        }

        public static function factory($id) {
            return new CFormInputSelectSearch($id);
        }

        public function set_multiple($bool) {
            $this->multiple = $bool;
            return $this;
        }

        public function set_auto_select($bool) {
            $this->auto_select = $bool;
            return $this;
        }

        public function set_min_input_length($min_input_length) {
            $this->min_input_length = $min_input_length;
            return $this;
        }

        public function set_key_field($key_field) {
            $this->key_field = $key_field;
            return $this;
        }

        public function set_search_field($search_field) {
            $this->search_field = $search_field;
            return $this;
        }

        public function set_query($query) {
            $this->query = $query;
            return $this;
        }

        public function set_format_result($fmt) {
            $this->format_result = $fmt;
            return $this;
        }

        public function set_format_selection($fmt) {
            $this->format_selection = $fmt;
            return $this;
        }

        public function set_placeholder($placeholder) {
            $this->placeholder = $placeholder;
            return $this;
        }

        public function html($indent = 0) {
            $html = new CStringBuilder();
            $custom_css = $this->custom_css;
            $custom_css = crenderer::render_style($custom_css);
            $multiple = "";
            if ($this->multiple) {
                $multiple = ' multiple="multiple"';
            }
            if (strlen($custom_css) > 0) {
                $custom_css = ' style="' . $custom_css . '"';
            }

            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0) $classes = " " . $classes;
            if ($this->bootstrap >= '3') {
                $classes = $classes . " form-control ";
            }
            $html->set_indent($indent);
            $value = $this->value;
            if ($this->auto_select) {
                $db = CDatabase::instance();
                $rjson = 'false';

                $q = "select `" . $this->key_field . "` from (" . $this->query . ") as a limit 1";
                $value = cdbutils::get_value($q);
            }
            if (strlen($this->value) > 0) {
                $value = $this->value;
            }

            if ($this->select2 >= '4') {
                $html->appendln('<select class="' . $classes . '" name="' . $this->name . '" id="' . $this->id . '" value="' . $value . '" ' . $custom_css . $multiple . '">');
                $html->appendln('</select');
                $html->br();
            }
            else {
                $html->appendln('<input type="hidden" class="' . $classes . '" name="' . $this->name . '" id="' . $this->id . '" value="' . $value . '" ' . $custom_css . $multiple . '>')->br();
            }
            return $html->text();
        }

        public function create_ajax_url() {
            return CAjaxMethod::factory()
                            ->set_type('searchselect')
                            ->set_data('query', $this->query)
                            ->set_data('key_field', $this->key_field)
                            ->set_data('search_field', $this->search_field)
                            ->makeurl();
        }

        public function js($indent = 0) {
            $ajax_url = $this->create_ajax_url();

            $str_selection = $this->format_selection;
            $str_result = $this->format_result;
            $str_selection = str_replace("'", "\'", $str_selection);
            $str_result = str_replace("'", "\'", $str_result);
            preg_match_all("/{([\w]*)}/", $str_selection, $matches, PREG_SET_ORDER);

            foreach ($matches as $val) {
                $thousand_separator_pre = '';
                $thousand_separator_post = '';
                $str = $val[1]; //matches str without bracket {}
                $b_str = $val[0]; //matches str with bracket {}
                $str_selection = str_replace($b_str, "'+item." . $str . "+'", $str_selection);
            }
            preg_match_all("/{([\w]*)}/", $str_result, $matches, PREG_SET_ORDER);
            foreach ($matches as $val) {
                $thousand_separator_pre = '';
                $thousand_separator_post = '';
                $str = $val[1]; //matches str without bracket {}
                $b_str = $val[0]; //matches str with bracket {}
                $str_result = str_replace($b_str, "'+item." . $str . "+'", $str_result);
            }
            if (strlen($str_result) == 0) {
                $str_result = "'+item." . $this->search_field . "+'";
            }
            if (strlen($str_selection) == 0) {
                $str_selection = "'+item." . $this->search_field . "+'";
            }

            $placeholder = "Search for a item";
            if (strlen($this->placeholder) > 0) {
                $placeholder = $this->placeholder;
            }
            $str_js_change = "";
            if ($this->submit_onchange) {
                $str_js_change = "$(this).closest('form').submit();";
            }

            $str_js_init = "";
            if ($this->auto_select) {
                $db = CDatabase::instance();
                $rjson = 'false';

                $q = "select * from (" . $this->query . ") as a limit 1";
                $r = $db->query($q)->result_array(false);
                if (count($r) > 0) $r = $r[0];
                $rjson = json_encode($r);


                $str_js_init = "
				initSelection : function (element, callback) {
					
				var data = " . $rjson . ";
				
				callback(data);
			},
			";
            }
            if (strlen($this->value) > 0) {

                $db = CDatabase::instance();
                $rjson = 'false';

                $q = "select * from (" . $this->query . ") as a where `" . $this->key_field . "`=" . $db->escape($this->value);
                $r = $db->query($q)->result_array(false);
                if (count($r) > 0) $r = $r[0];
                $rjson = json_encode($r);


                $str_js_init = "
				initSelection : function (element, callback) {
					
				var data = " . $rjson . ";
				
				callback(data);
			},
			";
            }

            $str_multiple = "";
            if ($this->multiple) $str_multiple = " multiple:'true',";
            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0) {
                $classes = " " . $classes;
            }
            if ($this->bootstrap >= '3') {
                $classes = $classes . " form-control ";
            }
            if ($this->select2 >= '4') {
                $str = "
                    $('#" . $this->id . "').select2({
                        placeholder: '" . $placeholder . "',
                        minimumInputLength: '" . $this->min_input_length . "',
                        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                                url: '" . $ajax_url . "',
                                dataType: 'jsonp',
                                " . $str_multiple . "
                                data: function (params) {
                                    return {
                                      q: params.term, // search term
                                      page: params.page
                                    };
                                },
                                processResults: function (data, params) { 
                                    // parse the results into the format expected by Select2
                                    // since we are using custom formatting functions we do not need to
                                    // alter the remote JSON data, except to indicate that infinite
                                    // scrolling can be used
                                    params.page = params.page || 1;
                                    
                                    return {
                                            results: data.data,
                                            pagination: {
                                              more: (params.page * 10) < data.total
                                            }
                                          };
                                },
                                cache:true,
                            },
                        " . $str_js_init . "
                        templateResult: function(item) {
                            if (item.id === '') {
                                return item.text;
                            }
                            return '" . $str_result . "';
                        }, // omitted for brevity, see the source of this page
                        templateSelection: function(item) {
                        
                            if (item.id === '') {
                                return item.text;
                            }
                            return '" . $str_selection . "';
                        },  // omitted for brevity, see the source of this page
                        dropdownCssClass: '', // apply css that makes the dropdown taller
                        containerCssClass : 'tpx-select2-container " . $classes . "'
                    }).change(function() {
				" . $str_js_change . "
                    });
                    ";
            }
            else {
                $str = "
			$('#" . $this->id . "').select2({
				placeholder: '" . $placeholder . "',
				minimumInputLength: '" . $this->min_input_length . "',
				ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
					url: '" . $ajax_url . "',
					dataType: 'jsonp',
					" . $str_multiple . "
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
					},
                    cache:true,

				},
				" . $str_js_init . "
				formatResult: function(item) {
					return '" . $str_result . "';
				}, // omitted for brevity, see the source of this page
				formatSelection: function(item) {
					return '" . $str_selection . "';
				},  // omitted for brevity, see the source of this page
				dropdownCssClass: '', // apply css that makes the dropdown taller
				containerCssClass : 'tpx-select2-container " . $classes . "'
			}).change(function() {
				" . $str_js_change . "
			});
	
                ";
            }

            $js = new CStringBuilder();
            $js->append(parent::js($indent))->br();
            $js->set_indent($indent);
            //echo $str;
            $js->append($str)->br();





            return $js->text();
        }

    }
    