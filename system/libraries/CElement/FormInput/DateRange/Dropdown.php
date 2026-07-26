<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_DateRange_Dropdown extends CElement_FormInput {
    use CElement_Trait_MomentJsTrait;

    /**
     * @var string
     */
    protected $dateFormat;

    /**
     * @var string
     */
    protected $momentFormat;

    /**
     * @var mixed
     */
    protected $dateStart;

    /**
     * @var mixed
     */
    protected $dateEnd;

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);

        CManager::instance()->registerModule('bootstrap-daterangepicker');

        $this->type = 'text';
        $dateFormat = c::formatter()->getDateFormat();
        if ($dateFormat == null) {
            $dateFormat = 'Y-m-d';
        }
        $this->dateFormat = $dateFormat;
        $this->momentFormat = $this->convertPHPToMomentFormat($dateFormat);
    }

    /**
     * @param mixed $value A {@see CPeriod} (its start/end are used) or a single
     *                      date value applied to both the start and end
     *
     * @return $this
     */
    public function setValue($value) {
        if ($value instanceof CPeriod) {
            $this->setValueStart($value->startDate);
            $this->setValueEnd($value->endDate);
        } else {
            $this->setValueStart($value);
            $this->setValueEnd($value);
        }

        return $this;
    }

    /**
     * @param mixed $dateStart
     *
     * @return $this
     */
    public function setValueStart($dateStart) {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * @param mixed $dateEnd
     *
     * @return $this
     */
    public function setValueEnd($dateEnd) {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * @return void
     */
    public function build() {
        $this->addClass('form-control');
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = '';
        $js .= "
            $('#" . $this->id . "').daterangepicker({
                opens: 'left',
                locale: {
                    format: '" . $this->momentFormat . "'
                },

            });
            ";

        return $js;
    }
}
