<?php

class CModel_Scout_Attributes_SearchUsingPrefix {
    /**
     * The prefix search columns.
     *
     * @var array
     */
    public $columns = [];

    /**
     * Create a new attribute instance.
     *
     * @param array|string $columns
     *
     * @return void
     */
    public function __construct($columns) {
        $this->columns = carr::wrap($columns);
    }
}
