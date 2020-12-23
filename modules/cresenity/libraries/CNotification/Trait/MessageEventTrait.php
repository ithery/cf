<?php

trait CNotification_Trait_MessageEventTrait {
    /**
     * The event map for the notification.
     *
     * Allows for object-based events for native Notification events.
     *
     * @var array
     */
    protected $dispatchesEvents = [];

    /**
     * User exposed observable events.
     *
     * These are extra user-defined events observers may subscribe to.
     *
     * @var array
     */
    protected $observables = [];

    /**
     * Register an observer with the Model.
     *
     * @param object|string $class
     *
     * @return void
     */
    public static function observe($class) {
        $instance = new static;

        $className = is_string($class) ? $class : get_class($class);

        // When registering a model observer, we will spin through the possible events
        // and determine if this observer has that method. If it does, we will hook
        // it into the model's event system, making it convenient to watch these.
        foreach ($instance->getObservableEvents() as $event) {
            if (method_exists($class, $event)) {
                static::registerNotificationEvent($event, $className . '@' . $event);
            }
        }
    }

    /**
     * Get the observable event names.
     *
     * @return array
     */
    public function getObservableEvents() {
        return array_merge(
            ['sending', 'sent'],
            $this->observables
        );
    }

    /**
     * Register a notification event with the dispatcher.
     *
     * @param string          $event
     * @param \Closure|string $callback
     *
     * @return void
     */
    protected static function registerNotificationEvent($event, $callback) {
        if (isset(static::$dispatcher)) {
            $name = static::class;
            static::$dispatcher->listen("notification.{$event}: {$name}", $callback);
        }
    }

    /**
     * Filter the notification event results.
     *
     * @param mixed $result
     *
     * @return mixed
     */
    protected function filterNotificationEventResults($result) {
        if (is_array($result)) {
            $result = array_filter($result, function ($response) {
                return !is_null($response);
            });
        }

        return $result;
    }

    /**
     * Fire a custom notification event for the given event.
     *
     * @param string $event
     * @param string $method
     *
     * @return mixed|null
     */
    protected function fireCustomNotificationEvent($event, $method) {
        if (!isset($this->dispatchesEvents[$event])) {
            return;
        }

        $result = static::$dispatcher->$method(new $this->dispatchesEvents[$event]($this));

        if (!is_null($result)) {
            return $result;
        }
    }

    /**
     * Fire the given event for the notification.
     *
     * @param string $event
     * @param bool   $halt
     *
     * @return mixed
     */
    protected function fireNotificationEvent($event, $halt = true) {
        if (!isset(static::$dispatcher)) {
            return true;
        }

        // First, we will get the proper method to call on the event dispatcher, and then we
        // will attempt to fire a custom, object based event for the given event. If that
        // returns a result we can return that result, or we'll call the string events.
        $method = $halt ? 'until' : 'dispatch';

        $result = $this->filterNotificationEventResults(
            $this->fireCustomNotificationEvent($event, $method)
        );

        if ($result === false) {
            return false;
        }

        return !empty($result) ? $result : static::$dispatcher->{$method}(
            "eloquent.{$event}: " . static::class,
            $this
        );
    }

    /**
     * Remove all of the event listeners for the notification.
     *
     * @return void
     */
    public static function flushEventListeners() {
        if (!isset(static::$dispatcher)) {
            return;
        }

        $instance = new static;

        foreach ($instance->getObservableEvents() as $event) {
            static::$dispatcher->forget("notification.{$event}: " . static::class);
        }

        foreach (array_values($instance->dispatchesEvents) as $event) {
            static::$dispatcher->forget($event);
        }
    }

    /**
     * Register a sending notification event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function sending($callback) {
        static::registerNotificationEvent('sending', $callback);
    }

    /**
     * Register a sent notification event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function sent($callback) {
        static::registerNotificationEvent('sent', $callback);
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return CEvent_Dispatcher
     */
    public static function getEventDispatcher() {
        return static::$dispatcher;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param CEvent_Dispatcher $dispatcher
     *
     * @return void
     */
    public static function setEventDispatcher(CEvent_Dispatcher $dispatcher) {
        static::$dispatcher = $dispatcher;
    }

    /**
     * Unset the event dispatcher for models.
     *
     * @return void
     */
    public static function unsetEventDispatcher() {
        static::$dispatcher = null;
    }
}
