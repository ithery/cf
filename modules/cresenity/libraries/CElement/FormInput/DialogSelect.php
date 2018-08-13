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
	protected $imgSrc;
	protected $width;
	protected $height;
	protected $buttonLabel;
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
		$this->imgSrc = CApp_Base::noImageUrl();
		$this->width = '100';
		$this->height = '100';
		$this->buttonLabel = 'Select an Item';
		$this->delay = '1000';
		$this->templateName = 'CElement/FormInput/DialogSelect';
		$this->onBeforeParse(function() {
		    $this->setVar('id', $this->id);
		    $this->setVar('imgSrc', $this->imgSrc);
		    $this->setVar('width', $this->width);
		    $this->setVar('height', $this->height);
		    $this->setVar('buttonLabel', $this->buttonLabel);
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

	public function setButtonLabel($label) {
		$this->buttonLabel = $label;
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