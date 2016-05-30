<?php

    defined('SYSPATH') OR die('No direct access allowed.');

    class csysfunc {

        private static function _find_program($strProgram) {
            $arrPath = array();
            if (PHP_OS == 'WINNT') {
                $strProgram .= '.exe';
                $arrPath = preg_split('/;/', getenv("Path"), -1, PREG_SPLIT_NO_EMPTY);
            }
            else {
                $arrPath = preg_split('/:/', getenv("PATH"), -1, PREG_SPLIT_NO_EMPTY);
            }
            /*
              if (PSI_ADD_PATHS !== false) {
              $addpaths = preg_split('/,/', PSI_ADD_PATHS, -1, PREG_SPLIT_NO_EMPTY);
              $arrPath = array_merge($addpaths, $arrPath); // In this order so $addpaths is before $arrPath when looking for a program
              }
             */
            //add some default paths if we still have no paths here
            if (empty($arrPath) && PHP_OS != 'WINNT') {
                array_push($arrPath, '/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
            }
            // If open_basedir defined, fill the $open_basedir array with authorized paths,. (Not tested when no open_basedir restriction)
            if ((bool) ini_get('open_basedir')) {
                $open_basedir = preg_split('/:/', ini_get('open_basedir'), -1, PREG_SPLIT_NO_EMPTY);
            }
            if (PHP_OS == "WINNT") {
                $arrPath[] = "C:\Windows\system32";
            }
            foreach ($arrPath as $strPath) {

                // To avoid "open_basedir restriction in effect" error when testing paths if restriction is enabled
                if ((isset($open_basedir) && !in_array($strPath, $open_basedir)) || !is_dir($strPath)) {
                    continue;
                }
                $strProgrammpath = $strPath . DIRECTORY_SEPARATOR . $strProgram;

                if (is_executable($strProgrammpath)) {
                    return $strProgrammpath;
                }
            }

            return false;
        }

        public static function execute($strProgramname, $strArgs, &$strBuffer, $booErrorRep = true) {
            $strBuffer = '';
            $strError = '';
            $pipes = array();
            $strProgram = self::_find_program($strProgramname);
            if (!$strProgram) {
                if ($booErrorRep) {
                    throw new Exception('find_program(' . $strProgramname . ') - program not found on the machine');
                    return false;
                }
                return false;
            }
            // see if we've gotten a |, if we have we need to do path checking on the cmd
            if ($strArgs) {
                $arrArgs = preg_split('/ /', $strArgs, -1, PREG_SPLIT_NO_EMPTY);
                for ($i = 0, $cnt_args = count($arrArgs); $i < $cnt_args; $i++) {
                    if ($arrArgs[$i] == '|') {
                        $strCmd = $arrArgs[$i + 1];
                        $strNewcmd = self::_find_program($strCmd);
                        $strArgs = preg_replace("/\| " . $strCmd . '/', "| " . $strNewcmd, $strArgs);
                    }
                }
            }
            $descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w"));
            $process = proc_open($strProgram . " " . $strArgs, $descriptorspec, $pipes);
            if (is_resource($process)) {
                $strBuffer .= self::_timeoutfgets($pipes, $strBuffer, $strError);
                $return_value = proc_close($process);
            }
            $strError = trim($strError);
            $strBuffer = trim($strBuffer);
            if (!empty($strError) && $return_value <> 0) {
                if ($booErrorRep) {
                    throw new Exception($strProgram . " - " . $strError . "\nReturn value: " . $return_value);
                }
                return false;
            }
            if (!empty($strError)) {
                if ($booErrorRep) {
                    throw new Exception($strProgram . " - " . $strError . "\nReturn value: " . $return_value);
                }
                return true;
            }
            return true;
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
        public static function rfts($strFileName, &$strRet, $intLines = 0, $intBytes = 4096, $booErrorRep = true) {
            $strFile = "";
            $intCurLine = 1;
            $error = CError::factory();
            if (file_exists($strFileName)) {
                if ($fd = fopen($strFileName, 'r')) {
                    while (!feof($fd)) {
                        $strFile .= fgets($fd, $intBytes);
                        if ($intLines <= $intCurLine && $intLines != 0) {
                            break;
                        }
                        else {
                            $intCurLine++;
                        }
                    }
                    fclose($fd);
                    $strRet = $strFile;
                }
                else {
                    if ($booErrorRep) {
                        $error->add_error('fopen(' . $strFileName . ')', 'file can not read by phpsysinfo');
                    }
                    return false;
                }
            }
            else {
                if ($booErrorRep) {
                    $error->add_error('file_exists(' . $strFileName . ')', 'the file does not exist on your machine');
                }
                return false;
            }
            return true;
        }

        /**
         * reads a directory and return the name of the files and directorys in it
         *
         * @param string  $strPath     path of the directory which should be read
         * @param boolean $booErrorRep en- or disables the reporting of errors which should be logged
         *
         * @return array content of the directory excluding . and ..
         */
        public static function gdc($strPath, $booErrorRep = true) {
            $arrDirectoryContent = array();
            $error = CError::factory();
            if (is_dir($strPath)) {
                if ($handle = opendir($strPath)) {
                    while (($strFile = readdir($handle)) !== false) {
                        if ($strFile != "." && $strFile != "..") {
                            $arrDirectoryContent[] = $strFile;
                        }
                    }
                    closedir($handle);
                }
                else {
                    if ($booErrorRep) {
                        $error->add_error('opendir(' . $strPath . ')', 'directory can not be read by phpsysinfo');
                    }
                }
            }
            else {
                if ($booErrorRep) {
                    $error->add_error('is_dir(' . $strPath . ')', 'directory does not exist on your machine');
                }
            }
            return $arrDirectoryContent;
        }

        /**
         * get the content of stdout/stderr with the option to set a timeout for reading
         *
         * @param array   $pipes array of file pointers for stdin, stdout, stderr (proc_open())
         * @param string  &$out  target string for the output message (reference)
         * @param string  &$err  target string for the error message (reference)
         * @param integer $sek   timeout value in seconds
         *
         * @return void
         */
        private static function _timeoutfgets($pipes, &$out, &$err, $sek = 10) {
            // fill output string
            $time = $sek;
            $w = null;
            $e = null;

            while ($time >= 0) {
                $read = array($pipes[1]);
                /*
                  while (!feof($read[0]) && ($n = stream_select($read, $w, $e, $time)) !== false && $n > 0 && strlen($c = fgetc($read[0])) > 0) {
                  $out .= $c;
                 */
                while (!feof($read[0]) && ($n = stream_select($read, $w, $e, $time)) !== false && $n > 0) {
                    $out .= fread($read[0], 4096);
                }
                --$time;
            }
            // fill error string
            $time = $sek;
            while ($time >= 0) {
                $read = array($pipes[2]);
                /*
                  while (!feof($read[0]) && ($n = stream_select($read, $w, $e, $time)) !== false && $n > 0 && strlen($c = fgetc($read[0])) > 0) {
                  $err .= $c;
                 */
                while (!feof($read[0]) && ($n = stream_select($read, $w, $e, $time)) !== false && $n > 0) {
                    $err .= fread($read[0], 4096);
                }
                --$time;
            }
        }

    }
    