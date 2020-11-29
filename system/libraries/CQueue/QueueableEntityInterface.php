<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
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
     * @return string|null
     */
    public function getQueueableConnection();
}
