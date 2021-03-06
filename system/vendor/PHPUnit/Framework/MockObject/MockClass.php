<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework\MockObject;

use function class_exists;
use function call_user_func;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class MockClass implements MockType {
    /**
     * @var string
     */
    private $classCode;

    /**
     * @var string
     */
    private $mockName;

    /**
     * @var ConfigurableMethod[]
     */
    private $configurableMethods;

    public function __construct($classCode, $mockName, array $configurableMethods) {
        $this->classCode = $classCode;
        $this->mockName = $mockName;
        $this->configurableMethods = $configurableMethods;
    }

    public function generate() {
        if (!class_exists($this->mockName, false)) {
            eval($this->classCode);

            call_user_func(
                [
                    $this->mockName,
                    '__phpunit_initConfigurableMethods',
                ],
                ...$this->configurableMethods
            );
        }

        return $this->mockName;
    }

    public function getClassCode() {
        return $this->classCode;
    }
}
