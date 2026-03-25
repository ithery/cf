<?php

defined('SYSPATH') or die('No direct access allowed.');

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
     * @return null|string
     */
    public function getLocale();

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param string     $name
     * @param null|mixed $default
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
