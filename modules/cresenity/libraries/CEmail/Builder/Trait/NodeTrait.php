<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CEmail_Builder_Trait_NodeTrait {

    protected function addNode($tagName) {
        $node = new CEmail_Builder_Node(['tagName' => $tagName]);
        $this->children[] = $node;
        return $node;
    }

    public function addBody() {
        return $this->addNode('c-body');
    }

    public function addSection() {
        return $this->addNode('c-section');
    }

    public function addColumn() {
        return $this->addNode('c-column');
    }

    public function addImage() {
        return $this->addNode('c-image');
    }
    
    public function addText() {
        return $this->addNode('c-text');
    }

    public function addDivider() {
        return $this->addNode('c-divider');
    }

}
