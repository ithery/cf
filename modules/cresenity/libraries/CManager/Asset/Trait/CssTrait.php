<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 1:41:33 AM
 */
trait CManager_Asset_Trait_CssTrait {
    public function fullpathCssFile($file) {
        foreach ($this->mediaPaths as $dir) {
            $path = $dir . 'css' . DS . $file;

            if (file_exists($path)) {
                return $path;
            }
        }
        $dirs = CF::getDirs('media');

        foreach ($dirs as $dir) {
            $path = $dir . 'css' . DS . $file;

            if (file_exists($path)) {
                return $path;
            }
        }
        $path = DOCROOT . 'media' . DS . 'css' . DS;
        return $path . $file;
    }

    public function getAllCssFileUrl() {
        $files = $this->cssFiles();

        $urls = [];
        foreach ($files as $f) {
            $urls[] = CManager_Asset_Helper::urlCssFile($f);
        }
        return $urls;
    }

    public function registerCssFiles($files, $pos = 'head') {
        $files = $files !== null ? (is_array($files) ? $files : [$files]) : [];
        foreach ($files as $file) {
            $this->registerCssFile($file, $pos);
        }
    }

    public function registerCssFile($file, $pos = 'head') {
        $dir_file = $file;
        $css_version = '';

        if (strpos($file, '?') !== false) {
            $dir_file = substr($file, 0, strpos($file, '?'));
            $css_version = substr($file, strpos($file, '?'), strlen($file) - 1);
        }
        if (strpos($dir_file, 'http') !== false) {
            $css_file = $dir_file;
        } else {
            $css_file = $this->fullpathCssFile($dir_file);
            if (!file_exists($css_file)) {
                throw new Exception('CSS File not exists, ' . $file);
            }
            if (strlen($css_version) > 0) {
                $css_file .= $css_version;
            }
        }
        $this->scripts[$pos]['css_file'][] = $css_file;
        return $this;
    }

    public function unregisterCssFiles($files, $pos = null) {
        if (!is_array($files)) {
            $files = [$files];
        }
        foreach ($files as $file) {
            $this->unregisterCssFile($file, $pos);
        }
    }

    public function unregisterCssFile($file, $pos = null) {
        $fullpathFile = $this->fullpathCssFile($file);
        //we will locate all pos for this pos if pos =null;
        if ($pos == null) {
            $pos = self::allAvailablePos();
        }
        if (!is_array($pos)) {
            $pos = [$pos];
        }
        foreach ($pos as $p) {
            $cssFiles = &$this->scripts[$p]['css_file'];
            foreach ($cssFiles as $k => $cssFile) {
                if ($cssFile == $fullpathFile) {
                    unset($cssFiles[$k]);
                }
            }
        }
    }

    public function cssFiles() {
        $cssFileArray = [];
        foreach ($this->scripts as $script) {
            foreach ($script['css_file'] as $k) {
                $cssFileArray[] = $k;
            }
        }
        return $cssFileArray;
    }
}
