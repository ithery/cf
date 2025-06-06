<?php

use Carbon\Carbon;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\NoSuchElementException;

trait CTesting_BrowserConcern_WaitsForElements {
    /**
     * Execute the given callback in a scoped browser once the selector is available.
     *
     * @param string   $selector
     * @param \Closure $callback
     * @param int      $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function whenAvailable($selector, Closure $callback, $seconds = null) {
        return $this->waitFor($selector, $seconds)->with($selector, $callback);
    }

    /**
     * Wait for the given selector to be visible.
     *
     * @param string $selector
     * @param int    $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitFor($selector, $seconds = null) {
        $message = $this->formatTimeOutMessage('Waited %s seconds for selector', $selector);

        return $this->waitUsing($seconds, 100, function () use ($selector) {
            return $this->resolver->findOrFail($selector)->isDisplayed();
        }, $message);
    }

    /**
     * Wait for the given selector to be removed.
     *
     * @param string $selector
     * @param int    $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitUntilMissing($selector, $seconds = null) {
        $message = $this->formatTimeOutMessage('Waited %s seconds for removal of selector', $selector);

        return $this->waitUsing($seconds, 100, function () use ($selector) {
            try {
                $missing = !$this->resolver->findOrFail($selector)->isDisplayed();
            } catch (NoSuchElementException $e) {
                $missing = true;
            }

            return $missing;
        }, $message);
    }

    /**
     * Wait for the given text to be removed.
     *
     * @param string $text
     * @param int    $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitUntilMissingText($text, $seconds = null) {
        $text = carr::wrap($text);

        $message = $this->formatTimeOutMessage('Waited %s seconds for removal of text', implode("', '", $text));

        return $this->waitUsing($seconds, 100, function () use ($text) {
            return !cstr::contains($this->resolver->findOrFail('')->getText(), $text);
        }, $message);
    }

    /**
     * Wait for the given text to be visible.
     *
     * @param array|string $text
     * @param int          $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitForText($text, $seconds = null) {
        $text = carr::wrap($text);

        $message = $this->formatTimeOutMessage('Waited %s seconds for text', implode("', '", $text));

        return $this->waitUsing($seconds, 100, function () use ($text) {
            return cstr::contains($this->resolver->findOrFail('')->getText(), $text);
        }, $message);
    }

    /**
     * Wait for the given text to be visible inside the given selector.
     *
     * @param string       $selector
     * @param array|string $text
     * @param int          $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitForTextIn($selector, $text, $seconds = null) {
        $message = 'Waited %s seconds for text "' . $text . '" in selector ' . $selector;

        return $this->waitUsing($seconds, 100, function () use ($selector, $text) {
            return $this->assertSeeIn($selector, $text);
        }, $message);
    }

    /**
     * Wait for the given link to be visible.
     *
     * @param string $link
     * @param int    $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitForLink($link, $seconds = null) {
        $message = $this->formatTimeOutMessage('Waited %s seconds for link', $link);

        return $this->waitUsing($seconds, 100, function () use ($link) {
            return $this->seeLink($link);
        }, $message);
    }

    /**
     * Wait for the given location.
     *
     * @param string $path
     * @param int    $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitForLocation($path, $seconds = null) {
        $message = $this->formatTimeOutMessage('Waited %s seconds for location', $path);

        return $this->waitUntil("window.location.pathname == '{$path}'", $seconds, $message);
    }

    /**
     * Wait for the given location using a named route.
     *
     * @param string $route
     * @param array  $parameters
     * @param int    $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitForRoute($route, $parameters = [], $seconds = null) {
        return $this->waitForLocation(c::route($route, $parameters, false), $seconds);
    }

    /**
     * Wait until the given script returns true.
     *
     * @param string $script
     * @param int    $seconds
     * @param string $message
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitUntil($script, $seconds = null, $message = null) {
        if (!cstr::startsWith($script, 'return ')) {
            $script = 'return ' . $script;
        }

        if (!cstr::endsWith($script, ';')) {
            $script = $script . ';';
        }

        return $this->waitUsing($seconds, 100, function () use ($script) {
            return $this->driver->executeScript($script);
        }, $message);
    }

    /**
     * Wait until the Vue component's attribute at the given key has the given value.
     *
     * @param string      $key
     * @param string      $value
     * @param null|string $componentSelector
     * @param null|mixed  $seconds
     *
     * @return $this
     */
    public function waitUntilVue($key, $value, $componentSelector = null, $seconds = null) {
        $this->waitUsing($seconds, 100, function () use ($key, $value, $componentSelector) {
            return $value == $this->vueAttribute($componentSelector, $key);
        });

        return $this;
    }

    /**
     * Wait until the Vue component's attribute at the given key does not have the given value.
     *
     * @param string      $key
     * @param string      $value
     * @param null|string $componentSelector
     * @param null|mixed  $seconds
     *
     * @return $this
     */
    public function waitUntilVueIsNot($key, $value, $componentSelector = null, $seconds = null) {
        $this->waitUsing($seconds, 100, function () use ($key, $value, $componentSelector) {
            return $value != $this->vueAttribute($componentSelector, $key);
        });

        return $this;
    }

    /**
     * Wait for a JavaScript dialog to open.
     *
     * @param int $seconds
     *
     * @return $this
     */
    public function waitForDialog($seconds = null) {
        $seconds = is_null($seconds) ? static::$waitSeconds : $seconds;

        $this->driver->wait($seconds, 100)->until(
            WebDriverExpectedCondition::alertIsPresent(),
            "Waited {$seconds} seconds for dialog."
        );

        return $this;
    }

    /**
     * Wait for the current page to reload.
     *
     * @param \Closure $callback
     * @param int      $seconds
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitForReload($callback = null, $seconds = null) {
        $token = cstr::random();

        $this->driver->executeScript("window['{$token}'] = {};");

        if ($callback) {
            $callback($this);
        }

        return $this->waitUsing($seconds, 100, function () use ($token) {
            return $this->driver->executeScript("return typeof window['{$token}'] === 'undefined';");
        }, 'Waited %s seconds for page reload.');
    }

    /**
     * Wait for the given callback to be true.
     *
     * @param int         $seconds
     * @param int         $interval
     * @param \Closure    $callback
     * @param null|string $message
     *
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     *
     * @return $this
     */
    public function waitUsing($seconds, $interval, Closure $callback, $message = null) {
        $seconds = is_null($seconds) ? static::$waitSeconds : $seconds;

        $this->pause($interval);

        $started = Carbon::now();

        while (true) {
            try {
                if ($callback()) {
                    break;
                }
            } catch (Exception $e) {
            }

            if ($started->lt(Carbon::now()->subSeconds($seconds))) {
                throw new TimeoutException(
                    $message
                    ? sprintf($message, $seconds)
                    : "Waited {$seconds} seconds for callback."
                );
            }

            $this->pause($interval);
        }

        return $this;
    }

    /**
     * Prepare custom TimeOutException message for sprintf().
     *
     * @param string $message
     * @param string $expected
     *
     * @return string
     */
    protected function formatTimeOutMessage($message, $expected) {
        return $message . ' [' . str_replace('%', '%%', $expected) . '].';
    }
}
