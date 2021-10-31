<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 1:18:28 AM
 */
abstract class CManager_Asset_Container implements CManager_Asset_ContainerInterface {
    use CManager_Asset_Trait_JsTrait,
        CManager_Asset_Trait_CssTrait;

    protected $scripts;

    /**
     * @var array
     */
    protected $mediaPaths;

    public function __construct() {
        $this->reset();
    }

    public function addMediaPath($mediaPath) {
        $this->mediaPaths[] = $mediaPath;

        return $this;
    }

    public function reset() {
        $allPos = CManager_Asset::allAvailablePos();
        $allType = CManager_Asset::allAvailableType();
        $this->scripts = [];
        foreach ($allPos as $pos) {
            $this->scripts[$pos] = [];
            foreach ($allType as $type) {
                $this->scripts[$pos][$type] = [];
            }
        }
        $this->mediaPaths = [];
    }

    public function registerJsInlines($jsArray, $pos = CManager_Asset::POS_HEAD) {
        $jsArray = $jsArray !== null ? (is_array($jsArray) ? $jsArray : [$jsArray]) : [];
        foreach ($jsArray as $js) {
            $this->registerJsInline($js, $pos);
        }
    }

    public function registerJsInline($js, $pos = CManager_Asset::POS_HEAD) {
        $this->scripts[$pos]['js'][] = $js;
    }

    public function registerCssInlines($cssArray, $pos = CManager_Asset::POS_HEAD) {
        $cssArray = $cssArray !== null ? (is_array($cssArray) ? $cssArray : [$cssArray]) : [];
        foreach ($cssArray as $css) {
            $this->registerCssInline($css, $pos);
        }
    }

    public function registerCssInline($css, $pos = CManager_Asset::POS_HEAD) {
        $this->scripts[$pos]['css'][] = $css;
    }

    public function registerPlains($plains, $pos = CManager_Asset::POS_HEAD) {
        $plains = $plains !== null ? (is_array($plains) ? $plains : [$plains]) : [];
        foreach ($plains as $plain) {
            $this->registerPlain($plain, $pos);
        }
    }

    public function registerPlain($plain, $pos = CManager_Asset::POS_HEAD) {
        $this->scripts[$pos]['plain'][] = $plain;
    }

    public function getScripts($pos = null) {
        if ($pos == null) {
            return $this->scripts;
        }

        return carr::get($this->scripts, $pos, []);
    }
}
