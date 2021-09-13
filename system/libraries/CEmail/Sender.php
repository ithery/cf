<?php

class CEmail_Sender {
    /**
     * Email Config
     *
     * @var CEmail_Config
     */
    protected $config;

    public function __construct($config) {
        if (!($config instanceof CEmail_Config)) {
            $config = new CEmail_Config($config);
        }

        $this->config = $config;
    }

    public function send($to, $subject, $message, $options = []) {
        //do send email
    }
}
