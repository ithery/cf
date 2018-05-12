<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:30:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Behavior_Closeable {

    /**
     *
     * @var bool
     */
    protected $closeable;

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setCloseable($bool) {
        $this->closeable = true;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getCloseable() {
        return $this->closeable;
    }

    /**
     * 
     * @return bool
     */
    public function isCloseable() {
        return $this->closeable == true;
    }

}
