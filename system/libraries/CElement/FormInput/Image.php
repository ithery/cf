<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 24, 2018, 6:55:42 PM
 */
class CElement_FormInput_Image extends CElement_FormInput {
    use CElement_Trait_UseViewTrait,
        CTrait_Compat_Element_FormInput_Image;

    protected $imgSrc;

    protected $maxWidth;

    protected $maxHeight;

    protected $disabledUpload;

    protected $accept;

    protected $labels = [];

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'image';
        $this->tag = 'div';
        $this->imgSrc = CApp_Base::noImageUrl();
        $this->maxWidth = '200';
        $this->maxHeight = '150';
        $this->disabledUpload = false;
        $this->accept = 'image/*';
        $this->view = 'cresenity/element/form-input/image';

        $this->onBeforeParse(function (CView_View $view) {
            $view->with('id', $this->id);
            $view->with('imgSrc', $this->imgSrc);
            $view->with('maxWidth', $this->maxWidth);
            $view->with('maxHeight', $this->maxHeight);
            $view->with('disabledUpload', $this->disabledUpload);
            $view->with('accept', $this->accept);
            $view->with('preTag', $this->pretag());
            $view->with('postTag', $this->posttag());
            $view->with('name', $this->name);
            $view->with('value', $this->value);
        });
    }

    /**
     * @param string $imgsrc
     *
     * @return $this
     */
    public function setImgSrc($imgsrc) {
        $this->imgSrc = $imgsrc;

        return $this;
    }

    /**
     * @param int $maxwidth
     *
     * @return $this
     */
    public function setMaxWidth($maxwidth) {
        $this->maxWidth = $maxwidth;

        return $this;
    }

    /**
     * @param int $maxheight
     *
     * @return $this
     */
    public function setMaxHeight($maxheight) {
        $this->maxHeight = $maxheight;

        return $this;
    }

    /**
     * @param string $accept
     *
     * @return $this
     */
    public function setAccept($accept) {
        $this->accept = $accept;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
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
}
