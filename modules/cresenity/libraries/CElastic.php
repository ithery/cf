<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 18, 2017, 6:46:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use ONGR\ElasticsearchDSL\Search as DSLQuery;

class CElastic {

    use CTrait_Compat_Elastic;

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
     * @var CElastic_Manager_Indices
     */
    protected $indicesManager;

    /**
     * Elastic Search default index.
     *
     * @var string
     */
    protected $index;

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
            $allConfig = CF::config('elastic');

            $found = false;
            $configName = 'default';
            if (is_string($config)) {
                $configName = $config;
            }

            if (isset($allConfig[$configName])) {
                $config = $allConfig[$configName];
                $found = true;
            }

            if ($found == false) {
                throw new CException('Config :config_name Not Found', array(':config_name' => $configName));
            }
        }

        $this->config = $config;

        $this->hosts = carr::path($this->config, 'connection.hosts');


        $clientBuilder = Elasticsearch\ClientBuilder::create();
        $clientBuilder->setHosts($this->hosts);
        $this->client = $clientBuilder->build();

        CF::log(CLogger::DEBUG, 'Elastic Library initialized');
    }

    /**
     * @return CElastic_Manager_Indices
     */
    public function indicesManager() {
        if (!$this->indicesManager) {
            $this->indicesManager = new CElastic_Manager_Indices($this);
        }
        return $this->indicesManager;
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
     * Execute a map statement on index;.
     *
     * @param array $search
     *
     * @return array
     */
    public function searchStatement(array $search) {
        return $this->client->search($this->setStatementIndex($search));
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

    /**
     * Get DSL grammar instance for this connection.
     *
     * @return DSLGrammar
     */
    public function getDSLQuery() {
        return new DSLQuery();
    }

    /**
     * Get the default elastic index.
     *
     * @return string
     */
    public function getDefaultIndex() {
        return $this->index;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function setStatementIndex(array $params) {
        if (isset($params['index']) and $params['index']) {
            return $params;
        }

        // merge the default index with the given params if the index is not set.
        return array_merge($params, ['index' => $this->getDefaultIndex()]);
    }

    /**
     * 
     * @return \CElastic_Client
     */
    public function createClient() {
        $config = array();
        $config['servers'] = array();
        foreach ($this->hosts as $host) {
            $hostArray = explode(':', $host);
            $config['servers'][] = array(
                'host' => carr::get($hostArray, 0),
                'port' => carr::get($hostArray, 1, '9200'),
            );
        }

        return new CElastic_Client($config);
    }

}
