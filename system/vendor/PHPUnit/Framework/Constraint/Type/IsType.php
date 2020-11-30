<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint\Type;

use function gettype;
use function is_array;
use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_iterable;
use function is_numeric;
use function is_object;
use function is_scalar;
use function is_string;
use function sprintf;
use PHPUnit\Framework\Constraint\Constraint;


/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class IsType extends Constraint
{
    /**
     * @var string
     */
    const TYPE_ARRAY = 'array';

    /**
     * @var string
     */
    const TYPE_BOOL = 'bool';

    /**
     * @var string
     */
    const TYPE_FLOAT = 'float';

    /**
     * @var string
     */
    const TYPE_INT = 'int';

    /**
     * @var string
     */
    const TYPE_NULL = 'null';

    /**
     * @var string
     */
    const TYPE_NUMERIC = 'numeric';

    /**
     * @var string
     */
    const TYPE_OBJECT = 'object';

    /**
     * @var string
     */
    const TYPE_RESOURCE = 'resource';

    /**
     * @var string
     */
    const TYPE_CLOSED_RESOURCE = 'resource (closed)';

    /**
     * @var string
     */
    const TYPE_STRING = 'string';

    /**
     * @var string
     */
    const TYPE_SCALAR = 'scalar';

    /**
     * @var string
     */
    const TYPE_CALLABLE = 'callable';

    /**
     * @var string
     */
    const TYPE_ITERABLE = 'iterable';

    /**
     * @var array<string,bool>
     */
    public static $KNOWN_TYPES = [
        'array'             => true,
        'boolean'           => true,
        'bool'              => true,
        'double'            => true,
        'float'             => true,
        'integer'           => true,
        'int'               => true,
        'null'              => true,
        'numeric'           => true,
        'object'            => true,
        'real'              => true,
        'resource'          => true,
        'resource (closed)' => true,
        'string'            => true,
        'scalar'            => true,
        'callable'          => true,
        'iterable'          => true,
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct($type)
    {
        if (!isset(self::$KNOWN_TYPES[$type])) {
            throw new \PHPUnit\Framework\Exception\Exception(
                sprintf(
                    'Type specified for PHPUnit\Framework\Constraint\Type\IsType <%s> ' .
                    'is not a valid type.',
                    $type
                )
            );
        }

        $this->type = $type;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString()
    {
        return sprintf(
            'is of type "%s"',
            $this->type
        );
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     */
    protected function matches($other)
    {
        switch ($this->type) {
            case 'numeric':
                return is_numeric($other);

            case 'integer':
            case 'int':
                return is_int($other);

            case 'double':
            case 'float':
            case 'real':
                return is_float($other);

            case 'string':
                return is_string($other);

            case 'boolean':
            case 'bool':
                return is_bool($other);

            case 'null':
                return null === $other;

            case 'array':
                return is_array($other);

            case 'object':
                return is_object($other);

            case 'resource':
                $type = gettype($other);

                return $type === 'resource' || $type === 'resource (closed)';

            case 'resource (closed)':
                return gettype($other) === 'resource (closed)';

            case 'scalar':
                return is_scalar($other);

            case 'callable':
                return is_callable($other);

            case 'iterable':
                return is_iterable($other);

            default:
                return false;
        }
    }
}
