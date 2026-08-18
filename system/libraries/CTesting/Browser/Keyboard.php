<?php

/**
 * @mixin \Facebook\WebDriver\Remote\RemoteKeyboard
 */
class CTesting_Browser_Keyboard {
    use CTrait_Macroable {
        __call as macroCall;
    }

    /**
     * The browser instance.
     *
     * @var \CTesting_Browser
     */
    public $browser;

    /**
     * Create a keyboard instance.
     *
     * @param \CTesting_Browser $browser
     *
     * @return void
     */
    public function __construct(CTesting_Browser $browser) {
        $this->browser = $browser;
    }

    /**
     * Press the key using keyboard.
     *
     * @param mixed $key
     *
     * @return $this
     */
    public function press($key) {
        $this->pressKey($key);

        return $this;
    }

    /**
     * Release the given pressed key.
     *
     * @param mixed $key
     *
     * @return $this
     */
    public function release($key) {
        $this->releaseKey($key);

        return $this;
    }

    /**
     * Type the given keys using keyboard.
     *
     * @param string|array<int, string> $keys
     *
     * @return $this
     */
    public function type($keys) {
        $this->sendKeys($keys);

        return $this;
    }

    /**
     * Pause for the given amount of milliseconds.
     *
     * @param int $milliseconds
     *
     * @return $this
     */
    public function pause($milliseconds) {
        $this->browser->pause($milliseconds);

        return $this;
    }

    /**
     * Dynamically call a method on the keyboard.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $keyboard = $this->browser->driver->getKeyboard();

        if (method_exists($keyboard, $method)) {
            $response = $keyboard->{$method}(...$parameters);

            if ($response === $keyboard) {
                return $this;
            } else {
                return $response;
            }
        }

        throw new BadMethodCallException("Call to undefined keyboard method [{$method}].");
    }
}
