<?php

/**
 * Description of InvalidArgumentException
 *
 * @author Hery
 */

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class CQC_Exception_InvalidArgumentException extends Exception {

    /**
     * 
     * @param int $argument
     * @param string $type
     * @return \InvalidArgumentException
     */
    public static function create($argument, $type) {
        $stack = debug_backtrace();

        return new self(
                sprintf(
                        'Argument #%d of %s::%s() must be %s %s', $argument, $stack[1]['class'], $stack[1]['function'], in_array(lcfirst($type)[0], ['a', 'e', 'i', 'o', 'u'], true) ? 'an' : 'a', $type
                )
        );
    }

    /**
     * 
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    private function __construct($message = '', $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
