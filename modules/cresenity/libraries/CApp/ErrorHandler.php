<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Aug 31, 2020 
 * @license Ittron Global Teknologi
 */
class CApp_ErrorHandler {

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
    
    public static function sendExceptionEmail(Exception $exception, $email = null, $subject = null) {
        $html = static::getHtml($exception);
        $app = CApp::instance();
        $org = $app->org();
        $orgName = 'CAPP';
        $orgEmail = $orgName;
        if ($org != null) {
            $orgEmail = $org->name;
            $orgName = $org->name;
        }

        $ymd = date('Ymd');
        if ($subject == null) {
            $subject = "Error Cresenity APP - " . $orgName . " on " . crouter::complete_uri() . ' [' . $ymd . ']';
        }
        $headers = "From: " . strip_tags($orgEmail) . "\r\n";
        $headers .= "Reply-To: " . strip_tags($orgEmail) . "\r\n";
        //$headers .= "CC: susan@example.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = $html;
        if ($email == null) {
            $email = ccfg::get("admin_email");
        }
        $smtpOptions = array();
        if (ccfg::get("mail_error_smtp")) {
            $smtpUsername = ccfg::get('smtp_username_error');
            $smtpPassword = ccfg::get('smtp_password_error');
            $smtpHost = ccfg::get('smtp_host_error');
            $smtpPort = ccfg::get('smtp_port_error');
            $secure = ccfg::get('smtp_secure_error');

            if (strlen($smtpUsername) > 0) {
                $smtpOptions['smtp_username'] = $smtpUsername;
            }
            if (strlen($smtpPassword) > 0) {
                $smtpOptions['smtp_password'] = $smtpPassword;
            }
            if (strlen($smtpHost) > 0) {
                $smtpOptions['smtp_host'] = $smtpHost;
            }
            if (strlen($smtpPort) > 0) {
                $smtpOptions['smtp_port'] = $smtpPort;
            }
            if (strlen($secure) > 0) {
                $smtpOptions['smtp_secure'] = $secure;
            }
        }

        $ret = cmail::send_smtp($email, $subject . " [FOR ADMINISTRATOR]", $message, array(), array(), array(), $smtpOptions);
    }

    public static function getHtml(Exception $exception) {
        $code = $exception->getCode();
        $type = get_class($exception);
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $app = CApp::instance();
        $org = $app->org();

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


        // Test if display_errors is on
        $trace = false;
        $traceArray = false;
        if ($line != FALSE) {
            // Remove the first entry of debug_backtrace(), it is the exception_handler call
            $traceArray = $exception->getTrace();

            // Beautify backtrace
            $trace = CF::backtrace($traceArray);
        }


        $v = CView::factory('cresenity/mail/exception');
        $v->error = $error;
        $v->description = $description;
        $v->file = $file;
        $v->line = $line;
        $v->trace = $trace;
        $v->message = $message;
        $v->exception = $exception;
        $html = $v->render();


        return $html;
    }

}
