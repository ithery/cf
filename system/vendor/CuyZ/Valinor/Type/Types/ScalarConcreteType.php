<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Type\Types;

use Stringable;
use function assert;
use function is_scalar;
use CuyZ\Valinor\Type\Type;
use CuyZ\Valinor\Compiler\Node;
use CuyZ\Valinor\Type\FloatType;
use CuyZ\Valinor\Type\ScalarType;
use CuyZ\Valinor\Type\StringType;
use CuyZ\Valinor\Type\BooleanType;
use CuyZ\Valinor\Type\IntegerType;
use CuyZ\Valinor\Utility\IsSingleton;
use CuyZ\Valinor\Compiler\Native\ComplianceNode;

use CuyZ\Valinor\Mapper\Tree\Message\ErrorMessage;
use CuyZ\Valinor\Mapper\Tree\Message\MessageBuilder;

/** @internal */
final class ScalarConcreteType implements ScalarType {
    use IsSingleton;

    public function accepts($value): bool {
        return is_scalar($value);
    }

    public function compiledAccept(ComplianceNode $node): ComplianceNode {
        return Node::functionCall('is_scalar', [$node]);
    }

    public function matches(Type $other): bool {
        if ($other instanceof UnionType) {
            return $other->isMatchedBy($this);
        }

        return $other instanceof self
            || $other instanceof IntegerType
            || $other instanceof FloatType
            || $other instanceof StringType
            || $other instanceof BooleanType
            || $other instanceof MixedType;
    }

    public function canCast($value): bool {
        return is_scalar($value) || $value instanceof Stringable;
    }

    /**
     * @param mixed $value
     *
     * @return bool|string|int|float
     */
    public function cast($value) {
        assert($this->canCast($value));

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        return $value; // @phpstan-ignore return.type (must be scalar)
    }

    public function errorMessage(): ErrorMessage {
        return MessageBuilder::newError('Value {source_value} is not a valid scalar.')->build();
    }

    public function nativeType(): UnionType {
        return new UnionType(
            new NativeIntegerType(),
            new NativeFloatType(),
            new NativeStringType(),
            new NativeBooleanType(),
        );
    }

    public function toString(): string {
        return 'scalar';
    }
}
