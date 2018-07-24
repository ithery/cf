<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 10:09:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Client_ResultSet_DefaultBuilder implements CElastic_Client_ResultSet_BuilderInterface {

    /**
     * Builds a ResultSet for a given Response.
     *
     * @param CElastic_Client_Response $response
     * @param CElastic_Client_Query    $query
     *
     * @return CElastic_Client_ResultSet
     */
    public function buildResultSet(CElastic_Client_Response $response, CElastic_Client_Query $query) {
        $results = $this->buildResults($response);
        $resultSet = new CElastic_Client_ResultSet($response, $query, $results);
        return $resultSet;
    }

    /**
     * Builds individual result objects.
     *
     * @param CElastic_Client_Response $response
     *
     * @return CElastic_Client_Result[]
     */
    private function buildResults(CElastic_Client_Response $response) {
        $data = $response->getData();
        $results = [];
        if (!isset($data['hits']['hits'])) {
            return $results;
        }
        foreach ($data['hits']['hits'] as $hit) {
            $results[] = new CElastic_Client_Result($hit);
        }
        return $results;
    }

}
