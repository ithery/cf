<?php

/**
 * @internal
 */
class CConsole_Signals {
    /**
     * The signal registry instance.
     *
     * @var \Symfony\Component\Console\SignalRegistry\SignalRegistry
     */
    protected $registry;

    /**
     * The signal registry's previous list of handlers.
     *
     * @var null|array<int, array<int, callable>>
     */
    protected $previousHandlers;

    /**
     * The current availability resolver, if any.
     *
     * @var (callable(): bool)|null
     */
    protected static $availabilityResolver;

    /**
     * Create a new signal registrar instance.
     *
     * @param \Symfony\Component\Console\SignalRegistry\SignalRegistry $registry
     *
     * @return void
     */
    public function __construct($registry) {
        $this->registry = $registry;

        $this->previousHandlers = $this->getHandlers();
    }

    /**
     * Register a new signal handler.
     *
     * @param int      $signal
     * @param callable $callback callable(int $signal): void
     *
     * @return void
     */
    public function register($signal, $callback) {
        $this->previousHandlers[$signal] ??= $this->initializeSignal($signal);

        c::with($this->getHandlers(), function ($handlers) use ($signal) {
            $handlers[$signal] ??= $this->initializeSignal($signal);

            $this->setHandlers($handlers);
        });

        $this->registry->register($signal, $callback);

        c::with($this->getHandlers(), function ($handlers) use ($signal) {
            $lastHandlerInserted = array_pop($handlers[$signal]);

            array_unshift($handlers[$signal], $lastHandlerInserted);

            $this->setHandlers($handlers);
        });
    }

    /**
     * Gets the signal's existing handler in array format.
     *
     * @param mixed $signal
     *
     * @return array<int, callable(int $signal): void>
     */
    protected function initializeSignal($signal) {
        return is_callable($existingHandler = pcntl_signal_get_handler($signal))
            ? [$existingHandler]
            : null;
    }

    /**
     * Unregister the current signal handlers.
     *
     * @return void
     */
    public function unregister() {
        $previousHandlers = $this->previousHandlers;

        foreach ($previousHandlers as $signal => $handler) {
            if (is_null($handler)) {
                pcntl_signal($signal, SIG_DFL);

                unset($previousHandlers[$signal]);
            }
        }

        $this->setHandlers($previousHandlers);
    }

    /**
     * Execute the given callback if "signals" should be used and are available.
     *
     * @param callable $callback
     *
     * @return void
     */
    public static function whenAvailable($callback) {
        $resolver = static::$availabilityResolver;

        if ($resolver()) {
            $callback();
        }
    }

    /**
     * Get the registry's handlers.
     *
     * @return array<int, array<int, callable>>
     */
    protected function getHandlers() {
        return (function () {
            return $this->signalHandlers;
        })->call($this->registry);
    }

    /**
     * Set the registry's handlers.
     *
     * @param array $handlers array<int, array<int, callable(int $signal):void>>
     *
     * @return void
     */
    protected function setHandlers($handlers) {
        (function () use ($handlers) {
            return $this->signalHandlers = $handlers;
        })->call($this->registry);
    }

    /**
     * Set the availability resolver.
     *
     * @param callable $resolver callable(): bool
     *
     * @return void
     */
    public static function resolveAvailabilityUsing($resolver) {
        static::$availabilityResolver = $resolver;
    }
}
