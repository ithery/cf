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

use const XDEBUG_CC_BRANCH_CHECK;
use const XDEBUG_CC_DEAD_CODE;
use const XDEBUG_CC_UNUSED;
use const XDEBUG_FILTER_CODE_COVERAGE;
use const XDEBUG_PATH_INCLUDE;
use function extension_loaded;
use function in_array;
use function ini_get;
use function phpversion;
use function sprintf;
use function version_compare;
use function xdebug_get_code_coverage;
use function xdebug_set_filter;
use function xdebug_start_code_coverage;
use function xdebug_stop_code_coverage;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\RawCodeCoverageData;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Xdebug3Driver extends Driver
{
    /**
     * @throws XdebugNotAvailableException
     * @throws WrongXdebugVersionException
     * @throws Xdebug3NotEnabledException
     */
    public function __construct(Filter $filter)
    {
        if (!extension_loaded('xdebug')) {
            throw new XdebugNotAvailableException;
        }

        if (version_compare(phpversion('xdebug'), '3', '<')) {
            throw new WrongXdebugVersionException(
                sprintf(
                    'This driver requires Xdebug 3 but version %s is loaded',
                    phpversion('xdebug')
                )
            );
        }

        if (!ini_get('xdebug.mode') || !in_array('coverage', explode(',', ini_get('xdebug.mode')), true)) {
            throw new Xdebug3NotEnabledException;
        }

        if (!$filter->isEmpty()) {
            xdebug_set_filter(
                XDEBUG_FILTER_CODE_COVERAGE,
                XDEBUG_PATH_INCLUDE,
                $filter->files()
            );
        }
    }

    public function canCollectBranchAndPathCoverage()
    {
        return true;
    }

    public function canDetectDeadCode()
    {
        return true;
    }

    public function start()
    {
        $flags = XDEBUG_CC_UNUSED;

        if ($this->detectsDeadCode() || $this->collectsBranchAndPathCoverage()) {
            $flags |= XDEBUG_CC_DEAD_CODE;
        }

        if ($this->collectsBranchAndPathCoverage()) {
            $flags |= XDEBUG_CC_BRANCH_CHECK;
        }

        xdebug_start_code_coverage($flags);
    }

    public function stop()
    {
        $data = xdebug_get_code_coverage();

        xdebug_stop_code_coverage();

        if ($this->collectsBranchAndPathCoverage()) {
            return RawCodeCoverageData::fromXdebugWithPathCoverage($data);
        }

        return RawCodeCoverageData::fromXdebugWithoutPathCoverage($data);
    }

    public function nameAndVersion()
    {
        return 'Xdebug ' . phpversion('xdebug');
    }
}
