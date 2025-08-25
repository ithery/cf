<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Type\Types;

use function is_callable;
use CuyZ\Valinor\Type\Type;
use CuyZ\Valinor\Compiler\Node;
use CuyZ\Valinor\Utility\IsSingleton;

use CuyZ\Valinor\Compiler\Native\ComplianceNode;

/** @internal */
final class CallableType implements Type {
    use IsSingleton;

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function accepts($value): bool {
        return is_callable($value);
    }

    public function compiledAccept(ComplianceNode $node): ComplianceNode {
        return Node::functionCall('is_callable', [$node]);
    }

    public function matches(Type $other): bool {
        if ($other instanceof UnionType) {
            return $other->isMatchedBy($this);
        }

        return $other instanceof self
            || $other instanceof MixedType;
    }

    public function nativeType(): Type {
        return $this;
    }

    public function toString(): string {
        return 'callable';
    }
}
