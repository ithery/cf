<?php

interface CDatabase_Contract_Query_ExpressionInterface {
    /**
     * Get the value of the expression.
     *
     * @param \CDatabase_Grammar $grammar
     *
     * @return string|int|float
     */
    public function getValue(CDatabase_Grammar $grammar);
}
