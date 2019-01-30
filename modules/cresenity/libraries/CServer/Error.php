<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 3:05:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Error {

    /**
     * holds the instance of this class
     *
     * @static
     * @var CServer_Error
     */
    private static $instance;

    /**
     * holds the error messages
     * @var array
     */
    private $errList = array();

    /**
     * current number ob errors
     * @var integer
     */
    private $errCount = 0;

    /**
     * initalize some used vars
     */
    private function __construct() {
        $this->errCount = 0;
        $this->errList = array();
    }

    /**
     * Singleton function
     * @return CServer_Error instance of the class
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CServer_Error();
        }

        return self::$instance;
    }

    /**
     * triggers an error when somebody tries to clone the object
     *
     * @return void
     */
    public function __clone() {
        throw new CException("Can't be cloned", array(), E_USER_ERROR);
    }

    /**
     * adds an CServer error to the internal list
     *
     * @param string $strCommand Command, which cause the Error
     * @param string $strMessage additional Message, to describe the Error
     *
     * @return void
     */
    public function addError($strCommand, $strMessage) {
        $this->addErrorTrace($strCommand, $this->trace($strMessage));
    }

    /**
     * adds an error to the internal list
     *
     * @param string $strCommand Command, which cause the Error
     * @param string $strMessage message, that describe the Error
     *
     * @return void
     */
    private function addErrorTrace($strCommand, $strMessage) {
        $index = count($this->errList) + 1;
        $this->errList[$index]['command'] = $strCommand;
        $this->errList[$index]['message'] = $strMessage;
        $this->errCount++;
    }

    /**
     * add a config error to the internal list
     *
     * @param string $strCommand Command, which cause the Error
     * @param string $strMessage additional Message, to describe the Error
     *
     * @return void
     */
    public function addConfigError($strCommand, $strMessage) {
        $this->addErrorTrace($strCommand, "Wrong Value in server.php for " . $strMessage);
    }

    /**
     * add a php error to the internal list
     *
     * @param string $strCommand Command, which cause the Error
     * @param string $strMessage additional Message, to describe the Error
     *
     * @return void
     */
    public function addPhpError($strCommand, $strMessage) {
        $this->addErrorTrace($strCommand, "PHP throws a error\n" . $strMessage);
    }

    /**
     * adds a waraning to the internal list
     *
     * @param string $strMessage Warning message to display
     *
     * @return void
     */
    public function addWarning($strMessage) {
        $index = count($this->errList) + 1;
        $this->errList[$index]['command'] = "WARN";
        $this->errList[$index]['message'] = $strMessage;
    }

    /**
     * check if errors exists
     *
     * @return boolean true if are errors logged, false if not
     */
    public function haveError() {
        return $this->errCount > 0;
    }

    /**
     * generate a function backtrace for error diagnostic, function is genearally based on code submitted in the php reference page
     * @param string $strMessage additional message to display
     * @return string formatted string of the backtrace
     */
    private function trace($strMessage) {
        $arrTrace = array_reverse(debug_backtrace());
        $strFunc = '';
        $strBacktrace = htmlspecialchars($strMessage) . "\n\n";
       
             
        foreach ($arrTrace as $val) {
            // avoid the last line, which says the error is from the error class
            if ($val == $arrTrace[count($arrTrace) - 1]) {
                break;
            }
            if(!isset($val['file'])) {
                continue;
            }
            $strBacktrace .= str_replace(DOCROOT, ".", carr::get($val, 'file')) . ' on line ' . $val['line'];
            if ($strFunc) {
                $strBacktrace .= ' in function ' . $strFunc;
            }
            if ($val['function'] == 'include' || $val['function'] == 'require' || $val['function'] == 'include_once' || $val['function'] == 'require_once') {
                $strFunc = '';
            } else {
                $strFunc = $val['function'] . '(';
                if (isset($val['args'][0])) {
                    $strFunc .= ' ';
                    $strComma = '';
                    foreach ($val['args'] as $valArgs) {
                        $strFunc .= $strComma . $this->printVar($valArgs);
                        $strComma = ', ';
                    }
                    $strFunc .= ' ';
                }
                $strFunc .= ')';
            }
            $strBacktrace .= "\n";
        }

        return $strBacktrace;
    }

    /**
     * convert some special vars into better readable output
     * @param mixed $var value, which should be formatted
     * @return string formatted string
     */
    private function printVar($var) {
        if (is_string($var)) {
            $search = array("\x00", "\x0a", "\x0d", "\x1a", "\x09");
            $replace = array('\0', '\n', '\r', '\Z', '\t');

            return ('"' . str_replace($search, $replace, $var) . '"');
        } elseif (is_bool($var)) {
            if ($var) {
                return ('true');
            } else {
                return ('false');
            }
        } elseif (is_array($var)) {
            $strResult = 'array( ';
            $strComma = '';
            foreach ($var as $key => $val) {
                $strResult .= $strComma . $this->_printVar($key) . ' => ' . $this->_printVar($val);
                $strComma = ', ';
            }
            $strResult .= ' )';

            return ($strResult);
        }
        // anything else, just let php try to print it
        return (var_export($var, true));
    }

}
