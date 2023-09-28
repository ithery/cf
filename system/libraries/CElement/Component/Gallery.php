<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 20, 2018, 2:23:56 AM
 */
class CElement_Component_Gallery extends CElement_Component {
    public function __construct($id = '', $tag = 'div') {
        parent::__construct($id, $tag);

        $this->tag = 'div';
    }

    /**
     * @return CElement_Component_Gallery_Item
     */
    public function addItem() {
        $item = new CElement_Component_Gallery_Item();
        $this->add($item);

        return $item;
    }

    protected function build() {
        $this->addClass('cres:element:component:Gallery');
        $this->setAttr('cres-element', 'component:Gallery');
        $config = [
            'thumbnail' => true,
            'selector' => '#' . $this->id . ' .cres-gallery-item',
        ];

        $this->setAttr('cres-config', c::json($config));
    }
}
