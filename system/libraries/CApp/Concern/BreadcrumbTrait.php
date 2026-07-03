<?php

use Opis\Closure\SerializableClosure;

defined('SYSPATH') or die('No direct access allowed.');

trait CApp_Concern_BreadcrumbTrait {
    /**
     * @var bool
     */
    private $showBreadcrumb = true;

    /**
     * @var array
     */
    private $breadcrumb = [];

    /**
     * @var null|Closure
     */
    private $breadcrumbCallback = null;

    public function showBreadcrumb($bool = true) {
        $this->showBreadcrumb = $bool;

        return $this;
    }

    /**
     * @param string $caption
     * @param string $url
     * @param bool   $lang
     *
     * @return CApp
     */
    public function addBreadcrumb($caption, $url = 'javascript:;', $lang = true) {
        if ($lang) {
            $caption = c::__($caption);
        }
        $this->breadcrumb[$caption] = $url;

        return $this;
    }

    public function getBreadcrumb() {
        $breadcrumb = $this->breadcrumb;
        if ($this->breadcrumbCallback != null) {
            $breadcrumb = CFunction::factory($this->breadcrumbCallback)->addArg($this->breadcrumb)->execute();
        }

        return $breadcrumb;
    }

    /**
     * @return bool
     */
    public function isShowBreadcrumb() {
        return $this->showBreadcrumb;
    }

    /**
     * @return null|Closure
     */
    public function getBreadcrumbCallback() {
        return $this->breadcrumbCallback;
    }

    /**
     * @param null|Closure $callback
     *
     * @return CApp
     */
    public function setBreadcrumbCallback($callback) {
        $this->breadcrumbCallback = c::toSerializableClosure($callback);

        return $this;
    }
}
