<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * 
 */
class CElement_FormInput_DialogSelect extends CElement_FormInput {

	use CElement_Trait_Template;

	protected $fields;
	protected $format;
	protected $keyField;
	protected $searchField;
	protected $limit;
	protected $itemName;
	protected $imgSrc;
	protected $minWidth;
	protected $minHeight;
	protected $buttonLabel;
	protected $placeholder;
	protected $itemTemplateName;
	protected $itemTemplateVariables;
	protected $delay;
	
	public function __construct($id) {
		parent::__construct($id);

		$this->type = 'dialogSelect';
		$this->tag = 'div';
		$this->format = '';
		$this->fields = '';
		$this->keyField = '';
		$this->searchField = '';
		$this->limit = 10;
		$this->itemName = '';
		$this->imgSrc = CApp_Base::noImageUrl();
		$this->minWidth = '100';
		$this->minHeight = '100';
		$this->buttonLabel = 'Select an Item';
		$this->placeholder = 'Search Item';
		$this->delay = '1000';
		$this->templateName = 'CElement/FormInput/DialogSelect';
		$this->itemTemplateName = 'CElement/Card/Item';
		$this->itemTemplateVariables = array('id', 'name', 'imageUrl');
		$this->onBeforeParse(function() {
		    $this->setVar('id', $this->id);
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

	public static function factory($id) {
		return new CElement_FormInput_DialogSelect($id);
	}

	public function setFields($fields) {
		$this->fields = $fields;
		return $this;
	}

	public function setFormat($format) {
		$this->format = $format;
		return $this;
	}

	public function setKeyField($key) {
		$this->keyField = $key;
		return $this;
	}

	public function setSearchField(array $fields) {
		$this->searchField = $fields;
		return $this;
	}

	public function setLimit($total) {
		$this->limit = $total;
		return $this;
	}

	public function setItemName($itemName) {
		$this->itemName = $itemName;
		return $this;
	}

	public function setImgSrc($imgsrc) {
		$this->imgSrc = $imgsrc;
		return $this;
	}

	public function setMinWidth($minWidth) {
	    $this->minWidth = $minWidth;
	    return $this;
	}

	public function setMinHeight($minHeight) {
	    $this->minHeight = $minHeight;
	    return $this;
	}

	public function setButtonLabel($label) {
		$this->buttonLabel = $label;
		return $this;
	}

	public function setPlaceholder($placeholder) {
		$this->placeholder = $placeholder;
		return $this;
	}

	public function setItemTemplateName($templateName) {
		$this->itemTemplateName = $templateName;
		return $this;
	}

	public function setItemTemplateVariables(array $vars) {
		$this->itemTemplateVariables = $vars;
		return $this;
	}

	public function setDelay($delay) {
		$this->delay = $delay;
		return $this;
	}

	public function createAjaxName() {
		$ajaxName = $this->name;
		$ajaxName = str_replace('[', '-', $this->name);
		$ajaxName = str_replace(']', '-', $ajaxName);

		return $ajaxName;
	}

	public function createAjaxUrl() {
		return CAjaxMethod::factory()
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

	public function html($indent = 0) {
	    $templateHtml = $this->getTemplateHtml();
	    $html = $templateHtml;
	    return $html;
	}

	public function js($indent = 0) {
	    $templateJs = $this->getTemplateJs();
	    $js = $templateJs;
	    return $js;
	}

}