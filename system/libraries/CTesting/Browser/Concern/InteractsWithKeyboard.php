<?php

/**
 * NOTE: parseKeys() is deliberately not duplicated here - it already exists
 * on CTesting_Browser_Concern_InteractsWithElements (used by keys()), and
 * CTesting_Browser uses both traits. Redeclaring it here would fatal with a
 * "trait method collision" the moment both traits are combined on one class.
 */
trait CTesting_Browser_Concern_InteractsWithKeyboard {
    /**
     * Execute the given callback while interacting with the keyboard.
     *
     * @param callable(\CTesting_Browser_Keyboard):void $callback
     *
     * @return $this
     */
    public function withKeyboard(callable $callback) {
        return c::tap($this, function () use ($callback) {
            $callback(new CTesting_Browser_Keyboard($this));
        });
    }
}
