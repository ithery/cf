<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 13, 2019, 6:38:34 PM
 */
use Carbon\Carbon;

class CElement_FormInput_DateRange_DropdownButton extends CElement_FormInput_DateRange_Dropdown {
    use CElement_FormInput_Trait_PredefinedDateRangeTrait;

    protected $start;

    protected $end;

    protected $openDirection = 'left';

    protected $disableCustomRange = false;

    protected $maxSpan;

    protected $previewFormat;

    protected $previewMomentFormat;

    public function __construct($id) {
        parent::__construct($id);
        $this->tag = 'button';
        $this->openDirection = 'left';
        $this->setAttr('capp-input', 'daterange-dropdownbutton');
        $this->addDefaultRange();
        $this->previewFormat = $this->dateFormat;
        $this->previewMomentFormat = $this->convertPHPToMomentFormat($this->dateFormat);
    }

    /**
     * @param string $format PHP date format
     *
     * @return $this
     */
    public function setPreviewFormat($format) {
        $this->previewFormat = $format;
        $this->previewMomentFormat = $this->convertPHPToMomentFormat($this->previewFormat);

        return $this;
    }

    /**
     * @param string $direction left,right
     *
     * @return $this
     */
    public function setOpenDirection($direction) {
        $this->openDirection = $direction;

        return $this;
    }

    public function setDisableCustomRange($bool = true) {
        $this->disableCustomRange = $bool;

        return $this;
    }

    public function setMaxSpan($span) {
        $this->maxSpan = $span;

        return $this;
    }

    protected function getTranslation($key) {
        return c::__('element/date.daterangepicker.' . $key);
    }

    public function build() {
        $this->addClass('btn dropdown-toggle md-btn-flat daterange-dropdownbutton uninit');
        $this->setAttr('type', 'button');
        $this->add($this->dateStart . ' - ' . $this->dateEnd);
        $this->after()->addControl($this->id . '-start', 'hidden')->setName($this->name . '[start]')->setValue($this->dateStart);
        $this->after()->addControl($this->id . '-end', 'hidden')->setName($this->name . '[end]')->setValue($this->dateEnd);
    }

    public function js($indent = 0) {
        $js = '';
        //make sure this element is builded
        $this->buildOnce();

        $jsChange = '';
        foreach ($this->listeners as $listener) {
            if ($listener->getEvent() == 'change') {
                $jsChange = $listener->getBodyJs();
            }
        }

        $jsCustomRange = '';
        if ($this->disableCustomRange) {
            $jsCustomRange = 'showCustomRangeLabel: false,';
        }

        $jsSpan = '';
        if ($this->maxSpan) {
            $jsSpan = "maxSpan: {
                    'days': " . $this->maxSpan . '
                },';
        }

        $jsRange = '';
        $jsRangeProperty = '';
        foreach ($this->predefinedRanges as $range) {
            $label = carr::get($range, 'label');
            $dateStart = carr::get($range, 'dateStart');
            $dateEnd = carr::get($range, 'dateEnd');
            $dateStartJs = 'null';
            if ($dateStart != null) {
                $dateStartJs = "moment('" . ((string) $dateStart) . "')";
            }
            $dateEndJs = 'null';
            if ($dateEnd != null) {
                $dateEndJs = "moment('" . ((string) $dateEnd) . "')";
            }
            $jsRange .= "'" . $label . "': [" . $dateStartJs . ', ' . $dateEndJs . '],';
        }

        if (strlen($jsRange) > 0) {
            $jsRangeProperty = 'ranges: {' . $jsRange . '},';
        }

        $js .= "

            var untilLabel = '" . c::__('element/date.until') . "';
            $('#" . $this->id . "').daterangepicker({
                startDate: moment('" . $this->dateStart . "'),
                endDate: moment('" . $this->dateEnd . "'),
                " . $jsRangeProperty . '
                ' . $jsCustomRange . '
                ' . $jsSpan . "
                opens: '" . $this->openDirection . "',
                locale: {
                    format: '" . $this->momentFormat . "',
                    separator: ' - ',
                    applyLabel: '" . $this->getTranslation('applyLabel') . "',
                    cancelLabel: '" . $this->getTranslation('cancelLabel') . "',
                    fromLabel: '" . $this->getTranslation('fromLabel') . "',
                    toLabel: '" . $this->getTranslation('toLabel') . "',
                    customRangeLabel: '" . $this->getTranslation('customRangeLabel') . "',
                    weekLabel: '" . $this->getTranslation('weekLabel') . "',
                    daysOfWeek: [
                        '" . $this->getTranslation('daysOfWeek.Su') . "',
                        '" . $this->getTranslation('daysOfWeek.Mo') . "',
                        '" . $this->getTranslation('daysOfWeek.Tu') . "',
                        '" . $this->getTranslation('daysOfWeek.We') . "',
                        '" . $this->getTranslation('daysOfWeek.Th') . "',
                        '" . $this->getTranslation('daysOfWeek.Fr') . "',
                        '" . $this->getTranslation('daysOfWeek.Sa') . "'
                    ],
                    monthNames: [
                        '" . $this->getTranslation('monthNames.January') . "',
                        '" . $this->getTranslation('monthNames.February') . "',
                        '" . $this->getTranslation('monthNames.March') . "',
                        '" . $this->getTranslation('monthNames.April') . "',
                        '" . $this->getTranslation('monthNames.May') . "',
                        '" . $this->getTranslation('monthNames.June') . "',
                        '" . $this->getTranslation('monthNames.July') . "',
                        '" . $this->getTranslation('monthNames.August') . "',
                        '" . $this->getTranslation('monthNames.September') . "',
                        '" . $this->getTranslation('monthNames.October') . "',
                        '" . $this->getTranslation('monthNames.November') . "',
                        '" . $this->getTranslation('monthNames.December') . "'
                    ],
                    firstDay: 1
                },
                isInvalidDate: function() {

                }
            }, function (start, end) {
                $('#" . $this->id . "').html(start.format('" . $this->previewMomentFormat . "') + ' - ' + end.format('" . $this->previewMomentFormat . "'));
                $('#" . $this->id . "-start').val(start.format('YYYY-MM-DD'));
                $('#" . $this->id . "-end').val(end.format('YYYY-MM-DD'));
                if(start.format('YYYY-MM-DD')=='1970-01-01') {
                    $('#" . $this->id . "').html(untilLabel + ' ' + end.format('" . $this->previewMomentFormat . "'));
                }
                " . $jsChange . "
            });

            $('#" . $this->id . "').html(moment('" . $this->dateStart . "').format('" . $this->previewMomentFormat . "') + ' - ' + moment('" . $this->dateEnd . "').format('" . $this->previewMomentFormat . "'));
            if(moment('" . $this->dateStart . "').format('YYYY-MM-DD')=='1970-01-01') {
                $('#" . $this->id . "').html(untilLabel + ' ' + moment('" . $this->dateEnd . "').format('" . $this->previewMomentFormat . "'));
            }
            $('#" . $this->id . "').removeClass('uninit');

        ";

        return $js;
    }
}
