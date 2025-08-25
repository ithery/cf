<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Type\Parser\Factory\Specifications;

use CuyZ\Valinor\Type\Parser\Lexer\Token\ObjectToken;
use CuyZ\Valinor\Type\Parser\Lexer\Token\TraversingToken;

/** @internal */
final class ClassContextSpecification implements TypeParserSpecification
{
    private string $className;
    public function __construct(
        /** @var class-string */
        string $className
    ) {
        $this->className = $className;
    }

    public function manipulateToken(TraversingToken $token): TraversingToken
    {
        if ($token->symbol() === 'self' || $token->symbol() === 'static') {
            return new ObjectToken($this->className);
        }

        return $token;
    }
}
