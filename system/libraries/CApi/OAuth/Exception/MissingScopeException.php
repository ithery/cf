<?php

class CApi_OAuth_Exception_MissingScopeException extends CAuth_Exception_AuthorizationException {
    /**
     * The scopes that the user did not have.
     *
     * @var array
     */
    protected $scopes;

    /**
     * Create a new missing scope exception.
     *
     * @param array|string $scopes
     * @param string       $message
     *
     * @return void
     */
    public function __construct($scopes = [], $message = 'Invalid scope(s) provided.') {
        parent::__construct($message);

        $this->scopes = carr::wrap($scopes);
    }

    /**
     * Get the scopes that the user did not have.
     *
     * @return array
     */
    public function scopes() {
        return $this->scopes;
    }
}
