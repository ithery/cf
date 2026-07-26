<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_DialogSelect extends CElement_FormInput {
    use CElement_Trait_Template;

    /**
     * Field(s) whose values are shown/searched, passed through to the ajax
     * lookup endpoint.
     *
     * @var mixed
     */
    protected $fields;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $keyField;

    /**
     * @var array|string
     */
    protected $searchField;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $itemName;

    /**
     * @var string
     */
    protected $imgSrc;

    /**
     * @var int|string
     */
    protected $minWidth;

    /**
     * @var int|string
     */
    protected $minHeight;

    /**
     * @var string
     */
    protected $buttonLabel;

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @var string
     */
    protected $itemTemplateName;

    /**
     * @var array
     */
    protected $itemTemplateVariables;

    /**
     * Debounce delay (in milliseconds) before the search input triggers an
     * ajax lookup.
     *
     * @var int|string
     */
    protected $delay;

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);

        $this->type = 'dialogSelect';
        $this->tag = 'div';
        $this->format = '';
        $this->fields = '';
        $this->keyField = '';
        $this->searchField = '';
        $this->limit = 10;
        $this->title = c::__('Please choose an Item');
        $this->itemName = '';
        $this->imgSrc = CApp_Base::noImageUrl();
        $this->minWidth = '100';
        $this->minHeight = '100';
        $this->buttonLabel = 'Select an Item';
        $this->placeholder = 'Search Item';
        $this->delay = '1000';
        $this->templateName = 'CElement/FormInput/DialogSelect';
        $this->itemTemplateName = 'CElement/Card/Item';
        $this->itemTemplateVariables = ['id', 'name', 'imageUrl'];
        $this->onBeforeParse(function () {
            $this->setVar('id', $this->id);
            $this->setVar('title', $this->title);
            $this->setVar('itemName', $this->itemName);
            $this->setVar('imgSrc', $this->imgSrc);
            $this->setVar('minWidth', $this->minWidth);
            $this->setVar('minHeight', $this->minHeight);
            $this->setVar('buttonLabel', $this->buttonLabel);
            $this->setVar('placeholder', $this->placeholder);
            $this->setVar('delay', $this->delay);
            $this->setVar('preTag', $this->pretag());
            $this->setVar('postTag', $this->posttag());
            $this->setVar('name', $this->name);
            $this->setVar('value', $this->value);
            $this->setVar('ajaxName', $this->createAjaxName());
            $this->setVar('ajaxUrl', $this->createAjaxUrl());
        });
    }

    /**
     * @param null|string $id
     *
     * @return self
     */
    public static function factory($id = null) {
        return new CElement_FormInput_DialogSelect($id);
    }

    /**
     * @param mixed $fields
     *
     * @return $this
     */
    public function setFields($fields) {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format) {
        $this->format = $format;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKeyField($key) {
        $this->keyField = $key;

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setSearchField(array $fields) {
        $this->searchField = $fields;

        return $this;
    }

    /**
     * @param int $total
     *
     * @return $this
     */
    public function setLimit($total) {
        $this->limit = $total;

        return $this;
    }

    /**
     * @param string $title
     * @param bool   $lang
     *
     * @return $this
     */
    public function setTitle($title, $lang = true) {
        if ($lang) {
            $title = c::__($title);
        }
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $itemName
     *
     * @return $this
     */
    public function setItemName($itemName) {
        $this->itemName = $itemName;

        return $this;
    }

    /**
     * @param string $imgsrc
     *
     * @return $this
     */
    public function setImgSrc($imgsrc) {
        $this->imgSrc = $imgsrc;

        return $this;
    }

    /**
     * @param int|string $minWidth
     *
     * @return $this
     */
    public function setMinWidth($minWidth) {
        $this->minWidth = $minWidth;

        return $this;
    }

    /**
     * @param int|string $minHeight
     *
     * @return $this
     */
    public function setMinHeight($minHeight) {
        $this->minHeight = $minHeight;

        return $this;
    }

    /**
     * @param string $label
     * @param bool   $lang
     *
     * @return $this
     */
    public function setButtonLabel($label, $lang = true) {
        if ($lang) {
            $label = c::__($label);
        }
        $this->buttonLabel = $label;

        return $this;
    }

    /**
     * @param string $placeholder
     * @param bool   $lang
     *
     * @return $this
     */
    public function setPlaceholder($placeholder, $lang = true) {
        if ($lang) {
            $placeholder = c::__($placeholder);
        }
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @param string $templateName
     *
     * @return $this
     */
    public function setItemTemplateName($templateName) {
        $this->itemTemplateName = $templateName;

        return $this;
    }

    /**
     * @param array $vars
     *
     * @return $this
     */
    public function setItemTemplateVariables(array $vars) {
        $this->itemTemplateVariables = $vars;

        return $this;
    }

    /**
     * @param int|string $delay
     *
     * @return $this
     */
    public function setDelay($delay) {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return string
     */
    public function createAjaxName() {
        $ajaxName = $this->name;
        $ajaxName = str_replace('[', '-', $this->name);
        $ajaxName = str_replace(']', '-', $ajaxName);

        return $ajaxName;
    }

    /**
     * @return string
     */
    public function createAjaxUrl() {
        return CAjax::createMethod()
            ->setType('DialogSelect')
            ->setData('format', $this->format)
            ->setData('fields', $this->fields)
            ->setData('keyField', $this->keyField)
            ->setData('searchField', $this->searchField)
            ->setData('itemTemplateName', $this->itemTemplateName)
            ->setData('itemTemplateVariables', $this->itemTemplateVariables)
            ->setData('limit', $this->limit)
            ->makeurl();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $templateHtml = $this->getTemplateHtml();
        $html = $templateHtml;

        return $html;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $templateJs = $this->getTemplateJs();
        $js = $templateJs;

        return $js;
    }
}
