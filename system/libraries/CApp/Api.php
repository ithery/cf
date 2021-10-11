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
     * @return array
     */
    public function exec(...$methods) {
        //locate file method
        $response = [];
        /**
         * @var CApp_Api_Method Description
         */
        $method = implode('_', $methods);
        $className = $method;
        if (!cstr::startsWith($method, 'CApp_Api_Method_')) {
            $className = 'CApp_Api_Method_' . $method;
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
