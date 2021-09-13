<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan

 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 27, 2019, 10:18:47 PM
 */
trait CApp_Concern_BreadcrumbTrait {
    private $showBreadcrumb = true;

    private $breadcrumb = [];

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
            $caption = clang::__($caption);
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

    public function setBreadcrumbCallback($callback) {
        $this->breadcrumbCallback = CHelper::closure()->serializeClosure($callback);
        return $this;
    }
}
