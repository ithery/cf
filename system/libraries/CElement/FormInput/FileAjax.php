<?php

class CElement_FormInput_FileAjax extends CElement_FormInput {
    use CElement_Trait_UseViewTrait;

    protected $fileName;

    protected $acceptFile;

    protected $maxUploadSize;   // in MB

    protected $disabledUpload;

    protected $tempStorage;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'file';
        $this->tag = 'div';
        $this->fileName = '';
        $this->acceptFile = '.doc,.docx,.xml,.pdf';
        $this->maxUploadSize = 0;
        $this->disabledUpload = false;
        $this->view = 'cresenity/element/form-input/file-ajax';
        $this->onBeforeParse(function (CView_View $view) {
            $ajaxName = $this->name;
            $ajaxName = str_replace('[', '-', $ajaxName);
            $ajaxName = str_replace(']', '-', $ajaxName);

            $ajaxUrl = CAjax::createMethod()->setType('FileUpload')
                ->setData('inputName', $ajaxName)
                ->makeUrl();

            $view->with('id', $this->id);
            $view->with('fileName', $this->fileName);
            $view->with('acceptFile', $this->acceptFile);
            $view->with('maxUploadSize', $this->maxUploadSize);
            $view->with('disabledUpload', $this->disabledUpload);
            $view->with('preTag', $this->pretag());
            $view->with('postTag', $this->posttag());
            $view->with('name', $this->name);
            $view->with('value', $this->value);
            $view->with('ajaxName', $ajaxName);
            $view->with('ajaxUrl', $ajaxUrl);
        });
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;

        return $this;
    }

    public function setAcceptFile($accept) {
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
        $templateHtml = $this->getViewHtml();
        $html = $templateHtml;

        return $html;
    }

    public function js($indent = 0) {
        $templateJs = $this->getViewJs();
        $js = $templateJs;

        return $js;
    }

    public function setTempStorage($tempStorage) {
        $this->tempStorage = $tempStorage;

        return $this;
    }
}
