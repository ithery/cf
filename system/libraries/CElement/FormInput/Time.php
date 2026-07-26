<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_Time extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Time,
        CTrait_Element_Property_Placeholder;

    /**
     * @var bool
     */
    protected $show_second;

    /**
     * bootstrap-timepicker `template` option (eg. `'dropdown'`|`'modal'`).
     *
     * @var string
     */
    protected $template;

    /**
     * @var bool
     */
    protected $show_meridian;

    /**
     * @var int
     */
    protected $minute_step;

    /**
     * @param string $id
     */
    public function __construct($id) {
        parent::__construct($id);

        $this->type = 'timepicker';
        $this->show_second = false;
        $this->template = 'dropdown';
        $this->show_meridian = false;
        $this->minute_step = 1;

        $this->placeholder = '';
        $this->addClass('form-control');
        CManager::instance()->registerModule('timepicker');
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $disabled = '';
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }

        $addition_attribute = '';
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= ' ' . $k . '="' . $v . '"';
        }

        $classes = $this->classes;
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }
        $custom_css = $this->custom_css;
        $custom_css = static::renderStyle($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $placeholder = '';
        if (strlen($this->placeholder) > 0) {
            $placeholder = ' placeholder="' . $this->placeholder . '"';
        }

        $html->appendln('<div class="bootstrap-timepicker">');
        $html->appendln('<input type="text" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled ' . $classes . $this->validation->validationClass() . '" value="' . $this->value . '"' . $disabled . $custom_css . $addition_attribute . $placeholder . '>')->br();
        $html->appendln('</div>');

        return $html->text();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);

        $js->appendln("$('#" . $this->id . "').timepicker({");
        if (strlen($this->value) > 0) {
            $js->appendln("	defaultTime: '" . $this->value . "',");
        } else {
            $js->appendln("	defaultTime: 'false',");
        }
        if (strlen($this->minute_step) > 0) {
            $js->appendln('	minuteStep: ' . $this->minute_step . ',');
        } else {
            $js->appendln('	minuteStep: 1,');
        }
        if (strlen($this->template) > 0) {
            $js->appendln("	template: '" . $this->template . "',");
        } else {
            $js->appendln("	template: 'dropdown',");
        }
        if ($this->show_second) {
            $js->appendln('	showSeconds: true,');
        } else {
            $js->appendln('	showSeconds: false,');
        }
        if ($this->show_meridian) {
            $js->appendln('	showMeridian: true,');
        } else {
            $js->appendln('	showMeridian: false,');
        }

        $js->appendln("	template: 'dropdown',");
        $js->appendln('	disableFocus: true');
        $js->appendln('});');

        return $js->text();
    }
}
