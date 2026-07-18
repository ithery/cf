<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Exception\ProcessSignaledException;

/**
 * Runs the real-browser (Dusk-style) test suite under default/tests/Web,
 * ported from Laravel Dusk's DuskCommand (originally pasted in here as an
 * unmodified Laravel copy - wrong namespace, extended
 * Illuminate\Console\Command, and depended on Dotenv/Collision/base_path()/
 * collect(), none of which exist here).
 *
 * Kept out of the plain `test` command (CConsole_Command_TestCommand) on
 * purpose - browser tests spawn chromedriver and hit a real server over the
 * network, so they're slow and shouldn't run on every routine `phpcf test`.
 */
class CTesting_Browser_Console_BrowserCommand extends CConsole_Command {
    /**
     * @var string
     */
    protected $signature = 'test:browser {phpunitArgs?*}
                    {--browse : Open a visible browser instead of using headless mode}
                    {--without-tty : Disable output to TTY}';

    /**
     * @var string
     */
    protected $description = 'Run the application browser (Dusk-style) tests under tests/Web';

    /**
     * @var array
     */
    protected $arguments = [
        '--printer',
        'CTesting_PhpUnit_Printer',
    ];

    public function __construct() {
        parent::__construct();

        $this->ignoreValidationErrors();
    }

    /**
     * @return mixed
     */
    public function handle() {
        if ((int) \PHPUnit\Runner\Version::id()[0] < 9) {
            throw new RuntimeException('Running test:browser command requires PHPUnit ^9.0.');
        }

        $this->purgeDebuggingFiles($this->testsPath() . 'Browser' . DS . 'screenshots', 'failure-*');
        $this->purgeDebuggingFiles($this->testsPath() . 'Browser' . DS . 'console', '*.log');
        $this->purgeDebuggingFiles($this->testsPath() . 'Browser' . DS . 'source', '*.txt');

        $options = [];
        $phpunitArgs = carr::get($this->input->getArguments(), 'phpunitArgs') ?: [];

        // If the caller didn't point at a specific file/directory (only gave
        // options like --filter=, or nothing at all), default to the whole
        // Web/ suite so `phpcf test:browser` alone does something useful.
        $hasPositionalPath = (bool) array_filter($phpunitArgs, function ($arg) {
            return !cstr::startsWith($arg, '--');
        });

        if (!$hasPositionalPath) {
            $phpunitArgs[] = 'Web';
        }

        $commands = array_merge(
            $this->binary(),
            array_merge(
                $this->arguments,
                $this->phpunitArguments($options),
                $this->reformatOptionsPath($phpunitArgs)
            )
        );

        $process = (new Process($commands, null, $this->env()))->setTimeout(null);

        try {
            $process->setTty(!$this->option('without-tty'));
        } catch (RuntimeException $e) {
            $this->output->writeln('Warning: ' . $e->getMessage());
        }

        try {
            return $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }
    }

    /**
     * @return bool
     */
    protected function isFrameworkContext() {
        return !defined('CFCLI_APPCODE') || CF::cliAppCode() === null;
    }

    /**
     * Absolute path to this app's (or the framework's) tests/ directory,
     * with a trailing separator.
     *
     * @return string
     */
    protected function testsPath() {
        return $this->isFrameworkContext()
            ? DOCROOT . 'tests' . DS
            : c::appRoot() . 'default/tests/';
    }

    /**
     * Environment variables passed to the phpunit process. --browse maps to
     * DUSK_HEADLESS_DISABLED, read by AbstractBrowserTestCase::driver() to
     * decide whether to add the --headless chrome argument.
     *
     * @return array
     */
    protected function env() {
        $variables = [];

        if ($this->option('browse')) {
            $variables['DUSK_HEADLESS_DISABLED'] = 1;
        }

        return $variables;
    }

    /**
     * Delete stale debugging artifacts from a previous run so failures from
     * this run aren't mixed up with old ones.
     *
     * @param string $path
     * @param string $pattern
     *
     * @return void
     */
    protected function purgeDebuggingFiles($path, $pattern) {
        if (!CFile::isDirectory($path)) {
            return;
        }

        foreach (glob(rtrim($path, DS) . DS . $pattern) as $file) {
            @unlink($file);
        }
    }

    /**
     * @return array
     */
    protected function binary() {
        $command = DOCROOT . '.bin' . DS . 'phpunit' . DS . 'phpunit';

        if ('phpdbg' === PHP_SAPI) {
            return [$this->getPhpBinary(), '-qrr', $command];
        }

        return [$this->getPhpBinary(), $command];
    }

    /**
     * @return false|string
     */
    protected function getPhpBinary() {
        $executableFinder = new PhpExecutableFinder();

        return $executableFinder->find();
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function phpunitArguments($options) {
        $options = array_values(array_filter($options, function ($option) {
            return !cstr::startsWith($option, '--env=');
        }));

        $basePath = $this->isFrameworkContext() ? c::fixPath(DOCROOT) : c::fixPath(CF::appDir());
        if (!file_exists($file = $basePath . 'phpunit.xml')) {
            $file = $basePath . 'phpunit.xml.dist';
        }
        if (!file_exists($file)) {
            throw new Exception('File not found:' . $file);
        }

        $options = $this->reformatOptionsPath($options);

        return array_merge(['-c', $file], $options);
    }

    /**
     * Resolve options against tests/Web/ first (so a bare "AuthTest.php"
     * finds tests/Web/AuthTest.php), falling back to the plain tests/
     * resolution for the injected "Web" directory itself.
     *
     * @param array $options
     *
     * @return array
     */
    protected function reformatOptionsPath(array $options) {
        $testsPath = $this->testsPath();
        $webPath = $testsPath . 'Web' . DS;

        foreach ($options as $key => $option) {
            $pathInWeb = $webPath . $option;
            if (CFile::isDirectory($pathInWeb) || CFile::isFile($pathInWeb)) {
                $options[$key] = $pathInWeb;

                continue;
            }

            $path = $testsPath . $option;
            if (CFile::isDirectory($path) || CFile::isFile($path)) {
                $options[$key] = $path;
            }
        }

        return $options;
    }
}
