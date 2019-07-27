<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 27, 2019, 10:18:47 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_App_Breadcrumb {

    private $showBreadcrumb = true;
    private $breadcrumb = array();
    private $breadcrumbCallback = null;

    public function showBreadcrumb($bool = true) {
        $this->show_breadcrumb = $bool;
        return $this;
    }

    /**
     * 
     * @param string $caption
     * @param string $url
     * @param boolean $lang
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
