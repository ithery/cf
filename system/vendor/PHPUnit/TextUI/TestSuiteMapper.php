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

use const PHP_VERSION;
use function explode;
use function in_array;
use function is_dir;
use function is_file;
use function strpos;
use function version_compare;
use PHPUnit\Framework\Exception\Exception as FrameworkException;
use PHPUnit\Framework\TestSuite as TestSuiteObject;
use PHPUnit\TextUI\XmlConfiguration\TestSuite\TestSuiteCollection;
use PHPUnit\TextUI\Exception\TestDirectoryNotFoundException;
use PHPUnit\TextUI\Exception\TestFileNotFoundException;
use PHPUnit\TextUI\Exception\RuntimeException;
use SebastianBergmann\FileIterator\Facade;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestSuiteMapper {
    /**
     * @throws RuntimeException
     * @throws TestDirectoryNotFoundException
     * @throws TestFileNotFoundException
     *
     * @param mixed $filter
     */
    public function map(TestSuiteCollection $configuration, $filter) {
        try {
            $filterAsArray = $filter ? explode(',', $filter) : [];
            $result = new TestSuiteObject;

            foreach ($configuration as $testSuiteConfiguration) {
                if (!empty($filterAsArray) && !in_array($testSuiteConfiguration->name(), $filterAsArray, true)) {
                    continue;
                }

                $testSuite = new TestSuiteObject($testSuiteConfiguration->name());
                $testSuiteEmpty = true;

                foreach ($testSuiteConfiguration->directories() as $directory) {
                    if (!version_compare(PHP_VERSION, $directory->phpVersion(), $directory->phpVersionOperator()->asString())) {
                        continue;
                    }

                    $exclude = [];

                    foreach ($testSuiteConfiguration->exclude()->asArray() as $file) {
                        $exclude[] = $file->path();
                    }

                    $files = (new Facade)->getFilesAsArray(
                        $directory->path(),
                        $directory->suffix(),
                        $directory->prefix(),
                        $exclude
                    );

                    if (!empty($files)) {
                        $testSuite->addTestFiles($files);

                        $testSuiteEmpty = false;
                    } elseif (strpos($directory->path(), '*') === false && !is_dir($directory->path())) {
                        throw new TestDirectoryNotFoundException($directory->path());
                    }
                }

                foreach ($testSuiteConfiguration->files() as $file) {
                    if (!is_file($file->path())) {
                        throw new TestFileNotFoundException($file->path());
                    }

                    if (!version_compare(PHP_VERSION, $file->phpVersion(), $file->phpVersionOperator()->asString())) {
                        continue;
                    }

                    $testSuite->addTestFile($file->path());

                    $testSuiteEmpty = false;
                }

                if (!$testSuiteEmpty) {
                    $result->addTest($testSuite);
                }
            }

            return $result;
        } catch (FrameworkException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }
}
