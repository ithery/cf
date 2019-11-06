<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 6, 2019, 8:15:04 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CModel_Queue_QueueableEntityInterface {

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
