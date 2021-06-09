<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * CGeo_Location_Ellipsoid
 *
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 7:36:05 PM
 */
class CGeo_Location_Ellipsoid {
    /**
     * @var string
     */
    protected $name;

    /**
     * The semi-major axis
     *
     * @var float
     */
    protected $a;

    /**
     * The Inverse Flattening (1/f)
     *
     * @var float
     */
    protected $f;

    /**
     * Some often used ellipsoids
     *
     * @var array
     */
    protected static $configs = [
        'WGS-84' => [
            'name' => 'World Geodetic System  1984',
            'a' => 6378137.0,
            'f' => 298.257223563,
        ],
        'GRS-80' => [
            'name' => 'Geodetic Reference System 1980',
            'a' => 6378137.0,
            'f' => 298.257222100,
        ],
    ];

    /**
     * @param string $name
     * @param float  $a
     * @param float  $f
     */
    public function __construct($name, $a, $f) {
        $this->name = $name;
        $this->a = $a;
        $this->f = $f;
    }

    /**
     * @param string $name
     *
     * @return CGeo_Location_Ellipsoid
     */
    public static function createDefault($name = 'WGS-84') {
        return static::createFromArray(static::$configs[$name]);
    }

    /**
     * @param array $config
     *
     * @return CGeo_Location_Ellipsoid
     */
    public static function createFromArray(array $config) {
        return new static($config['name'], $config['a'], $config['f']);
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getA() {
        return $this->a;
    }

    /**
     * Calculation of the semi-minor axis
     *
     * @return float
     */
    public function getB() {
        return $this->a * (1 - 1 / $this->f);
    }

    /**
     * @return float
     */
    public function getF() {
        return $this->f;
    }

    /**
     * Calculates the arithmetic mean radius
     *
     * @see http://home.online.no/~sigurdhu/WGS84_Eng.html
     *
     * @return float
     */
    public function getArithmeticMeanRadius() {
        return $this->a * (1 - 1 / $this->f / 3);
    }
}
