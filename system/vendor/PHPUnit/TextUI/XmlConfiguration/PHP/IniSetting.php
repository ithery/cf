<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\PHP;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class IniSetting {
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    public function __construct($name, $value) {
        $this->name = $name;
        $this->value = $value;
    }

    public function name() {
        return $this->name;
    }

    public function value() {
        return $this->value;
    }
}
