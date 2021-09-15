<?php

class CEmail_Sender {
    /**
     * Email Driver
     *
     * @var CEmail_DriverAbstract
     */
    protected $driver;

    public function __construct($config) {
        if (!($config instanceof CEmail_Config)) {
            $config = new CEmail_Config($config);
        }

        $this->driver = CEmail_Factory::createDriver($config);
    }

    /**
     * Undocumented function
     *
     * @param array|string $to
     * @param string       $subject
     * @param string       $message
     * @param array        $options
     *
     * @return void|CVendor_SendGrid_Response
     */
    public function send($to, $subject, $message, $options = []) {
        //build the default options
        $to = carr::wrap($to);

        $options = $this->rebuildOptions($options);
        $this->driver->send($to, $$subject, $message, $options);
    }

    protected function rebuildOptions($options) {
        if (!isset($options['from'])) {
            $options['from'] = carr::get($options, 'smtp_from', CF::config('app.smtp_from'));
        }

        if (!isset($options['from'])) {
            $options['from_name'] = carr::get($options, 'smtp_from_name', CF::config('app.email.from_name', CF::config('app.smtp_from_name')));
        }

        if (!isset($options['attachments'])) {
            $options['attachments'] = [];
        }
        if (!is_array($options['attachments'])) {
            $options['attachments'] = carr::wrap($options['attachments']);
        }

        if (!isset($options['cc'])) {
            $options['cc'] = [];
        }
        if (!is_array($options['cc'])) {
            $options['cc'] = carr::wrap($options['cc']);
        }

        if (!isset($options['bcc'])) {
            $options['bcc'] = [];
        }
        if (!is_array($options['bcc'])) {
            $options['bcc'] = carr::wrap($options['bcc']);
        }

        return $options;
    }
}
