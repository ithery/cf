<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2019, 6:38:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Carbon\Carbon;

class CElement_FormInput_DateRange_DropdownButton extends CElement_FormInput_DateRange_Dropdown {

    protected $dateFormat;
    protected $start;
    protected $end;
    protected $predefinedRanges;

    public function __construct($id) {
        parent::__construct($id);
        $this->tag = 'button';
        $this->resetRange();
    }

    public function resetRange() {
        $this->predefinedRanges = array();
        return $this;
    }

    public function addRange($label, $dateStart, $dateEnd) {
        $this->predefinedRanges[] = array(
            "label" => $label,
            "dateStart" => $dateStart,
            "dateEnd" => $dateEnd,
        );
        return $this;
    }

    public function addToday($label='Today') {
        $dateStart = Carbon::now();
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }
    
    public function addYesterday($label='Yesterday') {
        $dateStart = Carbon::yesterday();
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }
    public function addRange7Days($label='Last 7 Days') {
        $dateStart = Carbon::now()->subDay(7);
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }
    
    public function addRange14Days($label='Last 14 Days') {
        $dateStart = Carbon::now()->subDay(14);
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }
    
    public function addRange30Days($label='Last 30 Days') {
        $dateStart = Carbon::now()->subDay(30);
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }
    
    public function addRangeThisWeek($label='This Week') {
        $dateStart = Carbon::now()->weekday(0);
        $dateEnd = Carbon::now()->weekday(7);
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }
    
    public function addDefaultRange() {
        $this->resetRange();
        $this->addToday();
        $this->addYesterday();
        $this->addRange7Days();
        $this->addRange14Days();
        $this->addRange30Days();
        $this->addRangeThisWeek();
        return $this;
    }

    public function build() {

        $this->addClass('btn dropdown-toggle md-btn-flat');
        $this->add($this->dateStart . ' ' . $this->dateEnd);
        
        if(is_array($this->predefinedRanges) && count($this->predefinedRanges)==0) {
          
            $this->addDefaultRange();
             
        }
    }

    public function js($indent = 0) {
        $js = '';
        //make sure this element is builded
        $this->buildOnce();
        
        $jsRange = '';
        foreach ($this->predefinedRanges as $range) {
            $label = carr::get($range, 'label');
            $dateStart = carr::get($range, 'dateStart');
            $dateEnd = carr::get($range, 'dateEnd');
            $jsRange .= "'" . $label . "': [moment('" . $dateStart . "'), moment('" . $dateEnd . "')],";
        }
       
        $js .= "
           
          
            $('#" . $this->id . "').daterangepicker({
                startDate: moment('" . $this->dateStart . "'),
                endDate: moment('" . $this->dateEnd . "'),
                ranges: {" . $jsRange . "},
                opens: 'left',
                locale: {
                    format: '" . $this->momentFormat . "'
                },
            }, function (start, end) {
                $('#" . $this->id . "').html(start.format('" . $this->momentFormat . "') + ' - ' + end.format('" . $this->momentFormat . "'));
            });

            $('#" . $this->id . "').html(moment('" . $this->dateStart . "').format('" . $this->momentFormat . "') + ' - ' + moment('" . $this->dateEnd . "').format('" . $this->momentFormat . "'));
        ";
        return $js;
    }

}
