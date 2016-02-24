<?php
class CMobile_Element_Component_Switch extends CMobile_Element_AbstractControl {

    protected $group_list = array();

    public function __construct($id) {
        parent::__construct($id);
        $this->tag = "div";
    }

    public static function factory($id) {
        return new CMobile_Element_Component_Switch($id);
    }

    protected function html_attr() {
        $html_attr = parent::html_attr();
        return $html_attr;
    }

    public function build($indent = 0) {
        // $this->add_class( $this->validation->validation_class());
        $this->add_class('switch');
        $html_attr = $this->html_attr();
        $element = CMobile_Element_Component_Switch_Label::factory('');
        $this->add($element);
    }

    // public function js($indent = 0) {
    //     $js = "$('#" . $this->id . "').material_select();";

    //     return $js;
    // }

}
