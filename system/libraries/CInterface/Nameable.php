<?php

defined('SYSPATH') or die('No direct access allowed.');

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
