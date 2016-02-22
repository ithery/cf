<?php

class CMobile_Element_FormInput_Date extends CMobile_Element_AbstractFormInput {

    protected $date_format;
    protected $have_button;
    protected $min_date;
    protected $max_date;
    protected $current_date;
    protected $clear_button;
    protected $now_button;
    protected $disable_day;

    public function __construct($id) {
        parent::__construct($id);
        //CManager::instance()->register_module('datepicker_material');

        $this->type = "date";
		$this->date_format = "YYYY-MM-DD";
        $date_format = ccfg::get('date_formatted');
		if($date_format!=null) {
			$date_format = str_replace('Y','YYYY',$date_format);
			$date_format = str_replace('m','MM',$date_format);
			$date_format = str_replace('d','DD',$date_format);
			$this->date_format = $date_format;
		}
		
        $this->have_button = false;
        $this->min_date = "";
        $this->max_date = "";
        $this->current_date = "";
        $this->clear_button = "true";
        $this->now_button = "true";
        $this->disable_day = array();
    }

    public static function factory($id) {
        return new CMobile_Element_FormInput_Date($id);
    }

    public function set_have_button($boolean) {
        $this->have_button = $boolean;
        return $this;
    }

    public function set_min_date($str) {
        $this->min_date = $str;
        return $this;
    }

    public function set_max_date($str) {
        $this->max_date = $str;
        return $this;
    }

    public function set_current_date($str) {
        $this->current_date = $str;
        return $this;
    }

    public function show_clear_button() {
        $this->clear_button = true;
        return $this;
    }

    public function show_now_button() {
        $this->now_button = true;
        return $this;
    }

    public function add_disable_day($day) {

        $day_array = explode(",", $day);
        if (count($day_array) > 1) {
            foreach ($day_array as $d) {
                $this->disable_day[] = trim($d);
            }
        } else {
            $this->disable_day[] = $day;
        }
        return $this;
    }


    protected function html_attr() {
        $html_attr = parent::html_attr();
        return $html_attr;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        if ($this->disabled)
            $this->add_attr('disabled', 'disabled');
        $this->add_class('input-unstyled');
        $this->add_class('form-control');
        $this->add_class( $this->validation->validation_class());

        $html_attr = $this->html_attr();
        if ($this->have_button) {
            $html->appendln('<div class="input-append" id="dp3">
                        <input  ' . $html_attr . ' type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '"' . '>
                        <span class="add-on"><i class="icon-th"></i></span>
                    </div>')->br();
        } else {
            $html->appendln('<input  ' . $html_attr . ' type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '"' . '>')->br();
        }
//		$html->appendln('<input type="text" name="'.$this->name.'"  data-date-format="'.$this->date_format.'" id="'.$this->id.'" class="datepicker input-unstyled'.$classes.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.$custom_css.'>')->br();

        return $html->text();
    }

    public function js($indent = 0) {

        $option = "format:'" .  $this->date_format . "'";

        if (strlen($this->min_date) > 0) {
            if (strlen($option) > 0)
                $option .= ",";
            $option .= "minDate: '" . $this->min_date . "'";
        }

        if (strlen($this->max_date) > 0) {
            if (strlen($option) > 0)
                $option .= ",";
            $option .= "maxDate: '" . $this->max_date . "'";
        }
        if(strlen($this->value) > 0) {
            $this->current_date = $this->value;
        }
        if (strlen($this->current_date) > 0) {
            if (strlen($option) > 0)
                $option .= ",";
            $option .= "currentDate: '" . $this->current_date . "'";
        }

        if ($this->clear_button) {
            if (strlen($option) > 0)
                $option .= ",";
            $option .= "clearButton: true";
        }

        if ($this->now_button) {
            if (strlen($option) > 0)
                $option .= ",";
            $option .= "nowButton: true";
        }
        


        // if (strlen($option) > 0) {
        //     $option = "{" . $option . "}";
        // }
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append(parent::js($indent))->br();

        if ($this->have_button) {
            $js->append("$('#" . $this->id . "').parent().bootstrapMaterialDatePicker({ time: false, " . $option . "});")->br();
        } else {
            $js->append("$('#" . $this->id . "').bootstrapMaterialDatePicker({ time: false, " . $option . "});")->br();
        }
        return $js->text();
    }

}
