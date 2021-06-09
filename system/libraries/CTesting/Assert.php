<?php

use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\Constraint\Filesystem\DirectoryExists;
use PHPUnit\Framework\Constraint\Filesystem\FileExists;
use PHPUnit\Framework\Constraint\Operator\LogicalNot;
use PHPUnit\Framework\Constraint\String\RegularExpression;
use PHPUnit\Framework\Exception\InvalidArgumentException;

/**
 * @internal This class is not meant to be used or overwritten outside the framework itself.
 */
abstract class CTesting_Assert extends PHPUnit {
    /**
     * Asserts that an array has a specified subset.
     *
     * @param \ArrayAccess|array $subset
     * @param \ArrayAccess|array $array
     * @param bool               $checkForIdentity
     * @param string             $msg
     *
     * @return void
     */
    public static function assertArraySubset($subset, $array, bool $checkForIdentity = false, string $msg = ''): void {
        if (!(is_array($subset) || $subset instanceof ArrayAccess)) {
            throw InvalidArgumentException::create(1, 'array or ArrayAccess');
        }

        if (!(is_array($array) || $array instanceof ArrayAccess)) {
            throw InvalidArgumentException::create(2, 'array or ArrayAccess');
        }

        $constraint = new ArraySubset($subset, $checkForIdentity);

        PHPUnit::assertThat($array, $constraint, $msg);
    }

    /**
     * Asserts that a file does not exist.
     *
     * @param string $filename
     * @param string $message
     *
     * @return void
     */
    public static function assertFileDoesNotExist($filename, $message = '') {
        static::assertThat($filename, new LogicalNot(new FileExists), $message);
    }

    /**
     * Asserts that a directory does not exist.
     *
     * @param string $directory
     * @param string $message
     *
     * @return void
     */
    public static function assertDirectoryDoesNotExist($directory, $message = '') {
        static::assertThat($directory, new LogicalNot(new DirectoryExists), $message);
    }

    /**
     * Asserts that a string matches a given regular expression.
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     *
     * @return void
     */
    public static function assertMatchesRegularExpression($pattern, $string, $message = '') {
        static::assertThat($string, new RegularExpression($pattern), $message);
    }
}
