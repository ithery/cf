<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 14, 2018, 4:40:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api {

    private static $instance = array();
    protected $domain = null;

    protected function __construct($domain) {
        $this->domain = $domain;
    }

    /**
     * 
     * @param int $org_id
     * @return CMApi
     */
    public static function instance($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        if (self::$instance == null) {
            self::$instance = array();
        }
        if (self::$instance == null) {
            self::$instance[$domain] = new static($domain);
        }
        return self::$instance[$domain];
    }

    /**
     * 
     * @return string domain accessed
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * 
     * @param string $method
     * @return array
     */
    public function exec($method, $submethod = null) {

        //locate file method
        $response = array();
        /**
         * @var CMApi_Method Description
         */
        $className = 'CApp_Api_Method_' . $method;
        if ($submethod != null) {
            $className = 'CApp_Api_Method_' . $method . '_' . $submethod;
        }

        
        $logger = null;
        if (class_exists($className)) {
            $methodObject = new $className($this, $method);
            //$logger = new CApp_Api_Logger($methodObject->sessionId());
            //$logger->logRequest($method, $methodObject->request());
            if ($methodObject->getErrCode() == 0) {
                $methodObject->execute();
            }
            $response = $methodObject->result();
        } else {
            $response = array(
                'errCode' => '11',
                'errMessage' => 'Class not found',
            );
        }
        //if ($logger != null) {
        //$logger->logResponse($method, $response);
        //}




        return $response;
    }

}
