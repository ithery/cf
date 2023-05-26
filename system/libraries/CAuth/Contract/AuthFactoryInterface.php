<?php

interface CAuth_Contract_AuthFactoryInterface {
    /**
     * Get a guard instance by name.
     *
     * @param null|string $name
     *
     * @return \CAuth_Contract_GuardInterface|\CAuth_Contract_StatefulGuardInterface
     */
    public function guard($name = null);

    /**
     * Set the default guard the factory should serve.
     *
     * @param string $name
     *
     * @return void
     */
    public function shouldUse($name);
}
