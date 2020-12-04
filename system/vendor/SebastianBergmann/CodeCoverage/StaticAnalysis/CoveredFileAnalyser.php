<?php
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

use SebastianBergmann\LinesOfCode\LinesOfCode;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
interface CoveredFileAnalyser
{
    public function classesIn($filename);

    public function traitsIn($filename);

    public function functionsIn($filename);

    public function linesOfCodeFor($filename);

    public function ignoredLinesFor($filename);
}
