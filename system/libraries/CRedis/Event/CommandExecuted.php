<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CRedis_Event_CommandExecuted {

    /**
     * The Redis command that was executed.
     *
     * @var string
     */
    public $command;

    /**
     * The array of command parameters.
     *
     * @var array
     */
    public $parameters;

    /**
     * The number of milliseconds it took to execute the command.
     *
     * @var float
     */
    public $time;

    /**
     * The Redis connection instance.
     *
     * @var CRedis_AbstractConnection
     */
    public $connection;

    /**
     * The Redis connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * Create a new event instance.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @param  float|null  $time
     * @param  CRedis_AbstractConnection  $connection
     * @return void
     */
    public function __construct($command, $parameters, $time, $connection) {
        $this->time = $time;
        $this->command = $command;
        $this->parameters = $parameters;
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }

}
