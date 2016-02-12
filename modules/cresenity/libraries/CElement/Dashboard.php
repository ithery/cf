<?php

class CElement_Dashboard extends CElement {
    
    protected $options;
    
    public function __construct($id, $options) {
        parent::__construct($id);
        $this->options = $options;
    }
    
    
    public function opt($key) {
        return carr::get($this->options,$key);
    }
    
    
    
   
    
}
