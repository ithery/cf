<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 11:18:45 PM
 */
trait CTrait_Element_Responsive {
    protected $hiddenPhone;

    protected $hiddenTablet;

    protected $hiddenDesktop;

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setHiddenPhone($bool = true) {
        $this->hiddenPhone = $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setHiddenTablet($bool = true) {
        $this->hiddenTablet = $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setHiddenDesktop($bool = true) {
        $this->hiddenDesktop = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHiddenPhone() {
        return $this->hiddenPhone;
    }

    /**
     * @return bool
     */
    public function getHiddenTablet() {
        return $this->hiddenTablet;
    }

    /**
     * @return bool
     */
    public function getHiddenDesktop() {
        return $this->hiddenDesktop;
    }
}
