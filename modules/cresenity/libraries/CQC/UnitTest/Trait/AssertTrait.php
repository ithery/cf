<?php

/**
 * Description of AssertTrait
 *
 * @author Hery
 */
trait CQC_UnitTest_Trait_AssertTrait {

    /**
     * @var int
     */
    private $count = 0;

    /**
     * Asserts that an array has a specified key.
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertArrayHasKey($key, $array, $message = '') {
        if (!(is_int($key) || is_string($key))) {
            throw CQC_Exception_InvalidArgumentException::create(
                    1, 'integer or string'
            );
        }

        if (!(is_array($array) || $array instanceof ArrayAccess)) {
            throw CQC_Exception_InvalidArgumentException::create(
                    2, 'array or ArrayAccess'
            );
        }

        $constraint = new CQC_UnitTest_Constraint_ArrayHasKey($key);

        $this->assertThat($array, $constraint, $message);
    }

    /**
     * Asserts that an array does not have a specified key.
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertArrayNotHasKey($key, $array, $message = '') {
        if (!(is_int($key) || is_string($key))) {
            throw InvalidArgumentException::create(
                    1, 'integer or string'
            );
        }

        if (!(is_array($array) || $array instanceof ArrayAccess)) {
            throw InvalidArgumentException::create(
                    2, 'array or ArrayAccess'
            );
        }

        $constraint = new CQC_UnitTest_Constraint_LogicalNot(
                new CQC_UnitTest_Constraint_ArrayHasKey($key)
        );

        $this->assertThat($array, $constraint, $message);
    }

    /**
     * Asserts that a haystack contains a needle.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertContains($needle, $haystack, $message = '') {
        $constraint = new CQC_UnitTest_Constraint_TraversableContainsIdentical($needle);

        $this->assertThat($haystack, $constraint, $message);
    }

    public function assertContainsEquals($needle, $haystack, $message = '') {
        $constraint = new TraversableContainsEqual($needle);

        $this->assertThat($haystack, $constraint, $message);
    }

    /**
     * Asserts that a haystack does not contain a needle.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertNotContains($needle, $haystack, $message = '') {
        $constraint = new CQC_UnitTest_Constraint_LogicalNot(
                new TraversableContainsIdentical($needle)
        );

        $this->assertThat($haystack, $constraint, $message);
    }

    public function assertNotContainsEquals($needle, $haystack, $message = '') {
        $constraint = new LogicalNot(new TraversableContainsEqual($needle));

        $this->assertThat($haystack, $constraint, $message);
    }

    /**
     * Asserts that a haystack contains only values of a given type.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertContainsOnly($type, $haystack, $isNativeType = null, $message = '') {
        if ($isNativeType === null) {
            $isNativeType = Type::isType($type);
        }

        $this->assertThat(
                $haystack, new TraversableContainsOnly(
                $type, $isNativeType
                ), $message
        );
    }

    /**
     * Asserts that a haystack contains only instances of a given class name.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertContainsOnlyInstancesOf($className, $haystack, $message = '') {
        $this->assertThat(
                $haystack, new TraversableContainsOnly(
                $className, false
                ), $message
        );
    }

    /**
     * Asserts that a haystack does not contain only values of a given type.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertNotContainsOnly($type, $haystack, $isNativeType = null, $message = '') {
        if ($isNativeType === null) {
            $isNativeType = Type::isType($type);
        }

        $this->assertThat(
                $haystack, new LogicalNot(
                new TraversableContainsOnly(
                $type, $isNativeType
                )
                ), $message
        );
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param Countable|iterable $haystack
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertCount(int $expectedCount, $haystack, $message = '') {
        if (!$haystack instanceof Countable && !is_iterable($haystack)) {
            throw InvalidArgumentException::create(2, 'countable or iterable');
        }

        $this->assertThat(
                $haystack, new Count($expectedCount), $message
        );
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param Countable|iterable $haystack
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertNotCount(int $expectedCount, $haystack, $message = '') {
        if (!$haystack instanceof Countable && !is_iterable($haystack)) {
            throw InvalidArgumentException::create(2, 'countable or iterable');
        }

        $constraint = new LogicalNot(
                new Count($expectedCount)
        );

        $this->assertThat($haystack, $constraint, $message);
    }

    /**
     * Asserts that two variables are equal.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertEquals($expected, $actual, $message = '') {
        $constraint = new IsEqual($expected);

        $this->assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that two variables are equal (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertEqualsCanonicalizing($expected, $actual, $message = '') {
        $constraint = new IsEqualCanonicalizing($expected);

        $this->assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that two variables are equal (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertEqualsIgnoringCase($expected, $actual, $message = '') {
        $constraint = new IsEqualIgnoringCase($expected);

        $this->assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that two variables are equal (with delta).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertEqualsWithDelta($expected, $actual, float $delta, $message = '') {
        $constraint = new IsEqualWithDelta(
                $expected, $delta
        );

        $this->assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that two variables are not equal.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertNotEquals($expected, $actual, $message = '') {
        $constraint = new LogicalNot(
                new IsEqual($expected)
        );

        $this->assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that two variables are not equal (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertNotEqualsCanonicalizing($expected, $actual, $message = '') {
        $constraint = new LogicalNot(
                new IsEqualCanonicalizing($expected)
        );

        $this->assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that two variables are not equal (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertNotEqualsIgnoringCase($expected, $actual, $message = '') {
        $constraint = new LogicalNot(
                new IsEqualIgnoringCase($expected)
        );

        $this->assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that two variables are not equal (with delta).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertNotEqualsWithDelta($expected, $actual, float $delta, $message = '') {
        $constraint = new LogicalNot(
                new IsEqualWithDelta(
                $expected, $delta
                )
        );

        $this->assertThat($actual, $constraint, $message);
    }

    /**
     * @throws ExpectationFailedException
     */
    public function assertObjectEquals(object $expected, object $actual, $method = 'equals', $message = '') {
        $this->assertThat(
                $actual, $this->objectEquals($expected, $method), $message
        );
    }

    /**
     * Asserts that a variable is empty.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert empty $actual
     */
    public function assertEmpty($actual, $message = '') {
        $this->assertThat($actual, $this->isEmpty(), $message);
    }

    /**
     * Asserts that a variable is not empty.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !empty $actual
     */
    public function assertNotEmpty($actual, $message = '') {
        $this->assertThat($actual, $this->logicalNot($this->isEmpty()), $message);
    }

    /**
     * Asserts that a value is greater than another value.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertGreaterThan($expected, $actual, $message = '') {
        $this->assertThat($actual, $this->greaterThan($expected), $message);
    }

    /**
     * Asserts that a value is greater than or equal to another value.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertGreaterThanOrEqual($expected, $actual, $message = '') {
        $this->assertThat(
                $actual, $this->greaterThanOrEqual($expected), $message
        );
    }

    /**
     * Asserts that a value is smaller than another value.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertLessThan($expected, $actual, $message = '') {
        $this->assertThat($actual, $this->lessThan($expected), $message);
    }

    /**
     * Asserts that a value is smaller than or equal to another value.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertLessThanOrEqual($expected, $actual, $message = '') {
        $this->assertThat($actual, $this->lessThanOrEqual($expected), $message);
    }

    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileEquals($expected, $actual, $message = '') {
        $this->assertFileExists($expected, $message);
        $this->assertFileExists($actual, $message);

        $constraint = new IsEqual(file_get_contents($expected));

        $this->assertThat(file_get_contents($actual), $constraint, $message);
    }

    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileEqualsCanonicalizing($expected, $actual, $message = '') {
        $this->assertFileExists($expected, $message);
        $this->assertFileExists($actual, $message);

        $constraint = new IsEqualCanonicalizing(
                file_get_contents($expected)
        );

        $this->assertThat(file_get_contents($actual), $constraint, $message);
    }

    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileEqualsIgnoringCase($expected, $actual, $message = '') {
        $this->assertFileExists($expected, $message);
        $this->assertFileExists($actual, $message);

        $constraint = new IsEqualIgnoringCase(file_get_contents($expected));

        $this->assertThat(file_get_contents($actual), $constraint, $message);
    }

    /**
     * Asserts that the contents of one file is not equal to the contents of
     * another file.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileNotEquals($expected, $actual, $message = '') {
        $this->assertFileExists($expected, $message);
        $this->assertFileExists($actual, $message);

        $constraint = new LogicalNot(
                new IsEqual(file_get_contents($expected))
        );

        $this->assertThat(file_get_contents($actual), $constraint, $message);
    }

    /**
     * Asserts that the contents of one file is not equal to the contents of another
     * file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileNotEqualsCanonicalizing($expected, $actual, $message = '') {
        $this->assertFileExists($expected, $message);
        $this->assertFileExists($actual, $message);

        $constraint = new LogicalNot(
                new IsEqualCanonicalizing(file_get_contents($expected))
        );

        $this->assertThat(file_get_contents($actual), $constraint, $message);
    }

    /**
     * Asserts that the contents of one file is not equal to the contents of another
     * file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileNotEqualsIgnoringCase($expected, $actual, $message = '') {
        $this->assertFileExists($expected, $message);
        $this->assertFileExists($actual, $message);

        $constraint = new LogicalNot(
                new IsEqualIgnoringCase(file_get_contents($expected))
        );

        $this->assertThat(file_get_contents($actual), $constraint, $message);
    }

    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringEqualsFile($expectedFile, $actualString, $message = '') {
        $this->assertFileExists($expectedFile, $message);

        $constraint = new IsEqual(file_get_contents($expectedFile));

        $this->assertThat($actualString, $constraint, $message);
    }

    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringEqualsFileCanonicalizing($expectedFile, $actualString, $message = '') {
        $this->assertFileExists($expectedFile, $message);

        $constraint = new IsEqualCanonicalizing(file_get_contents($expectedFile));

        $this->assertThat($actualString, $constraint, $message);
    }

    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringEqualsFileIgnoringCase($expectedFile, $actualString, $message = '') {
        $this->assertFileExists($expectedFile, $message);

        $constraint = new IsEqualIgnoringCase(file_get_contents($expectedFile));

        $this->assertThat($actualString, $constraint, $message);
    }

    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringNotEqualsFile($expectedFile, $actualString, $message = '') {
        $this->assertFileExists($expectedFile, $message);

        $constraint = new LogicalNot(
                new IsEqual(file_get_contents($expectedFile))
        );

        $this->assertThat($actualString, $constraint, $message);
    }

    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringNotEqualsFileCanonicalizing($expectedFile, $actualString, $message = '') {
        $this->assertFileExists($expectedFile, $message);

        $constraint = new LogicalNot(
                new IsEqualCanonicalizing(file_get_contents($expectedFile))
        );

        $this->assertThat($actualString, $constraint, $message);
    }

    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringNotEqualsFileIgnoringCase($expectedFile, $actualString, $message = '') {
        $this->assertFileExists($expectedFile, $message);

        $constraint = new LogicalNot(
                new IsEqualIgnoringCase(file_get_contents($expectedFile))
        );

        $this->assertThat($actualString, $constraint, $message);
    }

    /**
     * Asserts that a file/dir is readable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertIsReadable($filename, $message = '') {
        $this->assertThat($filename, new IsReadable, $message);
    }

    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertIsNotReadable($filename, $message = '') {
        $this->assertThat($filename, new LogicalNot(new IsReadable), $message);
    }

    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4062
     */
    public function assertNotIsReadable($filename, $message = '') {
        self::createWarning('assertNotIsReadable() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertIsNotReadable() instead.');

        $this->assertThat($filename, new LogicalNot(new IsReadable), $message);
    }

    /**
     * Asserts that a file/dir exists and is writable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertIsWritable($filename, $message = '') {
        $this->assertThat($filename, new IsWritable, $message);
    }

    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertIsNotWritable($filename, $message = '') {
        $this->assertThat($filename, new LogicalNot(new IsWritable), $message);
    }

    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4065
     */
    public function assertNotIsWritable($filename, $message = '') {
        self::createWarning('assertNotIsWritable() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertIsNotWritable() instead.');

        $this->assertThat($filename, new LogicalNot(new IsWritable), $message);
    }

    /**
     * Asserts that a directory exists.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertDirectoryExists($directory, $message = '') {
        $this->assertThat($directory, new DirectoryExists, $message);
    }

    /**
     * Asserts that a directory does not exist.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertDirectoryDoesNotExist($directory, $message = '') {
        $this->assertThat($directory, new LogicalNot(new DirectoryExists), $message);
    }

    /**
     * Asserts that a directory does not exist.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4068
     */
    public function assertDirectoryNotExists($directory, $message = '') {
        self::createWarning('assertDirectoryNotExists() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertDirectoryDoesNotExist() instead.');

        $this->assertThat($directory, new LogicalNot(new DirectoryExists), $message);
    }

    /**
     * Asserts that a directory exists and is readable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertDirectoryIsReadable($directory, $message = '') {
        self::assertDirectoryExists($directory, $message);
        self::assertIsReadable($directory, $message);
    }

    /**
     * Asserts that a directory exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertDirectoryIsNotReadable($directory, $message = '') {
        self::assertDirectoryExists($directory, $message);
        self::assertIsNotReadable($directory, $message);
    }

    /**
     * Asserts that a directory exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4071
     */
    public function assertDirectoryNotIsReadable($directory, $message = '') {
        self::createWarning('assertDirectoryNotIsReadable() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertDirectoryIsNotReadable() instead.');

        self::assertDirectoryExists($directory, $message);
        self::assertIsNotReadable($directory, $message);
    }

    /**
     * Asserts that a directory exists and is writable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertDirectoryIsWritable($directory, $message = '') {
        self::assertDirectoryExists($directory, $message);
        self::assertIsWritable($directory, $message);
    }

    /**
     * Asserts that a directory exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertDirectoryIsNotWritable($directory, $message = '') {
        self::assertDirectoryExists($directory, $message);
        self::assertIsNotWritable($directory, $message);
    }

    /**
     * Asserts that a directory exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4074
     */
    public function assertDirectoryNotIsWritable($directory, $message = '') {
        self::createWarning('assertDirectoryNotIsWritable() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertDirectoryIsNotWritable() instead.');

        self::assertDirectoryExists($directory, $message);
        self::assertIsNotWritable($directory, $message);
    }

    /**
     * Asserts that a file exists.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileExists($filename, $message = '') {
        $this->assertThat($filename, new FileExists, $message);
    }

    /**
     * Asserts that a file does not exist.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileDoesNotExist($filename, $message = '') {
        $this->assertThat($filename, new LogicalNot(new FileExists), $message);
    }

    /**
     * Asserts that a file does not exist.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4077
     */
    public function assertFileNotExists($filename, $message = '') {
        self::createWarning('assertFileNotExists() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertFileDoesNotExist() instead.');

        $this->assertThat($filename, new LogicalNot(new FileExists), $message);
    }

    /**
     * Asserts that a file exists and is readable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileIsReadable($file, $message = '') {
        self::assertFileExists($file, $message);
        self::assertIsReadable($file, $message);
    }

    /**
     * Asserts that a file exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileIsNotReadable($file, $message = '') {
        self::assertFileExists($file, $message);
        self::assertIsNotReadable($file, $message);
    }

    /**
     * Asserts that a file exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4080
     */
    public function assertFileNotIsReadable($file, $message = '') {
        self::createWarning('assertFileNotIsReadable() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertFileIsNotReadable() instead.');

        self::assertFileExists($file, $message);
        self::assertIsNotReadable($file, $message);
    }

    /**
     * Asserts that a file exists and is writable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileIsWritable($file, $message = '') {
        self::assertFileExists($file, $message);
        self::assertIsWritable($file, $message);
    }

    /**
     * Asserts that a file exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFileIsNotWritable($file, $message = '') {
        self::assertFileExists($file, $message);
        self::assertIsNotWritable($file, $message);
    }

    /**
     * Asserts that a file exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4083
     */
    public function assertFileNotIsWritable($file, $message = '') {
        self::createWarning('assertFileNotIsWritable() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertFileIsNotWritable() instead.');

        self::assertFileExists($file, $message);
        self::assertIsNotWritable($file, $message);
    }

    /**
     * Asserts that a condition is true.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert true $condition
     */
    public function assertTrue($condition, $message = '') {
        $this->assertThat($condition, $this->isTrue(), $message);
    }

    /**
     * Asserts that a condition is not true.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !true $condition
     */
    public function assertNotTrue($condition, $message = '') {
        $this->assertThat($condition, $this->logicalNot($this->isTrue()), $message);
    }

    /**
     * Asserts that a condition is false.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert false $condition
     */
    public function assertFalse($condition, $message = '') {
        $this->assertThat($condition, $this->isFalse(), $message);
    }

    /**
     * Asserts that a condition is not false.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !false $condition
     */
    public function assertNotFalse($condition, $message = '') {
        $this->assertThat($condition, $this->logicalNot($this->isFalse()), $message);
    }

    /**
     * Asserts that a variable is null.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert null $actual
     */
    public function assertNull($actual, $message = '') {
        $this->assertThat($actual, $this->isNull(), $message);
    }

    /**
     * Asserts that a variable is not null.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !null $actual
     */
    public function assertNotNull($actual, $message = '') {
        $this->assertThat($actual, $this->logicalNot($this->isNull()), $message);
    }

    /**
     * Asserts that a variable is finite.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertFinite($actual, $message = '') {
        $this->assertThat($actual, $this->isFinite(), $message);
    }

    /**
     * Asserts that a variable is infinite.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertInfinite($actual, $message = '') {
        $this->assertThat($actual, $this->isInfinite(), $message);
    }

    /**
     * Asserts that a variable is nan.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertNan($actual, $message = '') {
        $this->assertThat($actual, $this->isNan(), $message);
    }

    /**
     * Asserts that a class has a specified attribute.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertClassHasAttribute($attributeName, $className, $message = '') {
        if (!self::isValidClassAttributeName($attributeName)) {
            throw InvalidArgumentException::create(1, 'valid attribute name');
        }

        if (!class_exists($className)) {
            throw InvalidArgumentException::create(2, 'class name');
        }

        $this->assertThat($className, new ClassHasAttribute($attributeName), $message);
    }

    /**
     * Asserts that a class does not have a specified attribute.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertClassNotHasAttribute($attributeName, $className, $message = '') {
        if (!self::isValidClassAttributeName($attributeName)) {
            throw InvalidArgumentException::create(1, 'valid attribute name');
        }

        if (!class_exists($className)) {
            throw InvalidArgumentException::create(2, 'class name');
        }

        $this->assertThat(
                $className, new LogicalNot(
                new ClassHasAttribute($attributeName)
                ), $message
        );
    }

    /**
     * Asserts that a class has a specified static attribute.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertClassHasStaticAttribute($attributeName, $className, $message = '') {
        if (!self::isValidClassAttributeName($attributeName)) {
            throw InvalidArgumentException::create(1, 'valid attribute name');
        }

        if (!class_exists($className)) {
            throw InvalidArgumentException::create(2, 'class name');
        }

        $this->assertThat(
                $className, new ClassHasStaticAttribute($attributeName), $message
        );
    }

    /**
     * Asserts that a class does not have a specified static attribute.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertClassNotHasStaticAttribute($attributeName, $className, $message = '') {
        if (!self::isValidClassAttributeName($attributeName)) {
            throw InvalidArgumentException::create(1, 'valid attribute name');
        }

        if (!class_exists($className)) {
            throw InvalidArgumentException::create(2, 'class name');
        }

        $this->assertThat(
                $className, new LogicalNot(
                new ClassHasStaticAttribute($attributeName)
                ), $message
        );
    }

    /**
     * Asserts that an object has a specified attribute.
     *
     * @param object $object
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertObjectHasAttribute($attributeName, $object, $message = '') {
        if (!self::isValidObjectAttributeName($attributeName)) {
            throw InvalidArgumentException::create(1, 'valid attribute name');
        }

        if (!is_object($object)) {
            throw InvalidArgumentException::create(2, 'object');
        }

        $this->assertThat(
                $object, new ObjectHasAttribute($attributeName), $message
        );
    }

    /**
     * Asserts that an object does not have a specified attribute.
     *
     * @param object $object
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertObjectNotHasAttribute($attributeName, $object, $message = '') {
        if (!self::isValidObjectAttributeName($attributeName)) {
            throw InvalidArgumentException::create(1, 'valid attribute name');
        }

        if (!is_object($object)) {
            throw InvalidArgumentException::create(2, 'object');
        }

        $this->assertThat(
                $object, new LogicalNot(
                new ObjectHasAttribute($attributeName)
                ), $message
        );
    }

    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-template ExpectedType
     * @psalm-param ExpectedType $expected
     * @psalm-assert =ExpectedType $actual
     */
    public function assertSame($expected, $actual, $message = '') {
        $this->assertThat(
                $actual, new IsIdentical($expected), $message
        );
    }

    /**
     * Asserts that two variables do not have the same type and value.
     * Used on objects, it asserts that two variables do not reference
     * the same object.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertNotSame($expected, $actual, $message = '') {
        if (is_bool($expected) && is_bool($actual)) {
            $this->assertNotEquals($expected, $actual, $message);
        }

        $this->assertThat(
                $actual, new LogicalNot(
                new IsIdentical($expected)
                ), $message
        );
    }

    /**
     * Asserts that a variable is of a given type.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     *
     * @psalm-template ExpectedType of object
     * @psalm-param class-string<ExpectedType> $expected
     * @psalm-assert ExpectedType $actual
     */
    public function assertInstanceOf($expected, $actual, $message = '') {
        if (!class_exists($expected) && !interface_exists($expected)) {
            throw InvalidArgumentException::create(1, 'class or interface name');
        }

        $this->assertThat(
                $actual, new IsInstanceOf($expected), $message
        );
    }

    /**
     * Asserts that a variable is not of a given type.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     *
     * @psalm-template ExpectedType of object
     * @psalm-param class-string<ExpectedType> $expected
     * @psalm-assert !ExpectedType $actual
     */
    public function assertNotInstanceOf($expected, $actual, $message = '') {
        if (!class_exists($expected) && !interface_exists($expected)) {
            throw InvalidArgumentException::create(1, 'class or interface name');
        }

        $this->assertThat(
                $actual, new LogicalNot(
                new IsInstanceOf($expected)
                ), $message
        );
    }

    /**
     * Asserts that a variable is of type array.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert array $actual
     */
    public function assertIsArray($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_ARRAY), $message
        );
    }

    /**
     * Asserts that a variable is of type bool.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert bool $actual
     */
    public function assertIsBool($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_BOOL), $message
        );
    }

    /**
     * Asserts that a variable is of type float.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert float $actual
     */
    public function assertIsFloat($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_FLOAT), $message
        );
    }

    /**
     * Asserts that a variable is of type int.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert int $actual
     */
    public function assertIsInt($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_INT), $message
        );
    }

    /**
     * Asserts that a variable is of type numeric.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert numeric $actual
     */
    public function assertIsNumeric($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_NUMERIC), $message
        );
    }

    /**
     * Asserts that a variable is of type object.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert object $actual
     */
    public function assertIsObject($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_OBJECT), $message
        );
    }

    /**
     * Asserts that a variable is of type resource.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert resource $actual
     */
    public function assertIsResource($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_RESOURCE), $message
        );
    }

    /**
     * Asserts that a variable is of type resource and is closed.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert resource $actual
     */
    public function assertIsClosedResource($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_CLOSED_RESOURCE), $message
        );
    }

    /**
     * Asserts that a variable is of type string.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert string $actual
     */
    public function assertIsString($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_STRING), $message
        );
    }

    /**
     * Asserts that a variable is of type scalar.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert scalar $actual
     */
    public function assertIsScalar($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_SCALAR), $message
        );
    }

    /**
     * Asserts that a variable is of type callable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert callable $actual
     */
    public function assertIsCallable($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_CALLABLE), $message
        );
    }

    /**
     * Asserts that a variable is of type iterable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert iterable $actual
     */
    public function assertIsIterable($actual, $message = '') {
        $this->assertThat(
                $actual, new IsType(IsType::TYPE_ITERABLE), $message
        );
    }

    /**
     * Asserts that a variable is not of type array.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !array $actual
     */
    public function assertIsNotArray($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_ARRAY)), $message
        );
    }

    /**
     * Asserts that a variable is not of type bool.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !bool $actual
     */
    public function assertIsNotBool($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_BOOL)), $message
        );
    }

    /**
     * Asserts that a variable is not of type float.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !float $actual
     */
    public function assertIsNotFloat($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_FLOAT)), $message
        );
    }

    /**
     * Asserts that a variable is not of type int.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !int $actual
     */
    public function assertIsNotInt($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_INT)), $message
        );
    }

    /**
     * Asserts that a variable is not of type numeric.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !numeric $actual
     */
    public function assertIsNotNumeric($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_NUMERIC)), $message
        );
    }

    /**
     * Asserts that a variable is not of type object.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !object $actual
     */
    public function assertIsNotObject($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_OBJECT)), $message
        );
    }

    /**
     * Asserts that a variable is not of type resource.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !resource $actual
     */
    public function assertIsNotResource($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_RESOURCE)), $message
        );
    }

    /**
     * Asserts that a variable is not of type resource.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !resource $actual
     */
    public function assertIsNotClosedResource($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_CLOSED_RESOURCE)), $message
        );
    }

    /**
     * Asserts that a variable is not of type string.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !string $actual
     */
    public function assertIsNotString($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_STRING)), $message
        );
    }

    /**
     * Asserts that a variable is not of type scalar.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !scalar $actual
     */
    public function assertIsNotScalar($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_SCALAR)), $message
        );
    }

    /**
     * Asserts that a variable is not of type callable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !callable $actual
     */
    public function assertIsNotCallable($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_CALLABLE)), $message
        );
    }

    /**
     * Asserts that a variable is not of type iterable.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-assert !iterable $actual
     */
    public function assertIsNotIterable($actual, $message = '') {
        $this->assertThat(
                $actual, new LogicalNot(new IsType(IsType::TYPE_ITERABLE)), $message
        );
    }

    /**
     * Asserts that a string matches a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertMatchesRegularExpression($pattern, $string, $message = '') {
        $this->assertThat($string, new RegularExpression($pattern), $message);
    }

    /**
     * Asserts that a string matches a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4086
     */
    public function assertRegExp($pattern, $string, $message = '') {
        self::createWarning('assertRegExp() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertMatchesRegularExpression() instead.');

        $this->assertThat($string, new RegularExpression($pattern), $message);
    }

    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertDoesNotMatchRegularExpression($pattern, $string, $message = '') {
        $this->assertThat(
                $string, new LogicalNot(
                new RegularExpression($pattern)
                ), $message
        );
    }

    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4089
     */
    public function assertNotRegExp($pattern, $string, $message = '') {
        self::createWarning('assertNotRegExp() is deprecated and will be removed in PHPUnit 10. Refactor your code to use assertDoesNotMatchRegularExpression() instead.');

        $this->assertThat(
                $string, new LogicalNot(
                new RegularExpression($pattern)
                ), $message
        );
    }

    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is the same.
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertSameSize($expected, $actual, $message = '') {
        if (!$expected instanceof Countable && !is_iterable($expected)) {
            throw InvalidArgumentException::create(1, 'countable or iterable');
        }

        if (!$actual instanceof Countable && !is_iterable($actual)) {
            throw InvalidArgumentException::create(2, 'countable or iterable');
        }

        $this->assertThat(
                $actual, new SameSize($expected), $message
        );
    }

    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is not the same.
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertNotSameSize($expected, $actual, $message = '') {
        if (!$expected instanceof Countable && !is_iterable($expected)) {
            throw InvalidArgumentException::create(1, 'countable or iterable');
        }

        if (!$actual instanceof Countable && !is_iterable($actual)) {
            throw InvalidArgumentException::create(2, 'countable or iterable');
        }

        $this->assertThat(
                $actual, new LogicalNot(
                new SameSize($expected)
                ), $message
        );
    }

    /**
     * Asserts that a string matches a given format string.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringMatchesFormat($format, $string, $message = '') {
        $this->assertThat($string, new StringMatchesFormatDescription($format), $message);
    }

    /**
     * Asserts that a string does not match a given format string.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringNotMatchesFormat($format, $string, $message = '') {
        $this->assertThat(
                $string, new LogicalNot(
                new StringMatchesFormatDescription($format)
                ), $message
        );
    }

    /**
     * Asserts that a string matches a given format file.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringMatchesFormatFile($formatFile, $string, $message = '') {
        $this->assertFileExists($formatFile, $message);

        $this->assertThat(
                $string, new StringMatchesFormatDescription(
                file_get_contents($formatFile)
                ), $message
        );
    }

    /**
     * Asserts that a string does not match a given format string.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringNotMatchesFormatFile($formatFile, $string, $message = '') {
        $this->assertFileExists($formatFile, $message);

        $this->assertThat(
                $string, new LogicalNot(
                new StringMatchesFormatDescription(
                file_get_contents($formatFile)
                )
                ), $message
        );
    }

    /**
     * Asserts that a string starts with a given prefix.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringStartsWith($prefix, $string, $message = '') {
        $this->assertThat($string, new StringStartsWith($prefix), $message);
    }

    /**
     * Asserts that a string starts not with a given prefix.
     *
     * @param string $prefix
     * @param string $string
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringStartsNotWith($prefix, $string, $message = '') {
        $this->assertThat(
                $string, new LogicalNot(
                new StringStartsWith($prefix)
                ), $message
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringContainsString($needle, $haystack, $message = '') {
        $constraint = new StringContains($needle, false);

        $this->assertThat($haystack, $constraint, $message);
    }

    /**
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringContainsStringIgnoringCase($needle, $haystack, $message = '') {
        $constraint = new StringContains($needle, true);

        $this->assertThat($haystack, $constraint, $message);
    }

    /**
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringNotContainsString($needle, $haystack, $message = '') {
        $constraint = new LogicalNot(new StringContains($needle));

        $this->assertThat($haystack, $constraint, $message);
    }

    /**
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringNotContainsStringIgnoringCase($needle, $haystack, $message = '') {
        $constraint = new LogicalNot(new StringContains($needle, true));

        $this->assertThat($haystack, $constraint, $message);
    }

    /**
     * Asserts that a string ends with a given suffix.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringEndsWith($suffix, $string, $message = '') {
        $this->assertThat($string, new StringEndsWith($suffix), $message);
    }

    /**
     * Asserts that a string ends not with a given suffix.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertStringEndsNotWith($suffix, $string, $message = '') {
        $this->assertThat(
                $string, new LogicalNot(
                new StringEndsWith($suffix)
                ), $message
        );
    }

    /**
     * Asserts that two XML files are equal.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function assertXmlFileEqualsXmlFile($expectedFile, $actualFile, $message = '') {
        $expected = (new XmlLoader)->loadFile($expectedFile);
        $actual = (new XmlLoader)->loadFile($actualFile);

        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML files are not equal.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws \PHPUnit\Util\Exception
     */
    public function assertXmlFileNotEqualsXmlFile($expectedFile, $actualFile, $message = '') {
        $expected = (new XmlLoader)->loadFile($expectedFile);
        $actual = (new XmlLoader)->loadFile($actualFile);

        $this->assertNotEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML documents are equal.
     *
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     */
    public function assertXmlStringEqualsXmlFile($expectedFile, $actualXml, $message = '') {
        if (!is_string($actualXml)) {
            self::createWarning('Passing an argument of type DOMDocument for the $actualXml parameter is deprecated. Support for this will be removed in PHPUnit 10.');

            $actual = $actualXml;
        } else {
            $actual = (new XmlLoader)->load($actualXml);
        }

        $expected = (new XmlLoader)->loadFile($expectedFile);

        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML documents are not equal.
     *
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     */
    public function assertXmlStringNotEqualsXmlFile($expectedFile, $actualXml, $message = '') {
        if (!is_string($actualXml)) {
            self::createWarning('Passing an argument of type DOMDocument for the $actualXml parameter is deprecated. Support for this will be removed in PHPUnit 10.');

            $actual = $actualXml;
        } else {
            $actual = (new XmlLoader)->load($actualXml);
        }

        $expected = (new XmlLoader)->loadFile($expectedFile);

        $this->assertNotEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML documents are equal.
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     */
    public function assertXmlStringEqualsXmlString($expectedXml, $actualXml, $message = '') {
        if (!is_string($expectedXml)) {
            self::createWarning('Passing an argument of type DOMDocument for the $expectedXml parameter is deprecated. Support for this will be removed in PHPUnit 10.');

            $expected = $expectedXml;
        } else {
            $expected = (new XmlLoader)->load($expectedXml);
        }

        if (!is_string($actualXml)) {
            self::createWarning('Passing an argument of type DOMDocument for the $actualXml parameter is deprecated. Support for this will be removed in PHPUnit 10.');

            $actual = $actualXml;
        } else {
            $actual = (new XmlLoader)->load($actualXml);
        }

        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two XML documents are not equal.
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     */
    public function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, $message = '') {
        if (!is_string($expectedXml)) {
            self::createWarning('Passing an argument of type DOMDocument for the $expectedXml parameter is deprecated. Support for this will be removed in PHPUnit 10.');

            $expected = $expectedXml;
        } else {
            $expected = (new XmlLoader)->load($expectedXml);
        }

        if (!is_string($actualXml)) {
            self::createWarning('Passing an argument of type DOMDocument for the $actualXml parameter is deprecated. Support for this will be removed in PHPUnit 10.');

            $actual = $actualXml;
        } else {
            $actual = (new XmlLoader)->load($actualXml);
        }

        $this->assertNotEquals($expected, $actual, $message);
    }

    /**
     * Asserts that a hierarchy of DOMElements matches.
     *
     * @throws AssertionFailedError
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4091
     */
    public function assertEqualXMLStructure(DOMElement $expectedElement, DOMElement $actualElement, $checkAttributes = false, $message = '') {
        self::createWarning('assertEqualXMLStructure() is deprecated and will be removed in PHPUnit 10.');

        $expectedElement = Xml::import($expectedElement);
        $actualElement = Xml::import($actualElement);

        $this->assertSame(
                $expectedElement->tagName, $actualElement->tagName, $message
        );

        if ($checkAttributes) {
            $this->assertSame(
                    $expectedElement->attributes->length, $actualElement->attributes->length, sprintf(
                            '%s%sNumber of attributes on node "%s" does not match', $message, !empty($message) ? "\n" : '', $expectedElement->tagName
                    )
            );

            for ($i = 0; $i < $expectedElement->attributes->length; $i++) {
                $expectedAttribute = $expectedElement->attributes->item($i);
                $actualAttribute = $actualElement->attributes->getNamedItem($expectedAttribute->name);

                assert($expectedAttribute instanceof DOMAttr);

                if (!$actualAttribute) {
                    $this->fail(
                            sprintf(
                                    '%s%sCould not find attribute "%s" on node "%s"', $message, !empty($message) ? "\n" : '', $expectedAttribute->name, $expectedElement->tagName
                            )
                    );
                }
            }
        }

        Xml::removeCharacterDataNodes($expectedElement);
        Xml::removeCharacterDataNodes($actualElement);

        $this->assertSame(
                $expectedElement->childNodes->length, $actualElement->childNodes->length, sprintf(
                        '%s%sNumber of child nodes of "%s" differs', $message, !empty($message) ? "\n" : '', $expectedElement->tagName
                )
        );

        for ($i = 0; $i < $expectedElement->childNodes->length; $i++) {
            $this->assertEqualXMLStructure(
                    $expectedElement->childNodes->item($i), $actualElement->childNodes->item($i), $checkAttributes, $message
            );
        }
    }

    /**
     * Evaluates a PHPUnit\Framework\Constraint matcher object.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertThat($value, CQC_UnitTest_ConstraintAbstract $constraint, $message = '') {
        $this->count += count($constraint);

        $constraint->evaluate($value, $message);
    }

    /**
     * Asserts that a string is a valid JSON string.
     *
     * @throws CQC_Exception_ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertJson($actualJson, $message = '') {
        $this->assertThat($actualJson, $this->isJson(), $message);
    }

    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message = '') {
        $this->assertJson($expectedJson, $message);
        $this->assertJson($actualJson, $message);

        $this->assertThat($actualJson, new JsonMatches($expectedJson), $message);
    }

    /**
     * Asserts that two given JSON encoded objects or arrays are not equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message = '') {
        $this->assertJson($expectedJson, $message);
        $this->assertJson($actualJson, $message);

        $this->assertThat(
                $actualJson, new LogicalNot(
                new JsonMatches($expectedJson)
                ), $message
        );
    }

    /**
     * Asserts that the generated JSON encoded object and the content of the given file are equal.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertJsonStringEqualsJsonFile($expectedFile, $actualJson, $message = '') {
        $this->assertFileExists($expectedFile, $message);
        $expectedJson = file_get_contents($expectedFile);

        $this->assertJson($expectedJson, $message);
        $this->assertJson($actualJson, $message);

        $this->assertThat($actualJson, new JsonMatches($expectedJson), $message);
    }

    /**
     * Asserts that the generated JSON encoded object and the content of the given file are not equal.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertJsonStringNotEqualsJsonFile($expectedFile, $actualJson, $message = '') {
        $this->assertFileExists($expectedFile, $message);
        $expectedJson = file_get_contents($expectedFile);

        $this->assertJson($expectedJson, $message);
        $this->assertJson($actualJson, $message);

        $this->assertThat(
                $actualJson, new LogicalNot(
                new JsonMatches($expectedJson)
                ), $message
        );
    }

    /**
     * Asserts that two JSON files are equal.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertJsonFileEqualsJsonFile($expectedFile, $actualFile, $message = '') {
        $this->assertFileExists($expectedFile, $message);
        $this->assertFileExists($actualFile, $message);

        $actualJson = file_get_contents($actualFile);
        $expectedJson = file_get_contents($expectedFile);

        $this->assertJson($expectedJson, $message);
        $this->assertJson($actualJson, $message);

        $constraintExpected = new JsonMatches(
                $expectedJson
        );

        $constraintActual = new JsonMatches($actualJson);

        $this->assertThat($expectedJson, $constraintActual, $message);
        $this->assertThat($actualJson, $constraintExpected, $message);
    }

    /**
     * Asserts that two JSON files are not equal.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function assertJsonFileNotEqualsJsonFile($expectedFile, $actualFile, $message = '') {
        $this->assertFileExists($expectedFile, $message);
        $this->assertFileExists($actualFile, $message);

        $actualJson = file_get_contents($actualFile);
        $expectedJson = file_get_contents($expectedFile);

        $this->assertJson($expectedJson, $message);
        $this->assertJson($actualJson, $message);

        $constraintExpected = new JsonMatches(
                $expectedJson
        );

        $constraintActual = new JsonMatches($actualJson);

        $this->assertThat($expectedJson, new LogicalNot($constraintActual), $message);
        $this->assertThat($actualJson, new LogicalNot($constraintExpected), $message);
    }

    /**
     * Fails a test with the given message.
     *
     * @throws AssertionFailedError
     *
     * @psalm-return never-return
     */
    public function fail($message = '') {
        $this->count++;

        throw new AssertionFailedError($message);
    }

    /**
     * Mark the test as incomplete.
     *
     * @throws IncompleteTestError
     *
     * @psalm-return never-return
     */
    public function markTestIncomplete($message = '') {
        throw new IncompleteTestError($message);
    }

    /**
     * Mark the test as skipped.
     *
     * @throws SkippedTestError
     * @throws SyntheticSkippedError
     *
     * @psalm-return never-return
     */
    public function markTestSkipped($message = '') {
        if ($hint = self::detectLocationHint($message)) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_unshift($trace, $hint);

            throw new SyntheticSkippedError($hint['message'], 0, $hint['file'], (int) $hint['line'], $trace);
        }

        throw new SkippedTestError($message);
    }

    /**
     * Return the current assertion count.
     */
    public function getCount() {
        return $this->count;
    }

    /**
     * Reset the assertion counter.
     */
    public function resetCount() {
        $this->count = 0;
    }

    /**
     * @return array|null
     */
    private static function detectLocationHint($message) {
        $hint = null;
        $lines = preg_split('/\r\n|\r|\n/', $message);

        while (strpos($lines[0], '__OFFSET') !== false) {
            $offset = explode('=', array_shift($lines));

            if ($offset[0] === '__OFFSET_FILE') {
                $hint['file'] = $offset[1];
            }

            if ($offset[0] === '__OFFSET_LINE') {
                $hint['line'] = $offset[1];
            }
        }

        if ($hint) {
            $hint['message'] = implode(PHP_EOL, $lines);
        }

        return $hint;
    }

    /**
     * 
     * @param type $attributeName
     * @return bool
     */
    private static function isValidObjectAttributeName($attributeName) {
        return (bool) preg_match('/[^\x00-\x1f\x7f-\x9f]+/', $attributeName);
    }

    /**
     * 
     * @param string $attributeName
     * @return bool
     */
    private static function isValidClassAttributeName($attributeName) {
        return (bool) preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $attributeName);
    }

    /**
     * @codeCoverageIgnore
     */
    private static function createWarning($warning) {
        foreach (debug_backtrace() as $step) {
            if (isset($step['object']) && $step['object'] instanceof TestCase) {
                assert($step['object'] instanceof TestCase);

                $step['object']->addWarning($warning);

                break;
            }
        }
    }

}
