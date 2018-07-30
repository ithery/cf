<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 10:03:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CElastic_Client_ResultSet_BuilderInterface {

    /**
     * Builds a ResultSet given a specific response and query.
     *
     * @param Response $response
     * @param Query    $query
     *
     * @return ResultSet
     */
    public function buildResultSet(CElastic_Client_Response $response, CElastic_Client_Query $query);
}
