<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 18, 2017, 10:26:29 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CElasticResult extends CDatabase_Result {

    protected $raw_response = array();
    protected $count_all = 0;
    private $result_object = null;

    public function __construct($elastic_response) {

        $this->raw_response = $elastic_response;
        $this->count_all = carr::path($this->raw_response, 'hits.total', 0);
        $this->fetch_type = 'object';
        $this->result = $this->_get_result();
        $this->total_rows = count($this->result);
    }

    abstract protected function _get_result();

    protected function _result_object() {
        if ($this->result_object == null) {
            $result = $this->result;
            foreach ($result as $k => $row) {
                $result[$k] = @json_decode(json_encode($row));
            }
            $this->result_object = $result;
        }

        return $this->result_object;
    }

    public function list_fields() {
        return array_keys($this->result);
    }

    public function result($object = TRUE, $type = FALSE) {
        $this->fetch_type = ((bool) $object) ? 'object' : 'array';

        // This check has to be outside the previous statement, because we do not
        // know the state of fetch_type when $object = NULL
        // NOTE - The class set by $type must be defined before fetching the result,
        // autoloading is disabled to save a lot of stupid overhead.
        $this->return_type = $type;

        return $this;
    }

    public function result_array($object = NULL, $type = FALSE) {
        if (is_string($object)) {
            $fetch = $object;
        } elseif (is_bool($object)) {
            if ($object === TRUE) {
                $fetch = 'object';
            } else {
                $fetch = 'array';
            }
        } else {
            // Use the default config values
            $fetch = $this->fetch_type;
        }

        $result = $this->result;
        if ($fetch == 'object') {
            $result = $this->_result_object();
        }

        return $result;
    }

    public function seek($offset) {
        if ($this->offsetExists($offset) AND isset($this->result[$offset])) {
            // Set the current row to the offset
            $this->current_row = $offset;

            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * ArrayAccess: offsetGet
     */
    public function offsetGet($offset) {
        if (!$this->seek($offset))
            return FALSE;

        // Return the row by check current fetch_type
        $result = $this->result;
        if($this->fetch_type=='object') {
            $result = $this->_result_object();
        }
        return $result[$this->current_row];
    }

    public function count_all() {
        return $this->count_all;
    }

}
