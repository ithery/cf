<?php

/**
 * Description of JsonMatchesErrorMessageProvider
 *
 * @author Hery
 */

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class CQC_UnitTest_Constraint_JsonMatchesErrorMessageProvider {

    /**
     * Translates JSON error to a human readable string.
     * @param string $error
     * @param string $prefix
     * @return string|null 
     */
    public static function determineJsonError($error, $prefix = '') {
        switch ($error) {
            case JSON_ERROR_NONE:
                return null;
            case JSON_ERROR_DEPTH:
                return $prefix . 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return $prefix . 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return $prefix . 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return $prefix . 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return $prefix . 'Malformed UTF-8 characters, possibly incorrectly encoded';

            default:
                return $prefix . 'Unknown error';
        }
    }

    /**
     * Translates a given type to a human readable message prefix.
     * @param string $type
     * @return string
     */
    public static function translateTypeToPrefix($type) {
        switch (strtolower($type)) {
            case 'expected':
                $prefix = 'Expected value JSON decode error - ';

                break;
            case 'actual':
                $prefix = 'Actual value JSON decode error - ';

                break;

            default:
                $prefix = '';

                break;
        }

        return $prefix;
    }

}
