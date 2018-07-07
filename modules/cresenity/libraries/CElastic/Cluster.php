<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:29:23 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Elasticsearch\Endpoints\Cluster\State;

class CElastic_Cluster {

    /**
     * Client.
     *
     * @var CElastic_Client Client object
     */
    protected $_client;

    /**
     * Cluster state response.
     *
     * @var CElastic_Response
     */
    protected $_response;

    /**
     * Cluster state data.
     *
     * @var array
     */
    protected $_data;

    /**
     * Creates a cluster object.
     *
     * @param CElastic_Client $client Connection client object
     */
    public function __construct(CElastic_Client $client) {
        $this->_client = $client;
        $this->refresh();
    }

    /**
     * Refreshes all cluster information (state).
     */
    public function refresh() {
        $this->_response = $this->_client->requestEndpoint(new State());
        $this->_data = $this->getResponse()->getData();
    }

    /**
     * Returns the response object.
     *
     * @return \Elastica\Response Response object
     */
    public function getResponse() {
        return $this->_response;
    }

    /**
     * Return list of index names.
     *
     * @return array List of index names
     */
    public function getIndexNames() {
        return array_keys($this->_data['metadata']['indices']);
    }

    /**
     * Returns the full state of the cluster.
     *
     * @return array State array
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-state.html
     */
    public function getState() {
        return $this->_data;
    }

    /**
     * Returns a list of existing node names.
     *
     * @return array List of node names
     */
    public function getNodeNames() {
        $data = $this->getState();
        $nodeNames = [];
        foreach ($data['nodes'] as $node) {
            $nodeNames[] = $node['name'];
        }
        return $nodeNames;
    }

    /**
     * Returns all nodes of the cluster.
     *
     * @return CElastic_Node[]
     */
    public function getNodes() {
        $nodes = [];
        $data = $this->getState();
        foreach ($data['nodes'] as $id => $name) {
            $nodes[] = new CElastic_Node($id, $this->getClient());
        }
        return $nodes;
    }

    /**
     * Returns the client object.
     *
     * @return CElastic_Client Client object
     */
    public function getClient() {
        return $this->_client;
    }

    /**
     * Returns the cluster information (not implemented yet).
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-nodes-info.html
     *
     * @param array $args Additional arguments
     *
     * @throws CElastic_Exception_NotImplementedException
     */
    public function getInfo(array $args) {
        throw new CElastic_Exception_NotImplementedException('not implemented yet');
    }

    /**
     * Return Cluster health.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-health.html
     *
     * @return CElastic_Cluster_Health
     */
    public function getHealth() {
        return new CElastic_Cluster_Health($this->getClient());
    }

    /**
     * Return Cluster settings.
     *
     * @return CElastic_Cluster_Settings
     */
    public function getSettings() {
        return new CElastic_Cluster_Settings($this->getClient());
    }

}
