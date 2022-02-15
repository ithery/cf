<?php
trait CSocialLogin_Trait_ConfigTrait {
    /**
     * @var array
     */
    protected $config;

    /**
     * @param \CSocialLogin_Contract_ConfigInterface|\CSocialLogin_OAuth1_ProviderInterface|\CSocialLogin_OAuth2_ProviderInterface $config
     */
    public function setConfig(CSocialLogin_Contract_ConfigInterface $config) {
        $config = $config->get();

        $this->config = $config;
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->redirectUrl = $config['redirect'];

        return $this;
    }

    /**
     * @return array
     */
    public static function additionalConfigKeys() {
        return [];
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|array
     */
    protected function getConfig($key = null, $default = null) {
        // check manually if a key is given and if it exists in the config
        // this has to be done to check for spoofed additional config keys so that null isn't returned
        if (!empty($key) && empty($this->config[$key])) {
            return $default;
        }

        return $key ? carr::get($this->config, $key, $default) : $this->config;
    }
}
