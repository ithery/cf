<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 5:24:18 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Elastica cluster node object.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 */
class CElastic_Client_Node {

    /**
     * Client.
     *
     * @var CElastic_Client
     */
    protected $_client;

    /**
     * @var string Unique node id
     */
    protected $_id;

    /**
     * Node name.
     *
     * @var string Node name
     */
    protected $_name;

    /**
     * Node stats.
     *
     * @var CElastic_Client_Node_Stats|null Node Stats
     */
    protected $_stats;

    /**
     * Node info.
     *
     * @var CElastic_Client_Node_Info|null Node info
     */
    protected $_info;

    /**
     * Create a new node object.
     *
     * @param string           $id     Node id or name
     * @param CElastic_Client $client Node object
     */
    public function __construct($id, CElastic_Client $client) {
        $this->_client = $client;
        $this->setId($id);
    }

    /**
     * @return string Unique node id. Can also be name if id not exists.
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * @param string $id Node id
     *
     * @return $this Refreshed object
     */
    public function setId($id) {
        $this->_id = $id;
        return $this->refresh();
    }

    /**
     * Get the name of the node.
     *
     * @return string Node name
     */
    public function getName() {
        if (empty($this->_name)) {
            $this->_name = $this->getInfo()->getName();
        }
        return $this->_name;
    }

    /**
     * Returns the current client object.
     *
     * @return \Elastica\Client Client
     */
    public function getClient() {
        return $this->_client;
    }

    /**
     * Return stats object of the current node.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-nodes-stats.html
     *
     * @return CElastic_Client_Node_Stats Node stats
     */
    public function getStats() {
        if (!$this->_stats) {
            $this->_stats = new CElastic_Client_Node_Stats($this);
        }
        return $this->_stats;
    }

    /**
     * Return info object of the current node.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-nodes-info.html
     *
     * @return CElastic_Client_Node_Info Node info object
     */
    public function getInfo() {
        if (!$this->_info) {
            $this->_info = new CElastic_Client_Node_Info($this);
        }
        return $this->_info;
    }

    /**
     * Refreshes all node information.
     *
     * This should be called after updating a node to refresh all information
     */
    public function refresh() {
        $this->_stats = null;
        $this->_info = null;
    }

}
