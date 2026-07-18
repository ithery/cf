<?php

trait CConsole_Prompt_Concerns_Events {
    /**
     * The registered event listeners.
     *
     * @var array<string, array<int, Closure>>
     */
    protected $listeners = [];

    /**
     * Register an event listener.
     *
     * @param string  $event
     * @param Closure $callback
     *
     * @return void
     */
    public function on($event, Closure $callback) {
        $this->listeners[$event][] = $callback;
    }

    /**
     * Emit an event.
     *
     * @param string $event
     * @param mixed  ...$data
     *
     * @return void
     */
    public function emit($event, ...$data) {
        foreach (carr::get($this->listeners, $event, []) as $listener) {
            $listener(...$data);
        }
    }

    /**
     * Clean the event listeners.
     *
     * @return void
     */
    public function clearListeners() {
        $this->listeners = [];
    }
}
