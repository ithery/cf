<?php

/**
 * 
 */
class CElement_FormInput_FileAjax extends CElement_FormInput {

    use CElement_Trait_Template;

    protected $fileName;
    protected $acceptFile;
    protected $maxUploadSize;   // in MB
    protected $disabledUpload;
    protected $tempStorage;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "file";
        $this->tag = "div";
        $this->fileName = '';
        $this->acceptFile = '.doc,.docx,.xml,.pdf';
        $this->maxUploadSize = 0;
        $this->disabledUpload = false;
        $this->templateName = 'CElement/FormInput/FileAjax';
        $this->onBeforeParse(function() {

            $ajaxName = $this->name;
            $ajaxName = str_replace('[', '-', $ajaxName);
            $ajaxName = str_replace(']', '-', $ajaxName);

            $ajaxUrl = CAjax::createMethod()->setType('FileUpload')
                    ->setData('inputName', $ajaxName)
                    ->makeUrl();  
                   
            $this->setVar('id', $this->id);
            $this->setVar('fileName', $this->fileName);
            $this->setVar('acceptFile', $this->acceptFile);
            $this->setVar('maxUploadSize', $this->maxUploadSize);
            $this->setVar('disabledUpload', $this->disabledUpload);
            $this->setVar('preTag', $this->pretag());
            $this->setVar('postTag', $this->posttag());
            $this->setVar('name', $this->name);
            $this->setVar('value', $this->value);
            $this->setVar('ajaxName', $ajaxName);
            $this->setVar('ajaxUrl', $ajaxUrl);
        });
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;
        return $this;
    }

    public function setAcceptFile($accept)
    {
        $this->acceptFile = $accept;
        return $this;
    }

    public function setMaxUploadSize($size) {
        $this->maxUploadSize = $size;
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
    
    public function setTempStorage($tempStorage) {
        $this->tempStorage = $tempStorage;
        return $this;
    }
}
