<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_Icon extends CElement_Component {
    use CTrait_Element_Property_Icon;

    /**
     * @param string $id
     * @param string $tag
     *
     * @return void
     */
    public function __construct($id = '', $tag = 'div') {
        parent::__construct($id, $tag);
        $this->icon = '';
        $this->tag = 'i';
    }

    /**
     * @return void
     */
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
        if (strpos($icon, 'fa-') !== false || strpos($icon, 'ion-') !== false || strpos($icon, 'ti-') !== false || strpos($icon, 'lnr') !== false || strpos($icon, 'pe-') !== false) {
            $this->addClass($icon);
        } else {
            $iconPrefix = c::theme('icon.prefix', 'icon icon-');
            $this->addClass($iconPrefix . $icon);
        }
    }
}
