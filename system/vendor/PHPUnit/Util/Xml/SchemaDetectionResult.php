<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\Xml;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @psalm-immutable
 */
abstract class SchemaDetectionResult
{
    public function detected()
    {
        return false;
    }

    /**
     * @throws Exception
     */
    public function version()
    {
        throw new Exception('No supported schema was detected');
    }
}
