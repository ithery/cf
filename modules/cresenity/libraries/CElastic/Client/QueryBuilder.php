<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 4:31:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Query Builder.
 *
 * @author Manuel Andreo Garcia <andreo.garcia@googlemail.com>
 */
class CElastic_Client_QueryBuilder {

    /**
     * @var Version
     */
    private $_version;

    /**
     * @var Facade[]
     */
    private $_facades = [];

    /**
     * Constructor.
     *
     * @param Version $version
     */
    public function __construct(CElastic_Client_QueryBuilder_Version $version = null) {
        $this->_version = $version == null ? new CElastic_Client_QueryBuilder_Version_Latest() : $version;
        $this->addDSL(new CElastic_Client_QueryBuilder_DSL_Query());
        $this->addDSL(new CElastic_Client_QueryBuilder_DSL_Aggregation());
        $this->addDSL(new CElastic_Client_QueryBuilder_DSL_Suggest());
    }

    /**
     * Returns Facade for custom DSL object.
     *
     * @param $dsl
     * @param array $arguments
     *
     * @throws QueryBuilderException
     *
     * @return Facade
     */
    public function __call($dsl, array $arguments) {
        if (false === isset($this->_facades[$dsl])) {
            throw new CElastic_Exception_QueryBuilderException('DSL "' . $dsl . '" not supported');
        }
        return $this->_facades[$dsl];
    }

    /**
     * Adds a new DSL object.
     *
     * @param DSL $dsl
     */
    public function addDSL(CElastic_Client_QueryBuilder_DSL $dsl) {
        $this->_facades[$dsl->getType()] = new CElastic_Client_QueryBuilder_Facade($dsl, $this->_version);
    }

    /*
     * convenience methods
     */

    /**
     * Query DSL.
     *
     * @return CElastic_Client_QueryBuilder_DSL_Query
     */
    public function query() {
        return $this->_facades[CElastic_Client_QueryBuilder_DSL::TYPE_QUERY];
    }

    /**
     * Aggregation DSL.
     *
     * @return CElastic_Client_QueryBuilder_DSL_Aggregation
     */
    public function aggregation() {
        return $this->_facades[CElastic_Client_QueryBuilder_DSL::TYPE_AGGREGATION];
    }

    /**
     * Suggest DSL.
     *
     * @return CElastic_Client_QueryBuilder_DSL_Suggest
     */
    public function suggest() {
        return $this->_facades[CElastic_Client_QueryBuilder_DSL::TYPE_SUGGEST];
    }

}
