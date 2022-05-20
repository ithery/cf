<?php

/**
 * Description of ImageAjax.
 *
 * @author Hery
 */
class CElement_FormInput_ImageAjax extends CElement_FormInput_Image {
    protected $maxUploadSize;   // in MB

    protected $cropper;

    protected $tempStorage;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'image';
        $this->tag = 'div';
        $this->imgSrc = CApp_Base::noImageUrl();
        $this->maxWidth = '200';
        $this->maxHeight = '150';
        $this->maxUploadSize = 0;
        $this->disabledUpload = false;
        $this->accept = 'image/*';
        $this->view = 'cresenity/element/form-input/image-ajax';
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
            $this->setVar('disabledUpload', $this->disabledUpload);
            $this->setVar('preTag', $this->pretag());
            $this->setVar('postTag', $this->posttag());
            $this->setVar('name', $this->name);
            $this->setVar('value', $this->value);
            $this->setVar('ajaxName', $ajaxName);
            $this->setVar('ajaxUrl', $ajaxUrl);
            $this->setVar('cropper', $this->cropper);
            $this->setVar('accept', $this->accept);
        });
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

    public function setTempStorage($tempStorage) {
        $this->tempStorage = $tempStorage;

        return $this;
    }
}
