<?php

interface CAuth_SupportBasicAuthInterface {
    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @param string $field
     * @param array  $extraConditions
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    public function basic($field = 'email', $extraConditions = []);

    /**
     * Perform a stateless HTTP Basic login attempt.
     *
     * @param string $field
     * @param array  $extraConditions
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    public function onceBasic($field = 'email', $extraConditions = []);
}
