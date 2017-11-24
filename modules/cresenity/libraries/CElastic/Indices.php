<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 18, 2017, 9:05:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Indices {

    protected $index;
    protected $document_type;

    /*
     * @var Elasticsearch\Client
     */
    protected $elastic;
    protected $client;
    protected $mappings;
    protected $settings;

    public function __construct(CElastic $elastic, $index, $document_type) {
        $this->elastic = $elastic;
        $this->client = $elastic->client();
        $this->index = $index;
        $this->document_type = $document_type;
        $this->mappings = array();
        $this->settings = array();
    }

    public function add_mapping($document_type, $field_name, $type) {
        $properties = array();
        if (isset($this->mappings[$document_type]["properties"])) {
            $properties = $this->mappings[$document_type]["properties"];
        }
        $properties[$field_name] = array(
            'type' => $type
        );
        $this->mappings[$document_type]["properties"] = $properties;
    }

    public function add_setting($key, $value) {
        $this->settings[$key] = $value;
    }

    public function create() {
        $params = array();
        $params['index'] = $this->index;
        
        //build the body
        $body = array();
        if(count($this->settings) > 0) {
            $body['settings'] = $this->settings;
        }
        if (count($this->mappings) > 0) {
            $body['mappings'] = $this->mappings;
        }

        $params['body'] = $body;

        try {
            $this->client->indices()->create($params);
        } catch(Exception $e) {
            throw new Exception($this->index . " Index is Exists", 1);
        }
    }

    public function put_mapping() {
        $params = array();
        $params['index'] = $this->index;
        if(strlen($this->document_type) > 0) {
            $params['type'] = $this->document_type;
        }
        
        //build the body
        if (count($this->mappings) > 0) {
            $params['body'] = $this->mappings;
        }

        try {
            $this->client->indices()->putMapping($params);
        } catch(Exception $e) {
            throw new Exception($this->index . " " . $this->document_type . " Failed", 1);
        }
    }

    public function get_mapping() {
        $params = array();
        $params['index'] = $this->index;
        if(strlen($this->document_type) > 0) {
            $params['type'] = $this->document_type;
        }

        $result;
        try {
            $result = $this->client->indices()->getMapping($params);
        } catch(Exception $e) {
            throw new Exception($this->index . " " . $this->document_type . " Not Found", 1);
        }

        return $result;
    }
}
