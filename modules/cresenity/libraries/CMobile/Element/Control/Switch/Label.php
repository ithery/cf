<?php
class CMobile_Element_Control_Switch_label extends CMobile_Element_AbstractControl {

    protected $group_list = array();

    public function __construct($id) {
        parent::__construct($id);
        $this->tag = "label";
    }

    public static function factory($id) {
        return new CMobile_Element_Control_Switch_label($id);
    }

    protected function html_attr() {
        $html_attr = parent::html_attr();
        return $html_attr;
    }

    public function build($indent = 0) {
        // $this->add_class( $this->validation->validation_class());
        $html_attr = $this->html_attr();
        $this->add('OFF<input type="checkbox">
      <span class="lever"></span>ON');
    }

    // public function js($indent = 0) {
    //     $js = "$('#" . $this->id . "').material_select();";

    //     return $js;
    // }

}
