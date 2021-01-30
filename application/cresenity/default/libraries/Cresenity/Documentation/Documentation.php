<?php

namespace Cresenity\Documentation;

class Documentation {
    protected $category;

    protected $page;

    public function __construct($category, $page) {
        $this->category = $category;
        $this->page = $page;
    }
}
