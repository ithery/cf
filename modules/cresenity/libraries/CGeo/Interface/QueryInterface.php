<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 9:05:03 PM
 */
interface CGeo_Interface_QueryInterface {
    /**
     * @param string $locale
     *
     * @return CGeo_Interface_QueryInterface
     */
    public function withLocale($locale);

    /**
     * @param int $limit
     *
     * @return CGeo_Interface_QueryInterface
     */
    public function withLimit($limit);

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return CGeo_Interface_QueryInterface
     */
    public function withData($name, $value);

    /**
     * @return string|null
     */
    public function getLocale();

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getData($name, $default = null);

    /**
     * @return array
     */
    public function getAllData();

    /**
     * @return string
     */
    public function __toString();
}
