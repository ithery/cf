<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api {
    protected $domain = null;

    private static $instance = [];

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

        $invalidClass = [
            CApp_Api_Method_Server::class,
        ];

        if (!in_array($className, $invalidClass) && class_exists($className)) {
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
