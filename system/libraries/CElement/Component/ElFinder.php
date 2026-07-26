<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_ElFinder extends CElement_Component {
    /**
     * @var bool|string
     */
    private $connectorUrl = false;

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id = '') {
        parent::__construct($id);

        $this->tag = 'div';
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_ElFinder
     */
    public static function factory($id = '') {
        return new CElement_Component_ElFinder($id);
    }

    /**
     * @return void
     */
    public function build() {
        CManager::instance()->asset()->module()->registerRunTimeModule('jquery.ui');
        CManager::instance()->asset()->module()->registerRunTimeModule('elfinder');
    }

    /**
     * @param string $connectorUrl
     *
     * @return void
     */
    public function setConnectorUrl($connectorUrl) {
        $this->connectorUrl = $connectorUrl;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $options = [];
        $contextMenu = [
            'cwd' => ['reload', 'upload', 'sort'],
            'files' => ['download', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'edit', 'rename', '|', 'info'],
        ];
        $options['requesttype'] = 'post';
        $options['url'] = $this->connectorUrl;
        $options['ui'] = ['stat'];
        $options['contextmenu'] = $contextMenu;

        $js = "
            jQuery('#" . $this->id() . "').elfinder(" . json_encode($options) . ');
        ';

        return $js;
    }
}
