<?php

defined('SYSPATH') or die('No direct access allowed.');

final class CGeo_Exception_FunctionNotFound extends \RuntimeException implements CGeo_Interface_ExceptionInterface {
    /**
     * @param string $functionName
     * @param string $description
     */
    public function __construct($functionName, $description = null) {
        parent::__construct(sprintf(
            'The function "%s" cannot be found. %s',
            $functionName,
            null !== $description ? sprintf(' %s', $description) : ''
        ));
    }
}
