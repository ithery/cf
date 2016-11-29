<?php

class CMobile_Element_Control_Input_Date extends CMobile_Element_Control_AbstractInput {

    protected $date_format;
    protected $have_button;
    protected $min_date;
    protected $max_date;
    protected $relative_min_date;
    protected $relative_max_date;
    protected $current_date;
    protected $clear_button;
    protected $now_button;
    protected $disable_day;
    protected $disable_week;
    protected $disable_from_day;
    protected $disable_to_day;
    protected $month_short;
    protected $select_month;
    protected $select_year;
    protected $first_day;
    protected $prefix_icon;
    protected $placeholder;
    protected $manual;

    public function __construct($id) {
        parent::__construct($id);
        //CManager::instance()->register_module('datepicker_material');

        $this->type = "date";
		$this->date_format = "yyyy-mm-dd";
        $date_format = ccfg::get('date_formatted');
		if($date_format!=null) {
			$date_format = str_replace('Y','yyyy',$date_format);
			$date_format = str_replace('m','mm',$date_format);
			$date_format = str_replace('d','dd',$date_format);
			$this->date_format = $date_format;
		}
		$this->manual = false;
        $this->have_button = false;
        $this->month_short = false;
        $this->select_month = true;
        $this->select_year = true;
        $this->min_date = "";
        $this->max_date = "";
        $this->relative_min_date = "";
        $this->relative_max_date = false;
        $this->first_day = "1";
        $this->current_date = "";
        $this->clear_button = "true";
        $this->now_button = "true";
        $this->prefix_icon = '';
        $this->placeholder = "";
        $this->disable_day = array();
        $this->disable_week = array();
    }

    public static function factory($id) {
        return new CMobile_Element_Control_Input_Date($id);
    }

    public function set_have_button($boolean) {
        $this->have_button = $boolean;
        return $this;
    }

    public function set_manual($boolean) {
        $this->manual = $boolean;
        return $this;
    }

    public function set_placeholder($placeholder) {
        $this->placeholder = $placeholder;
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
    
    public function set_prefix_icon($prefix_icon){
        $this->prefix_icon = $prefix_icon; 
        return $this;
    }

    public function set_relative_min_date($relative_min_date) {
        $this->relative_min_date = $relative_min_date;
        return $this;
    }

    public function set_relative_max_date($relative_max_date) {
        $this->relative_max_date = $relative_max_date;
        return $this;
    }

    public function set_disable_from_date($disable_from_day) {
        $this->disable_from_day = $disable_from_day;
        return $this;
    }

    public function set_disable_to_date($disable_to_day) {
        $this->disable_to_day = $disable_to_day;
        return $this;
    }

    public function set_current_date($str) {
        $this->current_date = $str;
        return $this;
    }

    public function set_first_day($first_day) {
        $this->first_day = $first_day;
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

    public function show_month_short() {
        $this->month_short = true;
        return $this;
    }

    public function disable_select_month() {
        $this->select_month = false;
        return $this;
    }

    public function disable_select_year() {
        $this->select_year = false;
        return $this;
    }

    public function add_disable_day($day) {
        if (is_array($day)) {
            $this->disable_day = array_merge($this->disable_day, $day);
        } else {
            $this->disable_day[] = $day;
        }
        return $this;
    }

    public function add_disable_week($week) {
        if (is_array($week)) {
            $this->disable_week = array_merge($this->disable_week, $week);
        } else {
            $this->disable_week[] = $week;
        }
        return $this;
    }

    protected function html_attr() {
        $html_attr = parent::html_attr();
        return $html_attr;
    }

    protected function build() {
        $this->set_attr('type', $this->type);
        $value = '';
        if(strlen($this->current_date) > 0) {
            $value = $this->current_date;
        }
        if(strlen($this->value) > 0) {
            $value = $this->value;
        }
        if (strlen($this->placeholder)>0) {
            $this->set_attr('placeholder',$this->placeholder);
        }
        if (strlen($this->prefix_icon) > 0) {
            $this->before()->add_icon()->set_icon($this->prefix_icon)->set_type('prefix');
            
        }
        $this->set_attr('data-value', $value);
        $this->add_class('datepicker');
        $this->add_class('validate');
        
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append(parent::js());
        foreach ($this->disable_day as $key => $value) {
            $this->disable_day[$key] = 'new Date("' . $value . '"),';
        }
        $disable_day = implode($this->disable_day);

        foreach ($this->disable_week as $key => $value) {
            $this->disable_week[$key] = $value . ',';
        }
        $disable_week = implode($this->disable_week);
        $option = 'firstDay: ' . $this->first_day . ',';
        if($this->month_short) {
            $option .= 'showMonthsShort:true,';
        }
        if(strlen($this->min_date) > 0 && strlen($this->relative_min_date) == 0) {
            $option .= 'min: new Date("' . $this->min_date . '"),';
        }
        if(strlen($this->relative_min_date) > 0) {
            $option .= 'min: ' . $this->relative_min_date . ',';
        }
        if(strlen($this->max_date) > 0 && $this->relative_max_date == false) {
            $option .= 'max: new Date("' . $this->max_date . '"),';
        }
        if($this->relative_max_date) {
            $option .= 'max: true,';
        }
        if(strlen($disable_day) > 0) {
            $option .= 'disable: [' . $disable_day . '],';
        }
        if(strlen($disable_week) > 0) {
            $option .= 'disable: [' . $disable_week . '],';
        }
        if(strlen($this->disable_from_day) > 0 && strlen($this->disable_to_day) > 0) {
            $option .= 'disable: [{ from: new Date( "' . $this->disable_from_day . '"), to: new Date( "' . $this->disable_to_day . '") }],';
        }
        if($this->select_month) {
            $option .= 'selectMonths:true,';
        } else {
            $option .= 'selectMonths:false,';
        }
        if($this->select_year) {
            $option .= 'selectYears:true,';
        } else {
            $option .= 'selectYears:false,';
        }
        if(!$this->manual) {
            $js->append("$('#" . $this->id . "').pickadate({
                            selectMonths: true, // Creates a dropdown to control month
                            yearSelector: 100,
                            format: '" . $this->date_format . "',
                            " . $option . "
                          });");

        }
        
        return $js->text();
    }
}
