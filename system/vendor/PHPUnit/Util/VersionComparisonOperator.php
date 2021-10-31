<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util;

use function in_array;
use function sprintf;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class VersionComparisonOperator
{
    /**
     * @psalm-var '<'|'lt'|'<='|'le'|'>'|'gt'|'>='|'ge'|'=='|'='|'eq'|'!='|'<>'|'ne'
     */
    private $operator;

    public function __construct($operator)
    {
        $this->ensureOperatorIsValid($operator);

        $this->operator = $operator;
    }

    /**
     * @return '<'|'lt'|'<='|'le'|'>'|'gt'|'>='|'ge'|'=='|'='|'eq'|'!='|'<>'|'ne'
     */
    public function asString()
    {
        return $this->operator;
    }

    /**
     * @throws Exception
     *
     * @psalm-assert '<'|'lt'|'<='|'le'|'>'|'gt'|'>='|'ge'|'=='|'='|'eq'|'!='|'<>'|'ne' $operator
     */
    private function ensureOperatorIsValid($operator)
    {
        if (!in_array($operator, ['<', 'lt', '<=', 'le', '>', 'gt', '>=', 'ge', '==', '=', 'eq', '!=', '<>', 'ne'], true)) {
            throw new Exception(
                sprintf(
                    '"%s" is not a valid version_compare() operator',
                    $operator
                )
            );
        }
    }
}
