<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 9:15:04 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * A Country has either a name or a code. A Country will never be without data.
 */
final class CGeo_Model_Country {

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
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
     * @return string|null
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the country ISO code.
     *
     * @return string|null
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
