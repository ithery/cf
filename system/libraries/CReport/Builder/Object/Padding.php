<?php

class CReport_Builder_Object_Padding implements CReport_Builder_Contract_JrXmlElementInterface {
    /**
     * @var float
     */
    protected $left;

    /**
     * @var float
     */
    protected $right;

    /**
     * @var float
     */
    protected $top;

    /**
     * @var float
     */
    protected $bottom;

    public function __construct($top = 0, $right = 1, $bottom = 0, $left = 1) {
        $this->top = $top;
        $this->right = $right;
        $this->bottom = $bottom;
        $this->left = $left;
    }

    /**
     * @param float $top
     *
     * @return $this
     */
    public function setPaddingTop($top) {
        $this->top = $top;

        return $this;
    }

    /**
     * @param float $right
     *
     * @return $this
     */
    public function setPaddingRight($right) {
        $this->right = $right;

        return $this;
    }

    /**
     * @param float $bottom
     *
     * @return $this
     */
    public function setPaddingBottom($bottom) {
        $this->bottom = $bottom;

        return $this;
    }

    /**
     * @param float $left
     *
     * @return $this
     */
    public function setPaddingLeft($left) {
        $this->left = $left;

        return $this;
    }

    /**
     * @return float
     */
    public function getPaddingTop() {
        return $this->top;
    }

    /**
     * @return float
     */
    public function getPaddingRight() {
        return $this->right;
    }

    /**
     * @return float
     */
    public function getPaddingBottom() {
        return $this->bottom;
    }

    /**
     * @return float
     */
    public function getPaddingLeft() {
        return $this->left;
    }

    public function toJrXml() {
        $tag = '<padding ';
        $tag .= ' top="' . $this->top . '"';
        $tag .= ' right="' . $this->right . '"';
        $tag .= ' bottom="' . $this->bottom . '"';
        $tag .= ' left="' . $this->left . '"';
        $tag .= '/>';

        return $tag;
    }
}
