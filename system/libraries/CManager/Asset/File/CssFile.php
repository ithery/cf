<?php

class CManager_Asset_File_CssFile extends CManager_Asset_FileAbstract {
    protected $media;

    public function __construct(array $options) {
        parent::__construct($options);
        $this->type = 'css';
        $this->media = carr::get($options, 'media');
    }

    public function getUrl($withHttp = false) {
        $file = $this->getPath();
        $path = $file;
        $path = carr::first(explode('?', $file));
        $docroot = str_replace(DS, '/', DOCROOT);
        $file = str_replace(DS, '/', $file);
        $base_url = curl::base();
        if ($withHttp || CManager::instance()->isMobile()) {
            $base_url = curl::base(false, 'http');
        }

        $file = str_replace($docroot, $base_url, $file);

        if (CF::config('assets.css.versioning')) {
            $separator = parse_url($file, PHP_URL_QUERY) ? '&' : '?';
            $interval = CF::config('assets.css.interval', 0);
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

        foreach ($dirs as $dir) {
            $path = $dir . 'css' . DS . $file;

            if (file_exists($path)) {
                return $path;
            }
        }

        $path = DOCROOT . 'media' . DS . 'css' . DS;

        return $path . $file;
    }

    public function render($withHttp = false) {
        $attrMedia = $this->media ? ' media="' . $this->media . '"' : '';
        $url = $this->getUrl($withHttp);

        $script = '<link href="' . $url . '"' . $attrMedia . ' rel="stylesheet" />';

        return $script;
    }
}
