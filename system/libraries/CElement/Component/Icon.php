<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 20, 2018, 2:23:56 AM
 */
class CElement_Component_Icon extends CElement_Component {
    use CTrait_Element_Property_Icon;

    public function __construct($id = '', $tag = 'div') {
        parent::__construct($id, $tag);
        $this->icon = '';
        $this->tag = 'i';
    }

    public function build() {
        $icon = $this->icon;

        $this->addClass($icon);
    }
}
