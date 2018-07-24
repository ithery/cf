<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 4:50:56 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Limit Query.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-limit-query.html
 */
class CElastic_Client_Query_Limit extends CElastic_Client_Query_AbstractQuery {

    /**
     * Construct limit query.
     *
     * @param int $limit Limit
     */
    public function __construct($limit) {
        $this->setLimit($limit);
    }

    /**
     * Set the limit.
     *
     * @param int $limit Limit
     *
     * @return $this
     */
    public function setLimit($limit) {
        return $this->setParam('value', (int) $limit);
    }

}
