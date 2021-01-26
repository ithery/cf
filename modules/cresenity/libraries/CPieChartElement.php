<?php

/**
 * @deprecated 2.0
 */
//@codingStandardsIgnoreStart
class CPieChartElement extends CChartElement {
    protected $percentage = true;
    private $data;

    public function __construct($id) {
        parent::__construct($id);
        CManager::instance()->register_module('flot');
    }

    public static function factory($id) {
        return new CPieChartElement($id);
    }

    public function set_data($data) {
        $this->data = $data;
    }

    public function set_list($list) {
        $this->data = cflot::list2piedata($list);
    }

    public function get_percentage() {
        return $this->percentage;
    }

    public function set_percentage($percentage) {
        $this->percentage = $percentage;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $classes = $this->generate_class();
        $html->append('<div class="flot-container"><div id="' . $this->id . '" class="flot ' . $classes . '"></div></div>');

        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::js($indent))->br();

        if ($this->percentage) {
            $formatter = "return '<div style=\"font-size:8pt;text-align:center;padding:2px;color:white;\">'+label+'<br/>'+Math.round(series.percent)+'%</div>';";
        } else {
            $formatter = "return '<div style=\"font-size:8pt;text-align:center;padding:2px;color:white;\">'+label+'<br/>'+series.data[0][1]+'</div>';";
        }
        $js->append('
        var data_category_' . $this->id . ' = ' . json_encode($this->data) . ";

//			console.log('" . json_encode($this->data) . "');
        jQuery.plot($('#" . $this->id . "'), data_category_" . $this->id . ', {
                        series: {
                            pie: {
                                show: true,
                                radius: 1,
                                label: {
                                    show: true,
                                    radius: 1,
                                    formatter: function(label, series){
                                        ' . $formatter . '
                                    },
                                    background: { opacity: 0.8 }
                                }
                            }
                        },
                        legend: {
                            show: false
                        }
        });


    ');
        return $js->text();
    }
}
//@codingStandardsIgnoreEnd
