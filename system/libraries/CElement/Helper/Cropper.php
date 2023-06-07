<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Description of Cropper.
 *
 * @author Hery
 */
class CElement_Helper_Cropper extends CElement_Element {
    //use CElement_Trait_Template;
    use CElement_Trait_UseViewTrait;

    protected $cropperWidth;

    protected $cropperHeight;

    protected $cropperResizable;

    protected $owner;

    protected $imgSrc;

    public function __construct($id = '', $tag = 'div') {
        parent::__construct($id, $tag);
        //$this->templateName = 'CElement/Helper/Cropper';
        $this->view = 'cresenity/element/helper/cropper';
        $dataModule = [
            'css' => [
                'plugins/cropper/cropper.css',
            ],
            'js' => [
                'plugins/cropper/cropper.js',
            ],
        ];
        CManager::registerModule('cropper', $dataModule);

        $this->cropperResizable = true;

        $this->onBeforeParse(function (CView_View $view) {
            $view->with('id', $this->id);
            $view->with('imgSrc', $this->imgSrc);
            $view->with('cropperWidth', $this->cropperWidth);
            $view->with('cropperHeight', $this->cropperHeight);
            $view->with('cropperResizable', $this->cropperResizable);
        });
    }

    public function setOwner($owner) {
        $this->owner = $owner;

        return $this;
    }

    public function setSize($width, $height) {
        $this->cropperWidth = $width;
        $this->cropperHeight = $height;

        return $this;
    }

    public function setCropperResizable($bool = true) {
        $this->cropperResizable = $bool;

        return $this;
    }

    public function getCropperWidth() {
        return $this->cropperWidth;
    }

    public function getCropperHeight() {
        return $this->cropperHeight;
    }

    public function getAspectRatio() {
        return $this->cropperWidth / $this->cropperHeight;
    }

    public function getCropperResizable() {
        return $this->cropperResizable;
    }
}
