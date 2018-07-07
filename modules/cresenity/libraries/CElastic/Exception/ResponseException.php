<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:55:20 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Exception_ResponseException extends \RuntimeException implements CElastic_Exception_ExceptionInterface {

    /**
     * @var CElastic_Client_Request Request object
     */
    protected $_request;

    /**
     * @var CElastic_Client_Response Response object
     */
    protected $_response;

    /**
     * Construct Exception.
     *
     * @param \Elastica\Request  $request
     * @param \Elastica\Response $response
     */
    public function __construct(Request $request, Response $response) {
        $this->_request = $request;
        $this->_response = $response;
        parent::__construct($response->getErrorMessage());
    }

    /**
     * Returns request object.
     *
     * @return CElastic_Client_Request Request object
     */
    public function getRequest() {
        return $this->_request;
    }

    /**
     * Returns response object.
     *
     * @return CElastic_Client_Response Response object
     */
    public function getResponse() {
        return $this->_response;
    }

    /**
     * Returns elasticsearch exception.
     *
     * @return ElasticsearchException
     */
    public function getElasticsearchException() {
        $response = $this->getResponse();
        return new ElasticsearchException($response->getStatus(), $response->getErrorMessage());
    }

}
