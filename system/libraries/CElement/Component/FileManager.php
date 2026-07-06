<?php

use CManager_File_Connector_FileManager_FM as FM;

/**
 * Renders a web-based file manager UI (browse/upload/rename/move/delete) backed by a
 * CStorage disk. Add one via ComponentTrait::addFileManager(); rendering/behavior is
 * handled client-side by cres.js (see media/js/cres/src/element/component/FileManager),
 * driven by a CManager_File_Connector_FileManager_FM instance built from this element's
 * config in build().
 *
 * $rootPath is a container directory on the chosen disk: actual content is expected to
 * live under its 'files'/'photos' subfolders (see CManager_File_Connector_FileManager_FM::getCategoryName()),
 * not directly inside $rootPath itself.
 */
class CElement_Component_FileManager extends CElement_Component {
    //use CElement_Trait_UseViewTrait;

    /**
     * @var null|string
     */
    protected $disk = null;

    /**
     * @var null|string
     */
    protected $rootPath = null;

    /**
     * @var null|string
     */
    protected $theme = null;

    /**
     * @var bool
     */
    protected $asPicker = false;

    /**
     * @var bool
     */
    protected $enableAuth = false;

    /**
     * Overrides controllers for filemanager.
     *
     * @var array
     */
    protected $controller = [];

    /**
     * Additional raw config passed to CManager_File_Connector_FileManager_FM, see setConfig().
     *
     * @var array
     */
    protected $config;

    /**
     * Short (2-letter) locale code used client-side to format dates (e.g. 'id', 'en') --
     * defaults from CF::getLocale(), same convention as CElement_Component_Calendar's
     * own $locale. Passed through to FileManager.js as settings.locale.
     *
     * @var string
     */
    protected $locale;

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id = '') {
        parent::__construct($id);

        $this->tag = 'div';
        $this->config = [];

        $locale = CF::getLocale();
        if (strlen($locale) > 2) {
            $locale = strtolower(substr($locale, 0, 2));
        }
        $this->locale = $locale;
    }

    /**
     * @param string $id
     *
     * @return static
     */
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

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setRootPath($path) {
        $this->rootPath = $path;

        return $this;
    }

    /**
     * @param string $theme
     *
     * @return $this
     */
    public function setTheme($theme) {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @param string $method
     * @param string $controllerClass
     *
     * @return $this
     */
    public function setController($method, $controllerClass) {
        $this->controller[$method] = $controllerClass;

        return $this;
    }

    /**
     * @param string $locale short (2-letter) locale code, e.g. 'id'
     *
     * @return $this
     */
    public function setLocale($locale) {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setAsPicker($bool = true) {
        $this->asPicker = $bool;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setConfig($key, $value) {
        carr::set($this->config, $key, $value);

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setEnableAuth($bool = true){
        $this->enableAuth = $bool;
        return $this;
    }

    /**
     * @return void
     */
    public function build() {
        $config = $this->buildConfig();

        $ajaxMethod = CAjax::createMethod()->setType(CAjax_Engine_FileManager::class)->setData('config', $config);
        if (c::app()->isAuthEnabled() || $this->enableAuth) {
            $ajaxMethod->enableAuth();
        }

        $ajaxUrl = $ajaxMethod->makeUrl();

        $config['connector_url'] = $ajaxUrl;

        $fm = new FM($config);
        // CManager::instance()->asset()->module()->registerRunTimeModule('jquery-ui-1.12.1.custom');
        CManager::instance()->asset()->module()->registerRunTimeModule('cropper');
        CManager::instance()->asset()->module()->registerRunTimeModule('mime-icons');

        $this->addView(
            'cresenity.element.component.file-manager.index',
            ['fm' => $fm]
        );
    }

    /**
     * @return array
     */
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
        $config['locale'] = $this->locale;
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
