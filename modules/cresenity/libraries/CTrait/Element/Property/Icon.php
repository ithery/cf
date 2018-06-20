<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:18:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Icon {

    protected $icon;

    /**
     * 
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon) {
        
        if (strpos($icon, 'fa-') === false && strpos($icon, 'ion-') === false) {
            $icon = $icon.' icon-' . $icon;
        }
       
        $this->icon = $icon;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getIcon() {
         
        return $this->icon;
    }

}
