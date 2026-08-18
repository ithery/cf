<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Type\Parser\Lexer;

use function implode;
use function trim;

/** @internal */
final class TokenizedAnnotation
{
    private string $name;
    private array $tokens;

    public function __construct(
        /** @var non-empty-string */
        string $name,
        /** @var non-empty-list<string> */
        array $tokens
    ) {
        $this->name = $name;
        $this->tokens = $tokens;
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function splice(int $length): string
    {
        return implode('', array_splice($this->tokens, 0, $length));
    }

    /**
     * @return non-empty-string
     */
    public function allAfter(int $offset): string
    {
        /** @var non-empty-string */
        return implode('', array_splice($this->tokens, $offset));
    }

    /**
     * @return non-empty-array<int, non-empty-string>
     */
    public function filtered(): array
    {
        return array_filter(
            array_map('trim', $this->tokens),
            static function ($value) {
                return $value !== '';
            }
        );
        /** @var non-empty-array<int, non-empty-string> / We can force the type as we know for sure it contains at least one non-empty-string */
        // return array_filter(
        //     array_map(trim(...), $this->tokens),
        //     static fn ($value) => $value !== '',
        // );
    }

    /**
     * @return non-empty-string
     */
    public function raw(): string
    {
        /** @var non-empty-string / We can force the type as we know for sure it contains at least one non-empty-string */
        return implode('', $this->tokens);
    }
}
