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

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
interface Reorderable
{
    public function sortId();

    /**
     * @return list<ExecutionOrderDependency>
     */
    public function provides();

    /**
     * @return list<ExecutionOrderDependency>
     */
    public function requires();
}
