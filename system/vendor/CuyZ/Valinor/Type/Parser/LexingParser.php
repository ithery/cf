<?php

namespace CuyZ\Valinor\Type\Parser;

use CuyZ\Valinor\Type\Parser\Lexer\TokensExtractor;
use CuyZ\Valinor\Type\Parser\Lexer\TokenStream;
use CuyZ\Valinor\Type\Parser\Lexer\TypeLexer;
use CuyZ\Valinor\Type\Type;

/** @internal */
class LexingParser implements TypeParser
{
    private TypeLexer $lexer;
    public function __construct(TypeLexer $lexer) {
        $this->lexer = $lexer;
    }

    public function parse(string $raw): Type
    {
        $tokens = array_map(
            fn (string $symbol) => $this->lexer->tokenize($symbol),
            (new TokensExtractor($raw))->filtered()
        );

        return (new TokenStream(...$tokens))->read();
    }
}
