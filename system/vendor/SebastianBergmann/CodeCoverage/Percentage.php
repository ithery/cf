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

use function sprintf;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Percentage
{
    /**
     * @var float
     */
    private $fraction;

    /**
     * @var float
     */
    private $total;

    public static function fromFractionAndTotal(float $fraction, $total)
    {
        return new self($fraction, $total);
    }

    private function __construct(float $fraction, $total)
    {
        $this->fraction = $fraction;
        $this->total    = $total;
    }

    public function asFloat()
    {
        if ($this->total > 0) {
            return (float) ($this->fraction / $this->total) * 100;
        }

        return 100.0;
    }

    public function asString()
    {
        if ($this->total > 0) {
            return sprintf('%01.2F%%', $this->asFloat());
        }

        return '';
    }

    public function asFixedWidthString()
    {
        if ($this->total > 0) {
            return sprintf('%6.2F%%', $this->asFloat());
        }

        return '';
    }
}
