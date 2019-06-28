<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CVendor_Namecheap_Trait_CommandTrait {

    /**
     * 
     * @return \CVendor_Namecheap_Command_Domains
     */
    public function domains() {
        return new CVendor_Namecheap_Command_Domains($this);
    }
    
    /**
     * 
     * @return \CVendor_Namecheap_Command_Domains_Ns
     */
    public function domainsNs() {
        return new CVendor_Namecheap_Command_Domains_Ns($this);
    }
    
    /**
     * 
     * @return \CVendor_Namecheap_Command_Domains_Dns
     */
    public function domainsDns() {
        return new CVendor_Namecheap_Command_Domains_Dns($this);
    }
    
    /**
     * 
     * @return \CVendor_Namecheap_Command_Domains_Transfer
     */
    public function domainsTransfer() {
        return new CVendor_Namecheap_Command_Domains_Transfer($this);
    }

    /**
     * 
     * @return \CVendor_Namecheap_Command_Users
     */
    public function users() {
        return new CVendor_Namecheap_Command_Users($this);
    }

    /**
     * 
     * @return \CVendor_Namecheap_Command_Users_Address
     */
    public function usersAddress() {
        return new CVendor_Namecheap_Command_Users_Address($this);
    }

    /**
     * 
     * @return \CVendor_Namecheap_Command_Ssl
     */
    public function ssl() {
        return new CVendor_Namecheap_Command_Ssl($this);
    }
    
    /**
     * 
     * @return \CVendor_Namecheap_Command_WhoIsGuard
     */
    public function whoIsGuard() {
        return new CVendor_Namecheap_Command_WhoIsGuard($this);
    }
    
}
