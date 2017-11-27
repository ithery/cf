<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 18, 2017, 6:46:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic {

    // Elastic instances
    public static $instances = array();
    //domain of instance
    protected $domain;
    //name of instance
    protected $name;
    //config of instance
    protected $config;
    //hosts of instance
    protected $hosts;
    //Client of instance
    protected $client;

    /**
     * Returns a singleton instance of CElastic.
     *
     * @param   mixed   configuration array or DSN
     * @return  CElastic
     */
    public static function & instance($domain = '', $name = 'default', $config = NULL) {
        if (strlen($domain) == 0) {
            //get current domain
            $domain = CF::domain();
        }
        if (!isset(CElastic::$instances[$domain])) {
            CElastic::$instances[$domain] = array();
        }
        if (!isset(CElastic::$instances[$domain][$name])) {
            // Create a new instance
            CElastic::$instances[$domain][$name] = new CElastic($config === NULL ? $name : $config, $domain);
        }

        return CElastic::$instances[$domain][$name];
    }

    public function config() {
        return $this->config;
    }

    /**
     * Returns the name of a given database instance.
     *
     * @param   CDatabase  instance of CDatabase
     * @return  string
     */
    public static function instance_name(CElastic $el, $domain = null) {
        if (strlen($domain) == 0) {
            //get current domain
            $domain = CF::domain();
        }
        return array_search($db, CDatabase::$instances[$domain], TRUE);
    }

    /**
     * Sets up the elastic configuration.
     *
     * @throws  CElastic_Exception
     */
    public function __construct($config = array(), $domain = null) {

        if ($domain == null) {
            $domain = CF::domain();
        }
        $load_config = true;

        if (!empty($config)) {
            if (is_array($config) AND count($config) > 0) {
                if (!array_key_exists('connection', $config)) {
                    $config = array('connection' => $config);
                    $load_config = false;
                } else {
                    $load_config = false;
                }
            }
            if (is_string($config)) {
                if (strpos($config, '://') !== FALSE) {
                    $config = array('connection' => $config);
                    $load_config = false;
                }
            }
        }

        if ($load_config) {
            $all_config = CF::config('elastic');

            $found = false;
            $config_name = 'default';
            if (is_string($config)) {
                $config_name = $config;
            }

            if (isset($all_config[$config_name])) {
                $config = $all_config[$config_name];
                $found = true;
            }

            if ($found == false) {
                throw new Exception('Config ' . $config_name . ' Not Found');
            }
        }

        $this->config = $config;

        $this->hosts = carr::path($this->config, 'connection.hosts');


        $client_builder = Elasticsearch\ClientBuilder::create();
        $client_builder->setHosts($this->hosts);
        $this->client = $client_builder->build();

        CF::log(CLogger::DEBUG, 'Elastic Library initialized');
    }

    /**
     * 
     * @param string $index
     * @param string $document_type
     * @return \CElastic_Search
     */
    public function search($index, $document_type = '') {
        return new CElastic_Search($this, $index, $document_type);
    }

    /**
     * 
     * @param string $index
     * @param string $document_type
     * @return \CElastic_Indices
     */
    public function indices($index, $document_type = "") {
        return new CElastic_Indices($this, $index, $document_type);
    }

    /**
     * 
     * @param string $index
     * @param string $document_type
     * @return \CElastic_Document
     */
    public function document($index, $document_type) {
        return new CElastic_Document($this, $index, $document_type);
    }

    /**
     * 
     * @return Elasticsearch\Client
     */
    public function &client() {
        return $this->client;
    }

}
