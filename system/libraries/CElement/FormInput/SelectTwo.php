<?php

/**
 * Select2-based select input with cres.js auto-initialization.
 *
 * Unlike SelectSearch which generates inline JS, this component
 * stores all config in a cres-config attribute and delegates
 * initialization to cres.js. This makes it work correctly
 * inside dynamic containers like Repeater.
 *
 * @see CElement_FormInput_SelectSearch
 */
class CElement_FormInput_SelectTwo extends CElement_FormInput {
    use CElement_FormInput_SelectSearch_Trait_SelectSearchUtilsTrait;
    use CTrait_Element_Property_ApplyJs;
    use CTrait_Element_Property_DependsOn;
    use CTrait_Element_Property_Placeholder;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var null|string|CFunction_SerializableClosure
     */
    protected $formatSelection;

    /**
     * @var null|string|CFunction_SerializableClosure
     */
    protected $formatResult;

    /**
     * @var string
     */
    protected $keyField;

    /**
     * @var array
     */
    protected $searchField = [];

    /**
     * @var array
     */
    protected $searchFullTextField = [];

    /**
     * @var bool
     */
    protected $multiple;

    /**
     * @var bool
     */
    protected $autoSelect;

    /**
     * @var int
     */
    protected $minInputLength;

    /**
     * @var array
     */
    protected $dropdownClasses;

    /**
     * @var int
     */
    protected $delay;

    /**
     * @var null|callable
     */
    protected $valueCallback;

    /**
     * @var null|CManager_Contract_DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var array
     */
    protected $requires;

    /**
     * @var bool
     */
    protected $allowClear;

    /**
     * @var null|CFunction_SerializableClosure
     */
    protected $queryResolver;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var array
     */
    protected $prependData;

    /**
     * @var int
     */
    protected $perPage;

    /**
     * @var bool
     */
    protected $onModal;

    /**
     * @param null|string $id
     */
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

    /**
     * @param null|string $id
     *
     * @return static
     */
    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }
    /**
     * @param Closure $resolver
     *
     * @return void
     */
    public function setQueryResolver(Closure $resolver) {
        $this->queryResolver = CFunction::serializeClosure($resolver);
    }

    /**
     * @return string
     */
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

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setPrependData(array $data) {
        $this->prependData = $data;

        return $this;
    }

    /**
     * @param array $row
     *
     * @return $this
     */
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

    /**
     * @param string|Closure $fmt
     *
     * @return $this
     */
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

    /**
     * @param string|array $c
     *
     * @return $this
     */
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

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setAllowClear($bool = true) {
        $this->allowClear = $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setOnModal($bool = true) {
        $this->onModal = $bool;

        return $this;
    }

    /**
     * @return string
     */
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

    /**
     * @return null|array|CCollection
     */
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

    /**
     * @param int $indent
     *
     * @return string
     */
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
        $config = $this->buildCresConfig();
        $html->appendln('<select class="' . $classes . '" name="' . $this->name . '" id="' . $this->id . '" ' . $disabled . $custom_css . $multiple . $additionAttribute . ' cres-element="control:SelectTwo" cres-config="' . c::e(json_encode($config)) . '">');

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

    /**
     * @return array
     */
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


    /**
     * @return array
     */
    protected function buildCresConfig() {
        $config = [
            'ajaxUrl' => $this->createAjaxUrl(),
            'language' => $this->language,
            'placeholder' => $this->placeholder,
            'multiple' => $this->multiple,
            'minInputLength' => $this->minInputLength,
            'delay' => $this->delay,
            'perPage' => $this->perPage,
            'allowClear' => $this->allowClear,
            'formatSelection' => $this->formatSelection,
            'formatResult' => $this->formatResult,
            'searchField' => carr::first($this->searchField),
            'selectedData' => $this->buildSelectedData(),
        ];

        if (count($this->dependsOn) > 0) {
            $config['dependsOn'] = $this->buildDependsOnConfig();
        }

        return $config;
    }

    /**
     * @return array
     */
    protected function buildDependsOnConfig() {
        $result = [];
        foreach ($this->dependsOn as $index => $dependOn) {
            $selectors = [];
            foreach (explode(', ', $dependOn->getSelector()->getQuerySelector()) as $sel) {
                $selectors[] = $sel;
            }

            $result[] = [
                'key' => 'dependsOn_' . $index,
                'selectors' => $selectors,
                'throttle' => $dependOn->getThrottle(),
            ];
        }

        return $result;
    }
}
