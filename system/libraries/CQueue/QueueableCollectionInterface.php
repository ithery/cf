<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CQueue_QueueableCollectionInterface {
    /**
     * Get the type of the entities being queued.
     *
     * @return null|string
     */
    public function getQueueableClass();

    /**
     * Get the identifiers for all of the entities.
     *
     * @return array
     */
    public function getQueueableIds();

    /**
     * Get the relationships of the entities being queued.
     *
     * @return array
     */
    public function getQueueableRelations();

    /**
     * Get the connection of the entities being queued.
     *
     * @return null|string
     */
    public function getQueueableConnection();
}
