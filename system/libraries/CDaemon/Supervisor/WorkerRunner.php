<?php
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class CDaemon_Supervisor_WorkerRunner {
    /**
     * @var string
     */
    protected $supervisor;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $masterDaemonClass;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var CDaemon_Supervisor_SupervisorOptions
     */
    protected $options;

    public function __construct(CDaemon_Supervisor_SupervisorOptions $options, $masterDaemonClass, $domain = null) {
        $this->options = $options;
        $this->domain = $domain ?: CF::domain();
        $this->name = $options->workersName;
        $this->supervisor = $options->name;
        $this->masterDaemonClass = $masterDaemonClass;
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
        $class = CF::config('daemon.supervisor.fast_termination')
            ? CDaemon_BackgroundProcess::class
            : Process::class;
        $isUnix = CDaemon_Helper::getPlatform() === CDaemon_Helper::UNIX;
        if ($isUnix && !extension_loaded('posix')) {
            throw new Exception('posix extension is required');
        }

        if ($isUnix) {
            return $this->runUnix($class);
        } else {
            return $this->runWindows($class);
        }
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    protected function getCommandToExecuteOnUnix($background = false) {
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

    /**
     * @param mixed $class
     *
     * @return Symfony\Component\Process\Process
     */
    protected function runUnix($class) {
        $commandToExecute = $this->getCommandToExecuteOnUnix();

        return $class::fromShellCommandline($commandToExecute, $this->options->directory ?? DOCROOT)
            ->setTimeout(null)
            ->disableOutput();
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
        $params = $this->options->toArray();
        $params['supervisor'] = $this->supervisor;
        $params['name'] = $this->name;
        $params['alias'] = $this->alias;

        $cmd = sprintf('"%s" "%s" "%s" "%s"', 'index.php', 'cresenity/worker', $this->domain, http_build_query($params));

        return $cmd;
    }

    protected function debugOutput() {
        $serviceClass = $this->masterDaemonClass;
        $output = DOCROOT . 'temp' . DS . 'daemon' . DS . CF::appCode() . '/' . $serviceClass . DS . $this->name . '-worker.log';
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

    public function getLogFile() {
        return '';
        //return CDaemon_Helper::getLogFile($this->serviceClass);
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

        if (strlen($logFile) > 0 && file_exists($logFile)) {
            $rotator = CLogger_Rotator::createRotate($logFile);

            $rotator->forceRotate();
        }
    }
}
