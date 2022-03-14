<?php

abstract class CEmail_DriverAbstract implements CEmail_DriverInterface {
    /**
     * Email Config.
     *
     * @var CEmail_Config
     */
    protected $config;

    /**
     * @param CEmail_Config $config
     */
    public function __construct(CEmail_Config $config) {
        $this->config = $config;
    }

    /**
     * Get Email Config.
     *
     * @return CEmail_Config
     */
    public function getConfig() {
        return $this->config;
    }

    public static function formatAddresses(array $items) {
        $addresses = static::arrayAddresses($items);
        $return = [];
        foreach ($addresses as $recipient) {
            $email = $recipient;
            $name = '';
            if (is_array($email)) {
                $name = carr::get($recipient, 'name');
                if (strlen($name) > 0) {
                    $email = $name . '" <' . carr::get($recipient, 'email') . '>';
                }
            }
            $return[] = $email;
        }

        return implode(', ', $return);
    }

    protected static function arrayAddresses(array $items, $emailOnly = false) {
        $addresses = [];
        foreach ($items as $item) {
            $toName = '';
            $email = $item;
            if (is_array($item)) {
                if ($emailOnly) {
                    $email = carr::get($item, 'email', carr::get($item, 'toEmail'));
                } else {
                    $email = [
                        'email' => carr::get($item, 'email', carr::get($item, 'toEmail')),
                        'name' => carr::get($item, 'name', carr::get($item, 'toName')),

                    ];
                    $addresses[] = $email;
                }
            }
            if (is_string($email) && strlen($email) > 0) {
                $addresses[] = $email;
            }
        }

        return $addresses;
    }
}
