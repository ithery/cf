<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 4:37:54 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * elasticsearch query DSL.
 *
 * @author Manuel Andreo Garcia <andreo.garcia@googlemail.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-queries.html
 */
class CElastic_Client_QueryBuilder_DSL_Query implements CElastic_Client_QueryBuilder_DSL {

    /**
     * must return type for QueryBuilder usage.
     *
     * @return string
     */
    public function getType() {
        return self::TYPE_QUERY;
    }

    /**
     * match query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html
     *
     * @param string $field
     * @param mixed  $values
     *
     * @return Match
     */
    public function match($field = null, $values = null) {
        return new CElastic_Client_Query_Match($field, $values);
    }

    /**
     * multi match query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html
     *
     * @return CElastic_Client_Query_MultiMatch
     */
    public function multi_match() {
        return new CElastic_Client_Query_MultiMatch();
    }

    /**
     * bool query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
     *
     * @return CElastic_Client_Query_BoolQuery
     */
    public function boolQuery() {
        return new CElastic_Client_Query_BoolQuery();
    }

    /**
     * boosting query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-boosting-query.html
     *
     * @return Boosting
     */
    public function boosting() {
        return new CElastic_Client_Query_Boosting();
    }

    /**
     * common terms query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-common-terms-query.html
     *
     * @param string $field
     * @param string $query
     * @param float  $cutoffFrequency percentage in decimal form (.001 == 0.1%)
     *
     * @return Common
     */
    public function common_terms($field, $query, $cutoffFrequency) {
        return new CElastic_Client_Query_Common($field, $query, $cutoffFrequency);
    }

    /**
     * constant score query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-constant-score-query.html
     *
     * @param null|CElastic_Client_Query_AbstractQuery|array $filter
     *
     * @return ConstantScore
     */
    public function constant_score($filter = null) {
        return new CElastic_Client_Query_ConstantScore($filter);
    }

    /**
     * dis max query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-dis-max-query.html
     *
     * @return DisMax
     */
    public function dis_max() {
        return new CElastic_Client_Query_DisMax();
    }

    /**
     * function score query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html
     *
     * @return FunctionScore
     */
    public function function_score() {
        return new CElastic_Client_Query_FunctionScore();
    }

    /**
     * fuzzy query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html
     *
     * @param string $fieldName Field name
     * @param string $value     String to search for
     *
     * @return Fuzzy
     */
    public function fuzzy($fieldName = null, $value = null) {
        return new CElastic_Client_Query_Fuzzy($fieldName, $value);
    }

    /**
     * geo shape query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-shape-query.html
     */
    public function geo_shape() {
        throw new NotImplementedException();
    }

    /**
     * has child query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-child-query.html
     *
     * @param string|\Elastica\Query|CElastic_Client_Query_AbstractQuery $query
     * @param string                                               $type  Parent document type
     *
     * @return HasChild
     */
    public function has_child($query, $type = null) {
        return new CElastic_Client_Query_HasChild($query, $type);
    }

    /**
     * has parent query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-parent-query.html
     *
     * @param string|\Elastica\Query|CElastic_Client_Query_AbstractQuery $query
     * @param string                                               $type  Parent document type
     *
     * @return HasParent
     */
    public function has_parent($query, $type) {
        return new CElastic_Client_Query_HasParent($query, $type);
    }

    /**
     * ids query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-ids-query.html
     *
     * @param array $ids
     *
     * @return Ids
     */
    public function ids(array $ids = []) {
        return new CElastic_Client_Query_Ids($ids);
    }

    /**
     * match all query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html
     *
     * @return MatchAll
     */
    public function match_all() {
        return new CElastic_Client_Query_MatchAll();
    }

    /**
     * match none query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html#query-dsl-match-none-query
     *
     * @return MatchNone
     */
    public function match_none() {
        return new CElastic_Client_Query_MatchNone();
    }

    /**
     * more like this query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-mlt-query.html
     *
     * @return MoreLikeThis
     */
    public function more_like_this() {
        return new CElastic_Client_Query_MoreLikeThis();
    }

    /**
     * nested query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html
     *
     * @return Nested
     */
    public function nested() {
        return new CElastic_Client_Query_Nested();
    }

    /**
     * prefix query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html
     *
     * @param array $prefix Prefix array
     *
     * @return Prefix
     */
    public function prefix(array $prefix = []) {
        return new CElastic_Client_Query_Prefix($prefix);
    }

    /**
     * query string query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html
     *
     * @param string $queryString OPTIONAL Query string for object
     *
     * @return QueryString
     */
    public function query_string($queryString = '') {
        return new CElastic_Client_Query_QueryString($queryString);
    }

    /**
     * simple_query_string query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html
     *
     * @param string $query
     * @param array  $fields
     *
     * @return SimpleQueryString
     */
    public function simple_query_string($query, array $fields = []) {
        return new CElastic_Client_Query_SimpleQueryString($query, $fields);
    }

    /**
     * range query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html
     *
     * @param string $fieldName
     * @param array  $args
     *
     * @return Range
     */
    public function range($fieldName = null, array $args = []) {
        return new CElastic_Client_Query_Range($fieldName, $args);
    }

    /**
     * regexp query.
     *
     * @param string $key
     * @param string $value
     * @param float  $boost
     *
     * @return Regexp
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html
     */
    public function regexp($key = '', $value = null, $boost = 1.0) {
        return new CElastic_Client_Query_Regexp($key, $value, $boost);
    }

    /**
     * span first query.
     *
     * @param CElastic_Client_Query_AbstractQuery|array $match
     * @param int                                 $end
     *
     * @return SpanFirst
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-first-query.html
     */
    public function span_first($match = null, $end = null) {
        return new CElastic_Client_Query_SpanFirst($match, $end);
    }

    /**
     * span multi term query.
     *
     * @param CElastic_Client_Query_AbstractQuery|array $match
     *
     * @return SpanMulti
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-multi-term-query.html
     */
    public function span_multi_term($match = null) {
        return new CElastic_Client_Query_SpanMulti($match);
    }

    /**
     * span near query.
     *
     * @param array $clauses
     * @param int   $slop
     * @param bool  $inOrder
     *
     * @return SpanNear
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-near-query.html
     */
    public function span_near($clauses = [], $slop = 1, $inOrder = false) {
        return new CElastic_Client_Query_SpanNear($clauses, $slop, $inOrder);
    }

    /**
     * span not query.
     *
     * @param AbstractSpanQuery|null $include
     * @param AbstractSpanQuery|null $exclude
     *
     * @return SpanNot
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-not-query.html
     */
    public function span_not(AbstractSpanQuery $include = null, AbstractSpanQuery $exclude = null) {
        return new CElastic_Client_Query_SpanNot($include, $exclude);
    }

    /**
     * span_or query.
     *
     * @param array $clauses
     *
     * @return SpanOr
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-or-query.html
     */
    public function span_or($clauses = []) {
        return new CElastic_Client_Query_SpanOr($clauses);
    }

    /**
     * span_term query.
     *
     * @param array $term
     *
     * @return SpanTerm
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-term-query.html
     */
    public function span_term(array $term = []) {
        return new CElastic_Client_Query_SpanTerm($term);
    }

    /**
     * span_containing query.
     *
     * @param AbstractSpanQuery|null $little
     * @param AbstractSpanQuery|null $big
     *
     * @return SpanContaining
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-containing-query.html
     */
    public function span_containing(AbstractSpanQuery $little = null, AbstractSpanQuery $big = null) {
        return new CElastic_Client_Query_SpanContaining($little, $big);
    }

    /**
     * span_within query.
     *
     * @param AbstractSpanQuery|null $little
     * @param AbstractSpanQuery|null $big
     *
     * @return SpanWithin
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-within-query.html
     */
    public function span_within(AbstractSpanQuery $little = null, AbstractSpanQuery $big = null) {
        return new CElastic_Client_Query_SpanWithin($little, $big);
    }

    /**
     * term query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html
     *
     * @param array $term
     *
     * @return Term
     */
    public function term(array $term = []) {
        return new CElastic_Client_Query_Term($term);
    }

    /**
     * terms query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html
     *
     * @param string $key
     * @param array  $terms
     *
     * @return Terms
     */
    public function terms($key = '', array $terms = []) {
        return new CElastic_Client_Query_Terms($key, $terms);
    }

    /**
     * wildcard query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html
     *
     * @param string $key   OPTIONAL Wildcard key
     * @param string $value OPTIONAL Wildcard value
     * @param float  $boost OPTIONAL Boost value (default = 1)
     *
     * @return Wildcard
     */
    public function wildcard($key = '', $value = null, $boost = 1.0) {
        return new CElastic_Client_Query_Wildcard($key, $value, $boost);
    }

    /**
     * geo distance query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html
     *
     * @param string       $key
     * @param array|string $location
     * @param string       $distance
     *
     * @return GeoDistance
     */
    public function geo_distance($key, $location, $distance) {
        return new CElastic_Client_Query_GeoDistance($key, $location, $distance);
    }

    /**
     * exists query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html
     *
     * @param string $field
     *
     * @return Exists
     */
    public function exists($field) {
        return new CElastic_Client_Query_Exists($field);
    }

    /**
     * type query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-type-query.html
     *
     * @param string $type Type name
     *
     * @return Type
     */
    public function type($type = null) {
        return new CElastic_Client_Query_Type($type);
    }

    /**
     * type query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/5.0/query-dsl-percolate-query.html
     *
     * @return Percolate
     */
    public function percolate() {
        return new CElastic_Client_Query_Percolate();
    }

}
