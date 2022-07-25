<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 7:58:56 PM
 */
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
