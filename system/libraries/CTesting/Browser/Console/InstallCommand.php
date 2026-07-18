<?php

/**
 * Scaffolds real-browser (Dusk-style) testing for this app: creates
 * default/tests/AbstractBrowserTestCase.php (edit its baseUrl() to point at
 * the app's dev/staging domain), a starter default/tests/Web/ExampleTest.php,
 * the Browser/{screenshots,console,source} debugging directories test:browser
 * writes to, and downloads a matching chromedriver binary.
 */
class CTesting_Browser_Console_InstallCommand extends CConsole_Command {
    /**
     * @var string
     */
    protected $signature = 'test:browser-install
                    {--proxy= : The proxy to download the binary through (example: "tcp://127.0.0.1:9000")}
                    {--ssl-no-verify : Bypass SSL certificate verification when installing through a proxy}';

    /**
     * @var string
     */
    protected $description = 'Install browser (Dusk-style) testing scaffolding into this app';

    /**
     * @return void
     */
    public function handle() {
        $testsPath = c::appRoot() . 'default/tests/';

        if (!CFile::isDirectory($testsPath . 'Web')) {
            CFile::makeDirectory($testsPath . 'Web', 0755, true);
        }

        $this->createDebuggingDirectory($testsPath . 'Browser/screenshots');
        $this->createDebuggingDirectory($testsPath . 'Browser/console');
        $this->createDebuggingDirectory($testsPath . 'Browser/source');

        $this->copyStubIfMissing('tests/browser/AbstractBrowserTestCase', $testsPath . 'AbstractBrowserTestCase.php');
        $this->copyStubIfMissing('tests/browser/ExampleTest', $testsPath . 'Web/ExampleTest.php');

        $this->info('Browser test scaffolding installed successfully.');

        $driverCommandArgs = [];
        if ($this->option('proxy')) {
            $driverCommandArgs['--proxy'] = $this->option('proxy');
        }
        if ($this->option('ssl-no-verify')) {
            $driverCommandArgs['--ssl-no-verify'] = true;
        }

        $this->call('test:chrome-driver', $driverCommandArgs);
    }

    /**
     * @param string $path
     *
     * @return void
     */
    protected function createDebuggingDirectory($path) {
        if (CFile::isDirectory($path)) {
            return;
        }

        CFile::makeDirectory($path, 0755, true);
        CFile::put($path . DS . '.gitignore', "*\n!.gitignore\n");
    }

    /**
     * @param string $stub        stub path relative to a "stubs" root, without extension (matches CF::findFile's convention)
     * @param string $destination
     *
     * @return void
     */
    protected function copyStubIfMissing($stub, $destination) {
        if (CFile::isFile($destination)) {
            $this->warn("Skipping [{$destination}], it already exists.");

            return;
        }

        $stubFile = CF::findFile('stubs', $stub, true, 'stub');
        CFile::copy($stubFile, $destination);
    }
}
