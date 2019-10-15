<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Option {
    
    protected $title;
    
    
    
    public function setTitle($title) {
        $this->title=$title;
        return $this;
    }
    
    public function getTitle() {
        return $this->title;
        
    }
    
    
}