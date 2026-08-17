<?php

class CEmail_Sender {
    /**
     * Email Driver.
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
     * Undocumented function.
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

        return $this->driver->send($to, $subject, $message, $options);
    }

    protected function rebuildOptions($options) {
        if (!isset($options['from'])) {
            //`app.email.from` dibaca lebih dulu, sejalan dengan baris
            //`from_name` di bawah. Sebelumnya hanya `app.smtp_from` yang
            //dibaca, sehingga aplikasi yang memakai bentuk `app.email.*`
            //diam-diam mengirim dengan alamat bawaan kerangka kerja - dan
            //penyedia email menolaknya karena pengirimnya tidak terautentikasi.
            $options['from'] = carr::get($options, 'smtp_from', CF::config('app.email.from', CF::config('app.smtp_from')));
        }
        if (!isset($options['domain'])) {
            $options['domain'] = carr::get($options, 'smtp_domain', CF::config('app.smtp_domain'));
        }

        if (!isset($options['from_name'])) {
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

    /**
     * @return CEmail_DriverAbstract
     */
    public function getDriver() {
        return $this->driver;
    }
}
