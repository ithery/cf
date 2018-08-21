<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:58:56 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CGeo_Exception_FunctionNotFound extends \RuntimeException implements CGeo_Exception {

    /**
     * @param string $functionName
     * @param string $description
     */
    public function __construct($functionName, $description = null) {
        parent::__construct(sprintf('The function "%s" cannot be found. %s', $functionName, null !== $description ? sprintf(' %s', $description) : ''
        ));
    }

}
