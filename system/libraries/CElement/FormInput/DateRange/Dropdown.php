<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 13, 2019, 6:38:26 PM
 */
class CElement_FormInput_DateRange_Dropdown extends CElement_FormInput {
    protected $dateFormat;
    protected $momentFormat;
    protected $dateStart;
    protected $dateEnd;

    public function __construct($id) {
        parent::__construct($id);

        CManager::instance()->registerModule('bootstrap-daterangepicker');

        $this->type = 'text';
        $dateFormat = ccfg::get('date_formatted');
        if ($dateFormat == null) {
            $dateFormat = 'Y-m-d';
        }
        $this->dateFormat = $dateFormat;
        $this->momentFormat = $this->convertPHPToMomentFormat($dateFormat);
    }

    public function convertPHPToMomentFormat($format) {
        $replacements = [
            'd' => 'DD',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'E',
            'S' => 'o',
            'w' => 'e',
            'z' => 'DDD',
            'W' => 'W',
            'F' => 'MMMM',
            'm' => 'MM',
            'M' => 'MMM',
            'n' => 'M',
            't' => '', // no equivalent
            'L' => '', // no equivalent
            'o' => 'YYYY',
            'Y' => 'YYYY',
            'y' => 'YY',
            'a' => 'a',
            'A' => 'A',
            'B' => '', // no equivalent
            'g' => 'h',
            'G' => 'H',
            'h' => 'hh',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => 'SSS',
            'e' => 'zz', // deprecated since version 1.6.0 of moment.js
            'I' => '', // no equivalent
            'O' => '', // no equivalent
            'P' => '', // no equivalent
            'T' => '', // no equivalent
            'Z' => '', // no equivalent
            'c' => '', // no equivalent
            'r' => '', // no equivalent
            'U' => 'X',
        ];
        $momentFormat = strtr($format, $replacements);
        return $momentFormat;
    }

    public function convertMomentToPhpFormat($format) {
        $replacements = [
            'DD' => 'd',
            'ddd' => 'D',
            'D' => 'j',
            'dddd' => 'l',
            'E' => 'N',
            'o' => 'S',
            'e' => 'w',
            'DDD' => 'z',
            'W' => 'W',
            'MMMM' => 'F',
            'MM' => 'm',
            'MMM' => 'M',
            'M' => 'n',
            'YYYY' => 'Y',
            'YY' => 'y',
            'a' => 'a',
            'A' => 'A',
            'h' => 'g',
            'H' => 'G',
            'hh' => 'h',
            'HH' => 'H',
            'mm' => 'i',
            'ss' => 's',
            'SSS' => 'u',
            'zz' => 'e',
            'X' => 'U',
        ];

        $phpFormat = strtr($format, $replacements);

        return $phpFormat;
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
