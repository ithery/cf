<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Type\Parser\Lexer;

use CuyZ\Valinor\Type\Parser\Lexer\Token\ArrayToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\CallableToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\ClassStringToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\ClosingBracketToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\ClosingCurlyBracketToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\ClosingSquareBracketToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\ColonToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\CommaToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\DoubleColonToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\FloatValueToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\IntegerToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\IntegerValueToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\IntersectionToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\IterableToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\ListToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\NullableToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\OpeningBracketToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\OpeningCurlyBracketToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\OpeningSquareBracketToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\StringValueToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\Token;
use CuyZ\Valinor\Type\Parser\Lexer\Token\TripleDotsToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\TypeToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\UnionToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\ValueOfToken;
use CuyZ\Valinor\Type\Types\ArrayKeyType;
use CuyZ\Valinor\Type\Types\BooleanValueType;
use CuyZ\Valinor\Type\Types\MixedType;
use CuyZ\Valinor\Type\Types\NativeBooleanType;
use CuyZ\Valinor\Type\Types\NativeFloatType;
use CuyZ\Valinor\Type\Types\NativeStringType;
use CuyZ\Valinor\Type\Types\NegativeIntegerType;
use CuyZ\Valinor\Type\Types\NonEmptyStringType;
use CuyZ\Valinor\Type\Types\NonNegativeIntegerType;
use CuyZ\Valinor\Type\Types\NonPositiveIntegerType;
use CuyZ\Valinor\Type\Types\NullType;
use CuyZ\Valinor\Type\Types\NumericStringType;
use CuyZ\Valinor\Type\Types\PositiveIntegerType;
use CuyZ\Valinor\Type\Types\ScalarConcreteType;
use CuyZ\Valinor\Type\Types\UndefinedObjectType;

use function filter_var;
use function is_numeric;
use function str_starts_with;
use function strtolower;

/** @internal */
final class NativeLexer implements TypeLexer
{
    private TypeLexer $delegate;
    public function __construct(TypeLexer $delegate) {
        $this->delegate = $delegate;
    }

    public function tokenize(string $symbol): Token
    {
        $lowerSymbol = strtolower($symbol);

        switch ($lowerSymbol) {
            case '|':
                return UnionToken::get();
            case '&':
                return IntersectionToken::get();
            case '<':
                return OpeningBracketToken::get();
            case '>':
                return ClosingBracketToken::get();
            case '[':
                return OpeningSquareBracketToken::get();
            case ']':
                return ClosingSquareBracketToken::get();
            case '{':
                return OpeningCurlyBracketToken::get();
            case '}':
                return ClosingCurlyBracketToken::get();
            case '::':
                return DoubleColonToken::get();
            case ':':
                return ColonToken::get();
            case '?':
                return NullableToken::get();
            case ',':
                return CommaToken::get();
            case '...':
                return TripleDotsToken::get();

            case 'int':
            case 'integer':
                return IntegerToken::get();
            case 'array':
                return ArrayToken::array();
            case 'non-empty-array':
                return ArrayToken::nonEmptyArray();
            case 'list':
                return ListToken::list();
            case 'non-empty-list':
                return ListToken::nonEmptyList();
            case 'iterable':
                return IterableToken::get();
            case 'class-string':
                return ClassStringToken::get();
            case 'callable':
                return CallableToken::get();
            case 'value-of':
                return ValueOfToken::get();

            case 'null':
                return new TypeToken(NullType::get());
            case 'true':
                return new TypeToken(BooleanValueType::true());
            case 'false':
                return new TypeToken(BooleanValueType::false());
            case 'mixed':
                return new TypeToken(MixedType::get());
            case 'float':
                return new TypeToken(NativeFloatType::get());
            case 'positive-int':
                return new TypeToken(PositiveIntegerType::get());
            case 'negative-int':
                return new TypeToken(NegativeIntegerType::get());
            case 'non-positive-int':
                return new TypeToken(NonPositiveIntegerType::get());
            case 'non-negative-int':
                return new TypeToken(NonNegativeIntegerType::get());
            case 'string':
                return new TypeToken(NativeStringType::get());
            case 'non-empty-string':
                return new TypeToken(NonEmptyStringType::get());
            case 'numeric-string':
                return new TypeToken(NumericStringType::get());
            case 'bool':
            case 'boolean':
                return new TypeToken(NativeBooleanType::get());
            case 'array-key':
                return new TypeToken(ArrayKeyType::default());
            case 'object':
                return new TypeToken(UndefinedObjectType::get());
            case 'scalar':
                return new TypeToken(ScalarConcreteType::get());
        }

        if (substr($symbol, 0, 1) === "'" || substr($symbol, 0, 1) === '"') {
            return new StringValueToken($symbol);
        }

        if (filter_var($symbol, FILTER_VALIDATE_INT) !== false) {
            return new IntegerValueToken((int)$symbol);
        }

        if (is_numeric($symbol)) {
            return new FloatValueToken((float)$symbol);
        }

        return $this->delegate->tokenize($symbol);
    }
}
