<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report;

use PHPUnit\TextUI\XmlConfiguration\Directory;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Html
{
    /**
     * @var Directory
     */
    private $target;

    /**
     * @var int
     */
    private $lowUpperBound;

    /**
     * @var int
     */
    private $highLowerBound;

    public function __construct(Directory $target, $lowUpperBound, $highLowerBound)
    {
        $this->target         = $target;
        $this->lowUpperBound  = $lowUpperBound;
        $this->highLowerBound = $highLowerBound;
    }

    public function target()
    {
        return $this->target;
    }

    public function lowUpperBound()
    {
        return $this->lowUpperBound;
    }

    public function highLowerBound()
    {
        return $this->highLowerBound;
    }
}
