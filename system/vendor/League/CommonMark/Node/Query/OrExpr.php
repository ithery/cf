<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Node\Query;

use League\CommonMark\Node\Node;

/**
 * @internal
 */
final class OrExpr implements ExpressionInterface {
    /**
     * @var callable[]
     * @psalm-var list<callable(Node): bool>
     */
    private $conditions;

    /**
     * @psalm-param callable(Node): bool $expressions
     */
    public function __construct(...$expressions) {
        $this->conditions = $expressions;
    }

    /**
     * @param callable(Node): bool $expression
     */
    public function add($expression) {
        $this->conditions[] = $expression;
    }

    public function __invoke(Node $node) {
        foreach ($this->conditions as $condition) {
            if ($condition($node)) {
                return true;
            }
        }

        return false;
    }
}
