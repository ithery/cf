<?php
	class CJSTrigger {
		
		public $name = "";
		
		public $element_id = "";
		public $event = "";
		
		public function __construct($elem,$evt) {
			$this->element_id = $elem;
			$this->event 	= $evt;
			
		}
		public static function factory($elem,$evt) {
			return new CJSTrigger($elem,$evt);
		}
		
		
		
		
		public function render_js($indent=0) {
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
			
			$url_str = "'".curl::base()."index.php/c_ajax/".$ajax_method."'";
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
						
						var select = (jQuery('#".$this->target."').parent('span'));
						select.children('.select-value').html((values.length > 0) ? values.join(', ') : '&nbsp;');
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
			return $js->text();
		}
	}
?>