<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 12, 2019, 4:15:09 PM
 */
class CDaemon_Helper {
    /**
     * @var int
     */
    const UNIX = 0;

    /**
     * @var int
     */
    const WINDOWS = 1;

    /**
     * @var resource[]
     */
    private $lockHandles = [];

    public function __construct() {
    }

    /**
     * @param string $lockFile
     *
     * @throws Exception
     */
    public function releaseLock($lockFile) {
        if (!array_key_exists($lockFile, $this->lockHandles)) {
            throw new Exception("Lock NOT held - bug? Lockfile: ${lockFile}");
        }
        if ($this->lockHandles[$lockFile]) {
            ftruncate($this->lockHandles[$lockFile], 0);
            flock($this->lockHandles[$lockFile], LOCK_UN);
        }
        unset($this->lockHandles[$lockFile]);
    }

    /**
     * @param string $lockFile
     *
     * @return int
     */
    public function getLockLifetime($lockFile) {
        if (!file_exists($lockFile)) {
            return 0;
        }
        $pid = file_get_contents($lockFile);
        if (empty($pid)) {
            return 0;
        }
        if (!posix_kill((int) $pid, 0)) {
            return 0;
        }
        $stat = stat($lockFile);

        return time() - $stat['mtime'];
    }

    /**
     * @return string
     */
    public function getTempDir() {
        // @codeCoverageIgnoreStart
        if (function_exists('sys_get_temp_dir')) {
            $tmp = sys_get_temp_dir();
        } elseif (!empty($_SERVER['TMP'])) {
            $tmp = $_SERVER['TMP'];
        } elseif (!empty($_SERVER['TEMP'])) {
            $tmp = $_SERVER['TEMP'];
        } elseif (!empty($_SERVER['TMPDIR'])) {
            $tmp = $_SERVER['TMPDIR'];
        } else {
            $tmp = getcwd();
        }
        // @codeCoverageIgnoreEnd
        return $tmp;
    }

    /**
     * @return string
     */
    public function getHost() {
        return php_uname('n');
    }

    /**
     * @return null|string
     */
    public function getApplicationEnv() {
        return isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : null;
    }

    /**
     * @return int
     */
    public static function getPlatform() {
        if (strncasecmp(PHP_OS, 'Win', 3) === 0) {
            // @codeCoverageIgnoreStart
            return self::WINDOWS;
            // @codeCoverageIgnoreEnd
        }

        return self::UNIX;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public static function escape($input) {
        $input = strtolower($input);
        $input = preg_replace('/[^a-z0-9_. -]+/', '', $input);
        $input = trim($input);
        $input = str_replace(' ', '_', $input);
        $input = preg_replace('/_{2,}/', '_', $input);

        return $input;
    }

    public static function getSystemNullDevice() {
        $platform = static::getPlatform();
        if ($platform === self::UNIX) {
            return '/dev/null';
        }

        return 'NUL';
    }

    public static function pidPath() {
        return DOCROOT . 'data/daemon/' . CF::appCode() . '/daemon/pid/';
    }

    public static function logPath() {
        return DOCROOT . 'data/daemon/' . CF::appCode() . '/log/';
    }

    public static function getLogFile($className, $filename = null) {
        if ($filename == null) {
            $filename = $className . '.log';
        }

        return static::logPath() . $className . '/' . $filename;
    }

    public static function getPidFile($className) {
        return static::pidPath() . $className . '.pid';
    }

    public static function getSupervisorPidFile($className, $name) {
        return static::pidPath() . $className . DS . $name . '.pid';
    }

    public static function getLogFileList($className) {
        $logPath = rtrim(static::logPath(), '/') . '/' . $className;
        if (!is_dir($logPath)) {
            return [];
        }
        $files = CFile::files($logPath);
        $list = [];
        foreach ($files as $file) {
            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            $basename = $file->getBasename();

            $list[$file->getPath() . DS . $file->getFilename()] = $basename;
        }

        return $list;
    }
}
