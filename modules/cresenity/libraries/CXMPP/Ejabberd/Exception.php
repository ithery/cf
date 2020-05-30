<?php

/**
 * Description of Exception
 * 
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since May 30, 2020 
 * @license Ittron Global Teknologi
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CXMPP_Ejabberd_Exception extends Exception {

    public function __construct($message = "", $code = 0,  $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public static function networkException(GuzzleException $exception) {
        return new CXMPP_Ejabberd_Exception('Network exception', $exception->getCode(), $exception);
    }

    public static function generalException(Exception $exception) {
        return new CXMPP_Ejabberd_Exception('An error occurred', $exception->getCode(), $exception);
    }

}
