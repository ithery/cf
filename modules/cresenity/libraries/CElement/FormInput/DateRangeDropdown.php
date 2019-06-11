<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 9, 2019, 6:35:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_DateRangeDropdown extends CElement_FormInput {

    protected $dateFormat;
    protected $start;
    protected $end;

    public function __construct($id) {
        parent::__construct($id);

        CManager::instance()->registerModule('bootstrap-daterangepicker');

        $this->type = "daterange";
        $this->dateFormat = "YYYY-MM-DD";
        $date_format = ccfg::get('date_formatted');
        if ($date_format != null) {
            $date_format = str_replace('Y', 'YYYY', $date_format);
            $date_format = str_replace('m', 'MM', $date_format);
            $date_format = str_replace('d', 'DD', $date_format);
            $this->dateFormat = $date_format;
        }
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
                    format: '" . $this->dateFormat . "'
                },
                
            });
            ";
        return $js;
    }

}
