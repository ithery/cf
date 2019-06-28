<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 7:06:52 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CApp_Cloud_AdapterInterface {

    /**
     * @param string $url
     *
     * @throws HttpException
     *
     * @return string
     */
    public function get($url);

    /**
     * @param string $url
     *
     * @throws HttpException
     */
    public function delete($url);

    /**
     * @param string       $url
     * @param array|string $content
     *
     * @throws HttpException
     *
     * @return string
     */
    public function put($url, $content = '');

    /**
     * @param string       $url
     * @param array|string $content
     *
     * @throws HttpException
     *
     * @return string
     */
    public function post($url, $content = '');

    /**
     * @return array|null
     */
    public function getLatestResponseHeaders();
}
