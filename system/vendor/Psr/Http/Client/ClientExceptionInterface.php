<?php

namespace Psr\Http\Client;

if (\class_exists(\Throwable::class)) {
    /**
     * Every HTTP client related exception MUST implement this interface.
     */
    interface ClientExceptionInterface extends \Throwable {
    }
} else {
    interface ClientExceptionInterface {
    }
}
