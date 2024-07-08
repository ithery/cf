<?php

trait CReport_Builder_Trait_HasChildrenElementTrait {
    public function addTitle() {
        $title = new CReport_Builder_Element_Title();
        $this->children[] = $title;

        return $title;
    }
}
