<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
interface TestResultCache
{
    public function setState($testName, $state);

    public function getState($testName);

    public function setTime($testName, $time);

    public function getTime($testName);

    public function load();

    public function persist();
}
