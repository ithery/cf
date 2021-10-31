<?php

class CManager_Asset_File_JsFile extends CManager_Asset_FileAbstract {
    protected $load;

    public function __construct(array $options) {
        parent::__construct($options);
        $this->type = 'js';
        $this->load = carr::get($options, 'load');
    }

    public function getUrl($withHttp = false) {
        $file = $this->getPath();
        $path = $file;
        $path = carr::first(explode('?', $file));
        $docroot = str_replace(DS, '/', DOCROOT);
        $file = str_replace(DS, '/', $file);
        $base_url = curl::base();
        if ($withHttp) {
            $base_url = curl::base(false, 'http');
        }

        $file = str_replace($docroot, $base_url, $file);

        if (CF::config('assets.js.versioning')) {
            $separator = parse_url($file, PHP_URL_QUERY) ? '&' : '?';
            $interval = CF::config('assets.js.interval', 0);
            $version = CManager_Asset_Helper::getFileVersion($path, $interval);
            $file .= $separator . 'v=' . $version;
        }

        return $file;
    }

    protected function fullpath($file) {
        foreach ($this->mediaPaths as $dir) {
            $path = $dir . 'js' . DS . $file;
            if (file_exists($path)) {
                return $path;
            }
        }
        $dirs = CF::getDirs('media');
        $dirs = array_merge($this->mediaPaths, $dirs);

        foreach ($dirs as $dir) {
            $path = $dir . 'js' . DS . $file;

            if (file_exists($path)) {
                return $path;
            }
        }

        $path = DOCROOT . 'media' . DS . 'js' . DS;

        return $path . $file;
    }

    public function render($withHttp = false) {
        $attrDefer = $this->load == 'defer' ? ' defer' : '';
        $url = $this->getUrl($withHttp);

        $script = '<script src="' . $url . '"' . $attrDefer . '></script>';

        return $script;
    }
}
