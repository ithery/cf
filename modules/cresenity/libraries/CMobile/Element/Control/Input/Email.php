<?php

class CMobile_Element_Control_Input_Email extends CMobile_Element_Control_AbstractInput {

    protected $placeholder;
    protected $label_float;
    protected $prefix_icon;
    protected $data_error;
    protected $data_success;


    public function __construct($id) {
        parent::__construct($id);

        $this->type = "text";

        $this->placeholder = "";

       
        $this->prefix_icon = '';
        $this->label_float = true;

        $this->data_error = '';
        $this->data_success = '';

    }

    /**
     * 
     * @param type $id
     * @return \CFormInputText
     */
    public static function factory($id = '') {
        return new CMobile_Element_Control_Input_Email($id);
    }


    public function set_placeholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function disable_label_float() {
        $this->label_float = false;
        return $this;
    }

    public function set_name($name){
        $this->name = $name; return $this;
    }

    public function set_prefix_icon($prefix_icon){
        $this->prefix_icon = $prefix_icon; 
        return $this;
    }
    public function set_data_error($data_error){
        $this->data_error = $data_error; 
        return $this;
    }

    public function set_data_success($data_success){
        $this->data_success = $data_success; 
        return $this;
    }
	
	protected function build() {
		if (strlen($this->placeholder)>0) {
			$this->set_attr('placeholder',$this->placeholder);
		}
		if (strlen($this->data_error)>0) {
			$this->set_attr('data-error',$this->data_error);
		}
		if(strlen($this->data_error) > 0) {
			$this->set_attr('data-error',$this->data_error);
		}
		$this->set_attr('type',$this->type);
		
		$this->add_class('validate');
		
		 if (strlen($this->prefix_icon) > 0) {
            $this->before()->add_icon()->set_icon($this->prefix_icon)->set_type('prefix');
            
        }
	}
    
	
    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append(parent::js());
        


        return $js->text();
    }

}