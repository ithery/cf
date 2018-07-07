<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 10:13:43 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Client_ResultSet_ProcessingBuilder implements CElastic_Client_ResultSet_BuilderInterface {

    /**
     * @var CElastic_Client_ResultSet_BuilderInterface
     */
    private $builder;

    /**
     * @var CElastic_Client_ResultSet_ProcessorInterface
     */
    private $processor;

    /**
     * @param CElastic_Client_ResultSet_BuilderInterface   $builder
     * @param CElastic_Client_ResultSet_ProcessorInterface $processor
     */
    public function __construct(CElastic_Client_ResultSet_BuilderInterface $builder, CElastic_Client_ResultSet_ProcessorInterface $processor) {
        $this->builder = $builder;
        $this->processor = $processor;
    }

    /**
     * Runs any registered transformers on the ResultSet before
     * returning it, allowing the transformers to inject additional
     * data into each Result.
     *
     * @param CElastic_Client_Response $response
     * @param CElastic_Client_Query    $query
     *
     * @return CElastic_Client_ResultSet
     */
    public function buildResultSet(CElastic_Client_Response $response, CElastic_Client_Query $query) {
        $resultSet = $this->builder->buildResultSet($response, $query);
        $this->processor->process($resultSet);
        return $resultSet;
    }

}
