<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Resolve the connection and queue of a queueable from the routing table.
 *
 * @see CQueue_QueueRoutes
 */
trait CQueue_Trait_ResolvesQueueRoutesTrait {
    /**
     * Resolve the default connection name for a given queueable instance.
     *
     * @param object $queueable
     *
     * @return null|string
     */
    public function resolveConnectionFromQueueRoute($queueable) {
        return $this->queueRoutes()->getConnection($queueable);
    }

    /**
     * Resolve the default queue name for a given queueable instance.
     *
     * @param object $queueable
     *
     * @return null|string
     */
    public function resolveQueueFromQueueRoute($queueable) {
        return $this->queueRoutes()->getQueue($queueable);
    }

    /**
     * Get the queue routes manager instance.
     *
     * @return CQueue_QueueRoutes
     */
    protected function queueRoutes() {
        $container = CContainer::getInstance();

        if ($container->bound('queue.routes')) {
            return $container->make('queue.routes');
        }

        return CQueue::routes();
    }
}
