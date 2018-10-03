<?php

/**
 * Description of ImageAjax
 *
 * @author Hery
 */
class CElement_FormInput_MultipleImageAjax extends CElement_FormInput {

    use CElement_Trait_Template;

    protected $imgSrc;
    protected $maxWidth;
    protected $maxHeight;
    protected $maxUploadSize;
    protected $disabledUpload;
    protected $cropper;
    protected $files;
    protected $link;
    protected $removeLink;
    protected $customControl;
    protected $customControlValue;
    protected $maximum;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "image";
        $this->tag = "div";
        $this->imgSrc = "";
        $this->maxWidth = "200";
        $this->maxHeight = "150";
        $this->maxUploadSize = 0;
        $this->disabledUpload = false;
        $this->templateName = 'CElement/FormInput/MultipleImageAjax';
        $this->removeLink = true;
        $this->files = array();
        $this->maximum = null;
        $this->customControl = array();
        $this->customControlValue = array();
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
            $this->setVar('maxUploadSize', $this->maxUploadSize);
            $this->setVar('disabledUpload', $this->disabledUpload);
            $this->setVar('preTag', $this->pretag());
            $this->setVar('postTag', $this->posttag());
            $this->setVar('name', $this->name);
            $this->setVar('value', $this->value);
            $this->setVar('ajaxName', $ajaxName);
            $this->setVar('ajaxUrl', $ajaxUrl);
            $this->setVar('files', $this->files);
            $this->setVar('maximum', $this->maximum==null?0:$this->maximum);
            $this->setVar('removeLink', $this->removeLink);
            $this->setVar('customControl', $this->customControl);
            $this->setVar('customControlValue', $this->customControlValue);
            $this->setVar('cropper', $this->cropper);
        });
    }

    public function html($indent = 0) {
        $templateHtml = $this->getTemplateHtml();
        $html = $templateHtml;
        if ($this->cropper != null) {
            $html .= $this->cropper->html();
        }
        return $html;
    }

    public function js($indent = 0) {
        $templateJs = $this->getTemplateJs();
        $js = $templateJs;
        if ($this->cropper != null) {
            $js .= $this->cropper->js();
        }
        return $js;
    }

    public function setMaxUploadSize($size) {
        $this->maxUploadSize = $size;
        return $this;
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

    public function addFile($fileUrl, $inputName = "", $inputValue = "") {
        $arr = array();
        $arr['input_name'] = $inputName;
        $arr['input_value'] = $inputValue;
        $arr['file_url'] = $fileUrl;

        $this->files[] = $arr;
        return $this;
    }

    public function addCustomControl($control, $inputName, $inputLabel) {
        $arr = array();
        $arr['control'] = $control;
        $arr['input_name'] = $inputName;
        $arr['input_label'] = $inputLabel;
        $this->customControl[] = $arr;
        return $this;
    }

    public function addCustomControlValue($inputName, $controlName, $inputValue) {
        $this->customControlValue[$inputName][$controlName] = $inputValue;
        return $this;
    }
    
    public function setMaximum($maximum=null) {
        $this->maximum = $maximum;
        return $this;
    }
    
}
