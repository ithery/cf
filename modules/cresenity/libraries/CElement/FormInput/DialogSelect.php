<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * 
 */
class CElement_FormInput_DialogSelect extends CElement_FormInput {

	use CElement_Trait_Template;

	protected $imgSrc;
	protected $width;
	protected $height;
	protected $buttonLabel;
	
	public function __construct($id) {
		parent::__construct($id);

		$this->type = 'dialogSelect';
		$this->tag = 'div';
		$this->imgSrc = CApp_Base::noImageUrl();
		$this->width = "100";
		$this->height = "100";
		$this->buttonLabel = 'Select an Item';
		$this->templateName = 'CElement/FormInput/DialogSelect';
		$this->onBeforeParse(function() {

		    $ajaxName = $this->name;
		    $ajaxName = str_replace('[', '-', $ajaxName);
		    $ajaxName = str_replace(']', '-', $ajaxName);

		    $ajaxUrl = CAjaxMethod::factory()
	    		->setType('dialogselect')
	            ->setData('inputName', $ajaxName)
	            ->makeurl();

		    $this->setVar('id', $this->id);
		    $this->setVar('imgSrc', $this->imgSrc);
		    $this->setVar('width', $this->width);
		    $this->setVar('height', $this->height);
		    $this->setVar('buttonLabel', $this->buttonLabel);
		    $this->setVar('preTag', $this->pretag());
		    $this->setVar('postTag', $this->posttag());
		    $this->setVar('name', $this->name);
		    $this->setVar('value', $this->value);
		    $this->setVar('ajaxName', $ajaxName);
		    $this->setVar('ajaxUrl', $ajaxUrl);
		});
	}

	public static function factory($id) {
		return new CElement_FormInput_DialogSelect($id);
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