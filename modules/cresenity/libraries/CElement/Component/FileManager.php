<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 28, 2019, 1:41:33 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_FileManager extends CElement_Component {

    public function __construct($id = "") {
        parent::__construct($id);

        $this->tag = 'div';
    }

    public static function factory($id = "") {
        return new CElement_Component_FileManager($id);
    }

    public function build() {
        CManager::instance()->asset()->module()->registerRunTimeModule('jquery-ui-1.12.1.custom');
        CManager::instance()->asset()->module()->registerRunTimeModule('plupload');
        CManager::instance()->asset()->module()->registerRunTimeModule('jquery.filemanager');
    }

    public function js($indent = 0) {
        $js = "
            $('#" . $this->id() . "').fileManager({ajaxPath:'" . curl::base() . "cresenity/connector/filemanager',upload:true});
        ";
        return $js;
    }

}
