<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 11:18:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Responsive {

    protected $hiddenPhone;
    protected $hiddenTablet;
    protected $hiddenDesktop;

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setHiddenPhone($bool) {
        $this->hiddenPhone = $bool;
        return $this;
    }

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setHiddenTablet($bool) {
        $this->hiddenTablet = $bool;
        return $this;
    }

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setHiddenDesktop($bool) {
        $this->hiddenDesktop = $bool;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getHiddenPhone() {
        return $this->hiddenPhone;
    }

    /**
     * 
     * @return bool
     */
    public function getHiddenTablet() {
        return $this->hiddenTablet;
    }

    /**
     * 
     * @return bool
     */
    public function getHiddenDesktop() {
        return $this->hiddenDesktop;
    }

}
