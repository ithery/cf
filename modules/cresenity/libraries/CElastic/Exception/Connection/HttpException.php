<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:50:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Connection exception.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 */
class CElastic_Exception_Connection_HttpException extends CElastic_Exception_ConnectionException {

    /**
     * Error code / message.
     *
     * @var int|string Error code / message
     */
    protected $_error = 0;

    /**
     * Construct Exception.
     *
     * @param int|string         $error    Error
     * @param CElastic_Client_Request  $request
     * @param CElastic_Client_Response $response
     */
    public function __construct($error, CElastic_Client_Request $request = null, CElastic_Client_Response $response = null) {
        $this->_error = $error;
        $message = $this->getErrorMessage($this->getError());
        parent::__construct($message, $request, $response);
    }

    /**
     * Returns the error message corresponding to the error code
     * cUrl error code reference can be found here {@link http://curl.haxx.se/libcurl/c/libcurl-errors.html}.
     *
     * @param string $error Error code
     *
     * @return string Error message
     */
    public function getErrorMessage($error) {
        switch ($error) {
            case CURLE_UNSUPPORTED_PROTOCOL:
                return 'Unsupported protocol';
            case CURLE_FAILED_INIT:
                return 'Internal cUrl error?';
            case CURLE_URL_MALFORMAT:
                return 'Malformed URL';
            case CURLE_COULDNT_RESOLVE_PROXY:
                return "Couldn't resolve proxy";
            case CURLE_COULDNT_RESOLVE_HOST:
                return "Couldn't resolve host";
            case CURLE_COULDNT_CONNECT:
                return "Couldn't connect to host, Elasticsearch down?";
            case 28:
                return 'Operation timed out';
        }
        return 'Unknown error:' . $error;
    }

    /**
     * Return Error code / message.
     *
     * @return string Error code / message
     */
    public function getError() {
        return $this->_error;
    }

}
