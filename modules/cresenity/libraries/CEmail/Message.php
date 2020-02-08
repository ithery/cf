<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Message {

    protected $to;
    protected $subject;
    protected $message;
    protected $attachments;
    protected $cc;
    protected $bcc;

    
    public function __construct($options = []) {
        $this->to = carr::get($options,'to');
        $this->subject = carr::get($options,'subject');
        $this->message = carr::get($options,'message');
        
    }
}
