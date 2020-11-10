<?php
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

use function array_diff;
use function array_diff_key;
use function array_flip;
use function array_keys;
use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function explode;
use function get_class;
use function is_array;
use function is_file;
use function sort;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\PhptTestCase;
use PHPUnit\Util\Test;
use ReflectionClass;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Node\Builder;
use SebastianBergmann\CodeCoverage\Node\Directory;
use SebastianBergmann\CodeCoverage\StaticAnalysis\CachingCoveredFileAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\CachingUncoveredFileAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\CoveredFileAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingCoveredFileAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingUncoveredFileAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\UncoveredFileAnalyser;
use SebastianBergmann\CodeUnitReverseLookup\Wizard;

/**
 * Provides collection functionality for PHP code coverage information.
 */
final class CodeCoverage
{
    const UNCOVERED_FILES = 'UNCOVERED_FILES';

    /**
     * @var Driver
     */
    private $driver;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var Wizard
     */
    private $wizard;

    /**
     * @var bool
     */
    private $checkForUnintentionallyCoveredCode = false;

    /**
     * @var bool
     */
    private $includeUncoveredFiles = true;

    /**
     * @var bool
     */
    private $processUncoveredFiles = false;

    /**
     * @var bool
     */
    private $ignoreDeprecatedCode = false;

    /**
     * @var PhptTestCase|string|TestCase
     */
    private $currentId;

    /**
     * Code coverage data.
     *
     * @var ProcessedCodeCoverageData
     */
    private $data;

    /**
     * @var bool
     */
    private $useAnnotationsForIgnoringCode = true;

    /**
     * Test data.
     *
     * @var array
     */
    private $tests = [];

    /**
     * @psalm-var list<class-string>
     */
    private $parentClassesExcludedFromUnintentionallyCoveredCodeCheck = [];

    /**
     * @var ?CoveredFileAnalyser
     */
    private $coveredFileAnalyser;

    /**
     * @var ?UncoveredFileAnalyser
     */
    private $uncoveredFileAnalyser;

    /**
     * @var ?string
     */
    private $cacheDirectory;

    public function __construct(Driver $driver, Filter $filter)
    {
        $this->driver = $driver;
        $this->filter = $filter;
        $this->data   = new ProcessedCodeCoverageData;
        $this->wizard = new Wizard;
    }

    /**
     * Returns the code coverage information as a graph of node objects.
     */
    public function getReport()
    {
        return (new Builder($this->coveredFileAnalyser()))->build($this);
    }

    /**
     * Clears collected code coverage data.
     */
    public function clear()
    {
        $this->currentId = null;
        $this->data      = new ProcessedCodeCoverageData;
        $this->tests     = [];
    }

    /**
     * Returns the filter object used.
     */
    public function filter()
    {
        return $this->filter;
    }

    /**
     * Returns the collected code coverage data.
     */
    public function getData($raw = false)
    {
        if (!$raw) {
            if ($this->processUncoveredFiles) {
                $this->processUncoveredFilesFromFilter();
            } elseif ($this->includeUncoveredFiles) {
                $this->addUncoveredFilesFromFilter();
            }
        }

        return $this->data;
    }

    /**
     * Sets the coverage data.
     */
    public function setData(ProcessedCodeCoverageData $data)
    {
        $this->data = $data;
    }

    /**
     * Returns the test data.
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * Sets the test data.
     */
    public function setTests(array $tests)
    {
        $this->tests = $tests;
    }

    /**
     * Start collection of code coverage information.
     *
     * @param PhptTestCase|string|TestCase $id
     */
    public function start($id, $clear = false)
    {
        if ($clear) {
            $this->clear();
        }

        $this->currentId = $id;

        $this->driver->start();
    }

    /**
     * Stop collection of code coverage information.
     *
     * @param array|false $linesToBeCovered
     */
    public function stop($append = true, $linesToBeCovered = [], array $linesToBeUsed = [])
    {
        if (!is_array($linesToBeCovered) && $linesToBeCovered !== false) {
            throw new InvalidArgumentException(
                '$linesToBeCovered must be an array or false'
            );
        }

        $data = $this->driver->stop();
        $this->append($data, null, $append, $linesToBeCovered, $linesToBeUsed);

        $this->currentId = null;

        return $data;
    }

    /**
     * Appends code coverage data.
     *
     * @param PhptTestCase|string|TestCase $id
     * @param array|false                  $linesToBeCovered
     *
     * @throws UnintentionallyCoveredCodeException
     * @throws TestIdMissingException
     * @throws ReflectionException
     */
    public function append(RawCodeCoverageData $rawData, $id = null, $append = true, $linesToBeCovered = [], array $linesToBeUsed = [])
    {
        if ($id === null) {
            $id = $this->currentId;
        }

        if ($id === null) {
            throw new TestIdMissingException;
        }

        $this->applyFilter($rawData);

        if ($this->useAnnotationsForIgnoringCode) {
            $this->applyIgnoredLinesFilter($rawData);
        }

        $this->data->initializeUnseenData($rawData);

        if (!$append) {
            return;
        }

        if ($id !== self::UNCOVERED_FILES) {
            $this->applyCoversAnnotationFilter(
                $rawData,
                $linesToBeCovered,
                $linesToBeUsed
            );

            if (empty($rawData->lineCoverage())) {
                return;
            }

            $size         = 'unknown';
            $status       = -1;
            $fromTestcase = false;

            if ($id instanceof TestCase) {
                $fromTestcase = true;
                $_size        = $id->getSize();

                if ($_size === Test::SMALL) {
                    $size = 'small';
                } elseif ($_size === Test::MEDIUM) {
                    $size = 'medium';
                } elseif ($_size === Test::LARGE) {
                    $size = 'large';
                }

                $status = $id->getStatus();
                $id     = get_class($id) . '::' . $id->getName();
            } elseif ($id instanceof PhptTestCase) {
                $fromTestcase = true;
                $size         = 'large';
                $id           = $id->getName();
            }

            $this->tests[$id] = ['size' => $size, 'status' => $status, 'fromTestcase' => $fromTestcase];

            $this->data->markCodeAsExecutedByTestCase($id, $rawData);
        }
    }

    /**
     * Merges the data from another instance.
     */
    public function merge(self $that)
    {
        $this->filter->includeFiles(
            $that->filter()->files()
        );

        $this->data->merge($that->data);

        $this->tests = array_merge($this->tests, $that->getTests());
    }

    public function enableCheckForUnintentionallyCoveredCode()
    {
        $this->checkForUnintentionallyCoveredCode = true;
    }

    public function disableCheckForUnintentionallyCoveredCode()
    {
        $this->checkForUnintentionallyCoveredCode = false;
    }

    public function includeUncoveredFiles()
    {
        $this->includeUncoveredFiles = true;
    }

    public function excludeUncoveredFiles()
    {
        $this->includeUncoveredFiles = false;
    }

    public function processUncoveredFiles()
    {
        $this->processUncoveredFiles = true;
    }

    public function doNotProcessUncoveredFiles()
    {
        $this->processUncoveredFiles = false;
    }

    public function enableAnnotationsForIgnoringCode()
    {
        $this->useAnnotationsForIgnoringCode = true;
    }

    public function disableAnnotationsForIgnoringCode()
    {
        $this->useAnnotationsForIgnoringCode = false;
    }

    public function ignoreDeprecatedCode()
    {
        $this->ignoreDeprecatedCode = true;
    }

    public function doNotIgnoreDeprecatedCode()
    {
        $this->ignoreDeprecatedCode = false;
    }

    /**
     * @psalm-assert-if-true !null $this->cacheDirectory
     */
    public function cachesStaticAnalysis()
    {
        return $this->cacheDirectory !== null;
    }

    public function cacheStaticAnalysis($directory)
    {
        $this->cacheDirectory = $directory;
    }

    public function doNotCacheStaticAnalysis()
    {
        $this->cacheDirectory = null;
    }

    /**
     * @throws StaticAnalysisCacheNotConfiguredException
     */
    public function cacheDirectory()
    {
        if (!$this->cachesStaticAnalysis()) {
            throw new StaticAnalysisCacheNotConfiguredException(
                'The static analysis cache is not configured'
            );
        }

        return $this->cacheDirectory;
    }

    /**
     * @psalm-param class-string $className
     */
    public function excludeSubclassesOfThisClassFromUnintentionallyCoveredCodeCheck($className)
    {
        $this->parentClassesExcludedFromUnintentionallyCoveredCodeCheck[] = $className;
    }

    public function enableBranchAndPathCoverage()
    {
        $this->driver->enableBranchAndPathCoverage();
    }

    public function disableBranchAndPathCoverage()
    {
        $this->driver->disableBranchAndPathCoverage();
    }

    public function collectsBranchAndPathCoverage()
    {
        return $this->driver->collectsBranchAndPathCoverage();
    }

    public function detectsDeadCode()
    {
        return $this->driver->detectsDeadCode();
    }

    /**
     * Applies the @covers annotation filtering.
     *
     * @param array|false $linesToBeCovered
     *
     * @throws UnintentionallyCoveredCodeException
     * @throws ReflectionException
     */
    private function applyCoversAnnotationFilter(RawCodeCoverageData $rawData, $linesToBeCovered, array $linesToBeUsed)
    {
        if ($linesToBeCovered === false) {
            $rawData->clear();

            return;
        }

        if (empty($linesToBeCovered)) {
            return;
        }

        if ($this->checkForUnintentionallyCoveredCode &&
            (!$this->currentId instanceof TestCase ||
            (!$this->currentId->isMedium() && !$this->currentId->isLarge()))) {
            $this->performUnintentionallyCoveredCodeCheck($rawData, $linesToBeCovered, $linesToBeUsed);
        }

        $rawLineData         = $rawData->lineCoverage();
        $filesWithNoCoverage = array_diff_key($rawLineData, $linesToBeCovered);

        foreach (array_keys($filesWithNoCoverage) as $fileWithNoCoverage) {
            $rawData->removeCoverageDataForFile($fileWithNoCoverage);
        }

        if (is_array($linesToBeCovered)) {
            foreach ($linesToBeCovered as $fileToBeCovered => $includedLines) {
                $rawData->keepCoverageDataOnlyForLines($fileToBeCovered, $includedLines);
            }
        }
    }

    private function applyFilter(RawCodeCoverageData $data)
    {
        if ($this->filter->isEmpty()) {
            return;
        }

        foreach (array_keys($data->lineCoverage()) as $filename) {
            if ($this->filter->isExcluded($filename)) {
                $data->removeCoverageDataForFile($filename);
            }
        }
    }

    private function applyIgnoredLinesFilter(RawCodeCoverageData $data)
    {
        foreach (array_keys($data->lineCoverage()) as $filename) {
            if (!$this->filter->isFile($filename)) {
                continue;
            }

            $data->removeCoverageDataForLines(
                $filename,
                $this->coveredFileAnalyser()->ignoredLinesFor($filename)
            );
        }
    }

    /**
     * @throws UnintentionallyCoveredCodeException
     */
    private function addUncoveredFilesFromFilter()
    {
        $uncoveredFiles = array_diff(
            $this->filter->files(),
            $this->data->coveredFiles()
        );

        foreach ($uncoveredFiles as $uncoveredFile) {
            if (is_file($uncoveredFile)) {
                $this->append(
                    RawCodeCoverageData::fromUncoveredFile(
                        $uncoveredFile,
                        $this->uncoveredFileAnalyser()
                    ),
                    self::UNCOVERED_FILES
                );
            }
        }
    }

    /**
     * @throws UnintentionallyCoveredCodeException
     */
    private function processUncoveredFilesFromFilter()
    {
        $uncoveredFiles = array_diff(
            $this->filter->files(),
            $this->data->coveredFiles()
        );

        $this->driver->start();

        foreach ($uncoveredFiles as $uncoveredFile) {
            if (is_file($uncoveredFile)) {
                include_once $uncoveredFile;
            }
        }

        $this->append($this->driver->stop(), self::UNCOVERED_FILES);
    }

    /**
     * @throws UnintentionallyCoveredCodeException
     * @throws ReflectionException
     */
    private function performUnintentionallyCoveredCodeCheck(RawCodeCoverageData $data, array $linesToBeCovered, array $linesToBeUsed)
    {
        $allowedLines = $this->getAllowedLines(
            $linesToBeCovered,
            $linesToBeUsed
        );

        $unintentionallyCoveredUnits = [];

        foreach ($data->lineCoverage() as $file => $_data) {
            foreach ($_data as $line => $flag) {
                if ($flag === 1 && !isset($allowedLines[$file][$line])) {
                    $unintentionallyCoveredUnits[] = $this->wizard->lookup($file, $line);
                }
            }
        }

        $unintentionallyCoveredUnits = $this->processUnintentionallyCoveredUnits($unintentionallyCoveredUnits);

        if (!empty($unintentionallyCoveredUnits)) {
            throw new UnintentionallyCoveredCodeException(
                $unintentionallyCoveredUnits
            );
        }
    }

    private function getAllowedLines(array $linesToBeCovered, array $linesToBeUsed)
    {
        $allowedLines = [];

        foreach (array_keys($linesToBeCovered) as $file) {
            if (!isset($allowedLines[$file])) {
                $allowedLines[$file] = [];
            }

            $allowedLines[$file] = array_merge(
                $allowedLines[$file],
                $linesToBeCovered[$file]
            );
        }

        foreach (array_keys($linesToBeUsed) as $file) {
            if (!isset($allowedLines[$file])) {
                $allowedLines[$file] = [];
            }

            $allowedLines[$file] = array_merge(
                $allowedLines[$file],
                $linesToBeUsed[$file]
            );
        }

        foreach (array_keys($allowedLines) as $file) {
            $allowedLines[$file] = array_flip(
                array_unique($allowedLines[$file])
            );
        }

        return $allowedLines;
    }

    /**
     * @throws ReflectionException
     */
    private function processUnintentionallyCoveredUnits(array $unintentionallyCoveredUnits)
    {
        $unintentionallyCoveredUnits = array_unique($unintentionallyCoveredUnits);
        sort($unintentionallyCoveredUnits);

        foreach (array_keys($unintentionallyCoveredUnits) as $k => $v) {
            $unit = explode('::', $unintentionallyCoveredUnits[$k]);

            if (count($unit) !== 2) {
                continue;
            }

            try {
                $class = new ReflectionClass($unit[0]);

                foreach ($this->parentClassesExcludedFromUnintentionallyCoveredCodeCheck as $parentClass) {
                    if ($class->isSubclassOf($parentClass)) {
                        unset($unintentionallyCoveredUnits[$k]);

                        break;
                    }
                }
            } catch (\ReflectionException $e) {
                throw new ReflectionException(
                    $e->getMessage(),
                    (int) $e->getCode(),
                    $e
                );
            }
        }

        return array_values($unintentionallyCoveredUnits);
    }

    private function coveredFileAnalyser()
    {
        if ($this->coveredFileAnalyser !== null) {
            return $this->coveredFileAnalyser;
        }

        $this->coveredFileAnalyser = new ParsingCoveredFileAnalyser(
            $this->useAnnotationsForIgnoringCode,
            $this->ignoreDeprecatedCode
        );

        if ($this->cachesStaticAnalysis()) {
            $this->coveredFileAnalyser = new CachingCoveredFileAnalyser(
                $this->cacheDirectory,
                $this->coveredFileAnalyser
            );
        }

        return $this->coveredFileAnalyser;
    }

    private function uncoveredFileAnalyser()
    {
        if ($this->uncoveredFileAnalyser !== null) {
            return $this->uncoveredFileAnalyser;
        }

        $this->uncoveredFileAnalyser = new ParsingUncoveredFileAnalyser;

        if ($this->cachesStaticAnalysis()) {
            $this->uncoveredFileAnalyser = new CachingUncoveredFileAnalyser(
                $this->cacheDirectory,
                $this->uncoveredFileAnalyser
            );
        }

        return $this->uncoveredFileAnalyser;
    }
}
