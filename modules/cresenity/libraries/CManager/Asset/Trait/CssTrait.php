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
            if ($f instanceof CManager_Asset_FileAbstract) {
                $urls[] = $f->getUrl();
            } else {
                $urls[] = CManager_Asset_Helper::urlCssFile($f);
            }
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
        $fileOptions = $file;
        if (!is_array($fileOptions)) {
            $fileOptions = [
                'script' => $file,
            ];
        }
        $fileOptions['type'] = 'js';
        $fileOptions['pos'] = $pos;

        $this->scripts[$pos]['css_file'][] = new CManager_Asset_File_CssFile($fileOptions);

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
