<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:30:47 AM
 */
trait CTrait_Element_Behavior_Closeable {
    /**
     * @var bool
     */
    protected $closeable;

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setCloseable($bool) {
        $this->closeable = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function getCloseable() {
        return $this->closeable;
    }

    /**
     * @return bool
     */
    public function isCloseable() {
        return $this->closeable == true;
    }
}
