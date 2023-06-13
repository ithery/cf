<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CQueue_QueueableEntityInterface {
    /**
     * Get the queueable identity for the entity.
     *
     * @return mixed
     */
    public function getQueueableId();

    /**
     * Get the relationships for the entity.
     *
     * @return array
     */
    public function getQueueableRelations();

    /**
     * Get the connection of the entity.
     *
     * @return null|string
     */
    public function getQueueableConnection();
}
