<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2019, 6:38:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Carbon\Carbon;

class CElement_FormInput_DateRange_DropdownButton extends CElement_FormInput_DateRange_Dropdown {

    use CElement_FormInput_Trait_PredefinedDateRangeTrait;

    protected $dateFormat;
    protected $start;
    protected $end;

    public function __construct($id) {
        parent::__construct($id);
        $this->tag = 'button';
        $this->setAttr('capp-input', 'daterange-dropdownbutton');
        $this->resetRange();
    }

    public function build() {

        $this->addClass('btn dropdown-toggle md-btn-flat daterange-dropdownbutton');
        $this->setAttr('type','button');
        $this->add($this->dateStart . ' - ' . $this->dateEnd);
        $this->after()->addControl($this->id . '-start', 'hidden')->setName($this->name . '[start]')->setValue($this->dateStart);
        $this->after()->addControl($this->id . '-end', 'hidden')->setName($this->name . '[end]')->setValue($this->dateEnd);
        if (is_array($this->predefinedRanges) && count($this->predefinedRanges) == 0) {
            $this->addDefaultRange();
        }
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


        $jsRange = '';
        foreach ($this->predefinedRanges as $range) {
            $label = carr::get($range, 'label');
            $dateStart = carr::get($range, 'dateStart');
            $dateEnd = carr::get($range, 'dateEnd');
            $dateStartJs = "null";
            if (strlen($dateStart > 0)) {
                $dateStartJs = "moment('" . $dateStart . "')";
            }
            $dateEndJs = "null";
            if (strlen($dateStart > 0)) {
                $dateEndJs = "moment('" . $dateEnd . "')";
            }
            $jsRange .= "'" . $label . "': [" . $dateStartJs . ", " . $dateEndJs . "],";
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
                isInvalidDate: function() {
                    
                }
            }, function (start, end) {
                
                  
                  
                    $('#" . $this->id . "').html(start.format('" . $this->momentFormat . "') + ' - ' + end.format('" . $this->momentFormat . "'));
                    $('#" . $this->id . "-start').val(start.format('" . $this->momentFormat . "'));
                    $('#" . $this->id . "-end').val(end.format('" . $this->momentFormat . "'));
                    if(start.format('" . $this->momentFormat . "')=='1970-01-01') { 
                        $('#" . $this->id . "').html('Until ' + end.format('" . $this->momentFormat . "'));
                    }
                " . $jsChange . "
            });

            $('#" . $this->id . "').html(moment('" . $this->dateStart . "').format('" . $this->momentFormat . "') + ' - ' + moment('" . $this->dateEnd . "').format('" . $this->momentFormat . "'));
        ";
        return $js;
    }

}
