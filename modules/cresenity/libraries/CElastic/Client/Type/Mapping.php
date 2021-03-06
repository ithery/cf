<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 11:59:41 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Elastica Mapping object.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html
 */
use Elasticsearch\Endpoints\Indices\Mapping\Put;

class CElastic_Client_Type_Mapping {

    /**
     * Mapping.
     *
     * @var array Mapping
     */
    protected $_mapping = [];

    /**
     * Type.
     *
     * @var CElastic_Client_Type Type object
     */
    protected $_type;

    /**
     * Construct Mapping.
     *
     * @param CElastic_Client_Type $type       OPTIONAL Type object
     * @param array          $properties OPTIONAL Properties
     */
    public function __construct(CElastic_Client_Type $type = null, array $properties = []) {
        if ($type) {
            $this->setType($type);
        }
        if (!empty($properties)) {
            $this->setProperties($properties);
        }
    }

    /**
     * Sets the mapping type
     * Enter description here ...
     *
     * @param \Elastica\Type $type Type object
     *
     * @return $this
     */
    public function setType(Type $type) {
        $this->_type = $type;
        return $this;
    }

    /**
     * Sets the mapping properties.
     *
     * @param array $properties Properties
     *
     * @return $this
     */
    public function setProperties(array $properties) {
        return $this->setParam('properties', $properties);
    }

    /**
     * Gets the mapping properties.
     *
     * @return array $properties Properties
     */
    public function getProperties() {
        return $this->getParam('properties');
    }

    /**
     * Sets the mapping _meta.
     *
     * @param array $meta metadata
     *
     * @return $this
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-meta.html
     */
    public function setMeta(array $meta) {
        return $this->setParam('_meta', $meta);
    }

    /**
     * Returns mapping type.
     *
     * @return \Elastica\Type Type
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Sets source values.
     *
     * To disable source, argument is
     * array('enabled' => false)
     *
     * @param array $source Source array
     *
     * @return $this
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-source-field.html
     */
    public function setSource(array $source) {
        return $this->setParam('_source', $source);
    }

    /**
     * Disables the source in the index.
     *
     * Param can be set to true to enable again
     *
     * @param bool $enabled OPTIONAL (default = false)
     *
     * @return $this
     */
    public function disableSource($enabled = false) {
        return $this->setSource(['enabled' => $enabled]);
    }

    /**
     * Sets raw parameters.
     *
     * Possible options:
     * _uid
     * _id
     * _type
     * _source
     * _analyzer
     * _boost
     * _routing
     * _index
     * _size
     * properties
     *
     * @param string $key   Key name
     * @param mixed  $value Key value
     *
     * @return $this
     */
    public function setParam($key, $value) {
        $this->_mapping[$key] = $value;
        return $this;
    }

    /**
     * Get raw parameters.
     *
     * @see setParam
     *
     * @param string $key Key name
     *
     * @return mixed $value Key value
     */
    public function getParam($key) {
        return isset($this->_mapping[$key]) ? $this->_mapping[$key] : null;
    }

    /**
     * Converts the mapping to an array.
     *
     * @throws \Elastica\Exception\InvalidException
     *
     * @return array Mapping as array
     */
    public function toArray() {
        $type = $this->getType();
        if (empty($type)) {
            throw new InvalidException('Type has to be set');
        }
        return [$type->getName() => $this->_mapping];
    }

    /**
     * Submits the mapping and sends it to the server.
     *
     * @param array $query Query string parameters to send with mapping
     *
     * @return \Elastica\Response Response object
     */
    public function send(array $query = []) {
        $endpoint = new Put();
        $endpoint->setBody($this->toArray());
        $endpoint->setParams($query);
        return $this->getType()->requestEndpoint($endpoint);
    }

    /**
     * Creates a mapping object.
     *
     * @param array|CElastic_Client_Type_Mapping $mapping Mapping object or properties array
     *
     * @throws CElastic_Exception_InvalidException If invalid type
     *
     * @return self
     */
    public static function create($mapping) {
        if (is_array($mapping)) {
            $mappingObject = new self();
            $mappingObject->setProperties($mapping);
            return $mappingObject;
        }
        if ($mapping instanceof self) {
            return $mapping;
        }
        throw new CElastic_Exception_InvalidException('Invalid object type');
    }

}
