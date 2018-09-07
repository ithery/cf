<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 10:15:05 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Match all query. Returns all results.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html
 */
class CElastic_Client_Query_MatchAll extends CElastic_Client_Query_AbstractQuery {

    /**
     * Creates match all query.
     */
    public function __construct() {
        $this->_params = new \stdClass();
    }

}
