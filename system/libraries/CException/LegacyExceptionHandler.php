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
        return ob_get_clean();
    }

}
