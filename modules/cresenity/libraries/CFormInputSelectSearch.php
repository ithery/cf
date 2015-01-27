<?php
class CFormInputSelectSearch extends CFormInput {
	protected $query;
	protected $format_selection;
	protected $format_result;
	protected $key_field;
	protected $search_field;
	protected $placeholder;

	public function __construct($id) {
		parent::__construct($id);
		
		$this->type="selectsearch";
		$this->query = "";
		$this->format_selection = "";
		$this->format_result = "";
		$this->key_field = "";
		$this->search_field = "";
		$this->placeholder = "Search for a item";
	}
	
	public static function factory($id) {
		return new CFormInputSelectSearch($id);
	}
	public function set_multiple($bool) {
		$this->multiple = true;
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
	
	public function html($indent=0) {
		$html = new CStringBuilder();
		$custom_css = $this->custom_css;
		$custom_css = crenderer::render_style($custom_css);
		if(strlen($custom_css)>0) {
			$custom_css = ' style="'.$custom_css.'"';
		}
		$html->set_indent($indent);
		$html->appendln('<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" value="'.$this->value.'" '.$custom_css.'>')->br();
		return $html->text();	
	}
	public function js($indent=0) {
		$ajax_url = CAjaxMethod::factory()
			->set_type('searchselect')
			->set_data('query',$this->query)
			->set_data('key_field',$this->key_field)
			->set_data('search_field',$this->search_field)
			->makeurl();
		
		$str_selection = $this->format_selection;
		$str_result = $this->format_result;
		$str_selection = str_replace("'","\'",$str_selection);
		$str_result = str_replace("'","\'",$str_result);
		preg_match_all("/{([\w]*)}/", $str_selection, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$str = $val[1]; //matches str without bracket {}
			$b_str = $val[0]; //matches str with bracket {}
			$str_selection = str_replace($b_str,"'+item.".$str."+'",$str_selection);
		}
		preg_match_all("/{([\w]*)}/", $str_result, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$str = $val[1]; //matches str without bracket {}
			$b_str = $val[0]; //matches str with bracket {}
			$str_result = str_replace($b_str,"'+item.".$str."+'",$str_result);
		}
		if(strlen($str_result)==0) {
			$str_result = "'+item.".$this->search_field."+'";
		}
		if(strlen($str_selection)==0) {
			$str_selection = "'+item.".$this->search_field."+'";
		}
		$placeholder = "Search for a item";
		if(strlen($this->placeholder)>0) {
			$placeholder = $this->placeholder;
		}
		$str_js_change = "";
		if($this->submit_onchange) {
			$str_js_change = "$(this).closest('form').submit();";
		
		}
		
		$str_js_init = "";
		if(strlen($this->value)>0) {
			
			$db = CDatabase::instance();
			$rjson = 'false';
			
			$q="select * from (".$this->query.") as a where `".$this->key_field."`=".$db->escape($this->value);
			$r = $db->query($q)->result_array(false);
			if(count($r)>0) $r= $r[0];
			$rjson = json_encode($r);
		
		
			$str_js_init = "
				initSelection : function (element, callback) {
			
				var data = ".$rjson."
				callback(data);
			},
			";
		}
		$str = "
			$('#".$this->id."').select2({
				placeholder: '".$placeholder."',
				minimumInputLength: 0,
				ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
					url: '".$ajax_url."',
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
				".$str_js_init."
				formatResult: function(item) {
					return '".$str_result."';
				
				}, // omitted for brevity, see the source of this page
				formatSelection: function(item) {
					return '".$str_selection."';
				},  // omitted for brevity, see the source of this page
				dropdownCssClass: 'bigdrop' // apply css that makes the dropdown taller
			}).change(function() {
				".$str_js_change."
			});
	
	";
		
		$js = new CStringBuilder();
		$js->set_indent($indent);
		//echo $str;
		$js->append($str)->br();
		
		return $js->text();
		
	}
	
}