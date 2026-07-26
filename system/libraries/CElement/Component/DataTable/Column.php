<?php

class CElement_Component_DataTable_Column extends CObject {
    use CTrait_Compat_Element_DataTable_Column;
    use CTrait_Element_Property_Label;
    use CTrait_Element_Responsive;
    use CTrait_Element_Transform;

    /**
     * @var string
     */
    public $fieldname;

    /**
     * @var string
     */
    public $width;

    /**
     * @var string
     */
    public $align;

    /**
     * @var string
     */
    public $format;

    /**
     * @var bool
     */
    public $sortable;

    /**
     * @var bool
     */
    public $searchable;

    /**
     * @var bool
     */
    public $editable;

    /**
     * @var bool
     */
    public $visible;

    /**
     * @var string
     */
    public $input_type;

    /**
     * @var bool
     */
    public $noLineBreak;

    /**
     * @var null|callable|CFunction_SerializableClosure
     */
    public $callback;

    /**
     * @var null|array|string
     */
    public $callbackRequire;

    /**
     * @var string[]
     */
    public $class;

    /**
     * @var string
     */
    public $searchType = 'text';

    /**
     * @var array
     */
    public $searchOptions = [];

    /**
     * @var null|string
     */
    protected $exportLabel;

    /**
     * @var null|callable|CFunction_SerializableClosure
     */
    protected $exportCallback;

    /**
     * @var null|array|string
     */
    protected $exportCallbackRequire;

    /**
     * @var null|string
     */
    protected $dataType = null;

    /**
     * @var array
     */
    protected $customCss = [];

    /**
     * @var null|callable|CFunction_SerializableClosure
     */
    protected $searchCallback = null;

    /**
     * @var null|callable|CFunction_SerializableClosure
     */
    protected $sortCallback = null;

    /**
     * @param string $fieldname
     *
     * @return void
     */
    public function __construct($fieldname) {
        parent::__construct();

        $this->fieldname = $fieldname;
        $this->align = 'left';
        $this->label = $fieldname;
        $this->width = '';
        $this->transforms = [];
        $this->format = '';
        $this->sortable = true;
        $this->searchable = true;
        $this->visible = true;
        $this->input_type = 'text';
        $this->editable = true;
        $this->noLineBreak = false;
        $this->hiddenPhone = false;
        $this->hiddenTablet = false;
        $this->hiddenDesktop = false;
        $this->callback = null;
        $this->callbackRequire = null;
        $this->class = [];
        $this->customCss = [];
        $this->searchCallback = null;
        $this->sortCallback = null;
    }

    /**
     * Set custom css style.
     *
     * @param string $key
     * @param string $val
     *
     * @return $this
     */
    public function customCss($key, $val) {
        $this->customCss[$key] = $val;

        return $this;
    }

    /**
     * @return string
     */
    public function getCssStyle() {
        return CRenderable::renderStyle($this->customCss);
    }

    /**
     * @param string $fieldname
     *
     * @return CElement_Component_DataTable_Column
     */
    public static function factory($fieldname) {
        return new CElement_Component_DataTable_Column($fieldname);
    }

    /**
     * @return string
     */
    public function getFieldname() {
        return $this->fieldname;
    }

    /**
     * @return string
     */
    public function getAlign() {
        return $this->align;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setInputType($type) {
        $this->input_type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNoLineBreak() {
        return $this->noLineBreak;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setNoLineBreak($bool = true) {
        return $this->setNoWrap($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setNoWrap($bool = true) {
        $this->noLineBreak = $bool;

        return $this;
    }

    /**
     * @param null|string $dataType
     *
     * @return $this
     */
    public function setDataType($dataType) {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDataType() {
        return $this->dataType;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setVisible($bool = true) {
        $this->visible = $bool;

        return $this;
    }

    /**
     * @return $this
     */
    public function setInvisible() {
        return $this->setVisible(false);
    }

    /**
     * @return bool
     */
    public function isVisible() {
        return $this->visible;
    }

    /**
     * Set sortable of column.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setSortable($bool) {
        $this->sortable = $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setSearchable($bool) {
        $this->searchable = $bool;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setSearchType($type) {
        $this->searchType = $type;

        return $this;
    }

    /**
     * @param array $option
     *
     * @return $this
     */
    public function setSearchOptions($option) {
        $this->searchOptions = $option;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setEditable($bool) {
        $this->editable = $bool;

        return $this;
    }

    /**
     * Set width of column.
     *
     * @param string $w
     *
     * @return $this
     */
    public function setWidth($w) {
        $this->width = $w;

        return $this;
    }

    /**
     * Set align of column (left,right,center).
     *
     * @param string $align
     *
     * @return $this
     */
    public function setAlign($align) {
        $this->align = $align;

        return $this;
    }

    /**
     * Set align to right.
     *
     * @return $this
     */
    public function setAlignRight() {
        return $this->setAlign('right');
    }

    /**
     * Set align to center.
     *
     * @return $this
     */
    public function setAlignCenter() {
        return $this->setAlign('center');
    }

    /**
     * @param callable|Closure $callback
     * @param string           $require
     *
     * @return $this
     */
    public function setCallback($callback, $require = '') {
        //$this->callback = c::toSerializableClosure($callback);
        $this->callback = c::toSerializableClosure($callback);
        $this->callbackRequire = $require;

        return $this;
    }

    /**
     * @param callable|Closure $callback
     *
     * @return $this
     */
    public function setSearchCallback($callback) {
        $this->searchCallback = c::toSerializableClosure($callback);

        return $this;
    }


    /**
     * @param callable|Closure $callback
     *
     * @return $this
     */
    public function setSortCallback($callback) {
        $this->sortCallback = c::toSerializableClosure($callback);

        return $this;
    }

    /**
     * @return null|callable|CFunction_SerializableClosure
     */
    public function getSearchCallback() {
        return $this->searchCallback;
    }

    /**
     * @return null|callable|CFunction_SerializableClosure
     */
    public function getSortCallback() {
        return $this->sortCallback;
    }

    /**
     * @param callable|Closure $callback
     * @param string           $require
     *
     * @return $this
     */
    public function setExportCallback($callback, $require = '') {
        $this->exportCallback = c::toSerializableClosure($callback);
        $this->exportCallbackRequire = $require;

        return $this;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setExportLabel($label) {
        $this->exportLabel = $label;

        return $this;
    }

    /**
     * @param string $s
     *
     * @return $this
     */
    public function setFormat($s) {
        $this->format = $s;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat() {
        return $this->format;
    }

    /**
     * @param bool   $exportPdf
     * @param string $thClass
     * @param int    $indent
     *
     * @return string
     */
    public function renderHeaderHtml($exportPdf, $thClass = '', $indent = 0) {
        $pdfTHeadTdAttr = '';
        if ($exportPdf) {
            $pdfTHeadTdAttr = ' bgcolor="#9f9f9f" color="#000"  ';
        }
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $addition_attr = '';
        if (strlen($this->width) > 0) {
            $addition_attr .= ' width="' . $this->width . '"';
        }
        $class = $this->getClassAttribute();
        $dataClass = $class;
        $dataAlign = '';
        switch ($this->getAlign()) {
            case 'left':
                $dataAlign .= 'align-left';

                break;
            case 'right':
                $dataAlign .= 'align-right';

                break;
            case 'center':
                $dataAlign .= 'align-center';

                break;
        }
        $dataNoLineBreak = '';
        if ($this->getNoLineBreak()) {
            $dataNoLineBreak = 'no-line-break';
        }
        if ($exportPdf) {
            switch ($this->getAlign()) {
                case 'left':
                    $pdfTHeadTdAttr .= ' align="left"';

                    break;
                case 'right':
                    $pdfTHeadTdAttr .= ' align="right"';

                    break;
                case 'center':
                    $pdfTHeadTdAttr .= ' align="center"';

                    break;
            }
        }

        if ($this->sortable) {
            $class .= ' sortable';
        }
        if ($this->hiddenPhone) {
            $class .= ' hidden-phone';
        }
        if ($this->hiddenTablet) {
            $class .= ' hidden-tablet';
        }
        if ($this->hiddenDesktop) {
            $class .= ' hidden-desktop';
        }
        if ($exportPdf) {
            $html->appendln('<th ' . $pdfTHeadTdAttr . ' field_name = "' . $this->fieldname . '" align="center" class="thead ' . $thClass . $class . '" scope="col"' . $addition_attr . '>' . $this->label . '</th>');
        } else {
            $html->appendln('<th ' . $pdfTHeadTdAttr . ' field_name = "' . $this->fieldname . '" data-no-line-break="' . $dataNoLineBreak . '" data-align="' . $dataAlign . '" data-class="' . $dataClass . '" class="thead ' . $thClass . $class . '" scope="col"' . $addition_attr . '>' . $this->label . '</th>');
        }

        return $html->text();
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function addClass($class) {
        $this->class[] = $class;

        return $this;
    }

    /**
     * @return null|callable|CFunction_SerializableClosure
     */
    public function determineExportCallback() {
        return $this->exportCallback ?: $this->callback;
    }

    /**
     * @return null|array|string
     */
    public function determineExportCallbackRequire() {
        return $this->exportCallbackRequire ?: $this->callbackRequire;
    }

    /**
     * @return string
     */
    public function determineExportLabel() {
        return $this->exportLabel ?: $this->label;
    }

    /**
     * @return string
     */
    public function getClassAttribute() {
        return implode(' ', $this->class);
    }
}
