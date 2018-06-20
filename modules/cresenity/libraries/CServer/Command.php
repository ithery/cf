<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 3:04:56 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Command {

    protected static $instance;

    public static function instance() {
        if (self::$instance == null) {
            return new CServer_Command();
        }
        return self::$instance;
    }

    /**
     * get the content of stdout/stderr with the option to set a timeout for reading
     *
     * @param array   $pipes   array of file pointers for stdin, stdout, stderr (proc_open())
     * @param string  &$out    target string for the output message (reference)
     * @param string  &$err    target string for the error message (reference)
     * @param integer $timeout timeout value in seconds
     *
     * @return boolean timeout expired or not
     */
    private function timeoutfgets($pipes, &$out, &$err, $timeout) {
        $w = null;
        $e = null;
        $te = false;

        if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
            $pipe2 = false;
        } else {
            $pipe2 = true;
        }
        while (!(feof($pipes[1]) && (!$pipe2 || feof($pipes[2])))) {
            if ($pipe2) {
                $read = array($pipes[1], $pipes[2]);
            } else {
                $read = array($pipes[1]);
            }

            $n = stream_select($read, $w, $e, $timeout);

            if ($n === false) {
                error_log('stream_select: failed !');
                break;
            } elseif ($n === 0) {
                error_log('stream_select: timeout expired !');
                $te = true;
                break;
            }

            foreach ($read as $r) {
                if ($r == $pipes[1]) {
                    $out .= fread($r, 4096);
                } elseif (feof($pipes[1]) && $pipe2 && ($r == $pipes[2])) {//read STDERR after STDOUT
                    $err .= fread($r, 4096);
                }
            }
        }

        return $te;
    }

    /**
     * Find a system program, do also path checking when not running on WINNT
     * on WINNT we simply return the name with the exe extension to the program name
     * @param string $strProgram name of the program
     * @return string|null complete path and name of the program
     */
    private function findProgram($strProgram) {
        $path_parts = pathinfo($strProgram);
        if (empty($path_parts['basename'])) {
            return null;
        }
        $arrPath = array();

        if (empty($path_parts['dirname']) || ($path_parts['dirname'] == '.')) {
            if ((CServer::getOS() == 'WINNT') && empty($path_parts['extension'])) {
                $strProgram .= '.exe';
                $path_parts = pathinfo($strProgram);
            }
            if (CServer::getOS() == 'WINNT') {
                if ($this->readEnv('Path', $serverpath)) {
                    $arrPath = preg_split('/;/', $serverpath, -1, PREG_SPLIT_NO_EMPTY);
                }
            } else {
                if ($this->readEnv('PATH', $serverpath)) {
                    $arrPath = preg_split('/:/', $serverpath, -1, PREG_SPLIT_NO_EMPTY);
                }
            }
            if (defined('PSI_UNAMEO') && (PSI_UNAMEO === 'Android') && !empty($arrPath)) {
                array_push($arrPath, '/system/bin'); // Termux patch
            }
            if (defined('PSI_ADD_PATHS') && is_string(PSI_ADD_PATHS)) {
                if (preg_match(ARRAY_EXP, PSI_ADD_PATHS)) {
                    $arrPath = array_merge(eval(PSI_ADD_PATHS), $arrPath); // In this order so $addpaths is before $arrPath when looking for a program
                } else {
                    $arrPath = array_merge(array(PSI_ADD_PATHS), $arrPath); // In this order so $addpaths is before $arrPath when looking for a program
                }
            }
        } else { //directory defined
            array_push($arrPath, $path_parts['dirname']);
            $strProgram = $path_parts['basename'];
        }

        //add some default paths if we still have no paths here
        if (empty($arrPath) && CServer::getOS() != 'WINNT') {
            if (CServer::getOS() == 'Android') {
                array_push($arrPath, '/system/bin');
            } else {
                array_push($arrPath, '/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
            }
        }

        $exceptPath = "";
        if ((CServer::getOS() == 'WINNT') && $this->readEnv('WinDir', $windir)) {
            foreach ($arrPath as $strPath) {
                if ((strtolower($strPath) == $windir . "\\system32") && is_dir($windir . "\\SysWOW64")) {
                    if (is_dir($windir . "\\sysnative")) {
                        $exceptPath = $windir . "\\sysnative"; //32-bit PHP on 64-bit Windows
                    } else {
                        $exceptPath = $windir . "\\SysWOW64"; //64-bit PHP on 64-bit Windows
                    }
                    array_push($arrPath, $exceptPath);
                    break;
                }
            }
        } elseif (CServer::getOS() == 'Android') {
            $exceptPath = '/system/bin';
        }

        foreach ($arrPath as $strPath) {
            // Path with and without trailing slash
            if (CServer::getOS() == 'WINNT') {
                $strPath = rtrim($strPath, "\\");
                $strPathS = $strPath . "\\";
            } else {
                $strPath = rtrim($strPath, "/");
                $strPathS = $strPath . "/";
            }
            if (($strPath !== $exceptPath) && !is_dir($strPath)) {
                continue;
            }
            if (CServer::getOS() == 'WINNT') {
                $strProgrammpath = $strPathS . $strProgram;
            } else {
                $strProgrammpath = $strPathS . $strProgram;
            }
            if (is_executable($strProgrammpath)) {
                return $strProgrammpath;
            }
        }

        return null;
    }

    /**
     * file exists
     *
     * @param string $strFileName name of the file which should be check
     *
     * @return boolean command successfull or not
     */
    public static function fileExists($strFileName) {
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG) > 0) && ((substr(PSI_LOG, 0, 1) == "-") || (substr(PSI_LOG, 0, 1) == "+"))) {
            $log_file = substr(PSI_LOG, 1);
            if (file_exists($log_file) && ($contents = @file_get_contents($log_file)) && preg_match("/^\-\-\-[^-\n]+\-\-\- " . preg_quote("Reading: " . $strFileName, '/') . "\n/m", $contents)) {
                return true;
            } else {
                if (substr(PSI_LOG, 0, 1) == "-") {
                    return false;
                }
            }
        }

        $exists = file_exists($strFileName);
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG) > 0) && (substr(PSI_LOG, 0, 1) != "-") && (substr(PSI_LOG, 0, 1) != "+")) {
            if ((substr($strFileName, 0, 5) === "/dev/") && $exists) {
                error_log("---" . gmdate('r T') . "--- Reading: " . $strFileName . "\ndevice exists\n", 3, PSI_LOG);
            }
        }

        return $exists;
    }

    /**
     * read data from array $_SERVER
     *
     * @param string $strElem    element of array
     * @param string &$strBuffer output of the command
     *
     * @return string
     */
    public function readEnv($strElem, &$strBuffer) {
        $strBuffer = '';
        if (CServer::getOS() == 'WINNT') { //case insensitive
            if (isset($_SERVER)) {
                foreach ($_SERVER as $index => $value) {
                    if (is_string($value) && (trim($value) !== '') && (strtolower($index) === strtolower($strElem))) {
                        $strBuffer = $value;

                        return true;
                    }
                }
            }
        } else {
            if (isset($_SERVER[$strElem]) && is_string($value = $_SERVER[$strElem]) && (trim($value) !== '')) {
                $strBuffer = $value;

                return true;
            }
        }

        return false;
    }

    /**
     * read a file and return the content as a string
     *
     * @param string  $strFileName name of the file which should be read
     * @param string  &$strRet     content of the file (reference)
     * @param integer $intLines    control how many lines should be read
     * @param integer $intBytes    control how many bytes of each line should be read
     * @param boolean $booErrorRep en- or disables the reporting of errors which should be logged
     *
     * @return boolean command successfull or not
     */
    public function rfts($strFileName, &$strRet, $intLines = 0, $intBytes = 4096, $booErrorRep = true) {


        $strFile = "";
        $intCurLine = 1;
        $error = CServer::error();
        if (file_exists($strFileName)) {
            if (is_readable($strFileName)) {
                if ($fd = fopen($strFileName, 'r')) {
                    while (!feof($fd)) {
                        $strFile .= fgets($fd, $intBytes);
                        if ($intLines <= $intCurLine && $intLines != 0) {
                            break;
                        } else {
                            $intCurLine++;
                        }
                    }
                    fclose($fd);
                    $strRet = $strFile;
                    if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG) > 0) && (substr(PSI_LOG, 0, 1) != "-") && (substr(PSI_LOG, 0, 1) != "+")) {
                        if ((strlen($strRet) > 0) && (substr($strRet, -1) != "\n")) {
                            error_log("---" . gmdate('r T') . "--- Reading: " . $strFileName . "\n" . $strRet . "\n", 3, PSI_LOG);
                        } else {
                            error_log("---" . gmdate('r T') . "--- Reading: " . $strFileName . "\n" . $strRet, 3, PSI_LOG);
                        }
                    }
                } else {
                    if ($booErrorRep) {
                        $error->addError('fopen(' . $strFileName . ')', 'file can not read by phpsysinfo');
                    }

                    return false;
                }
            } else {
                if ($booErrorRep) {
                    $error->addError('fopen(' . $strFileName . ')', 'file permission error');
                }

                return false;
            }
        } else {
            if ($booErrorRep) {
                $error->addError('file_exists(' . $strFileName . ')', 'the file does not exist on your machine');
            }

            return false;
        }

        return true;
    }

    /**
     * Execute a system program. return a trim()'d result.
     * does very crude pipe checking.  you need ' | ' for it to work
     * ie $program = CommonFunctions::executeProgram('netstat', '-anp | grep LIST');
     * NOT $program = CommonFunctions::executeProgram('netstat', '-anp|grep LIST');
     *
     * @param string  $strProgramname name of the program
     * @param string  $strArgs        arguments to the program
     * @param string  &$strBuffer     output of the command
     * @param boolean $booErrorRep    en- or disables the reporting of errors which should be logged
     * @param integer $timeout        timeout value in seconds (default value is 30)
     *
     * @return boolean command successfull or not
     */
    public function executeProgram($strProgramname, $strArgs, &$strBuffer, $booErrorRep = true, $timeout = 30) {


        if ((CServer::getOS() !== 'WINNT') && preg_match('/^([^=]+=[^ \t]+)[ \t]+(.*)$/', $strProgramname, $strmatch)) {
            $strSet = $strmatch[1] . ' ';
            $strProgramname = $strmatch[2];
        } else {
            $strSet = '';
        }
        $strProgram = $this->findProgram($strProgramname);
        $error = CServer::error();
        if (!$strProgram) {
            if ($booErrorRep) {
                $error->addError('find_program("' . $strProgramname . '")', 'program not found on the machine');
            }

            return false;
        } else {
            if (preg_match('/\s/', $strProgram)) {
                $strProgram = '"' . $strProgram . '"';
            }
        }

        if ((CServer::getOS() !== 'WINNT') && defined('PSI_SUDO_COMMANDS') && is_string(PSI_SUDO_COMMANDS)) {
            if (preg_match(ARRAY_EXP, PSI_SUDO_COMMANDS)) {
                $sudocommands = eval(PSI_SUDO_COMMANDS);
            } else {
                $sudocommands = array(PSI_SUDO_COMMANDS);
            }
            if (in_array($strProgramname, $sudocommands)) {
                $sudoProgram = self::_findProgram("sudo");
                if (!$sudoProgram) {
                    if ($booErrorRep) {
                        $error->addError('find_program("sudo")', 'program not found on the machine');
                    }

                    return false;
                } else {
                    if (preg_match('/\s/', $sudoProgram)) {
                        $strProgram = '"' . $sudoProgram . '" ' . $strProgram;
                    } else {
                        $strProgram = $sudoProgram . ' ' . $strProgram;
                    }
                }
            }
        }

        // see if we've gotten a |, if we have we need to do path checking on the cmd
        if ($strArgs) {
            $arrArgs = preg_split('/ /', $strArgs, -1, PREG_SPLIT_NO_EMPTY);
            for ($i = 0, $cnt_args = count($arrArgs); $i < $cnt_args; $i++) {
                if ($arrArgs[$i] == '|') {
                    $strCmd = $arrArgs[$i + 1];
                    $strNewcmd = self::_findProgram($strCmd);
                    $strArgs = preg_replace("/\| " . $strCmd . '/', '| "' . $strNewcmd . '"', $strArgs);
                }
            }
            $strArgs = ' ' . $strArgs;
        }

        $strBuffer = '';
        $strError = '';
        $pipes = array();
        $descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w"));
        if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
            if (PSI_OS == 'WINNT') {
                $process = $pipes[1] = popen($strSet . $strProgram . $strArgs . " 2>nul", "r");
            } else {
                $process = $pipes[1] = popen($strSet . $strProgram . $strArgs . " 2>/dev/null", "r");
            }
        } else {
            $process = proc_open($strSet . $strProgram . $strArgs, $descriptorspec, $pipes);
        }
        if (is_resource($process)) {
            $te = $this->timeoutfgets($pipes, $strBuffer, $strError, $timeout);
            if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
                $return_value = pclose($pipes[1]);
            } else {
                fclose($pipes[0]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                // It is important that you close any pipes before calling
                // proc_close in order to avoid a deadlock
                if ($te) {
                    proc_terminate($process); // proc_close tends to hang if the process is timing out
                    $return_value = 0;
                } else {
                    $return_value = proc_close($process);
                }
            }
        } else {
            if ($booErrorRep) {
                $error->addError($strProgram, "\nOpen process error");
            }

            return false;
        }
        $strError = trim($strError);
        $strBuffer = trim($strBuffer);
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG) > 0) && (substr(PSI_LOG, 0, 1) != "-") && (substr(PSI_LOG, 0, 1) != "+")) {
            error_log("---" . gmdate('r T') . "--- Executing: " . trim($strProgramname . $strArgs) . "\n" . $strBuffer . "\n", 3, PSI_LOG);
        }
        if (!empty($strError)) {
            if ($booErrorRep) {
                $error->addError($strProgram, $strError . "\nReturn value: " . $return_value);
            }

            return $return_value == 0;
        }

        return true;
    }

}
