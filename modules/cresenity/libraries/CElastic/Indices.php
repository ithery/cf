<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Nov 18, 2017, 9:05:59 PM
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
        $this->mappings = [];
        $this->settings = [];
    }

    public function addMapping($document_type, $field_name, $type, $fielddata = true) {
        $properties = [];
        if (isset($this->mappings[$document_type]['properties'])) {
            $properties = $this->mappings[$document_type]['properties'];
        }
        $properties[$field_name] = [
            'type' => $type,
            'fielddata' => $fielddata
        ];
        $this->mappings[$document_type]['properties'] = $properties;
    }

    public function addSetting($key, $value) {
        $this->settings[$key] = $value;
    }

    public function create() {
        $params = [];
        $params['index'] = $this->index;

        //build the body
        $body = [];
        if (count($this->settings) > 0) {
            $body['settings'] = $this->settings;
        }
        if (count($this->mappings) > 0) {
            $body['mappings'] = $this->mappings;
        }

        $params['body'] = $body;

        if ($this->exists() != 1) {
            try {
                $this->client->indices()->create($params);
            } catch (Exception $e) {
                throw new Exception($this->index . '-' . $e->getMessage(), 1);
            }
        } else {
            throw new Exception($this->index . ' - Index is Exists', 1);
        }
    }

    public function putMapping() {
        $params = [];
        $params['index'] = $this->index;
        if (strlen($this->document_type) > 0) {
            $params['type'] = $this->document_type;
        }

        //build the body
        if (count($this->mappings) > 0) {
            $params['body'] = $this->mappings;
        }

        if ($this->exists() == 1) {
            try {
                $this->client->indices()->putMapping($params);
            } catch (Exception $e) {
                throw new Exception($this->index . ' ' . $this->document_type . '-' . $e->getMessage(), 1);
            }
        } else {
            throw new Exception($this->index . ' - Index Not Found', 1);
        }
    }

    public function putSetting() {
        $params = [];
        $params['index'] = $this->index;

        //build the body
        $body = [];
        if (count($this->settings) > 0) {
            $body['settings'] = $this->settings;
        }

        $params['body'] = $body;

        if ($this->exists() == 1) {
            try {
                $this->client->indices()->putSettings($params);
            } catch (Exception $e) {
                throw new Exception($this->index . '-' . $e->getMessage(), 1);
            }
        } else {
            throw new Exception($this->index . ' - Index Not Found', 1);
        }
    }

    public function getMapping() {
        $params = [];
        $params['index'] = $this->index;
        if (strlen($this->document_type) > 0) {
            $params['type'] = $this->document_type;
        }

        if ($this->exists() == 1) {
            try {
                $result = $this->client->indices()->getMapping($params);

                return $result;
            } catch (Exception $e) {
                throw new Exception($this->index . ' ' . $this->document_type . '-' . $e->getMessage(), 1);
            }
        } else {
            throw new Exception($this->index . ' - Index Not Found', 1);
        }
    }

    public function getSetting() {
        $params = [];
        $params['index'] = $this->index;

        if ($this->exists() == 1) {
            try {
                $result = $this->client->indices()->getSettings($params);

                return $result;
            } catch (Exception $e) {
                throw new Exception($this->index . '-' . $e->getMessage(), 1);
            }
        } else {
            throw new Exception($this->index . ' - Index Not Found', 1);
        }
    }

    public function delete() {
        $params = [];
        $params['index'] = $this->index;

        if ($this->exists() == 1) {
            try {
                $result = $this->client->indices()->delete($params);

                return $result;
            } catch (Exception $e) {
                throw new Exception($this->index . '-' . $e->getMessage(), 1);
            }
        } else {
            throw new Exception($this->index . ' - Index Not Found', 1);
        }
    }

    public function deleteMapping() {
        $params = [];
        $params['index'] = $this->index;
        if (strlen($this->document_type) > 0) {
            $params['type'] = $this->document_type;
        }

        if ($this->exists() == 1) {
            try {
                $result = $this->client->indices()->deleteMapping($params);

                return $result;
            } catch (Exception $e) {
                throw new Exception($this->index . '-' . $e->getMessage(), 1);
            }
        } else {
            throw new Exception($this->index . ' - Index Not Found', 1);
        }
    }

    public function exists() {
        $params = [];
        $params['index'] = $this->index;

        $result = $this->client->indices()->exists($params);

        return $result;
    }
}
