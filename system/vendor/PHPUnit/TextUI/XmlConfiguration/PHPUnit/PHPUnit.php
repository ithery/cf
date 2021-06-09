<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\PHPUnit;

use PHPUnit\TextUI\XmlConfiguration\Exception;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class PHPUnit {
    /**
     * @var bool
     */
    private $cacheResult;

    /**
     * @var ?string
     */
    private $cacheResultFile;

    /**
     * @var int|string
     */
    private $columns;

    /**
     * @var string
     */
    private $colors;

    /**
     * @var bool
     */
    private $stderr;

    /**
     * @var bool
     */
    private $noInteraction;

    /**
     * @var bool
     */
    private $verbose;

    /**
     * @var bool
     */
    private $reverseDefectList;

    /**
     * @var bool
     */
    private $convertDeprecationsToExceptions;

    /**
     * @var bool
     */
    private $convertErrorsToExceptions;

    /**
     * @var bool
     */
    private $convertNoticesToExceptions;

    /**
     * @var bool
     */
    private $convertWarningsToExceptions;

    /**
     * @var bool
     */
    private $forceCoversAnnotation;

    /**
     * @var ?string
     */
    private $bootstrap;

    /**
     * @var bool
     */
    private $processIsolation;

    /**
     * @var bool
     */
    private $failOnEmptyTestSuite;

    /**
     * @var bool
     */
    private $failOnIncomplete;

    /**
     * @var bool
     */
    private $failOnRisky;

    /**
     * @var bool
     */
    private $failOnSkipped;

    /**
     * @var bool
     */
    private $failOnWarning;

    /**
     * @var bool
     */
    private $stopOnDefect;

    /**
     * @var bool
     */
    private $stopOnError;

    /**
     * @var bool
     */
    private $stopOnFailure;

    /**
     * @var bool
     */
    private $stopOnWarning;

    /**
     * @var bool
     */
    private $stopOnIncomplete;

    /**
     * @var bool
     */
    private $stopOnRisky;

    /**
     * @var bool
     */
    private $stopOnSkipped;

    /**
     * @var ?string
     */
    private $extensionsDirectory;

    /**
     * @var ?string
     *
     * @deprecated see https://github.com/sebastianbergmann/phpunit/issues/4039
     */
    private $testSuiteLoaderClass;

    /**
     * @var ?string
     *
     * @deprecated see https://github.com/sebastianbergmann/phpunit/issues/4039
     */
    private $testSuiteLoaderFile;

    /**
     * @var ?string
     */
    private $printerClass;

    /**
     * @var ?string
     */
    private $printerFile;

    /**
     * @var bool
     */
    private $beStrictAboutChangesToGlobalState;

    /**
     * @var bool
     */
    private $beStrictAboutOutputDuringTests;

    /**
     * @var bool
     */
    private $beStrictAboutResourceUsageDuringSmallTests;

    /**
     * @var bool
     */
    private $beStrictAboutTestsThatDoNotTestAnything;

    /**
     * @var bool
     */
    private $beStrictAboutTodoAnnotatedTests;

    /**
     * @var bool
     */
    private $beStrictAboutCoversAnnotation;

    /**
     * @var bool
     */
    private $enforceTimeLimit;

    /**
     * @var int
     */
    private $defaultTimeLimit;

    /**
     * @var int
     */
    private $timeoutForSmallTests;

    /**
     * @var int
     */
    private $timeoutForMediumTests;

    /**
     * @var int
     */
    private $timeoutForLargeTests;

    /**
     * @var ?string
     */
    private $defaultTestSuite;

    /**
     * @var int
     */
    private $executionOrder;

    /**
     * @var bool
     */
    private $resolveDependencies;

    /**
     * @var bool
     */
    private $defectsFirst;

    /**
     * @var bool
     */
    private $backupGlobals;

    /**
     * @var bool
     */
    private $backupStaticAttributes;

    /**
     * @var bool
     */
    private $registerMockObjectsFromTestArgumentsRecursively;

    /**
     * @var bool
     */
    private $conflictBetweenPrinterClassAndTestdox;

    public function __construct($cacheResult, $cacheResultFile, $columns, $colors, $stderr, $noInteraction, $verbose, $reverseDefectList, $convertDeprecationsToExceptions, $convertErrorsToExceptions, $convertNoticesToExceptions, $convertWarningsToExceptions, $forceCoversAnnotation, $bootstrap, $processIsolation, $failOnEmptyTestSuite, $failOnIncomplete, $failOnRisky, $failOnSkipped, $failOnWarning, $stopOnDefect, $stopOnError, $stopOnFailure, $stopOnWarning, $stopOnIncomplete, $stopOnRisky, $stopOnSkipped, $extensionsDirectory, $testSuiteLoaderClass, $testSuiteLoaderFile, $printerClass, $printerFile, $beStrictAboutChangesToGlobalState, $beStrictAboutOutputDuringTests, $beStrictAboutResourceUsageDuringSmallTests, $beStrictAboutTestsThatDoNotTestAnything, $beStrictAboutTodoAnnotatedTests, $beStrictAboutCoversAnnotation, $enforceTimeLimit, $defaultTimeLimit, $timeoutForSmallTests, $timeoutForMediumTests, $timeoutForLargeTests, $defaultTestSuite, $executionOrder, $resolveDependencies, $defectsFirst, $backupGlobals, $backupStaticAttributes, $registerMockObjectsFromTestArgumentsRecursively, $conflictBetweenPrinterClassAndTestdox) {
        $this->cacheResult = $cacheResult;
        $this->cacheResultFile = $cacheResultFile;
        $this->columns = $columns;
        $this->colors = $colors;
        $this->stderr = $stderr;
        $this->noInteraction = $noInteraction;
        $this->verbose = $verbose;
        $this->reverseDefectList = $reverseDefectList;
        $this->convertDeprecationsToExceptions = $convertDeprecationsToExceptions;
        $this->convertErrorsToExceptions = $convertErrorsToExceptions;
        $this->convertNoticesToExceptions = $convertNoticesToExceptions;
        $this->convertWarningsToExceptions = $convertWarningsToExceptions;
        $this->forceCoversAnnotation = $forceCoversAnnotation;
        $this->bootstrap = $bootstrap;
        $this->processIsolation = $processIsolation;
        $this->failOnEmptyTestSuite = $failOnEmptyTestSuite;
        $this->failOnIncomplete = $failOnIncomplete;
        $this->failOnRisky = $failOnRisky;
        $this->failOnSkipped = $failOnSkipped;
        $this->failOnWarning = $failOnWarning;
        $this->stopOnDefect = $stopOnDefect;
        $this->stopOnError = $stopOnError;
        $this->stopOnFailure = $stopOnFailure;
        $this->stopOnWarning = $stopOnWarning;
        $this->stopOnIncomplete = $stopOnIncomplete;
        $this->stopOnRisky = $stopOnRisky;
        $this->stopOnSkipped = $stopOnSkipped;
        $this->extensionsDirectory = $extensionsDirectory;
        $this->testSuiteLoaderClass = $testSuiteLoaderClass;
        $this->testSuiteLoaderFile = $testSuiteLoaderFile;
        $this->printerClass = $printerClass;
        $this->printerFile = $printerFile;
        $this->beStrictAboutChangesToGlobalState = $beStrictAboutChangesToGlobalState;
        $this->beStrictAboutOutputDuringTests = $beStrictAboutOutputDuringTests;
        $this->beStrictAboutResourceUsageDuringSmallTests = $beStrictAboutResourceUsageDuringSmallTests;
        $this->beStrictAboutTestsThatDoNotTestAnything = $beStrictAboutTestsThatDoNotTestAnything;
        $this->beStrictAboutTodoAnnotatedTests = $beStrictAboutTodoAnnotatedTests;
        $this->beStrictAboutCoversAnnotation = $beStrictAboutCoversAnnotation;
        $this->enforceTimeLimit = $enforceTimeLimit;
        $this->defaultTimeLimit = $defaultTimeLimit;
        $this->timeoutForSmallTests = $timeoutForSmallTests;
        $this->timeoutForMediumTests = $timeoutForMediumTests;
        $this->timeoutForLargeTests = $timeoutForLargeTests;
        $this->defaultTestSuite = $defaultTestSuite;
        $this->executionOrder = $executionOrder;
        $this->resolveDependencies = $resolveDependencies;
        $this->defectsFirst = $defectsFirst;
        $this->backupGlobals = $backupGlobals;
        $this->backupStaticAttributes = $backupStaticAttributes;
        $this->registerMockObjectsFromTestArgumentsRecursively = $registerMockObjectsFromTestArgumentsRecursively;
        $this->conflictBetweenPrinterClassAndTestdox = $conflictBetweenPrinterClassAndTestdox;
    }

    public function cacheResult() {
        return $this->cacheResult;
    }

    /**
     * @psalm-assert-if-true !null $this->cacheResultFile
     */
    public function hasCacheResultFile() {
        return $this->cacheResultFile !== null;
    }

    /**
     * @throws Exception
     */
    public function cacheResultFile() {
        if (!$this->hasCacheResultFile()) {
            throw new Exception('Cache result file is not configured');
        }

        return (string) $this->cacheResultFile;
    }

    public function columns() {
        return $this->columns;
    }

    public function colors() {
        return $this->colors;
    }

    public function stderr() {
        return $this->stderr;
    }

    public function noInteraction() {
        return $this->noInteraction;
    }

    public function verbose() {
        return $this->verbose;
    }

    public function reverseDefectList() {
        return $this->reverseDefectList;
    }

    public function convertDeprecationsToExceptions() {
        return $this->convertDeprecationsToExceptions;
    }

    public function convertErrorsToExceptions() {
        return $this->convertErrorsToExceptions;
    }

    public function convertNoticesToExceptions() {
        return $this->convertNoticesToExceptions;
    }

    public function convertWarningsToExceptions() {
        return $this->convertWarningsToExceptions;
    }

    public function forceCoversAnnotation() {
        return $this->forceCoversAnnotation;
    }

    /**
     * @psalm-assert-if-true !null $this->bootstrap
     */
    public function hasBootstrap() {
        return $this->bootstrap !== null;
    }

    /**
     * @throws Exception
     */
    public function bootstrap() {
        if (!$this->hasBootstrap()) {
            throw new Exception('Bootstrap script is not configured');
        }

        return (string) $this->bootstrap;
    }

    public function processIsolation() {
        return $this->processIsolation;
    }

    public function failOnEmptyTestSuite() {
        return $this->failOnEmptyTestSuite;
    }

    public function failOnIncomplete() {
        return $this->failOnIncomplete;
    }

    public function failOnRisky() {
        return $this->failOnRisky;
    }

    public function failOnSkipped() {
        return $this->failOnSkipped;
    }

    public function failOnWarning() {
        return $this->failOnWarning;
    }

    public function stopOnDefect() {
        return $this->stopOnDefect;
    }

    public function stopOnError() {
        return $this->stopOnError;
    }

    public function stopOnFailure() {
        return $this->stopOnFailure;
    }

    public function stopOnWarning() {
        return $this->stopOnWarning;
    }

    public function stopOnIncomplete() {
        return $this->stopOnIncomplete;
    }

    public function stopOnRisky() {
        return $this->stopOnRisky;
    }

    public function stopOnSkipped() {
        return $this->stopOnSkipped;
    }

    /**
     * @psalm-assert-if-true !null $this->extensionsDirectory
     */
    public function hasExtensionsDirectory() {
        return $this->extensionsDirectory !== null;
    }

    /**
     * @throws Exception
     */
    public function extensionsDirectory() {
        if (!$this->hasExtensionsDirectory()) {
            throw new Exception('Extensions directory is not configured');
        }

        return (string) $this->extensionsDirectory;
    }

    /**
     * @psalm-assert-if-true !null $this->testSuiteLoaderClass
     *
     * @deprecated see https://github.com/sebastianbergmann/phpunit/issues/4039
     */
    public function hasTestSuiteLoaderClass() {
        return $this->testSuiteLoaderClass !== null;
    }

    /**
     * @throws Exception
     *
     * @deprecated see https://github.com/sebastianbergmann/phpunit/issues/4039
     */
    public function testSuiteLoaderClass() {
        if (!$this->hasTestSuiteLoaderClass()) {
            throw new Exception('TestSuiteLoader class is not configured');
        }

        return (string) $this->testSuiteLoaderClass;
    }

    /**
     * @psalm-assert-if-true !null $this->testSuiteLoaderFile
     *
     * @deprecated see https://github.com/sebastianbergmann/phpunit/issues/4039
     */
    public function hasTestSuiteLoaderFile() {
        return $this->testSuiteLoaderFile !== null;
    }

    /**
     * @throws Exception
     *
     * @deprecated see https://github.com/sebastianbergmann/phpunit/issues/4039
     */
    public function testSuiteLoaderFile() {
        if (!$this->hasTestSuiteLoaderFile()) {
            throw new Exception('TestSuiteLoader sourcecode file is not configured');
        }

        return (string) $this->testSuiteLoaderFile;
    }

    /**
     * @psalm-assert-if-true !null $this->printerClass
     */
    public function hasPrinterClass() {
        return $this->printerClass !== null;
    }

    /**
     * @throws Exception
     */
    public function printerClass() {
        if (!$this->hasPrinterClass()) {
            throw new Exception('ResultPrinter class is not configured');
        }

        return (string) $this->printerClass;
    }

    /**
     * @psalm-assert-if-true !null $this->printerFile
     */
    public function hasPrinterFile() {
        return $this->printerFile !== null;
    }

    /**
     * @throws Exception
     */
    public function printerFile() {
        if (!$this->hasPrinterFile()) {
            throw new Exception('ResultPrinter sourcecode file is not configured');
        }

        return (string) $this->printerFile;
    }

    public function beStrictAboutChangesToGlobalState() {
        return $this->beStrictAboutChangesToGlobalState;
    }

    public function beStrictAboutOutputDuringTests() {
        return $this->beStrictAboutOutputDuringTests;
    }

    public function beStrictAboutResourceUsageDuringSmallTests() {
        return $this->beStrictAboutResourceUsageDuringSmallTests;
    }

    public function beStrictAboutTestsThatDoNotTestAnything() {
        return $this->beStrictAboutTestsThatDoNotTestAnything;
    }

    public function beStrictAboutTodoAnnotatedTests() {
        return $this->beStrictAboutTodoAnnotatedTests;
    }

    public function beStrictAboutCoversAnnotation() {
        return $this->beStrictAboutCoversAnnotation;
    }

    public function enforceTimeLimit() {
        return $this->enforceTimeLimit;
    }

    public function defaultTimeLimit() {
        return $this->defaultTimeLimit;
    }

    public function timeoutForSmallTests() {
        return $this->timeoutForSmallTests;
    }

    public function timeoutForMediumTests() {
        return $this->timeoutForMediumTests;
    }

    public function timeoutForLargeTests() {
        return $this->timeoutForLargeTests;
    }

    /**
     * @psalm-assert-if-true !null $this->defaultTestSuite
     */
    public function hasDefaultTestSuite() {
        return $this->defaultTestSuite !== null;
    }

    /**
     * @throws Exception
     */
    public function defaultTestSuite() {
        if (!$this->hasDefaultTestSuite()) {
            throw new Exception('Default test suite is not configured');
        }

        return (string) $this->defaultTestSuite;
    }

    public function executionOrder() {
        return $this->executionOrder;
    }

    public function resolveDependencies() {
        return $this->resolveDependencies;
    }

    public function defectsFirst() {
        return $this->defectsFirst;
    }

    public function backupGlobals() {
        return $this->backupGlobals;
    }

    public function backupStaticAttributes() {
        return $this->backupStaticAttributes;
    }

    public function registerMockObjectsFromTestArgumentsRecursively() {
        return $this->registerMockObjectsFromTestArgumentsRecursively;
    }

    public function conflictBetweenPrinterClassAndTestdox() {
        return $this->conflictBetweenPrinterClassAndTestdox;
    }
}
