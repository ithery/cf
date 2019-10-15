<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CConsole_Command_DomainCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain {name}';
    
    
    public function handle() {
        $domain = $this->argument('name');
        $this->info('Switching to '.$domain);
        
        $fileData = DOCROOT.'data/current-domain';
        cfs::atomic_write($fileData, $domain);
        $this->info('Switched to '.$domain);
        
        
    }
}