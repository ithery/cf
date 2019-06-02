<?php

use SuperClosure\SerializableClosure;

class CElement_Component_DataTable_Column extends CObject {

    use CTrait_Compat_Element_DataTable_Column,
        CTrait_Element_Property_Label,
        CTrait_Element_Responsive;

    public $transforms = array();
    public $fieldname;
    public $width;
    public $align;
    public $format;
    public $sortable;
    public $searchable;
    public $editable;
    public $visible;
    public $input_type;
    public $noLineBreak;
    public $callback;
    public $callbackRequire;

    public function __construct($fieldname) {
        parent::__construct();

        $this->fieldname = $fieldname;
        $this->align = "left";
        $this->label = "";
        $this->width = "";
        $this->transforms = array();
        $this->format = "";
        $this->sortable = true;
        $this->searchable = true;
        $this->visible = true;
        $this->input_type = "text";
        $this->editable = true;
        $this->noLineBreak = false;
        $this->hiddenPhone = false;
        $this->hiddenTablet = false;
        $this->hiddenDesktop = false;
        $this->callback = null;
        $this->callbackRequire = null;
    }

    public static function factory($fieldname) {
        return new CElement_Component_DataTable_Column($fieldname);
    }

    public function getFieldname() {
        return $this->fieldname;
    }

    public function getAlign() {
        return $this->align;
    }

    public function setInputType($type) {
        $this->input_type = $type;
        return $this;
    }

    public function getNoLineBreak() {
        return $this->noLineBreak;
    }

    public function setNoLineBreak($bool) {
        $this->noLineBreak = $bool;
        return $this;
    }

    public function setVisible($bool) {
        $this->visible = $bool;
        return $this;
    }

    public function setSortable($bool) {
        $this->sortable = $bool;
        return $this;
    }

    public function setSearchable($bool) {
        $this->searchable = $bool;
        return $this;
    }

    public function setEditable($bool) {
        $this->editable = $bool;
        return $this;
    }

    public function setWidth($w) {
        $this->width = $w;
        return $this;
    }

    public function setAlign($al) {
        $this->align = $al;
        return $this;
    }

    public function setCallback($callback, $require = '') {

        $this->callback = CHelper::closure()->serializeClosure($callback);
        $this->callbackRequire = $this->callbackRequire;
        return $this;
    }

    public function addTransform($name, $args = array()) {
        $func = CFunction::factory($name);
        if (!is_array($args)) {
            $args = array($args);
        }
        foreach ($args as $arg) {
            $func->addArg($arg);
        }


        $this->transforms[] = $func;
        return $this;
    }

    public function setFormat($s) {
        $this->format = $s;
        return $this;
    }

    public function getFormat() {
        return $this->format;
    }

    public function renderHeaderHtml($exportPdf, $thClass = "", $indent = 0) {

        $pdfTHeadTdAttr = '';
        if ($exportPdf) {


            $pdfTHeadTdAttr = ' bgcolor="#9f9f9f" color="#000"  ';
        }
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $addition_attr = "";
        if (strlen($this->width) > 0) {
            $addition_attr .= ' width="' . $this->width . '"';
        }
        $class = "";
        $data_align = "";
        switch ($this->getAlign()) {
            case "left": $data_align .= "align-left";
                break;
            case "right": $data_align .= "align-right";
                break;
            case "center": $data_align .= "align-center";
                break;
        }
        $dataNoLineBreak = "";
        if ($this->getNoLineBreak()) {
            $dataNoLineBreak = "no-line-break";
        }
        if ($exportPdf) {
            switch ($this->getAlign()) {
                case "left": $pdfTHeadTdAttr .= ' align="left"';
                    break;
                case "right": $pdfTHeadTdAttr .= ' align="right"';
                    break;
                case "center": $pdfTHeadTdAttr .= ' align="center"';
                    break;
            }
        }
        if ($this->sortable) {
            $class .= " sortable";
        }
        if ($this->hiddenPhone) {
            $class .= " hidden-phone";
        }
        if ($this->hiddenTablet) {
            $class .= " hidden-tablet";
        }
        if ($this->hiddenDesktop) {
            $class .= " hidden-desktop";
        }
        if ($exportPdf) {
            $html->appendln('<th ' . $pdfTHeadTdAttr . ' field_name = "' . $this->fieldname . '" align="center" class="thead ' . $thClass . $class . '" scope="col"' . $addition_attr . '>' . $this->label . '</th>');
        } else {
            $html->appendln('<th ' . $pdfTHeadTdAttr . ' field_name = "' . $this->fieldname . '" data-no-line-break="' . $dataNoLineBreak . '" data-align="' . $data_align . '" class="thead ' . $thClass . $class . '" scope="col"' . $addition_attr . '>' . $this->label . '</th>');
        }
        return $html->text();
    }

}
