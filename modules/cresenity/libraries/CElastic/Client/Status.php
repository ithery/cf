<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 9:24:36 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Elasticsearch\Endpoints\Indices\Alias\Get;
use Elasticsearch\Endpoints\Indices\Stats;

/**
 * Elastica general status.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-status.html
 */
class CElastic_Client_Status {

    /**
     * Contains all status infos.
     *
     * @var CElastic_Client_Response Response object
     */
    protected $_response;

    /**
     * Data.
     *
     * @var array Data
     */
    protected $_data;

    /**
     * Client object.
     *
     * @var CElastic_Client Client object
     */
    protected $_client;

    /**
     * Constructs Status object.
     *
     * @param CElastic_Client $client Client object
     */
    public function __construct(CElastic_Client $client) {
        $this->_client = $client;
    }

    /**
     * Returns status data.
     *
     * @return array Status data
     */
    public function getData() {
        if (is_null($this->_data)) {
            $this->refresh();
        }
        return $this->_data;
    }

    /**
     * Returns a list of the existing index names.
     *
     * @return array Index names list
     */
    public function getIndexNames() {
        $data = $this->getData();
        return array_keys($data['indices']);
    }

    /**
     * Checks if the given index exists.
     *
     * @param string $name Index name to check
     *
     * @return bool True if index exists
     */
    public function indexExists($name) {
        return in_array($name, $this->getIndexNames());
    }

    /**
     * Checks if the given alias exists.
     *
     * @param string $name Alias name
     *
     * @return bool True if alias exists
     */
    public function aliasExists($name) {
        return count($this->getIndicesWithAlias($name)) > 0;
    }

    /**
     * Returns an array with all indices that the given alias name points to.
     *
     * @param string $alias Alias name
     *
     * @return array|CElastic_Index[] List of CElastic_Index
     */
    public function getIndicesWithAlias($alias) {
        $endpoint = new Get();
        $endpoint->setName($alias);
        $response = null;
        try {
            $response = $this->_client->requestEndpoint($endpoint);
        } catch (CElastic_Exception_ResponseException $e) {
            // 404 means the index alias doesn't exist which means no indexes have it.
            if ($e->getResponse()->getStatus() === 404) {
                return [];
            }
            // If we don't have a 404 then this is still unexpected so rethrow the exception.
            throw $e;
        }
        $indices = [];
        foreach ($response->getData() as $name => $unused) {
            $indices[] = new CElastic_Index($this->_client, $name);
        }
        return $indices;
    }

    /**
     * Returns response object.
     *
     * @return CElastic_Client_Response Response object
     */
    public function getResponse() {
        if (is_null($this->_response)) {
            $this->refresh();
        }
        return $this->_response;
    }

    /**
     * Return shards info.
     *
     * @return array Shards info
     */
    public function getShards() {
        $data = $this->getData();
        if (isset($data['_shards'])) {
            return $data['_shards'];
        }
        if (isset($data['shards'])) {
            return $data['shards'];
        }
        return array();
    }

    /**
     * Refresh status object.
     */
    public function refresh() {
        $this->_response = $this->_client->requestEndpoint(new Stats());
        $this->_data = $this->getResponse()->getData();
    }

}
