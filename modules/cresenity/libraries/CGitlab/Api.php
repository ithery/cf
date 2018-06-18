<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 4:31:53 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Abstract class for Api classes
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Matt Humphrey <matt@m4tt.co>
 * @author Radu Topala <radu.topala@trisoft.ro>
 */
abstract class CGitlab_Api implements CGitlab_ApiInterface {

    /**
     * Default entries per page
     */
    const PER_PAGE = 10000;

    /**
     * The client
     *
     * @var CGitlab_Client
     */
    protected $client;

    /**
     * @param CGitlab_Client $client
     */
    public function __construct(CGitlab_Client $client) {
        $this->client = $client;
    }

    /**
     * @return $this
     * @codeCoverageIgnore
     */
    public function configure() {
        return $this;
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function get($path, array $parameters = array(), $requestHeaders = array()) {
        $response = $this->client->get($path, $parameters, $requestHeaders);
        return $response;
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @param array $files
     * @return mixed
     */
    protected function post($path, array $parameters = array(), $requestHeaders = array(), array $files = array()) {
        $response = $this->client->post($path, $parameters, $requestHeaders, $files);
        return $response;
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function patch($path, array $parameters = array(), $requestHeaders = array()) {
        $response = $this->client->patch($path, $parameters, $requestHeaders);
        return $response;
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function put($path, array $parameters = array(), $requestHeaders = array()) {
        $response = $this->client->put($path, $parameters, $requestHeaders);
        return $response;
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function delete($path, array $parameters = array(), $requestHeaders = array()) {
        $response = $this->client->delete($path, $parameters, $requestHeaders);
        return $response;
    }

    /**
     * @param int $id
     * @param string $path
     * @return string
     */
    protected function getProjectPath($id, $path) {
        return 'projects/' . $this->encodePath($id) . '/' . $path;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function encodePath($path) {
        $path = rawurlencode($path);
        return str_replace('.', '%2E', $path);
    }

}
