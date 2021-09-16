<?php

namespace Cresenity\Component;

class Counter extends \CComponent {
    public $count = 0;

    public function increment() {
        $this->count++;
    }

    public function render() {
        return \CView::factory('component.counter');
    }
}
