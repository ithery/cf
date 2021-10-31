<?php

class CEmail_Message {
    protected $to;
    protected $subject;
    protected $message;
    protected $attachments;
    protected $cc;
    protected $bcc;

    public function __construct($options = []) {
        $this->to = carr::get($options, 'to');
        $this->subject = carr::get($options, 'subject');
        $this->message = carr::get($options, 'message');
    }
}
