<?php

interface CSocialLogin_Contract_ConfigRetrieverInterface {
    /**
     * @param string $providerName
     * @param array  $additionalConfigKeys
     *
     * @return CSocialLogin_Contract_ConfigInterface
     */
    public function fromServices($providerName, array $additionalConfigKeys = []);
}
