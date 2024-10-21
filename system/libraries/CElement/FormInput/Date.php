<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 24, 2018, 3:56:10 PM
 */
class CElement_FormInput_Date extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Date;

    protected $dateFormat;

    protected $haveButton;

    protected $startDate;

    protected $endDate;

    protected $disableDay;

    protected $inline;

    public function __construct($id) {
        parent::__construct($id);

        if (CManager::isRegisteredModule('bootstrap-4-material')) {
            CManager::instance()->registerModule('bootstrap-4-material-datepicker');
        } elseif (CManager::isRegisteredModule('bootstrap-4')) {
            CManager::instance()->registerModule('bootstrap-4-datepicker');
        } else {
            CManager::instance()->registerModule('datepicker');
        }
        //CManager::instance()->registerModule('datepicker');
        $this->type = 'date';
        $this->dateFormat = c::formatter()->getDateFormat();

        $this->haveButton = false;
        $this->startDate = '';
        $this->endDate = '';
        $this->disableDay = [];
        $this->inline = false;
        $this->addClass('form-control');
    }

    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    public function setStartDate($str) {
        $this->startDate = $str;

        return $this;
    }

    public function setEndDate($str) {
        $this->endDate = $str;

        return $this;
    }

    /**
     * @param string|array $day
     *
     * @return $this
     */
    public function addDisableDaysOfWeek($day) {
        $dayArray = $day;
        if (!is_array($day)) {
            $dayArray = explode(',', $day);
        }

        foreach ($dayArray as $d) {
            $this->disableDay[] = trim($d);
        }

        return $this;
    }

    public function setDateFormat($str) {
        $this->dateFormat = $str;

        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $disabled = '';
        $readonly = '';
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }
        if ($this->readonly) {
            $readonly = ' readonly="readonly"';
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

        $custom_css = $this->renderStyle($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }

        if ($this->haveButton) {
            $html->appendln('<div class="input-append date" id="dp3" data-date="' . $this->value . '" data-date-format="' . $this->dateFormat . '">
                        <input class="input-unstyled ' . $classes . $this->validation->validationClass() . '" size="16" type="text" name="' . $this->name . '"  data-date-format="' . $this->dateFormat . '" id="' . $this->id . '" value="' . $this->value . '"' . $disabled . $readonly . $addition_attribute . $custom_css . '>
                        <span class="add-on"><i class="icon-th"></i></span>
                    </div>')->br();
        } else {
            $html->appendln('<input type="text" name="' . $this->name . '" id="' . $this->id . '" class="datepicker input-unstyled' . $classes . $this->validation->validationClass() . '" value="' . c::formatter()->formatDate($this->value, $this->dateFormat) . '"' . $disabled . $readonly . $addition_attribute . $custom_css . '>')->br();
        }
        //$html->appendln('<input type="text" name="'.$this->name.'"  data-date-format="'.$this->dateFormat.'" id="'.$this->id.'" class="datepicker input-unstyled'.$classes.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.$custom_css.'>')->br();

        return $html->text();
    }

    protected function getTranslation($key) {
        return c::__('element/date.datepicker.' . $key);
    }

    public function js($indent = 0) {
        $jsLanguages = "$.fn.datepicker.dates['custom'] = {
            days: [
                '" . $this->getTranslation('days.Sunday') . "',
                '" . $this->getTranslation('days.Monday') . "',
                '" . $this->getTranslation('days.Tuesday') . "',
                '" . $this->getTranslation('days.Wednesday') . "',
                '" . $this->getTranslation('days.Thursday') . "',
                '" . $this->getTranslation('days.Friday') . "',
                '" . $this->getTranslation('days.Saturday') . "'
            ],
            daysShort: [
                '" . $this->getTranslation('daysShort.Sun') . "',
                '" . $this->getTranslation('daysShort.Mon') . "',
                '" . $this->getTranslation('daysShort.Tue') . "',
                '" . $this->getTranslation('daysShort.Wed') . "',
                '" . $this->getTranslation('daysShort.Thu') . "',
                '" . $this->getTranslation('daysShort.Fri') . "',
                '" . $this->getTranslation('daysShort.Sat') . "'
            ],
            daysMin: [
                '" . $this->getTranslation('daysMin.Su') . "',
                '" . $this->getTranslation('daysMin.Mo') . "',
                '" . $this->getTranslation('daysMin.Tu') . "',
                '" . $this->getTranslation('daysMin.We') . "',
                '" . $this->getTranslation('daysMin.Th') . "',
                '" . $this->getTranslation('daysMin.Fr') . "',
                '" . $this->getTranslation('daysMin.Sa') . "'
            ],
            months: [
                '" . $this->getTranslation('months.January') . "',
                '" . $this->getTranslation('months.February') . "',
                '" . $this->getTranslation('months.March') . "',
                '" . $this->getTranslation('months.April') . "',
                '" . $this->getTranslation('months.May') . "',
                '" . $this->getTranslation('months.June') . "',
                '" . $this->getTranslation('months.July') . "',
                '" . $this->getTranslation('months.August') . "',
                '" . $this->getTranslation('months.September') . "',
                '" . $this->getTranslation('months.October') . "',
                '" . $this->getTranslation('months.November') . "',
                '" . $this->getTranslation('months.December') . "'
            ],
            monthsShort: [
                '" . $this->getTranslation('monthsShort.Jan') . "',
                '" . $this->getTranslation('monthsShort.Feb') . "',
                '" . $this->getTranslation('monthsShort.Mar') . "',
                '" . $this->getTranslation('monthsShort.Apr') . "',
                '" . $this->getTranslation('monthsShort.May') . "',
                '" . $this->getTranslation('monthsShort.Jun') . "',
                '" . $this->getTranslation('monthsShort.Jul') . "',
                '" . $this->getTranslation('monthsShort.Aug') . "',
                '" . $this->getTranslation('monthsShort.Sep') . "',
                '" . $this->getTranslation('monthsShort.Oct') . "',
                '" . $this->getTranslation('monthsShort.Nov') . "',
                '" . $this->getTranslation('monthsShort.Dec') . "',
            ],
            today: '" . $this->getTranslation('today') . "',
            clear: '" . $this->getTranslation('clear') . "',
            format: 'yyyy-mm-dd',
            titleFormat: 'MM yyyy', /* Leverages same syntax as 'format' */
            weekStart: 0
        }
        ";
        $dayMap = [
            'sunday' => '0',
            'monday' => '1',
            'tuesday' => '2',
            'wednesday' => '3',
            'thursday' => '4',
            'friday' => '5',
            'saturday' => '6',
        ];

        foreach ($this->disableDay as $k => $v) {
            if (isset($dayMap[strtolower($this->disableDay[$k])])) {
                $this->disableDay[$k] = $dayMap[strtolower($this->disableDay[$k])];
            }
        }

        $disableDayStr = implode(',', $this->disableDay);

        $jsOption = "
            format: {
                toDisplay: function (date, format, language) {
                    let timezoneOffset = date.getTimezoneOffset();
                    let newDate = new Date(date.getTime() + (timezoneOffset * 60000));
                    let formatted = cresenity.formatter.formatDate(newDate,'" . $this->dateFormat . "');

                    return formatted;
                },
                toValue: function (date, format, language) {
                    let dateUnformat = cresenity.formatter.unformatDate(date,'" . $this->dateFormat . "');
                    let timezoneOffset = dateUnformat.getTimezoneOffset();
                    return new Date(dateUnformat.getTime() - (timezoneOffset * 60000));

                }
            }
            ,language: 'custom'
        ";

        if (strlen($this->startDate) > 0) {
            $jsOption .= ",startDate: '" . $this->startDate . "'";
        }
        if (strlen($this->endDate) > 0) {
            $jsOption .= ",endDate: '" . $this->endDate . "'";
        }
        if (strlen($disableDayStr)) {
            $jsOption .= ",daysOfWeekDisabled: '" . $disableDayStr . "'";
        }
        $autoclose = 'true';
        $jsOption .= ',autoclose: ' . $autoclose . '';

        $option = '{' . $jsOption . '}';
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::js($indent))->br();

        if ($this->haveButton) {
            $js->append("$('#" . $this->id . "').parent().datepicker(" . $option . ');')->br();
        } else {
            $js->append("$('#" . $this->id . "').datepicker(" . $option . ');')->br();
        }

        $jsText = $jsLanguages . $js->text();

        return $jsText;
    }
}
