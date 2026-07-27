<?php

namespace Cresenity\Demo\Api\Widget;

/**
 * Shared base for every method in the Widget example group - registers
 * common middleware once instead of repeating it on every method class.
 * See /docs/api/introduction.
 */
abstract class MethodAbstract extends \CApi_OAuth_MethodAbstract {
    public function __construct() {
        parent::__construct();
        $this->middleware(Middleware\UserCredentialsMiddleware::class);
    }
}
