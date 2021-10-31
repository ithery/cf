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

    protected function formatAddresses(array $items) {
        $addresses = $this->arrayAddresses($items);

        return implode(', ' . $addresses);
    }

    protected function arrayAddresses(array $items) {
        $addresses = [];
        foreach ($items as $item) {
            $toName = '';
            $email = $item;
            if (is_array($item)) {
                $email = carr::get($item, 'email', carr::get($item, 'toEmail'));
            }
            if (is_string($email) && strlen($email) > 0) {
                $addresses[] = $email;
            }
        }

        return $addresses;
    }
}
