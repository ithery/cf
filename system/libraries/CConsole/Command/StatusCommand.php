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
       
        $domain = CConsole::domain();
        if($domain==null) {
            $this->error('Domain not set, please set with php cf domain {domain}');
        }
        
        $this->info('Domain: '.$domain);
        $this->output->newLine();
        
        
        $this->info('AppID: '.CF::appId());
        $this->info('AppCode: '.CF::appCode());
        $this->info('OrgID: '.CF::orgId());
        $this->info('OrgCode: '.CF::orgCode());
        $this->output->newLine();
        
        $db = CDatabase::instance();
        $config = $db->config();
       
        $configConnection = carr::get($config,'connection');
       
        $rows=array();
        $rows[] = array('Type',carr::get($configConnection,'type'));
        $rows[] = array('Host',carr::get($configConnection,'host'));
        $rows[] = array('Port',carr::get($configConnection,'port'));
        $rows[] = array('Username',carr::get($configConnection,'user'));
        $rows[] = array('Password',carr::get($configConnection,'pass'));
        $rows[] = array('Database',carr::get($configConnection,'database'));
        
        $this->info('Database Configuration');
        
        $this->info('======================');
        
        $this->table(array('Description','Value'),$rows);
        
    }
}