<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 5:18:23 AM
 */
trait CTrait_Element_Property_Height {
    /**
     * @var int|string
     */
    protected $height;

    /**
     * @param int|string $height
     *
     * @return $this
     */
    public function setHeight($height) {
        $this->height = $height;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getHeight() {
        return $this->height;
    }
}
