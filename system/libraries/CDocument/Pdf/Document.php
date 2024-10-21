<?php

class CDocument_Pdf_Document {
    /**
     * Margin in x direction starting from the left.
     */
    protected float $marginLeft = 0;

    /**
     * Margin in x direction starting from the right.
     */
    protected float $marginRight = 0;

    /**
     * Margin in y direction starting from the top.
     */
    protected float $marginTop = 0;

    /**
     * Margin in y direction starting from the bottom.
     */
    protected float $marginBottom = 0;

    public function __construct(
        CDocument_Pdf_Element_Rectangle $rectangle = null,
        float $marginLeft = 36,
        float $marginRight = 36,
        float $marginTop = 36,
        float $marginBottom = 36
    ) {
        if ($rectangle == null) {
            $rectangle = CDocument_Pdf_PageSize::a4();
        }
        $this->marginLeft = $marginLeft;
        $this->marginRight = $marginRight;
        $this->marginTop = $marginTop;
        $this->marginBottom = $marginBottom;
    }
}
