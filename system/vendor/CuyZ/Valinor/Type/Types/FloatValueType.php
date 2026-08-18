<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Type\Types;

use function assert;
use CuyZ\Valinor\Type\Type;
use CuyZ\Valinor\Compiler\Node;
use CuyZ\Valinor\Type\FixedType;
use CuyZ\Valinor\Type\FloatType;
use CuyZ\Valinor\Compiler\Native\ComplianceNode;
use CuyZ\Valinor\Mapper\Tree\Message\ErrorMessage;

use CuyZ\Valinor\Mapper\Tree\Message\MessageBuilder;

/** @internal */
final class FloatValueType implements FloatType, FixedType {
    private float $value;

    public function __construct(float $value) {
        $this->value = $value;
    }

    public function accepts($value): bool {
        return $value === $this->value;
    }

    public function compiledAccept(ComplianceNode $node): ComplianceNode {
        return $node->equals(Node::value($this->value));
    }

    public function matches(Type $other): bool {
        if ($other instanceof UnionType) {
            return $other->isMatchedBy($this);
        }

        if ($other instanceof self) {
            return $this->value === $other->value;
        }

        return $other instanceof NativeFloatType
            || $other instanceof ScalarConcreteType
            || $other instanceof MixedType;
    }

    public function canCast($value): bool {
        return is_numeric($value) && (float) $value === $this->value;
    }

    public function cast($value): float {
        assert($this->canCast($value));

        return (float) $value; // @phpstan-ignore-line
    }

    public function errorMessage(): ErrorMessage {
        return MessageBuilder::newError('Value {source_value} does not match float value {expected_value}.')
            ->withCode('invalid_float_value')
            ->withParameter('expected_value', (string) $this->value)
            ->build();
    }

    public function value(): float {
        return $this->value;
    }

    public function nativeType(): NativeFloatType {
        return NativeFloatType::get();
    }

    public function toString(): string {
        return (string) $this->value;
    }
}
