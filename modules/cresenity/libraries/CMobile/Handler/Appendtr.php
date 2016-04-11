<?php defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Handler_Driver extends CMobile_HandlerDriver {

	

	protected $target;
	protected $method;
	protected $content;
	protected $param;
	protected $param_inputs;
    protected $check_duplicate_tr;
    protected $value_duplicate_tr;
    protected $bulk_data;
    protected $bulk_url;

	public function __construct($owner,$event,$name){
		parent::__construct($owner,$event,$name);
		$this->method = "get";
		$this->target = "";
		$this->content = CHandlerElement::factory();
		$this->param_inputs=array();
		$this->bulk_data = false;
		
	}
	public function add_param_input($inputs) {
		if(!is_array($inputs)) {
			$inputs = array($inputs);
		}
		foreach($inputs as $inp) {
			$this->param_inputs[] = $inp;
		}
		return $this;
	}
	
	public function set_target($target) {
		
		$this->target = $target;
		
		return $this;
	}
	
	public function set_method($bulk_data) {
		$this->method = $method;
	}
	
	public function set_bulk($bulk_data) {
		$this->bulk_data = $bulk_data;
	}

	public function set_bulk_url($bulk_url) {
		$this->bulk_url = $bulk_url;
	}
	
	public function content() {
		return $this->content;
	}

    //set check duplicate tr must using hidden field and validate by hidden value.
    public function set_check_duplicate_tr($param='true'){
        $this->check_duplicate_tr=$param;
        return $this;
    }

    public function set_value_duplicate_tr($data=array()){
        $this->value_duplicate_tr=$data;
        return $this;
    }
	
	public function script() {
		$js = parent::script();
		$data_addition = '';
		
		foreach($this->param_inputs as $inp) {
			if(strlen($data_addition)>0) $data_addition.=',';
			$data_addition.="'".$inp."':$('#".$inp."').val()";
		}
		$data_addition = '{'.$data_addition.'}';

		// $js_get_data = "";
		// if($this->bulk_data) {
		// 	$js_get_data = "
		// 	var bulk_value=[];
		// 	jQuery.ajax({
		//         type: 'get',
		//         url: '" . curl::base() . $this->bulk_url . "',
		//         dataType: 'json',
		//         data: " . $data_addition . "
		//     }).done(function( data ) {
		// 		console.log(data);
		// 		jQuery('#".$this->target." tr').each(function() {
		// 			var hidden_elem= tr.find(':hidden').get(i);
  //                   console.log(hidden_elem);
  //                   var hidden_value=jQuery(hidden_elem).val();
		// 			bulk_value.push(hidden_value);
		// 		});
		// 		console.log(bulk_value);
		//     }).error(function(obj,t,msg) {
  //       	});";
		// 	$js .= $js_get_data;
		// }
        $param_duplicate='';
        foreach ($this->value_duplicate_tr as $inp) {

            if (strlen($param_duplicate) > 0) $param_duplicate .= ',';
            $param_duplicate .= "'" . $inp . "':$('#" . $inp . "').val()";
        }
        $param_duplicate = '{' . $param_duplicate . '}';

  //       if(!$this->bulk_data) {
		$js.= "
                    var is_duplicate = false;

                    var check_duplicate = '".$this->check_duplicate_tr."';
                    if(check_duplicate=='true'){
                        var p = ".$param_duplicate.";
                        var param=[];
                        for (var key in p) {
                          if (p.hasOwnProperty(key)) {
                            param.push(p[key]);
                          }
                        }

                        jQuery('#".$this->target." tr').each(function() {
                        	
                            var tr = $(this);
                            var lengthparam = param.length;
                            var counter=0;
                            for(var i=0;i<lengthparam;i++){

                                var hidden_elem= tr.find(':hidden').get(i);
                                var hidden_value=jQuery(hidden_elem).val();
                                if(hidden_value==param[i]){
                                    counter++;
                                }

                            }

                            if(counter==lengthparam){
                                is_duplicate=true;
                            }

                        });
                    }

                    if(!is_duplicate){
                        $.cresenity.append('".$this->target."','".$this->generated_url()."','".$this->method."',".$data_addition.");
                    }

		";
		// }
		return $js;
			
	}
	
}