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

final class MixedType extends Type {
    public function isAssignable(Type $other) {
        return !$other instanceof VoidType;
    }

    public function asString() {
        return 'mixed';
    }

    public function name() {
        return 'mixed';
    }

    public function allowsNull() {
        return true;
    }

    public function isMixed() {
        return true;
    }
}
