<?php
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Type;

final class VoidType extends Type {
    public function isAssignable(Type $other) {
        return $other instanceof self;
    }

    public function name() {
        return 'void';
    }

    public function allowsNull() {
        return false;
    }

    public function isVoid() {
        return true;
    }
}
