<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 13, 2019, 6:38:26 PM
 */
class CElement_FormInput_DateRange_Dropdown extends CElement_FormInput {
    use CElement_Trait_MomentJsTrait;

    protected $dateFormat;

    protected $momentFormat;

    protected $dateStart;

    protected $dateEnd;

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

    public function setValueStart($dateStart) {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function setValueEnd($dateEnd) {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function build() {
        $this->addClass('form-control');
    }

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
