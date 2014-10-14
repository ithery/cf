<?php
	class CAjax extends CRenderable{
		
		public $name = "";
		public $method = "GET";
		
		public $trigger_element = "";
		public $trigger_event = "";
		public $param = array();
		public $type = "";
		public $query = "";
		public $target = "";
		public $opt = array();
		
		public function __construct() {
			parent::__construct();
		}
		public static function factory() {
			return new CAjax();
		}
		
		public function set_type($type) {
			$this->type = $type;
			return $this;
		}
		
		public function set_query($query) {
			$this->query=$query;
			return $this;
		}
		
		public function set_target($elem) {
			$this->target = $elem;
			return $this;
		}
		
		public function set_method($method) {
			$this->method = $method;
			return $this;
		}
		
		public function add_param($ajax_param) {
			$this->param[] = $ajax_param;
			return $this;
		}
		public function add_param_input($key,$val) {
			$this->param[] = CAjaxParam::factory($key)->set_type("input")->set_value($val);
			return $this;
		}
		public function set_trigger($elem,$evt) {
			$this->trigger_element = $elem;
			$this->trigger_event 	= $evt;
			return $this;
			
		}
		
		public function set_options($opt) {
			$this->opt = $opt;
			return $this;
		}
		
		public function html($indent=0) {
			$html = new CStringBuilder();
			$html->set_indent($indent);
			$html->appendln(parent::html($html->get_indent()));
			return $html->text();
		}
		
		
		
		public function js($indent=0) {
			$js = CStringBuilder::factory()->set_indent($indent);
			//generate ajax_method
			//save this object to file.
			$json = json_encode($this);
			$filename = 
			$ajax_method = cutils::randmd5();
			$file = ctemp::makepath("ajax",$ajax_method.".tmp");
			file_put_contents($file,$json);
			
			$jq_method = "change";
			switch($this->trigger_event) {
				case "change":
					$jq_method = "change";
				break;
				case "click":
					$jq_method = "click";
				break;
			}
			
			$url_str = "'".curl::base()."index.php/cresenity/ajax/".$ajax_method."'";
			$params = array();
			foreach($this->param as $param) {
				$val = "'".$param->get_value()."'";
				switch($param->get_type()) {
					case "value":
						$val = "'".$param->get_value()."'";
					break;
					case "input":
						$val = "jQuery('#".$param->get_value()."').val()";
					break;
				}
				$params[] = "'".$param->get_name()."': ".$val;
			}
			$str_param = implode(",",$params);
			$donescript = "";
			switch($this->type) {
				case "fillselect":
					$donescript = "".
						"options = ''; ".
						"for (x in data) { options += '<option value=\"' + x + '\" >' + data[x] + '</option>';}; ".
						"jQuery('#".$this->target."').html(options);".
						"
						values = [];
						jQuery('#".$this->target."').find(':selected').each(function(i)
						{
							values.push($(this).text());
						});
						
						//var select = (jQuery('#".$this->target."').parent('span'));
						//select.children('.select-value').html((values.length > 0) ? values.join(', ') : '&nbsp;');
						if(jQuery('#".$this->target."_chzn')) {
							//console.log('chzn exists');
							//jQuery('#".$this->target."_chzn').remove();
							//setTimeout(function() { jQuery('#".$this->target."').show();jQuery('#".$this->target."').chosen(); }, 500);
							//jQuery('#".$this->target."').select2();
							//console.log('after chosen');
						}
						if('.select2-container') {
							jQuery('#".$this->target."').select2();
						
						}
						".
						"";
				break;
			}
			
			$js->appendln("
				jQuery('#".$this->trigger_element."').".$jq_method."(function(event) {
					jQuery.ajax({
					  type: '".$this->method."',
					  url: ".$url_str.",
					  dataType: 'json',
					  data: { ".$str_param." }
					}).done(function( data ) {
					   ".$donescript."
					});
				});
			");
			$js->appendln(parent::js($js->get_indent()))->br();
			return $js->text();
		}
	}
?>