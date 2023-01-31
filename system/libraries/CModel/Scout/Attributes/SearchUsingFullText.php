<?php

class CModel_Scout_Attributes_SearchUsingFullText {
    /**
     * The full-text columns.
     *
     * @var array
     */
    public $columns = [];

    /**
     * The full-text options.
     */
    public $options = [];

    /**
     * Create a new attribute instance.
     *
     * @param array $columns
     * @param array $options
     *
     * @return void
     */
    public function __construct($columns, $options = []) {
        $this->columns = carr::wrap($columns);
        $this->options = carr::wrap($options);
    }
}
