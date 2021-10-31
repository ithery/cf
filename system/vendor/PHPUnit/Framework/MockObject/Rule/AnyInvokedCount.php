<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Rule;

use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class AnyInvokedCount extends InvocationOrder
{
    public function toString()
    {
        return 'invoked zero or more times';
    }

    public function verify()
    {
    }

    public function matches(BaseInvocation $invocation)
    {
        return true;
    }

    protected function invokedDo(BaseInvocation $invocation)
    {
    }
}
