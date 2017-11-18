<?php

defined('SYSPATH') OR die('No direct access allowed.');

use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\BuilderInterface;

/**
 * @author Hery Kurniawan
 * @since Nov 18, 2017, 9:05:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Search {

    protected $index;
    protected $document_type;
    protected $client;
    protected $must;
    protected $must_not;
    protected $should;

    public function __construct($client, $index, $document_type = '') {
        $this->client = $client;
        $this->index = $index;
        $this->document_type = $document_type;
        $this->must = array();
        $this->must_not = array();
    }

    public function must($path, $value = null) {
        $arr = array();
        if (is_array($path)) {
            $arr = $path;
        } else {
            carr::set_path($arr, $path, $value);
        }
        $this->must[] = $arr;
    }

    public function must_not($path, $value = null) {
        $arr = array();
        if (is_array($path)) {
            $arr = $path;
        } else {
            carr::set_path($arr, $path, $value);
        }
        $this->must[] = $arr;
    }

    public function exec() {
        $params = array();
        $params['index'] = $this->index;
        if (strlen($this->document_type) > 0) {
            $params['type'] = $this->document_type;
        }

        //build the body

        $body = array();
        if (count($this->must) > 0) {
            carr::set_path($body, 'query.bool.must', $this->must);
        }
        if (count($this->must_not) > 0) {
            carr::set_path($body, 'query.bool.must_not', $this->must);
        }


        $params['body'] = $body;
        cdbg::var_dump($params);
        $result = $this->client->search($params);
        cdbg::var_dump($result);
        die();
        return $result;
    }

}
