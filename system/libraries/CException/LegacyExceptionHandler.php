<?php

/**
 * Description of LegacyExceptionHandler
 *
 * @author Hery
 */
class CException_LegacyExceptionHandler {

    public static $errorLang = array(
        E_CF => array(1, 'Framework Error', 'Please check the CF documentation for information about the following error.'),
        E_PAGE_NOT_FOUND => array(1, 'Page Not Found', 'The requested page was not found. It may have moved, been deleted, or archived.'),
        E_DATABASE_ERROR => array(1, 'Database Error', 'A database error occurred while performing the requested procedure. Please review the database error below for more information.'),
        E_RECOVERABLE_ERROR => array(1, 'Recoverable Error', 'An error was detected which prevented the loading of this page. If this problem persists, please contact the website administrator.'),
        E_ERROR => array(1, 'Fatal Error', ''),
        E_USER_ERROR => array(1, 'Fatal Error', ''),
        E_PARSE => array(1, 'Syntax Error', ''),
        E_WARNING => array(1, 'Warning Message', ''),
        E_USER_WARNING => array(1, 'Warning Message', ''),
        E_STRICT => array(2, 'Strict Mode Error', ''),
        E_NOTICE => array(2, 'Runtime Message', ''),
    );

    public static function getContent($exception) {

        $code = $exception->getCode();
        $type = get_class($exception);
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $uri = CFRouter::$current_uri;
        $template = 'errors/exception';



        if (is_numeric($code)) {
            $codes = static::$errorLang;

            if (!empty($codes[$code])) {
                list($level, $error, $description) = $codes[$code];
            } else {
                $level = 1;
                $error = get_class($exception);
                $description = '';
            }
        } else {
// Custom error message, this will never be logged
            $level = 5;
            $error = $code;
            $description = '';
        }

// Remove the DOCROOT from the path, as a security precaution
        $file = str_replace('\\', '/', realpath($file));
        $file = preg_replace('|^' . preg_quote(DOCROOT) . '|', '', $file);

        if ($level <= CF::$log_threshold) {
// Log the error
            $need_to_log = true;

//CF::log(LOG_ERR, self::lang('core.uncaught_exception', $type, $message, $file, $line . " on uri:" . $uri . " with trace:\n" . $trace));
        }




        ob_start();
        require CF::findFile('views', empty($template) ? 'kohana_error_page' : $template);
        $output = ob_get_clean();


// Fetch memory usage in MB
        $memory = function_exists('memory_get_usage') ? (memory_get_usage() / 1024 / 1024) : 0;

// Fetch benchmark for page execution time
        $benchmark = CFBenchmark::get(SYSTEM_BENCHMARK . '_total_execution');
        $output = str_replace(
                array
                    (
                    '{cf_version}',
                    '{cf_codename}',
                    '{execution_time}',
                    '{memory_usage}',
                    '{included_files}',
                ), array
            (
            CF::version(),
            CF::codeName(),
            $benchmark['time'],
            number_format($memory, 2) . 'MB',
            count(get_included_files()),
                ), $output
        );

        return $output;
    }

}
