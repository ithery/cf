<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 20, 2018, 2:23:56 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Icon extends CElement_Component {

    use CTrait_Element_Property_Icon;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->icon = "";
        $this->tag = 'i';
    }

    public function build() {
        $icon = $this->icon;
        if (strpos($icon, 'fa-') === false && strpos($icon, 'ion-') === false && strpos($icon, 'icon-') === false) {
            $icon = 'icon-' . $icon;
        }
        $this->addClass($icon);
    }

}
