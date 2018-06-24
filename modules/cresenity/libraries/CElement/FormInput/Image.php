<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 6:55:42 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_Image extends CElement_FormInput {

    use CElement_Trait_Template,
        CTrait_Compat_Element_FormInput_Image;

    protected $imgSrc;
    protected $maxWidth;
    protected $maxHeight;
    protected $disabledUpload;
   

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "image";
        $this->tag = "div";
        $this->imgSrc = CApp_Base::noImageUrl();
        $this->maxWidth = "200";
        $this->maxHeight = "150";
        $this->disabledUpload = false;
        $this->templateName = 'CElement/FormInput/Image';

        $this->onBeforeParse(function() {

            $this->setVar('id', $this->id);
            $this->setVar('imgSrc', $this->imgSrc);
            $this->setVar('maxWidth', $this->maxWidth);
            $this->setVar('maxHeight', $this->maxHeight);
            $this->setVar('disabledUpload', $this->disabledUpload);
            $this->setVar('preTag', $this->pretag());
            $this->setVar('postTag', $this->posttag());
            $this->setVar('name', $this->name);
            $this->setVar('value', $this->value);
        });
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

    public function setDisabledUpload($bool) {
        $this->disabledUpload = $bool;
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
