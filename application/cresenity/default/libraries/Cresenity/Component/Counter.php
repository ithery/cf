<?php

namespace Cresenity\Component;

class Counter extends \CComponent {

    public $counter = 0;
    public $count = 0;

    public function increment() {
        $this->count++;
    }

    public function render() {
        return \CView::factory('component.counter');
    }

}
