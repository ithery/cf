<?php defined('SYSPATH') OR die('No direct access allowed.');

class CHandler_Appendtr_Driver extends CHandler_Driver {

	

	protected $target;
	protected $method;
	protected $content;
	protected $param;
	protected $param_inputs;
    protected $check_duplicate_tr;
    protected $value_duplicate_tr;

	public function __construct($owner,$event,$name){
		parent::__construct($owner,$event,$name);
		$this->method = "get";
		$this->target = "";
		$this->content = CHandlerElement::factory();
		$this->param_inputs=array();
		
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
	
	public function set_method($method) {
		$this->method = $method;
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


        $param_duplicate='';
        foreach ($this->value_duplicate_tr as $inp) {
            if (strlen($param_duplicate) > 0) $param_duplicate .= ',';
            $param_duplicate .= "'" . $inp . "':$('#" . $inp . "').val()";
        }
        $param_duplicate = '{' . $param_duplicate . '}';


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
		
		return $js;
			
	}
	
}