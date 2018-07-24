<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 10:12:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Allows multiple ProcessorInterface instances to operate on the same
 * ResultSet, calling each in turn.
 */
class CElastic_Client_ResultSet_ChainProcessor implements CElastic_Client_ResultSet_ProcessorInterface {

    /**
     * @var CElastic_Client_ResultSet_ProcessorInterface[]
     */
    private $processors;

    /**
     * @param CElastic_Client_ResultSet_ProcessorInterface[] $processors
     */
    public function __construct($processors) {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CElastic_Client_ResultSet $resultSet) {
        foreach ($this->processors as $processor) {
            $processor->process($resultSet);
        }
    }

}
