<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner;

use const DIRECTORY_SEPARATOR;
use function assert;
use function defined;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function in_array;
use function is_dir;
use function is_file;
use function is_float;
use function is_int;
use function is_string;
use function serialize;
use function sprintf;
use function unserialize;
use PHPUnit\Util\ErrorHandler;
use PHPUnit\Util\Filesystem;
use Serializable;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class DefaultTestResultCache implements Serializable, TestResultCache
{
    /**
     * @var string
     */
    const DEFAULT_RESULT_CACHE_FILENAME = '.phpunit.result.cache';

    /**
     * Provide extra protection against incomplete or corrupt caches.
     *
     * @var int[]
     */
    const ALLOWED_CACHE_TEST_STATUSES = [
        BaseTestRunner::STATUS_SKIPPED,
        BaseTestRunner::STATUS_INCOMPLETE,
        BaseTestRunner::STATUS_FAILURE,
        BaseTestRunner::STATUS_ERROR,
        BaseTestRunner::STATUS_RISKY,
        BaseTestRunner::STATUS_WARNING,
    ];

    /**
     * Path and filename for result cache file.
     *
     * @var string
     */
    private $cacheFilename;

    /**
     * The list of defective tests.
     *
     * <code>
     * // Mark a test skipped
     * $this->defects[$testName] = BaseTestRunner::TEST_SKIPPED;
     * </code>
     *
     * @var array<string, int>
     */
    private $defects = [];

    /**
     * The list of execution duration of suites and tests (in seconds).
     *
     * <code>
     * // Record running time for test
     * $this->times[$testName] = 1.234;
     * </code>
     *
     * @var array<string, float>
     */
    private $times = [];

    public function __construct($filepath = null)
    {
        if ($filepath !== null && is_dir($filepath)) {
            // cache path provided, use default cache filename in that location
            $filepath .= DIRECTORY_SEPARATOR . self::DEFAULT_RESULT_CACHE_FILENAME;
        }

        $this->cacheFilename = $filepath != null ? $filepath : isset($_ENV['PHPUNIT_RESULT_CACHE']) ? $_ENV['PHPUNIT_RESULT_CACHE'] : self::DEFAULT_RESULT_CACHE_FILENAME;
    }

    /**
     * @throws Exception
     */
    public function persist()
    {
        $this->saveToFile();
    }

    /**
     * @throws Exception
     */
    public function saveToFile()
    {
        if (defined('PHPUNIT_TESTSUITE_RESULTCACHE')) {
            return;
        }

        if (!Filesystem::createDirectory(dirname($this->cacheFilename))) {
            throw new Exception(
                sprintf(
                    'Cannot create directory "%s" for result cache file',
                    $this->cacheFilename
                )
            );
        }

        file_put_contents(
            $this->cacheFilename,
            serialize($this)
        );
    }

    public function setState($testName, $state)
    {
        if ($state !== BaseTestRunner::STATUS_PASSED) {
            $this->defects[$testName] = $state;
        }
    }

    public function getState($testName)
    {
        return isset($this->defects[$testName]) ? $this->defects[$testName] : BaseTestRunner::STATUS_UNKNOWN;
    }

    public function setTime($testName, $time)
    {
        $this->times[$testName] = $time;
    }

    public function getTime($testName)
    {
        return isset($this->times[$testName]) ? $this->times[$testName] : 0.0;
    }

    public function load()
    {
        $this->clear();

        if (!is_file($this->cacheFilename)) {
            return;
        }

        $cacheData = @file_get_contents($this->cacheFilename);

        // @codeCoverageIgnoreStart
        if ($cacheData === false) {
            return;
        }
        // @codeCoverageIgnoreEnd

        $cache = ErrorHandler::invokeIgnoringWarnings(
            static function () use ($cacheData) {
                return @unserialize($cacheData, ['allowed_classes' => [self::class]]);
            }
        );

        if ($cache === false) {
            return;
        }

        if ($cache instanceof self) {
            /* @var DefaultTestResultCache $cache */
            $cache->copyStateToCache($this);
        }
    }

    public function copyStateToCache(self $targetCache)
    {
        foreach ($this->defects as $name => $state) {
            $targetCache->setState($name, $state);
        }

        foreach ($this->times as $name => $time) {
            $targetCache->setTime($name, $time);
        }
    }

    public function clear()
    {
        $this->defects = [];
        $this->times   = [];
    }

    public function serialize()
    {
        return serialize([
            'defects' => $this->defects,
            'times'   => $this->times,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        if (isset($data['times'])) {
            foreach ($data['times'] as $testName => $testTime) {
                assert(is_string($testName));
                assert(is_float($testTime));
                $this->times[$testName] = $testTime;
            }
        }

        if (isset($data['defects'])) {
            foreach ($data['defects'] as $testName => $testResult) {
                assert(is_string($testName));
                assert(is_int($testResult));

                if (in_array($testResult, self::ALLOWED_CACHE_TEST_STATUSES, true)) {
                    $this->defects[$testName] = $testResult;
                }
            }
        }
    }
}
