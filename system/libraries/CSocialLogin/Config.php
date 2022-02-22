<?php

class CSocialLogin_Config implements CSocialLogin_Contract_ConfigInterface {
    /**
     * @var array
     */
    protected $config;

    /**
     * Config constructor.
     *
     * @param string          $key
     * @param string          $secret
     * @param string|callable $callbackUri
     * @param array           $additionalProviderConfig
     */
    public function __construct($key, $secret, $callbackUri, array $additionalProviderConfig = []) {
        $this->config = array_merge([
            'client_id' => $key,
            'client_secret' => $secret,
            'redirect' => $this->formatRedirectUri($callbackUri),
        ], $additionalProviderConfig);
    }

    /**
     * Format the callback URI, resolving a relative URI if needed.
     *
     * @param string $callbackUri
     *
     * @return string
     */
    protected function formatRedirectUri($callbackUri) {
        $redirect = c::value($callbackUri);

        return cstr::startsWith($redirect, '/')
                    ? c::url()->to($redirect)
                    : $redirect;
    }

    /**
     * @return array
     */
    public function get() {
        return $this->config;
    }
}
