<?php
use Symfony\Component\Process\Process;

class CDaemon_Runner extends CDaemon_RunnerAbstract {
    /**
     * @var string
     */
    protected $serviceClass;

    /**
     * @param string      $serviceClass
     * @param null|string $domain
     */
    public function __construct($serviceClass, $domain = null) {
        $this->serviceClass = $serviceClass;
        $this->domain = $domain ?: CF::domain();
    }

    /**
     * @return bool
     */
    public function run() {
        $isUnix = CDaemon_Helper::getPlatform() === CDaemon_Helper::UNIX;
        if ($isUnix && !extension_loaded('posix')) {
            throw new Exception('posix extension is required');
        }

        if ($this->isRunning()) {
            throw new CDaemon_Exception_AlreadyRunningException('daemon is running');
        }
        if ($isUnix) {
            return $this->runUnix();
        } else {
            $this->runWindows();

            return true;
        }
    }

    /**
     * @return bool
     */
    public function isRunning() {
        if ($pid = $this->getPid()) {
            $pid = trim($pid);

            return CDaemon_Utils::daemonIsRunningWithPid($pid, $this->serviceClass);
        }

        return false;
    }

    /**
     * @return string
     */
    public function getServiceClass() {
        return $this->serviceClass;
    }

    /**
     * @return false|string
     */
    public function getPid() {
        $pidFile = CDaemon_Helper::getPidFile($this->serviceClass);

        if ($pidFile && file_exists($pidFile)) {
            return file_get_contents($pidFile);
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function runUnix() {
        $commandToExecute = $this->getCommandToExecuteOnUnix(true);
        $process = new Process($commandToExecute);
        $process->setWorkingDirectory(DOCROOT);
        $process->setTimeout(null);
        $process->disableOutput();
        $process->run();

        return $process->getExitCode() == 0;
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

    /**
     * @return string
     */
    protected function debugOutput() {
        $serviceClass = $this->serviceClass;
        $output = DOCROOT . 'temp' . DS . 'daemon' . DS . CF::appCode() . '/' . $serviceClass . '.log';
        $dir = dirname($output);
        if (!CFile::isDirectory($dir)) {
            CFile::makeDirectory($dir, 0755, true);
        }

        return $output;
    }

    /**
     * @return void
     */
    public function logDump() {
        $pid = $this->getPid();
        if ($pid) {
            exec("kill -10 {$pid}");
        }
    }

    /**
     * @param bool $force
     *
     * @return string
     */
    public function stop($force = false) {
        $pid = $this->getPid();
        $option = $force ? '-9' : '-2';
        $command = 'kill ' . $option . ' ' . $pid;
        if (defined('CFCLI')) {
            $process = new Process($command);
            $process->run();
            $result = $process->getOutput();
        } else {
            $result = shell_exec($command);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getLogFile() {
        return CDaemon_Helper::getLogFile($this->serviceClass);
    }

    /**
     * @param null|int $size
     * @param null|int $keep
     *
     * @return void
     */
    public function autoRotateLog($size = null, $keep = null) {
        $logFile = $this->getLogFile();
        $size = $size ?: CF::config('daemon.logs.rotation.size', 500 * 1024);
        $keep = $keep ?: CF::config('daemon.logs.rotation.keep', 10);
        if (strlen($logFile) > 0 && CFile::isFile($logFile) && CFile::size($logFile) > $size) {
            $rotator = CLogger_Rotator::createRotate($logFile);
            $rotator->size($size)->keep($keep)->run();
        }
    }

    /**
     * @return string
     */
    public function status() {
        return $this->isRunning() ? 'Running' : 'Stopped';
    }

    /**
     * @return null|CCarbon
     */
    public function getStartTime() {
        $pid = $this->getPid();
        if (!$pid) {
            return null;
        }

        $pid = trim($pid);

        if (CDaemon_Helper::getPlatform() !== CDaemon_Helper::UNIX) {
            return null;
        }

        if (!CDaemon_Utils::daemonIsRunningWithPid($pid, $this->serviceClass)) {
            return null;
        }

        $command = "ps -o lstart= -p {$pid}";
        $output = trim(shell_exec($command));

        if (!$output) {
            return null;
        }

        try {
            return new CCarbon($output);
        } catch (Exception $e) {
            return null;
        }
    }
}
