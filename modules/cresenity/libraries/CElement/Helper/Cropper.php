<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Cropper
 *
 * @author Hery
 */
class CElement_Helper_Cropper extends CElement_Element {

    use CElement_Trait_Template;

    protected $cropperWidth;
    protected $cropperHeight;
    protected $cropperResizable;
    protected $owner;
    protected $imgSrc;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->templateName = 'CElement/Helper/Cropper';
        $dataModule = array(
            'css' => array(
                'plugins/cropper/cropper.css',
            ),
            'js' => array(
                'plugins/cropper/cropper.js',
            ),
        );
        CManager::registerModule('cropper', $dataModule);

        $this->cropperResizable = true;
        
        $this->onBeforeParse(function() {
            $this->setVar('id', $this->id);
            $this->setVar('imgSrc', $this->imgSrc);
            $this->setVar('cropperWidth', $this->cropperWidth);
            $this->setVar('cropperHeight', $this->cropperHeight);
            $this->setVar('cropperResizable', $this->cropperResizable);
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
        return $this->cropperWidth/$this->cropperHeight;
    }

    public function getCropperResizable() {
        return $this->cropperResizable;
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
