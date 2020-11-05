<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

use function func_get_args;
use ArrayAccess;
use Countable;
use DOMDocument;
use DOMElement;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\ClassHasAttribute;
use PHPUnit\Framework\Constraint\ClassHasStaticAttribute;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\Count;
use PHPUnit\Framework\Constraint\DirectoryExists;
use PHPUnit\Framework\Constraint\FileExists;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsAnything;
use PHPUnit\Framework\Constraint\IsEmpty;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsEqualCanonicalizing;
use PHPUnit\Framework\Constraint\IsEqualIgnoringCase;
use PHPUnit\Framework\Constraint\IsEqualWithDelta;
use PHPUnit\Framework\Constraint\IsFalse;
use PHPUnit\Framework\Constraint\IsFinite;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsInfinite;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsJson;
use PHPUnit\Framework\Constraint\IsNan;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsReadable;
use PHPUnit\Framework\Constraint\IsTrue;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\IsWritable;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\LogicalXor;
use PHPUnit\Framework\Constraint\ObjectEquals;
use PHPUnit\Framework\Constraint\ObjectHasAttribute;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use PHPUnit\Framework\Constraint\StringStartsWith;
use PHPUnit\Framework\Constraint\TraversableContainsEqual;
use PHPUnit\Framework\Constraint\TraversableContainsIdentical;
use PHPUnit\Framework\Constraint\TraversableContainsOnly;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount as AnyInvokedCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtIndex as InvokedAtIndexMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastCount as InvokedAtLeastCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastOnce as InvokedAtLeastOnceMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtMostCount as InvokedAtMostCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls as ConsecutiveCallsStub;
use PHPUnit\Framework\MockObject\Stub\Exception as ExceptionStub;
use PHPUnit\Framework\MockObject\Stub\ReturnArgument as ReturnArgumentStub;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback as ReturnCallbackStub;
use PHPUnit\Framework\MockObject\Stub\ReturnSelf as ReturnSelfStub;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use PHPUnit\Framework\MockObject\Stub\ReturnValueMap as ReturnValueMapStub;
use Throwable;

if (!function_exists('PHPUnit\Framework\assertArrayHasKey')) {
    /**
     * Asserts that an array has a specified key.
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertArrayHasKey
     */
    function assertArrayHasKey($key, $array, $message = '')
    {
        Assert::assertArrayHasKey(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertArrayNotHasKey')) {
    /**
     * Asserts that an array does not have a specified key.
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertArrayNotHasKey
     */
    function assertArrayNotHasKey($key, $array, $message = '')
    {
        Assert::assertArrayNotHasKey(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertContains')) {
    /**
     * Asserts that a haystack contains a needle.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertContains
     */
    function assertContains($needle, iterable $haystack, $message = '')
    {
        Assert::assertContains(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertContainsEquals')) {
    function assertContainsEquals($needle, iterable $haystack, $message = '')
    {
        Assert::assertContainsEquals(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotContains')) {
    /**
     * Asserts that a haystack does not contain a needle.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotContains
     */
    function assertNotContains($needle, iterable $haystack, $message = '')
    {
        Assert::assertNotContains(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotContainsEquals')) {
    function assertNotContainsEquals($needle, iterable $haystack, $message = '')
    {
        Assert::assertNotContainsEquals(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertContainsOnly')) {
    /**
     * Asserts that a haystack contains only values of a given type.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertContainsOnly
     */
    function assertContainsOnly($type, iterable $haystack, $isNativeType = null, $message = '')
    {
        Assert::assertContainsOnly(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyInstancesOf')) {
    /**
     * Asserts that a haystack contains only instances of a given class name.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertContainsOnlyInstancesOf
     */
    function assertContainsOnlyInstancesOf($className, iterable $haystack, $message = '')
    {
        Assert::assertContainsOnlyInstancesOf(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotContainsOnly')) {
    /**
     * Asserts that a haystack does not contain only values of a given type.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotContainsOnly
     */
    function assertNotContainsOnly($type, iterable $haystack, $isNativeType = null, $message = '')
    {
        Assert::assertNotContainsOnly(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertCount')) {
    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param Countable|iterable $haystack
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertCount
     */
    function assertCount($expectedCount, $haystack, $message = '')
    {
        Assert::assertCount(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotCount')) {
    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param Countable|iterable $haystack
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotCount
     */
    function assertNotCount($expectedCount, $haystack, $message = '')
    {
        Assert::assertNotCount(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertEquals')) {
    /**
     * Asserts that two variables are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEquals
     */
    function assertEquals($expected, $actual, $message = '')
    {
        Assert::assertEquals(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertEqualsCanonicalizing')) {
    /**
     * Asserts that two variables are equal (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEqualsCanonicalizing
     */
    function assertEqualsCanonicalizing($expected, $actual, $message = '')
    {
        Assert::assertEqualsCanonicalizing(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertEqualsIgnoringCase')) {
    /**
     * Asserts that two variables are equal (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEqualsIgnoringCase
     */
    function assertEqualsIgnoringCase($expected, $actual, $message = '')
    {
        Assert::assertEqualsIgnoringCase(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertEqualsWithDelta')) {
    /**
     * Asserts that two variables are equal (with delta).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEqualsWithDelta
     */
    function assertEqualsWithDelta($expected, $actual, $delta, $message = '')
    {
        Assert::assertEqualsWithDelta(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotEquals')) {
    /**
     * Asserts that two variables are not equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEquals
     */
    function assertNotEquals($expected, $actual, $message = '')
    {
        Assert::assertNotEquals(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotEqualsCanonicalizing')) {
    /**
     * Asserts that two variables are not equal (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEqualsCanonicalizing
     */
    function assertNotEqualsCanonicalizing($expected, $actual, $message = '')
    {
        Assert::assertNotEqualsCanonicalizing(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotEqualsIgnoringCase')) {
    /**
     * Asserts that two variables are not equal (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEqualsIgnoringCase
     */
    function assertNotEqualsIgnoringCase($expected, $actual, $message = '')
    {
        Assert::assertNotEqualsIgnoringCase(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotEqualsWithDelta')) {
    /**
     * Asserts that two variables are not equal (with delta).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEqualsWithDelta
     */
    function assertNotEqualsWithDelta($expected, $actual, $delta, $message = '')
    {
        Assert::assertNotEqualsWithDelta(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertObjectEquals')) {
    /**
     * @throws ExpectationFailedException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertObjectEquals
     */
    function assertObjectEquals(object $expected, object $actual, $method = 'equals', $message = '')
    {
        Assert::assertObjectEquals(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertEmpty')) {
    /**
     * Asserts that a variable is empty.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert empty $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEmpty
     */
    function assertEmpty($actual, $message = '')
    {
        Assert::assertEmpty(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotEmpty')) {
    /**
     * Asserts that a variable is not empty.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !empty $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEmpty
     */
    function assertNotEmpty($actual, $message = '')
    {
        Assert::assertNotEmpty(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertGreaterThan')) {
    /**
     * Asserts that a value is greater than another value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertGreaterThan
     */
    function assertGreaterThan($expected, $actual, $message = '')
    {
        Assert::assertGreaterThan(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertGreaterThanOrEqual')) {
    /**
     * Asserts that a value is greater than or equal to another value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertGreaterThanOrEqual
     */
    function assertGreaterThanOrEqual($expected, $actual, $message = '')
    {
        Assert::assertGreaterThanOrEqual(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertLessThan')) {
    /**
     * Asserts that a value is smaller than another value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertLessThan
     */
    function assertLessThan($expected, $actual, $message = '')
    {
        Assert::assertLessThan(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertLessThanOrEqual')) {
    /**
     * Asserts that a value is smaller than or equal to another value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertLessThanOrEqual
     */
    function assertLessThanOrEqual($expected, $actual, $message = '')
    {
        Assert::assertLessThanOrEqual(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileEquals')) {
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileEquals
     */
    function assertFileEquals($expected, $actual, $message = '')
    {
        Assert::assertFileEquals(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileEqualsCanonicalizing')) {
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileEqualsCanonicalizing
     */
    function assertFileEqualsCanonicalizing($expected, $actual, $message = '')
    {
        Assert::assertFileEqualsCanonicalizing(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileEqualsIgnoringCase')) {
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileEqualsIgnoringCase
     */
    function assertFileEqualsIgnoringCase($expected, $actual, $message = '')
    {
        Assert::assertFileEqualsIgnoringCase(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileNotEquals')) {
    /**
     * Asserts that the contents of one file is not equal to the contents of
     * another file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotEquals
     */
    function assertFileNotEquals($expected, $actual, $message = '')
    {
        Assert::assertFileNotEquals(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileNotEqualsCanonicalizing')) {
    /**
     * Asserts that the contents of one file is not equal to the contents of another
     * file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotEqualsCanonicalizing
     */
    function assertFileNotEqualsCanonicalizing($expected, $actual, $message = '')
    {
        Assert::assertFileNotEqualsCanonicalizing(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileNotEqualsIgnoringCase')) {
    /**
     * Asserts that the contents of one file is not equal to the contents of another
     * file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotEqualsIgnoringCase
     */
    function assertFileNotEqualsIgnoringCase($expected, $actual, $message = '')
    {
        Assert::assertFileNotEqualsIgnoringCase(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringEqualsFile')) {
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEqualsFile
     */
    function assertStringEqualsFile($expectedFile, $actualString, $message = '')
    {
        Assert::assertStringEqualsFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringEqualsFileCanonicalizing')) {
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEqualsFileCanonicalizing
     */
    function assertStringEqualsFileCanonicalizing($expectedFile, $actualString, $message = '')
    {
        Assert::assertStringEqualsFileCanonicalizing(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringEqualsFileIgnoringCase')) {
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEqualsFileIgnoringCase
     */
    function assertStringEqualsFileIgnoringCase($expectedFile, $actualString, $message = '')
    {
        Assert::assertStringEqualsFileIgnoringCase(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringNotEqualsFile')) {
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotEqualsFile
     */
    function assertStringNotEqualsFile($expectedFile, $actualString, $message = '')
    {
        Assert::assertStringNotEqualsFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringNotEqualsFileCanonicalizing')) {
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotEqualsFileCanonicalizing
     */
    function assertStringNotEqualsFileCanonicalizing($expectedFile, $actualString, $message = '')
    {
        Assert::assertStringNotEqualsFileCanonicalizing(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringNotEqualsFileIgnoringCase')) {
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotEqualsFileIgnoringCase
     */
    function assertStringNotEqualsFileIgnoringCase($expectedFile, $actualString, $message = '')
    {
        Assert::assertStringNotEqualsFileIgnoringCase(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsReadable')) {
    /**
     * Asserts that a file/dir is readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsReadable
     */
    function assertIsReadable($filename, $message = '')
    {
        Assert::assertIsReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotReadable')) {
    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotReadable
     */
    function assertIsNotReadable($filename, $message = '')
    {
        Assert::assertIsNotReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotIsReadable')) {
    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4062
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotIsReadable
     */
    function assertNotIsReadable($filename, $message = '')
    {
        Assert::assertNotIsReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsWritable')) {
    /**
     * Asserts that a file/dir exists and is writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsWritable
     */
    function assertIsWritable($filename, $message = '')
    {
        Assert::assertIsWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotWritable')) {
    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotWritable
     */
    function assertIsNotWritable($filename, $message = '')
    {
        Assert::assertIsNotWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotIsWritable')) {
    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4065
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotIsWritable
     */
    function assertNotIsWritable($filename, $message = '')
    {
        Assert::assertNotIsWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDirectoryExists')) {
    /**
     * Asserts that a directory exists.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryExists
     */
    function assertDirectoryExists($directory, $message = '')
    {
        Assert::assertDirectoryExists(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDirectoryDoesNotExist')) {
    /**
     * Asserts that a directory does not exist.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryDoesNotExist
     */
    function assertDirectoryDoesNotExist($directory, $message = '')
    {
        Assert::assertDirectoryDoesNotExist(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDirectoryNotExists')) {
    /**
     * Asserts that a directory does not exist.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4068
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryNotExists
     */
    function assertDirectoryNotExists($directory, $message = '')
    {
        Assert::assertDirectoryNotExists(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDirectoryIsReadable')) {
    /**
     * Asserts that a directory exists and is readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryIsReadable
     */
    function assertDirectoryIsReadable($directory, $message = '')
    {
        Assert::assertDirectoryIsReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDirectoryIsNotReadable')) {
    /**
     * Asserts that a directory exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryIsNotReadable
     */
    function assertDirectoryIsNotReadable($directory, $message = '')
    {
        Assert::assertDirectoryIsNotReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDirectoryNotIsReadable')) {
    /**
     * Asserts that a directory exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4071
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryNotIsReadable
     */
    function assertDirectoryNotIsReadable($directory, $message = '')
    {
        Assert::assertDirectoryNotIsReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDirectoryIsWritable')) {
    /**
     * Asserts that a directory exists and is writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryIsWritable
     */
    function assertDirectoryIsWritable($directory, $message = '')
    {
        Assert::assertDirectoryIsWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDirectoryIsNotWritable')) {
    /**
     * Asserts that a directory exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryIsNotWritable
     */
    function assertDirectoryIsNotWritable($directory, $message = '')
    {
        Assert::assertDirectoryIsNotWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDirectoryNotIsWritable')) {
    /**
     * Asserts that a directory exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4074
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryNotIsWritable
     */
    function assertDirectoryNotIsWritable($directory, $message = '')
    {
        Assert::assertDirectoryNotIsWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileExists')) {
    /**
     * Asserts that a file exists.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileExists
     */
    function assertFileExists($filename, $message = '')
    {
        Assert::assertFileExists(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileDoesNotExist')) {
    /**
     * Asserts that a file does not exist.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileDoesNotExist
     */
    function assertFileDoesNotExist($filename, $message = '')
    {
        Assert::assertFileDoesNotExist(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileNotExists')) {
    /**
     * Asserts that a file does not exist.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4077
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotExists
     */
    function assertFileNotExists($filename, $message = '')
    {
        Assert::assertFileNotExists(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileIsReadable')) {
    /**
     * Asserts that a file exists and is readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileIsReadable
     */
    function assertFileIsReadable($file, $message = '')
    {
        Assert::assertFileIsReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileIsNotReadable')) {
    /**
     * Asserts that a file exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileIsNotReadable
     */
    function assertFileIsNotReadable($file, $message = '')
    {
        Assert::assertFileIsNotReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileNotIsReadable')) {
    /**
     * Asserts that a file exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4080
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotIsReadable
     */
    function assertFileNotIsReadable($file, $message = '')
    {
        Assert::assertFileNotIsReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileIsWritable')) {
    /**
     * Asserts that a file exists and is writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileIsWritable
     */
    function assertFileIsWritable($file, $message = '')
    {
        Assert::assertFileIsWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileIsNotWritable')) {
    /**
     * Asserts that a file exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileIsNotWritable
     */
    function assertFileIsNotWritable($file, $message = '')
    {
        Assert::assertFileIsNotWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFileNotIsWritable')) {
    /**
     * Asserts that a file exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4083
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotIsWritable
     */
    function assertFileNotIsWritable($file, $message = '')
    {
        Assert::assertFileNotIsWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertTrue')) {
    /**
     * Asserts that a condition is true.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert true $condition
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertTrue
     */
    function assertTrue($condition, $message = '')
    {
        Assert::assertTrue(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotTrue')) {
    /**
     * Asserts that a condition is not true.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !true $condition
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotTrue
     */
    function assertNotTrue($condition, $message = '')
    {
        Assert::assertNotTrue(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFalse')) {
    /**
     * Asserts that a condition is false.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert false $condition
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFalse
     */
    function assertFalse($condition, $message = '')
    {
        Assert::assertFalse(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotFalse')) {
    /**
     * Asserts that a condition is not false.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !false $condition
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotFalse
     */
    function assertNotFalse($condition, $message = '')
    {
        Assert::assertNotFalse(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNull')) {
    /**
     * Asserts that a variable is null.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert null $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNull
     */
    function assertNull($actual, $message = '')
    {
        Assert::assertNull(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotNull')) {
    /**
     * Asserts that a variable is not null.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !null $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotNull
     */
    function assertNotNull($actual, $message = '')
    {
        Assert::assertNotNull(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertFinite')) {
    /**
     * Asserts that a variable is finite.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFinite
     */
    function assertFinite($actual, $message = '')
    {
        Assert::assertFinite(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertInfinite')) {
    /**
     * Asserts that a variable is infinite.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertInfinite
     */
    function assertInfinite($actual, $message = '')
    {
        Assert::assertInfinite(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNan')) {
    /**
     * Asserts that a variable is nan.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNan
     */
    function assertNan($actual, $message = '')
    {
        Assert::assertNan(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertClassHasAttribute')) {
    /**
     * Asserts that a class has a specified attribute.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertClassHasAttribute
     */
    function assertClassHasAttribute($attributeName, $className, $message = '')
    {
        Assert::assertClassHasAttribute(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertClassNotHasAttribute')) {
    /**
     * Asserts that a class does not have a specified attribute.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertClassNotHasAttribute
     */
    function assertClassNotHasAttribute($attributeName, $className, $message = '')
    {
        Assert::assertClassNotHasAttribute(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertClassHasStaticAttribute')) {
    /**
     * Asserts that a class has a specified static attribute.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertClassHasStaticAttribute
     */
    function assertClassHasStaticAttribute($attributeName, $className, $message = '')
    {
        Assert::assertClassHasStaticAttribute(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertClassNotHasStaticAttribute')) {
    /**
     * Asserts that a class does not have a specified static attribute.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertClassNotHasStaticAttribute
     */
    function assertClassNotHasStaticAttribute($attributeName, $className, $message = '')
    {
        Assert::assertClassNotHasStaticAttribute(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertObjectHasAttribute')) {
    /**
     * Asserts that an object has a specified attribute.
     *
     * @param object $object
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertObjectHasAttribute
     */
    function assertObjectHasAttribute($attributeName, $object, $message = '')
    {
        Assert::assertObjectHasAttribute(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertObjectNotHasAttribute')) {
    /**
     * Asserts that an object does not have a specified attribute.
     *
     * @param object $object
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertObjectNotHasAttribute
     */
    function assertObjectNotHasAttribute($attributeName, $object, $message = '')
    {
        Assert::assertObjectNotHasAttribute(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertSame')) {
    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-template ExpectedType
     * @psalm-param ExpectedType $expected
     * @psalm-assert =ExpectedType $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertSame
     */
    function assertSame($expected, $actual, $message = '')
    {
        Assert::assertSame(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotSame')) {
    /**
     * Asserts that two variables do not have the same type and value.
     * Used on objects, it asserts that two variables do not reference
     * the same object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotSame
     */
    function assertNotSame($expected, $actual, $message = '')
    {
        Assert::assertNotSame(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertInstanceOf')) {
    /**
     * Asserts that a variable is of a given type.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @psalm-template ExpectedType of object
     * @psalm-param class-string<ExpectedType> $expected
     * @psalm-assert ExpectedType $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertInstanceOf
     */
    function assertInstanceOf($expected, $actual, $message = '')
    {
        Assert::assertInstanceOf(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotInstanceOf')) {
    /**
     * Asserts that a variable is not of a given type.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @psalm-template ExpectedType of object
     * @psalm-param class-string<ExpectedType> $expected
     * @psalm-assert !ExpectedType $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotInstanceOf
     */
    function assertNotInstanceOf($expected, $actual, $message = '')
    {
        Assert::assertNotInstanceOf(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsArray')) {
    /**
     * Asserts that a variable is of type array.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert array $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsArray
     */
    function assertIsArray($actual, $message = '')
    {
        Assert::assertIsArray(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsBool')) {
    /**
     * Asserts that a variable is of type bool.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert bool $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsBool
     */
    function assertIsBool($actual, $message = '')
    {
        Assert::assertIsBool(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsFloat')) {
    /**
     * Asserts that a variable is of type float.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert float $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsFloat
     */
    function assertIsFloat($actual, $message = '')
    {
        Assert::assertIsFloat(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsInt')) {
    /**
     * Asserts that a variable is of type int.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert int $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsInt
     */
    function assertIsInt($actual, $message = '')
    {
        Assert::assertIsInt(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNumeric')) {
    /**
     * Asserts that a variable is of type numeric.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert numeric $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNumeric
     */
    function assertIsNumeric($actual, $message = '')
    {
        Assert::assertIsNumeric(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsObject')) {
    /**
     * Asserts that a variable is of type object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert object $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsObject
     */
    function assertIsObject($actual, $message = '')
    {
        Assert::assertIsObject(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsResource')) {
    /**
     * Asserts that a variable is of type resource.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert resource $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsResource
     */
    function assertIsResource($actual, $message = '')
    {
        Assert::assertIsResource(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsClosedResource')) {
    /**
     * Asserts that a variable is of type resource and is closed.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert resource $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsClosedResource
     */
    function assertIsClosedResource($actual, $message = '')
    {
        Assert::assertIsClosedResource(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsString')) {
    /**
     * Asserts that a variable is of type string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert string $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsString
     */
    function assertIsString($actual, $message = '')
    {
        Assert::assertIsString(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsScalar')) {
    /**
     * Asserts that a variable is of type scalar.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert scalar $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsScalar
     */
    function assertIsScalar($actual, $message = '')
    {
        Assert::assertIsScalar(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsCallable')) {
    /**
     * Asserts that a variable is of type callable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert callable $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsCallable
     */
    function assertIsCallable($actual, $message = '')
    {
        Assert::assertIsCallable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsIterable')) {
    /**
     * Asserts that a variable is of type iterable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert iterable $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsIterable
     */
    function assertIsIterable($actual, $message = '')
    {
        Assert::assertIsIterable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotArray')) {
    /**
     * Asserts that a variable is not of type array.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !array $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotArray
     */
    function assertIsNotArray($actual, $message = '')
    {
        Assert::assertIsNotArray(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotBool')) {
    /**
     * Asserts that a variable is not of type bool.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !bool $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotBool
     */
    function assertIsNotBool($actual, $message = '')
    {
        Assert::assertIsNotBool(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotFloat')) {
    /**
     * Asserts that a variable is not of type float.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !float $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotFloat
     */
    function assertIsNotFloat($actual, $message = '')
    {
        Assert::assertIsNotFloat(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotInt')) {
    /**
     * Asserts that a variable is not of type int.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !int $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotInt
     */
    function assertIsNotInt($actual, $message = '')
    {
        Assert::assertIsNotInt(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotNumeric')) {
    /**
     * Asserts that a variable is not of type numeric.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !numeric $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotNumeric
     */
    function assertIsNotNumeric($actual, $message = '')
    {
        Assert::assertIsNotNumeric(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotObject')) {
    /**
     * Asserts that a variable is not of type object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !object $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotObject
     */
    function assertIsNotObject($actual, $message = '')
    {
        Assert::assertIsNotObject(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotResource')) {
    /**
     * Asserts that a variable is not of type resource.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !resource $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotResource
     */
    function assertIsNotResource($actual, $message = '')
    {
        Assert::assertIsNotResource(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotClosedResource')) {
    /**
     * Asserts that a variable is not of type resource.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !resource $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotClosedResource
     */
    function assertIsNotClosedResource($actual, $message = '')
    {
        Assert::assertIsNotClosedResource(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotString')) {
    /**
     * Asserts that a variable is not of type string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !string $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotString
     */
    function assertIsNotString($actual, $message = '')
    {
        Assert::assertIsNotString(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotScalar')) {
    /**
     * Asserts that a variable is not of type scalar.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !scalar $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotScalar
     */
    function assertIsNotScalar($actual, $message = '')
    {
        Assert::assertIsNotScalar(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotCallable')) {
    /**
     * Asserts that a variable is not of type callable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !callable $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotCallable
     */
    function assertIsNotCallable($actual, $message = '')
    {
        Assert::assertIsNotCallable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertIsNotIterable')) {
    /**
     * Asserts that a variable is not of type iterable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !iterable $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotIterable
     */
    function assertIsNotIterable($actual, $message = '')
    {
        Assert::assertIsNotIterable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertMatchesRegularExpression')) {
    /**
     * Asserts that a string matches a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertMatchesRegularExpression
     */
    function assertMatchesRegularExpression($pattern, $string, $message = '')
    {
        Assert::assertMatchesRegularExpression(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertRegExp')) {
    /**
     * Asserts that a string matches a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4086
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertRegExp
     */
    function assertRegExp($pattern, $string, $message = '')
    {
        Assert::assertRegExp(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertDoesNotMatchRegularExpression')) {
    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDoesNotMatchRegularExpression
     */
    function assertDoesNotMatchRegularExpression($pattern, $string, $message = '')
    {
        Assert::assertDoesNotMatchRegularExpression(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotRegExp')) {
    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4089
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotRegExp
     */
    function assertNotRegExp($pattern, $string, $message = '')
    {
        Assert::assertNotRegExp(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertSameSize')) {
    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is the same.
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertSameSize
     */
    function assertSameSize($expected, $actual, $message = '')
    {
        Assert::assertSameSize(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertNotSameSize')) {
    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is not the same.
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotSameSize
     */
    function assertNotSameSize($expected, $actual, $message = '')
    {
        Assert::assertNotSameSize(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringMatchesFormat')) {
    /**
     * Asserts that a string matches a given format string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringMatchesFormat
     */
    function assertStringMatchesFormat($format, $string, $message = '')
    {
        Assert::assertStringMatchesFormat(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringNotMatchesFormat')) {
    /**
     * Asserts that a string does not match a given format string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotMatchesFormat
     */
    function assertStringNotMatchesFormat($format, $string, $message = '')
    {
        Assert::assertStringNotMatchesFormat(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringMatchesFormatFile')) {
    /**
     * Asserts that a string matches a given format file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringMatchesFormatFile
     */
    function assertStringMatchesFormatFile($formatFile, $string, $message = '')
    {
        Assert::assertStringMatchesFormatFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringNotMatchesFormatFile')) {
    /**
     * Asserts that a string does not match a given format string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotMatchesFormatFile
     */
    function assertStringNotMatchesFormatFile($formatFile, $string, $message = '')
    {
        Assert::assertStringNotMatchesFormatFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringStartsWith')) {
    /**
     * Asserts that a string starts with a given prefix.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringStartsWith
     */
    function assertStringStartsWith($prefix, $string, $message = '')
    {
        Assert::assertStringStartsWith(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringStartsNotWith')) {
    /**
     * Asserts that a string starts not with a given prefix.
     *
     * @param string $prefix
     * @param string $string
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringStartsNotWith
     */
    function assertStringStartsNotWith($prefix, $string, $message = '')
    {
        Assert::assertStringStartsNotWith(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringContainsString')) {
    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringContainsString
     */
    function assertStringContainsString($needle, $haystack, $message = '')
    {
        Assert::assertStringContainsString(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringContainsStringIgnoringCase')) {
    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringContainsStringIgnoringCase
     */
    function assertStringContainsStringIgnoringCase($needle, $haystack, $message = '')
    {
        Assert::assertStringContainsStringIgnoringCase(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringNotContainsString')) {
    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotContainsString
     */
    function assertStringNotContainsString($needle, $haystack, $message = '')
    {
        Assert::assertStringNotContainsString(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringNotContainsStringIgnoringCase')) {
    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotContainsStringIgnoringCase
     */
    function assertStringNotContainsStringIgnoringCase($needle, $haystack, $message = '')
    {
        Assert::assertStringNotContainsStringIgnoringCase(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringEndsWith')) {
    /**
     * Asserts that a string ends with a given suffix.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEndsWith
     */
    function assertStringEndsWith($suffix, $string, $message = '')
    {
        Assert::assertStringEndsWith(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertStringEndsNotWith')) {
    /**
     * Asserts that a string ends not with a given suffix.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEndsNotWith
     */
    function assertStringEndsNotWith($suffix, $string, $message = '')
    {
        Assert::assertStringEndsNotWith(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertXmlFileEqualsXmlFile')) {
    /**
     * Asserts that two XML files are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlFileEqualsXmlFile
     */
    function assertXmlFileEqualsXmlFile($expectedFile, $actualFile, $message = '')
    {
        Assert::assertXmlFileEqualsXmlFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertXmlFileNotEqualsXmlFile')) {
    /**
     * Asserts that two XML files are not equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlFileNotEqualsXmlFile
     */
    function assertXmlFileNotEqualsXmlFile($expectedFile, $actualFile, $message = '')
    {
        Assert::assertXmlFileNotEqualsXmlFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertXmlStringEqualsXmlFile')) {
    /**
     * Asserts that two XML documents are equal.
     *
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlStringEqualsXmlFile
     */
    function assertXmlStringEqualsXmlFile($expectedFile, $actualXml, $message = '')
    {
        Assert::assertXmlStringEqualsXmlFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertXmlStringNotEqualsXmlFile')) {
    /**
     * Asserts that two XML documents are not equal.
     *
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlStringNotEqualsXmlFile
     */
    function assertXmlStringNotEqualsXmlFile($expectedFile, $actualXml, $message = '')
    {
        Assert::assertXmlStringNotEqualsXmlFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertXmlStringEqualsXmlString')) {
    /**
     * Asserts that two XML documents are equal.
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlStringEqualsXmlString
     */
    function assertXmlStringEqualsXmlString($expectedXml, $actualXml, $message = '')
    {
        Assert::assertXmlStringEqualsXmlString(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertXmlStringNotEqualsXmlString')) {
    /**
     * Asserts that two XML documents are not equal.
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlStringNotEqualsXmlString
     */
    function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, $message = '')
    {
        Assert::assertXmlStringNotEqualsXmlString(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertEqualXMLStructure')) {
    /**
     * Asserts that a hierarchy of DOMElements matches.
     *
     * @throws AssertionFailedError
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4091
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEqualXMLStructure
     */
    function assertEqualXMLStructure(DOMElement $expectedElement, DOMElement $actualElement, $checkAttributes = false, $message = '')
    {
        Assert::assertEqualXMLStructure(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertThat')) {
    /**
     * Evaluates a PHPUnit\Framework\Constraint matcher object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertThat
     */
    function assertThat($value, Constraint $constraint, $message = '')
    {
        Assert::assertThat(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertJson')) {
    /**
     * Asserts that a string is a valid JSON string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJson
     */
    function assertJson($actualJson, $message = '')
    {
        Assert::assertJson(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertJsonStringEqualsJsonString')) {
    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonStringEqualsJsonString
     */
    function assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message = '')
    {
        Assert::assertJsonStringEqualsJsonString(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertJsonStringNotEqualsJsonString')) {
    /**
     * Asserts that two given JSON encoded objects or arrays are not equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonStringNotEqualsJsonString
     */
    function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message = '')
    {
        Assert::assertJsonStringNotEqualsJsonString(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertJsonStringEqualsJsonFile')) {
    /**
     * Asserts that the generated JSON encoded object and the content of the given file are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonStringEqualsJsonFile
     */
    function assertJsonStringEqualsJsonFile($expectedFile, $actualJson, $message = '')
    {
        Assert::assertJsonStringEqualsJsonFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertJsonStringNotEqualsJsonFile')) {
    /**
     * Asserts that the generated JSON encoded object and the content of the given file are not equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonStringNotEqualsJsonFile
     */
    function assertJsonStringNotEqualsJsonFile($expectedFile, $actualJson, $message = '')
    {
        Assert::assertJsonStringNotEqualsJsonFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertJsonFileEqualsJsonFile')) {
    /**
     * Asserts that two JSON files are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonFileEqualsJsonFile
     */
    function assertJsonFileEqualsJsonFile($expectedFile, $actualFile, $message = '')
    {
        Assert::assertJsonFileEqualsJsonFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\assertJsonFileNotEqualsJsonFile')) {
    /**
     * Asserts that two JSON files are not equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonFileNotEqualsJsonFile
     */
    function assertJsonFileNotEqualsJsonFile($expectedFile, $actualFile, $message = '')
    {
        Assert::assertJsonFileNotEqualsJsonFile(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\logicalAnd')) {
    function logicalAnd()
    {
        return Assert::logicalAnd(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\logicalOr')) {
    function logicalOr()
    {
        return Assert::logicalOr(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\logicalNot')) {
    function logicalNot(Constraint $constraint)
    {
        return Assert::logicalNot(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\logicalXor')) {
    function logicalXor()
    {
        return Assert::logicalXor(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\anything')) {
    function anything()
    {
        return Assert::anything(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isTrue')) {
    function isTrue()
    {
        return Assert::isTrue(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\callback')) {
    function callback(callable $callback)
    {
        return Assert::callback(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isFalse')) {
    function isFalse()
    {
        return Assert::isFalse(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isJson')) {
    function isJson()
    {
        return Assert::isJson(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isNull')) {
    function isNull()
    {
        return Assert::isNull(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isFinite')) {
    function isFinite()
    {
        return Assert::isFinite(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isInfinite')) {
    function isInfinite()
    {
        return Assert::isInfinite(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isNan')) {
    function isNan()
    {
        return Assert::isNan(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\containsEqual')) {
    function containsEqual($value)
    {
        return Assert::containsEqual(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\containsIdentical')) {
    function containsIdentical($value)
    {
        return Assert::containsIdentical(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\containsOnly')) {
    function containsOnly($type)
    {
        return Assert::containsOnly(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\containsOnlyInstancesOf')) {
    function containsOnlyInstancesOf($className)
    {
        return Assert::containsOnlyInstancesOf(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\arrayHasKey')) {
    function arrayHasKey($key)
    {
        return Assert::arrayHasKey(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\equalTo')) {
    function equalTo($value)
    {
        return Assert::equalTo(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\equalToCanonicalizing')) {
    function equalToCanonicalizing($value)
    {
        return Assert::equalToCanonicalizing(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\equalToIgnoringCase')) {
    function equalToIgnoringCase($value)
    {
        return Assert::equalToIgnoringCase(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\equalToWithDelta')) {
    function equalToWithDelta($value, $delta)
    {
        return Assert::equalToWithDelta(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isEmpty')) {
    function isEmpty()
    {
        return Assert::isEmpty(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isWritable')) {
    function isWritable()
    {
        return Assert::isWritable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isReadable')) {
    function isReadable()
    {
        return Assert::isReadable(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\directoryExists')) {
    function directoryExists()
    {
        return Assert::directoryExists(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\fileExists')) {
    function fileExists()
    {
        return Assert::fileExists(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\greaterThan')) {
    function greaterThan($value)
    {
        return Assert::greaterThan(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\greaterThanOrEqual')) {
    function greaterThanOrEqual($value)
    {
        return Assert::greaterThanOrEqual(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\classHasAttribute')) {
    function classHasAttribute($attributeName)
    {
        return Assert::classHasAttribute(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\classHasStaticAttribute')) {
    function classHasStaticAttribute($attributeName)
    {
        return Assert::classHasStaticAttribute(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\objectHasAttribute')) {
    function objectHasAttribute($attributeName)
    {
        return Assert::objectHasAttribute(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\identicalTo')) {
    function identicalTo($value)
    {
        return Assert::identicalTo(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isInstanceOf')) {
    function isInstanceOf($className)
    {
        return Assert::isInstanceOf(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\isType')) {
    function isType($type)
    {
        return Assert::isType(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\lessThan')) {
    function lessThan($value)
    {
        return Assert::lessThan(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\lessThanOrEqual')) {
    function lessThanOrEqual($value)
    {
        return Assert::lessThanOrEqual(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\matchesRegularExpression')) {
    function matchesRegularExpression($pattern)
    {
        return Assert::matchesRegularExpression(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\matches')) {
    function matches($string)
    {
        return Assert::matches(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\stringStartsWith')) {
    function stringStartsWith($prefix)
    {
        return Assert::stringStartsWith(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\stringContains')) {
    function stringContains($string, $case = true)
    {
        return Assert::stringContains(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\stringEndsWith')) {
    function stringEndsWith($suffix)
    {
        return Assert::stringEndsWith(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\countOf')) {
    function countOf($count)
    {
        return Assert::countOf(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\objectEquals')) {
    function objectEquals(object $object, $method = 'equals')
    {
        return Assert::objectEquals(...func_get_args());
    }
}

if (!function_exists('PHPUnit\Framework\any')) {
    /**
     * Returns a matcher that matches when the method is executed
     * zero or more times.
     */
    function any()
    {
        return new AnyInvokedCountMatcher;
    }
}

if (!function_exists('PHPUnit\Framework\never')) {
    /**
     * Returns a matcher that matches when the method is never executed.
     */
    function never()
    {
        return new InvokedCountMatcher(0);
    }
}

if (!function_exists('PHPUnit\Framework\atLeast')) {
    /**
     * Returns a matcher that matches when the method is executed
     * at least N times.
     */
    function atLeast($requiredInvocations)
    {
        return new InvokedAtLeastCountMatcher(
            $requiredInvocations
        );
    }
}

if (!function_exists('PHPUnit\Framework\atLeastOnce')) {
    /**
     * Returns a matcher that matches when the method is executed at least once.
     */
    function atLeastOnce()
    {
        return new InvokedAtLeastOnceMatcher;
    }
}

if (!function_exists('PHPUnit\Framework\once')) {
    /**
     * Returns a matcher that matches when the method is executed exactly once.
     */
    function once()
    {
        return new InvokedCountMatcher(1);
    }
}

if (!function_exists('PHPUnit\Framework\exactly')) {
    /**
     * Returns a matcher that matches when the method is executed
     * exactly $count times.
     */
    function exactly($count)
    {
        return new InvokedCountMatcher($count);
    }
}

if (!function_exists('PHPUnit\Framework\atMost')) {
    /**
     * Returns a matcher that matches when the method is executed
     * at most N times.
     */
    function atMost($allowedInvocations)
    {
        return new InvokedAtMostCountMatcher($allowedInvocations);
    }
}

if (!function_exists('PHPUnit\Framework\at')) {
    /**
     * Returns a matcher that matches when the method is executed
     * at the given index.
     */
    function at($index)
    {
        return new InvokedAtIndexMatcher($index);
    }
}

if (!function_exists('PHPUnit\Framework\returnValue')) {
    function returnValue($value)
    {
        return new ReturnStub($value);
    }
}

if (!function_exists('PHPUnit\Framework\returnValueMap')) {
    function returnValueMap(array $valueMap)
    {
        return new ReturnValueMapStub($valueMap);
    }
}

if (!function_exists('PHPUnit\Framework\returnArgument')) {
    function returnArgument($argumentIndex)
    {
        return new ReturnArgumentStub($argumentIndex);
    }
}

if (!function_exists('PHPUnit\Framework\returnCallback')) {
    function returnCallback($callback)
    {
        return new ReturnCallbackStub($callback);
    }
}

if (!function_exists('PHPUnit\Framework\returnSelf')) {
    /**
     * Returns the current object.
     *
     * This method is useful when mocking a fluent interface.
     */
    function returnSelf()
    {
        return new ReturnSelfStub;
    }
}

if (!function_exists('PHPUnit\Framework\throwException')) {
    function throwException(Throwable $exception)
    {
        return new ExceptionStub($exception);
    }
}

if (!function_exists('PHPUnit\Framework\onConsecutiveCalls')) {
    function onConsecutiveCalls()
    {
        $args = func_get_args();

        return new ConsecutiveCallsStub($args);
    }
}
