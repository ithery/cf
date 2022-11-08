<?php
/**
 * Formatting old app config to new config like vendor config.
 * old app config will used as fallback when vendor config is not found.
 */
class CEmail_Config_LegacyAppConfig {
    protected static $smtpHostToDriverMap = [
        'smtp.sendgrid.net' => 'sendgrid',
        'smtp.mailgun.org' => 'mailgun',
        'smtp.elasticemail.com' => 'elasticemail',
        'smtp25.elasticemail.com' => 'elasticemail',
        'smtp.postmarkapp.com' => 'postmarkapp',
    ];

    public static function getConfig() {
        $smtpHost = CF::config('app.smtp_host');
        $driver = carr::get(static::$smtpHostToDriverMap, $smtpHost, 'smtp');
        $newConfig = [];
        $newConfig['driver'] = $driver;
        $newConfig['username'] = CF::config('app.smtp_username');
        $newConfig['password'] = CF::config('app.smtp_password');
        $newConfig['from'] = CF::config('app.smtp_from');
        $newConfig['from_name'] = CF::config('app.smtp_from_name');
        $newConfig['secure'] = CF::config('app.smtp_secure');
        if ($driver == 'smtp') {
            $newConfig['host'] = CF::config('app.smtp_host');
            $newConfig['port'] = CF::config('app.smtp_port');
        }

        return $newConfig;
    }
}
