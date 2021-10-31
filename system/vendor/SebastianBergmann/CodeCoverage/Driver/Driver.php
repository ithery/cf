<?php
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Driver;

use function sprintf;
use SebastianBergmann\CodeCoverage\BranchAndPathCoverageNotSupportedException;
use SebastianBergmann\CodeCoverage\DeadCodeDetectionNotSupportedException;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use SebastianBergmann\CodeCoverage\RawCodeCoverageData;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
abstract class Driver
{
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    const LINE_NOT_EXECUTABLE = -2;

    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    const LINE_NOT_EXECUTED = -1;

    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    const LINE_EXECUTED = 1;

    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    const BRANCH_NOT_HIT = 0;

    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    const BRANCH_HIT = 1;

    /**
     * @var bool
     */
    private $collectBranchAndPathCoverage = false;

    /**
     * @var bool
     */
    private $detectDeadCode = false;

    /**
     * @throws NoCodeCoverageDriverAvailableException
     * @throws PcovNotAvailableException
     * @throws PhpdbgNotAvailableException
     * @throws XdebugNotAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     *
     * @deprecated Use DriverSelector::forLineCoverage() instead
     */
    public static function forLineCoverage(Filter $filter)
    {
        return (new Selector)->forLineCoverage($filter);
    }

    /**
     * @throws NoCodeCoverageDriverWithPathCoverageSupportAvailableException
     * @throws XdebugNotAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     *
     * @deprecated Use DriverSelector::forLineAndPathCoverage() instead
     */
    public static function forLineAndPathCoverage(Filter $filter)
    {
        return (new Selector)->forLineAndPathCoverage($filter);
    }

    public function canCollectBranchAndPathCoverage()
    {
        return false;
    }

    public function collectsBranchAndPathCoverage()
    {
        return $this->collectBranchAndPathCoverage;
    }

    /**
     * @throws BranchAndPathCoverageNotSupportedException
     */
    public function enableBranchAndPathCoverage()
    {
        if (!$this->canCollectBranchAndPathCoverage()) {
            throw new BranchAndPathCoverageNotSupportedException(
                sprintf(
                    '%s does not support branch and path coverage',
                    $this->nameAndVersion()
                )
            );
        }

        $this->collectBranchAndPathCoverage = true;
    }

    public function disableBranchAndPathCoverage()
    {
        $this->collectBranchAndPathCoverage = false;
    }

    public function canDetectDeadCode()
    {
        return false;
    }

    public function detectsDeadCode()
    {
        return $this->detectDeadCode;
    }

    /**
     * @throws DeadCodeDetectionNotSupportedException
     */
    public function enableDeadCodeDetection()
    {
        if (!$this->canDetectDeadCode()) {
            throw new DeadCodeDetectionNotSupportedException(
                sprintf(
                    '%s does not support dead code detection',
                    $this->nameAndVersion()
                )
            );
        }

        $this->detectDeadCode = true;
    }

    public function disableDeadCodeDetection()
    {
        $this->detectDeadCode = false;
    }

    abstract public function nameAndVersion();

    abstract public function start();

    abstract public function stop();
}
