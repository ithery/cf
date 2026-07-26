<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_DateTime_MaterialDateTime extends CElement_FormInput_DateTime {
    /**
     * `false` to allow yesterday, `true` to disable it from today, or a date
     * string used as the picker's `minDate`.
     *
     * @var bool|string
     */
    protected $disableYesterday;

    /**
     * `false` to allow tomorrow, `true` to disable it from today, or a date
     * string used as the picker's `maxDate`.
     *
     * @var bool|string
     */
    protected $disableTomorrow;

    /**
     * @var bool
     */
    protected $disableDate;

    /**
     * @var bool
     */
    protected $disableTime;

    /**
     * @var bool
     */
    protected $isAmPmEnabled;

    /**
     * @param string $id
     *
     * @return void
     */
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

    /**
     * @param null|string $id
     *
     * @return self
     */
    public static function factory($id = null) {
        return new CElement_FormInput_DateTime_MaterialDateTime($id);
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setDateTimeFormat($format) {
        $this->dateTimeFormat = $format;

        return $this;
    }

    /**
     * @param bool|string $bool `true`/`false`, or a date string used as the minimum
     *
     * @return $this
     */
    public function setDisableYesterday($bool = true) {
        $this->disableYesterday = $bool;

        return $this;
    }

    /**
     * @param bool|string $bool `true`/`false`, or a date string used as the maximum
     *
     * @return $this
     */
    public function setDisableTomorrow($bool = true) {
        $this->disableTomorrow = $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setDisableDate($bool = true) {
        $this->dateTimeFormat = 'HH:mm';
        $this->disableDate = $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setDisableTime($bool = true) {
        $this->dateTimeFormat = 'YYYY-MM-DD';
        $this->disableTime = $bool;

        return $this;
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();
        $this->addClass('form-control');
    }

    /**
     * @return $this
     */
    public function disableAmPm() {
        $this->isAmPmEnabled = false;

        return $this;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::js($indent))->br();

        $shortTimeValue = $this->isAmPmEnabled ? 'true' : 'false';
        $option = ' weekStart: 1';
        $option .= " ,format : '" . $this->dateTimeFormat . "'";
        $option .= ' ,shortTime: ' . $shortTimeValue;

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
