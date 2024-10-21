<?php

namespace Illuminate\Contracts\Database\Query;

interface Expression {
    /**
     * Get the value of the expression.
     *
     * @param \Illuminate\Database\Grammar $grammar
     *
     * @return string|int|float
     */
    public function getValue(Grammar $grammar);
}
