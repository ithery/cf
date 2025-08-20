<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 3:04:56 PM
 */
class CServer_Command extends CServer_Base {
    protected static $instance = [];

    public function __construct($sshConfig = null) {
        if ($sshConfig && $sshConfig instanceof CRemote_SSH) {
            $this->ssh = $sshConfig;
            $sshConfig = $sshConfig->getConfig();
        }
        $this->sshConfig = $sshConfig;
        $this->host = carr::get($sshConfig, 'host');
    }

    public static function instance($sshConfig = null) {
        $ssh = null;
        if ($sshConfig && $sshConfig instanceof CRemote_SSH) {
            $ssh = $sshConfig;
            $sshConfig = $sshConfig->getConfig();
        }

        if (!is_array(self::$instance)) {
            self::$instance = [];
        }
        $host = 'localhost';

        if ($sshConfig != null) {
            $host = carr::get($sshConfig, 'host', carr::get($sshConfig, 'ip_address'));
            if ($ssh == null) {
                $ssh = CRemote::ssh($sshConfig);
            }
        }
        if (!isset(self::$instance[$host])) {
            self::$instance[$host] = new CServer_Command($ssh);
        }

        return self::$instance[$host];
    }

    /**
     * Get the content of stdout/stderr with the option to set a timeout for reading.
     *
     * @param array  $pipes   array of file pointers for stdin, stdout, stderr (proc_open())
     * @param string &$out    target string for the output message (reference)
     * @param string &$err    target string for the error message (reference)
     * @param int    $timeout timeout value in seconds
     *
     * @return bool timeout expired or not
     */
    private function timeoutfgets($pipes, &$out, &$err, $timeout) {
        $w = null;
        $e = null;
        $te = false;

        if (CServer::config()->isModePopen()) {
            $pipe2 = false;
        } else {
            $pipe2 = true;
        }
        while (!(feof($pipes[1]) && (!$pipe2 || feof($pipes[2])))) {
            if ($pipe2) {
                $read = [$pipes[1], $pipes[2]];
            } else {
                $read = [$pipes[1]];
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
     * on WINNT we simply return the name with the exe extension to the program name.
     *
     * @param string $strProgram name of the program
     *
     * @return null|string complete path and name of the program
     */
    private static function findProgram($strProgram) {
        $path_parts = pathinfo($strProgram);
        if (empty($path_parts['basename'])) {
            return null;
        }
        $arrPath = [];

        if (empty($path_parts['dirname']) || ($path_parts['dirname'] == '.')) {
            if ((CServer::getOS() == 'WINNT') && empty($path_parts['extension'])) {
                $strProgram .= '.exe';
                $path_parts = pathinfo($strProgram);
            }
            if (CServer::getOS() == 'WINNT') {
                if (static::readEnv('Path', $serverpath)) {
                    $arrPath = preg_split('/;/', $serverpath, -1, PREG_SPLIT_NO_EMPTY);
                }
            } else {
                if (static::readEnv('PATH', $serverpath)) {
                    $arrPath = preg_split('/:/', $serverpath, -1, PREG_SPLIT_NO_EMPTY);
                }
            }
            if ((CServer::config()->getUnameo() === 'Android') && !empty($arrPath)) {
                array_push($arrPath, '/system/bin'); // Termux patch
            }
            if (is_string(CServer::config()->getAddPaths())) {
                if (preg_match(CServer::ARRAY_EXP, CServer::config()->getAddPaths())) {
                    $arrPath = array_merge(eval(CServer::config()->getAddPaths()), $arrPath); // In this order so $addpaths is before $arrPath when looking for a program
                } else {
                    $arrPath = array_merge([CServer::config()->getAddPaths()], $arrPath); // In this order so $addpaths is before $arrPath when looking for a program
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

        $exceptPath = '';
        if ((CServer::getOS() == 'WINNT') && static::readEnv('WinDir', $windir)) {
            foreach ($arrPath as $strPath) {
                if ((strtolower($strPath) == $windir . '\\system32') && is_dir($windir . '\\SysWOW64')) {
                    if (is_dir($windir . '\\sysnative')) {
                        $exceptPath = $windir . '\\sysnative'; //32-bit PHP on 64-bit Windows
                    } else {
                        $exceptPath = $windir . '\\SysWOW64'; //64-bit PHP on 64-bit Windows
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
                $strPath = rtrim($strPath, '\\');
                $strPathS = $strPath . '\\';
            } else {
                $strPath = rtrim($strPath, '/');
                $strPathS = $strPath . '/';
            }
            if (($strPath !== $exceptPath) && !@is_dir($strPath)) {
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
     * File exists.
     *
     * @param string $strFileName name of the file which should be check
     *
     * @return bool command successfull or not
     */
    public static function fileExists($strFileName) {
        $log = CServer::config()->getLog();
        if ($log && is_string($log) && (strlen($log) > 0) && ((substr($log, 0, 1) == '-') || (substr($log, 0, 1) == '+'))) {
            $log_file = substr($log, 1);
            if (file_exists($log_file) && ($contents = @file_get_contents($log_file)) && preg_match("/^\-\-\-[^-\n]+\-\-\- " . preg_quote('Reading: ' . $strFileName, '/') . "\n/m", $contents)) {
                return true;
            } else {
                if (substr($log, 0, 1) == '-') {
                    return false;
                }
            }
        }

        $exists = @file_exists($strFileName);
        if (is_string($log) && (strlen($log) > 0) && (substr($log, 0, 1) != '-') && (substr($log, 0, 1) != '+')) {
            if ((substr($strFileName, 0, 5) === '/dev/') && $exists) {
                error_log('---' . gmdate('r T') . '--- Reading: ' . $strFileName . "\ndevice exists\n", 3, $log);
            }
        }

        return $exists;
    }

    /**
     * Read data from array $_SERVER.
     *
     * @param string $strElem    element of array
     * @param string &$strBuffer output of the command
     *
     * @return string
     */
    public static function readEnv($strElem, &$strBuffer) {
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
     * Read a file and return the content as a string.
     *
     * @param string $strFileName name of the file which should be read
     * @param string &$strRet     content of the file (reference)
     * @param int    $intLines    control how many lines should be read
     * @param int    $intBytes    control how many bytes of each line should be read
     * @param bool   $booErrorRep en- or disables the reporting of errors which should be logged
     *
     * @return bool command successfull or not
     */
    public function rfts($strFileName, &$strRet, $intLines = 0, $intBytes = 4096, $booErrorRep = true) {
        $log = CServer::config()->getLog();
        if ($ssh = $this->getSSH()) {
            $output = '';
            $ssh->run('cat ' . $strFileName, function ($line) use (&$output) {
                $output .= $line;
            });

            $strRet = $output;
            if (cstr::contains($output, ['No such file or directory'])) {
                return false;
            }

            return true;
        }

        $strFile = '';
        $intCurLine = 1;
        $error = CServer::error();
        if (@file_exists($strFileName)) {
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
                    if (is_string($log) && (strlen($log) > 0) && (substr($log, 0, 1) != '-') && (substr($log, 0, 1) != '+')) {
                        if ((strlen($strRet) > 0) && (substr($strRet, -1) != "\n")) {
                            error_log('---' . gmdate('r T') . '--- Reading: ' . $strFileName . "\n" . $strRet . "\n", 3, $log);
                        } else {
                            error_log('---' . gmdate('r T') . '--- Reading: ' . $strFileName . "\n" . $strRet, 3, $log);
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
     * NOT $program = CommonFunctions::executeProgram('netstat', '-anp|grep LIST');.
     *
     * @param string $strProgramname name of the program
     * @param string $strArgs        arguments to the program
     * @param string &$strBuffer     output of the command
     * @param bool   $booErrorRep    en- or disables the reporting of errors which should be logged
     * @param int    $timeout        timeout value in seconds (default value is 30)
     *
     * @return bool command successfull or not
     */
    public function executeProgram($strProgramname, $strArgs, &$strBuffer, $booErrorRep = true, $timeout = 30) {
        if ($ssh = $this->getSSH()) {
            $output = '';
            $command = $strProgramname . ' ' . $strArgs;
            $ssh->run($command, function ($line) use (&$output) {
                $output .= $line;
            });

            $strBuffer = $output;
            // if (cstr::contains($output, ['No such file or directory'])) {
            //     return false;
            // }

            return true;
        }

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

        if ((CServer::getOS() !== 'WINNT') && is_string(CServer::config()->getSudoCommands())) {
            if (preg_match(CSERVER::ARRAY_EXP, CServer::config()->getSudoCommands())) {
                $sudocommands = eval(CServer::config()->getSudoCommands());
            } else {
                $sudocommands = [CServer::config()->getSudoCommands()];
            }
            if (in_array($strProgramname, $sudocommands)) {
                $sudoProgram = self::findProgram('sudo');
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
                    $strNewcmd = self::findProgram($strCmd);
                    $strArgs = preg_replace("/\| " . $strCmd . '/', '| "' . $strNewcmd . '"', $strArgs);
                }
            }
            $strArgs = ' ' . $strArgs;
        }
        $cmd = $strSet . $strProgram . $strArgs;
        $strBuffer = '';
        $strError = '';
        $pipes = [];
        $descriptorspec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];

        return $this->procOpen($cmd, $strBuffer, $strError, $booErrorRep, $timeout);
    }

    /**
     * Parsing the output of df command.
     *
     * @param string $df_param   additional parameter for df command
     * @param bool   $get_inodes
     *
     * @return array
     */
    public function df($df_param = '', $get_inodes = true) {
        $arrResult = [];
        if ($this->executeProgram('mount', '', $mount, CServer::config()->isDebug())) {
            $mount = preg_split("/\n/", $mount, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($mount as $mount_line) {
                if (preg_match("/(\S+) on ([\S ]+) type (.*) \((.*)\)/", $mount_line, $mount_buf)) {
                    $parm = [];
                    $parm['mountpoint'] = trim($mount_buf[2]);
                    $parm['fstype'] = $mount_buf[3];
                    $parm['name'] = $mount_buf[1];
                    if (CServer_Storage::SHOW_MOUNT_OPTION) {
                        $parm['options'] = $mount_buf[4];
                    }
                    $mount_parm[] = $parm;
                } elseif (preg_match("/(\S+) is (.*) mounted on (\S+) \(type (.*)\)/", $mount_line, $mount_buf)) {
                    $parm = [];
                    $parm['mountpoint'] = trim($mount_buf[3]);
                    $parm['fstype'] = $mount_buf[4];
                    $parm['name'] = $mount_buf[1];
                    if (CServer_Storage::SHOW_MOUNT_OPTION) {
                        $parm['options'] = $mount_buf[2];
                    }
                    $mount_parm[] = $parm;
                } elseif (preg_match("/(\S+) (.*) on (\S+) \((.*)\)/", $mount_line, $mount_buf)) {
                    $parm = [];
                    $parm['mountpoint'] = trim($mount_buf[3]);
                    $parm['fstype'] = $mount_buf[2];
                    $parm['name'] = $mount_buf[1];
                    if (CServer_Storage::SHOW_MOUNT_OPTION) {
                        $parm['options'] = $mount_buf[4];
                    }
                    $mount_parm[] = $parm;
                } elseif (preg_match("/(\S+) on ([\S ]+) \((\S+)(,\s(.*))?\)/", $mount_line, $mount_buf)) {
                    $parm = [];
                    $parm['mountpoint'] = trim($mount_buf[2]);
                    $parm['fstype'] = $mount_buf[3];
                    $parm['name'] = $mount_buf[1];
                    if (CServer_Storage::SHOW_MOUNT_OPTION) {
                        $parm['options'] = isset($mount_buf[5]) ? $mount_buf[5] : '';
                    }
                    $mount_parm[] = $parm;
                }
            }
        } elseif ($this->rfts('/etc/mtab', $mount)) {
            $mount = preg_split("/\n/", $mount, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($mount as $mount_line) {
                if (preg_match("/(\S+) (\S+) (\S+) (\S+) ([0-9]+) ([0-9]+)/", $mount_line, $mount_buf)) {
                    $parm = [];
                    $mount_point = preg_replace('/\\\\040/i', ' ', $mount_buf[2]); //space as \040
                    $parm['mountpoint'] = $mount_point;
                    $parm['fstype'] = $mount_buf[3];
                    $parm['name'] = $mount_buf[1];
                    if (CServer_Storage::SHOW_MOUNT_OPTION) {
                        $parm['options'] = $mount_buf[4];
                    }
                    $mount_parm[] = $parm;
                }
            }
        }
        if ($this->executeProgram('df', '-k ' . $df_param, $df, CServer::config()->isDebug()) && ($df !== '')) {
            $df = preg_split("/\n/", $df, -1, PREG_SPLIT_NO_EMPTY);
            if ($get_inodes && CServer_Storage::SHOW_INODES) {
                if ($this->executeProgram('df', '-i ' . $df_param, $df2, CServer::config()->isDebug())) {
                    $df2 = preg_split("/\n/", $df2, -1, PREG_SPLIT_NO_EMPTY);
                    // Store inode use% in an associative array (df_inodes) for later use
                    foreach ($df2 as $df2_line) {
                        if (preg_match("/^(\S+).*\s([0-9]+)%/", $df2_line, $inode_buf)) {
                            $df_inodes[$inode_buf[1]] = $inode_buf[2];
                        }
                    }
                }
            }
            foreach ($df as $df_line) {
                $df_buf1 = preg_split("/(\%\s)/", $df_line, 3);
                if (count($df_buf1) < 2) {
                    continue;
                }
                if (preg_match("/(.*)(\s+)(([0-9]+)(\s+)([0-9]+)(\s+)([\-0-9]+)(\s+)([0-9]+)$)/", $df_buf1[0], $df_buf2)) {
                    if (count($df_buf1) == 3) {
                        $df_buf = [$df_buf2[1], $df_buf2[4], $df_buf2[6], $df_buf2[8], $df_buf2[10], $df_buf1[2]];
                    } else {
                        $df_buf = [$df_buf2[1], $df_buf2[4], $df_buf2[6], $df_buf2[8], $df_buf2[10], $df_buf1[1]];
                    }
                    if (count($df_buf) == 6) {
                        $df_buf[5] = trim($df_buf[5]);
                        $dev = new CServer_Device_Disk();
                        $dev->setName(trim($df_buf[0]));
                        if ($df_buf[2] < 0) {
                            $dev->setTotal($df_buf[3] * 1024);
                            $dev->setUsed($df_buf[3] * 1024);
                        } else {
                            $dev->setTotal($df_buf[1] * 1024);
                            $dev->setUsed($df_buf[2] * 1024);
                            if ($df_buf[3] > 0) {
                                $dev->setFree($df_buf[3] * 1024);
                            }
                        }
                        if (CServer_Storage::SHOW_MOUNT_POINT) {
                            $dev->setMountPoint($df_buf[5]);
                        }

                        $notwas = true;
                        if (isset($mount_parm)) {
                            foreach ($mount_parm as $mount_param) { //name and mountpoint find
                                if (($mount_param['name'] === trim($df_buf[0])) && ($mount_param['mountpoint'] === $df_buf[5])) {
                                    $dev->setFsType($mount_param['fstype']);
                                    if (CServer_Storage::SHOW_MOUNT_OPTION && (trim($mount_param['options']) !== '')) {
                                        if (CServer_Storage::SHOW_MOUNT_CREDENTIALS) {
                                            $dev->setOptions($mount_param['options']);
                                        } else {
                                            $mpo = $mount_param['options'];

                                            $mpo = preg_replace('/(^guest,)|(^guest$)|(,guest$)/i', '', $mpo);
                                            $mpo = preg_replace('/,guest,/i', ',', $mpo);

                                            $mpo = preg_replace('/(^user=[^,]*,)|(^user=[^,]*$)|(,user=[^,]*$)/i', '', $mpo);
                                            $mpo = preg_replace('/,user=[^,]*,/i', ',', $mpo);

                                            $mpo = preg_replace('/(^username=[^,]*,)|(^username=[^,]*$)|(,username=[^,]*$)/i', '', $mpo);
                                            $mpo = preg_replace('/,username=[^,]*,/i', ',', $mpo);

                                            $mpo = preg_replace('/(^password=[^,]*,)|(^password=[^,]*$)|(,password=[^,]*$)/i', '', $mpo);
                                            $mpo = preg_replace('/,password=[^,]*,/i', ',', $mpo);

                                            $dev->setOptions($mpo);
                                        }
                                    }
                                    $notwas = false;

                                    break;
                                }
                            }
                            if ($notwas) {
                                foreach ($mount_parm as $mount_param) { //mountpoint find
                                    if ($mount_param['mountpoint'] === $df_buf[5]) {
                                        $dev->setFsType($mount_param['fstype']);
                                        if (CServer_Storage::SHOW_MOUNT_OPTION && (trim($mount_param['options']) !== '')) {
                                            if (CServer_Storage::SHOW_MOUNT_CREDENTIALS) {
                                                $dev->setOptions($mount_param['options']);
                                            } else {
                                                $mpo = $mount_param['options'];

                                                $mpo = preg_replace('/(^guest,)|(^guest$)|(,guest$)/i', '', $mpo);
                                                $mpo = preg_replace('/,guest,/i', ',', $mpo);

                                                $mpo = preg_replace('/(^user=[^,]*,)|(^user=[^,]*$)|(,user=[^,]*$)/i', '', $mpo);
                                                $mpo = preg_replace('/,user=[^,]*,/i', ',', $mpo);

                                                $mpo = preg_replace('/(^username=[^,]*,)|(^username=[^,]*$)|(,username=[^,]*$)/i', '', $mpo);
                                                $mpo = preg_replace('/,username=[^,]*,/i', ',', $mpo);

                                                $mpo = preg_replace('/(^password=[^,]*,)|(^password=[^,]*$)|(,password=[^,]*$)/i', '', $mpo);
                                                $mpo = preg_replace('/,password=[^,]*,/i', ',', $mpo);

                                                $dev->setOptions($mpo);
                                            }
                                        }
                                        $notwas = false;

                                        break;
                                    }
                                }
                            }
                        }

                        if ($notwas) {
                            $dev->setFsType('unknown');
                        }

                        if ($get_inodes && CServer_Storage::SHOW_INODES && isset($df_inodes[trim($df_buf[0])])) {
                            $dev->setPercentInodesUsed($df_inodes[trim($df_buf[0])]);
                        }
                        $arrResult[] = $dev;
                    }
                }
            }
        } else {
            if (isset($mount_parm)) {
                foreach ($mount_parm as $mount_param) {
                    $total = disk_total_space($mount_param['mountpoint']);
                    if (($mount_param['fstype'] != 'none') && ($total > 0)) {
                        $dev = new CServer_Device_Disk();
                        $dev->setName($mount_param['name']);
                        $dev->setFsType($mount_param['fstype']);

                        if (CServer_Storage::SHOW_MOUNT_POINT) {
                            $dev->setMountPoint($mount_param['mountpoint']);
                        }

                        $dev->setTotal($total);
                        $free = disk_free_space($mount_param['mountpoint']);
                        if ($free > 0) {
                            $dev->setFree($free);
                        } else {
                            $free = 0;
                        }
                        if ($total > $free) {
                            $dev->setUsed($total - $free);
                        }

                        if (CServer_Storage::SHOW_MOUNT_OPTION) {
                            if (CServer_Storage::SHOW_MOUNT_CREDENTIALS) {
                                $dev->setOptions($mount_param['options']);
                            } else {
                                $mpo = $mount_param['options'];

                                $mpo = preg_replace('/(^guest,)|(^guest$)|(,guest$)/i', '', $mpo);
                                $mpo = preg_replace('/,guest,/i', ',', $mpo);

                                $mpo = preg_replace('/(^user=[^,]*,)|(^user=[^,]*$)|(,user=[^,]*$)/i', '', $mpo);
                                $mpo = preg_replace('/,user=[^,]*,/i', ',', $mpo);

                                $mpo = preg_replace('/(^username=[^,]*,)|(^username=[^,]*$)|(,username=[^,]*$)/i', '', $mpo);
                                $mpo = preg_replace('/,username=[^,]*,/i', ',', $mpo);

                                $mpo = preg_replace('/(^password=[^,]*,)|(^password=[^,]*$)|(,password=[^,]*$)/i', '', $mpo);
                                $mpo = preg_replace('/,password=[^,]*,/i', ',', $mpo);

                                $dev->setOptions($mpo);
                            }
                        }
                        $arrResult[] = $dev;
                    }
                }
            }
        }

        return $arrResult;
    }

    public function procOpen($cmd, &$strBuffer, &$strError, $booErrorRep = true, $timeout = 30) {
        $log = CServer::config()->getLog();
        $pipes = [];
        $process = null;
        $descriptorspec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        if ($ssh = $this->getSSH()) {
            $output = '';
            $ssh->run($cmd, function ($line) use (&$strBuffer) {
                $strBuffer .= $line;
            });
        } else {
            if (CServer::config()->isModePopen()) {
                if (CServer::getOS() == 'WINNT') {
                    $process = $pipes[1] = popen($cmd . ' 2>nul', 'r');
                } else {
                    $process = $pipes[1] = popen($cmd . ' 2>/dev/null', 'r');
                }
            } else {
                $process = proc_open($cmd, $descriptorspec, $pipes);
            }
        }
        if ($this->sshConfig == null) {
            if (is_resource($process)) {
                $te = $this->timeoutfgets($pipes, $strBuffer, $strError, $timeout);

                if (CServer::config()->isModePopen()) {
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
                    CServer::error()->addError($cmd, "\nOpen process error");
                }

                return false;
            }
        }

        $strError = trim($strError);
        $strBuffer = trim($strBuffer);
        if (is_string($log) && (strlen($log) > 0) && (substr($log, 0, 1) != '-') && (substr($log, 0, 1) != '+')) {
            error_log('---' . gmdate('r T') . '--- Executing: ' . trim($cmd) . "\n" . $strBuffer . "\n", 3, $log);
        }

        if (!empty($strError)) {
            if ($booErrorRep) {
                CServer::error()->addError($cmd, $strError . "\nReturn value: " . $return_value);
            }

            return $return_value == 0;
        }

        return true;
    }
}
