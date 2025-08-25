<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Type\Parser\Lexer;

use CuyZ\Valinor\Type\Parser\Factory\Specifications\TypeParserSpecification;
use CuyZ\Valinor\Type\Parser\Lexer\Token\Token;
use CuyZ\Valinor\Type\Parser\Lexer\Token\VacantToken;

/** @internal */
final class SpecificationsLexer implements TypeLexer
{
    private array $specifications;
    public function __construct(
        /** @var array<TypeParserSpecification> */
        array $specifications
    ) {
        $this->specifications = $specifications;
    }

    public function tokenize(string $symbol): Token
    {
        return (new VacantToken($symbol, $this->specifications));
    }
}
