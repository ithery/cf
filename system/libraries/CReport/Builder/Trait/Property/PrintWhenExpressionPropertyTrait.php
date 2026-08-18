<?php

trait CReport_Builder_Trait_Property_PrintWhenExpressionPropertyTrait {
    protected $printWhenExpression;

    public function getPrintWhenExpression() {
        return $this->printWhenExpression;
    }

    /**
     * @param string $printWhenExpression
     *
     * @return $this
     */
    public function setPrintWhenExpression(string $printWhenExpression) {
        $this->printWhenExpression = $printWhenExpression;

        return $this;
    }
}
