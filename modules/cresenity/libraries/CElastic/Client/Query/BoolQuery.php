<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 4:28:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Bool query.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
 */
class CElastic_Client_Query_BoolQuery extends CElastic_Client_Query_AbstractQuery {

    /**
     * Add should part to query.
     *
     * @param CElastic_Client_Query_AbstractQuery|array $args Should query
     *
     * @return $this
     */
    public function addShould($args) {
        return $this->_addQuery('should', $args);
    }

    /**
     * Add must part to query.
     *
     * @param CElastic_Client_Query_AbstractQuery|array $args Must query
     *
     * @return $this
     */
    public function addMust($args) {
        return $this->_addQuery('must', $args);
    }

    /**
     * Add must not part to query.
     *
     * @param CElastic_Client_Query_AbstractQuery|array $args Must not query
     *
     * @return $this
     */
    public function addMustNot($args) {
        return $this->_addQuery('must_not', $args);
    }

    /**
     * Sets the filter.
     *
     * @param CElastic_Client_Query_AbstractQuery $filter Filter object
     *
     * @return $this
     */
    public function addFilter(CElastic_Client_Query_AbstractQuery $filter) {
        return $this->addParam('filter', $filter);
    }

    /**
     * Adds a query to the current object.
     *
     * @param string                              $type Query type
     * @param \Elastica\Query\AbstractQuery|array $args Query
     *
     * @throws \Elastica\Exception\InvalidException If not valid query
     *
     * @return $this
     */
    protected function _addQuery($type, $args) {
        if (!is_array($args) && !($args instanceof CElastic_Client_Query_AbstractQuery)) {
            throw new CElastic_Exception_InvalidException('Invalid parameter. Has to be array or instance of CElastic_Client_Query_AbstractQuery');
        }
        return $this->addParam($type, $args);
    }

    /**
     * Sets boost value of this query.
     *
     * @param float $boost Boost value
     *
     * @return $this
     */
    public function setBoost($boost) {
        return $this->setParam('boost', $boost);
    }

    /**
     * Sets the minimum number of should clauses to match.
     *
     * @param int|string $minimum Minimum value
     *
     * @return $this
     */
    public function setMinimumShouldMatch($minimum) {
        return $this->setParam('minimum_should_match', $minimum);
    }

    /**
     * Converts array to an object in case no queries are added.
     *
     * @return array
     */
    public function toArray() {
        if (empty($this->_params)) {
            $this->_params = new \stdClass();
        }
        return parent::toArray();
    }

}
