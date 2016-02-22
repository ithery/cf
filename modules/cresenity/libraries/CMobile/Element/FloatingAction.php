<?php

class CMobile_Element_FloatingAction extends CMobile_Element_Action {

    protected $fab;

    public function __construct($id) {
        parent::__construct($id);
    }

    public static function factory($id = '') {
        return new CMobile_Element_FloatingAction($id);
    }

    public function set_icon($ic) {
        $this->icon = $ic;
        return $this;
    }

	protected function html_attr() {
		return parent::html_attr();
	}

    public function html($indent = 0) {
        $this->reassign_confirm();
        
		$this->add_class('btn-floating waves-effect');

        //$html->appendln('<a id="' . $this->id . '" href="' . $link . '"' . $link_target . ' class=" ' . $add_class . '' . $classes . '" ' . $disabled . $add_attr .$addition_attribute. $custom_css . '>');

	
        return parent::html($indent);
    }

    public function js($indent = 0) {
        $this->reassign_confirm();
        $js = new CStringBuilder();
        $js->set_indent($indent);

        if ($this->disabled) {
            $js->appendln("jQuery('#" . $this->id . "').click(function(e) { e.preventDefault(); });");
        } else {
            if ($this->render_as_input()) {

                if (strlen($this->link) > 0) {
                    if ($this->submit) {
                        $js->appendln("jQuery('#" . $this->id . "').click(function() { jQuery(this).closest('form').attr('action','" . $this->link . "'); });");
                    } else {
                        $js->appendln("jQuery('#" . $this->id . "').click(function() { window.location.href='" . $this->link . "'; });");
                    }
                }
            } else {
                if (strlen($this->submit_to) > 0) {
                    $str_submit_to_target = "";
                    if (strlen($this->submit_to_target) > 0) {
                        $str_submit_to_target = "jQuery(this).closest('form').attr('target','" . $this->submit_to_target . "');";
                    }
                    $js->appendln("
						jQuery('#" . $this->id . "').click(function() {
							jQuery(this).closest('form').attr('action','" . $this->submit_to . "');
							" . $str_submit_to_target . "
							jQuery(this).closest('form').submit();
						}
					);");
                }
            }
        }



        $js->appendln(parent::js($js->get_indent()))->br();
        return $js->text();
    }

}

?>