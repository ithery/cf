<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 3:20:51 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Identifier {

    /**
     * The class name of the model.
     *
     * @var string
     */
    public $class;

    /**
     * The unique identifier of the model.
     *
     * This may be either a single ID or an array of IDs.
     *
     * @var mixed
     */
    public $id;

    /**
     * The relationships loaded on the model.
     *
     * @var array
     */
    public $relations;

    /**
     * The connection name of the model.
     *
     * @var string|null
     */
    public $connection;

    /**
     * Create a new model identifier.
     *
     * @param  string  $class
     * @param  mixed  $id
     * @param  array  $relations
     * @param  mixed  $connection
     * @return void
     */
    public function __construct($class, $id, array $relations, $connection) {
        $this->id = $id;
        $this->class = $class;
        $this->relations = $relations;
        $this->connection = $connection;
    }

}
