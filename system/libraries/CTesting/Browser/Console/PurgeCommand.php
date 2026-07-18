<?php

/**
 * Deletes stale debugging artifacts (failure screenshots, console logs,
 * page-source dumps) left behind by previous `phpcf test:browser` runs.
 * `test:browser` already purges these automatically before each run (see
 * BrowserCommand::handle()); this is for manual cleanup outside that flow.
 */
class CTesting_Browser_Console_PurgeCommand extends CConsole_Command {
    /**
     * @var string
     */
    protected $signature = 'test:browser-purge';

    /**
     * @var string
     */
    protected $description = 'Purge browser test debugging files';

    /**
     * @return void
     */
    public function handle() {
        $this->purgeDebuggingFiles($this->testsPath() . 'Browser' . DS . 'screenshots', 'failure-*');
        $this->purgeDebuggingFiles($this->testsPath() . 'Browser' . DS . 'console', '*.log');
        $this->purgeDebuggingFiles($this->testsPath() . 'Browser' . DS . 'source', '*.txt');
    }

    /**
     * @return bool
     */
    protected function isFrameworkContext() {
        return !defined('CFCLI_APPCODE') || CF::cliAppCode() === null;
    }

    /**
     * @return string
     */
    protected function testsPath() {
        return $this->isFrameworkContext()
            ? DOCROOT . 'tests' . DS
            : c::appRoot() . 'default/tests/';
    }

    /**
     * @param string $path
     * @param string $pattern
     *
     * @return void
     */
    protected function purgeDebuggingFiles($path, $pattern) {
        if (!CFile::isDirectory($path)) {
            $this->warn("Skipping missing directory [{$path}].");

            return;
        }

        $count = 0;
        foreach (glob(rtrim($path, DS) . DS . $pattern) as $file) {
            if (@unlink($file)) {
                $count++;
            }
        }

        $this->info("Purged {$count} file(s) matching \"{$pattern}\" from [{$path}].");
    }
}
