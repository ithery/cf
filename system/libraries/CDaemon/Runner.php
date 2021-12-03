<?php
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class CDaemon_Runner {
    protected $serviceClass;

    protected $domain;

    protected $debug = false;

    public function __construct($serviceClass, $domain = null) {
        $this->serviceClass = $serviceClass;
        $this->domain = $domain ?: CF::domain();
    }

    public function setDebug($bool = true) {
        $this->debug = $bool;

        return $this;
    }

    public function run() {
        $isUnix = CDaemon_Helper::getPlatform() === CDaemon_Helper::UNIX;
        if ($isUnix && !extension_loaded('posix')) {
            throw new Exception('posix extension is required');
        }

        if ($isRunning = $this->isRunning()) {
            throw new CDaemon_Exception_AlreadyRunningException('daemon is running');
        }

        if ($isUnix) {
            return $this->runUnix();
        } else {
            return $this->runWindows();
        }
    }

    public function isRunning() {
        $result = '';
        if ($pid = $this->getPid()) {
            $pid = trim($pid);

            $command = 'ps x | grep "' . $pid . '" | grep "'
                . $this->serviceClass
                . '" | grep -v "grep"';

            if (defined('CFCLI')) {
                $process = new Process($command);
                $process->run();
                $result = $process->getOutput();
            } else {
                $result = shell_exec($command);
            }
        }

        return strlen(trim($result)) > 0;
    }

    public function getPid() {
        $pidFile = CDaemon_Helper::getPidFile($this->serviceClass);

        if ($pidFile && file_exists($pidFile)) {
            return file_get_contents($pidFile);
        }

        return false;
    }

    protected function runUnix() {
        $command = $this->getExecutableCommand();
        $binary = $this->getPhpBinary();
        $output = $this->debug ? $this->debugOutput() : '/dev/null';
        //$output = $this->debugOutput();

        $commandToExecute = "NSS_STRICT_NOFORK=DISABLED ${binary} ${command} 1> \"${output}\" 2>&1 &";

        if (defined('CFCLI')) {
            $process = new Process($commandToExecute);
            $process->run();
            $result = $process->getOutput();

            return $result;
        } else {
            return exec($commandToExecute);
        }
    }

    // @codeCoverageIgnoreStart

    /**
     * Run windows.
     *
     * @return void
     */
    protected function runWindows() {
        // Run in background (non-blocking). From
        // http://us3.php.net/manual/en/function.exec.php#43834
        $binary = $this->getPhpBinary();
        $command = $this->getExecutableCommand();

        pclose(popen("start \"blah\" /B \"${binary}\" ${command}", 'r'));
    }

    /**
     * @return false|string
     */
    protected function getPhpBinary() {
        $executableFinder = new PhpExecutableFinder();

        return $executableFinder->find();
    }

    /**
     * @return string
     */
    protected function getExecutableCommand() {
        $params = [
            'serviceClass' => $this->serviceClass,
            'command' => 'start',
        ];
        $cmd = sprintf('"%s" "%s" "%s" "%s"', 'index.php', 'cresenity/daemon', $this->domain, http_build_query($params));

        return $cmd;
    }

    protected function debugOutput() {
        $serviceClass = $this->config['serviceClass'];
        $output = DOCROOT . 'temp' . DS . 'daemon' . DS . CF::appCode() . '/' . $serviceClass . '.log';
        $dir = dirname($output);
        if (!CFile::isDirectory($dir)) {
            CFile::makeDirectory($dir, 0755, true);
        }

        return $output;
    }

    protected function debugContent() {
        $output = $this->debugOutput();
        if (CFile::exists($output)) {
            return file_get_contents($output);
        }

        return null;
    }

    public function logDump() {
        $pid = $this->getPid();
        if ($pid) {
            exec("kill -10 ${pid}");
        }
    }

    public function stop($exit = true) {
        $pid = $this->getPid();
        $command = 'kill -2 ' . $pid;
        if (defined('CFCLI')) {
            $process = new Process($command);
            $process->run();
            $result = $process->getOutput();
        } else {
            $result = shell_exec($command);
        }

        return $result;
    }

    public function rotateLog() {
        $logFile = CDaemon_Helper::getLogFile($this->serviceClass);

        if (strlen($logFile) > 0 && file_exists($logFile)) {
            $rotator = CLogger_Rotator::createRotate($logFile);

            $rotator->forceRotate();
        }
    }

    public function status() {
        $labelStatus = $this->isRunning() ? 'Running' : 'Stopped';

        return $labelStatus;
    }
}
