<?php

trait CConsole_Trait_InteractsWithSignalsTrait {
    /**
     * The signal registrar instance.
     *
     * @var null|\CConsole_Signals
     */
    protected $signals;

    /**
     * Define a callback to be run when the given signal(s) occurs.
     *
     * @param iterable<array-key, int>|int $signals
     * @param callable                     $callback callable(int $signal): void
     *
     * @return void
     */
    public function trap($signals, $callback) {
        CConsole_Signals::whenAvailable(function () use ($signals, $callback) {
            $this->signals ??= new CConsole_Signals(
                $this->getApplication()->getSignalRegistry(),
            );

            c::collect(carr::wrap($signals))
                ->each(fn ($signal) => $this->signals->register($signal, $callback));
        });
    }

    /**
     * Untrap signal handlers set within the command's handler.
     *
     * @return void
     *
     * @internal
     */
    public function untrap() {
        if (!is_null($this->signals)) {
            $this->signals->unregister();

            $this->signals = null;
        }
    }
}
