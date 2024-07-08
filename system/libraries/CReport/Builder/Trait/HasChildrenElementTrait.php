<?php

trait CReport_Builder_Trait_HasChildrenElementTrait {
    /**
     * @return CReport_Builder_Element_Title
     */
    public function addTitle() {
        $title = new CReport_Builder_Element_Title();
        $this->children[] = $title;

        return $title;
    }

    /**
     * @return CReport_Builder_Element_Band
     */
    public function addBand() {
        $band = new CReport_Builder_Element_Band();
        $this->children[] = $band;

        return $band;
    }

    /**
     * @return CReport_Builder_Element_Image
     */
    public function addImage() {
        $band = new CReport_Builder_Element_Image();
        $this->children[] = $band;

        return $band;
    }

    /**
     * @return CReport_Builder_Element_PageHeader
     */
    public function addPageHeader() {
        $pageHeader = new CReport_Builder_Element_PageHeader();
        $this->children[] = $pageHeader;

        return $pageHeader;
    }

    /**
     * @return CReport_Builder_Element_Frame
     */
    public function addFrame() {
        $frame = new CReport_Builder_Element_Frame();
        $this->children[] = $frame;

        return $frame;
    }

    /**
     * @return CReport_Builder_Element_StaticText
     */
    public function addStaticText() {
        $frame = new CReport_Builder_Element_StaticText();
        $this->children[] = $frame;

        return $frame;
    }

    /**
     * @return CReport_Builder_Element_ColumnHeader
     */
    public function addColumnHeader() {
        $frame = new CReport_Builder_Element_ColumnHeader();
        $this->children[] = $frame;

        return $frame;
    }

    /**
     * @return CReport_Builder_Element_Detail
     */
    public function addDetail() {
        $frame = new CReport_Builder_Element_Detail();
        $this->children[] = $frame;

        return $frame;
    }

    /**
     * @return CReport_Builder_Element_TextField
     */
    public function addTextField() {
        $frame = new CReport_Builder_Element_TextField();
        $this->children[] = $frame;

        return $frame;
    }
}
