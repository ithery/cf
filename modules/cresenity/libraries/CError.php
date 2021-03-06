<?php
/**
 * Error class
 *
 * PHP version 5
 *
 * @deprecated 1.2
 */
class CError {
    /**
     * Holds the instance of this class
     *
     * @static
     *
     * @var object
     */
    private static $_instance;

    /**
     * Holds the error messages
     *
     * @var array
     */
    private $_arrErrorList = [];

    /**
     * Current number ob errors
     *
     * @var int
     */
    private $_errors = 0;

    /**
     * Initalize some used vars
     */
    private function __construct() {
        $this->_errors = 0;
        $this->_arrErrorList = [];
    }

    /**
     * Singleton function
     *
     * @return Error instance of the class
     */
    public static function factory() {
        if (!isset(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        return self::$_instance;
    }

    /**
     * Triggers an error when somebody tries to clone the object
     *
     * @return void
     */
    public function __clone() {
        trigger_error("Can't be cloned", E_USER_ERROR);
    }

    /**
     * Adds an phpsysinfo error to the internal list
     *
     * @param string $strCommand Command, which cause the Error
     * @param string $strMessage additional Message, to describe the Error
     *
     * @return void
     */
    public function add_error($strCommand, $strMessage) {
        $this->_add_error($strCommand, $this->_trace($strMessage));
    }

    /**
     * adds an error to the internal list
     *
     * @param string $strCommand Command, which cause the Error
     * @param string $strMessage message, that describe the Error
     *
     * @return void
     */
    private function _add_error($strCommand, $strMessage) {
        $index = count($this->_arrErrorList) + 1;
        $this->_arrErrorList[$index]['command'] = $strCommand;
        $this->_arrErrorList[$index]['message'] = $strMessage;
        $this->_errors++;
    }

    /**
     * add a config error to the internal list
     *
     * @param object $strCommand Command, which cause the Error
     * @param object $strMessage additional Message, to describe the Error
     *
     * @return void
     */
    public function add_config_error($strCommand, $strMessage) {
        $this->_addError($strCommand, 'Wrong Value in config.php for ' . $strMessage);
    }

    /**
     * add a php error to the internal list
     *
     * @param object $strCommand Command, which cause the Error
     * @param object $strMessage additional Message, to describe the Error
     *
     * @return void
     */
    public function add_php_error($strCommand, $strMessage) {
        $this->_addError($strCommand, "PHP throws a error\n" . $strMessage);
    }

    /**
     * adds a waraning to the internal list
     *
     * @param string $strMessage Warning message to display
     *
     * @return void
     */
    public function add_warning($strMessage) {
        $index = count($this->_arrErrorList) + 1;
        $this->_arrErrorList[$index]['command'] = 'WARN';
        $this->_arrErrorList[$index]['message'] = $strMessage;
    }

    /**
     * converts the internal error and warning list to a XML file
     *
     * @return void
     */
    public function tostring() {
        $message = '';
        foreach ($this->_arrErrorList as $arrLine) {
            $message .= '[' . $arrLine['command'] . '] ' . $arrLine['message'] . "\n";
        }
        return $message;
    }

    /**
     * converts the internal error and warning list to a XML file
     *
     * @return void
     */
    public function toxml() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement('phpsysinfo');
        $dom->appendChild($root);
        $xml = new SimpleXMLExtended(simplexml_import_dom($dom), 'UTF-8');
        $generation = $xml->addChild('Generation');
        $generation->addAttribute('version', CommonFunctions::$PSI_VERSION_STRING);
        $generation->addAttribute('timestamp', time());
        $xmlerr = $xml->addChild('Errors');
        foreach ($this->_arrErrorList as $arrLine) {
            $error = $xmlerr->addCData('Error', $arrLine['message']);
            $error->addAttribute('Function', $arrLine['command']);
        }
        header("Cache-Control: no-cache, must-revalidate\n");
        header("Content-Type: text/xml\n\n");
        echo $xml->getSimpleXmlElement()->asXML();
        exit();
    }

    /**
     * add the errors to an existing xml document
     *
     * @param string $encoding encoding
     *
     * @return SimpleXmlElement
     */
    public function errorsAddToXML($encoding) {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement('Errors');
        $dom->appendChild($root);
        $xml = simplexml_import_dom($dom);
        $xmlerr = new SimpleXMLExtended($xml, $encoding);
        foreach ($this->_arrErrorList as $arrLine) {
            $error = $xmlerr->addCData('Error', $arrLine['message']);
            $error->addAttribute('Function', $arrLine['command']);
        }
        return $xmlerr->getSimpleXmlElement();
    }

    /**
     * check if errors exists
     *
     * @return bool true if are errors logged, false if not
     */
    public function errorsExist() {
        if ($this->_errors > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * generate a function backtrace for error diagnostic, function is genearally based on code submitted in the php reference page
     *
     * @param string $strMessage additional message to display
     *
     * @return string formatted string of the backtrace
     */
    private function _trace($strMessage) {
        $arrTrace = array_reverse(debug_backtrace());
        $strFunc = '';
        $strBacktrace = htmlspecialchars($strMessage) . "\n\n";
        foreach ($arrTrace as $val) {
            // avoid the last line, which says the error is from the error class
            if ($val == $arrTrace[count($arrTrace) - 1]) {
                break;
            }
            if (isset($val['file'])) {
                $strBacktrace .= str_replace(DOCROOT, '.', $val['file']) . ' on line ' . $val['line'];
            }
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
                    foreach ($val['args'] as $val) {
                        $strFunc .= $strComma . $this->_printVar($val);
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
     *
     * @param mixed $var value, which should be formatted
     *
     * @return string formatted string
     */
    private function _printVar($var) {
        if (is_string($var)) {
            $search = ["\x00", "\x0a", "\x0d", "\x1a", "\x09"];
            $replace = ['\0', '\n', '\r', '\Z', '\t'];
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
