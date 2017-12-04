<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 18, 2017, 9:05:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Search {

    protected $index;
    protected $document_type;

    /*
     * @var Elasticsearch\Client
     */
    protected $elastic;
    protected $client;
    protected $must;
    protected $must_not;
    protected $should;
    protected $select;
    protected $from;
    protected $size;
    protected $sort;
    protected $aggs;

    public function __construct(CElastic $elastic, $index, $document_type = '') {
        $this->elastic = $elastic;
        $this->client = $elastic->client();
        $this->index = $index;
        $this->document_type = $document_type;
        $this->must = array();
        $this->must_not = array();
        $this->select = array();
        $this->from = null;
        $this->size = null;
        $this->sort = array();
        $this->aggs = array();
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
        $this->must_not[] = $arr;
    }

    public function from($from) {
        $this->from = $from;
    }

    public function size($size) {
        $this->size = $size;
    }

    public function sort($field, $mode = 'asc') {
        $arr = array();
        if (is_array($field)) {
            $arr = $field;
        } else {
            $arr = array($field => array('order' => $mode));
        }
        $this->sort[] = $arr;
    }

    public function aggs($name, $function, $field, $min_doc_count = 0) {
        $this->aggs[$name] = array(
            $function => array(
                "field" => $field
            )
        );
        
        if($min_doc_count > 0) {
            $this->aggs[$name]['terms']["min_doc_count"] = $min_doc_count;
        }
    }

    public function aggs_order($name, $order, $order_type = "", $order_mode = "") {
        if (isset($this->aggs[$name])) {
            if ($order != null) {
                $this->aggs[$name]["terms"]["order"] = $order;
            }

            if (strlen($order_type) > 0 && strlen($order_mode) > 0) {
                $this->aggs[$name]["terms"]["order"] = array(
                    $order_type => $order_mode
                );
            }
        }
    }

    public function sub_aggs($name_parent, $name, $field, $type = "avg", $filter_field = "", $filter_value = "") {
        if (isset($this->aggs[$name_parent])) {
            $this->aggs[$name_parent]["aggs"] = array(
                $name => array(
                    $type => array(
                        "field" => $field
                    )
                )
            );
            if(strlen($filter_field) > 0 && strlen($filter_value) > 0) {
                $this->aggs[$name_parent]["aggs"][$name] = array(
                    "filter" => array(
                        "term" => array(
                            $filter_field => $filter_value
                        )
                    )
                );
            }
        }
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
            carr::set_path($body, 'query.bool.must_not', $this->must_not);
        }

        if ($this->size != null) {
            $body['size'] = $this->size;
        }
        if ($this->from != null) {
            $body['from'] = $this->from;
        }

        if (count($this->sort) > 0) {
            $body['sort'] = $this->sort;
        }
        if (count($this->aggs) > 0) {
            $body['aggs'] = $this->aggs;
        }

        $params['body'] = $body;
        $response = $this->client->search($params);
        $result = new CElastic_Result($response, $this->select);
        return $result;
    }

    public function select($field, $alias = null) {
        if ($alias == null) {
            $alias = $field;
        }
        $arr = array('field' => $field, 'alias' => $alias);
        $this->select[] = $arr;
    }

    public function ajax_data() {
        $data = array();
        $data['index'] = $this->index;
        $data['document_type'] = $this->document_type;
        $data['config'] = $this->elastic->config();
        $data['must'] = $this->must;
        $data['must_not'] = $this->must_not;
        $data['select'] = $this->select;
        $data['from'] = $this->from;
        $data['size'] = $this->size;
        return $data;
    }

}
