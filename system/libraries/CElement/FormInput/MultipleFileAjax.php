<?php

/**
 * Description of ImageAjax.
 *
 * @author Hery
 */
class CElement_FormInput_MultipleFileAjax extends CElement_FormInput {
    use CElement_Trait_UseViewTrait;

    protected $imgSrc;

    protected $maxWidth;

    protected $maxHeight;

    protected $maxUploadSize;

    /**
     * @var array
     */
    protected $allowedExtension;

    /**
     * @var CFunction_SerializableClosure
     */
    protected $validationCallback;

    /**
     * @var int
     */
    protected $limitFile;

    protected $disabledUpload;

    /**
     * @var null|CElement_Helper_Cropper
     */
    protected $cropper;

    protected $files;

    protected $link;

    protected $removeLink;

    protected $customControl;

    protected $customControlValue;

    protected $maximum;

    protected $accept;

    protected $withInfo;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'image';
        $this->tag = 'div';
        $this->imgSrc = '';
        $this->maxWidth = '200';
        $this->maxHeight = '150';
        $this->maxUploadSize = 0;
        $this->allowedExtension = [];
        $this->limitFile = 10;
        $this->accept = '*/*';
        $this->disabledUpload = false;
        $this->view = 'cresenity/element/form-input/multiple-file-ajax';
        $this->removeLink = true;
        $this->files = [];
        $this->maximum = null;
        $this->customControl = [];
        $this->customControlValue = [];
        $this->withInfo = false;
        c::manager()->registerModule('mime-icons');
        $this->onBeforeParse(function (CView_View $view) {
            $ajaxName = $this->name;
            $ajaxName = str_replace('[', '-', $ajaxName);
            $ajaxName = str_replace(']', '-', $ajaxName);

            $ajaxUrl = CAjax::createMethod()->setType(CAjax_Engine_FileUpload::class)
                ->setData('inputName', $ajaxName)
                ->setData('allowedExtension', $this->allowedExtension)
                ->setData('validationCallback', $this->validationCallback)
                ->setData('withInfo', $this->withInfo)
                ->makeUrl();

            $view->with('id', $this->id);
            $view->with('imgSrc', $this->imgSrc);
            $view->with('maxWidth', $this->maxWidth);
            $view->with('maxHeight', $this->maxHeight);
            $view->with('maxUploadSize', $this->maxUploadSize);
            $view->with('limitFile', $this->limitFile);
            $view->with('disabledUpload', $this->disabledUpload);
            $view->with('preTag', $this->pretag());
            $view->with('postTag', $this->posttag());
            $view->with('name', $this->name);
            $view->with('value', $this->value);
            $view->with('ajaxName', $ajaxName);
            $view->with('ajaxUrl', $ajaxUrl);
            $view->with('files', $this->files);
            $view->with('maximum', $this->maximum == null ? 0 : $this->maximum);
            $view->with('removeLink', $this->removeLink);
            $view->with('customControl', $this->customControl);
            $view->with('customControlValue', $this->customControlValue);
            $view->with('cropper', $this->cropper);
            $view->with('accept', $this->accept);
        });
    }

    public function setWithInfo($withInfo = true) {
        $this->withInfo = $withInfo;

        return $this;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $templateHtml = $this->getViewHtml();
        $html = $templateHtml;
        if ($this->cropper != null) {
            $html .= $this->cropper->html();
        }

        return $html;
    }

    public function js($indent = 0) {
        $templateJs = $this->getViewJs();
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
     * @param string|array $ext
     *
     * @return $this
     */
    public function setAllowedExtension($ext) {
        $arr = $ext;
        if (!is_array($arr)) {
            $arr = [$ext];
        }
        $this->allowedExtension = $arr;

        return $this;
    }

    public function setValidationCallback($callback) {
        $this->validationCallback = c::toSerializableClosure($callback);

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

    public function setFileFromResources(CModel_Collection $collection, $inputName = '') {
        foreach ($collection as $model) {
            $this->addFile($model->getUrl(), $inputName, $model->getKey());
        }

        return $this;
    }

    public function addFile($fileUrl, $inputName = '', $inputValue = '', $fileName = '') {
        $arr = [];
        $arr['input_name'] = $inputName;
        $arr['input_value'] = $inputValue;
        $arr['file_url'] = $fileUrl;
        $arr['file_name'] = $fileName;

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
