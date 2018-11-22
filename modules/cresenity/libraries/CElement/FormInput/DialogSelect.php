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
	protected $title;
	protected $itemName;
	protected $imgSrc;
	protected $width;
	protected $height;
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
		$this->title = clang::__('Please choose an Item');
		$this->itemName = '';
		$this->imgSrc = CApp_Base::noImageUrl();
		$this->width = '100';
		$this->height = '100';
		$this->buttonLabel = clang::__('Select an Item');
		$this->placeholder = clang::__('Search Item');
		$this->delay = '1000';
		$this->templateName = 'CElement/FormInput/DialogSelect';
		$this->itemTemplateName = 'CElement/Card/Item';
		$this->itemTemplateVariables = array('id', 'name', 'imageUrl');
		$this->onBeforeParse(function() {
		    $this->setVar('id', $this->id);
		    $this->setVar('title', $this->title);
		    $this->setVar('itemName', $this->itemName);
		    $this->setVar('imgSrc', $this->imgSrc);
		    $this->setVar('width', $this->width);
		    $this->setVar('height', $this->height);
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

	public function setTitle($title, $lang = true) {
		if ($lang) {
			$title = clang::__($title);
		}
		$this->title = $title;
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

	public function setMaxWidth($maxwidth) {
	    $this->maxWidth = $maxwidth;
	    return $this;
	}

	public function setMaxHeight($maxheight) {
	    $this->maxHeight = $maxheight;
	    return $this;
	}

	public function setButtonLabel($label, $lang = true) {
		if ($lang) {
			$label = clang::__($lang);
		}
		$this->buttonLabel = $label;
		return $this;
	}

	public function setPlaceholder($placeholder, $lang = true) {
		if ($lang) {
			$placeholder = clang::__($placeholder);
		}
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