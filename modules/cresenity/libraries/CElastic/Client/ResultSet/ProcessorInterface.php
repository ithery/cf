<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 10:12:47 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CElastic_Client_ResultSet_ProcessorInterface {

    /**
     * Iterates over a ResultSet allowing a processor to iterate over any
     * Results as required.
     *
     * @param CElastic_Client_ResultSet $resultSet
     */
    public function process(CElastic_Client_ResultSet $resultSet);
}
