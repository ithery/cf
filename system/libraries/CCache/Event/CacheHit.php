<?php

class CCache_Event_CacheHit extends CCache_EventAbstract {
    //
    /**
     * The value that was retrieved.
     *
     * @var mixed
     */
    public $value;

    /**
     * Create a new event instance.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $tags
     *
     * @return void
     */
    public function __construct($key, $value, array $tags = []) {
        parent::__construct($key, $tags);

        $this->value = $value;
    }
}