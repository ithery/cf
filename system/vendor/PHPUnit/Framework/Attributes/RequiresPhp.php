<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class RequiresPhp
{
    /**
     * @var string
     */
    private $version;

    /**
     * @psalm-var '<'|'lt'|'<='|'le'|'>'|'gt'|'>='|'ge'|'=='|'='|'eq'|'!='|'<>'|'ne'
     */
    private $operator;

    /**
     * @psalm-param '<'|'lt'|'<='|'le'|'>'|'gt'|'>='|'ge'|'=='|'='|'eq'|'!='|'<>'|'ne' $operator
     */
    public function __construct($version, $operator = '>=')
    {
        $this->version  = $version;
        $this->operator = $operator;
    }

    public function version()
    {
        return $this->version;
    }

    public function operator()
    {
        return $this->operator;
    }
}
