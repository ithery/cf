<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * File log writer. Writes out messages and stores them in a YYYY/MM directory.
 */
class CLogger_Writer_File extends CLogger_Writer {

    /**
     * @var  string  Directory to place log files in
     */
    protected $_directory;

    /**
     * Creates a new file logger. Checks that the directory exists and
     * is writable.
     *
     *     $writer = new Log_File($directory);
     *
     * @param   string  $directory  log directory
     * @return  void
     */
    public function __construct($options) {
        $basic_path = DEFAULTPATH;
        if(!is_dir(DEFAULTPATH)) {
            $basic_path = DOCROOT;
        }
        $dir = $basic_path . 'logs' . DS;
        $path = carr::get($options, 'path');
        if (!is_dir($dir . ltrim($path, '/'))) {
            if (!is_dir($dir)) {
                mkdir($dir, 02777);
                // Set permissions (must be manually set to fix umask issues)
                chmod($dir, 02777);
            }
            
            if (strlen($path) > 0) {
                $folders = explode('/', $path);
                
                foreach ($folders as $folder) {
                    if (strlen($folder) > 0) {
                        if (!is_dir($dir)) {
                            mkdir($dir, 02777);
                            // Set permissions (must be manually set to fix umask issues)
                            chmod($dir, 02777);
                        }
                    }
                }
            }
        }
        
        
        if (!is_dir($dir) OR ! is_writable($dir)) {
            throw new CException('Directory :dir must be writable', array(':dir' => $path));
        }
        
        // Determine the directory path
        $this->_directory = realpath($dir) . DIRECTORY_SEPARATOR;
    }

    /**
     * Writes each of the messages into the log file. The log file will be
     * appended to the `YYYY/MM/DD.log.php` file, where YYYY is the current
     * year, MM is the current month, and DD is the current day.
     *
     *     $writer->write($messages);
     *
     * @param   array   $messages
     * @return  void
     */
    public function write(array $messages) {
        // Set the yearly directory name
        $date = date('Y-m-d');
        list($year, $month, $day) = explode("-", $date);
        $directory = $this->_directory . $year;

        if (!is_dir($directory)) {
            // Create the yearly directory
            mkdir($directory, 02777);

            // Set permissions (must be manually set to fix umask issues)
            chmod($directory, 02777);
        }

        // Add the month to the directory
        $directory .= DIRECTORY_SEPARATOR . $month;

        if (!is_dir($directory)) {
            // Create the monthly directory
            mkdir($directory, 02777);

            // Set permissions (must be manually set to fix umask issues)
            chmod($directory, 02777);
        }

        // Set the name of the log file
        $filename = $directory . DIRECTORY_SEPARATOR . $year . $month . $day . EXT;

        if (!file_exists($filename)) {
            // Create the log file
            file_put_contents($filename, CF::FILE_SECURITY . ' ?>' . PHP_EOL);

            // Allow anyone to write to log files
            chmod($filename, 0666);
        }

        foreach ($messages as $message) {
            // Write each message into the log file
            file_put_contents($filename, PHP_EOL . $this->format_message($message), FILE_APPEND);
        }
        
        $rotator = CLogger_Rotator::createRotate($filename);
        $rotator->run();
    }

}
