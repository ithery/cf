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
        $this->addClass('capp-icon');
        if (strpos($this->originalIcon, '.') !== false) {
            if ($icon = c::manager()->icon()->loadFile($this->originalIcon)) {
                $component = new CView_Component_IconComponent($this->originalIcon);

                $icon = $component->render()->toHtml();
                $this->setAttr('data-icon', $this->originalIcon);
                $this->add($icon);

                return;
            }
        }
        $iconPrefix = c::theme('icon.prefix', 'icon icon-');

        $this->addClass($iconPrefix . $icon);
    }
}
