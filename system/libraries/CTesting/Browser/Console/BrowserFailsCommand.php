<?php

/**
 * Re-runs only the browser tests that failed on the previous test:browser
 * run, stopping at the first new failure - handy while iterating on a fix
 * without waiting for the whole (slow, network-bound) suite each time.
 */
class CTesting_Browser_Console_BrowserFailsCommand extends CTesting_Browser_Console_BrowserCommand {
    /**
     * @var string
     */
    protected $signature = 'test:browser-fails {phpunitArgs?*}
                    {--browse : Open a visible browser instead of using headless mode}
                    {--without-tty : Disable output to TTY}';

    /**
     * @var string
     */
    protected $description = 'Run the failing browser tests from the last run and stop on failure';

    /**
     * @param array $options
     *
     * @return array
     */
    protected function phpunitArguments($options) {
        return array_unique(array_merge(parent::phpunitArguments($options), [
            '--cache-result', '--order-by=defects', '--stop-on-failure',
        ]));
    }
}
