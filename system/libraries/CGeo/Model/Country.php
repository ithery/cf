<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * A Country has either a name or a code. A Country will never be without data.
 */
final class CGeo_Model_Country {
    /**
     * @var null|string
     */
    private $name;

    /**
     * @var null|string
     */
    private $code;

    /**
     * @param string $name
     * @param string $code
     */
    public function __construct($name = null, $code = null) {
        if (null === $name && null === $code) {
            throw new CGeo_Exception_InvalidArgument('A country must have either a name or a code');
        }
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * Returns the country name.
     *
     * @return null|string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the country ISO code.
     *
     * @return null|string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Returns a string with the country name.
     *
     * @return string
     */
    public function __toString() {
        return $this->getName() ? $this->getName() : '';
    }
}
