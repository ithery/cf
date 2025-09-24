<?php

/**
 * Description of ImageAjax.
 *
 * @author Hery
 */
class CElement_FormInput_ImageAjax extends CElement_FormInput_Image {
    protected $maxUploadSize;   // in MB

    protected $allowedExtension;

    protected $validationCallback;

    /**
     * @var null|CElement_Helper_Cropper
     */
    protected $cropper;

    protected $tempStorage;

    protected $onExists;

    protected $withInfo;

    protected $fileProvider;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'image';
        $this->tag = 'div';
        $this->imgSrc = CApp_Base::noImageUrl();
        $this->maxWidth = '200';
        $this->maxHeight = '150';
        $this->maxUploadSize = 0;
        $this->withInfo = false;
        $this->allowedExtension = [];
        $this->disabledUpload = false;
        $this->onExists = false;
        $this->accept = 'image/*';
        $this->view = 'cresenity/element/form-input/image-ajax';
        $this->onBeforeParse(function (CView_View $view) {
            $ajaxName = $this->name;
            $ajaxName = str_replace('[', '-', $ajaxName);
            $ajaxName = str_replace(']', '-', $ajaxName);

            $ajaxUrl = CAjax::createMethod()->setType(CAjax_Engine_ImgUpload::class)
                ->setData('inputName', $ajaxName)
                ->setData('allowedExtension', $this->allowedExtension)
                ->setData('withInfo', $this->withInfo)
                ->setData('validationCallback', $this->validationCallback)
                ->setData('fileProvider', $this->fileProvider)
                ->makeUrl();

            $view->with('id', $this->id);
            $view->with('imgSrc', $this->imgSrc);
            $view->with('maxWidth', $this->maxWidth);
            $view->with('maxHeight', $this->maxHeight);
            $view->with('maxUploadSize', $this->maxUploadSize);
            $view->with('disabledUpload', $this->disabledUpload);
            $view->with('preTag', $this->pretag());
            $view->with('postTag', $this->posttag());
            $view->with('name', $this->name);
            $view->with('value', $this->value);
            $view->with('ajaxName', $ajaxName);
            $view->with('ajaxUrl', $ajaxUrl);
            $view->with('cropper', $this->cropper);
            $view->with('accept', $this->accept);
            $view->with('onExists', $this->onExists);
        });
    }

    public function setValue($val) {
        parent::setValue($val);
        if ($val && $this->imgSrc == null) {
            $this->imgSrc = CTemporary::getUrl('imgupload', $val);
        }

        return $this;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setMaxUploadSize($size) {
        $this->maxUploadSize = $size;

        return $this;
    }

    /**
     * @param int|array $ext
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

    public function setWithInfo($withInfo = true) {
        $this->withInfo = $withInfo;

        return $this;
    }

    public function setOnExists($bool) {
        $this->onExists = $bool;

        return $this;
    }

    public function setValidationCallback($callback) {
        $this->validationCallback = c::toSerializableClosure($callback);

        return $this;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $html = parent::html($indent);
        if ($this->cropper != null) {
            $html .= $this->cropper->html();
        }

        return $html;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = parent::js($indent);
        if ($this->cropper != null) {
            $js .= $this->cropper->js();
        }

        return $js;
    }

    /**
     * @return CElement_Helper_Cropper
     */
    public function cropper() {
        if ($this->cropper == null) {
            $this->cropper = new CElement_Helper_Cropper($this->id . '__cropper');
            $this->cropper->setOwner($this);
        }

        return $this->cropper;
    }

    /**
     * @return CManager_FileProvider_ImageFileProvider
     */
    public function withFileProvider() {
        if ($this->fileProvider == null) {
            $this->fileProvider = c::manager()->createImageFileProvider();
        }

        return $this->fileProvider;
    }

    public function setTempStorage($tempStorage) {
        $this->tempStorage = $tempStorage;

        return $this;
    }
}
