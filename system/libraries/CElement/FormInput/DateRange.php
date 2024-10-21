<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Nov 30, 2018, 4:32:01 PM
 */
class CElement_FormInput_DateRange extends CElement_FormInput {
    protected $dateFormat;

    protected $dateStart;

    protected $dateEnd;

    protected $haveButton;

    public function __construct($id) {
        parent::__construct($id);

        if (CManager::isRegisteredModule('bootstrap-4-material')) {
            CManager::instance()->registerModule('bootstrap-4-material-datepicker');
        } elseif (CManager::isRegisteredModule('bootstrap-4')) {
            CManager::instance()->registerModule('bootstrap-4-datepicker');
        } else {
            CManager::instance()->registerModule('datepicker');
        }

        $this->type = 'date';
        $this->dateFormat = 'yyyy-mm-dd';
        $dateFormat = c::formatter()->getDateFormat();
        if ($dateFormat != null) {
            $dateFormat = str_replace('Y', 'yyyy', $dateFormat);
            $dateFormat = str_replace('m', 'mm', $dateFormat);
            $dateFormat = str_replace('d', 'dd', $dateFormat);
            $this->dateFormat = $dateFormat;
        }

        $this->addClass('form-control');
    }

    public function setHaveButton($boolean) {
        $this->haveButton = $boolean;

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
        $classes .= ' form-control';
        $custom_css = $this->custom_css;
        $custom_css = $this->renderStyle($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }

        $html->appendln('<div class="input-daterange input-group" id="' . $this->id . '">')->br();
        $html->appendln('<input type="text" name="' . $this->name . '[start]"  data-date-format="' . $this->dateFormat . '" id="' . $this->id . '-start" class="datepicker input-unstyled' . $classes . $this->validation->validationClass() . '" value="' . $this->value . '"' . $disabled . $addition_attribute . $custom_css . '>')->br();
        $html->appendln('<div class="input-group-prepend"><span class="input-group-text">to</span></div>');
        $html->appendln('<input type="text" name="' . $this->name . '[end]"  data-date-format="' . $this->dateFormat . '" id="' . $this->id . '-end" class="datepicker input-unstyled' . $classes . $this->validation->validationClass() . '" value="' . $this->value . '"' . $disabled . $addition_attribute . $custom_css . '>')->br();
        $html->appendln('</div>');

        return $html->text();
    }

    public function js($indent = 0) {
        $option = '';

        $autoclose = 'true';
        if (strlen($option) > 0) {
            $option .= ',';
        }
        $option .= 'autoclose: ' . $autoclose . '';

        if (strlen($option) > 0) {
            $option = '{' . $option . '}';
        }
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::js($indent))->br();

        $js->append("$('#" . $this->id . "').datepicker(" . $option . ');')->br();

        return $js->text();
    }
}
