<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 12, 2019, 3:17:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Symfony\Component\Process\PhpExecutableFinder;

class CDaemon {

    /**
     * @var array
     */
    protected $config = [];

    /**
     *
     * @var CDaemon_Helper
     */
    protected $helper;

    public static function cliRunner($parameter = null) {

        $argv = carr::get($_SERVER, 'argv');
        if ($parameter == null) {
            $parameter = $argv[3];
        }
        parse_str($parameter, $config);
        $cls = carr::get($config, 'serviceClass');
        /** @var CJob_Exception $job */
        $serviceName = carr::get($config, 'serviceName', $cls);
        $cmd = carr::get($config, 'command');
        $pidFile = carr::get($config, 'pidFile');
        $dirPidFile = dirname($pidFile);
        $file = new CFile();
        if (!$file->isDirectory($dirPidFile)) {
            $file->makeDirectory($dirPidFile, 0755, true);
        }
     

        $service = $cls::createInstance($serviceName, $config);

        switch ($cmd) {
            case 'start':
            case 'stop':
            case 'restart':
            case 'reload':
            case 'status':
            case 'kill':
                call_user_func(array($service, $cmd));
                break;
            default:
                $service->showHelp();
                break;
        }
    }

    /**
     * @param array $config
     */
    public function __construct($config = []) {
        $this->setConfig($this->getDefaultConfig());
        $this->setConfig($config);

        $this->script = carr::get($config, 'script', DOCROOT . 'index.php');
        $this->uri = carr::get($config, 'uri', 'cresenity/daemon');
    }

    /**
     * @return array
     */
    public function getDefaultConfig() {
        return [
            'domain' => CF::domain(),
            'logFile' => 'log',
            'logErr' => 'log.err',
            'dateFormat' => 'Y-m-d H:i:s',
            'debug' => false,
        ];
    }

    /**
     * @return Helper
     */
    protected function getHelper() {
        if ($this->helper === null) {
            $this->helper = new CJob_Helper();
        }
        return $this->helper;
    }

    /**
     * @param array
     */
    public function setConfig(array $config) {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }

    public function run() {
        $isUnix = ($this->getHelper()->getPlatform() === CJob_Helper::UNIX);
        if ($isUnix && !extension_loaded('posix')) {
            throw new Exception('posix extension is required');
        }
        if ($isUnix) {
            return $this->runUnix();
        } else {
            return $this->runWindows();
        }
    }

    /**
     * @param string $job
     * @param array  $config
     */
    protected function runUnix() {
        $command = $this->getExecutableCommand();
        $binary = $this->getPhpBinary();
        $output = shell_exec("$binary $command");
        return $output;
    }

    // @codeCoverageIgnoreStart
    /**
     * @param string $job
     * @param array  $config
     */
    protected function runWindows() {
        // Run in background (non-blocking). From
        // http://us3.php.net/manual/en/function.exec.php#43834
        $binary = $this->getPhpBinary();
        $command = $this->getExecutableCommand();
        pclose(popen("start \"blah\" /B \"$binary\" $command", "r"));
    }

    // @codeCoverageIgnoreEnd
    /**
     * @param string $job
     * @param array  $config
     *
     * @return string
     */
    protected function getExecutableCommand() {
        $domain = carr::get($this->config, 'domain', CF::domain());
        return sprintf('"%s" "%s" "%s" "%s"', $this->script, $this->uri, $domain, http_build_query($this->config));
    }

    /**
     * @return false|string
     */
    protected function getPhpBinary() {
        $executableFinder = new PhpExecutableFinder();
        return $executableFinder->find();
    }

}
