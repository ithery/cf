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

    public static function cliRunner($parameter = null) {

        $argv = carr::get($_SERVER, 'argv');
        if ($parameter == null) {
            $parameter = $argv[3];
        }
        parse_str($parameter, $config);
        $cls = carr::get($config, 'serviceClass');
        /** @var CJob_Exception $job */
        $serviceName = carr::get($config, 'serviceName');
        $command = carr::get($config, 'command');

        $service = new $cls($serviceName, $config);

        $cmd = strtolower(end($_SERVER['argv']));
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
        $this->uri = carr::get($config, 'uri', 'cresenity/service');
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
        $isUnix = ($this->helper->getPlatform() === CJob_Helper::UNIX);
        if ($isUnix && !extension_loaded('posix')) {
            throw new Exception('posix extension is required');
        }
        if ($isUnix) {
            $this->runUnix();
        } else {
            $this->runWindows();
        }
    }

    /**
     * @param string $job
     * @param array  $config
     */
    protected function runUnix($job, array $config) {
        $command = $this->getExecutableCommand($job, $config);
        $binary = $this->getPhpBinary();
        $output = $config['debug'] ? 'debug.log' : '/dev/null';
        exec("$binary $command 1> $output 2>&1 &");
    }

    // @codeCoverageIgnoreStart
    /**
     * @param string $job
     * @param array  $config
     */
    protected function runWindows($job, array $config) {
        // Run in background (non-blocking). From
        // http://us3.php.net/manual/en/function.exec.php#43834
        $binary = $this->getPhpBinary();
        $command = $this->getExecutableCommand($job, $config);
        pclose(popen("start \"blah\" /B \"$binary\" $command", "r"));
    }

    // @codeCoverageIgnoreEnd
    /**
     * @param string $job
     * @param array  $config
     *
     * @return string
     */
    protected function getExecutableCommand($job, array $config) {
        $domain = carr::get($config, 'domain', CF::domain());
        return sprintf('"%s" "%s" "%s" "%s"', $this->script, $this->uri, $domain, http_build_query($config));
    }

    /**
     * @return false|string
     */
    protected function getPhpBinary() {
        $executableFinder = new PhpExecutableFinder();
        return $executableFinder->find();
    }

}
