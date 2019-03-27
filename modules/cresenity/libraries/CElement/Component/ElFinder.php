<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 27, 2019, 12:44:24 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_ElFinder extends CElement_Component {

    public function __construct($id = "") {
        parent::__construct($id);

        $this->tag = 'div';
    }
    public static function factory($id = "") {
        return new CElement_Component_ElFinder($id);
    }
    
    public function build() {
        CManager::instance()->asset()->module()->registerRunTimeModule('jquery-ui-1.12.1.custom');
        CManager::instance()->asset()->module()->registerRunTimeModule('elfinder');
    }

    public function js($indent = 0) {
        $js = "
            var elf = jQuery('#".$this->id()."').elfinder({
			requesttype: 'post',
			url : '/base/backend/my_elFinderConnector.php'
            }).elfinder('instance');
                ";
        return $js;
    }

}
