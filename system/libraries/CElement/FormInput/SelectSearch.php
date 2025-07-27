<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 12:00:39 AM
 */
class CElement_FormInput_SelectSearch extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_SelectSearch;
    use CElement_FormInput_SelectSearch_Trait_Select2v23Trait;
    use CElement_FormInput_SelectSearch_Trait_SelectSearchUtilsTrait;
    use CTrait_Element_Property_ApplyJs;
    use CTrait_Element_Property_DependsOn;
    use CTrait_Element_Property_Placeholder;

    protected $query;

    protected $formatSelection;

    protected $formatResult;

    protected $keyField;

    /**
     * @var array
     */
    protected $searchField = [];

    /**
     * @var array
     */
    protected $searchFullTextField = [];

    protected $multiple;

    protected $autoSelect;

    protected $minInputLength;

    protected $dropdownClasses;

    protected $delay;

    protected $valueCallback;

    protected $dataProvider;

    protected $requires;

    protected $allowClear;

    protected $queryResolver;

    protected $language;

    protected $prependData;

    protected $perPage;

    protected $onModal;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->dropdownClasses = [];
        $this->type = 'selectsearch';
        $this->query = '';
        $this->formatSelection = null;
        $this->formatResult = null;
        $this->keyField = '';
        $this->searchField = [];
        $this->placeholder = c::__('element/selectsearch.placeholder');
        $this->multiple = false;
        $this->autoSelect = false;
        $this->minInputLength = 0;
        $this->delay = 100;
        $this->requires = [];
        $this->valueCallback = null;
        $this->applyJs = c::theme('selectsearch.applyJs', 'select2');
        $this->perPage = 10;
        $this->value = null;
        $this->allowClear = false;
        $this->prependData = [];
        $this->onModal = false;
        $language = CF::getLocale();
        if (strlen($language) > 2) {
            $language = strtolower(substr($language, 0, 2));
        }
        $this->language = $language;
    }

    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    public function setQueryResolver(Closure $resolver) {
        $this->queryResolver = CFunction::serializeClosure($resolver);
    }

    public function query() {
        if ($this->queryResolver != null) {
            return $this->queryResolver->__invoke($this->query);
        }

        return $this->query;
    }

    /**
     * @param callable $callback
     * @param string   $require
     *
     * @return $this
     */
    public function setValueCallback(callable $callback, $require = '') {
        $this->valueCallback = $callback;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setMultiple($bool = true) {
        $this->multiple = $bool;

        return $this;
    }

    /**
     * Set delay in miliseconds, default is 100.
     *
     * @param int $val
     *
     * @return $this
     */
    public function setDelay($val) {
        $this->delay = $val;

        return $this;
    }

    /**
     * Set per page for ajax, default is 10.
     *
     * @param mixed $perPage
     *
     * @return $this
     */
    public function setPerPage($perPage) {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setAutoSelect($bool = true) {
        $this->autoSelect = $bool;

        return $this;
    }

    /**
     * @param int $minInputLength
     *
     * @return $this
     */
    public function setMinInputLength($minInputLength) {
        $this->minInputLength = $minInputLength;

        return $this;
    }

    /**
     * @param string $keyField
     *
     * @return $this
     */
    public function setKeyField($keyField) {
        $this->keyField = $keyField;

        return $this;
    }

    /**
     * @param string|array $searchField
     *
     * @return $this
     */
    public function setSearchField($searchField) {
        $searchField = carr::wrap($searchField);
        $this->searchField = $searchField;

        if ($this->formatSelection == null) {
            $this->formatSelection = '{' . carr::first($searchField) . '}';
        }
        if ($this->formatResult == null) {
            $this->formatResult = '{' . carr::first($searchField) . '}';
        }

        return $this;
    }

    /**
     * @param string|array $searchField
     *
     * @return $this
     */
    public function setSearchFullTextField($searchField) {
        $searchField = carr::wrap($searchField);
        $this->searchFullTextField = $searchField;

        if ($this->formatSelection == null) {
            $this->formatSelection = '{' . carr::first($searchField) . '}';
        }
        if ($this->formatResult == null) {
            $this->formatResult = '{' . carr::first($searchField) . '}';
        }

        return $this;
    }

    public function setPrependData(array $data) {
        $this->prependData = $data;

        return $this;
    }

    public function prependRow(array $row) {
        $this->prependData[] = $row;

        return $this;
    }

    /**
     * @param string $query
     *
     * @return $this
     */
    public function setQuery($query) {
        $this->query = $query;

        return $this;
    }

    public function setFormat($fmt) {
        $this->setFormatResult($fmt);
        $this->setFormatSelection($fmt);

        return $this;
    }

    /**
     * @param string|Closure $fmt
     *
     * @return $this
     */
    public function setFormatResult($fmt) {
        if ($fmt instanceof Closure) {
            $fmt = CFunction::serializeClosure($fmt);
        }
        $this->formatResult = $fmt;

        return $this;
    }

    /**
     * @param string|Closure $fmt
     *
     * @return $this
     */
    public function setFormatSelection($fmt) {
        if ($fmt instanceof Closure) {
            $fmt = CFunction::serializeClosure($fmt);
        }
        $this->formatSelection = $fmt;

        return $this;
    }

    public function addDropdownClass($c) {
        if (is_array($c)) {
            $this->dropdownClasses = array_merge($c, $this->dropdownClasses);
        } else {
            $this->dropdownClasses[] = $c;
        }

        return $this;
    }

    /**
     * @param CModel|CModel_Query|string $model
     * @param null|mixed                 $queryCallback
     *
     * @return $this
     */
    public function setDataFromModel($model, $queryCallback = null) {
        $this->dataProvider = CManager::createModelDataProvider($model, $queryCallback);

        return $this;
    }

    /**
     * @param Closure $closure
     *
     * @return $this
     */
    public function setDataFromClosure($closure) {
        $this->dataProvider = CManager::createClosureDataProvider($closure);

        return $this;
    }

    /**
     * @param CCollection $collection
     *
     * @return $this
     */
    public function setDataFromCollection($collection) {
        $this->dataProvider = CManager::createCollectionDataProvider($collection);

        return $this;
    }

    public function setAllowClear($bool = true) {
        $this->allowClear = $bool;

        return $this;
    }

    public function setOnModal($bool = true) {
        $this->onModal = $bool;

        return $this;
    }

    public function createAjaxUrl() {
        $ajaxMethod = CAjax::createMethod();
        $ajaxMethod->setType(CAjax::TYPE_SELECT_SEARCH);
        $ajaxMethod->setData('query', $this->query);
        $ajaxMethod->setData('dataProvider', serialize($this->dataProvider));
        $ajaxMethod->setData('keyField', $this->keyField);
        $ajaxMethod->setData('searchField', $this->searchField);
        $ajaxMethod->setData('searchFullTextField', $this->searchFullTextField);
        $ajaxMethod->setData('valueCallback', $this->valueCallback);
        $ajaxMethod->setData('formatSelection', serialize($this->formatSelection));
        $ajaxMethod->setData('formatResult', serialize($this->formatResult));
        $ajaxMethod->setData('dependsOn', serialize($this->dependsOn));
        $ajaxMethod->setData('prependData', serialize($this->prependData));

        if (c::app()->isAuthEnabled()) {
            $ajaxMethod->enableAuth();
        }

        $ajaxUrl = $ajaxMethod->makeUrl();

        return $ajaxUrl;
    }

    private function generateSelect2Template($template) {
        //escape the character
        $template = str_replace("'", "\'", $template);
        preg_match_all("/{([\w]*)}/", $template, $matches, PREG_SET_ORDER);

        foreach ($matches as $val) {
            $str = carr::get($val, 1); //matches str without bracket {}
            $bracketStr = carr::get($val, 0); //matches str with bracket {}
            if (strlen($str) > 0) {
                $template = str_replace($bracketStr, "'+item." . $str . "+'", $template);
            }
        }

        return $template;
    }

    protected function getSelectedRow() {
        if ($this->autoSelect || $this->value != null) {
            $value = null;
            if ($this->autoSelect && $this->value === null) {
                $value = [null];
            }
            if ($this->value !== null) {
                $value = $this->value;
            }
            if ($value instanceof CCollection) {
                $value = $value->toArray();
            }
            $values = carr::wrap($value);

            $result = c::collect($values)->map(function ($value) {
                $db = c::db();
                if (count($this->prependData) > 0) {
                    $resultFromPrepend = c::collect($this->prependData)->where($this->keyField, '=', $value)->first();
                    if ($resultFromPrepend != null) {
                        return $resultFromPrepend;
                    }
                }
                if ($this->dataProvider instanceof CManager_DataProvider_ModelDataProvider) {
                    $query = clone $this->dataProvider;

                    if ($value !== null) {
                        // new, get query from setDataFromModel
                        $query = $query->getModelQuery();
                        $query->where($this->keyField, '=', $value);

                        // old
                        // $query->queryCallback(function ($q) use ($value) {
                        //     $q->where($this->keyField, '=', $value);
                        // });
                    }
                    $model = $query->first();

                    return $model;
                }
                $q = 'select * from (' . $this->query() . ') as a limit 1';

                if ($value !== null) {
                    $q = 'select * from (' . $this->query() . ') as a where `' . $this->keyField . '`=' . $db->escape($value);
                }

                $result = $db->query($q)->resultArray(false);

                if (count($result) > 0) {
                    return carr::first($result);
                }

                return null;
            });

            if (!($this->dataProvider instanceof CManager_DataProvider_ModelDataProvider)) {
                $result = $result->toArray();
            }

            return $result;
        }

        return null;
    }

    public function html($indent = 0) {
        //call parent to trigger build

        parent::html($indent);

        if ($this->applyJs == 'select2v2.3') {
            return $this->htmlSelect2v23($indent);
        }
        $html = new CStringBuilder();
        $custom_css = $this->custom_css;

        $custom_css = $this->renderStyle($custom_css);
        $disabled = '';
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }
        $multiple = '';
        if ($this->multiple) {
            $multiple = ' multiple="multiple"';
        }
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }

        $classes = $this->classes;
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }

        $classes = $classes . ' form-control ';

        $html->setIndent($indent);

        $additionAttribute = '';
        foreach ($this->attr as $k => $v) {
            if ($k !== 'value') {
                $additionAttribute .= ' ' . $k . '="' . $v . '"';
            }
        }
        $selectedRows = $this->getSelectedRow();
        $html->appendln('<select class="' . $classes . '" name="' . $this->name . '" id="' . $this->id . '" ' . $disabled . $custom_css . $multiple . $additionAttribute . '">');

        if ($selectedRows) {
            foreach ($selectedRows as $index => $selectedRow) {
                if ($selectedRow != null) {
                    $row = $selectedRow;
                    $model = null;
                    if ($row instanceof CModel) {
                        $model = $row;
                        $row = $this->modelToSelect2Array($model);
                    }
                    if (isset($this->valueCallback) && is_callable($this->valueCallback)) {
                        foreach ($row as $k => $v) {
                            $row[$k] = ($this->valueCallback)($row, $k, $v);
                        }
                    }

                    $strSelection = $this->formatSelection;

                    if ($strSelection == null) {
                        $strSelection = '{' . carr::first($this->searchField) . '}';
                    }

                    if ($strSelection instanceof CFunction_SerializableClosure) {
                        $strSelection = $strSelection->__invoke($model ?: $row);
                    }
                    if ($strSelection instanceof CRenderable) {
                        $strSelection = $strSelection->html();
                    } else {
                        $strSelection = c::value($strSelection);
                        $strSelection = str_replace("'", "\'", $strSelection);
                        preg_match_all("/{([\w]*)}/", $strSelection, $matches, PREG_SET_ORDER);

                        foreach ($matches as $val) {
                            $str = $val[1]; //matches str without bracket {}
                            $bStr = $val[0]; //matches str with bracket {}

                            $strSelection = str_replace($bStr, carr::get($row, $str), $strSelection);
                        }
                    }

                    $selectedValue = carr::get($row, $this->keyField, carr::get($row, 'id'));
                    //$valueTemp = is_array($this->value) ? $this->value[$index] : $this->value;

                    $html->appendln('<option data-multiple="' . ($this->multiple ? '1' : '0') . '" value="' . $selectedValue . '" data-content="' . c::e($strSelection) . '" selected="selected" >' . $strSelection . '</option>');
                }
            }
        }

        $html->appendln('</select>');
        $html->br();

        return $html->text();
    }

    protected function buildSelectedData() {
        $selectedData = [];

        $selectedRows = $this->getSelectedRow();

        if ($selectedRows) {
            foreach ($selectedRows as $index => $selectedRow) {
                if ($selectedRow != null) {
                    $row = $selectedRow;
                    $model = null;

                    if ($row instanceof CModel) {
                        $model = $row;
                        $row = $this->modelToSelect2Array($model);
                    }

                    if (is_object($row)) {
                        $row = (array) $row;
                    }
                    if (isset($this->valueCallback) && is_callable($this->valueCallback)) {
                        foreach ($row as $k => $v) {
                            $row[$k] = ($this->valueCallback)($row, $k, $v);
                        }
                    }
                    $row = $this->addCAppFormatToData($this->formatResult, $row, $model ?: $row, 'result');
                    $row = $this->addCAppFormatToData($this->formatSelection, $row, $model ?: $row, 'selection');
                    $selectedData[] = $row;
                }
            }
        }

        return $selectedData;
    }

    public function js($indent = 0) {
        if ($this->applyJs == 'select2v2.3') {
            return $this->jsSelect2v23($indent);
        }

        $ajaxUrl = $this->createAjaxUrl();
        $strSelection = $this->formatSelection;
        $strResult = $this->formatResult;

        if ($strSelection instanceof CFunction_SerializableClosure) {
            $strSelection = '';
        }

        if ($strResult instanceof CFunction_SerializableClosure) {
            $strResult = '';
        }

        //dont generate here when closure
        $strSelection = $this->generateSelect2Template($strSelection);

        if (strlen($strSelection) == 0) {
            $searchFieldText = c::value(carr::first($this->searchField));
            if (strlen($searchFieldText) > 0) {
                $strSelection = "'+item." . $searchFieldText . "+'";
            }
        }

        if (is_string($strResult)) {
            $strResult = $this->generateSelect2Template($strResult);

            if (strlen($strResult) == 0) {
                $searchFieldText = c::value(carr::first($this->searchField));
                if (strlen($searchFieldText) > 0) {
                    $strResult = "'+item." . $searchFieldText . "+'";
                }
            }
            $strResult = preg_replace("/[\r\n]+/", '', $strResult);
        }

        $placeholder = $this->placeholder;
        $strJsChange = '';
        if ($this->submit_onchange) {
            $strJsChange = "$(this).closest('form').submit();";
        }

        $strJsInit = '';
        $value = null;
        if ($this->value !== null) {
            $value = $this->value;
        }
        $selectedData = $this->buildSelectedData();

        if ($selectedData && is_array($selectedData) && count($selectedData) > 0) {
            if (!$this->multiple) {
                $selectedData = carr::first($selectedData);
            }
            $rjson = json_encode($selectedData);
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
        $additionalRequestDataJs = '';
        foreach ($this->dependsOn as $index => $dependOn) {
            $dependsOnSelector = $dependOn->getSelector()->getQuerySelector();
            $variableUniqueKey = 'dependsOn_' . $index;
            $valueScript = $dependOn->getSelector()->getScriptForValue();
            $additionalRequestDataJs .= "
                result['" . $variableUniqueKey . "']= " . $valueScript . ';
            ';
        }

        $strJsOnModal = '';
        if ($this->onModal) {
            $strJsOnModal = 'dropdownParent: $("#' . $this->id . '").closest(".modal"),';
        }

        $str = "

            $('#" . $this->id . "').select2({
                width: '100%',
                language: '" . $this->language . "',
                placeholder: '" . $placeholder . "',
                allowClear: " . ($this->allowClear ? 'true' : 'false') . ",
                minimumInputLength: '" . $this->minInputLength . "',
                " . $strJsOnModal . "
                ajax: {
                    url: '" . $ajaxUrl . "',
                    dataType: 'jsonp',
                    quietMillis: " . $this->delay . ',
                    delay: ' . $this->delay . ',
                    ' . $strMultiple . '
                    data: function (params) {
                        let result = {
                            q: params.term, // search term
                            page: params.page,
                            limit: ' . $this->perPage . '
                        };
                        ' . $additionalRequestDataJs . '
                        return result;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        var more = (params.page * ' . $this->perPage . ') < data.total;
                        return {
                            results: data.data,
                            pagination: {
                                more: more
                            }
                        };
                    },
                    cache:true,
                    error: function (jqXHR, status, error) {
                        if(cresenity && cresenity.handleAjaxError) {
                            cresenity.handleAjaxError(jqXHR, status, error);
                        }
                    }
                },
                ' . $strJsInit . "
                templateResult: function(item) {

                    if (typeof item.loading !== 'undefined') {
                        return item.text;
                    }
                    if(item.cappFormatResult) {
                        if(item.cappFormatResultIsHtml) {
                            return $('<div>' + item.cappFormatResult +'</div>');
                        } else {
                            return item.cappFormatResult;
                        }
                    }

                    return $('<div>" . $strResult . "</div>');
                },
                templateSelection: function(item) {
                    if(item.element) {
                        let dataMultiple = $(item.element).attr('data-multiple');
                        if(dataMultiple == '0') {
                            let dataContent = $(item.element).attr('data-content');

                            if(dataContent) {
                                if(/<\/?[a-z][\s\S]*>/i.test(dataContent)) {
                                    return $(dataContent);
                                }
                                return dataContent;
                            }
                        } else {
                            let dataContent = $(item.element).attr('data-content');

                            if(dataContent) {
                                if(/<\/?[a-z][\s\S]*>/i.test(dataContent)) {
                                    return $(dataContent);
                                }
                                return dataContent;
                            }
                            if(item.text){
                                return item.text;
                            }
                        }
                    }
                    if(item.cappFormatSelection) {
                        if(item.cappFormatSelectionIsHtml) {
                            return $('<div>' + item.cappFormatSelection +'</div>');
                        } else {
                            return item.cappFormatSelection;
                        }
                    }
                    if (item.id === '' && item.text) {

                        return item.text;
                    }

                    let htmlResult = '" . $strSelection . "';
                    if(htmlResult==='undefined') {
                        return item.text;
                    }


                    return $('<div>" . $strSelection . "</div>');

                },
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
        if ($this->multiple) {
            // if ($selectedData && is_array($selectedData) && count($selectedData) > 0) {
            //     $value = c::json($selectedData);
            //     $str .= "
            //         $('#" . $this->id . "').select2('val'," . $value . ');
            //     ';
            // }
        }
        if (($this->valueCallback != null && is_callable($this->valueCallback))) {
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
            $dependsOnSelector = $dependOn->getSelector()->getQuerySelector();
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

        if ($this->readonly) {
            $js->appendln("
                $('#" . $this->id . "').select2({
                    disabled: true
                });
            ");
        }

        return $js->text();
    }

    public function buildJavascriptOptions() {
        $options = [];
        $ajaxUrl = $this->createAjaxUrl();
        $strSelection = $this->formatSelection;
        $strResult = $this->formatResult;
        $options['ajaxUrl'] = $ajaxUrl;
        $options['language'] = $this->language;
        $options['placeholder'] = $this->placeholder;
        $options['multiple'] = $this->multiple;
        $options['autoSelect'] = $this->autoSelect;
        $options['minInputLength'] = $this->minInputLength;
        $options['delay'] = $this->delay;
        $options['requires'] = $this->requires;
        $options['valueCallback'] = $this->valueCallback;
        $options['applyJs'] = $this->applyJs;
        $options['perPage'] = (int) $this->perPage;
        $options['value'] = $this->value;
        $options['allowClear'] = $this->allowClear;
        $options['onModal'] = $this->onModal;
        $options['strSelection'] = $strSelection;
        $options['strResult'] = $strResult;
        $options['prependData'] = $this->prependData;
        $options['readonly'] = $this->readonly;
        $options['value'] = $this->value;

        $dependsOn = [];
        foreach ($this->dependsOn as $index => $dependOn) {
            $dependsOnSelector = $dependOn->getSelector()->getQuerySelector();
            $targetSelector = '#' . $this->id();
            $throttle = $dependOn->getThrottle();
            $dependOn = [];
            $dependOn['targetSelector'] = $targetSelector;
            $dependOn['dependsOnSelector'] = $dependsOnSelector;
            $dependOn['throttle'] = $throttle;

            $dependsOn[] = $dependOn;
        }

        $options['dependsOn'] = $dependsOn;
        $options['selectedData'] = $this->buildSelectedData();

        return $options;
    }
}
