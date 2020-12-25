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
        if (strpos($icon, 'fa-') === false && strpos($icon, 'ion-') === false) {
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
}
