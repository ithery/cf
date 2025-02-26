<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2019, 2:08:19 AM
 */
class CElement_FormInput_DateTime_MaterialDateTime extends CElement_FormInput_DateTime {
    protected $disableYesterday;

    protected $disableTomorrow;

    protected $disableDate;

    protected $disableTime;

    protected $isAmPmEnabled;
    public function __construct($id) {
        parent::__construct($id);
        CManager::instance()->registerModule('bootstrap-4-material-datepicker');

        $this->dateTimeFormat = 'YYYY-MM-DD';
        $this->disableYesterday = false;
        $this->disableTomorrow = false;
        $this->disableDate = false;
        $this->disableTime = false;
        $this->isAmPmEnabled = true;

        $dateTimeFormat = c::formatter()->getDatetimeFormat();
        if ($dateTimeFormat != null) {
            $dateTimeFormat = str_replace('Y', 'YYYY', $dateTimeFormat);
            $dateTimeFormat = str_replace('m', 'MM', $dateTimeFormat);
            $dateTimeFormat = str_replace('d', 'DD', $dateTimeFormat);
            $dateTimeFormat = str_replace('H', 'HH', $dateTimeFormat);
            $dateTimeFormat = str_replace('i', 'mm', $dateTimeFormat);
            $dateTimeFormat = str_replace(':s', '', $dateTimeFormat);
            $dateTimeFormat = str_replace('s', '', $dateTimeFormat);
            $this->dateTimeFormat = $dateTimeFormat;
        }
    }

    public static function factory($id = null) {
        return new CElement_FormInput_DateTime_MaterialDateTime($id);
    }

    public function setDateTimeFormat($format) {
        $this->dateTimeFormat = $format;

        return $this;
    }

    public function setDisableYesterday($bool = true) {
        $this->disableYesterday = $bool;

        return $this;
    }

    public function setDisableTomorrow($bool = true) {
        $this->disableTomorrow = $bool;

        return $this;
    }

    public function setDisableDate($bool = true) {
        $this->dateTimeFormat = 'HH:mm';
        $this->disableDate = $bool;

        return $this;
    }

    public function setDisableTime($bool = true) {
        $this->dateTimeFormat = 'YYYY-MM-DD';
        $this->disableTime = $bool;

        return $this;
    }

    protected function build() {
        parent::build();
        $this->addClass('form-control');
    }

    public function disableAmPm() {
        $this->isAmPmEnabled = false;
        return $this;
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::js($indent))->br();

        $shortTimeValue = $this->isAmPmEnabled ? 'true' : 'false';
        $option = ' weekStart: 1';
        $option .= " ,format : '" . $this->dateTimeFormat . "'";
        $option .= ' ,shortTime: '. $shortTimeValue;

        if ($this->disableDate) {
            $option .= ' ,date: false';
        }

        if ($this->disableTime) {
            $option .= ' ,time: false';
        }

        if ($this->disableYesterday) {
            if (strlen($option) > 0) {
                if (is_bool($this->disableYesterday)) {
                    $option .= ',minDate: new Date()';
                } else {
                    $option .= ',minDate: new Date(moment("' . $this->disableYesterday . '"))';
                }
            }
        }

        if ($this->disableTomorrow) {
            if (strlen($option) > 0) {
                if (is_bool($this->disableTomorrow)) {
                    $option .= ',maxDate: new Date()';
                } else {
                    $option .= ',maxDate: new Date(moment("' . $this->disableTomorrow . '"))';
                }
            }
        }
        //$option .= " ,nowButton : true";
        //$option .= " ,minDate : new Date()";

        $js->append("$('#" . $this->id . "').bootstrapMaterialDatePicker({" . $option . '});')->br();

        return $js->text();
    }
}
