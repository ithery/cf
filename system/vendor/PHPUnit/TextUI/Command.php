<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI;

use Throwable;
use const STDIN;
use const PHP_EOL;
use function copy;
use function sort;
use function trim;
use function fgets;
use function assert;
use function getcwd;
use function is_dir;
use function printf;
use function strpos;
use ReflectionClass;
use function ini_get;
use function ini_set;
use function is_file;
use function sprintf;
use function realpath;
use function is_string;
use function array_keys;
use const PATH_SEPARATOR;
use function is_callable;
use PHPUnit\Util\Printer;
use function class_exists;
use PHPUnit\Runner\Version;
use PHPUnit\Util\FileLoader;
use PHPUnit\Util\Filesystem;
use function version_compare;
use function extension_loaded;
use function file_get_contents;
use function file_put_contents;
use PHPUnit\Framework\TestSuite;
use SebastianBergmann\Timer\Timer;
use PharIo\Manifest\ManifestLoader;
use PHPUnit\Runner\TestSuiteLoader;
use PharIo\Manifest\ApplicationName;
use PHPUnit\Util\Xml\SchemaDetector;
use PHPUnit\Util\XmlTestListRenderer;
use PHPUnit\Util\TextTestListRenderer;
use PHPUnit\TextUI\CliArguments\Mapper;
use PHPUnit\TextUI\Exception\Exception;
use PHPUnit\TextUI\CliArguments\Builder;
use function stream_resolve_include_path;
use SebastianBergmann\CodeCoverage\Filter;
use PHPUnit\Runner\StandardTestSuiteLoader;
use PHPUnit\TextUI\XmlConfiguration\Loader;
use PharIo\Version\Version as PharIoVersion;
use PHPUnit\TextUI\CliArguments\Configuration;
use PHPUnit\TextUI\Exception\RuntimeException;
use PHPUnit\TextUI\XmlConfiguration\Generator;
use PHPUnit\TextUI\Exception\ReflectionException;
use PharIo\Manifest\Exception as ManifestException;
use PHPUnit\TextUI\XmlConfiguration\PHP\PhpHandler;
use PHPUnit\TextUI\XmlConfiguration\Migration\Migrator;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\FilterMapper;
use SebastianBergmann\CodeCoverage\StaticAnalysis\CacheWarmer;
use PHPUnit\TextUI\CliArguments\Exception as ArgumentsException;
use SebastianBergmann\FileIterator\Facade as FileIteratorFacade;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
class Command {
    /**
     * @var array<string,mixed>
     */
    protected $arguments = [];

    /**
     * @var array<string,mixed>
     */
    protected $longOptions = [];

    /**
     * @var bool
     */
    private $versionStringPrinted = false;

    /**
     * @psalm-var list<string>
     */
    private $warnings = [];

    /**
     * @param mixed $exit
     *
     * @throws Exception
     */
    public static function main($exit = true) {
        try {
            return (new static())->run($_SERVER['argv'], $exit);
        } catch (Throwable $t) {
            throw new RuntimeException(
                $t->getMessage(),
                (int) $t->getCode(),
                $t
            );
        }
    }

    /**
     * @param mixed $exit
     *
     * @throws Exception
     */
    public function run(array $argv, $exit = true) {
        $this->handleArguments($argv);

        $runner = $this->createRunner();

        if ($this->arguments['test'] instanceof TestSuite) {
            $suite = $this->arguments['test'];
        } else {
            $suite = $runner->getTest(
                $this->arguments['test'],
                $this->arguments['testSuffixes']
            );
        }

        if ($this->arguments['listGroups']) {
            return $this->handleListGroups($suite, $exit);
        }

        if ($this->arguments['listSuites']) {
            return $this->handleListSuites($exit);
        }

        if ($this->arguments['listTests']) {
            return $this->handleListTests($suite, $exit);
        }

        if ($this->arguments['listTestsXml']) {
            return $this->handleListTestsXml($suite, $this->arguments['listTestsXml'], $exit);
        }

        unset($this->arguments['test'], $this->arguments['testFile']);

        try {
            $result = $runner->run($suite, $this->arguments, $this->warnings, $exit);
        } catch (Throwable $t) {
            print $t->getMessage() . PHP_EOL;
        }

        $return = TestRunner::FAILURE_EXIT;

        if (isset($result) && $result->wasSuccessful()) {
            $return = TestRunner::SUCCESS_EXIT;
        } elseif (!isset($result) || $result->errorCount() > 0) {
            $return = TestRunner::EXCEPTION_EXIT;
        }

        if ($exit) {
            exit($return);
        }

        return $return;
    }

    /**
     * Create a TestRunner, override in subclasses.
     */
    protected function createRunner() {
        return new TestRunner($this->arguments['loader']);
    }

    /**
     * Handles the command-line arguments.
     *
     * A child class of PHPUnit\TextUI\Command can hook into the argument
     * parsing by adding the switch(es) to the $longOptions array and point to a
     * callback method that handles the switch(es) in the child class like this
     *
     * <code>
     * <?php
     * class MyCommand extends PHPUnit\TextUI\Command
     * {
     *     public function __construct()
     *     {
     *         // my-switch won't accept a value, it's an on/off
     *         $this->longOptions['my-switch'] = 'myHandler';
     *         // my-secondswitch will accept a value - note the equals sign
     *         $this->longOptions['my-secondswitch='] = 'myOtherHandler';
     *     }
     *
     *     // --my-switch  -> myHandler()
     *     protected function myHandler()
     *     {
     *     }
     *
     *     // --my-secondswitch foo -> myOtherHandler('foo')
     *     protected function myOtherHandler ($value)
     *     {
     *     }
     *
     *     // You will also need this - the static keyword in the
     *     // PHPUnit\TextUI\Command will mean that it'll be
     *     // PHPUnit\TextUI\Command that gets instantiated,
     *     // not MyCommand
     *     public static function main($exit = true)
     *     {
     *         $command = new static;
     *
     *         return $command->run($_SERVER['argv'], $exit);
     *     }
     *
     * }
     * </code>
     *
     * @throws Exception
     */
    protected function handleArguments(array $argv) {
        try {
            $arguments = (new Builder())->fromParameters($argv, array_keys($this->longOptions));
        } catch (ArgumentsException $e) {
            $this->exitWithErrorMessage($e->getMessage());
        }

        assert(isset($arguments) && $arguments instanceof Configuration);

        if ($arguments->hasGenerateConfiguration() && $arguments->generateConfiguration()) {
            $this->generateConfiguration();
        }

        if ($arguments->hasAtLeastVersion()) {
            if (version_compare(Version::id(), $arguments->atLeastVersion(), '>=')) {
                exit(TestRunner::SUCCESS_EXIT);
            }

            exit(TestRunner::FAILURE_EXIT);
        }

        if ($arguments->hasVersion() && $arguments->version()) {
            $this->printVersionString();

            exit(TestRunner::SUCCESS_EXIT);
        }

        if ($arguments->hasCheckVersion() && $arguments->checkVersion()) {
            $this->handleVersionCheck();
        }

        if ($arguments->hasHelp()) {
            $this->showHelp();

            exit(TestRunner::SUCCESS_EXIT);
        }

        if ($arguments->hasUnrecognizedOrderBy()) {
            $this->exitWithErrorMessage(
                sprintf(
                    'unrecognized --order-by option: %s',
                    $arguments->unrecognizedOrderBy()
                )
            );
        }

        if ($arguments->hasIniSettings()) {
            foreach ($arguments->iniSettings() as $name => $value) {
                ini_set($name, $value);
            }
        }

        if ($arguments->hasIncludePath()) {
            ini_set(
                'include_path',
                $arguments->includePath() . PATH_SEPARATOR . ini_get('include_path')
            );
        }

        $this->arguments = (new Mapper())->mapToLegacyArray($arguments);

        $this->handleCustomOptions($arguments->unrecognizedOptions());
        $this->handleCustomTestSuite();

        if (!isset($this->arguments['testSuffixes'])) {
            $this->arguments['testSuffixes'] = ['Test.php', '.phpt'];
        }

        if (!isset($this->arguments['test']) && $arguments->hasArgument()) {
            $this->arguments['test'] = realpath($arguments->argument());

            if ($this->arguments['test'] === false) {
                $this->exitWithErrorMessage(
                    sprintf(
                        'Cannot open file "%s".',
                        $arguments->argument()
                    )
                );
            }
        }

        if ($this->arguments['loader'] !== null) {
            $this->arguments['loader'] = $this->handleLoader($this->arguments['loader']);
        }

        if (isset($this->arguments['configuration'])) {
            if (is_dir($this->arguments['configuration'])) {
                $candidate = $this->configurationFileInDirectory($this->arguments['configuration']);

                if ($candidate !== null) {
                    $this->arguments['configuration'] = $candidate;
                }
            }
        } elseif ($this->arguments['useDefaultConfiguration']) {
            $candidate = $this->configurationFileInDirectory(getcwd());

            if ($candidate !== null) {
                $this->arguments['configuration'] = $candidate;
            }
        }

        if ($arguments->hasMigrateConfiguration() && $arguments->migrateConfiguration()) {
            if (!isset($this->arguments['configuration'])) {
                print 'No configuration file found to migrate.' . PHP_EOL;

                exit(TestRunner::EXCEPTION_EXIT);
            }

            $this->migrateConfiguration(realpath($this->arguments['configuration']));
        }

        if (isset($this->arguments['configuration'])) {
            try {
                $this->arguments['configurationObject'] = (new Loader())->load($this->arguments['configuration']);
            } catch (Throwable $e) {
                print $e->getMessage() . PHP_EOL;

                exit(TestRunner::FAILURE_EXIT);
            }

            $phpunitConfiguration = $this->arguments['configurationObject']->phpunit();

            (new PhpHandler())->handle($this->arguments['configurationObject']->php());

            if (isset($this->arguments['bootstrap'])) {
                $this->handleBootstrap($this->arguments['bootstrap']);
            } elseif ($phpunitConfiguration->hasBootstrap()) {
                $this->handleBootstrap($phpunitConfiguration->bootstrap());
            }

            if (!isset($this->arguments['stderr'])) {
                $this->arguments['stderr'] = $phpunitConfiguration->stderr();
            }

            if (!isset($this->arguments['noExtensions']) && $phpunitConfiguration->hasExtensionsDirectory() && extension_loaded('phar')) {
                $this->handleExtensions($phpunitConfiguration->extensionsDirectory());
            }

            if (!isset($this->arguments['columns'])) {
                $this->arguments['columns'] = $phpunitConfiguration->columns();
            }

            if (!isset($this->arguments['printer']) && $phpunitConfiguration->hasPrinterClass()) {
                $file = $phpunitConfiguration->hasPrinterFile() ? $phpunitConfiguration->printerFile() : '';

                $this->arguments['printer'] = $this->handlePrinter(
                    $phpunitConfiguration->printerClass(),
                    $file
                );
            }

            if ($phpunitConfiguration->hasTestSuiteLoaderClass()) {
                $file = $phpunitConfiguration->hasTestSuiteLoaderFile() ? $phpunitConfiguration->testSuiteLoaderFile() : '';

                $this->arguments['loader'] = $this->handleLoader(
                    $phpunitConfiguration->testSuiteLoaderClass(),
                    $file
                );
            }

            if (!isset($this->arguments['testsuite']) && $phpunitConfiguration->hasDefaultTestSuite()) {
                $this->arguments['testsuite'] = $phpunitConfiguration->defaultTestSuite();
            }

            if (!isset($this->arguments['test'])) {
                try {
                    $this->arguments['test'] = (new TestSuiteMapper())->map(
                        $this->arguments['configurationObject']->testSuite(),
                        isset($this->arguments['testsuite']) ? $this->arguments['testsuite'] : ''
                    );
                } catch (Exception $e) {
                    $this->printVersionString();

                    print $e->getMessage() . PHP_EOL;

                    exit(TestRunner::EXCEPTION_EXIT);
                }
            }
        } elseif (isset($this->arguments['bootstrap'])) {
            $this->handleBootstrap($this->arguments['bootstrap']);
        }

        if (isset($this->arguments['printer']) && is_string($this->arguments['printer'])) {
            $this->arguments['printer'] = $this->handlePrinter($this->arguments['printer']);
        }

        if (isset($this->arguments['configurationObject'], $this->arguments['warmCoverageCache'])) {
            $this->handleWarmCoverageCache($this->arguments['configurationObject']);
        }

        if (!isset($this->arguments['test'])) {
            $this->showHelp();

            exit(TestRunner::EXCEPTION_EXIT);
        }
    }

    /**
     * Handles the loading of the PHPUnit\Runner\TestSuiteLoader implementation.
     *
     * @deprecated see https://github.com/sebastianbergmann/phpunit/issues/4039
     *
     * @param mixed $loaderClass
     * @param mixed $loaderFile
     */
    protected function handleLoader($loaderClass, $loaderFile = '') {
        $this->warnings[] = 'Using a custom test suite loader is deprecated';

        if (!class_exists($loaderClass, false)) {
            if ($loaderFile == '') {
                $loaderFile = Filesystem::classNameToFilename(
                    $loaderClass
                );
            }

            $loaderFile = stream_resolve_include_path($loaderFile);

            if ($loaderFile) {
                require $loaderFile;
            }
        }

        if (class_exists($loaderClass, false)) {
            try {
                $class = new ReflectionClass($loaderClass);
                // @codeCoverageIgnoreStart
            } catch (\ReflectionException $e) {
                throw new ReflectionException(
                    $e->getMessage(),
                    (int) $e->getCode(),
                    $e
                );
            }
            // @codeCoverageIgnoreEnd

            if ($class->implementsInterface(TestSuiteLoader::class) && $class->isInstantiable()) {
                $object = $class->newInstance();

                assert($object instanceof TestSuiteLoader);

                return $object;
            }
        }

        if ($loaderClass == StandardTestSuiteLoader::class) {
            return null;
        }

        $this->exitWithErrorMessage(
            sprintf(
                'Could not use "%s" as loader.',
                $loaderClass
            )
        );

        return null;
    }

    /**
     * Handles the loading of the PHPUnit\Util\Printer implementation.
     *
     * @param mixed $printerClass
     * @param mixed $printerFile
     *
     * @return null|Printer|string
     */
    protected function handlePrinter($printerClass, $printerFile = '') {
        if (!class_exists($printerClass, false)) {
            if ($printerFile === '') {
                $printerFile = Filesystem::classNameToFilename(
                    $printerClass
                );
            }

            $printerFile = stream_resolve_include_path($printerFile);

            if ($printerFile) {
                require $printerFile;
            }
        }

        if (!class_exists($printerClass)) {
            $this->exitWithErrorMessage(
                sprintf(
                    'Could not use "%s" as printer: class does not exist',
                    $printerClass
                )
            );
        }

        try {
            $class = new ReflectionClass($printerClass);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
            // @codeCoverageIgnoreEnd
        }

        if (!$class->implementsInterface(ResultPrinter::class)) {
            $this->exitWithErrorMessage(
                sprintf(
                    'Could not use "%s" as printer: class does not implement %s',
                    $printerClass,
                    ResultPrinter::class
                )
            );
        }

        if (!$class->isInstantiable()) {
            $this->exitWithErrorMessage(
                sprintf(
                    'Could not use "%s" as printer: class cannot be instantiated',
                    $printerClass
                )
            );
        }

        if ($class->isSubclassOf(ResultPrinter::class)) {
            return $printerClass;
        }

        $outputStream = isset($this->arguments['stderr']) ? 'php://stderr' : null;

        return $class->newInstance($outputStream);
    }

    /**
     * Loads a bootstrap file.
     *
     * @param mixed $filename
     */
    protected function handleBootstrap($filename) {
        try {
            FileLoader::checkAndLoad($filename);
        } catch (Throwable $t) {
            $this->exitWithErrorMessage($t->getMessage());
        }
    }

    protected function handleVersionCheck() {
        $this->printVersionString();

        $latestVersion = file_get_contents('https://phar.phpunit.de/latest-version-of/phpunit');
        $isOutdated = version_compare($latestVersion, Version::id(), '>');

        if ($isOutdated) {
            printf(
                'You are not using the latest version of PHPUnit.' . PHP_EOL
                . 'The latest version is PHPUnit %s.' . PHP_EOL,
                $latestVersion
            );
        } else {
            print 'You are using the latest version of PHPUnit.' . PHP_EOL;
        }

        exit(TestRunner::SUCCESS_EXIT);
    }

    /**
     * Show the help message.
     */
    protected function showHelp() {
        $this->printVersionString();
        (new Help())->writeToConsole();
    }

    /**
     * Custom callback for test suite discovery.
     */
    protected function handleCustomTestSuite() {
    }

    private function printVersionString() {
        if ($this->versionStringPrinted) {
            return;
        }

        print Version::getVersionString() . PHP_EOL . PHP_EOL;

        $this->versionStringPrinted = true;
    }

    private function exitWithErrorMessage($message) {
        $this->printVersionString();

        print $message . PHP_EOL;

        exit(TestRunner::FAILURE_EXIT);
    }

    private function handleExtensions($directory) {
        foreach ((new FileIteratorFacade())->getFilesAsArray($directory, '.phar') as $file) {
            if (!is_file('phar://' . $file . '/manifest.xml')) {
                $this->arguments['notLoadedExtensions'][] = $file . ' is not an extension for PHPUnit';

                continue;
            }

            try {
                $applicationName = new ApplicationName('phpunit/phpunit');
                $version = new PharIoVersion(Version::series());
                $manifest = ManifestLoader::fromFile('phar://' . $file . '/manifest.xml');

                if (!$manifest->isExtensionFor($applicationName)) {
                    $this->arguments['notLoadedExtensions'][] = $file . ' is not an extension for PHPUnit';

                    continue;
                }

                if (!$manifest->isExtensionFor($applicationName, $version)) {
                    $this->arguments['notLoadedExtensions'][] = $file . ' is not compatible with this version of PHPUnit';

                    continue;
                }
            } catch (ManifestException $e) {
                $this->arguments['notLoadedExtensions'][] = $file . ': ' . $e->getMessage();

                continue;
            }

            require $file;

            $this->arguments['loadedExtensions'][] = $manifest->getName()->asString() . ' ' . $manifest->getVersion()->getVersionString();
        }
    }

    private function handleListGroups(TestSuite $suite, $exit) {
        $this->printVersionString();

        print 'Available test group(s):' . PHP_EOL;

        $groups = $suite->getGroups();
        sort($groups);

        foreach ($groups as $group) {
            if (strpos($group, '__phpunit_') === 0) {
                continue;
            }

            printf(
                ' - %s' . PHP_EOL,
                $group
            );
        }

        if ($exit) {
            exit(TestRunner::SUCCESS_EXIT);
        }

        return TestRunner::SUCCESS_EXIT;
    }

    /**
     * @param mixed $exit
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\TextUI\XmlConfiguration\Exception
     */
    private function handleListSuites($exit) {
        $this->printVersionString();

        print 'Available test suite(s):' . PHP_EOL;

        foreach ($this->arguments['configurationObject']->testSuite() as $testSuite) {
            printf(
                ' - %s' . PHP_EOL,
                $testSuite->name()
            );
        }

        if ($exit) {
            exit(TestRunner::SUCCESS_EXIT);
        }

        return TestRunner::SUCCESS_EXIT;
    }

    /**
     * @param mixed $exit
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    private function handleListTests(TestSuite $suite, $exit) {
        $this->printVersionString();

        $renderer = new TextTestListRenderer();

        print $renderer->render($suite);

        if ($exit) {
            exit(TestRunner::SUCCESS_EXIT);
        }

        return TestRunner::SUCCESS_EXIT;
    }

    /**
     * @param mixed $target
     * @param mixed $exit
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    private function handleListTestsXml(TestSuite $suite, $target, $exit) {
        $this->printVersionString();

        $renderer = new XmlTestListRenderer();

        file_put_contents($target, $renderer->render($suite));

        printf(
            'Wrote list of tests that would have been run to %s' . PHP_EOL,
            $target
        );

        if ($exit) {
            exit(TestRunner::SUCCESS_EXIT);
        }

        return TestRunner::SUCCESS_EXIT;
    }

    private function generateConfiguration() {
        $this->printVersionString();

        print 'Generating phpunit.xml in ' . getcwd() . PHP_EOL . PHP_EOL;
        print 'Bootstrap script (relative to path shown above; default: vendor/autoload.php): ';

        $bootstrapScript = trim(fgets(STDIN));

        print 'Tests directory (relative to path shown above; default: tests): ';

        $testsDirectory = trim(fgets(STDIN));

        print 'Source directory (relative to path shown above; default: src): ';

        $src = trim(fgets(STDIN));

        print 'Cache directory (relative to path shown above; default: .phpunit.cache): ';

        $cacheDirectory = trim(fgets(STDIN));

        if ($bootstrapScript === '') {
            $bootstrapScript = 'vendor/autoload.php';
        }

        if ($testsDirectory === '') {
            $testsDirectory = 'tests';
        }

        if ($src === '') {
            $src = 'src';
        }

        if ($cacheDirectory === '') {
            $cacheDirectory = '.phpunit.cache';
        }

        $generator = new Generator();

        file_put_contents(
            'phpunit.xml',
            $generator->generateDefaultConfiguration(
                Version::series(),
                $bootstrapScript,
                $testsDirectory,
                $src,
                $cacheDirectory
            )
        );

        print PHP_EOL . 'Generated phpunit.xml in ' . getcwd() . '.' . PHP_EOL;
        print 'Make sure to exclude the ' . $cacheDirectory . ' directory from version control.' . PHP_EOL;

        exit(TestRunner::SUCCESS_EXIT);
    }

    private function migrateConfiguration($filename) {
        $this->printVersionString();

        if (!(new SchemaDetector())->detect($filename)->detected()) {
            print $filename . ' does not need to be migrated.' . PHP_EOL;

            exit(TestRunner::EXCEPTION_EXIT);
        }

        copy($filename, $filename . '.bak');

        print 'Created backup:         ' . $filename . '.bak' . PHP_EOL;

        try {
            file_put_contents(
                $filename,
                (new Migrator())->migrate($filename)
            );

            print 'Migrated configuration: ' . $filename . PHP_EOL;
        } catch (Throwable $t) {
            print 'Migration failed: ' . $t->getMessage() . PHP_EOL;

            exit(TestRunner::EXCEPTION_EXIT);
        }

        exit(TestRunner::SUCCESS_EXIT);
    }

    private function handleCustomOptions(array $unrecognizedOptions) {
        foreach ($unrecognizedOptions as $name => $value) {
            if (isset($this->longOptions[$name])) {
                $handler = $this->longOptions[$name];
            }

            $name .= '=';

            if (isset($this->longOptions[$name])) {
                $handler = $this->longOptions[$name];
            }

            if (isset($handler) && is_callable([$this, $handler])) {
                $this->{$handler}($value);

                unset($handler);
            }
        }
    }

    private function handleWarmCoverageCache(XmlConfiguration\Configuration $configuration) {
        $this->printVersionString();

        if (isset($this->arguments['coverageCacheDirectory'])) {
            $cacheDirectory = $this->arguments['coverageCacheDirectory'];
        } elseif ($configuration->codeCoverage()->hasCacheDirectory()) {
            $cacheDirectory = $configuration->codeCoverage()->cacheDirectory()->path();
        } else {
            print 'Cache for static analysis has not been configured' . PHP_EOL;

            exit(TestRunner::EXCEPTION_EXIT);
        }

        $filter = new Filter();

        if ($configuration->codeCoverage()->hasNonEmptyListOfFilesToBeIncludedInCodeCoverageReport()) {
            (new FilterMapper())->map(
                $filter,
                $configuration->codeCoverage()
            );
        } elseif (isset($this->arguments['coverageFilter'])) {
            if (!is_array($this->arguments['coverageFilter'])) {
                $coverageFilterDirectories = [$this->arguments['coverageFilter']];
            } else {
                $coverageFilterDirectories = $this->arguments['coverageFilter'];
            }

            foreach ($coverageFilterDirectories as $coverageFilterDirectory) {
                $filter->includeDirectory($coverageFilterDirectory);
            }
        } else {
            print 'Filter for code coverage has not been configured' . PHP_EOL;

            exit(TestRunner::EXCEPTION_EXIT);
        }

        $timer = new Timer();
        $timer->start();

        print 'Warming cache for static analysis ... ';

        (new CacheWarmer())->warmCache(
            $cacheDirectory,
            !$configuration->codeCoverage()->disableCodeCoverageIgnore(),
            $configuration->codeCoverage()->ignoreDeprecatedCodeUnits(),
            $filter
        );

        print 'done [' . $timer->stop()->asString() . ']' . PHP_EOL;

        exit(TestRunner::SUCCESS_EXIT);
    }

    private function configurationFileInDirectory($directory) {
        $candidates = [
            $directory . '/phpunit.xml',
            $directory . '/phpunit.xml.dist',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return realpath($candidate);
            }
        }

        return null;
    }
}
