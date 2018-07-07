<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 5:30:01 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Client_Cluster_Health_Shard {

    /**
     * @var int The shard index/number.
     */
    protected $_shardNumber;

    /**
     * @var array The shard health data.
     */
    protected $_data;

    /**
     * @param int   $shardNumber The shard index/number.
     * @param array $data        The shard health data.
     */
    public function __construct($shardNumber, $data) {
        $this->_shardNumber = $shardNumber;
        $this->_data = $data;
    }

    /**
     * Gets the index/number of this shard.
     *
     * @return int
     */
    public function getShardNumber() {
        return $this->_shardNumber;
    }

    /**
     * Gets the status of this shard.
     *
     * @return string green, yellow or red.
     */
    public function getStatus() {
        return $this->_data['status'];
    }

    /**
     * Is the primary active?
     *
     * @return bool
     */
    public function isPrimaryActive() {
        return $this->_data['primary_active'];
    }

    /**
     * Is this shard active?
     *
     * @return bool
     */
    public function isActive() {
        return $this->_data['active_shards'] == 1;
    }

    /**
     * Is this shard relocating?
     *
     * @return bool
     */
    public function isRelocating() {
        return $this->_data['relocating_shards'] == 1;
    }

    /**
     * Is this shard initialized?
     *
     * @return bool
     */
    public function isInitialized() {
        return $this->_data['initializing_shards'] == 1;
    }

    /**
     * Is this shard unassigned?
     *
     * @return bool
     */
    public function isUnassigned() {
        return $this->_data['unassigned_shards'] == 1;
    }

}
