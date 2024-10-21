<?php

trait CReport_Builder_Trait_Property_PaddingPropertyTrait {
    /**
     * @var CReport_Builder_Object_Padding
     */
    protected $padding;

    /**
     * @return CReport_Builder_Object_Padding
     */
    public function getPadding() {
        return $this->padding;
    }

    /**
     * @param CReport_Builder_Object_Padding $padding
     *
     * @return $this
     */
    public function setPadding(CReport_Builder_Object_Padding $padding) {
        $this->padding = $padding;

        return $this;
    }

    /**
     * @param float $top
     *
     * @return $this
     */
    public function setPaddingTop($top) {
        $this->padding->setPaddingTop($top);

        return $this;
    }

    /**
     * @param float $right
     *
     * @return $this
     */
    public function setPaddingRight($right) {
        $this->padding->setPaddingRight($right);

        return $this;
    }

    /**
     * @param float $bottom
     *
     * @return $this
     */
    public function setPaddingBottom($bottom) {
        $this->padding->setPaddingBottom($bottom);

        return $this;
    }

    /**
     * @param float $left
     *
     * @return $this
     */
    public function setPaddingLeft($left) {
        $this->padding->setPaddingLeft($left);

        return $this;
    }

    /**
     * @return float
     */
    public function getPaddingTop() {
        return $this->padding->getPaddingTop();
    }

    /**
     * @return float
     */
    public function getPaddingRight() {
        return $this->padding->getPaddingRight();
    }

    /**
     * @return float
     */
    public function getPaddingBottom() {
        return $this->padding->getPaddingBottom();
    }

    /**
     * @return float
     */
    public function getPaddingLeft() {
        return $this->padding->getPaddingLeft();
    }
}
