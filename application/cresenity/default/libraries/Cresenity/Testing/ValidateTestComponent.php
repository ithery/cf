<?php

namespace Cresenity\Testing;

class ValidateTestComponent extends \CComponent {

    public $name;

    public function mount() {
        
    }
    
    public function save() {
        $this->validate([
            'name'=>'required',
        ]);
    }

    public function render() {
        return \CView::factory('component.test.validate');
    }

}
