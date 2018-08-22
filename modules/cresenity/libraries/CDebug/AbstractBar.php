<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 3:13:50 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDebug_AbstractBar implements ArrayAccess {

    /**
     *
     * @var CDebug_Interface_DataCollectorInterface[] 
     */
    protected $collectors = array();

    /**
     * Config of this bar
     * 
     * @var CDebug_Bar_Config
     */
    protected $config;

    public function __construct(array $options = array()) {
        $this->config = new CDebug_Bar_Config($options);
    }

    /**
     * 
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options) {
        $this->config->setOptions($options);
        return $this;
    }

    /**
     * Adds a data collector
     *
     * @param DataCollectorInterface $collector
     *
     * @throws DebugBarException
     * @return $this
     */
    public function addCollector(CDebug_Interface_DataCollectorInterface $collector) {
        if ($collector->getName() === '__meta') {
            throw new CDebug_Bar_Exception("'__meta' is a reserved name and cannot be used as a collector name");
        }
        if (isset($this->collectors[$collector->getName()])) {
            throw new CDebug_Bar_Exception("'{$collector->getName()}' is already a registered collector");
        }
        $this->collectors[$collector->getName()] = $collector;
        return $this;
    }

    /**
     * Returns a data collector
     *
     * @param string $name
     * @return DataCollectorInterface
     * @throws DebugBarException
     */
    public function getCollector($name) {
        if (!isset($this->collectors[$name])) {
            throw new DebugBarException("'$name' is not a registered collector");
        }
        return $this->collectors[$name];
    }

    /**
     * Returns an array of all data collectors
     *
     * @return array[DataCollectorInterface]
     */
    public function getCollectors() {
        return $this->collectors;
    }

    /**
     * Checks if a data collector has been added
     *
     * @param string $name
     * @return boolean
     */
    public function hasCollector($name) {
        return isset($this->collectors[$name]);
    }

    // --------------------------------------------
    // ArrayAccess implementation
    public function offsetSet($key, $value) {
        throw new DebugBarException("DebugBar[] is read-only");
    }

    public function offsetGet($key) {
        return $this->getCollector($key);
    }

    public function offsetExists($key) {
        return $this->hasCollector($key);
    }

    public function offsetUnset($key) {
        throw new DebugBarException("DebugBar[] is read-only");
    }

}
