<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:36:38 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Exception_ConnectionException extends \RuntimeException implements CElastic_Exception_ExceptionInterface {

    /**
     * @var CElastic_Client_Request object
     */
    protected $_request;

    /**
     * @var CElastic_Client_Response Response object
     */
    protected $_response;

    /**
     * Construct Exception.
     *
     * @param string                    $message  Message
     * @param CElastic_Client_Request   $request
     * @param CElastic_Client_Response  $response
     */
    public function __construct($message, CElastic_Client_Request $request = null, CElastic_Client_Response $response = null) {
        $this->_request = $request;
        $this->_response = $response;
        parent::__construct($message);
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

}
