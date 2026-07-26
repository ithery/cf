<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_SelectModal extends CElement_FormInput {
    use CElement_Trait_UseViewTrait;

    /**
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
     * @var array
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
     * @var string
     */
    protected $minWidth;

    /**
     * @var string
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
     * @var mixed
     */
    protected $formatSelection;

    /**
     * @var mixed
     */
    protected $formatResult;

    /**
     * @var string
     */
    protected $delay;

    /**
     * View/template name used to render an item; set via
     * {@see setItemTemplateName()}. Not currently consumed by
     * {@see createAjaxUrl()} or the view.
     *
     * @var string
     */
    protected $itemTemplateName;

    /**
     * Variables passed to the item template; set via
     * {@see setItemTemplateVariables()}. Not currently consumed by
     * {@see createAjaxUrl()} or the view.
     *
     * @var array
     */
    protected $itemTemplateVariables;

    /**
     * @param string $id
     */
    public function __construct($id) {
        parent::__construct($id);

        $this->type = 'selectModal';
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
        $this->view = 'cresenity/element/form-input/select-modal';

        $this->onBeforeParse(function (CView_View $view) {
            $view->with('id', $this->id);
            $view->with('title', $this->title);
            $view->with('itemName', $this->itemName);
            $view->with('imgSrc', $this->imgSrc);
            $view->with('minWidth', $this->minWidth);
            $view->with('minHeight', $this->minHeight);
            $view->with('buttonLabel', $this->buttonLabel);
            $view->with('placeholder', $this->placeholder);
            $view->with('delay', $this->delay);
            $view->with('preTag', $this->pretag());
            $view->with('postTag', $this->posttag());
            $view->with('name', $this->name);
            $view->with('value', $this->value);
            $view->with('ajaxName', $this->createAjaxName());
            $view->with('ajaxUrl', $this->createAjaxUrl());
        });
    }

    /**
     * @param null|string $id
     *
     * @return static
     */
    public static function factory($id = null) {
        return new CElement_FormInput_SelectModal($id);
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
     * @param string $minWidth
     *
     * @return $this
     */
    public function setMinWidth($minWidth) {
        $this->minWidth = $minWidth;

        return $this;
    }

    /**
     * @param string $minHeight
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
     * @param string $delay
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
            ->setType('SelectModal')
            ->setData('format', $this->format)
            ->setData('fields', $this->fields)
            ->setData('keyField', $this->keyField)
            ->setData('searchField', $this->searchField)
            ->setData('limit', $this->limit)
            ->makeurl();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $templateHtml = $this->getViewHtml();
        $html = $templateHtml;

        return $html;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $templateJs = $this->getViewJs();
        $js = $templateJs;

        return $js;
    }
}
