<?php

class CElastic_Result implements ArrayAccess, Iterator, Countable {

    /**
     * Time needed to execute the query.
     *
     * @var
     */
    protected $took;

    /**
     * Check if the query timed out.
     *
     * @var
     */
    protected $timed_out;

    /**
     * @var
     */
    protected $shards;

    /**
     * Result of the query.
     *
     * @var
     */
    protected $hits;

    /**
     * Total number of hits.
     *
     * @var
     */
    protected $totalHits;

    /**
     * Highest document score.
     *
     * @var
     */
    protected $maxScore;

    /**
     * The aggregations result.
     *
     * @var array|null
     */
    protected $aggregations = null;

    /**
     * The aggregations result.
     *
     * @var CElastic_Database_Result|null
     */
    protected $result = null;

    /**
     * _construct.
     *
     * @param array $results
     */
    public function __construct(array $results) {
        $this->took = $results['took'];

        $this->timed_out = $results['timed_out'];

        $this->shards = $results['_shards'];

        $this->hits = new CCollection($results['hits']['hits']);

        $this->totalHits = $results['hits']['total'];

        $this->maxScore = $results['hits']['max_score'];

        $this->aggregations = isset($results['aggregations']) ? $results['aggregations'] : [];

        $this->result = new CElastic_Database_Result($results);
    }

    /**
     * Total Hits.
     *
     * @return int
     */
    public function totalHits() {
        return $this->totalHits;
    }

    /**
     * Max Score.
     *
     * @return float
     */
    public function maxScore() {
        return $this->maxScore;
    }

    /**
     * Get Shards.
     *
     * @return array
     */
    public function shards() {
        return $this->shards;
    }

    /**
     * Took.
     *
     * @return string
     */
    public function took() {
        return $this->took;
    }

    /**
     * Timed Out.
     *
     * @return bool
     */
    public function timedOut() {
        return (bool) $this->timed_out;
    }

    /**
     * Get Hits.
     *
     * Get the hits from Elasticsearch
     * results as a Collection.
     *
     * @return Collection
     */
    public function hits() {
        return $this->hits;
    }

    /**
     * Set the hits value.
     *
     * @param $values
     */
    public function setHits($values) {
        $this->hits = $values;
    }

    /**
     * Get aggregations.
     *
     * Get the raw hits array from
     * Elasticsearch results.
     *
     * @return array
     */
    public function aggregations() {
        return $this->aggregations;
    }

    /**
     * Get _source Hits.
     *
     * Get the _source from hits Elasticsearch
     * results as a CDatabase_Result.
     *
     * @return CElastic_Database_Result
     */
    public function result() {
        return $this->result;
    }

    public function count_all() {
        return $this->result()->count_all();
    }

    public function count() {
        return $this->result()->count();
    }

    public function current() {
        return $this->result()->current();
    }

    public function key() {
        return $this->result()->key();
    }

    public function next() {
        return $this->result()->next();
    }

    public function offsetExists($offset) {
        return $this->result()->offsetExists($offset);
    }

    public function offsetGet($offset) {
        return $this->result()->offsetGet($offset);
    }

    public function offsetSet($offset, $value) {
        return $this->result()->offsetSet($offset, $value);
    }

    public function offsetUnset($offset) {
        return $this->result()->offsetUnset($offset);
    }

    public function rewind() {
        return $this->result()->rewind();
    }

    public function valid() {
        return $this->result()->valid();
    }

}
