<?php

/**
 * @mixin \CTesting_Browser
 */
trait CTesting_Browser_Concern_InteractsWithJavascript {
    /**
     * Execute JavaScript within the browser.
     *
     * @param string|array $scripts
     *
     * @return array
     */
    public function script($scripts) {
        return c::collect((array) $scripts)->map(function ($script) {
            return $this->driver->executeScript($script);
        })->all();
    }
}
