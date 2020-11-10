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
final class BackupGlobals
{
    /**
     * @var bool
     */
    private $enabled;

    public function __construct($enabled)
    {
        $this->enabled = $enabled;
    }

    public function enabled()
    {
        return $this->enabled;
    }
}
