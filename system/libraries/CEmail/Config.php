<?php

class CEmail_Config {
    protected $driver;

    protected static $smtpHostToDriverMap = [
        'smtp.sendgrid.net' => 'sendgrid',
        'smtp.mailgun.org' => 'mailgun',
        'smtp.elasticemail.com' => 'elasticemail',
        'smtp25.elasticemail.com' => 'elasticemail',
        'smtp.postmarkapp.com' => 'postmarkapp',
    ];

    public function __construct($options = []) {
        $options = $this->reformatOptions($options);
        $this->driverName = carr::get($options, 'driver');
        $this->username = carr::get($options, 'username');
        $this->password = carr::get($options, 'password');
        $this->host = carr::get($options, 'host');
        $this->port = carr::get($options, 'port');

        $smtp_username = carr::get($options, 'smtp_username');
        $smtp_password = carr::get($options, 'smtp_password');
        $smtpHost = carr::get($options, 'smtp_host');
        $smtp_port = carr::get($options, 'smtp_port');
        $secure = carr::get($options, 'smtp_secure');

        if (!$smtp_username) {
            $smtp_username = ccfg::get('smtp_username');
        }
        if (!$smtp_password) {
            $smtp_password = ccfg::get('smtp_password');
        }
        if (!$smtpHost) {
            $smtp_host = ccfg::get('smtp_host');
        }
        if (!$smtp_port) {
            $smtp_port = ccfg::get('smtp_port');
        }
        if (!$secure) {
            $secure = ccfg::get('smtp_secure');
        }
        $this->driverName = carr::get(static::$smtpHostToDriverMap, $smtpHost, 'smtp');
    }

    public function reformatOptions($config) {
        $smtpHostLegacy = carr::get($config, 'smtp_host');
        $isLegacyOptions = c::filled($smtpHostLegacy);
        $newConfig = $config;
        if ($isLegacyOptions) {
            $smtpHost = carr::get($config, 'smtp_host');
            $driver = carr::get(static::$smtpHostToDriverMap, $smtpHost, 'smtp');
            $newConfig = [];
            $newConfig['driver'] = $driver;
            $newConfig['username'] = carr::get($config, 'smtp_username', carr::get($config, 'username'));
            $newConfig['password'] = carr::get($config, 'smtp_password', carr::get($config, 'password'));
            if ($driver == 'smtp') {
                $newConfig['host'] = carr::get($config, 'smtp_host', carr::get($config, 'host'));
                $newConfig['port'] = carr::get($config, 'smtp_port', carr::get($config, 'port'));
            }
        }
        return $newConfig;
    }
}
