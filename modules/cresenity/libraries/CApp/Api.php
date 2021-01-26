<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 14, 2018, 4:40:47 AM
 */
class CApp_Api {
    private static $instance = [];

    protected $domain = null;

    protected function __construct($domain) {
        $this->domain = $domain;
    }

    /**
     * @param null|mixed $domain
     *
     * @return CApp_Api
     */
    public static function instance($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        if (self::$instance == null) {
            self::$instance = [];
        }
        if (self::$instance == null) {
            self::$instance[$domain] = new static($domain);
        }
        return self::$instance[$domain];
    }

    /**
     * @return string domain accessed
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * @param string     $method
     * @param null|mixed $submethod
     *
     * @return array
     */
    public function exec($method, $submethod = null) {
        //locate file method
        $response = [];
        /**
         * @var CApp_Api_Method Description
         */
        $className = 'CApp_Api_Method_' . $method;
        if ($submethod != null) {
            $className = 'CApp_Api_Method_' . $method . '_' . $submethod;
        }

        $logger = null;
        if (class_exists($className)) {
            $methodObject = new $className($this, $method);

            if ($methodObject->getErrCode() == 0) {
                $methodObject->execute();
            }
            $response = $methodObject->result();
        } else {
            $response = [
                'errCode' => '11',
                'errMessage' => 'Class not found',
            ];
        }

        return $response;
    }
}
