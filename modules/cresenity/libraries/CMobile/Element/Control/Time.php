<?php

class CMobile_Element_FormInput_Time extends CMobile_Element_AbstractFormInput {

    protected $time_format;
    protected $have_button;
    protected $min_time;
    protected $max_time;
    protected $current_time;
    protected $clear_button;
    protected $now_button;
    protected $disable_day;

    public function __construct($id) {
        parent::__construct($id);
        //CManager::instance()->register_module('datepicker_material');

        $this->type = "timepicker";
		$this->time_format = "HH:mm";
		
        $this->have_button = false;
        $this->min_time = "";
        $this->max_time = "";
        $this->current_time = "";
        $this->clear_button = "true";
        $this->now_button = "true";
        $this->disable_day = array();
    }

    public static function factory($id) {
        return new CMobile_Element_FormInput_Time($id);
    }

    public function set_have_button($boolean) {
        $this->have_button = $boolean;
        return $this;
    }

    public function set_min_time($str) {
        $this->min_time = $str;
        return $this;
    }

    public function set_max_time($str) {
        $this->max_time = $str;
        return $this;
    }

    public function set_current_time($str) {
        $this->current_time = $str;
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
//		$html->appendln('<input type="text" name="'.$this->name.'"  data-time-format="'.$this->time_format.'" id="'.$this->id.'" class="timepicker input-unstyled'.$classes.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.$custom_css.'>')->br();

        return $html->text();
    }

    public function js($indent = 0) {
        $day_map = array(
            "sunday" => "0",
            "monday" => "1",
            "tuesday" => "2",
            "wednesday" => "3",
            "thursday" => "4",
            "friday" => "5",
            "saturday" => "6",
        );

        foreach ($this->disable_day as $k => $v) {
            if (isset($day_map[strtolower($this->disable_day[$k])])) {
                $this->disable_day[$k] = $day_map[strtolower($this->disable_day[$k])];
            }
        }


        // $disable_day_str = implode(",", $this->disable_day);

        $option = "format:'" .  $this->time_format . "'";

        if (strlen($this->min_time) > 0) {
            if (strlen($option) > 0)
                $option .= ",";
            $option .= "minDate: '" . $this->min_time . "'";
        }

        if (strlen($this->max_time) > 0) {
            if (strlen($option) > 0)
                $option .= ",";
            $option .= "maxDate: '" . $this->max_time . "'";
        }
        if(strlen($this->value) > 0) {
            $this->current_time = $this->value;
        }
        if (strlen($this->current_time) > 0) {
            if (strlen($option) > 0)
                $option .= ",";
            $option .= "currentDate: '" . $this->current_time . "'";
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
            $js->append("$('#" . $this->id . "').parent().bootstrapMaterialDatePicker({ time: true, date: false, " . $option . "});")->br();
        } else {
            $js->append("$('#" . $this->id . "').bootstrapMaterialDatePicker({ time: true, date: false, " . $option . "});")->br();
        }
        return $js->text();
    }

}
