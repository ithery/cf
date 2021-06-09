<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 3:50:35 PM
 */
abstract class CObservable_Listener_Handler {
    use CTrait_Compat_Handler_Driver;

    const TYPE_REMOVE = 'remove';
    const TYPE_RELOAD = 'reload';
    const TYPE_SUBMIT = 'submit';
    const TYPE_DIALOG = 'dialog';
    const TYPE_EMPTY = 'empty';
    const TYPE_CUSTOM = 'custom';
    const TYPE_APPEND = 'append';
    const TYPE_PREPEND = 'prepend';

    protected $name;
    protected $handlers;
    protected $driver;
    protected $listener;
    protected $handlerListeners = [];

    /**
     * Event from listener
     *
     * @var string
     */
    protected $event;

    /**
     * Id element of owner this event listener
     *
     * @var string
     */
    protected $owner;

    public function __construct(CObservable_ListenerAbstract $listener) {
        $this->listener = $listener;
        $this->owner = $this->listener->getOwner();
        $this->event = $this->listener->getEvent();
    }

    public function setOwner($owner) {
        $this->owner = $owner;
        return $this;
    }

    public function haveListener($event) {
        return isset($this->handlerListeners[$event]);
    }

    public function getListener($event) {
        if ($this->haveListener($event)) {
            return $this->handlerListeners[$event];
        }
        return null;
    }

    abstract public function js();
}
