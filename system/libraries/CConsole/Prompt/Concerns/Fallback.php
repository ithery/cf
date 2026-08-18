<?php

trait CConsole_Prompt_Concerns_Fallback {
    /**
     * Whether to fallback to a custom implementation.
     *
     * @var bool
     */
    protected static $shouldFallback = false;

    /**
     * The fallback implementations.
     *
     * @var array<class-string, Closure($this): mixed>
     */
    protected static $fallbacks = [];

    /**
     * Enable the fallback implementation.
     *
     * @param bool $condition
     *
     * @return void
     */
    public static function fallbackWhen($condition) {
        static::$shouldFallback = $condition || static::$shouldFallback;
    }

    /**
     * Whether the prompt should fallback to a custom implementation.
     *
     * @return bool
     */
    public static function shouldFallback() {
        return static::$shouldFallback && isset(static::$fallbacks[static::class]);
    }

    /**
     * Set the fallback implementation.
     *
     * @param Closure $fallback Closure($this): mixed
     *
     * @return void
     */
    public static function fallbackUsing(Closure $fallback) {
        static::$fallbacks[static::class] = $fallback;
    }

    /**
     * Call the registered fallback implementation.
     *
     * @return mixed
     */
    public function fallback() {
        $fallback = carr::get(static::$fallbacks, static::class);

        if ($fallback === null) {
            throw new RuntimeException('No fallback implementation registered for [' . static::class . ']');
        }

        return $fallback($this);
    }
}
