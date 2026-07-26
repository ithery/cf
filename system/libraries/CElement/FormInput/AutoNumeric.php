<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_AutoNumeric extends CElement_FormInput {
    use CTrait_Element_Property_Placeholder;

    /**
     * Number of decimal digits kept (autoNumeric's `mDec` option).
     *
     * @var int
     */
    protected $decimalDigit = 0;

    /**
     * Thousands separator character (autoNumeric's `aSep` option).
     *
     * @var string
     */
    protected $thousandSeparator = ',';

    /**
     * Decimal point character (autoNumeric's `aDec` option).
     *
     * @var string
     */
    protected $decimalSeparator = '.';

    /**
     * @var null|float
     */
    protected $minValue = null;

    /**
     * @var null|float
     */
    protected $maxValue = null;

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);

        $this->type = 'text';
        $this->placeholder = '';
        $this->value = '0';
        $this->decimalDigit = CApp::formatter()->getDecimalDigit();
        $this->thousandSeparator = CApp::formatter()->getThousandSeparator();
        $this->decimalSeparator = CApp::formatter()->getDecimalSeparator();
        $this->addClass('form-control');

        if (!CManager::asset()->module()->isRegisteredModule('auto-numeric')) {
            CManager::asset()->module()->registerRunTimeModule('auto-numeric');
        }
    }

    /**
     * @param null|string $id
     *
     * @return self
     */
    public static function factory($id = null) {
        return new CElement_FormInput_AutoNumeric($id);
    }

    /**
     * @param int $digit
     *
     * @return $this
     */
    public function setDecimalDigit($digit) {
        $this->decimalDigit = $digit;

        return $this;
    }

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setThousandSeparator($separator) {
        $this->thousandSeparator = $separator;

        return $this;
    }

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setDecimalSeparator($separator) {
        $this->decimalSeparator = $separator;

        return $this;
    }

    /**
     * @param float $maxValue
     *
     * @return $this
     */
    public function setMaxValue($maxValue) {
        $this->maxValue = $maxValue;

        return $this;
    }

    /**
     * @param float $minValue
     *
     * @return $this
     */
    public function setMinValue($minValue) {
        $this->minValue = $minValue;

        return $this;
    }

    /**
     * @return void
     */
    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
        if (!isset($this->attr['data-m-dec'])) {
            $this->setAttr('data-m-dec', $this->decimalDigit);
        }
        if (!isset($this->attr['data-a-sep'])) {
            $this->setAttr('data-a-sep', $this->thousandSeparator);
        }
        if (!isset($this->attr['data-a-dec'])) {
            $this->setAttr('data-a-dec', $this->decimalSeparator);
        }
        if ($this->placeholder) {
            $this->setAttr('placeholder', $this->placeholder);
        }

        if ($this->maxValue !== null) {
            $this->setAttr('data-v-max', $this->maxValue);
        }
        if ($this->minValue !== null) {
            $this->setAttr('data-v-min', $this->minValue);
        }
        if ($this->readonly) {
            $this->setAttr('readonly', 'readonly');
        }
        if ($this->disabled) {
            $this->setAttr('disabled', 'disabled');
        }
        $this->addClass('cres:element:control:AutoNumeric');
        $this->setAttr('cres-element', 'control:AutoNumeric');
        $this->setAttr('cres-config', c::json($this->buildControlConfig()));
    }

    /**
     * @return array
     */
    protected function buildControlConfig() {
        $config = [
            'applyJs' => 'autoNumeric',
        ];

        return $config;
    }
}
