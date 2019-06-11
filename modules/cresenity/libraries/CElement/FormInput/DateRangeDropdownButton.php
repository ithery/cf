<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 9, 2019, 6:48:04 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_DateRangeDropdownButton extends CElement_FormInput_DateRangeDropdown {

    protected $dateFormat;
    protected $start;
    protected $end;

    public function __construct($id) {
        parent::__construct($id);
        $this->tag = 'button';
        
    }
    
    public function build() {
        
        $this->addClass('btn dropdown-toggle md-btn-flat');
    }

    public function js($indent = 0) {
        $js = '';
        $js .= "
            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
              $('#".$this->id."').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#".$this->id."').daterangepicker({
              startDate: start,
              endDate: end,
              ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
             },
             opens: 'left' 
            }, cb);

            cb(start, end);
            ";
        return $js;
    }

}
