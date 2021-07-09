<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 7, 2018, 8:12:50 PM
 */
interface CInterface_Nameable {
    /**
     * Retrieve the name of this object.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the name of this object.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);
}
