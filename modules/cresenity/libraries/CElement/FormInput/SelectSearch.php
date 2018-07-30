<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 12:00:39 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_SelectSearch extends CElement_FormInput {

    use CTrait_Compat_Element_FormInput_SelectSearch;

    protected $query;
    protected $formatSelection;
    protected $formatResult;
    protected $keyField;
    protected $searchField;
    protected $multiple;
    protected $placeholder;
    protected $autoSelect;
    protected $minInputLength;
    protected $dropdownClasses;
    protected $delay;

    public function __construct($id) {
        parent::__construct($id);

        $this->dropdownClasses = array();
        $this->type = "selectsearch";
        $this->query = "";
        $this->formatSelection = "";
        $this->formatResult = "";
        $this->keyField = "";
        $this->searchField = "";
        $this->placeholder = "Search for a item";
        $this->multiple = false;
        $this->autoSelect = false;
        $this->minInputLength = 0;
        $this->delay = 100;
    }

    public static function factory($id) {
        return new CElement_FormInput_SelectSearch($id);
    }

    public function setMultiple($bool) {
        $this->multiple = $bool;
        return $this;
    }

    public function setDelay($val) {
        $this->delay = $val;
        return $this;
    }

    public function setAutoSelect($bool) {
        $this->autoSelect = $bool;
        return $this;
    }

    public function setMinInputLength($minInputLength) {
        $this->minInputLength = $minInputLength;
        return $this;
    }

    public function setKeyField($keyField) {
        $this->keyField = $keyField;
        return $this;
    }

    public function setSearchField($searchField) {
        $this->searchField = $searchField;
        return $this;
    }

    public function setQuery($query) {
        $this->query = $query;
        return $this;
    }

    public function setFormatResult($fmt) {
        $this->formatResult = $fmt;
        return $this;
    }

    public function setFormatSelection($fmt) {
        $this->formatSelection = $fmt;
        return $this;
    }

    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function addDropdownClass($c) {
        if (is_array($c)) {
            $this->dropdownClasses = array_merge($c, $this->dropdownClasses);
        } else {
            if ($this->bootstrap == '3.3') {
                $c = str_replace('span', 'col-md-', $c);
                $c = str_replace('row-fluid', 'row', $c);
            }
            $this->dropdownClasses[] = $c;
        }
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        $disabled = "";
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }
        $multiple = "";
        if ($this->multiple) {
            $multiple = ' multiple="multiple"';
        }
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }

        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0) {
            $classes = " " . $classes;
        }

        $classes = $classes . " form-control ";

        $html->setIndent($indent);
        $value = $this->value;
        if ($this->autoSelect) {
            $db = CDatabase::instance();
            $rjson = 'false';

            $q = "select `" . $this->keyField . "` from (" . $this->query . ") as a limit 1";
            $value = cdbutils::get_value($q);
        }
        if (strlen($this->value) > 0) {
            $value = $this->value;
        }


        $html->appendln('<select class="' . $classes . '" name="' . $this->name . '" id="' . $this->id . '" ' . $disabled . $custom_css . $multiple . '">');

        // select2 4.0 using option to set default value
        if (strlen($this->value) > 0 || $this->autoSelect) {
            $db = CDatabase::instance();
            $rjson = 'false';

            if ($this->autoSelect) {
                $q = "select * from (" . $this->query . ") as a limit 1";
            } else {
                $q = "select * from (" . $this->query . ") as a where `" . $this->keyField . "`=" . $db->escape($this->value);
            }
            $r = $db->query($q)->result_array(false);
            if (count($r) > 0) {
                $r = $r[0];
                $str_selection = $this->formatSelection;
                $str_selection = str_replace("'", "\'", $str_selection);
                preg_match_all("/{([\w]*)}/", $str_selection, $matches, PREG_SET_ORDER);

                foreach ($matches as $val) {
                    $str = $val[1]; //matches str without bracket {}
                    $b_str = $val[0]; //matches str with bracket {}
                    $str_selection = str_replace($b_str, $r[$str], $str_selection);
                }

                $html->appendln('<option value="' . $this->value . '">' . $str_selection . '</option>');
            }
        }
        $html->appendln('</select>');
        $html->br();

        return $html->text();
    }

    public function createAjaxUrl() {
        return CAjaxMethod::factory()
                        ->set_type('searchselect')
                        ->set_data('query', $this->query)
                        ->set_data('keyField', $this->keyField)
                        ->set_data('searchField', $this->searchField)
                        ->makeurl();
    }

    public function js($indent = 0) {
        $ajax_url = $this->createAjaxUrl();

        $str_selection = $this->formatSelection;
        $str_result = $this->formatResult;

        $str_selection = str_replace("'", "\'", $str_selection);
        $str_result = str_replace("'", "\'", $str_result);
        preg_match_all("/{([\w]*)}/", $str_selection, $matches, PREG_SET_ORDER);

        foreach ($matches as $val) {
            $thousand_separator_pre = '';
            $thousand_separator_post = '';
            $str = $val[1]; //matches str without bracket {}
            $b_str = $val[0]; //matches str with bracket {}
            if (strlen($str) > 0) {
                $str_selection = str_replace($b_str, "'+item." . $str . "+'", $str_selection);
            }
        }

        preg_match_all("/{([\w]*)}/", $str_result, $matches, PREG_SET_ORDER);


        foreach ($matches as $val) {
            $thousand_separator_pre = '';
            $thousand_separator_post = '';
            $str = $val[1]; //matches str without bracket {}
            $b_str = $val[0]; //matches str with bracket {}
            if (strlen($str) > 0) {
                $str_result = str_replace($b_str, "'+item." . $str . "+'", $str_result);
            }
        }
        if (strlen($str_result) == 0) {
            $searchFieldText = CF::value($this->searchField);
            if (strlen($searchFieldText) > 0) {
                $str_result = "'+item." . $searchFieldText . "+'";
            }
        }
        if (strlen($str_selection) == 0) {
            $searchFieldText = CF::value($this->searchField);
            if (strlen($searchFieldText) > 0) {
                $str_selection = "'+item." . $searchFieldText . "+'";
            }
        }

        $str_result = preg_replace("/[\r\n]+/", "", $str_result);
        $placeholder = "Search for a item";
        if (strlen($this->placeholder) > 0) {
            $placeholder = $this->placeholder;
        }
        $str_js_change = "";
        if ($this->submit_onchange) {
            $str_js_change = "$(this).closest('form').submit();";
        }

        $str_js_init = "";
        if ($this->autoSelect) {
            $db = CDatabase::instance();
            $rjson = 'false';

            $q = "select * from (" . $this->query . ") as a limit 1";
            $r = $db->query($q)->result_array(false);
            if (count($r) > 0) {
                $r = $r[0];
            }
            $rjson = json_encode($r);


            $str_js_init = "
				initSelection : function (element, callback) {
					
				var data = " . $rjson . ";
				
				callback(data);
			},
			";
        }




        $str_multiple = "";
        if ($this->multiple) {
            $str_multiple = " multiple:'true',";
        }
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0) {
            $classes = " " . $classes;
        }

        //$classes = $classes . " form-control ";

        $dropdownClasses = $this->dropdownClasses;
        $dropdownClasses = implode(" ", $dropdownClasses);
        if (strlen($dropdownClasses) > 0) {
            $dropdownClasses = " " . $dropdownClasses;
        }

        $str = "

            $('#" . $this->id . "').select2({
                width: '100%',
                placeholder: '" . $placeholder . "',
                minimumInputLength: '" . $this->minInputLength . "',
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                        url: '" . $ajax_url . "',
                        dataType: 'jsonp',
                        quietMillis: " . $this->delay . ", 
                        delay: " . $this->delay . ", 
                        " . $str_multiple . "
                        data: function (params) {
                            return {
                              q: params.term, // search term
                              page: params.page,
                              limit: 10
                            };
                        },
                        processResults: function (data, params) { 
                            // parse the results into the format expected by Select2
                            // since we are using custom formatting functions we do not need to
                            // alter the remote JSON data, except to indicate that infinite
                            // scrolling can be used
                            params.page = params.page || 1;
                            var more = (params.page * 10) < data.total;
                            return {
                                    results: data.data,
                                    pagination: {
                                      more: more
                                    }
                                  };
                        },
                        cache:true,
                    },
                " . $str_js_init . "
                templateResult: function(item) {
                    if (typeof item.loading !== 'undefined') {
                        return item.text;
                    }
                    return $('<div>" . $str_result . "</div>');
                }, // omitted for brevity, see the source of this page
                templateSelection: function(item) {
                    if (item.id === '' || item.selected) {
                        return item.text;
                    }
                    else {
                        return $('<div>" . $str_selection . "</div>');
                    }
                },  // omitted for brevity, see the source of this page
                dropdownCssClass: '" . $dropdownClasses . "', // apply css that makes the dropdown taller
                containerCssClass : 'tpx-select2-container " . $classes . "'
            }).change(function() {
                        " . $str_js_change . "
            });
            
        ";


        $js = new CStringBuilder();
        $js->append(parent::jsChild($indent))->br();
        $js->setIndent($indent);
        //echo $str;
        $js->append($str)->br();





        return $js->text();
    }

}
