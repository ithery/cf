<?php

defined('SYSPATH') or die('No direct access allowed.');

final class CFEvent {
    // CFEvent callbacks
    private static $events = [];

    // Cache of events that have been run
    private static $has_run = [];

    // Data that can be processed during events
    public static $data;

    /**
     * Add a callback to an event queue.
     *
     * @param string $name     event name
     * @param array  $callback http://php.net/callback
     *
     * @return bool
     */
    public static function add($name, $callback) {
        if (!is_callable($callback)) {
            trigger_error('Uncallable callback');
        }
        if (!isset(self::$events[$name])) {
            // Create an empty event if it is not yet defined
            self::$events[$name] = [];
        } elseif (in_array($callback, self::$events[$name], true)) {
            // The event already exists
            return false;
        }

        // Add the event
        self::$events[$name][] = $callback;

        return true;
    }

    /**
     * Add a callback to an event queue, before a given event.
     *
     * @param string $name     event name
     * @param array  $existing existing event callback
     * @param array  $callback event callback
     *
     * @return bool
     */
    public static function add_before($name, $existing, $callback) {
        if (empty(self::$events[$name]) or ($key = array_search($existing, self::$events[$name])) === false) {
            // Just add the event if there are no events
            return self::add($name, $callback);
        } else {
            // Insert the event immediately before the existing event
            return self::insert_event($name, $key, $callback);
        }
    }

    /**
     * Add a callback to an event queue, after a given event.
     *
     * @param string $name     event name
     * @param array  $existing existing event callback
     * @param array  $callback event callback
     *
     * @return bool
     */
    public static function add_after($name, $existing, $callback) {
        if (empty(self::$events[$name]) or ($key = array_search($existing, self::$events[$name])) === false) {
            // Just add the event if there are no events
            return self::add($name, $callback);
        } else {
            // Insert the event immediately after the existing event
            return self::insert_event($name, $key + 1, $callback);
        }
    }

    /**
     * Inserts a new event at a specfic key location.
     *
     * @param string $name     event name
     * @param int    $key      key to insert new event at
     * @param array  $callback event callback
     *
     * @return void
     */
    private static function insert_event($name, $key, $callback) {
        if (in_array($callback, self::$events[$name], true)) {
            return false;
        }

        // Add the new event at the given key location
        self::$events[$name] = array_merge(
            // Events before the key
            array_slice(self::$events[$name], 0, $key),
            // New event callback
            [$callback],
            // Events after the key
            array_slice(self::$events[$name], $key)
        );

        return true;
    }

    /**
     * Replaces an event with another event.
     *
     * @param string $name     event name
     * @param array  $existing event to replace
     * @param array  $callback new callback
     *
     * @return bool
     */
    public static function replace($name, $existing, $callback) {
        if (empty(self::$events[$name]) or ($key = array_search($existing, self::$events[$name], true)) === false) {
            return false;
        }

        if (!in_array($callback, self::$events[$name], true)) {
            // Replace the exisiting event with the new event
            self::$events[$name][$key] = $callback;
        } else {
            // Remove the existing event from the queue
            unset(self::$events[$name][$key]);

            // Reset the array so the keys are ordered properly
            self::$events[$name] = array_values(self::$events[$name]);
        }

        return true;
    }

    /**
     * Get all callbacks for an event.
     *
     * @param string $name event name
     *
     * @return array
     */
    public static function get($name) {
        return empty(self::$events[$name]) ? [] : self::$events[$name];
    }

    /**
     * Clear some or all callbacks from an event.
     *
     * @param string $name     event name
     * @param array  $callback specific callback to remove, FALSE for all callbacks
     *
     * @return void
     */
    public static function clear($name, $callback = false) {
        if ($callback === false) {
            self::$events[$name] = [];
        } elseif (isset(self::$events[$name])) {
            // Loop through each of the event callbacks and compare it to the
            // callback requested for removal. The callback is removed if it
            // matches.
            foreach (self::$events[$name] as $i => $event_callback) {
                if ($callback === $event_callback) {
                    unset(self::$events[$name][$i]);
                }
            }
        }
    }

    /**
     * Execute all of the callbacks attached to an event.
     *
     * @param string $name event name
     * @param array  $data data can be processed as CFEvent::$data by the callbacks
     *
     * @return void
     */
    public static function run($name, &$data = null) {
        if (!empty(self::$events[$name])) {
            // So callbacks can access CFEvent::$data
            self::$data = &$data;
            $callbacks = self::get($name);

            foreach ($callbacks as $callback) {
                call_user_func($callback);
            }

            // Do this to prevent data from getting 'stuck'
            $clear_data = '';
            self::$data = &$clear_data;
        }

        // The event has been run!
        self::$has_run[$name] = $name;
    }

    /**
     * Check if a given event has been run.
     *
     * @param string $name event name
     *
     * @return bool
     */
    public static function has_run($name) {
        return isset(self::$has_run[$name]);
    }
}

// End Event
