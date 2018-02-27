<?php

/**
 * Description of ImageAjax
 *
 * @author Hery
 */
class CElement_FormInput_ImageAjax extends CElement_FormInput {

    use CElement_Trait_Template;

    protected $imgSrc;
    protected $maxWidth;
    protected $maxHeight;
    protected $disabledUpload;
    protected $cropper;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "image";
        $this->tag = "div";
        $this->imgSrc = "";
        $this->maxWidth = "200";
        $this->maxHeight = "150";
        $this->disabledUpload = false;
        $this->templateName = 'CElement/FormInput/ImageAjax';
        $this->onBeforeParse(function() {

            $ajaxName = $this->name;
            $ajaxName = str_replace('[', '-', $ajaxName);
            $ajaxName = str_replace(']', '-', $ajaxName);

            $ajaxUrl = CAjaxMethod::factory()->set_type('imgupload')
                    ->set_data('input_name', $ajaxName)
                    ->makeurl();
            $this->setVar('id', $this->id);
            $this->setVar('imgSrc', $this->imgSrc);
            $this->setVar('maxWidth', $this->maxWidth);
            $this->setVar('maxHeight', $this->maxHeight);
            $this->setVar('disabledUpload', $this->disabledUpload);
            $this->setVar('preTag', $this->pretag());
            $this->setVar('postTag', $this->posttag());
            $this->setVar('name', $this->name);
            $this->setVar('value', $this->value);
            $this->setVar('ajaxName', $ajaxName);
            $this->setVar('ajaxUrl', $ajaxUrl);
            $this->setVar('cropper', $this->cropper);
        });
    }

    public function set_imgsrc($imgsrc) {
        return $this->setImgSrc($imgsrc);
    }

    public function setImgSrc($imgsrc) {
        $this->imgSrc = $imgsrc;
        return $this;
    }

    public function set_maxwidth($maxwidth) {
        return $this->setMaxWidth($maxwidth);
    }

    public function setMaxWidth($maxwidth) {
        $this->maxWidth = $maxwidth;
        return $this;
    }

    public function set_maxheight($maxheight) {
        return $this->setMaxHeight($maxheight);
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
        if($this->cropper!=null) {
            $html.=$this->cropper->html();
        }
        return $html;
    }

    public function js($indent = 0) {
        $templateJs = $this->getTemplateJs();
        $js = $templateJs;
        if($this->cropper!=null) {
            $js.=$this->cropper->js();
        }
        return $js;
    }

    /**
     * 
     * @return CElement_Helper_Cropper
     */
    public function cropper() {
        if ($this->cropper == null) {
            $this->cropper = new CElement_Helper_Cropper();
            $this->cropper->setOwner($this);
        }
        return $this->cropper;
    }

}
