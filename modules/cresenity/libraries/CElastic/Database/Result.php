<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Nov 18, 2017, 10:41:43 PM
 */
class CElastic_Database_Result extends CElasticResult {
    protected $select;

    public function __construct($elastic_response, $select = []) {
        $this->raw_response = $elastic_response;
        $this->count_all = carr::get($this->raw_response, 'hits.total', 0);
        $this->fetch_type = 'object';
        $this->select = $select;
        $this->result = $this->getElasticResult();
        $this->total_rows = count($this->result);
    }

    protected function getElasticResult() {
        $hits = carr::get($this->raw_response, 'hits.hits');
        $result = [];
        foreach ($hits as $k => $node) {
            $row = carr::get($node, '_source');
            foreach ($this->select as $k => $v) {
                $field = carr::get($v, 'field');
                $alias = carr::get($v, 'alias');
                $row[$alias] = $row[$field];
            }

            $result[] = $row;
        }

        return $result;
    }
}
