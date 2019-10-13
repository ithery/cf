<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CConsole_Command_StatusCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status';
    
    
    public function handle() {
       
        $fileData = DOCROOT.'data/current-domain';
        $domain=null;
        if(file_exists($fileData)) {
            $domain = file_get_contents($fileData);
        }
        if($domain==null) {
            $this->error('Domain not set, please set with php cf domain {domain}');
        }
        
        $this->line('Domain:'.$domain, 'yellow');
        
    }
}