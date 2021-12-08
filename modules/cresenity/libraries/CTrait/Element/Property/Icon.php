<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 5:18:23 AM
 */
trait CTrait_Element_Property_Icon {
    protected $icon;

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon) {
        if (strpos($icon, 'fa-') === false && strpos($icon, 'ion-') === false && strpos($icon, 'ti-') === false) {
            $icon = $icon . ' icon-' . $icon;
        }

        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    public function getIconHtml() {
        if (strlen($this->icon) == 0) {
            return '';
        }

        if ($icon = c::manager()->icon()->loadFile($this->icon)) {
            $component = new CView_Component_IconComponent($this->icon);

            $icon = $component->render()->toHtml();

            return '<i class="capp-icon">' . $icon . '</i> ';
        }
        $iconPrefix = c::theme('icon.prefix', 'icon icon-');

        return '<i class="capp-icon ' . $iconPrefix . $this->getIcon() . ' ' . $this->getIcon() . '"></i> ';
    }
}
