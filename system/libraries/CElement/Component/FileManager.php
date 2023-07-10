<?php

use CManager_File_Connector_FileManager_FM as FM;

class CElement_Component_FileManager extends CElement_Component {
    //use CElement_Trait_UseViewTrait;
    protected $disk = null;

    protected $rootPath = null;

    protected $theme = null;

    protected $asPicker = false;

    /**
     * Overrides controllers for filemanager.
     *
     * @var array
     */
    protected $controller = [];

    protected $config;

    public function __construct($id = '') {
        parent::__construct($id);

        $this->tag = 'div';
        $this->config = [];
    }

    public static function factory($id = '') {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    /**
     * @param string $diskName
     *
     * @return $this
     */
    public function setDisk($diskName) {
        $this->disk = $diskName;

        return $this;
    }

    public function setRootPath($path) {
        $this->rootPath = $path;

        return $this;
    }

    public function setTheme($theme) {
        $this->theme = $theme;

        return $this;
    }

    public function setController($method, $controllerClass) {
        $this->controller[$method] = $controllerClass;

        return $this;
    }

    public function setAsPicker($bool = true) {
        $this->asPicker = $bool;

        return $this;
    }

    public function setConfig($key, $value) {
        carr::set($this->config, $key, $value);

        return $this;
    }

    public function build() {
        $config = $this->buildConfig();

        $ajaxMethod = CAjax::createMethod()->setType(CAjax_Engine_FileManager::class)->setData('config', $config);

        $ajaxUrl = $ajaxMethod->makeUrl();

        $config['connector_url'] = $ajaxUrl;

        $fm = new FM($config);
        // CManager::instance()->asset()->module()->registerRunTimeModule('jquery-ui-1.12.1.custom');
        CManager::instance()->asset()->module()->registerRunTimeModule('dropzone');
        CManager::instance()->asset()->module()->registerRunTimeModule('cropper');
        CManager::instance()->asset()->module()->registerRunTimeModule('mime-icons');

        CManager::registerCss('element/filemanager/fm.css');
        CManager::registerJs('element/filemanager/fm.js');
        $this->addView(
            'cresenity.element.component.file-manager.index',
            ['fm' => $fm]
        );
        //$this->addTemplate()->setTemplate('CElement/Component/FileManager/Index')->setVar('fm', $fm);
    }

    protected function buildConfig() {
        $config = $this->config ?: [];
        if ($this->disk != null) {
            $config['disk'] = $this->disk;
        }
        if ($this->rootPath != null) {
            $config['root_path'] = $this->rootPath;
        }
        if ($this->theme != null) {
            $config['theme'] = $this->theme;
        }

        $config['controller'] = $this->controller;
        $config['action'] = [
            'use' => false,
            'preview' => true,
            'download' => true,
            'resize' => false,
            'move' => true,
            'rename' => true,
            'delete' => true,
            'crop' => false,
        ];
        if ($this->asPicker != null) {
            $config['action']['use'] = true;
        }

        return $config;
    }
}
