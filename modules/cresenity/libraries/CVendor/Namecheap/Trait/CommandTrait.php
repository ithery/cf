<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CVendor_Namecheap_Trait_CommandTrait {
    public function domain() {
        return new CVendor_Namecheap_Command_Domains($this);
        
    }
}