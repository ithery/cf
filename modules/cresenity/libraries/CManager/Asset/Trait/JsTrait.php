<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 1:41:39 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CManager_Asset_Trait_JsTrait {

    public function fullpathJsFile($file) {
        $dirs = CF::getDirs('media');

        foreach ($dirs as $dir) {
            $path = $dir . 'js' . DS . $file;


            if (file_exists($path)) {
                return $path;
            }
        }

        $path = DOCROOT . "media" . DS . 'js' . DS;
        return $path . $file;
    }

    public function getAllJsFileUrl() {
        $files = $this->jsFiles();
        $urls = array();
        foreach ($files as $f) {
            $urls[] = CManager_Asset_Helper::urlJsFile($f);
        }
        return $urls;
    }

    public function registerJsFiles($files, $pos = "end") {
        $files = $files !== null ? (is_array($files) ? $files : array($files)) : array();
        foreach ($files as $file) {
            $this->registerJsFile($file, $pos);
        }
        return $this;
    }

    public function registerJsFile($file, $pos = "end") {
       
        $dir_file = $file;
        $js_version = '';
        if (strpos($file, '?') !== false) {
            $dir_file = substr($file, 0, strpos($file, '?'));
            $js_version = substr($file, strpos($file, '?'), strlen($file) - 1);
        }
        $js_file = $this->fullpathJsFile($dir_file);
        if (strpos($dir_file, 'http') !== false) {
            $js_file = $dir_file;
            // do nothing
        } else {
            $js_file = $this->fullpathJsFile($dir_file);
            if (!file_exists($js_file)) {
                trigger_error('JS File not exists, ' . $file);
            }
            if (strlen($js_version) > 0) {
                $js_file .= $js_version;
            }
        }
        $this->scripts[$pos]['js_file'][] = $js_file;
       
        return $this;
    }

    public function unregisterJsFiles($files, $pos = null) {
        if (!is_array($files)) {
            $files = array($files);
        }
        foreach ($files as $file) {
            $this->unregisterJsFile($file, $pos);
        }
    }

    public function unregisterJsFile($file, $pos = null) {
        $fullpathFile = $this->fullpathJsFile($file);
        //we will locate all pos for this pos if pos =null;
        if ($pos == null) {
            $pos = self::allAvailablePos();
        }
        if (!is_array($pos)) {
            $pos = array($pos);
        }
        foreach ($pos as $p) {

            $jsFiles = &$this->scripts[$p]['js_file'];
            foreach ($jsFiles as $k => $jsFile) {

                if ($jsFile == $fullpathFile) {

                    unset($jsFiles[$k]);
                }
            }
        }
    }

    public function jsFiles() {
        $js_file_array = array();
        foreach ($this->scripts as $script) {

            foreach ($script['js_file'] as $k) {
                $js_file_array[] = $k;
            }
        }
        return $js_file_array;
    }

}
