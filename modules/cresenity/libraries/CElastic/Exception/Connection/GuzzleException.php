<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:51:29 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use GuzzleHttp\Exception\TransferException;

/**
 * Transport exception.
 *
 * @author Milan Magudia <milan@magudia.com>
 */
class CElastic_Exception_Connection_GuzzleException extends CElastic_Exception_ConnectionException {

    /**
     * @var TransferException
     */
    protected $_guzzleException;

    /**
     * @param \GuzzleHttp\Exception\TransferException $guzzleException
     * @param CElastic_Client_Request                       $request
     * @param CElastic_Client_Response                      $response
     */
    public function __construct(TransferException $guzzleException, CElastic_Client_Request $request = null, CElastic_Client_Response $response = null) {
        $this->_guzzleException = $guzzleException;
        $message = $this->getErrorMessage($this->getGuzzleException());
        parent::__construct($message, $request, $response);
    }

    /**
     * @param \GuzzleHttp\Exception\TransferException $guzzleException
     *
     * @return string
     */
    public function getErrorMessage(TransferException $guzzleException) {
        return $guzzleException->getMessage();
    }

    /**
     * @return TransferException
     */
    public function getGuzzleException() {
        return $this->_guzzleException;
    }

}
