<?php

class CBase_Defer_DeferredCallback {
    public $callback;

    /**
     * @var null|string
     */
    public $name;

    /**
     * @var bool
     */
    public $always = false;

    /**
     * Create a new deferred callback instance.
     *
     * @param callable $callback
     *
     * @return void
     */
    public function __construct($callback, string $name = null, bool $always = false) {
        $this->callback = $callback;
        $this->name = $name ?? (string) cstr::uuid();
        $this->always = $always;
    }

    /**
     * Specify the name of the deferred callback so it can be cancelled later.
     *
     * @param string $name
     *
     * @return $this
     */
    public function name(string $name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Indicate that the deferred callback should run even on unsuccessful requests and jobs.
     *
     * @param bool $always
     *
     * @return $this
     */
    public function always(bool $always = true) {
        $this->always = $always;

        return $this;
    }

    /**
     * Invoke the deferred callback.
     *
     * @return void
     */
    public function __invoke() {
        call_user_func($this->callback);
    }
}
