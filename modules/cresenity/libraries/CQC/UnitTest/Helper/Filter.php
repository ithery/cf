<?php

/**
 * Description of Filter
 *
 * @author Hery
 */

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class CQC_Helper_Filter {

    /**
     * @throws Exception
     */
    public static function getFilteredStacktrace(Throwable $t) {
        $filteredStacktrace = '';

        if ($t instanceof SyntheticError) {
            $eTrace = $t->getSyntheticTrace();
            $eFile = $t->getSyntheticFile();
            $eLine = $t->getSyntheticLine();
        } elseif ($t instanceof Exception) {
            $eTrace = $t->getSerializableTrace();
            $eFile = $t->getFile();
            $eLine = $t->getLine();
        } else {
            if ($t->getPrevious()) {
                $t = $t->getPrevious();
            }

            $eTrace = $t->getTrace();
            $eFile = $t->getFile();
            $eLine = $t->getLine();
        }

        if (!self::frameExists($eTrace, $eFile, $eLine)) {
            array_unshift(
                    $eTrace, ['file' => $eFile, 'line' => $eLine]
            );
        }

        $prefix = defined('__PHPUNIT_PHAR_ROOT__') ? __PHPUNIT_PHAR_ROOT__ : null;
        $excludeList = new ExcludeList;

        foreach ($eTrace as $frame) {
            if (self::shouldPrintFrame($frame, $prefix, $excludeList)) {
                $filteredStacktrace .= sprintf(
                        "%s:%s\n", $frame['file'], isset($frame['line']) ? $frame['line'] : '?'
                );
            }
        }

        return $filteredStacktrace;
    }

    private static function shouldPrintFrame($frame, $prefix, ExcludeList $excludeList) {
        if (!isset($frame['file'])) {
            return false;
        }

// @see https://github.com/sebastianbergmann/phpunit/issues/4033
        $script = '';

        if (isset($GLOBALS['_SERVER']['SCRIPT_NAME'])) {
            $script = realpath($GLOBALS['_SERVER']['SCRIPT_NAME']);
        }

        $file = $frame['file'];

        if ($file === $script) {
            return false;
        }

        return $prefix === null &&
                self::fileIsExcluded($file, $excludeList) &&
                is_file($file);
    }

    private static function fileIsExcluded($file, ExcludeList $excludeList) {
        return (empty($GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST']) ||
                !in_array($file, $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'], true)) &&
                !$excludeList->isExcluded($file);
    }

    private static function frameExists(array $trace, $file, $line) {
        foreach ($trace as $frame) {
            if (isset($frame['file'], $frame['line']) && $frame['file'] === $file && $frame['line'] === $line) {
                return true;
            }
        }

        return false;
    }

}
