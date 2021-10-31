<?php

final class CComparator_Differ_Exception_ConfigurationException extends InvalidArgumentException {
    public function __construct(
        string $option,
        string $expected,
        $value,
        int $code = 0,
        Exception $previous = null
    ) {
        parent::__construct(
            sprintf(
                'Option "%s" must be %s, got "%s".',
                $option,
                $expected,
                is_object($value) ? get_class($value) : (null === $value ? '<null>' : gettype($value) . '#' . $value)
            ),
            $code,
            $previous
        );
    }
}
