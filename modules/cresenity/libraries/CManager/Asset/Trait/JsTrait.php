<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 1:41:39 AM
 */
trait CManager_Asset_Trait_JsTrait {
    public function fullpathJsFile($file) {
        foreach ($this->mediaPaths as $dir) {
            $path = $dir . 'js' . DS . $file;
            if (file_exists($path)) {
                return $path;
            }
        }
        $dirs = CF::getDirs('media');

        foreach ($dirs as $dir) {
            $path = $dir . 'js' . DS . $file;

            if (file_exists($path)) {
                return $path;
            }
        }

        $path = DOCROOT . 'media' . DS . 'js' . DS;
        return $path . $file;
    }

    public function getAllJsFileUrl() {
        $files = $this->jsFiles();

        $urls = [];
        foreach ($files as $f) {
            $urls[] = CManager_Asset_Helper::urlJsFile($f);
        }
        return $urls;
    }

    public function registerJsFiles($files, $pos = 'end') {
        $files = $files !== null ? (is_array($files) ? $files : [$files]) : [];
        foreach ($files as $file) {
            $this->registerJsFile($file, $pos);
        }
        return $this;
    }

    public function registerJsFile($file, $pos = 'end') {
        $dir_file = $file;
        $js_version = '';
        if (!cstr::startsWith($file, 'http')) {
            if (strpos($file, '?') !== false) {
                $dir_file = substr($file, 0, strpos($file, '?'));
                $js_version = substr($file, strpos($file, '?'), strlen($file) - 1);
            }
        }
        $js_file = $this->fullpathJsFile($dir_file);
        if (strpos($dir_file, 'http') !== false) {
            $js_file = $dir_file;
        } else {
            $js_file = $this->fullpathJsFile($dir_file);
            if (!file_exists($js_file)) {
                throw new Exception('JS File not exists, ' . $file);
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
            $files = [$files];
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
            $pos = [$pos];
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
        $js_file_array = [];
        foreach ($this->scripts as $script) {
            foreach ($script['js_file'] as $k) {
                $js_file_array[] = $k;
            }
        }
        return $js_file_array;
    }
}
