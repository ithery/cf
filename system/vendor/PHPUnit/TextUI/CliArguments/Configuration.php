<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\CliArguments;

use PHPUnit\TextUI\XmlConfiguration\Extension;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Configuration
{
    /**
     * @var ?string
     */
    private $argument;

    /**
     * @var ?string
     */
    private $atLeastVersion;

    /**
     * @var ?bool
     */
    private $backupGlobals;

    /**
     * @var ?bool
     */
    private $backupStaticAttributes;

    /**
     * @var ?bool
     */
    private $beStrictAboutChangesToGlobalState;

    /**
     * @var ?bool
     */
    private $beStrictAboutResourceUsageDuringSmallTests;

    /**
     * @var ?string
     */
    private $bootstrap;

    /**
     * @var ?bool
     */
    private $cacheResult;

    /**
     * @var ?string
     */
    private $cacheResultFile;

    /**
     * @var ?bool
     */
    private $checkVersion;

    /**
     * @var ?string
     */
    private $colors;

    /**
     * @var null|int|string
     */
    private $columns;

    /**
     * @var ?string
     */
    private $configuration;

    /**
     * @var null|string[]
     */
    private $coverageFilter;

    /**
     * @var ?string
     */
    private $coverageClover;

    /**
     * @var ?string
     */
    private $coverageCobertura;

    /**
     * @var ?string
     */
    private $coverageCrap4J;

    /**
     * @var ?string
     */
    private $coverageHtml;

    /**
     * @var ?string
     */
    private $coveragePhp;

    /**
     * @var ?string
     */
    private $coverageText;

    /**
     * @var ?bool
     */
    private $coverageTextShowUncoveredFiles;

    /**
     * @var ?bool
     */
    private $coverageTextShowOnlySummary;

    /**
     * @var ?string
     */
    private $coverageXml;

    /**
     * @var ?bool
     */
    private $pathCoverage;

    /**
     * @var ?string
     */
    private $coverageCacheDirectory;

    /**
     * @var ?bool
     */
    private $warmCoverageCache;

    /**
     * @var ?bool
     */
    private $debug;

    /**
     * @var ?int
     */
    private $defaultTimeLimit;

    /**
     * @var ?bool
     */
    private $disableCodeCoverageIgnore;

    /**
     * @var ?bool
     */
    private $disallowTestOutput;

    /**
     * @var ?bool
     */
    private $disallowTodoAnnotatedTests;

    /**
     * @var ?bool
     */
    private $enforceTimeLimit;

    /**
     * @var null|string[]
     */
    private $excludeGroups;

    /**
     * @var ?int
     */
    private $executionOrder;

    /**
     * @var ?int
     */
    private $executionOrderDefects;

    /**
     * @var null|Extension[]
     */
    private $extensions;

    /**
     * @var null|string[]
     */
    private $unavailableExtensions;

    /**
     * @var ?bool
     */
    private $failOnEmptyTestSuite;

    /**
     * @var ?bool
     */
    private $failOnIncomplete;

    /**
     * @var ?bool
     */
    private $failOnRisky;

    /**
     * @var ?bool
     */
    private $failOnSkipped;

    /**
     * @var ?bool
     */
    private $failOnWarning;

    /**
     * @var ?string
     */
    private $filter;

    /**
     * @var ?bool
     */
    private $generateConfiguration;

    /**
     * @var ?bool
     */
    private $migrateConfiguration;

    /**
     * @var null|string[]
     */
    private $groups;

    /**
     * @var null|string[]
     */
    private $testsCovering;

    /**
     * @var null|string[]
     */
    private $testsUsing;

    /**
     * @var ?bool
     */
    private $help;

    /**
     * @var ?string
     */
    private $includePath;

    /**
     * @var null|string[]
     */
    private $iniSettings;

    /**
     * @var ?string
     */
    private $junitLogfile;

    /**
     * @var ?bool
     */
    private $listGroups;

    /**
     * @var ?bool
     */
    private $listSuites;

    /**
     * @var ?bool
     */
    private $listTests;

    /**
     * @var ?string
     */
    private $listTestsXml;

    /**
     * @var ?string
     */
    private $loader;

    /**
     * @var ?bool
     */
    private $noCoverage;

    /**
     * @var ?bool
     */
    private $noExtensions;

    /**
     * @var ?bool
     */
    private $noInteraction;

    /**
     * @var ?bool
     */
    private $noLogging;

    /**
     * @var ?string
     */
    private $printer;

    /**
     * @var ?bool
     */
    private $processIsolation;

    /**
     * @var ?int
     */
    private $randomOrderSeed;

    /**
     * @var ?int
     */
    private $repeat;

    /**
     * @var ?bool
     */
    private $reportUselessTests;

    /**
     * @var ?bool
     */
    private $resolveDependencies;

    /**
     * @var ?bool
     */
    private $reverseList;

    /**
     * @var ?bool
     */
    private $stderr;

    /**
     * @var ?bool
     */
    private $strictCoverage;

    /**
     * @var ?bool
     */
    private $stopOnDefect;

    /**
     * @var ?bool
     */
    private $stopOnError;

    /**
     * @var ?bool
     */
    private $stopOnFailure;

    /**
     * @var ?bool
     */
    private $stopOnIncomplete;

    /**
     * @var ?bool
     */
    private $stopOnRisky;

    /**
     * @var ?bool
     */
    private $stopOnSkipped;

    /**
     * @var ?bool
     */
    private $stopOnWarning;

    /**
     * @var ?string
     */
    private $teamcityLogfile;

    /**
     * @var null|string[]
     */
    private $testdoxExcludeGroups;

    /**
     * @var null|string[]
     */
    private $testdoxGroups;

    /**
     * @var ?string
     */
    private $testdoxHtmlFile;

    /**
     * @var ?string
     */
    private $testdoxTextFile;

    /**
     * @var ?string
     */
    private $testdoxXmlFile;

    /**
     * @var null|string[]
     */
    private $testSuffixes;

    /**
     * @var ?string
     */
    private $testSuite;

    /**
     * @var string[]
     */
    private $unrecognizedOptions;

    /**
     * @var ?string
     */
    private $unrecognizedOrderBy;

    /**
     * @var ?bool
     */
    private $useDefaultConfiguration;

    /**
     * @var ?bool
     */
    private $verbose;

    /**
     * @var ?bool
     */
    private $version;

    /**
     * @var ?string
     */
    private $xdebugFilterFile;

    /**
     * @param null|int|string $columns
     */
    public function __construct($argument, $atLeastVersion, $backupGlobals, $backupStaticAttributes, $beStrictAboutChangesToGlobalState, $beStrictAboutResourceUsageDuringSmallTests, $bootstrap, $cacheResult, $cacheResultFile, $checkVersion, $colors, $columns, $configuration, $coverageClover, $coverageCobertura, $coverageCrap4J, $coverageHtml, $coveragePhp, $coverageText, $coverageTextShowUncoveredFiles, $coverageTextShowOnlySummary, $coverageXml, $pathCoverage, $coverageCacheDirectory, $warmCoverageCache, $debug, $defaultTimeLimit, $disableCodeCoverageIgnore, $disallowTestOutput, $disallowTodoAnnotatedTests, $enforceTimeLimit, $excludeGroups, $executionOrder, $executionOrderDefects, $extensions, $unavailableExtensions, $failOnEmptyTestSuite, $failOnIncomplete, $failOnRisky, $failOnSkipped, $failOnWarning, $filter, $generateConfiguration, $migrateConfiguration, $groups, $testsCovering, $testsUsing, $help, $includePath, $iniSettings, $junitLogfile, $listGroups, $listSuites, $listTests, $listTestsXml, $loader, $noCoverage, $noExtensions, $noInteraction, $noLogging, $printer, $processIsolation, $randomOrderSeed, $repeat, $reportUselessTests, $resolveDependencies, $reverseList, $stderr, $strictCoverage, $stopOnDefect, $stopOnError, $stopOnFailure, $stopOnIncomplete, $stopOnRisky, $stopOnSkipped, $stopOnWarning, $teamcityLogfile, $testdoxExcludeGroups, $testdoxGroups, $testdoxHtmlFile, $testdoxTextFile, $testdoxXmlFile, $testSuffixes, $testSuite, array $unrecognizedOptions, $unrecognizedOrderBy, $useDefaultConfiguration, $verbose, $version, $coverageFilter, $xdebugFilterFile)
    {
        $this->argument                                   = $argument;
        $this->atLeastVersion                             = $atLeastVersion;
        $this->backupGlobals                              = $backupGlobals;
        $this->backupStaticAttributes                     = $backupStaticAttributes;
        $this->beStrictAboutChangesToGlobalState          = $beStrictAboutChangesToGlobalState;
        $this->beStrictAboutResourceUsageDuringSmallTests = $beStrictAboutResourceUsageDuringSmallTests;
        $this->bootstrap                                  = $bootstrap;
        $this->cacheResult                                = $cacheResult;
        $this->cacheResultFile                            = $cacheResultFile;
        $this->checkVersion                               = $checkVersion;
        $this->colors                                     = $colors;
        $this->columns                                    = $columns;
        $this->configuration                              = $configuration;
        $this->coverageFilter                             = $coverageFilter;
        $this->coverageClover                             = $coverageClover;
        $this->coverageCobertura                          = $coverageCobertura;
        $this->coverageCrap4J                             = $coverageCrap4J;
        $this->coverageHtml                               = $coverageHtml;
        $this->coveragePhp                                = $coveragePhp;
        $this->coverageText                               = $coverageText;
        $this->coverageTextShowUncoveredFiles             = $coverageTextShowUncoveredFiles;
        $this->coverageTextShowOnlySummary                = $coverageTextShowOnlySummary;
        $this->coverageXml                                = $coverageXml;
        $this->pathCoverage                               = $pathCoverage;
        $this->coverageCacheDirectory                     = $coverageCacheDirectory;
        $this->warmCoverageCache                          = $warmCoverageCache;
        $this->debug                                      = $debug;
        $this->defaultTimeLimit                           = $defaultTimeLimit;
        $this->disableCodeCoverageIgnore                  = $disableCodeCoverageIgnore;
        $this->disallowTestOutput                         = $disallowTestOutput;
        $this->disallowTodoAnnotatedTests                 = $disallowTodoAnnotatedTests;
        $this->enforceTimeLimit                           = $enforceTimeLimit;
        $this->excludeGroups                              = $excludeGroups;
        $this->executionOrder                             = $executionOrder;
        $this->executionOrderDefects                      = $executionOrderDefects;
        $this->extensions                                 = $extensions;
        $this->unavailableExtensions                      = $unavailableExtensions;
        $this->failOnEmptyTestSuite                       = $failOnEmptyTestSuite;
        $this->failOnIncomplete                           = $failOnIncomplete;
        $this->failOnRisky                                = $failOnRisky;
        $this->failOnSkipped                              = $failOnSkipped;
        $this->failOnWarning                              = $failOnWarning;
        $this->filter                                     = $filter;
        $this->generateConfiguration                      = $generateConfiguration;
        $this->migrateConfiguration                       = $migrateConfiguration;
        $this->groups                                     = $groups;
        $this->testsCovering                              = $testsCovering;
        $this->testsUsing                                 = $testsUsing;
        $this->help                                       = $help;
        $this->includePath                                = $includePath;
        $this->iniSettings                                = $iniSettings;
        $this->junitLogfile                               = $junitLogfile;
        $this->listGroups                                 = $listGroups;
        $this->listSuites                                 = $listSuites;
        $this->listTests                                  = $listTests;
        $this->listTestsXml                               = $listTestsXml;
        $this->loader                                     = $loader;
        $this->noCoverage                                 = $noCoverage;
        $this->noExtensions                               = $noExtensions;
        $this->noInteraction                              = $noInteraction;
        $this->noLogging                                  = $noLogging;
        $this->printer                                    = $printer;
        $this->processIsolation                           = $processIsolation;
        $this->randomOrderSeed                            = $randomOrderSeed;
        $this->repeat                                     = $repeat;
        $this->reportUselessTests                         = $reportUselessTests;
        $this->resolveDependencies                        = $resolveDependencies;
        $this->reverseList                                = $reverseList;
        $this->stderr                                     = $stderr;
        $this->strictCoverage                             = $strictCoverage;
        $this->stopOnDefect                               = $stopOnDefect;
        $this->stopOnError                                = $stopOnError;
        $this->stopOnFailure                              = $stopOnFailure;
        $this->stopOnIncomplete                           = $stopOnIncomplete;
        $this->stopOnRisky                                = $stopOnRisky;
        $this->stopOnSkipped                              = $stopOnSkipped;
        $this->stopOnWarning                              = $stopOnWarning;
        $this->teamcityLogfile                            = $teamcityLogfile;
        $this->testdoxExcludeGroups                       = $testdoxExcludeGroups;
        $this->testdoxGroups                              = $testdoxGroups;
        $this->testdoxHtmlFile                            = $testdoxHtmlFile;
        $this->testdoxTextFile                            = $testdoxTextFile;
        $this->testdoxXmlFile                             = $testdoxXmlFile;
        $this->testSuffixes                               = $testSuffixes;
        $this->testSuite                                  = $testSuite;
        $this->unrecognizedOptions                        = $unrecognizedOptions;
        $this->unrecognizedOrderBy                        = $unrecognizedOrderBy;
        $this->useDefaultConfiguration                    = $useDefaultConfiguration;
        $this->verbose                                    = $verbose;
        $this->version                                    = $version;
        $this->xdebugFilterFile                           = $xdebugFilterFile;
    }

    public function hasArgument()
    {
        return $this->argument !== null;
    }

    /**
     * @throws Exception
     */
    public function argument()
    {
        if ($this->argument === null) {
            throw new Exception;
        }

        return $this->argument;
    }

    public function hasAtLeastVersion()
    {
        return $this->atLeastVersion !== null;
    }

    /**
     * @throws Exception
     */
    public function atLeastVersion()
    {
        if ($this->atLeastVersion === null) {
            throw new Exception;
        }

        return $this->atLeastVersion;
    }

    public function hasBackupGlobals()
    {
        return $this->backupGlobals !== null;
    }

    /**
     * @throws Exception
     */
    public function backupGlobals()
    {
        if ($this->backupGlobals === null) {
            throw new Exception;
        }

        return $this->backupGlobals;
    }

    public function hasBackupStaticAttributes()
    {
        return $this->backupStaticAttributes !== null;
    }

    /**
     * @throws Exception
     */
    public function backupStaticAttributes()
    {
        if ($this->backupStaticAttributes === null) {
            throw new Exception;
        }

        return $this->backupStaticAttributes;
    }

    public function hasBeStrictAboutChangesToGlobalState()
    {
        return $this->beStrictAboutChangesToGlobalState !== null;
    }

    /**
     * @throws Exception
     */
    public function beStrictAboutChangesToGlobalState()
    {
        if ($this->beStrictAboutChangesToGlobalState === null) {
            throw new Exception;
        }

        return $this->beStrictAboutChangesToGlobalState;
    }

    public function hasBeStrictAboutResourceUsageDuringSmallTests()
    {
        return $this->beStrictAboutResourceUsageDuringSmallTests !== null;
    }

    /**
     * @throws Exception
     */
    public function beStrictAboutResourceUsageDuringSmallTests()
    {
        if ($this->beStrictAboutResourceUsageDuringSmallTests === null) {
            throw new Exception;
        }

        return $this->beStrictAboutResourceUsageDuringSmallTests;
    }

    public function hasBootstrap()
    {
        return $this->bootstrap !== null;
    }

    /**
     * @throws Exception
     */
    public function bootstrap()
    {
        if ($this->bootstrap === null) {
            throw new Exception;
        }

        return $this->bootstrap;
    }

    public function hasCacheResult()
    {
        return $this->cacheResult !== null;
    }

    /**
     * @throws Exception
     */
    public function cacheResult()
    {
        if ($this->cacheResult === null) {
            throw new Exception;
        }

        return $this->cacheResult;
    }

    public function hasCacheResultFile()
    {
        return $this->cacheResultFile !== null;
    }

    /**
     * @throws Exception
     */
    public function cacheResultFile()
    {
        if ($this->cacheResultFile === null) {
            throw new Exception;
        }

        return $this->cacheResultFile;
    }

    public function hasCheckVersion()
    {
        return $this->checkVersion !== null;
    }

    /**
     * @throws Exception
     */
    public function checkVersion()
    {
        if ($this->checkVersion === null) {
            throw new Exception;
        }

        return $this->checkVersion;
    }

    public function hasColors()
    {
        return $this->colors !== null;
    }

    /**
     * @throws Exception
     */
    public function colors()
    {
        if ($this->colors === null) {
            throw new Exception;
        }

        return $this->colors;
    }

    public function hasColumns()
    {
        return $this->columns !== null;
    }

    /**
     * @throws Exception
     */
    public function columns()
    {
        if ($this->columns === null) {
            throw new Exception;
        }

        return $this->columns;
    }

    public function hasConfiguration()
    {
        return $this->configuration !== null;
    }

    /**
     * @throws Exception
     */
    public function configuration()
    {
        if ($this->configuration === null) {
            throw new Exception;
        }

        return $this->configuration;
    }

    public function hasCoverageFilter()
    {
        return $this->coverageFilter !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageFilter()
    {
        if ($this->coverageFilter === null) {
            throw new Exception;
        }

        return $this->coverageFilter;
    }

    public function hasCoverageClover()
    {
        return $this->coverageClover !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageClover()
    {
        if ($this->coverageClover === null) {
            throw new Exception;
        }

        return $this->coverageClover;
    }

    public function hasCoverageCobertura()
    {
        return $this->coverageCobertura !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageCobertura()
    {
        if ($this->coverageCobertura === null) {
            throw new Exception;
        }

        return $this->coverageCobertura;
    }

    public function hasCoverageCrap4J()
    {
        return $this->coverageCrap4J !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageCrap4J()
    {
        if ($this->coverageCrap4J === null) {
            throw new Exception;
        }

        return $this->coverageCrap4J;
    }

    public function hasCoverageHtml()
    {
        return $this->coverageHtml !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageHtml()
    {
        if ($this->coverageHtml === null) {
            throw new Exception;
        }

        return $this->coverageHtml;
    }

    public function hasCoveragePhp()
    {
        return $this->coveragePhp !== null;
    }

    /**
     * @throws Exception
     */
    public function coveragePhp()
    {
        if ($this->coveragePhp === null) {
            throw new Exception;
        }

        return $this->coveragePhp;
    }

    public function hasCoverageText()
    {
        return $this->coverageText !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageText()
    {
        if ($this->coverageText === null) {
            throw new Exception;
        }

        return $this->coverageText;
    }

    public function hasCoverageTextShowUncoveredFiles()
    {
        return $this->coverageTextShowUncoveredFiles !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageTextShowUncoveredFiles()
    {
        if ($this->coverageTextShowUncoveredFiles === null) {
            throw new Exception;
        }

        return $this->coverageTextShowUncoveredFiles;
    }

    public function hasCoverageTextShowOnlySummary()
    {
        return $this->coverageTextShowOnlySummary !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageTextShowOnlySummary()
    {
        if ($this->coverageTextShowOnlySummary === null) {
            throw new Exception;
        }

        return $this->coverageTextShowOnlySummary;
    }

    public function hasCoverageXml()
    {
        return $this->coverageXml !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageXml()
    {
        if ($this->coverageXml === null) {
            throw new Exception;
        }

        return $this->coverageXml;
    }

    public function hasPathCoverage()
    {
        return $this->pathCoverage !== null;
    }

    /**
     * @throws Exception
     */
    public function pathCoverage()
    {
        if ($this->pathCoverage === null) {
            throw new Exception;
        }

        return $this->pathCoverage;
    }

    public function hasCoverageCacheDirectory()
    {
        return $this->coverageCacheDirectory !== null;
    }

    /**
     * @throws Exception
     */
    public function coverageCacheDirectory()
    {
        if ($this->coverageCacheDirectory === null) {
            throw new Exception;
        }

        return $this->coverageCacheDirectory;
    }

    public function hasWarmCoverageCache()
    {
        return $this->warmCoverageCache !== null;
    }

    /**
     * @throws Exception
     */
    public function warmCoverageCache()
    {
        if ($this->warmCoverageCache === null) {
            throw new Exception;
        }

        return $this->warmCoverageCache;
    }

    public function hasDebug()
    {
        return $this->debug !== null;
    }

    /**
     * @throws Exception
     */
    public function debug()
    {
        if ($this->debug === null) {
            throw new Exception;
        }

        return $this->debug;
    }

    public function hasDefaultTimeLimit()
    {
        return $this->defaultTimeLimit !== null;
    }

    /**
     * @throws Exception
     */
    public function defaultTimeLimit()
    {
        if ($this->defaultTimeLimit === null) {
            throw new Exception;
        }

        return $this->defaultTimeLimit;
    }

    public function hasDisableCodeCoverageIgnore()
    {
        return $this->disableCodeCoverageIgnore !== null;
    }

    /**
     * @throws Exception
     */
    public function disableCodeCoverageIgnore()
    {
        if ($this->disableCodeCoverageIgnore === null) {
            throw new Exception;
        }

        return $this->disableCodeCoverageIgnore;
    }

    public function hasDisallowTestOutput()
    {
        return $this->disallowTestOutput !== null;
    }

    /**
     * @throws Exception
     */
    public function disallowTestOutput()
    {
        if ($this->disallowTestOutput === null) {
            throw new Exception;
        }

        return $this->disallowTestOutput;
    }

    public function hasDisallowTodoAnnotatedTests()
    {
        return $this->disallowTodoAnnotatedTests !== null;
    }

    /**
     * @throws Exception
     */
    public function disallowTodoAnnotatedTests()
    {
        if ($this->disallowTodoAnnotatedTests === null) {
            throw new Exception;
        }

        return $this->disallowTodoAnnotatedTests;
    }

    public function hasEnforceTimeLimit()
    {
        return $this->enforceTimeLimit !== null;
    }

    /**
     * @throws Exception
     */
    public function enforceTimeLimit()
    {
        if ($this->enforceTimeLimit === null) {
            throw new Exception;
        }

        return $this->enforceTimeLimit;
    }

    public function hasExcludeGroups()
    {
        return $this->excludeGroups !== null;
    }

    /**
     * @throws Exception
     */
    public function excludeGroups()
    {
        if ($this->excludeGroups === null) {
            throw new Exception;
        }

        return $this->excludeGroups;
    }

    public function hasExecutionOrder()
    {
        return $this->executionOrder !== null;
    }

    /**
     * @throws Exception
     */
    public function executionOrder()
    {
        if ($this->executionOrder === null) {
            throw new Exception;
        }

        return $this->executionOrder;
    }

    public function hasExecutionOrderDefects()
    {
        return $this->executionOrderDefects !== null;
    }

    /**
     * @throws Exception
     */
    public function executionOrderDefects()
    {
        if ($this->executionOrderDefects === null) {
            throw new Exception;
        }

        return $this->executionOrderDefects;
    }

    public function hasFailOnEmptyTestSuite()
    {
        return $this->failOnEmptyTestSuite !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnEmptyTestSuite()
    {
        if ($this->failOnEmptyTestSuite === null) {
            throw new Exception;
        }

        return $this->failOnEmptyTestSuite;
    }

    public function hasFailOnIncomplete()
    {
        return $this->failOnIncomplete !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnIncomplete()
    {
        if ($this->failOnIncomplete === null) {
            throw new Exception;
        }

        return $this->failOnIncomplete;
    }

    public function hasFailOnRisky()
    {
        return $this->failOnRisky !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnRisky()
    {
        if ($this->failOnRisky === null) {
            throw new Exception;
        }

        return $this->failOnRisky;
    }

    public function hasFailOnSkipped()
    {
        return $this->failOnSkipped !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnSkipped()
    {
        if ($this->failOnSkipped === null) {
            throw new Exception;
        }

        return $this->failOnSkipped;
    }

    public function hasFailOnWarning()
    {
        return $this->failOnWarning !== null;
    }

    /**
     * @throws Exception
     */
    public function failOnWarning()
    {
        if ($this->failOnWarning === null) {
            throw new Exception;
        }

        return $this->failOnWarning;
    }

    public function hasFilter()
    {
        return $this->filter !== null;
    }

    /**
     * @throws Exception
     */
    public function filter()
    {
        if ($this->filter === null) {
            throw new Exception;
        }

        return $this->filter;
    }

    public function hasGenerateConfiguration()
    {
        return $this->generateConfiguration !== null;
    }

    /**
     * @throws Exception
     */
    public function generateConfiguration()
    {
        if ($this->generateConfiguration === null) {
            throw new Exception;
        }

        return $this->generateConfiguration;
    }

    public function hasMigrateConfiguration()
    {
        return $this->migrateConfiguration !== null;
    }

    /**
     * @throws Exception
     */
    public function migrateConfiguration()
    {
        if ($this->migrateConfiguration === null) {
            throw new Exception;
        }

        return $this->migrateConfiguration;
    }

    public function hasGroups()
    {
        return $this->groups !== null;
    }

    /**
     * @throws Exception
     */
    public function groups()
    {
        if ($this->groups === null) {
            throw new Exception;
        }

        return $this->groups;
    }

    public function hasTestsCovering()
    {
        return $this->testsCovering !== null;
    }

    /**
     * @throws Exception
     */
    public function testsCovering()
    {
        if ($this->testsCovering === null) {
            throw new Exception;
        }

        return $this->testsCovering;
    }

    public function hasTestsUsing()
    {
        return $this->testsUsing !== null;
    }

    /**
     * @throws Exception
     */
    public function testsUsing()
    {
        if ($this->testsUsing === null) {
            throw new Exception;
        }

        return $this->testsUsing;
    }

    public function hasHelp()
    {
        return $this->help !== null;
    }

    /**
     * @throws Exception
     */
    public function help()
    {
        if ($this->help === null) {
            throw new Exception;
        }

        return $this->help;
    }

    public function hasIncludePath()
    {
        return $this->includePath !== null;
    }

    /**
     * @throws Exception
     */
    public function includePath()
    {
        if ($this->includePath === null) {
            throw new Exception;
        }

        return $this->includePath;
    }

    public function hasIniSettings()
    {
        return $this->iniSettings !== null;
    }

    /**
     * @throws Exception
     */
    public function iniSettings()
    {
        if ($this->iniSettings === null) {
            throw new Exception;
        }

        return $this->iniSettings;
    }

    public function hasJunitLogfile()
    {
        return $this->junitLogfile !== null;
    }

    /**
     * @throws Exception
     */
    public function junitLogfile()
    {
        if ($this->junitLogfile === null) {
            throw new Exception;
        }

        return $this->junitLogfile;
    }

    public function hasListGroups()
    {
        return $this->listGroups !== null;
    }

    /**
     * @throws Exception
     */
    public function listGroups()
    {
        if ($this->listGroups === null) {
            throw new Exception;
        }

        return $this->listGroups;
    }

    public function hasListSuites()
    {
        return $this->listSuites !== null;
    }

    /**
     * @throws Exception
     */
    public function listSuites()
    {
        if ($this->listSuites === null) {
            throw new Exception;
        }

        return $this->listSuites;
    }

    public function hasListTests()
    {
        return $this->listTests !== null;
    }

    /**
     * @throws Exception
     */
    public function listTests()
    {
        if ($this->listTests === null) {
            throw new Exception;
        }

        return $this->listTests;
    }

    public function hasListTestsXml()
    {
        return $this->listTestsXml !== null;
    }

    /**
     * @throws Exception
     */
    public function listTestsXml()
    {
        if ($this->listTestsXml === null) {
            throw new Exception;
        }

        return $this->listTestsXml;
    }

    public function hasLoader()
    {
        return $this->loader !== null;
    }

    /**
     * @throws Exception
     */
    public function loader()
    {
        if ($this->loader === null) {
            throw new Exception;
        }

        return $this->loader;
    }

    public function hasNoCoverage()
    {
        return $this->noCoverage !== null;
    }

    /**
     * @throws Exception
     */
    public function noCoverage()
    {
        if ($this->noCoverage === null) {
            throw new Exception;
        }

        return $this->noCoverage;
    }

    public function hasNoExtensions()
    {
        return $this->noExtensions !== null;
    }

    /**
     * @throws Exception
     */
    public function noExtensions()
    {
        if ($this->noExtensions === null) {
            throw new Exception;
        }

        return $this->noExtensions;
    }

    public function hasExtensions()
    {
        return $this->extensions !== null;
    }

    /**
     * @throws Exception
     */
    public function extensions()
    {
        if ($this->extensions === null) {
            throw new Exception;
        }

        return $this->extensions;
    }

    public function hasUnavailableExtensions()
    {
        return $this->unavailableExtensions !== null;
    }

    /**
     * @throws Exception
     */
    public function unavailableExtensions()
    {
        if ($this->unavailableExtensions === null) {
            throw new Exception;
        }

        return $this->unavailableExtensions;
    }

    public function hasNoInteraction()
    {
        return $this->noInteraction !== null;
    }

    /**
     * @throws Exception
     */
    public function noInteraction()
    {
        if ($this->noInteraction === null) {
            throw new Exception;
        }

        return $this->noInteraction;
    }

    public function hasNoLogging()
    {
        return $this->noLogging !== null;
    }

    /**
     * @throws Exception
     */
    public function noLogging()
    {
        if ($this->noLogging === null) {
            throw new Exception;
        }

        return $this->noLogging;
    }

    public function hasPrinter()
    {
        return $this->printer !== null;
    }

    /**
     * @throws Exception
     */
    public function printer()
    {
        if ($this->printer === null) {
            throw new Exception;
        }

        return $this->printer;
    }

    public function hasProcessIsolation()
    {
        return $this->processIsolation !== null;
    }

    /**
     * @throws Exception
     */
    public function processIsolation()
    {
        if ($this->processIsolation === null) {
            throw new Exception;
        }

        return $this->processIsolation;
    }

    public function hasRandomOrderSeed()
    {
        return $this->randomOrderSeed !== null;
    }

    /**
     * @throws Exception
     */
    public function randomOrderSeed()
    {
        if ($this->randomOrderSeed === null) {
            throw new Exception;
        }

        return $this->randomOrderSeed;
    }

    public function hasRepeat()
    {
        return $this->repeat !== null;
    }

    /**
     * @throws Exception
     */
    public function repeat()
    {
        if ($this->repeat === null) {
            throw new Exception;
        }

        return $this->repeat;
    }

    public function hasReportUselessTests()
    {
        return $this->reportUselessTests !== null;
    }

    /**
     * @throws Exception
     */
    public function reportUselessTests()
    {
        if ($this->reportUselessTests === null) {
            throw new Exception;
        }

        return $this->reportUselessTests;
    }

    public function hasResolveDependencies()
    {
        return $this->resolveDependencies !== null;
    }

    /**
     * @throws Exception
     */
    public function resolveDependencies()
    {
        if ($this->resolveDependencies === null) {
            throw new Exception;
        }

        return $this->resolveDependencies;
    }

    public function hasReverseList()
    {
        return $this->reverseList !== null;
    }

    /**
     * @throws Exception
     */
    public function reverseList()
    {
        if ($this->reverseList === null) {
            throw new Exception;
        }

        return $this->reverseList;
    }

    public function hasStderr()
    {
        return $this->stderr !== null;
    }

    /**
     * @throws Exception
     */
    public function stderr()
    {
        if ($this->stderr === null) {
            throw new Exception;
        }

        return $this->stderr;
    }

    public function hasStrictCoverage()
    {
        return $this->strictCoverage !== null;
    }

    /**
     * @throws Exception
     */
    public function strictCoverage()
    {
        if ($this->strictCoverage === null) {
            throw new Exception;
        }

        return $this->strictCoverage;
    }

    public function hasStopOnDefect()
    {
        return $this->stopOnDefect !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnDefect()
    {
        if ($this->stopOnDefect === null) {
            throw new Exception;
        }

        return $this->stopOnDefect;
    }

    public function hasStopOnError()
    {
        return $this->stopOnError !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnError()
    {
        if ($this->stopOnError === null) {
            throw new Exception;
        }

        return $this->stopOnError;
    }

    public function hasStopOnFailure()
    {
        return $this->stopOnFailure !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnFailure()
    {
        if ($this->stopOnFailure === null) {
            throw new Exception;
        }

        return $this->stopOnFailure;
    }

    public function hasStopOnIncomplete()
    {
        return $this->stopOnIncomplete !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnIncomplete()
    {
        if ($this->stopOnIncomplete === null) {
            throw new Exception;
        }

        return $this->stopOnIncomplete;
    }

    public function hasStopOnRisky()
    {
        return $this->stopOnRisky !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnRisky()
    {
        if ($this->stopOnRisky === null) {
            throw new Exception;
        }

        return $this->stopOnRisky;
    }

    public function hasStopOnSkipped()
    {
        return $this->stopOnSkipped !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnSkipped()
    {
        if ($this->stopOnSkipped === null) {
            throw new Exception;
        }

        return $this->stopOnSkipped;
    }

    public function hasStopOnWarning()
    {
        return $this->stopOnWarning !== null;
    }

    /**
     * @throws Exception
     */
    public function stopOnWarning()
    {
        if ($this->stopOnWarning === null) {
            throw new Exception;
        }

        return $this->stopOnWarning;
    }

    public function hasTeamcityLogfile()
    {
        return $this->teamcityLogfile !== null;
    }

    /**
     * @throws Exception
     */
    public function teamcityLogfile()
    {
        if ($this->teamcityLogfile === null) {
            throw new Exception;
        }

        return $this->teamcityLogfile;
    }

    public function hasTestdoxExcludeGroups()
    {
        return $this->testdoxExcludeGroups !== null;
    }

    /**
     * @throws Exception
     */
    public function testdoxExcludeGroups()
    {
        if ($this->testdoxExcludeGroups === null) {
            throw new Exception;
        }

        return $this->testdoxExcludeGroups;
    }

    public function hasTestdoxGroups()
    {
        return $this->testdoxGroups !== null;
    }

    /**
     * @throws Exception
     */
    public function testdoxGroups()
    {
        if ($this->testdoxGroups === null) {
            throw new Exception;
        }

        return $this->testdoxGroups;
    }

    public function hasTestdoxHtmlFile()
    {
        return $this->testdoxHtmlFile !== null;
    }

    /**
     * @throws Exception
     */
    public function testdoxHtmlFile()
    {
        if ($this->testdoxHtmlFile === null) {
            throw new Exception;
        }

        return $this->testdoxHtmlFile;
    }

    public function hasTestdoxTextFile()
    {
        return $this->testdoxTextFile !== null;
    }

    /**
     * @throws Exception
     */
    public function testdoxTextFile()
    {
        if ($this->testdoxTextFile === null) {
            throw new Exception;
        }

        return $this->testdoxTextFile;
    }

    public function hasTestdoxXmlFile()
    {
        return $this->testdoxXmlFile !== null;
    }

    /**
     * @throws Exception
     */
    public function testdoxXmlFile()
    {
        if ($this->testdoxXmlFile === null) {
            throw new Exception;
        }

        return $this->testdoxXmlFile;
    }

    public function hasTestSuffixes()
    {
        return $this->testSuffixes !== null;
    }

    /**
     * @throws Exception
     */
    public function testSuffixes()
    {
        if ($this->testSuffixes === null) {
            throw new Exception;
        }

        return $this->testSuffixes;
    }

    public function hasTestSuite()
    {
        return $this->testSuite !== null;
    }

    /**
     * @throws Exception
     */
    public function testSuite()
    {
        if ($this->testSuite === null) {
            throw new Exception;
        }

        return $this->testSuite;
    }

    public function unrecognizedOptions()
    {
        return $this->unrecognizedOptions;
    }

    public function hasUnrecognizedOrderBy()
    {
        return $this->unrecognizedOrderBy !== null;
    }

    /**
     * @throws Exception
     */
    public function unrecognizedOrderBy()
    {
        if ($this->unrecognizedOrderBy === null) {
            throw new Exception;
        }

        return $this->unrecognizedOrderBy;
    }

    public function hasUseDefaultConfiguration()
    {
        return $this->useDefaultConfiguration !== null;
    }

    /**
     * @throws Exception
     */
    public function useDefaultConfiguration()
    {
        if ($this->useDefaultConfiguration === null) {
            throw new Exception;
        }

        return $this->useDefaultConfiguration;
    }

    public function hasVerbose()
    {
        return $this->verbose !== null;
    }

    /**
     * @throws Exception
     */
    public function verbose()
    {
        if ($this->verbose === null) {
            throw new Exception;
        }

        return $this->verbose;
    }

    public function hasVersion()
    {
        return $this->version !== null;
    }

    /**
     * @throws Exception
     */
    public function version()
    {
        if ($this->version === null) {
            throw new Exception;
        }

        return $this->version;
    }

    public function hasXdebugFilterFile()
    {
        return $this->xdebugFilterFile !== null;
    }

    /**
     * @throws Exception
     */
    public function xdebugFilterFile()
    {
        if ($this->xdebugFilterFile === null) {
            throw new Exception;
        }

        return $this->xdebugFilterFile;
    }
}
