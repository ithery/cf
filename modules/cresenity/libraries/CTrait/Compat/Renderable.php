<?php
trait CTrait_Compat_Renderable {
    public function child_count() {
        return $this->childCount();
    }

    public function set_parent($parent) {
        return $this->setParent($parent);
    }
    
    public function set_visibility($bool) {
        return $this->setVisibility($bool);
    }
    
    public function add_js($js) {
        return $this->addJs($js);
    }

    public function regenerate_id($recursive = false) {    
        return $this->regenerateId($recursive);
    }
    
    public function toarray() {
        return $this->toArray();
    }
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

