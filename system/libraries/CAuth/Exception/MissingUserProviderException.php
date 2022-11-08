<?php

class CAuth_Exception_MissingUserProviderException extends \Exception {
    public function __construct($guard, $code = 0, $previous = null) {
        parent::__construct(sprintf('Missing user provider for guard %s', $guard), $code, $previous);
    }
}
