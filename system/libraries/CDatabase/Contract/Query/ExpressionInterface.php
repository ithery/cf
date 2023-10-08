<?php

interface CDatabase_Contract_Query_ExpressionInterface {
    /**
     * Get the value of the expression.
     *
     * @param \CDatabase_Query_Grammar $grammar
     *
     * @return string|int|float
     */
    public function getValue(CDatabase_Query_Grammar $grammar);
}
