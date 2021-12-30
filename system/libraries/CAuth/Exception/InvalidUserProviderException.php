<?php

class CAuth_Exception_InvalidUserProviderException extends \Exception {
    public function __construct($guard, $message = '', $code = 0, $previous = null) {
        parent::__construct(sprintf('Invalid user provider for guard %s', $guard), $code, $previous);
    }
}
