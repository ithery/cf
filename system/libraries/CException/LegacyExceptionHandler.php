<?php

/**
 * Description of LegacyExceptionHandler
 *
 * @author Hery
 */
class CException_LegacyExceptionHandler {

    public static function getContent($exception) {

        $code = $exception->getCode();
        $type = get_class($exception);
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $uri = CFRouter::$current_uri;
        $template = 'kohana_error_page';



        if (is_numeric($code)) {
            $codes = CF::lang('errors');

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
