<?php

/**
 * Description of ImageAjax.
 *
 * @author Hery
 */
class CElement_FormInput_MultipleImageAjax extends CElement_FormInput {
    use CApp_Trait_Template;

    protected $imgSrc;

    protected $maxWidth;

    protected $maxHeight;

    protected $maxUploadSize;

    protected $limitFile;

    protected $disabledUpload;

    protected $cropper;

    protected $files;

    protected $link;

    protected $removeLink;

    protected $customControl;

    protected $customControlValue;

    protected $maximum;

    protected $accept;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'image';
        $this->tag = 'div';
        $this->imgSrc = '';
        $this->maxWidth = '200';
        $this->maxHeight = '150';
        $this->maxUploadSize = 0;
        $this->limitFile = 10;
        $this->accept = 'image/*';
        $this->disabledUpload = false;
        $this->templateName = 'CElement/FormInput/MultipleImageAjax';
        $this->removeLink = true;
        $this->files = [];
        $this->maximum = null;
        $this->customControl = [];
        $this->customControlValue = [];
        $this->onBeforeParse(function () {
            $ajaxName = $this->name;
            $ajaxName = str_replace('[', '-', $ajaxName);
            $ajaxName = str_replace(']', '-', $ajaxName);

            $ajaxUrl = CAjax::createMethod()->setType('ImgUpload')
                ->setData('inputName', $ajaxName)
                ->makeUrl();
            $this->setVar('id', $this->id);
            $this->setVar('imgSrc', $this->imgSrc);
            $this->setVar('maxWidth', $this->maxWidth);
            $this->setVar('maxHeight', $this->maxHeight);
            $this->setVar('maxUploadSize', $this->maxUploadSize);
            $this->setVar('limitFile', $this->limitFile);
            $this->setVar('disabledUpload', $this->disabledUpload);
            $this->setVar('preTag', $this->pretag());
            $this->setVar('postTag', $this->posttag());
            $this->setVar('name', $this->name);
            $this->setVar('value', $this->value);
            $this->setVar('ajaxName', $ajaxName);
            $this->setVar('ajaxUrl', $ajaxUrl);
            $this->setVar('files', $this->files);
            $this->setVar('maximum', $this->maximum == null ? 0 : $this->maximum);
            $this->setVar('removeLink', $this->removeLink);
            $this->setVar('customControl', $this->customControl);
            $this->setVar('customControlValue', $this->customControlValue);
            $this->setVar('cropper', $this->cropper);
            $this->setVar('accept', $this->accept);
        });
    }

    // protected function build() {
    //     $ajaxName = $this->name;
    //     $ajaxName = str_replace('[', '-', $ajaxName);
    //     $ajaxName = str_replace(']', '-', $ajaxName);

    //     $ajaxUrl = CAjax::createMethod()->setType('ImgUpload')
    //         ->setData('inputName', $ajaxName)
    //         ->makeUrl();

    //     $viewData['id'] = $this->id;
    //     $viewData['imgSrc'] = $this->imgSrc;
    //     $viewData['maxWidth'] = $this->maxWidth;
    //     $viewData['maxHeight'] = $this->maxHeight;
    //     $viewData['maxUploadSize'] = $this->maxUploadSize;
    //     $viewData['limitFile'] = $this->limitFile;
    //     $viewData['disabledUpload'] = $this->disabledUpload;
    //     $viewData['preTag'] = $this->preTag();
    //     $viewData['postTag'] = $this->postTag();
    //     $viewData['name'] = $this->name;
    //     $viewData['value'] = $this->value;
    //     $viewData['ajaxName'] = $ajaxName;
    //     $viewData['ajaxUrl'] = $this->ajaxUrl;
    //     $viewData['files'] = $this->files;
    //     $viewData['maximum'] = $this->maximum == null ?: 0;
    //     $viewData['removeLink'] = $this->removeLink;
    //     $viewData['customControl'] = $this->customControl;
    //     $viewData['cropper'] = $this->cropper;
    //     $viewData['accept'] = $this->accept;
    //     cdbg::dd($viewData);
    // }

    /**
     * @param int $indent
     *
     * @return string
     */
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

    public function setLimitFile($limit) {
        $this->limitFile = $limit;

        return $this;
    }

    /**
     * @return CElement_Helper_Cropper
     */
    public function cropper() {
        if ($this->cropper == null) {
            $this->cropper = new CElement_Helper_Cropper();
            $this->cropper->setOwner($this);
        }

        return $this->cropper;
    }

    public function addFile($fileUrl, $inputName = '', $inputValue = '') {
        $arr = [];
        $arr['input_name'] = $inputName;
        $arr['input_value'] = $inputValue;
        $arr['file_url'] = $fileUrl;

        $this->files[] = $arr;

        return $this;
    }

    public function addCustomControl($control, $inputName, $inputLabel) {
        $arr = [];
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

    public function setMaximum($maximum = null) {
        $this->maximum = $maximum;

        return $this;
    }

    public function setAccept($accept) {
        $this->accept = $accept;

        return $this;
    }
}
