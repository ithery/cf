<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 5:21:09 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Terms query.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 * @author Roberto Nygaard <roberto@nygaard.es>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html
 */
class CElastic_Client_Query_Terms extends CElastic_Client_Query_AbstractQuery
{
    /**
     * Terms.
     *
     * @var array Terms
     */
    protected $_terms;
    /**
     * Terms key.
     *
     * @var string Terms key
     */
    protected $_key;
    /**
     * Construct terms query.
     *
     * @param string $key   OPTIONAL Terms key
     * @param array  $terms OPTIONAL Terms list
     */
    public function __construct($key = '', array $terms = [])
    {
        $this->setTerms($key, $terms);
    }
    /**
     * Sets key and terms for the query.
     *
     * @param string $key   Terms key
     * @param array  $terms Terms for the query.
     *
     * @return $this
     */
    public function setTerms($key, array $terms)
    {
        $this->_key = $key;
        $this->_terms = array_values($terms);
        return $this;
    }
    /**
     * Sets key and terms lookup for the query.
     *
     * @param string $key         Terms key
     * @param array  $termsLookup Terms lookup for the query.
     *
     * @return $this
     */
    public function setTermsLookup($key, array $termsLookup)
    {
        $this->_key = $key;
        $this->_terms = $termsLookup;
        return $this;
    }
    /**
     * Adds a single term to the list.
     *
     * @param string $term Term
     *
     * @return $this
     */
    public function addTerm($term)
    {
        $this->_terms[] = $term;
        return $this;
    }
    /**
     * Sets the minimum matching values.
     *
     * @param int|string $minimum Minimum value
     *
     * @return $this
     */
    public function setMinimumMatch($minimum)
    {
        return $this->setParam('minimum_match', $minimum);
    }
    /**
     * Converts the terms object to an array.
     *
     * @see \Elastica\Query\AbstractQuery::toArray()
     *
     * @throws \Elastica\Exception\InvalidException If term key is empty
     *
     * @return array Query array
     */
    public function toArray()
    {
        if (empty($this->_key)) {
            throw new InvalidException('Terms key has to be set');
        }
        $this->setParam($this->_key, $this->_terms);
        return parent::toArray();
    }
}