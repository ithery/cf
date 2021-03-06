<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 9:39:11 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CElastic_Interface_SearchableInterface {

    /**
     * Searches results for a query.
     *
     * {
     *     "from" : 0,
     *     "size" : 10,
     *     "sort" : {
     *          "postDate" : {"order" : "desc"},
     *          "user" : { },
     *          "_score" : { }
     *      },
     *      "query" : {
     *          "term" : { "user" : "kimchy" }
     *      }
     * }
     *
     * @param string|array|\Elastica\Query $query   Array with all query data inside or a Elastica\Query object
     * @param null                         $options
     *
     * @return \Elastica\ResultSet with all results inside
     */
    public function search($query = '', $options = null);

    /**
     * Counts results for a query.
     *
     * If no query is set, matchall query is created
     *
     * @param string|array|\Elastica\Query $query Array with all query data inside or a Elastica\Query object
     *
     * @return int number of documents matching the query
     */
    public function count($query = '');

    /**
     * @param \Elastica\Query|string $query
     * @param array                  $options
     *
     * @return \Elastica\Search
     */
    public function createSearch($query = '', $options = null);
}
