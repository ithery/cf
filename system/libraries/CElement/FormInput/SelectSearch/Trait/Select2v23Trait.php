<?php

trait CElement_FormInput_SelectSearch_Trait_Select2v23Trait {
    public function htmlSelect2v23($indent = 0) {
        return '<input type="text" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled validate[]" value="' . $this->value . '">';
    }

    public function jsSelect2v23($indent = 0) {
        if ($this->value > 0) {
            $this->autoSelect = true;
        }
        $ajaxUrl = $this->createAjaxUrl();

        $strSelection = $this->formatSelection;
        $strResult = $this->formatResult;

        $strSelection = $this->generateSelect2Template($strSelection);
        $strResult = $this->generateSelect2Template($strResult);

        if (strlen($strResult) == 0) {
            $searchFieldText = c::value($this->searchField);
            if (strlen($searchFieldText) > 0) {
                $strResult = "'+item." . $searchFieldText . "+'";
            }
        }
        if (strlen($strSelection) == 0) {
            $searchFieldText = c::value($this->searchField);
            if (strlen($searchFieldText) > 0) {
                $strSelection = "'+item." . $searchFieldText . "+'";
            }
        }

        $strResult = preg_replace("/[\r\n]+/", '', $strResult);
        $placeholder = 'Search for a item';
        if (strlen($this->placeholder) > 0) {
            $placeholder = $this->placeholder;
        }
        $strJsChange = '';
        if ($this->submit_onchange) {
            $strJsChange = "$(this).closest('form').submit();";
        }

        $strJsInit = '';
        if ($this->autoSelect) {
            $db = CDatabase::instance();
            $rjson = 'false';

            $q = 'select * from (' . $this->query . ') as a limit 1';
            $r = $db->query($q)->resultArray(false);
            if (count($r) > 0) {
                $r = $r[0];
                if ($this->valueCallback != null && is_callable($this->valueCallback)) {
                    foreach ($r as $k => $val) {
                        $r[$k] = $this->valueCallback($r, $k, $val);
                    }
                }
            }
            $rjson = json_encode($r);

            $strJsInit = '
                initSelection : function (element, callback) {
                    var data = ' . $rjson . ';
                    callback(data);
                },
            ';
        }

        $strMultiple = '';
        if ($this->multiple) {
            $strMultiple = " multiple:'true',";
        }
        $classes = $this->classes;
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }

        //$classes = $classes . " form-control ";

        $dropdownClasses = $this->dropdownClasses;
        $dropdownClasses = implode(' ', $dropdownClasses);
        if (strlen($dropdownClasses) > 0) {
            $dropdownClasses = ' ' . $dropdownClasses;
        }

        $str = "

            $('#" . $this->id . "').select2({
                width: '100%',
                placeholder: '" . $placeholder . "',
                minimumInputLength: '" . $this->minInputLength . "',
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                        url: '" . $ajaxUrl . "',
                        dataType: 'jsonp',
                        quietMillis: " . $this->delay . ',
                        delay: ' . $this->delay . ',
                        ' . $strMultiple . '
                        data: function (term,page) {
                            return {
                                q: term, // search term
                                page: page,
                                limit: 10
                            };
                        },
                        results: function (data, page) {
                            // parse the results into the format expected by Select2
                            // since we are using custom formatting functions we do not need to
                            // alter the remote JSON data, except to indicate that infinite
                            // scrolling can be used
                            page = page || 1;
                            var more = (page * 10) < data.total;
                            return {results: data.data, more: more};
                        },
                        cache:true,
                        error: function (jqXHR, status, error) {
                            if(cresenity && cresenity.handleAjaxError) {
                                cresenity.handleAjaxError(jqXHR, status, error);
                            }
                        }
                    },
                ' . $strJsInit . "
                formatResult: function(item) {
                    if (typeof item.loading !== 'undefined') {
                        return item.text;
                    }
                    return $('<div>" . $strResult . "</div>');
                }, // omitted for brevity, see the source of this page
                formatSelection: function(item) {
                    if (item.id === '' || item.selected) {
                        return item.text;
                    }
                    else {
                        return $('<div>" . $strSelection . "</div>');
                    }
                },  // omitted for brevity, see the source of this page
                dropdownCssClass: '" . $dropdownClasses . "', // apply css that makes the dropdown taller
                containerCssClass : 'tpx-select2-container " . $classes . "'
            }).change(function() {
                " . $strJsChange . "
            });
            $('#" . $this->id . "').on('select2:open',function(event){
                var modal = $('#" . $this->id . "').closest('.modal');
                if(modal[0]){
                    var modalZ=modal.css('z-index');
                    var newZ=parseInt(modalZ)+1;
                    $('#" . $this->id . "').data('select2').\$container.css('z-index',newZ);
                    $('#" . $this->id . "').data('select2').\$dropdown.css('z-index',newZ);
                    $('#" . $this->id . "').data('select2').\$element.css('z-index',newZ);
                    $('#" . $this->id . "').data('select2').\$results.css('z-index',newZ);
                    $('#" . $this->id . "').data('select2').\$selection.css('z-index',newZ);
                }
            });
        ";
        if ($this->valueCallback != null && is_callable($this->valueCallback)) {
            $str .= "
                $('#" . $this->id . "').trigger('change');
            ";
        }

        $js = new CStringBuilder();
        $js->append(parent::jsChild($indent))->br();
        $js->setIndent($indent);
        //echo $str;
        $js->append($str)->br();
        foreach ($this->dependsOn as $index => $dependOn) {
            $dependsOnSelector = $dependOn->getSelector();
            $targetSelector = '#' . $this->id();
            $throttle = $dependOn->getThrottle();
            $dependsOnFunctionName = 'dependsOnFunction' . uniqid();
            $js->appendln('
                 let ' . $dependsOnFunctionName . " = () => {
                    $('" . $targetSelector . "').val('');
                    $('" . $targetSelector . "').select2('val', null);
                    $('" . $targetSelector . "').trigger('change');
                 };
                 $('" . $dependsOnSelector . "').change(cresenity.debounce(" . $dependsOnFunctionName . ' ,' . $throttle . '));
            ');
        }

        return $js->text();
    }
}
