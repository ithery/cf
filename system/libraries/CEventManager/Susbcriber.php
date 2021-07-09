<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 *
 * @since Aug 18, 2018, 9:16:21 AM
 *
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * An EventSubscriber knows himself what events he is interested in.
 * If an EventSubscriber is added to an EventManager, the manager invokes
 * {@link getSubscribedEvents} and registers the subscriber as a listener for all
 * returned events.
 */
interface CEventManager_Subscriber {
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents();
}
