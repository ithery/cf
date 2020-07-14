<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 28, 2019, 1:41:33 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;

class CElement_Component_FileManager extends CElement_Component {

    protected $disk = null;
    protected $rootPath = null;
    protected $theme = null;
    protected $asPicker = false;

    public function __construct($id = "") {
        parent::__construct($id);

        $this->tag = 'div';
    }

    public static function factory($id = "") {
        return new CElement_Component_FileManager($id);
    }

    /**
     * 
     * @param type $diskName
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

    public function setAsPicker($bool = true) {
        $this->asPicker = $bool;
        return $this;
    }

    public function build() {

        $config = $this->buildConfig();

        $ajaxMethod = CAjax::createMethod()->setType('FileManager')->setData('config', $config);

        $ajaxUrl = $ajaxMethod->makeUrl();

        $config['connector_url'] = $ajaxUrl;

        $fm = new FM($config);
        CManager::instance()->asset()->module()->registerRunTimeModule('jquery-ui-1.12.1.custom');
        CManager::instance()->asset()->module()->registerRunTimeModule('dropzone');
        CManager::instance()->asset()->module()->registerRunTimeModule('cropper');
        CManager::registerCss('element/filemanager/fm.css?v=2' . uniqid());
        CManager::registerJs('element/filemanager/fm.js?v=1' . uniqid());
        $this->addTemplate()->setTemplate('CElement/Component/FileManager/Index')->setVar('fm', $fm);
    }

    protected function buildConfig() {
        $config = [];
        if ($this->disk != null) {
            $config['disk'] = $this->disk;
        }
        if ($this->rootPath != null) {
            $config['root_path'] = $this->rootPath;
        }
        if ($this->theme != null) {
            $config['theme'] = $this->theme;
        }
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
