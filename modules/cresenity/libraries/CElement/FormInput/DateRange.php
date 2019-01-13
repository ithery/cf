<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 30, 2018, 4:32:01 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_DateRange extends CElement_FormInput {

    protected $dateFormat;
    protected $start;
    protected $end;

    public function __construct($id) {
        parent::__construct($id);

        if (CManager::isRegisteredModule('bootstrap-4-material')) {
            CManager::instance()->registerModule('bootstrap-4-material-datepicker');
        } elseif (CManager::isRegisteredModule('bootstrap-4')) {
            CManager::instance()->registerModule('bootstrap-4-datepicker');
        } else {
            CManager::instance()->registerModule('datepicker');
        }

        $this->type = "date";
        $this->dateFormat = "yyyy-mm-dd";
        $date_format = ccfg::get('date_formatted');
        if ($date_format != null) {
            $date_format = str_replace('Y', 'yyyy', $date_format);
            $date_format = str_replace('m', 'mm', $date_format);
            $date_format = str_replace('d', 'dd', $date_format);
            $this->dateFormat = $date_format;
        }

        $this->addClass('form-control');
    }

    public function set_have_button($boolean) {
        $this->have_button = $boolean;
        return $this;
    }

    public function setValueStart($dateStart) {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function setValueEnd($dateEnd) {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $disabled = "";
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }
        $addition_attribute = "";
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= " " . $k . '="' . $v . '"';
        }

        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0) {
            $classes = " " . $classes;
        }
        $classes .= " form-control";
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }

        $html->appendln('<div class="input-daterange input-group" id="' . $this->id . '">')->br();
        $html->appendln('<input type="text" name="' . $this->name . '[start]"  data-date-format="' . $this->dateFormat . '" id="' . $this->id . '-start" class="datepicker input-unstyled' . $classes . $this->validation->validation_class() . '" value="' . $this->value . '"' . $disabled . $addition_attribute . $custom_css . '>')->br();
        $html->appendln('<div class="input-group-prepend"><span class="input-group-text">to</span></div>');
        $html->appendln('<input type="text" name="' . $this->name . '[end]"  data-date-format="' . $this->dateFormat . '" id="' . $this->id . '-end" class="datepicker input-unstyled' . $classes . $this->validation->validation_class() . '" value="' . $this->value . '"' . $disabled . $addition_attribute . $custom_css . '>')->br();
        $html->appendln('</div>');


        return $html->text();
    }

    public function js($indent = 0) {

        $option = "";


        $autoclose = "true";
        if (strlen($option) > 0)
            $option .= ",";
        $option .= "autoclose: " . $autoclose . "";


        if (strlen($option) > 0) {
            $option = "{" . $option . "}";
        }
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append(parent::js($indent))->br();


        $js->append("$('#" . $this->id . "').datepicker(" . $option . ");")->br();

        return $js->text();
    }

}
