<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 28, 2019, 1:41:33 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;

class CElement_Component_FileManager extends CElement_Component {

    public function __construct($id = "") {
        parent::__construct($id);

        $this->tag = 'div';
    }

    public static function factory($id = "") {
        return new CElement_Component_FileManager($id);
    }

    public function build() {
        $fm = new FM();
        CManager::instance()->asset()->module()->registerRunTimeModule('jquery-ui-1.12.1.custom');
        CManager::instance()->asset()->module()->registerRunTimeModule('dropzone');
        CManager::instance()->asset()->module()->registerRunTimeModule('cropper');
        CManager::registerCss('element/filemanager/fm.css');
        CManager::registerJs('element/filemanager/fm.js?v=1');
        $this->addTemplate()->setTemplate('CElement/Component/FileManager/Index')->setVar('fm', $fm);
    }

   

}
