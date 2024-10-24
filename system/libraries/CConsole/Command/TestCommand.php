<?php

/**
 * Description of TestCommand.
 *
 * @author Hery
 */
use Dotenv\Parser\Parser;
use Dotenv\Store\StoreBuilder;
use Symfony\Component\Process\Process;
use Dotenv\Exception\InvalidPathException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Exception\ProcessSignaledException;

/**
 * @final
 */
class CConsole_Command_TestCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {phpunitArgs?*} {--without-tty : Disable output to TTY}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the application tests';

    /**
     * The arguments to be used while calling phpunit.
     *
     * @var array
     */
    protected $arguments = [
        '--printer',
        'CTesting_PhpUnit_Printer',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->ignoreValidationErrors();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        if ((int) \PHPUnit\Runner\Version::id()[0] < 9) {
            throw new RuntimeException('Running Collision ^5.0 cf test command requires PHPUnit ^9.0.');
        }
        //$options = array_slice(isset($_SERVER['argv']) ? $_SERVER['argv'] : [], $this->option('without-tty') ? 3 : 2);
        $options = [];
        $phpunitArgs = carr::get($this->input->getArguments(), 'phpunitArgs');

        $commands = array_merge(
            $this->binary(),
            array_merge(
                $this->arguments,
                $this->phpunitArguments($options),
                $this->reformatOptionsPath($phpunitArgs),
            )
        );

        //$this->clearEnv();
        $process = (new Process($commands))->setTimeout(null);

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
     * Get the PHP binary to execute.
     *
     * @return array
     */
    protected function binary() {
        $command = class_exists(\Pest\Laravel\PestServiceProvider::class)
            ? c::fixPath(CF::appDir()) . 'vendor/pestphp/pest/bin/pest'
            : c::fixPath(CF::appDir()) . 'vendor/phpunit/phpunit/phpunit';

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
     * Get the array of arguments for running PHPUnit.
     *
     * @param array $options
     *
     * @return array
     */
    protected function phpunitArguments($options) {
        $options = array_values(array_filter($options, function ($option) {
            return !cstr::startsWith($option, '--env=');
        }));

        if (!file_exists($file = c::fixPath(CF::appDir()) . 'phpunit.xml')) {
            $file = c::fixPath(CF::appDir()) . 'phpunit.xml.dist';
        }
        if (!file_exists($file)) {
            throw new Exception('File not found:' . $file);
        }

        $options = $this->reformatOptionsPath($options);

        return array_merge(['-c', $file], $options);
    }

    protected function reformatOptionsPath(array $options) {
        foreach ($options as $key => $option) {
            $rootPath = c::appRoot();
            $path = $rootPath . 'default/tests/' . $option;
            if (CFile::isDirectory($path) || CFile::isFile($path)) {
                $options[$key] = $path;
            }
        }

        return $options;
    }

    /**
     * Clears any set Environment variables set by Laravel if the --env option is empty.
     *
     * @return void
     */
    protected function clearEnv() {
        if (!$this->option('env')) {
            // $vars = self::getEnvironmentVariables(
            //     // @phpstan-ignore-next-line
            //     $this->laravel->environmentPath(),
            //     // @phpstan-ignore-next-line
            //     $this->laravel->environmentFile()
            // );

            // $repository = CEnv::getRepository();

            // foreach ($vars as $name) {
            //     $repository->clear($name);
            // }
        }
    }

    /**
     * @param string $path
     * @param string $file
     *
     * @return array
     */
    protected static function getEnvironmentVariables($path, $file) {
        try {
            $content = StoreBuilder::createWithNoNames()
                ->addPath($path)
                ->addName($file)
                ->make()
                ->read();
        } catch (InvalidPathException $e) {
            return [];
        }

        $vars = [];

        foreach ((new Parser())->parse($content) as $entry) {
            $vars[] = $entry->getName();
        }

        return $vars;
    }
}
