<?php

interface CAuth_Contract_PasswordBrokerFactoryInterface {
    /**
     * Get a password broker instance by name.
     *
     * @param null|string $name
     *
     * @return mixed
     */
    public function broker($name = null);
}
