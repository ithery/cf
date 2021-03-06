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

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class TestWith {
    /**
     * @var array
     */
    private $data;

    public function __construct(...$data) {
        $this->data = $data;
    }

    public function data() {
        return $this->data;
    }
}
