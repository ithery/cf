<?php
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class CDaemon_Runner {
    /**
     * @var string
     */
    protected $serviceClass;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var bool
     */
    protected $debug = false;

    public function __construct($serviceClass, $domain = null) {
        $this->serviceClass = $serviceClass;
        $this->domain = $domain ?: CF::domain();
    }

    public function setDebug($bool = true) {
        $this->debug = $bool;

        return $this;
    }

    /**
     * Alias for run.
     */
    public function start() {
        return $this->run();
    }

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
            return $this->runWindows();
        }
    }

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

    public function getPid() {
        $pidFile = CDaemon_Helper::getPidFile($this->serviceClass);

        if ($pidFile && file_exists($pidFile)) {
            return file_get_contents($pidFile);
        }

        return false;
    }

    protected function getCommandToExecuteOnUnix($background = true) {
        $command = $this->getExecutableCommand();
        $binary = $this->getPhpBinary();
        $output = $this->debug ? $this->debugOutput() : '/dev/null';
        //$output = $this->debugOutput();

        $commandToExecute = "NSS_STRICT_NOFORK=DISABLED ${binary} ${command}";
        if ($background) {
            $commandToExecute .= " 1> \"${output}\" 2>&1 &";
        }

        return $commandToExecute;
    }

    protected function getCommandToExecuteOnWindows($background = true) {
        $command = $this->getExecutableCommand();
        $binary = $this->getPhpBinary();
        //$output = $this->debug ? $this->debugOutput() : '/dev/null';
        //$output = $this->debugOutput();

        $commandToExecute = "\"${binary}\" ${command}";
        if ($background) {
            $commandToExecute = 'start "blah" /B ' . $commandToExecute;
        }

        return $commandToExecute;
    }

    public function getCommandToExecute($background = true) {
        $isUnix = CDaemon_Helper::getPlatform() === CDaemon_Helper::UNIX;
        if ($isUnix) {
            return $this->getCommandToExecuteOnUnix($background);
        }

        return $this->getCommandToExecuteOnWindows($background);
    }

    protected function runUnix() {
        $commandToExecute = $this->getCommandToExecuteOnUnix();
        $process = new Process($commandToExecute);
        $process->setWorkingDirectory(DOCROOT);
        $process->run();
        $result = $process->getExitCode();

        return $result == 0;
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
        $serviceClass = $this->serviceClass;
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

    /**
     * @return void
     */
    public function logDump() {
        $pid = $this->getPid();
        if ($pid) {
            exec("kill -10 ${pid}");
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

    public function getLogFile() {
        return CDaemon_Helper::getLogFile($this->serviceClass);
    }

    public function getLog() {
        $logFile = $this->getLogFile();
        if (CFile::exists($logFile)) {
            return CFile::get($logFile);
        }

        return null;
    }

    public function rotateLog() {
        $logFile = $this->getLogFile();

        if (strlen($logFile) > 0 && CFile::isFile($logFile)) {
            $rotator = CLogger_Rotator::createRotate($logFile);
            $rotator->forceRotate();
        }
    }

    public function autoRotateLog($size = null, $keep = null) {
        $logFile = $this->getLogFile();
        $size = $size ?: CF::config('daemon.logs.rotation.size', 500 * 1024);
        $keep = $keep ?: CF::config('daemon.logs.rotation.keep', 10);
        if (strlen($logFile) > 0 && CFile::isFile($logFile) && CFile::size($logFile) > $size) {
            $rotator = CLogger_Rotator::createRotate($logFile);
            $rotator->size($size)->keep($keep)->run();
        }
    }

    public function status() {
        $labelStatus = $this->isRunning() ? 'Running' : 'Stopped';

        return $labelStatus;
    }
}
